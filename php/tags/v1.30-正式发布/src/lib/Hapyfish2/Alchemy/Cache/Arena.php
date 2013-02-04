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
					$dalArena->insUpd($uid, array('score'=>0));
					return 0;
				}
			} catch (Exception $e) {
				return 0;
			}
		}
        return $score;
	}
	
	public static function getUserRank($uid, $userScore=null)
	{
		$key = 'a:u:arenarank:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$rank = $cache->get($key);
		
		if ($rank === false) {
			try {
				if ( !$userScore ) {
					$userScore = self::getUserScore($uid);
				}
				$dalArena = Hapyfish2_Alchemy_Event_Dal_Arena::getDefaultInstance();
				$rank = $dalArena->getUserRank($uid, $userScore);
				if ($rank) {
					$cache->add($key, $rank);
				} else {
					return 9999;
				}
			} catch (Exception $e) {
				return 9999;
			}
		}
        return $rank;
	}

	public static function getUserArena($uid)
	{
		$key = 'a:u:arena:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$info = $cache->get($key);
		if ($info === false) {
			//"challengeTimes":剩余可挑战的次数
			//"refreshTimes":剩余可刷新次数
			//"cd":挑战CD 时间戳
			//"prizeGetted":是否已经领取过奖励
			//"lastRefreshTime":上一次竞技场对手刷新时间
			$info = array('challengeTimes' => 3,
						  'refreshTimes' => 3,
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
		return;
	}
	
	public static function getRankList()
	{
		$key = 'a:u:arenarank';
		$cache = Hapyfish2_Cache_Factory::getMC(0);
		$rankList = $cache->get($key);
		
		if ($rankList === false) {
			try {
				$dalArena = Hapyfish2_Alchemy_Event_Dal_Arena::getDefaultInstance();
				$list = $dalArena->getUidsTop(100);
				if ($list) {
					$rankList = array();
					foreach ( $list as $k=>$v ) {
						$rankNum = $k + 1;
						$platInfo = Hapyfish2_Platform_Bll_User::getUser($uid);
						$rankList[] = array($v['uid'], $platInfo['name'], $rankNum, $v['score']);
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
							'score' => $r[3]);
		}
		
        return $data;
	}
	
}