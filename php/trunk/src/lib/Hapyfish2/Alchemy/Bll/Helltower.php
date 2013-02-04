<?php

class Hapyfish2_Alchemy_Bll_Helltower
{
	const WATER_SC_ID = 3001;
	const ABYSS_SC_ID = 3001;
	const SWEEP_TIME = 20;
	
	public static function initShop($uid)
	{
		$data = array();
		$shop = Hapyfish2_Alchemy_Cache_Basic::getHelltowerShop();
		foreach($shop as $k=>$v){
			$data[] = array_values($v);
		}
		$userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterStatus($uid);
		$userAbyss = Hapyfish2_Alchemy_Cache_Helltower::getUserAbyssStatus($uid);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'goods', $data);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'bestWaterDungeonLevel', $userwater['max']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'bestAbyssDungeonLevel', $userAbyss['max']);
		return 1;
		
	}
	
	public static function initWaterDungeon($uid)
	{
		$date = date('Ymd');
		$award = array();
		$config = Hapyfish2_Alchemy_Cache_Basic::getHelltowerConfig(1);
		if($config){
			foreach($config as $k => $v){
				$award[] = $v['awards'];
			}
		}
		$userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterStatus($uid);
		$max = isset($userwater['max'])?$userwater['max']:0;
		$curLevel = isset($userwater['current'])?$userwater['current']:0;
		$totalexp = isset($userwater['totalexp'])?$userwater['totalexp']:0;
//		$monster = Hapyfish2_Alchemy_Cache_Basic::getMonsterInfo($config['monster']);
		$userRank = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterRank($uid);
		if($userwater && $userwater['refreshTime'] == $date){
			$challenged = 1;
		}else{
			$challenged = 0;
		}
		$canEnter = self::canEnterWater($uid,$userwater);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'canEnter', $canEnter);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'prize', $award);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'bestLevel', $max);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'curLevel', $curLevel);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'totalExp', $totalexp);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'challenged', $challenged);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rank', $userRank);
		return 1;
	}
	
	public static function initAbyssDungeon($uid)
	{
		$date = date('Ymd');
		$award = array();
		$config = Hapyfish2_Alchemy_Cache_Basic::getHelltowerConfig(2);
		if($config){
			foreach($config as $k => $v){
				$award[] = $v['awards'];
			}
		}
		$userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserAbyssStatus($uid);
		$max = isset($userwater['max'])?$userwater['max']:0;
		$curLevel = isset($userwater['current'])?$userwater['current']:0;
		$totalexp = isset($userwater['totalexp'])?$userwater['totalexp']:0;
//		$monster = Hapyfish2_Alchemy_Cache_Basic::getMonsterInfo($config['monster']);
		$userRank = Hapyfish2_Alchemy_Cache_Helltower::getUserAbyssRank($uid);
		if($userwater && $userwater['refreshTime'] == $date){
			$challenged = 1;
		}else{
			$challenged = 0;
		}
		$canEnter = self::canEnterAbyss($uid,$userwater);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'canEnter', $canEnter);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'prize', $award);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'bestLevel', $max);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'curLevel', $curLevel);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'totalExp', $totalexp);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'challenged', $challenged);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rank', $userRank);
		return 1;
	}
	
	public static function resetWaterDungeon($uid)
	{
		$date = date('Ymd');
		$userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterStatus($uid);
		if($userwater['refreshTime'] == $date){
			return -720;
		}
		$userwater['current'] = 1;
		$userwater['totalexp'] = 0;
		$userwater['totalcoin'] = 0;
		$userwater['refreshTime'] = $date;
		$userwater['type'] = 1;
		Hapyfish2_Alchemy_Cache_Helltower::updateUserWaterStatus($uid, $userwater);
		$userRefresh = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterRefiesh($uid);
		$userRefresh['refresh'] = 0;
		$userRefresh['status'] = 1;
		Hapyfish2_Alchemy_Cache_Helltower::updateUserWaterRefiesh($uid, $userRefresh);
		self::initWaterDungeon($uid);
		return 1;
	}
	
	public static function resetAbyssDungeon($uid)
	{
		$date = date('Ymd');
		$userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserAbyssStatus($uid);
		if($userwater['refreshTime'] == $date){
			return -720;
		}
		$userwater['current'] = 1;
		$userwater['totalexp'] = 0;
		$userwater['open'] = 0;
		$userwater['totalcoin'] = 0;
		$userwater['refreshTime'] = $date;
		$userwater['type'] = 2;
		Hapyfish2_Alchemy_Cache_Helltower::updateUserAbyssStatus($uid, $userwater);
		$userRefresh = Hapyfish2_Alchemy_Cache_Helltower::getUserAbyssRefiesh($uid);
		$userRefresh['refresh'] = 0;
		$userRefresh['status'] = 1;
		Hapyfish2_Alchemy_Cache_Helltower::updateUserAbyssRefiesh($uid, $userRefresh);
		self::initAbyssDungeon($uid);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'abyssDungeonOpen', 0);
		return 1;
	}
	
	public static function buyItemFromHellTowerShop($uid, $cid, $num=1)
	{
		$needCid = 4000315;
		$info = Hapyfish2_Alchemy_Cache_Basic::getHelltowerShopInfo($cid);
		if(!$info){
			return -200;
		}
		if($info['needWater'] > 0){
			$userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterStatus($uid);
			if($userwater['max'] < $info['needWater']){
				return -721;
			}
		}
		
		if($info['needAbyss'] > 0){
			$userAbyss = Hapyfish2_Alchemy_Cache_Helltower::getUserAbyssStatus($uid);
			if($userAbyss['max'] < $info['needAbyss']){
				return -721;
			}
		}
		
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		$vipInfo = $vip->getVipInfo($uid);
		$price = $info['price'];
		if($vipInfo['level'] >= 1 && $vipInfo['vipStatus'] == 1){
			if($info['vip_price'] > 0){
			}
			$price = $info['vip_price'];
		}
		
		$needNum = $price*$num;
		$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
        if ( !isset($userGoods[$needCid]) || $userGoods[$needCid]['count'] < $needNum ) {
            return -309;
        }
        
        Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $cid, $num);
        Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $needCid, $needNum);
        return 1;
	}
	
	public static function rushWaterDungeon($uid)
	{
		$userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterStatus($uid);
		$cur = $userwater['current'];
		if($cur > 100){
			return -200;
		}
		$curInfo = Hapyfish2_Alchemy_Cache_Basic::getHelltowerInfo(1, $cur);
		$monster = Hapyfish2_Alchemy_Cache_Basic::getMonsterInfo($curInfo['monster']);
		if(!$monster){
			return -200;
		}
		$enemy['name'] = $monster['name'];
		$enemy['className'] = $monster['class_name'];
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'enemy', $enemy);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'curLevel', $cur);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'totalExp', $userwater['totalexp']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'totalMoney', $userwater['totalcoin']);
		return 1;
	}
	
	public static function rushAbyssDungeon($uid)
	{
		$userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserAbyssStatus($uid);
		$cur = $userwater['current'];
		if($cur > 30){
			return -200;
		}
		$curInfo = Hapyfish2_Alchemy_Cache_Basic::getHelltowerInfo(2, $cur);
		$monster = Hapyfish2_Alchemy_Cache_Basic::getMonsterInfo($curInfo['monster']);
		if(!$monster){
			return -200;
		}
		$enemy['name'] = $monster['name'];
		$enemy['className'] = $monster['class_name'];
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'enemy', $enemy);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'curLevel', $cur);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'totalExp', $userwater['totalexp']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'totalMoney', $userwater['totalcoin']);
		return 1;
	}
		
	public static function fightWaterDungeon($uid)
	{
		$userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterStatus($uid);
		$cur = $userwater['current'];
		$aryRnd = array();
		for ($i=0; $i<20; $i++) {
            $aryRnd[] = mt_rand(1,1000);
		}
		$homeSide = Hapyfish2_Alchemy_Bll_FightCorps::getHomeSideUnitList($uid);
		if (!$homeSide) {
		    return -321;
		}
	    $enemySide = self::getenemySide(1, $cur);
		if (!$enemySide) {
		    return -322;
		}
		$curInfo = Hapyfish2_Alchemy_Cache_Basic::getHelltowerInfo(1, $cur);
		$info['uid'] = $uid;
		//0-普通打怪 1-侵略 2-反抗 3-救援 7-1v1竞技场 8-对白触发战斗 9-新手引导战斗
		$info['type'] = 10;
		$info['status'] = 0;
		$info['rnd_element'] = $aryRnd;
		$info['home_side'] = $homeSide;
		$info['content'] = array();
		$info['create_time'] = time();
		$info['current'] = $cur;
		$usrScene = Hapyfish2_Alchemy_HFC_User::getUserScene($uid);

		$info['enemy_id'] = $usrScene['cur_scene_id'] . '-1-' . $cur;
		$saveDb = (defined('ENABLE_FIGHT_DB_LOG') && ENABLE_FIGHT_DB_LOG);
	    //}

        //战斗宣言
		$canTalk = false;
        $aryTalk = array();
        $cntHomeSide = count($homeSide);
        $rndTalkRole = mt_rand(1, $cntHomeSide);
        $idx = 0;
        foreach ($homeSide as $data) {
        	$idx ++;
            //if ($data['id'] == 0) {
            if ($idx == $rndTalkRole) {
                $talks = Hapyfish2_Alchemy_Cache_Basic::getFightDeclareByJob($data['job']);
                if ($talks) {
                    $rndKey = mt_rand(1, count($talks));
                    $aryTalk[] = array((int)$data['matrix_pos'], $talks[$rndKey-1]);
                }
                break;
            }
        }

        $newMonsterAry = array();
        $vip = new Hapyfish2_Alchemy_Bll_Vip();
        $vipAddition = array();
	    foreach ($enemySide as $key => $data) {
	        if ($data['is_boss']) {
	        	$canTalk = true;
    	        if ( isset($data['talk']) && $data['talk'] ) {
                    $aryTalk[] = array((int)$data['matrix_pos'], $data['talk']);
                }
	        }
	        if ( !in_array($data['cid'], $newMonsterAry) ) {
		        //添加遇到怪物记录，并判断是否有首杀奖励
	            $isNewMonster = Hapyfish2_Alchemy_HFC_Monster::isNewMonster($uid, $data['cid']);
	            if ( $isNewMonster ) {
	            	$newMonsterAry[] = $data['cid'];
			        $data['award_conditions'] = array_merge($data['award_conditions'], $data['first_award_conditions']);
			        $enemySide[$key] = $data;
	            
			        $newHelpId = 0;
					if ( $data['cid'] == 14571 ) {
						$newHelpId = 8;
					}
					/* else if ( $data['cid'] == 15271 ) {
						$newHelpId = 16;
					} */
					if ( $newHelpId > 0 ) {
                        Hapyfish2_Alchemy_Bll_Help::startHelp($uid, $newHelpId);
					}
	            }
	        }
            $vip->setAddition($uid, $data['award_conditions']);
            //添加图鉴
            $illResult = Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $data['tid']);
        }
        //非BOSS战，不宣言
        if ( !$canTalk ) {
        	$aryTalk = array(); 
        }
        
//        $vipAddition = $vip->getAddition();
        //首次遇到怪物记录
        $info['new_monster'] = implode(',', $newMonsterAry);
        
        //保存初始战斗信息
		$info['enemy_side'] = $enemySide;
        Hapyfish2_Alchemy_Cache_Fight::saveFightInfo($uid, $info, $saveDb);

        //拼接Vo数据返回前端
        //我方
        $roleList1 = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
        //敌方
        $roleList2 = Hapyfish2_Alchemy_Bll_MapCopy::genEnemyRolesVo($enemySide);
        $roleList2[0]['items'] =  $curInfo['awards'];
        //可援助攻击
        $aryAssist = array();
        $assCnt = 0;
        $extCnt = 0;
        $assistInfo = Hapyfish2_Alchemy_HFC_User::getUserFightAssistInfo($uid);
        $assCnt = $assistInfo['assist_bas_count'];
        $extCnt = $assistInfo['assist_ext_count'];
        $aryAssist = Hapyfish2_Alchemy_Cache_Fight::getFightFriendAssistInfo($uid);
        if (!$aryAssist) {
            $aryAssist = self::getFriendAssistVo($uid);
            Hapyfish2_Alchemy_Cache_Fight::setFightFriendAssistInfo($uid, $aryAssist);
        }
		$skip = $vip->getVipSkip($uid);
		$jumpTime = $skip['max'] - $skip['num'] >0 ? $skip['max'] - $skip['num']:0;
		$invite = Hapyfish2_Alchemy_HFC_User::getTotalInvite($uid);
		$isInvite = 0;
		if($invite > 0){
			$isInvite = 1;
		}
        $battle = array(
            'id' => $info['fid'],
            'bgClassName' => 'fbg.100.shuilao',
            'roleList' => array_merge($roleList1, $roleList2),
        	'talk' => $aryTalk,
//            'friendSkill' => $aryAssist,
            'assCnt' => $assCnt,
            'extCnt' => $extCnt,
        	'jumpTimes'=>$jumpTime,
        	'vipPrize'=>array(),
        	'isInvite'=>$isInvite
        );
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'RndNums', $info['rnd_element']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'BattleVo', $battle);
		return 1;
	}
	
	public static function fightAbyssDungeon($uid)
	{
		$userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserAbyssStatus($uid);
		$cur = $userwater['current'];
		$aryRnd = array();
		for ($i=0; $i<20; $i++) {
            $aryRnd[] = mt_rand(1,1000);
		}
		$homeSide = Hapyfish2_Alchemy_Bll_FightCorps::getHomeSideUnitList($uid);
		if (!$homeSide) {
		    return -321;
		}
	    $enemySide = self::getenemySide(2, $cur);
	    $curInfo = Hapyfish2_Alchemy_Cache_Basic::getHelltowerInfo(2, $cur);
		if (!$enemySide) {
		    return -322;
		}
		$info['uid'] = $uid;
		//0-普通打怪 1-侵略 2-反抗 3-救援 7-1v1竞技场 8-对白触发战斗 9-新手引导战斗
		$info['type'] = 11;
		$info['current'] = $cur;
		$info['status'] = 0;
		$info['rnd_element'] = $aryRnd;
		$info['home_side'] = $homeSide;
		$info['content'] = array();
		$info['create_time'] = time();

		$usrScene = Hapyfish2_Alchemy_HFC_User::getUserScene($uid);
		$info['enemy_id'] = $usrScene['cur_scene_id'] .'-2-'.$cur;
		$saveDb = (defined('ENABLE_FIGHT_DB_LOG') && ENABLE_FIGHT_DB_LOG);
	    //}

        //战斗宣言
		$canTalk = false;
        $aryTalk = array();
        $cntHomeSide = count($homeSide);
        $rndTalkRole = mt_rand(1, $cntHomeSide);
        $idx = 0;
        foreach ($homeSide as $data) {
        	$idx ++;
            //if ($data['id'] == 0) {
            if ($idx == $rndTalkRole) {
                $talks = Hapyfish2_Alchemy_Cache_Basic::getFightDeclareByJob($data['job']);
                if ($talks) {
                    $rndKey = mt_rand(1, count($talks));
                    $aryTalk[] = array((int)$data['matrix_pos'], $talks[$rndKey-1]);
                }
                break;
            }
        }

        $newMonsterAry = array();
        $vip = new Hapyfish2_Alchemy_Bll_Vip();
        $vipAddition = array();
	    foreach ($enemySide as $key => $data) {
	        if ($data['is_boss']) {
	        	$canTalk = true;
    	        if ( isset($data['talk']) && $data['talk'] ) {
                    $aryTalk[] = array((int)$data['matrix_pos'], $data['talk']);
                }
	        }
	        if ( !in_array($data['cid'], $newMonsterAry) ) {
		        //添加遇到怪物记录，并判断是否有首杀奖励
	            $isNewMonster = Hapyfish2_Alchemy_HFC_Monster::isNewMonster($uid, $data['cid']);
	            if ( $isNewMonster ) {
	            	$newMonsterAry[] = $data['cid'];
			        $data['award_conditions'] = array_merge($data['award_conditions'], $data['first_award_conditions']);
			        $enemySide[$key] = $data;
	            
			        $newHelpId = 0;
					if ( $data['cid'] == 14571 ) {
						$newHelpId = 8;
					}
					/* else if ( $data['cid'] == 15271 ) {
						$newHelpId = 16;
					} */
					if ( $newHelpId > 0 ) {
                        Hapyfish2_Alchemy_Bll_Help::startHelp($uid, $newHelpId);
					}
	            }
	        }
//            $vip->setAddition($uid, $data['award_conditions']);
            //添加图鉴
            $illResult = Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $data['tid']);
        }
        //非BOSS战，不宣言
        if ( !$canTalk ) {
        	$aryTalk = array(); 
        }
        
//        $vipAddition = $vip->getAddition();
        //首次遇到怪物记录
        $info['new_monster'] = implode(',', $newMonsterAry);
        
        //保存初始战斗信息
		$info['enemy_side'] = $enemySide;
        Hapyfish2_Alchemy_Cache_Fight::saveFightInfo($uid, $info, $saveDb);

        //拼接Vo数据返回前端
        //我方
        $roleList1 = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
        //敌方
        $roleList2 = Hapyfish2_Alchemy_Bll_MapCopy::genEnemyRolesVo($enemySide);
        $roleList2[0]['items'] = $curInfo['awards'];
        //可援助攻击
        $aryAssist = array();
        $assCnt = 0;
        $extCnt = 0;
        $assistInfo = Hapyfish2_Alchemy_HFC_User::getUserFightAssistInfo($uid);
        $assCnt = $assistInfo['assist_bas_count'];
        $extCnt = $assistInfo['assist_ext_count'];
        $aryAssist = Hapyfish2_Alchemy_Cache_Fight::getFightFriendAssistInfo($uid);
        if (!$aryAssist) {
            $aryAssist = self::getFriendAssistVo($uid);
            Hapyfish2_Alchemy_Cache_Fight::setFightFriendAssistInfo($uid, $aryAssist);
        }
		$skip = $vip->getVipSkip($uid);
		$jumpTime = $skip['max'] - $skip['num'] >0 ? $skip['max'] - $skip['num']:0;
		$invite = Hapyfish2_Alchemy_HFC_User::getTotalInvite($uid);
		$isInvite = 0;
		if($invite > 0){
			$isInvite = 1;
		}
        $battle = array(
            'id' => $info['fid'],
            'bgClassName' => 'fbg.101.shenyuan',
            'roleList' => array_merge($roleList1, $roleList2),
        	'talk' => $aryTalk,
//            'friendSkill' => $aryAssist,
            'assCnt' => $assCnt,
            'extCnt' => $extCnt,
        	'jumpTimes'=>$jumpTime,
        	'vipPrize'=>array(),
        	'isInvite'=>$isInvite
        );
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'RndNums', $info['rnd_element']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'BattleVo', $battle);
		return 1;
	}
	
	
	public static function getenemySide($type, $num)
	{
		$curInfo = Hapyfish2_Alchemy_Cache_Basic::getHelltowerInfo($type, $num);
		$posMonster = $curInfo['monster_stations'];
		$eid = 1;
		 foreach ($posMonster as $pos=>$cid) {
            $monsterInfo = $monster = Hapyfish2_Alchemy_Cache_Basic::getMonsterInfo($cid);
            if ($monsterInfo) {
                $monsterInfo['id'] = (int)$eid;
                $monsterInfo['matrix_pos'] = (int)$pos;
                $monsterInfo['hp_max'] = (int)$monsterInfo['hp'];
                $monsterInfo['mp_max'] = (int)$monsterInfo['mp'];
                $monsterInfo['skill'] = json_decode($monsterInfo['skill'], true);
                $monsterInfo['weapon'] = json_decode($monsterInfo['weapon'], true);
                $monsterInfo['award_conditions'] = Hapyfish2_Alchemy_Bll_MapCopy::_preCalcAwardCondition($monsterInfo['award_conditions']);
                $monsterInfo['first_award_conditions'] = Hapyfish2_Alchemy_Bll_MapCopy::_preCalcAwardCondition($monsterInfo['first_award_conditions']);
                unset($monsterInfo['content']);
                unset($monsterInfo['avatar_class_name']);

                //add weapon prop to attribute prop
                Hapyfish2_Alchemy_Bll_Fight::addWeaponProp($monsterInfo);
                $enemySideInfo[(int)$pos] = $monsterInfo;
                $eid ++;
            }
        }
	    return $enemySideInfo;
	}
	
    private static function _synWaterRank($uid, $max)
    {
    	$time = time();
		//local cache
    	$locKey = 'a:b:water:rank';
    	$loc = Hapyfish2_Cache_LocalCache::getInstance();
    	$minLine = $loc->get($locKey);
    	//memcache
    	$mkey = 'a:b:water:rank';
		$eventRank = Hapyfish2_Cache_Factory::getEventRank();
    	if (empty($minLine)) {
    		$minLine = 1;
	    	$lstRank = $eventRank->get($mkey);
			if (!empty($lstRank)) {
				$cnt = count($lstRank);
				if ($cnt >=1000) {
					$minLine = $lstRank[$cnt - 1][1];
				}
			}
			$loc->set($locKey, $minLine, true, 600);
    	} 

	    if ($max < $minLine) {
			return;
    	}
    	$info = array($uid, $max, $time);
    	$eventRank->insert($mkey, $info);
    }
    
    private static function _synAbyssRank($uid, $max)
    {
    	$time = time();
		//local cache
    	$locKey = 'a:b:abyss:rank';
    	$loc = Hapyfish2_Cache_LocalCache::getInstance();
    	$minLine = $loc->get($locKey);
    	//memcache
    	$mkey = 'a:b:abyss:rank';
		$eventRank = Hapyfish2_Cache_Factory::getEventRank();
    	if (empty($minLine)) {
    		$minLine = 0;
	    	$lstRank = $eventRank->get($mkey);
			if (!empty($lstRank)) {
				$cnt = count($lstRank);
				if ($cnt >=1000) {
					$minLine = $lstRank[$cnt - 1][1];
				}
			}
			$loc->set($locKey, $minLine, true, 600);
    	} 

	    if ($max <= $minLine) {
			return;
    	}
    	$info = array($uid, $max, $time);
    	$eventRank->insert($mkey, $info);
    }
	
    public static function initWaterDungeonTop($uid)
    {
    	$totalRank = array();
    	$list = Hapyfish2_Alchemy_Cache_Helltower::getWaterRank();
    	$userRank = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterRank($uid);
    	$myInfo = Hapyfish2_Platform_Bll_User::getUser($uid);
    	if($list){
    		$top100 = array_chunk($list,100);
    		foreach($top100[0] as $k => $v){
    			$rank = array();
    			$userInfo = Hapyfish2_Platform_Bll_User::getUser($v[0]);
    			$rank['name'] = $userInfo['name'];
    			$rank['rank'] = $k + 1;
    			$rank['bestLevel'] = $v[1];
    			$rank['head'] = $userInfo['figureurl'];
    			$rank['uid'] = $v[0];
    			$totalRank[] = $rank;
    		}
    	}
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'top100', $totalRank);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'myRank', $userRank);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'myHead', $myInfo['figureurl']);
    	return 1;
    }
    
 	public static function initAbyssDungeonTop($uid)
    {
    	$totalRank = array();
    	$list = Hapyfish2_Alchemy_Cache_Helltower::getAbyssRank();
    	$userRank = Hapyfish2_Alchemy_Cache_Helltower::getUserAbyssRank($uid);
    	$myInfo = Hapyfish2_Platform_Bll_User::getUser($uid);
    	if($list){
    		$top100 = array_chunk($list,100);
    		foreach($top100[0] as $k => $v){
    			$rank = array();
    			$userInfo = Hapyfish2_Platform_Bll_User::getUser($v[0]);
    			$rank['name'] = $userInfo['name'];
    			$rank['rank'] = $k + 1;
    			$rank['bestLevel'] = $v[1];
    			$rank['head'] = $userInfo['figureurl'];
    			$rank['uid'] = $v[0];
    			$totalRank[] = $rank;
    		}
    	}
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'top100', $totalRank);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'myRank', $userRank);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'myHead', $myInfo['figureurl']);
    	return 1;
    }
    
    public static function completeFightWater($uid, $info, $rst, $id, $win)
    {
//		if ( $info['type'] != 10 ) {
//			return;
//		}
	    $userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterStatus($uid);
	    $addExp = 0;
		//挑战胜利
		if ( $win == Hapyfish2_Alchemy_Bll_Fight_Simulator::RESULT_WIN ) {
			$curInfo = Hapyfish2_Alchemy_Cache_Basic::getHelltowerInfo(1, $info['current']);
			$awardItem = $curInfo['awards'];
    		$changeResult = array();
            self::awardCondition($uid, $awardItem);
			if($info['current'] > $userwater['max']){
				$userwater['max'] = $info['current'];
			}
			$userwater['current'] += 1;
		 	foreach($awardItem as $k=>$v){
            	if($v['type'] == 2){
            		if ($v['id'] == 'coin') {
						$userwater['totalcoin'] += $v['num'];
            		}
            		if ($v['id'] == 'battleExp') {
            			$userwater['totalexp'] += $v['num'];
            			$addExp += $v['num'];
            		}
            	}
            }
             self::_synWaterRank($uid, $userwater['max']);
			 Hapyfish2_Alchemy_Cache_Helltower::updateUserWaterStatus($uid, $userwater);
		}
		else {
			$date = date('Ymd');
	        $userRefresh = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterRefiesh($uid);
			if($userwater['refreshTime'] == $date){
	        	 $userRefresh['status'] = 0;
	        }else{
	        	$userRefresh['status'] = 1;
	        }
	        $userRefresh['data'] = $userwater;
	        $userRefresh['refresh'] += 1;
	        $userRefresh['date'] = $date;
	       
	       	$userwater['current'] = 1;
	       	$userwater['totalcoin'] = 0;
	       	$userwater['totalexp'] = 0;
	       	$userwater['refreshTime'] = $date;
			if($userRefresh['refresh'] > 1){
	       		$needGem = 10;
	       	}else{
	       		$needGem = 0;
	       	}
	       	Hapyfish2_Alchemy_Cache_Helltower::updateUserWaterStatus($uid, $userwater);
	       	Hapyfish2_Alchemy_Cache_Helltower::updateUserWaterRefiesh($uid, $userRefresh);
	       	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'revivalGem', $needGem);
		}
		$userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterStatus($uid);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'totalExp', $userwater['totalexp']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'totalMoney', $userwater['totalcoin']);
    	return $addExp;
    }
    
    public static function completeFightAbyss($uid, $info, $rst, $id, $win)
    {
		$addExp = 0;
	     $userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserAbyssStatus($uid);
		//挑战胜利
		if ( $win == Hapyfish2_Alchemy_Bll_Fight_Simulator::RESULT_WIN ) {
			$curInfo = Hapyfish2_Alchemy_Cache_Basic::getHelltowerInfo(2, $info['current']);
			$awardItem = $curInfo['awards'];
    		$changeResult = array();
            self::awardCondition($uid, $awardItem);
			if($info['current'] > $userwater['max']){
				$userwater['max'] = $info['current'];
			}
			$userwater['current'] += 1;
		 	foreach($awardItem as $k=>$v){
            	if($v['type'] == 2){
            		if ($v['id'] == 'coin') {
						$userwater['totalcoin'] += $v['num'];
            		}
            		if ($v['id'] == 'battleExp') {
            			$userwater['totalexp'] += $v['num'];
            			$addExp += $v['num'];
            		}
            	}
            }
            self::_synAbyssRank($uid, $userwater['max']);
			Hapyfish2_Alchemy_Cache_Helltower::updateUserAbyssStatus($uid, $userwater);
		}//挑战失败
		else {
	        $date = date('Ymd');
	        $userRefresh = Hapyfish2_Alchemy_Cache_Helltower::getUserAbyssRefiesh($uid);
	        if($userwater['refreshTime'] == $date){
	        	 $userRefresh['status'] = 0;
	        }else{
	        	$userRefresh['status'] = 1;
	        }
	        $userRefresh['data'] = $userwater;
	        $userRefresh['refresh'] += 1;
	        $userRefresh['date'] = $date;
	       	$userwater['current'] = 1;
	       	$userwater['totalcoin'] = 0;
	       	$userwater['totalexp'] = 0;
	       	$userwater['refreshTime'] = $date;
	        $userwater['open'] = 0;
	        Hapyfish2_Alchemy_Cache_Helltower::updateUserAbyssStatus($uid, $userwater);
	       	if($userRefresh['refresh'] > 1){
	       		$needGem = 10;
	       	}else{
	       		$needGem = 0;
	       	}
	       	Hapyfish2_Alchemy_Cache_Helltower::updateUserAbyssStatus($uid, $userwater);
	       	Hapyfish2_Alchemy_Cache_Helltower::updateUserAbyssRefiesh($uid, $userRefresh);
	        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'revivalGem', $needGem);
	        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'abyssDungeonOpen', 0);
		}
		$userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserAbyssStatus($uid);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'totalExp', $userwater['totalexp']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'totalMoney', $userwater['totalcoin']);
    	return $addExp;
    }
    
    public static function sweepWaterDungeon($uid)
    {
    	$awards = array();
    	$userVo = Hapyfish2_Alchemy_Bll_User::getUserInit($uid);
    	if($userVo['currentSceneId'] != self::WATER_SC_ID){
    		return -200;
    	}
    	$userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterStatus($uid);
    	if($userwater['current'] > $userwater['max']){
    		return -200;
    	}
    	if($userwater['current'] > 100){
    		return -200;
    	}
    	
    	$vip = new Hapyfish2_Alchemy_Bll_Vip();
    	$vipInfo = $vip->getVipInfo($uid);
    	if($vipInfo['level'] < 1 || $vipInfo['vipStatus'] !=1){
    		return -200;
    	}
    	$userwatersweep = Hapyfish2_Alchemy_Cache_Helltower::getWaterSweepStart($uid);
    	if(!$userwatersweep){
    		$userwatersweep = 0;
    	}
    	$now = time();
    	$costTime = $now - $userwatersweep;
    	if($costTime < self::SWEEP_TIME){
    		return -200;
    	}
    	$curInfo = Hapyfish2_Alchemy_Cache_Basic::getHelltowerInfo(1, $userwater['current']);
    	$awards = $curInfo['awards'];
    	$awardItem = Hapyfish2_Alchemy_Bll_VipWelfare::getFightVipAward($uid, $awards);
    	$changeResult = array();
        self::awardCondition($uid, $awardItem);
        foreach($awardItem as $k=>$v){
            if($v['type'] == 2){
            	if ($v['id'] == 'coin') {
					$userwater['totalcoin'] += $v['num'];
            	}
            	if ($v['id'] == 'battleExp') {
            		$userwater['totalexp'] += $v['num'];
            	}
            }
        }
        $posMatrix = Hapyfish2_Alchemy_Cache_FightCorps::getFightCorpsInfo($uid);
		foreach($posMatrix as $pos=>$id){
			if($v == 0){
		    	$userMerc = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		    }else{
		    	$mercInfo = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
		    }
			$levelUp = Hapyfish2_Alchemy_Bll_Mercenary::checkMercenaryLevelUp($uid, $id, $userMerc);
		}
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
        $userwater['current'] += 1;
        Hapyfish2_Alchemy_Cache_Helltower::updateUserWaterStatus($uid, $userwater);
    	Hapyfish2_Alchemy_Cache_Helltower::resetWaterSweepStart($uid,$now);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'sweepTo', $userwater['current']);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'totalExp', $userwater['totalexp']);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'totalMoney', $userwater['totalcoin']);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'awards', $awards);
    	return 1;
    }
    
	public static function sweepAbyssDungeon($uid)
    {
    	$awards = array();
    	$userVo = Hapyfish2_Alchemy_Bll_User::getUserInit($uid);
    	if($userVo['currentSceneId'] != self::ABYSS_SC_ID){
    		return -200;
    	}
    	$userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserAbyssStatus($uid);
    	if($userwater['current'] > $userwater['max']){
    		return -200;
    	}
    	if($userwater['current'] > 30){
    		return -200;
    	}
    	$vip = new Hapyfish2_Alchemy_Bll_Vip();
    	$vipInfo = $vip->getVipInfo($uid);
    	if($vipInfo['level'] < 1 || $vipInfo['vipStatus'] !=1){
    		return -200;
    	}
    	$userwatersweep = Hapyfish2_Alchemy_Cache_Helltower::getAbyssSweepStart($uid);
    	if(!$userwatersweep){
    		$userwatersweep = 0;
    	}
    	$now = time();
    	$costTime = $now - $userwatersweep;
    	if($costTime < self::SWEEP_TIME){
			return -200;
    	}
    	$curInfo = Hapyfish2_Alchemy_Cache_Basic::getHelltowerInfo(2, $userwater['current']);
    	$awards = $curInfo['awards'];
    	$awardItem = Hapyfish2_Alchemy_Bll_VipWelfare::getFightVipAward($uid, $awards);
    	$changeResult = array();
        self::awardCondition($uid, $awardItem);
        foreach($awardItem as $k=>$v){
        	if($v['type'] == 2){
                if ($v['id'] == 'coin') {
					$userwater['totalcoin'] += $v['num'];
            	}
            	if ($v['id'] == 'battleExp') {
            		$userwater['totalexp'] += $v['num'];
            	}
            }
         }
	     $posMatrix = Hapyfish2_Alchemy_Cache_FightCorps::getFightCorpsInfo($uid);
		 foreach($posMatrix as $pos=>$id){
			if($v == 0){
		    	$userMerc = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		    }else{
		    	$mercInfo = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
		    }
			$levelUp = Hapyfish2_Alchemy_Bll_Mercenary::checkMercenaryLevelUp($uid, $id, $userMerc);
		}
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
        $userwater['current'] += 1;
        Hapyfish2_Alchemy_Cache_Helltower::updateUserAbyssStatus($uid, $userwater);
    	Hapyfish2_Alchemy_Cache_Helltower::resetAbyssSweepStart($uid,$now);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'sweepTo', $userwater['current']);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'totalExp', $userwater['totalexp']);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'totalMoney', $userwater['totalcoin']);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'awards', $awards);
    	return 1;
    }
    
    public static function finishWaterSweep($uid)
    {
    	$userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterStatus($uid);
    	if($userwater['current'] > $userwater['max']){
    		return -200;
    	}
    	if($userwater['current'] > 100){
    		return -200;
    	}
    	$needGem = 10;
		$userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
		if ($userGem < $needGem) {
			return -206;
		}
		for($i = $userwater['current'];$i <= $userwater['max'];$i++){
			$curInfo = Hapyfish2_Alchemy_Cache_Basic::getHelltowerInfo(1, $i);
			$awards = $curInfo['awards'];
			$awardItem = Hapyfish2_Alchemy_Bll_VipWelfare::getFightVipAward($uid, $awards);
    		$changeResult = array();
            self::awardCondition($uid, $awardItem);
			foreach($awardItem as $k=>$v){
            	if($v['type'] == 2){
            		if ($v['id'] == 'coin') {
						$userwater['totalcoin'] += $v['num'];
            		}
            		if ($v['id'] == 'battleExp') {
            			$userwater['totalexp'] += $v['num'];
            		}
            	}
            }
		}
		
    	$posMatrix = Hapyfish2_Alchemy_Cache_FightCorps::getFightCorpsInfo($uid);
		foreach($posMatrix as $pos=>$id){
			if($v == 0){
	    		$userMerc = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
	    	}else{
	    		$userMerc = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
	    	}
			$levelUp = Hapyfish2_Alchemy_Bll_Mercenary::checkMercenaryLevelUp($uid, $id, $userMerc);
		}
		$userwater['current'] = $userwater['max'] + 1;
		Hapyfish2_Alchemy_Cache_Helltower::updateUserWaterStatus($uid, $userwater);
		$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
    	$gemInfo = array(
    			'uid' => $uid,
    			'cost' => $needGem,
    			'summary' => LANG_PLATFORM_BASE_TXT_10,
    			'user_level' => $userLevel,
    			'cid' => 32,
    			'num' => 1
    	);
    	Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo);
    	$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
    	return 1;
    }
    
	public static function finishAbyssSweep($uid)
    {
    	$userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserAbyssStatus($uid);
    	if($userwater['current'] > $userwater['max']){
    		return -200;
    	}
    	if($userwater['current'] > 30){
    		return -200;
    	}
    	$needGem = 10;
		$userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
		if ($userGem < $needGem) {
			return -206;
		}
		for($i = $userwater['current'];$i <= $userwater['max'];$i++){
			$curInfo = Hapyfish2_Alchemy_Cache_Basic::getHelltowerInfo(2, $i);
			$awards = $curInfo['awards'];
			$awardItem = Hapyfish2_Alchemy_Bll_VipWelfare::getFightVipAward($uid, $awards);
    		$changeResult = array();
            self::awardCondition($uid, $awardItem);
			foreach($awardItem as $k=>$v){
            	if($v['type'] == 2){
            		if ($v['id'] == 'coin') {
						$userwater['totalcoin'] += $v['num'];
            		}
            		if ($v['id'] == 'battleExp') {
            			$userwater['totalexp'] += $v['num'];
            		}
            	}
            }
		}
		$posMatrix = Hapyfish2_Alchemy_Cache_FightCorps::getFightCorpsInfo($uid);
		foreach($posMatrix as $pos=>$id){
			if($v == 0){
	    			$userMerc = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
	    		}else{
	    			$mercInfo = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
	    		}
			$levelUp = Hapyfish2_Alchemy_Bll_Mercenary::checkMercenaryLevelUp($uid, $id, $userMerc);
		}
		
		$userwater['current'] = $userwater['max'] + 1;
		Hapyfish2_Alchemy_Cache_Helltower::updateUserAbyssStatus($uid, $userwater);
		$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
    	$gemInfo = array(
    			'uid' => $uid,
    			'cost' => $needGem,
    			'summary' => LANG_PLATFORM_BASE_TXT_10,
    			'user_level' => $userLevel,
    			'cid' => 33,
    			'num' => 1
    	);
    	Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo);
    	$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
    	return 1;
    }
    
    public static function revivalWaterDungeon($uid)
    {
    	$userRefresh = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterRefiesh($uid);
    	if($userRefresh['refresh'] > 1){
       		$needGem = 10;
       	}else{
       		$needGem = 0;
       	}
       	if($userRefresh['refresh'] == 0){
       		return -200;
       	}
       	if($needGem){
	       	$userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
			if ($userGem < $needGem) {
				return -206;
			}
       	}
       	Hapyfish2_Alchemy_Cache_Helltower::updateUserWaterStatus($uid, $userRefresh['data']);
       	if($needGem){
       	$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
	    	$gemInfo = array(
	    			'uid' => $uid,
	    			'cost' => $needGem,
	    			'summary' => LANG_PLATFORM_BASE_TXT_10,
	    			'user_level' => $userLevel,
	    			'cid' => 32,
	    			'num' => 1
	    	);
	    	Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo);
       	}
    	$posMatrix = Hapyfish2_Alchemy_Cache_FightCorps::getFightCorpsInfo($uid);
    	foreach($posMatrix as $k => $v){
    		if($v == 0){
    			$selfInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
    			$selfInfo['hp'] = $selfInfo['hp_max'];
    			$selfInfo['mp'] = $selfInfo['mp_max'];
    			Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $selfInfo);
    		}else{
    			$mercInfo = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $v);
    			$mercInfo['hp'] = $mercInfo['hp_max'];
    			$mercInfo['mp'] = $mercInfo['mp_max'];
    			 Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $v, $mercInfo);
    		}
    	}
    	$userRefresh['status'] = 1;
    	Hapyfish2_Alchemy_Cache_Helltower::updateUserWaterRefiesh($uid, $userRefresh);
    	$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
    	return 1;
    }
    
    public static function revivalAbyssDungeon($uid)
    {
    	$userRefresh = Hapyfish2_Alchemy_Cache_Helltower::getUserAbyssRefiesh($uid);
    	if($userRefresh['refresh'] > 1){
       		$needGem = 10;
       	}else{
       		$needGem = 0;
       	}
       	if($userRefresh['refresh'] == 0){
       		return -200;
       	}
       	if($needGem){
	       	$userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
			if ($userGem < $needGem) {
				return -206;
			}
       	}
       	Hapyfish2_Alchemy_Cache_Helltower::updateUserAbyssStatus($uid, $userRefresh['data']);
       	if($needGem){
	       	$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
	    	$gemInfo = array(
	    			'uid' => $uid,
	    			'cost' => $needGem,
	    			'summary' => LANG_PLATFORM_BASE_TXT_10,
	    			'user_level' => $userLevel,
	    			'cid' => 32,
	    			'num' => 1
	    	);
	    	Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo);
       	}
    	$posMatrix = Hapyfish2_Alchemy_Cache_FightCorps::getFightCorpsInfo($uid);
    	foreach($posMatrix as $k => $v){
    		if($v == 0){
    			$selfInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
    			$selfInfo['hp'] = $selfInfo['hp_max'];
    			$selfInfo['mp'] = $selfInfo['mp_max'];
    			Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $selfInfo);
    		}else{
    			$mercInfo = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $v);
    			$mercInfo['hp'] = $mercInfo['hp_max'];
    			$mercInfo['mp'] = $mercInfo['mp_max'];
    			Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $v, $mercInfo);
    		}
    	}
    	$userRefresh['status'] = 1;
    	Hapyfish2_Alchemy_Cache_Helltower::updateUserAbyssRefiesh($uid, $userRefresh);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'abyssDungeonOpen', 1);
    	$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
    	return 1;
    	
    }
    
    public static function abyssOpenStatus($uid)
    {
    	$userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterStatus($uid);
    	if($userwater['max'] >= 100){
    		return 1;
    	}else{
    		return 0;
    	}
    }
    
    public static function openAbyss($uid)
    {
    	$userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterStatus($uid);
    	if($userwater['max'] < 100){
    		return -200;
    	}
    	$needCid = 4000115;
    	$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
        if ( !isset($userGoods[$needCid]) || $userGoods[$needCid]['count'] < 3 ) {
            return -309;
        }
        $userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserAbyssStatus($uid);
        $userwater['open'] = 1;
        Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $needCid, 3);
        Hapyfish2_Alchemy_Cache_Helltower::updateUserAbyssStatus($uid, $userwater);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'abyssDungeonOpen', 1);
        return 1;
    }
    
    public static function awardCondition($uid, $awardItem)
    {
    	$posMatrix = Hapyfish2_Alchemy_Cache_FightCorps::getFightCorpsInfo($uid);
    	foreach ($awardItem as $k => $v) {
		    //道具
			if ($v['type'] == 1) {
			    $cid = $v['id'];
			    $num = $v['num'];
    			$type =	substr($cid, -2, 1);

    			//1->物品,2->卷轴,3->材料,4->工作台,5->装修,6->装备
    			switch ($type) {
                    case 1:
                        Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, $cid, $num);
                        break;
                    case 2:
                        Hapyfish2_Alchemy_HFC_Scroll::addUserScroll($uid, $cid, $num);
                        break;
                    case 3:
                        Hapyfish2_Alchemy_HFC_Stuff::addUserStuff($uid, $cid, $num);
                        break;
                    case 4:
                    	for ( $n = 0; $n < $num; $n++ ) {
				        	$furnace = array('uid' => $uid,
				        					 'furnace_id' => $cid,
				        					 'status' => 0,
				        					 'idx' => 1);
				        	Hapyfish2_Alchemy_HFC_Furnace::addOne($uid, $furnace);
                    	}
                        break;
                    case 5:
                        Hapyfish2_Alchemy_HFC_Decor::addBag($uid, $cid, $num);
                        break;
                    case 6:
                    	Hapyfish2_Alchemy_HFC_Weapon::addUserWeapon($uid, $cid, $num);
                       
                        break;
                    default:
    			}
			}
			//玩家属性
			else if ($v['type'] == 2) {
				if ($v['id'] == 'coin') {
				    $ok = Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $v['num']);
				    if ($ok) {
					    $coinChange += $v['num'];
				    }
				}
				else if ($v['id'] == 'sp') {
                    $ok = Hapyfish2_Alchemy_HFC_User::incUserSp($uid, $v['num']);
                    if ($ok) {
                        $spChange += $v['num'];
                    }
				}
			    else if ($v['id'] == 'exp') {
                    $ok = Hapyfish2_Alchemy_HFC_User::incUserExp($uid, $v['num']);
                    if ($ok) {
                        $expChange += $v['num'];
                    }
				}
				else if ($v['id'] == 'activity') {
                    $ok = Hapyfish2_Alchemy_Bll_Activity::addUserActivity($uid, $v['num']);
				}
			    else if ($v['id'] == 'battleExp') {
				    foreach($posMatrix as $pos => $id){
			    		if($id == 0){
			    			$mercInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
			    			$mercInfo['exp'] += $v['num'];
			    			Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $mercInfo);
			    		}else{
			    			$mercInfo = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
			    			$mercInfo['exp'] += $v['num'];
			    			Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $id, $mercInfo);
			    		}
			    	}
				}
			}//奖励图鉴
			else if ($v['type'] == 3) {
				$illCid = $v['id'];
				//添加图鉴
				Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $illCid);
			}
		}
    }
    
    public static function canEnterWater($uid,$userwater)
    {
    	$userRefresh = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterRefiesh($uid);
    	$date = date('Ymd');
    	$enter = 1;
    	if($userwater['refreshTime'] == $date && $userRefresh['status'] == 0){
    		$enter = 0;
    	}
    	return $enter;
    }
    
    public static function canEnterAbyss($uid,$userwater)
    {
    	$userRefresh = Hapyfish2_Alchemy_Cache_Helltower::getUserAbyssRefiesh($uid);
    	$date = date('Ymd');
    	$enter = 1;
    	if($userwater['refreshTime'] == $date && $userRefresh['status'] == 0){
    		$enter = 0;
    	}
    	return $enter;
    }
}