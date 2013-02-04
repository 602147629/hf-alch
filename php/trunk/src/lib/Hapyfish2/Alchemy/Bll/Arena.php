<?php

/**
 * arena
 *
 * @copyright  Copyright (c) 2012 HapyFish
 * @create      2012/08   Nick
 */
class Hapyfish2_Alchemy_Bll_Arena
{
    const LOSE_SCORE = -1;			//挑战失败扣除积分
    const OPEN_LEVEL = 1;			//竞技场开放等级
    const ARENA_REFRESH_CID = 5615;	//刷新竞技场对手道具cid
    const FIGHT_TIME = 300;			//战斗限制时间
    const RANK_NUM = 50;			//排行榜显示人数
    const OPPONENT_RANDNUM = 10;	//竞技场对手列表随机前10名对手
    const FIGHT_BG_CLASSNAME = 'fbg.7.jingjichang';			//背景图
    
	/**
	 * 初始化竞技场信息
	 * @param int $uid
	 * @param int $tid
	 */
	public static function initArena($uid)
	{
		//检查判断 重置排行信息
		self::resetArenaRank();
		
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
			$userArena = self::resetUserArena($uid, $userScore, $userArena);
		}
		
		//获取竞争对手列表
		$opponentListVo = self::ganOpponentVo($uid, $userArena['opponentList'], $userArena['fightUids']);
		
		//根据积分获取奖励
		$userPrize = self::getPrizeByScore($userRank);

		$curWeekDay = date("w");
		//周一 以外时间不做检查
		if ( $curWeekDay != 1 ) {
			//getPrizeRemain,下次领奖时间
	    	$getPrizeRemain = strtotime("+0 week Monday");
		}
		else {
	    	$getPrizeRemain = strtotime("+1 week Monday");
		}
		
		//lastWeekRank，上周的排名
		$lastWeekRank = $userArena['lastWeekRank'];
		//lastWeekPrize，上周的奖励
		$lastWeekPrize = self::getPrizeByScore($lastWeekRank);
		
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'opponents', $opponentListVo);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'challengeTimes', $userArena['challengeTimes']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'refreshTimes', $userArena['refreshTimes']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'cd', $userArena['cd']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'prizeGetted', $userArena['prizeGetted']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'score', $userScore);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rank', $userRank);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'prize', $userPrize);

		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'getPrizeRemain', $getPrizeRemain);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'lastWeekRank', $lastWeekRank);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'lastWeekPrize', $lastWeekPrize);
		
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
		/*if ( !in_array($fid, $userArena['opponentList']) ) {
			return -262;
		}*/
		
		//佣兵与主角数据
		$enemySide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($fid);
		$enemyRoles = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($fid, $enemySide);
		$opponent = array();
		foreach ( $enemyRoles as $k=>$v ) {
			if ( $v['pos'] > -1 ) {
				$enemyRoles[$k]['hp'] = $v['maxHp'];
				$enemyRoles[$k]['mp'] = $v['maxMp'];
				$opponent[] = $enemyRoles[$k];
			}
		}
		
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'opponent', $opponent);
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
		$userArena = Hapyfish2_Alchemy_Cache_Arena::getUserArena($uid);
    	
    	$ret = self::_challengePre($uid, $fid, $userArena);
    
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
		$sideAry = array('9' => 8, '10' => 7, '11' => 6,
						 '12' => 5, '13' => 4, '14' => 3,
						 '15' => 2, '16' => 1, '17' => 0);
		foreach ( $enemySide as $key => $enemy ) {
			$pos = $enemySide[$key]['matrix_pos'];
			$enemySide[$key]['matrix_pos'] = $sideAry[$pos];
			$enemySide[$key]['mp'] = $enemySide[$key]['mp_max'];
			$enemySide[$key]['hp'] = $enemySide[$key]['hp_max'];
		}
		
		$info['rnd_element'] = $aryRnd;
		$info['content'] = array();
		$info['create_time'] = $nowTime;
		$info['enemy_id'] = '7-' . $fid;

        //战斗宣言
        $aryTalk = array();
        $cntHomeSide = count($homeSide);
        $rndTalkRole = mt_rand(1, $cntHomeSide);
        $idx = 0;
        foreach ($homeSide as $k => $data) {
        	$idx ++;
        	
            $homeSide[$k]['mp'] = $homeSide[$k]['mp_max'];
            $homeSide[$k]['hp'] = $homeSide[$k]['hp_max'];
            
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
		$info['home_side'] = $homeSide;
		$saveDb = (defined('ENABLE_FIGHT_DB_LOG') && ENABLE_FIGHT_DB_LOG);
        Hapyfish2_Alchemy_Cache_Fight::saveFightInfo($uid, $info, $saveDb);

        //拼接Vo数据返回前端
        //我方
        $roleList1 = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
        //敌方
        $roleList2 = Hapyfish2_Alchemy_Bll_MapCopy::genEnemyRolesVo($enemySide);
                
        $vip = new Hapyfish2_Alchemy_Bll_Vip();
		$skip = $vip->getVipSkip($uid);
		$jumpTime = $skip['max'] - $skip['num'] >0 ? $skip['max'] - $skip['num']:0;
        $battle = array(
            'id' => $info['fid'],
            'bgClassName' => self::FIGHT_BG_CLASSNAME,
            'roleList' => array_merge($roleList1, $roleList2),
        	'talk' => $aryTalk,
        	'canUseFriendSkill' => 0,
        	'canUseItem' => 0,
        	'canEscape' => 0,
        	'jumpTimes'=>$jumpTime,
        	'isPK'=>1
        );

		//玩家当前积分
		$userScore = Hapyfish2_Alchemy_Cache_Arena::getUserScore($uid);
		
		//对手当前积分
		$rivalScore = Hapyfish2_Alchemy_Cache_Arena::getUserScore($fid);
		
        //预扣积分
        $decScore = self::_getScoreChange(2, $userScore, $rivalScore);
        Hapyfish2_Alchemy_Cache_Arena::decUserScore($uid, $decScore);
        
        //挑战冷却时间
        $fightCd = self::_getFightCdTm($uid, $userArena['challengeCount']);
        
        //更新竞技场信息
        $userArena['challengeTimes'] -= 1;
        $userArena['cd'] = $fightCd + time();
        $userArena['challengeCount'] ++;
        
    	Hapyfish2_Alchemy_Cache_Arena::updateUserArena($uid, $userArena);

        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'BattleVo', $battle);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'RndNums', $info['rnd_element']);
        
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'limitTime', self::FIGHT_TIME);

        Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'arenaCd');
        
        return 1;
    }
    
    /**
     * 挑战CD时间
     * @param int $count,今日已挑战次数
     */
    private static function _getFightCdTm($uid, $count)
    {
    	$vip = new Hapyfish2_Alchemy_Bll_Vip();
    	$vipInfo = $vip->getInfo($uid);
		if ( $vipInfo['level'] >= 2 && $vipInfo['vipStatus'] == 1 ) {
    		$cdTm = 0;
    	}
    	else {
    		$cdTm = 300;
    	}
    	return $cdTm;
    }
    
    /**
     * 挑战对手前条件验证
     * @param $uid
     * @param $fid
     * @param $userArena
     */
    private static function _challengePre($uid, $fid, $userArena)
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
        
		if ( $userArena['challengeTimes'] < 1 ) {
			return -263;
		}
		
		if ( $userArena['cd'] - time() > 0 ) {
			return -264;
		}
		
		return 1;
    }
	
    /**
     * 完成竞技场战斗
     * @param int $uid
     * @param array $info
     * @param array $rst
     * @param int $fightId
     * @param int $fightRst,战斗结果（胜利，失败）
     */
    public static function completeFightArena($uid, $info, $rst, $fightId, $fightRst)
    {
		$enemyId = $info['enemy_id'];
		$fightType = substr($enemyId, 0, 1);
		$fid = substr($enemyId, 2);
		if ( $fightType != 7 ) {
			return;
		}
     	$vip = new Hapyfish2_Alchemy_Bll_Vip();
	    $vipInfo = $vip->getVipInfo($uid);
	    if($vipInfo['level'] >= 2 && $vipInfo['vipStatus']){
	        $addition = 2;
	    }else{
	       $addition = 1;
	    }
		//挑战胜利
		if ( $fightRst == Hapyfish2_Alchemy_Bll_Fight_Simulator::RESULT_WIN ) {
			//玩家当前积分
			$userScore = Hapyfish2_Alchemy_Cache_Arena::getUserScore($uid);
			//对手当前积分
			$rivalScore = Hapyfish2_Alchemy_Cache_Arena::getUserScore($fid);
	        //胜利得积分
	        $incScore = self::_getScoreChange(1, $userScore, $rivalScore);
	        Hapyfish2_Alchemy_Cache_Arena::incUserScore($uid, $incScore);
			$userArena = Hapyfish2_Alchemy_Cache_Arena::getUserArena($uid);
        	$userArena['fightUids'][] = $fid;
    		Hapyfish2_Alchemy_Cache_Arena::updateUserArena($uid, $userArena);
    		
			//对手当前积分
			//$friendScore = Hapyfish2_Alchemy_Cache_Arena::getUserScore($fid);
			//对手当前排名
			//$friendRank = Hapyfish2_Alchemy_Cache_Arena::getUserRank($fid, $friendScore);
	        $arenaFeed = array('uid' => $fid,
	        				   'actor' => $uid,
	        				   'target' => $fid,
	        				   'initiative' => 0,
	        				   'win' => 0,
	        				   //'rank' => $friendRank,
	        				   'battleId' => $fightId);
	        //添加战报
	        self::insertArenaFeed($arenaFeed);
	        
	        $arenaFeed2 = array('uid' => $uid,
	        				   'actor' => $uid,
	        				   'target' => $fid,
	        				   'initiative' => 1,
	        				   'win' => 1,
	        				   //'rank' => $friendRank,
	        				   'battleId' => 0);
	        //添加战报
	        self::insertArenaFeed($arenaFeed2);
	        
	        //发放战斗奖励,胜利
	        $prize = self::_getFightPrize($uid, 1);
	         Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, 3315, 3*$addition);
	         $winAward = array(
					array('type'=>1,
						  'id'  =>3315,
						  'num' =>3*$addition
					)
				);
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'extraItems', $winAward);
			
		}//挑战失败
		else {
	        //发放战斗奖励,失败
	        $prize = self::_getFightPrize($uid, 2);
	        
	        $arenaFeed = array('uid' => $fid,
	        				   'actor' => $uid,
	        				   'target' => $fid,
	        				   'initiative' => 0,
	        				   'win' => 1,
	        				   //'rank' => $friendRank,
	        				   'battleId' => 0);
	        //添加战报
	        self::insertArenaFeed($arenaFeed);
	        
	        $arenaFeed2 = array('uid' => $uid,
	        				   'actor' => $uid,
	        				   'target' => $fid,
	        				   'initiative' => 1,
	        				   'win' => 0,
	        				   //'rank' => $friendRank,
	        				   'battleId' => 0);
	        //添加战报
	        self::insertArenaFeed($arenaFeed2);
	         Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, 3315, 1*$addition);
	         $winAward = array(
					array('type'=>1,
						  'id'  =>3315,
						  'num' =>1*$addition	
					)
				);
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'extraItems', $winAward);
		}
		//玩家最新积分
		$userScore = Hapyfish2_Alchemy_Cache_Arena::getUserScore($uid);
		if('sinaweibo' == PLATFORM){
			Hapyfish2_Platform_Bll_WeiboRank::setRank($uid,1, $userScore);
		}
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'prize', $prize);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'score', $userScore);
		
    	return;
    }
    
    /**
     * 每日重置竞技场信息
     * 
     * @param int $uid
     * @param array $userArena
     * @param int $userScore
     */
    public static function resetUserArena($uid, $userScore, $userArena = null)
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

	    $curWeekDay = date("w");
	    //周一 以外时间不做检查
	    if ( $curWeekDay != 1 ) {
		    //上周最后时间, 周日 24:00
		    $lastWeekEndTm = strtotime("-1 week Monday");
	    }
	    else {
		    $lastWeekEndTm = strtotime("+0 week Monday");
	    }
	    
	    if ( $userArena['lastRefreshTime'] < $lastWeekEndTm ) {
	    	$lastWeekScore = 0;
	    	try {
				$dalArena = Hapyfish2_Alchemy_Event_Dal_Arena::getDefaultInstance();
				//玩家上周积分
				$lastWeekScore = $dalArena->getUserLastScore($uid);
	    	} catch ( Exception $e ) {
	    		info_log('[Hapyfish2_Alchemy_Bll_Arena::resetUserArena:'.$uid.']' . $e->getMessage(), 'db.err.arena');
	    	}

	    	//玩家上周排名
	    	$userLastRank = Hapyfish2_Alchemy_Cache_Arena::getUserLastRank($uid, $lastWeekScore);
	    	$userArena['lastWeekRank'] = $userLastRank;
    		$userArena['prizeGetted'] = 0;
    		Hapyfish2_Alchemy_Cache_Arena::updateUserScore($uid, 0);
	    }
	    
    	$vip = new Hapyfish2_Alchemy_Bll_Vip();
    	$add = $vip->getVipArena($uid);
    	$userArena['challengeTimes'] = 10 + $add[0];
    	$userArena['refreshTimes'] = 4 + $add[1];
    	$userArena['cd'] = 0;
    	//$userArena['prizeGetted'] = 0;
    	$userArena['fightUids'] = array();
    	$userArena['opponentList'] = $opponentList;
    	$userArena['lastRefreshTime'] = time();
    	
    	//竞技场等级提高后增加每日挑战次数
		$userArenaLevel = Hapyfish2_Alchemy_HFC_User::getUserArenaLevel($uid);
		$addChallengeTimes = $userArenaLevel - 1;
		$userArena['challengeTimes'] += $addChallengeTimes;
    	
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
    	$uidsList = $dalArena->getUidsByScore($userScore, self::OPPONENT_RANDNUM);
    
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
    	
    	shuffle($list);
    	
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
		$opponentListVo = self::ganOpponentVo($uid, $userArena['opponentList'], $userArena['fightUids']);
		
		//玩家当前排名
		$userRank = Hapyfish2_Alchemy_Cache_Arena::getUserRank($uid, $userScore);
		//根据积分获取奖励
		$userPrize = self::getPrizeByScore($userRank);

		$curWeekDay = date("w");
		//周一 以外时间不做检查
		if ( $curWeekDay != 1 ) {
			//getPrizeRemain,下次领奖时间
			$getPrizeRemain = strtotime("+0 week Monday");
		}
		else {
			$getPrizeRemain = strtotime("+1 week Monday");
		}
		
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'getPrizeRemain', $getPrizeRemain);
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
     * 拼接战斗对手信息列表
     * @param array $list
     * @param array $fightUids
     */
    public static function ganOpponentVo($uid, $list, $fightUids = null)
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
    		$bllVip = new Hapyfish2_Alchemy_Bll_Vip();
			//VIP等级信息
    		$friendVipInfo = $bllVip->getVipInfo($fid);
    		
			if ( in_array($fid, $fightUids) ) {
				$canFight = 0;
			}
			else {
				$canFight = 1;
			}
    		$opponentList[] = array('fid' => $fid,
    						'name' => $roleInfo['name'],
    						'className' => $roleInfo['face_class_name'],
    						'star' => $roleInfo['rp'],
    						'rank' => $rankNum,
    						'score' => $score,
    						'level' => $roleInfo['level'],
    						'vipLevel' => $friendVipInfo['level'],
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
    public static function initRank($uid, $from)
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
     * 查看战斗记录
     * @param int $uid
     * @param int $fid
     * @param int $id
     */
    public static function readFight($uid, $fid, $id)
    {
    	$info = Hapyfish2_Alchemy_Cache_Fight::loadFightInfo($fid, $id);
    	if ( !$info ) {
    		return -265;
    	}
    	
    	if ( $info['type'] != 7 ) {
    		return -265;
    	}
    	
        $aryRandomNum = $info['rnd_element'];
        $homeSide = $info['home_side'];
        $enemySide = $info['enemy_side'];
        $replayData = $info['content'];
        
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
        
        //拼接Vo数据返回前端
        //我方
        $roleList1 = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $enemySide);
        //敌方
        $roleList2 = Hapyfish2_Alchemy_Bll_MapCopy::genEnemyRolesVo($homeSide);
        
        $battle = array(
            'id' => $id,
            'bgClassName' => self::FIGHT_BG_CLASSNAME,
            'roleList' => array_merge($roleList1, $roleList2),
        	'talk' => $aryTalk
        );
        
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'BattleVo', $battle);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'RndNums', $aryRandomNum);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'replayData', $replayData);
        
    	return 1;
    }
    
    /**
     * 查看战报
     * @param int $uid
     */
    public static function getBattleRecord($uid)
    {
		//get user arena feed
        $feeds = self::getFeedData($uid);
        
        if (empty($feeds)) {
        	$feeds = array();
        }
        
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'records', $feeds);
        
    	return 1;
    }
    
	public static function getFeedData($uid)
	{
		$data = Hapyfish2_Alchemy_Cache_Arena::getArenaFeed($uid);
		if ($data === false) {
			return array();
		}
		
		$i = 0;
		$battleCnt = 0;
		$result = array();
		foreach ($data as $feed) {
			if ( $feed[1] == $uid ) {
				$fid = $feed[2];
			}
			else {
				$fid = $feed[1];
			}
			
			//平台信息，名字
			$platUser = Hapyfish2_Platform_Bll_User::getUser($fid);
			
			$battleId = 0;
			if ( $feed[6] != 0 ) {
				if ( $battleCnt < 5 ) {
					$battleCnt++;
					$battleId = $feed[6];
				}
			}
			
			$result[] = array(
				'fid' => $fid,
				'name' => $platUser['name'],
				'initiative' => $feed[3],
				'win' => $feed[4],
				//'rank' => $feed[5],
				'battleId' => $battleId
			);
			$i++;
		}
		
		return $result;
	}
	
	public static function insertArenaFeed($feed)
	{
	    $uid = $feed['uid'];
	    
	    $id = self::getNewArenaFeedId($uid);
	    
	    $newfeed = array(
	    	$feed['uid'], $feed['actor'], $feed['target'], $feed['initiative'], $feed['win'], $feed['rank'], $feed['battleId'], $id
	    );
	    
	    Hapyfish2_Alchemy_Cache_Arena::insertArenaFeed($uid, $newfeed);
	}
	
    public static function getNewArenaFeedId($uid)
    {
        try {
            $dalUserSequence = Hapyfish2_Alchemy_Dal_UserSequence::getDefaultInstance();
            return $dalUserSequence->get($uid, 'k', 1);
        } catch (Exception $e) {
        	info_log('[Hapyfish2_Alchemy_Bll_Arena::getNewArenaFeedId:'. $uid. ']' . $e->getMessage(), 'db.err');
        }
        return 0;
    }

	/**
	 * 领取竞技场奖励
	 */
	public function getPrize($uid)
	{
		$userArena = Hapyfish2_Alchemy_Cache_Arena::getUserArena($uid);
		if ( $userArena['prizeGetted'] != 0 ) {
			return -266;
		}
		
		/* //玩家当前积分
		$userScore = Hapyfish2_Alchemy_Cache_Arena::getUserScore($uid);
		//玩家当前排名
		$userRank = Hapyfish2_Alchemy_Cache_Arena::getUserRank($uid, $userScore);
		 */
		$userLastRank = $userArena['lastWeekRank'];
		
		//根据积分获取奖励
		$userPrize = self::getPrizeByScore($userLastRank);
		
		foreach ( $userPrize as $key => $value ) {
			if ( $key == 'coin' ) {
				Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $value,3);
			}
			else if ( $key == 'feats' ) {
				$addCid = FEATS_CID;
				$addCount = $value;
				Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, $addCid, $addCount);
			}
		}
		
		$userArena['prizeGetted'] = 1;
		Hapyfish2_Alchemy_Cache_Arena::updateUserArena($uid, $userArena);
		
		return 1;
	}
	
    /**
     * 根据积分，计算奖励
     * @param int $score
     */
    private static function getPrizeByScore($rank)
    {
    	//通过排名，查询对应奖励
    	$prizeBasic = Hapyfish2_Alchemy_Cache_BasicExt::getArenaPrizeByRank($rank);
    	
    	$addItems = array('coin' => $prizeBasic['coin'],
    					  'feats' => $prizeBasic['feats']);
    	return $addItems;
    }
    
    /**
     * 获取积分更改值
     * 
     * @param int $type,1:挑战胜利,2:失败
     * @param int $score,挑战者当前积分
     * @param int $rivalScore,被挑战者当前积分
     */
    public static function _getScoreChange($type, $score, $rivalScore)
    {
    	if ( $type == 1 ) {
    		$KI1 = 25;
    		$KI2 = 2;
    		$LI = 1000;
    		//获得积分值为△SI=KI1-KI2*int(S1/LI)
    		$incScore = $KI1 - $KI2 * floor($score / $LI);
    		$incScore = $incScore < 1 ? 1 : $incScore;
    		if ( $score < $rivalScore ) {
    			$incScore = floor($incScore * (1 + ($rivalScore - $score)/100*0.20));
    		}
    		else {
    			$incScore = floor($incScore / (1 + ($score - $rivalScore)/100*0.20));
    		}

    		//补齐预扣分数
    		$decScore = self::_getScoreChange(2, $score, $rivalScore);
    		$scoreChang = $incScore + $decScore;
    	}
    	else {
    		$KD1 = 5;
    		$KD2 = 1;
    		$LD = 1000;
    		//扣除积分值为△SD=KD1+KD2*int(S1/LD)
    		$decScore = $KD1 + $KD2 * floor($score / $LD);
    		
    		if ( $score < $rivalScore ) {
    			$decScore = floor($decScore * (1 + ($score - $rivalScore)/100*0.20));
    		}
    		else {
    			$decScore = floor($decScore / (1 + ($rivalScore - $score)/100*0.20));
    		}
    		
    		$scoreChang = abs($decScore);
    	}
    	
    	return $scoreChang;
    }
    
    /**
     * 获取战斗奖励
     * @param int $type,1:挑战胜利,2:失败
     */
    public static function _getFightPrize($uid, $type)
    {
    	$awardList = Hapyfish2_Alchemy_Cache_BasicExt::getArenaAwardListByType($type);
    	$itemList = array();
    	$randAry = array();
    	foreach ( $awardList as $award ) {
    		$itemList[$award['id']] = array('id' => (int)$award['id'],
    							'cid' => (int)$award['cid'],
    							'num' => (int)$award['num'],
    							'status' => (int)$award['status']);
    		for( $i=0;$i<$award['pro'];$i++ ) {
    			$randAry[] = $award['id'];
    		}
    	}
    	$rand = array_rand($randAry);
    	$randKey = $randAry[$rand];
    	$item = $itemList[$randKey];
    	
    	//发放奖励
    	Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $item['cid'], $item['num']);
        
    	$itemListVo = array();
    	foreach ( $itemList as $m ) {
    		$itemListVo[] = $m;
    	}
        $getItemIndex = $item['id'];
        $prize = array('itemList' => $itemListVo,
        			   'getItemIndex' => $getItemIndex);
        
        return $prize;
    }
	
    public static function completeCd($uid)
    {
		//玩家竞技场信息
		$userArena = Hapyfish2_Alchemy_Cache_Arena::getUserArena($uid);
		$nowTm = time();
		$remainTime = $userArena['cd'] - $nowTm;
    	if ( $remainTime <= 0 ) {
    		return -200;
    	}
    	
		//半小时一个沙漏,cid = 2715;
		$gameData = Hapyfish2_Alchemy_Bll_BasicInfo::getGameData();
		$needGoodsCid =	$gameData['shalouCid'];
		$needCardCount = 1;
		$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
		if ( !isset($userGoods[$needGoodsCid]) || $userGoods[$needGoodsCid]['count'] < $needCardCount )	{
			return -212;
		}
		
		$userArena['cd'] = 0;
		Hapyfish2_Alchemy_Cache_Arena::updateUserArena($uid, $userArena);
    
		//使用沙漏
		if ( $needCardCount	> 0	) {
			Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid,	$needGoodsCid, $needCardCount);
		}

		Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'arenaCd');
		
		return 1;
    }
    
    /**
     * 重置竞技场排行信息，每周日24:00重置
     * 
     */
    public static function resetArenaRank()
    {
    	$curWeekDay = date("w");
    	
    	//周一 以外时间不做检查
    	if ( $curWeekDay != 1 ) {
    		return;
    	}

    	$todayTm = strtotime(date('Ymd'));

    	//上周最后时间, 周一 00:00
    	$lastWeekEndTm = $todayTm;
    	
    	//竞技场信息
    	$arenaInfo = Hapyfish2_Alchemy_Cache_Arena::getArena();
    	if ( $arenaInfo['last_time'] >= $lastWeekEndTm ) {
    		return;
    	}
    	
    	$lastWeek = date("Ymd", $lastWeekEndTm);
    	
    	$dalArena = Hapyfish2_Alchemy_Event_Dal_Arena::getDefaultInstance();
    	
    	try {
    		//备份上周排行数据
    		$bakTableName = 'alchemy_arena_rank_'.$lastWeek;
    		$sqlBakLast = "DROP TABLE IF EXISTS `$bakTableName`;CREATE TABLE `$bakTableName` SELECT * FROM `alchemy_arena_rank_lastweek`;";
    		$dalArena->updateArenaTable($sqlBakLast);
    	}
    	catch (Exception $e) {
        	info_log('[Hapyfish2_Alchemy_Bll_Arena::resetArenaRank:del]' . $e->getMessage(), 'db.err.arena');
    	}
    	
    	try {
    		//删除上周数据
    		$sqlTruncate = "TRUNCATE TABLE `alchemy_arena_rank_lastweek`;";
    		$dalArena->updateArenaTable($sqlTruncate);
    		
    		//记录本周数据
    		$sqlBakCur = "INSERT INTO `alchemy_arena_rank_lastweek` SELECT * FROM `alchemy_arena_rank`;";
    		$okBak = $dalArena->updateArenaTable($sqlBakCur);
    		
    		if ( !$okBak ) {
    			return -100;
    		}
    		//重置本周数据
    		$sqlReset = "UPDATE `alchemy_arena_rank` SET `score`=0;";
    		$okUpdate = $dalArena->updateArenaTable($sqlReset);
    	}
    	catch (Exception $e) {
    		info_log('[Hapyfish2_Alchemy_Bll_Arena::resetArenaRank:update]' . $e->getMessage(), 'db.err.arena');
    	}
    	
    	if ( !$okUpdate ) {
    		return -100;
    	}
    	
    	//更新重置时间
    	$arenaInfo['last_time'] = time();
    	Hapyfish2_Alchemy_Cache_Arena::updateArena($arenaInfo);
    	
    	//重置排行榜
    	Hapyfish2_Alchemy_Cache_Arena::reloadRankList();

    	return;
    }
    
    
    
}