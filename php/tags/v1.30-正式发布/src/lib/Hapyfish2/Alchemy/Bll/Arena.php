<?php

/**
 * arena
 *
 * @copyright  Copyright (c) 2012 HapyFish
 * @create      2012/08   Nick
 */
class Hapyfish2_Alchemy_Bll_Arena
{
    const OPEN_LEVEL = 1;			//竞技场开放等级
    const ARENA_REFRESH_CID = 5615;	//刷新竞技场对手道具cid
    const FIGHT_TIME = 300;			//战斗限制时间
    
	/**
	 * 初始化竞技场信息
	 * @param int $uid
	 * @param int $tid
	 */
	public static function initArena($uid)
	{
		//玩家当前积分
		$userScore = Hapyfish2_Alchemy_Cache_Arena::getUserScore($uid);
		//玩家当前排名
		$userRank = Hapyfish2_Alchemy_Cache_Arena::getUserRank($uid, $userScore);
		//玩家竞技场信息
		$userArena = Hapyfish2_Alchemy_Cache_Arena::getUserArena($uid);
		
		//每日重置竞技场信息
		$nowTm = time();
		$todayTm = strtotime(date('Ymd'));
		if ( $userArena['lastRefreshTime'] < $todayTm ) {
			$userArena = self::resetUserArena($uid, $userArena, $userScore);
		}
		
		//获取竞争对手列表
		$opponentListVo = self::ganOpponentVo($userArena['opponentList'], $userArena['fightUids']);
		
		//根据积分获取奖励
		$userPrize = self::getPrizeByScore($userScore);

		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'opponents', $opponentListVo);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'challengeTimes', $userArena['challengeTimes']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'refreshTimes', $userArena['refreshTimes']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'cd', $userArena['cd']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'prizeGetted', $userArena['prizeGetted']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'score', $userScore);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rank', $userRank);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'prize', $userPrize);
		
        return 1;
    }
    
    /**
     * 查看对手信息
     *
     * @param int $uid
     * @param int $fid
     */
    public static function getOpponentInfo($uid, $fid)
    {
		$userArena = Hapyfish2_Alchemy_Cache_Arena::getUserArena($uid);
		if ( !in_array($fid, $userArena['opponentList']) ) {
			return -262;
		}
		
		//佣兵与主角数据
		$enemySide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($fid);
		$enemyRoles = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($fid, $enemySide);
		foreach ( $enemyRoles as $k=>$v ) {
			$enemyRoles[$k]['hp'] = $v['maxHp'];
			$enemyRoles[$k]['mp'] = $v['maxMp'];
		}
		
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'opponent', $enemyRoles);
		return 1;
    }
    
    /**
     * 挑战对手
     * 
     * @param int $uid
     * @param int $fid
     */
    public static function challenge($uid, $fid)
    {
    	$ret = self::_challengePre($uid, $fid);
    
	    if ($ret != 1) {
            return $ret;
        }
        
        $nowTime = time();
		
        $info = array();
		$info['uid'] = $uid;
		$info['fid'] = Hapyfish2_Alchemy_Bll_Fight::getNewId($uid);
		//0-普通打怪 1-侵略 2-反抗 3-救援 7-1v1竞技场 8-对白触发战斗 9-新手引导战斗
		$info['type'] = 7;
		$info['status'] = 0;

		$aryRnd = array();
		for ($i=0; $i<20; $i++) {
            $aryRnd[] = mt_rand(1,1000);
		}

		$homeSide = Hapyfish2_Alchemy_Bll_FightCorps::getHomeSideUnitList($uid);
		if (!$homeSide) {
		    return -321;
		}

	    $enemySide = Hapyfish2_Alchemy_Bll_FightCorps::getHomeSideUnitList($fid);
		if (!$enemySide) {
		    return -322;
		}
		
		$info['rnd_element'] = $aryRnd;
		$info['home_side'] = $homeSide;
		$info['content'] = array();
		$info['create_time'] = $nowTime;
		$info['enemy_id'] = '7-' . $fid;

        //战斗宣言
        $aryTalk = array();
        $cntHomeSide = count($homeSide);
        $rndTalkRole = mt_rand(1, $cntHomeSide);
        $idx = 0;
        foreach ($homeSide as $data) {
        	$idx ++;
            if ($idx == $rndTalkRole) {
                $talks = Hapyfish2_Alchemy_Cache_Basic::getFightDeclareByJob($data['job']);
                if ($talks) {
                    $rndKey = mt_rand(1, count($talks));
                    $aryTalk[] = array((int)$data['matrix_pos'], $talks[$rndKey-1]);
                }
                break;
            }
        }
        
        //保存初始战斗信息
		$info['enemy_side'] = $enemySide;
		$saveDb = (defined('ENABLE_FIGHT_DB_LOG') && ENABLE_FIGHT_DB_LOG);
        Hapyfish2_Alchemy_Cache_Fight::saveFightInfo($uid, $info, $saveDb);

        //拼接Vo数据返回前端
        //我方
        $roleList1 = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
        //敌方
        $roleList2 = Hapyfish2_Alchemy_Bll_MapCopy::genEnemyRolesVo($enemySide);
        
        $battle = array(
            'id' => $info['fid'],
            'bgClassName' => 'battlebg.1.Background',
            'roleList' => array_merge($roleList1, $roleList2),
        	'talk' => $aryTalk
        );

        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'BattleVo', $battle);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'RndNums', $info['rnd_element']);
        
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'limitTime', self::FIGHT_TIME);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'canUseFriendSkill', 0);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'canUseItem', 0);
        
        return 1;
    }
    
    private static function _challengePre($uid, $fid)
    {
	    if ($uid == $fid || empty($fid)) {
            return -200;
	    }

        $myLev = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
        if ($myLev < self::OPEN_LEVEL) {
            return -261;//level too low
        }

		$userArena = Hapyfish2_Alchemy_Cache_Arena::getUserArena($uid);
		if ( !in_array($fid, $userArena['opponentList']) ) {
			return -262;
		}
        
		return 1;
    }
	
    /**
     * 每日重置竞技场信息
     * 
     * @param int $uid
     * @param array $userArena
     * @param int $userScore
     */
    public static function resetUserArena($uid, $userArena = null, $userScore)
    {
    	if ( !$userArena ) {
			$userArena = Hapyfish2_Alchemy_Cache_Arena::getUserArena($uid);
    	}
    	
    	try {
	    	//对手列表
	    	$opponentList = self::getRandOpponent($uid, $userScore);
    	}
	    catch (Exception $e) {
	    }
    	
    	$userArena['challengeTimes'] = 3;
    	$userArena['refreshTimes'] = 3;
    	$userArena['cd'] = 0;
    	$userArena['prizeGetted'] = 0;
    	$userArena['fightUids'] = array();
    	$userArena['opponentList'] = $opponentList;
    	$userArena['lastRefreshTime'] = time();
    	
    	Hapyfish2_Alchemy_Cache_Arena::updateUserArena($uid, $userArena);
    	
    	return $userArena;
    }

    /**
     * 随机获取竞争对手
     *
     * @param int $uid
     * @param int $userRank
     */
    public static function getRandOpponent($uid, $userScore)
    {
    	$dalArena = Hapyfish2_Alchemy_Event_Dal_Arena::getDefaultInstance();
    	$uidsList = $dalArena->getUidsByScore($userScore, 100);
    
    	if ( count($uidsList) < 6 ) {
    		$uidsList = $dalArena->getUidsTop(7);
    		foreach ( $uidsList as $k=>$r ) {
    			if ( $r['uid'] == $uid ) {
    				unset($uidsList[$k]);
    			}
    		}
    	}
    
    	$uidsCount = count($uidsList);
    	if ( $uidsCount < 6 ) {
    		$randList = array_rand($uidsList, $uidsCount);
    	}
    	else {
    		$randList = array_rand($uidsList, 6);
    	}
    	 
    	$opponentList = array();
    	foreach ( $randList as $rand ) {
    		$opponentList[] = $uidsList[$rand];
    	}
    	
    	$list = array();
    	foreach ( $opponentList as $v ) {
    		$list[] = $v['uid'];
    	}
    	
    	return $list;
    }
    
    /**
     * 刷新竞技场对手（道具、免费）
     * @param int $uid
     */
    public static function refreshOpponents($uid)
    {
		//玩家竞技场信息
		$userArena = Hapyfish2_Alchemy_Cache_Arena::getUserArena($uid);
		$isFree = true;
		if ( $userArena['refreshTimes'] < 1 ) {
			$needCid = ARENA_REFRESH_CID;
			$needCount = 1;
			$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
			
			if ( !isset($userGoods[$needCid]) || $userGoods[$needCid]['count'] < $needCount ) {
				return -204;
			}
			$isFree = false;
		}
		
		$userScore = Hapyfish2_Alchemy_Cache_Arena::getUserScore($uid);
    	//对手列表
    	$opponentList = self::getRandOpponent($uid, $userScore);
    	
    	if ( $isFree ) {
    		$userArena['refreshTimes'] -= 1;
    	}
    	else {
    		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $needCid, $needCount, $userGoods);
    	}
    	
    	$userArena['fightUids'] = array();
    	$userArena['opponentList'] = $opponentList;
    	$userArena['lastRefreshTime'] = time();
    	
    	Hapyfish2_Alchemy_Cache_Arena::updateUserArena($uid, $userArena);
    	
		//获取竞争对手列表
		$opponentListVo = self::ganOpponentVo($userArena['opponentList'], $userArena['fightUids']);
		
		//根据积分获取奖励
		$userPrize = self::getPrizeByScore($userScore);

		//玩家当前排名
		$userRank = Hapyfish2_Alchemy_Cache_Arena::getUserRank($uid, $userScore);
		
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'opponents', $opponentListVo);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'challengeTimes', $userArena['challengeTimes']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'refreshTimes', $userArena['refreshTimes']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'cd', $userArena['cd']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'prizeGetted', $userArena['prizeGetted']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'score', $userScore);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rank', $userRank);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'prize', $userPrize);
		
    	return 1;
    }
    
    public static function ganOpponentVo($list, $fightUids = null)
    {
    	if ( !$fightUids ) {
			$userArena = Hapyfish2_Alchemy_Cache_Arena::getUserArena($uid);
			$fightUids = $userArena['fightUids'];
    	}
    	
		//获取竞争对手列表
		$opponentList = array();
		foreach ( $list as $k=>$v ) {
			$fid = $v;
			$score = Hapyfish2_Alchemy_Cache_Arena::getUserScore($fid);
			$rankNum = Hapyfish2_Alchemy_Cache_Arena::getUserRank($fid, $score);
    		$roleInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($fid);
    		
			if ( in_array($fid, $fightUids) ) {
				$canFight = 0;
			}
			else {
				$canFight = 1;
			}
    		$opponentList[] = array('fid' => $fid,
    						'name' => $roleInfo['name'],
    						'className' => $roleInfo['class_name'],
    						'star' => $roleInfo['rp'],
    						'rank' => $rankNum,
    						'score' => $score,
    						'canFight' => $canFight);
		}
		return $opponentList;
    }
    
    /**
     * 排行榜
     * 
     * @param int $uid
     * @param int $from
     * @param int $pageSize
     */
    public static function initRank($uid, $from, $pageSize = 100)
    {
    	$rankList = Hapyfish2_Alchemy_Cache_Arena::getRankList();
    	
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rank', $rankList);
        
		//玩家当前积分
		$userScore = Hapyfish2_Alchemy_Cache_Arena::getUserScore($uid);
		//玩家当前排名
		$userRank = Hapyfish2_Alchemy_Cache_Arena::getUserRank($uid, $userScore);
		
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'myRank', $userRank);
        
    	return 1;
    }
    
    /**
     * 根据积分，计算奖励
     * @param int $score
     */
    private static function getPrizeByScore($score)
    {
    	$addItems = array('coin' => 100);
    	return $addItems;
    }
	
}