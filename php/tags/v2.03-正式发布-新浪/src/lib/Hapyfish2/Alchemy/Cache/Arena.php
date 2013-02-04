<?php

class Hapyfish2_Alchemy_Cache_Arena
{
	
	public static function getUserScore($uid)
	{
		$key = 'a:u:arenascore:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$score = $cache->get($key);
		
		if ($score === false) {
			try {
				$dalArena = Hapyfish2_Alchemy_Event_Dal_Arena::getDefaultInstance();
				$score = $dalArena->getUserScore($uid);
				if ($score) {
					$cache->add($key, $score);
				} else {
					$initScore = 1000;
					$dalArena->insUpd($uid, array('score'=>$initScore));
				
					//玩家当前排名
					$userRank = Hapyfish2_Alchemy_Cache_Arena::getUserRank($uid, $initScore);
		        	if ( $userRank <= Hapyfish2_Alchemy_Bll_Arena::RANK_NUM ) {
		        		self::reloadRankList();
		        	}
					return $initScore;
				}
			} catch (Exception $e) {
				return 1000;
			}
		}
        return $score;
	}
	
    public static function updateUserScore($uid, $score)
    {
		$key = 'a:u:arenascore:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);

        $ok = $cache->set($key, $score);
        if ($ok) {
        	try {
        		$info = array('score' => $score);
        		$dalUser = Hapyfish2_Alchemy_Event_Dal_Arena::getDefaultInstance();
        		$dalUser->update($uid, $info);
        	} catch (Exception $e) {
        		info_log('[Hapyfish2_Alchemy_Cache_Arena::updateUserScore:' . $uid . ']' . $e->getMessage(), 'db.err');
        	}
        	
			//玩家当前排名
			$userRank = Hapyfish2_Alchemy_Cache_Arena::getUserRank($uid, $score);
        	if ( $userRank <= Hapyfish2_Alchemy_Bll_Arena::RANK_NUM ) {
        		self::reloadRankList();
        	}
        }
        return $ok;
    }

    public static function incUserScore($uid, $scoreChange)
    {
    	if ($scoreChange <= 0) {
    		return false;
    	}

    	$userScore = self::getUserScore($uid);
    	if ($userScore === null) {
    		return false;
    	}
    	$userScore += $scoreChange;
    	$ok = self::updateUserScore($uid, $userScore);
    	return $ok;
    }

    public static function decUserScore($uid, $scoreChange)
    {
    	if ($scoreChange <= 0) {
    		return false;
    	}

    	$userScore = self::getUserScore($uid);
    	if ($userScore === null) {
    		return false;
    	}
    	
    	$userScore -= $scoreChange;
    	$userScore = $userScore < 0 ? 0 : $userScore;
    	$ok = self::updateUserScore($uid, $userScore);
    	return $ok;
    }
	
	public static function getUserRank($uid, $userScore=null)
	{
		$key = 'a:u:arenarank:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$rank = $cache->get($key);
		
		//if ($rank === false) {
			try {
				if ( !$userScore ) {
					$userScore = self::getUserScore($uid);
				}
				$dalArena = Hapyfish2_Alchemy_Event_Dal_Arena::getDefaultInstance();
				$rank = $dalArena->getUserRank($uid, $userScore);
				$rank ++;
				if ($rank) {
					$cache->add($key, $rank);
				} else {
					return 9999;
				}
			} catch (Exception $e) {
				return 9999;
			}
		//}
        return $rank;
	}
	
	public static function getUserArena($uid)
	{
		$key = 'a:u:arena:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$info = $cache->get($key);
		if ($info === false) {
			//"challengeTimes":剩余可挑战的次数
			//"challengeCount":已累计挑战次数
			//"refreshTimes":剩余可刷新次数
			//"cd":挑战CD 时间戳
			//"fightUids":当前竞技场列表中，已挑战用户uid列表
			//"prizeGetted":是否已经领取过奖励
			//"lastRefreshTime":上一次竞技场对手刷新时间
			$info = array('challengeTimes' => 5,
						  'challengeCount' => 0,
						  'refreshTimes' => 2,
						  'cd' => 0,
						  'prizeGetted' => 0,
						  'fightUids' => array(),
						  'opponentList' => array(),
						  'lastRefreshTime' => 0);
		}
        return $info;
	}
	
	public static function updateUserArena($uid, $info)
	{
		$key = 'a:u:arena:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $info, 172800);
		return true;
	}
	
	public static function getRankList()
	{
		$key = 'a:u:arenarank';
		$cache = Hapyfish2_Cache_Factory::getMC(0);
		$rankList = $cache->get($key);
		
		if ($rankList === false) {
			try {
				$dalArena = Hapyfish2_Alchemy_Event_Dal_Arena::getDefaultInstance();
				$list = $dalArena->getUidsTop(Hapyfish2_Alchemy_Bll_Arena::RANK_NUM);
				if ($list) {
					$rankList = array();
					foreach ( $list as $k=>$v ) {
						$rankNum = $k + 1;
						$uid = $v['uid'];
						$platInfo = Hapyfish2_Platform_Bll_User::getUser($uid);
						$roleInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
						$rankList[] = array($v['uid'], $platInfo['name'], $rankNum, $v['score'], $roleInfo['level']);
    				}
					$cache->add($key, $rankList);
				} else {
					return array();
				}
			} catch (Exception $e) {
				return array();
			}
		}
		
		$data = array();
		foreach ( $rankList as $r ) {
			$data[] = array('uid' => $r[0],
							'name' => $r[1],
							'rank' => $r[2],
							'score' => $r[3],
							'level' => $r[4]);
		}
		
        return $data;
	}
	
	public static function reloadRankList()
	{
		$key = 'a:u:arenarank';
		$cache = Hapyfish2_Cache_Factory::getMC(0);

		try {
			$dalArena = Hapyfish2_Alchemy_Event_Dal_Arena::getDefaultInstance();
			$list = $dalArena->getUidsTop(100);
			if ($list) {
				$rankList = array();
				foreach ( $list as $k=>$v ) {
					$rankNum = $k + 1;
					$uid = $v['uid'];
					$platInfo = Hapyfish2_Platform_Bll_User::getUser($uid);
					$roleInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
					$rankList[] = array($v['uid'], $platInfo['name'], $rankNum, $v['score'], $roleInfo['level']);
    			}
				$cache->set($key, $rankList);
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
		
        return true;
	}
	
	public static function getArenaFeed($uid)
	{
		$key = 'i:u:arenafeed:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getFeed($uid);
		return $cache->get($key);
	}
	
	public static function flushArenaFeed($uid)
	{
		$key = 'i:u:arenafeed:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getFeed($uid);
		$cache->set($key, array());
	}
	
	public static function insertArenaFeed($uid, $feed)
    {
        $key = 'i:u:arenafeed:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getFeed($uid);
        $cache->insertMiniFeed($key, $feed);
    }
}