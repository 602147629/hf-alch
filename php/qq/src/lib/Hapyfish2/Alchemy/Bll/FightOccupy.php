<?php

class Hapyfish2_Alchemy_Bll_FightOccupy
{

    const PROTECT_ITEM_ID = 3215;
    const PROTECT_INTERVAL = 21600;//7200;//主动保护时间
    const ATT_PROTECT_INTERVAL = 3600;//7200;//被动保护时间

    const COLLECT_TAX_INTERVAL = 7200;//7200;//收税时间
    const MAX_OCCUPY_INTERVAL = 86400;//86400;//最长占领时间

    const PROTECT_LEVEL = 10;//保护等级
    const PROTECT_LEVEL_DIFF = 5;//保护等级

    const ATTACK_NEED_SP = 1;//侵略需要sp
    const ATTACK_NEED_COIN = 100;//侵略需要coin
    const CORPS_COOLDOWN_INTERVAL = 21600;//7200;//佣兵使用冷却时间

    const WIN_AWARD_COIN = 200;//胜利奖励coin
    const FEATS_ITEM_CID = 3315;//功勋值道具cid
    const WIN_AWARD_FEATS = 3;//胜利奖励功勋值
    const TAXES_AWARD_FEATS = 1;//收税奖励功勋值
    const LOSE_AWARD_COIN = 10;//失利奖励coin
    const LOSE_AWARD_FEATS = 1;//失利奖励功勋值

    const STATUS_FREE = 0;
    const STATUS_BEING_ATTACKED = 1;
    const STATUS_BEING_OCCUPIED = 2;

    const AUTO_RELEASE_LOCK_INTERVAL = 600;//600;//自动释放beiing attack状态时间

    const MODE_AGGRESS = 1; //侵略
    const MODE_GAINST = 2;  //反抗
    const MODE_SUCCOR = 3;  //救援
    const MODE_ARENA = 7;  	//1v1竞技场
    const MODE_DIALOG = 8;  //对白触发战斗
    const MODE_GUIDE = 9;   //新手引导触发战斗

    /**
	 * 开启侵略保护
	 * @param int $uid
     * @return int
	 */
	public static function openProtect($uid)
	{
        $occupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($uid);
        if (!$occupyInfo) {
            return -600;
        }

        $nowTm = time();
        if ($occupyInfo['last_protect_open_tm'] && ($nowTm - $occupyInfo['last_protect_open_tm'])<self::PROTECT_INTERVAL) {
            return -601;//protect still in use
        }

        if ($occupyInfo['passive']) {
            if ($occupyInfo['passive']['status'] != self::STATUS_FREE) {//0-free 1-being attack 2-being occupied
                return -602;//being occupied,can not use protect
            }
        }

        //check item enough
        $rst = Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, self::PROTECT_ITEM_ID);
        if (!$rst) {
            return -239;
        }

        $occupyInfo['last_protect_open_tm'] = $nowTm;
        $ok = Hapyfish2_Alchemy_HFC_FightOccupy::save($uid, $occupyInfo, true);
        if ($ok) {
            Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'safeTime', $nowTm + self::PROTECT_INTERVAL);
            return 1;
        }
        return -200;
	}

	/**
	 * 收取占领金
	 * @param int $uid
	 * @param int $fid
     * @return int
	 */
	public static function collectTax($uid, $fid)
	{
	    if ($uid == $fid || empty($fid)) {
            return -200;
	    }
		
	    $botUid = explode(',',BOTFRIEND);
		if(in_array($fid, $botUid)){
	    	$occupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getBotInfo($uid);
			if (!$occupyInfo) {
	            return -600;
	        }
			$tarOccupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($uid);
	        if (!$tarOccupyInfo) {
	            return -600;
	        }
	        if(!isset($occupyInfo[$fid])){
	        	 return -603;//not occupied or not occupied by uid
	        }
			 if ($occupyInfo[$fid]['status'] != self::STATUS_BEING_OCCUPIED) {//0-free 1-being attack 2-being occupied
	            return -604;//not occupied or not occupied by uid
	        }
	
	        if (!array_key_exists($fid, $tarOccupyInfo['initiative'])) {
	            return -605;//not occupied or not occupied by uid
	        }
	
	        $nowTm = time();
	        if ($nowTm - $occupyInfo[$fid]['tm'] > self::MAX_OCCUPY_INTERVAL) {
	            return -606;//max occupy time arrived
	        }
	
	        if (isset($occupyInfo[$fid]['taxTm'])) {
	            foreach ($occupyInfo[$fid]['taxTm'] as $tm) {
	                if ($nowTm - $tm < self::COLLECT_TAX_INTERVAL) {
	                    return -607;//tax time not arrive
	                }
	            }
	        }
	        $house = $occupyInfo[$fid]['house'];
	        $userHomeLevel = Hapyfish2_Alchemy_HFC_User::getUserHomeLevel($fid);
	        $coin = floor(100*pow($userHomeLevel, 1.2));
//	        $exp = 0;
//	        $feats = self::TAXES_AWARD_FEATS;
			$coin = Hapyfish2_Alchemy_Bll_VipWelfare::getVipTaxescoin($uid, $coin);
	         Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $coin);
	        //Hapyfish2_Alchemy_HFC_User::incUserFeats($uid, $feats);
//	        Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, self::FEATS_ITEM_CID, $feats);
	
	        $tarOccupyInfo['initiative'][$fid]['taxTm'][] = $nowTm;
	        $ok = Hapyfish2_Alchemy_HFC_FightOccupy::save($uid, $tarOccupyInfo);
	
	        $occupyInfo[$fid]['taxTm'][] = $nowTm;
	        Hapyfish2_Alchemy_HFC_FightOccupy::saveBot($uid, $occupyInfo);
	        $ok = 1;
		}else{
			$occupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($uid);
	        if (!$occupyInfo) {
	            return -600;
	        }
	
		    $tarOccupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($fid);
	        if (!$tarOccupyInfo) {
	            return -600;
	        }
		    if (!$tarOccupyInfo['passive'] || !$occupyInfo['initiative']) {
		        return -603;//not occupied or not occupied by uid
	        }
	
		    if ($tarOccupyInfo['passive']['status'] != self::STATUS_BEING_OCCUPIED || $tarOccupyInfo['passive']['uid'] != $uid) {//0-free 1-being attack 2-being occupied
	            return -604;//not occupied or not occupied by uid
	        }
	
	        if (!array_key_exists($fid, $occupyInfo['initiative'])) {
	            return -605;//not occupied or not occupied by uid
	        }
	
	        $nowTm = time();
	        if ($nowTm - $occupyInfo['initiative'][$fid]['tm'] > self::MAX_OCCUPY_INTERVAL) {
	            return -606;//max occupy time arrived
	        }
	
	        if (isset($occupyInfo['initiative'][$fid]['taxTm'])) {
	            foreach ($occupyInfo['initiative'][$fid]['taxTm'] as $tm) {
	                if ($nowTm - $tm < self::COLLECT_TAX_INTERVAL) {
	                    return -607;//tax time not arrive
	                }
	            }
	        }
	        $house = $tarOccupyInfo['passive']['house'];
	        $userHomeLevel = Hapyfish2_Alchemy_HFC_User::getUserHomeLevel($fid);
	        $coin = floor(100*pow($userHomeLevel, 1.2));
			$coin = Hapyfish2_Alchemy_Bll_VipWelfare::getVipTaxescoin($uid, $coin);
	        Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $coin);
	
	        $occupyInfo['initiative'][$fid]['taxTm'][] = $nowTm;
	        $ok = Hapyfish2_Alchemy_HFC_FightOccupy::save($uid, $occupyInfo);
	
	        $tarOccupyInfo['passive']['taxTm'][] = $nowTm;
	        $ok = Hapyfish2_Alchemy_HFC_FightOccupy::save($fid, $tarOccupyInfo);
		}
	    
        if ($ok) {
        	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'nextAwardTime', $nowTm+self::COLLECT_TAX_INTERVAL);
            return 1;
        }
        return -200;
	}

	/**
	 * 侵略抽人初始化
	 * @param int $uid
	 * @param int $fid
     * @return int
	 */
	public static function aggress($uid, $fid)
	{
	    $homeSide = array();
        $enemySide = array();
        $ret = self::_aggressPre($uid, $fid, $homeSide, $enemySide, $enemyId);
        if ($ret != 1) {
            return $ret;
        }

        //Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, self::ATTACK_NEED_COIN);
	    //Hapyfish2_Alchemy_HFC_User::decUserSp($uid, self::ATTACK_NEED_SP);

	    $homeSideVo = self::_genLottoPreRolesVo($uid, $homeSide);
        $enemySideVo = self::_genLottoPreRolesVo($fid, $enemySide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'myRoles', $homeSideVo);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'enemyRoles', $enemySideVo);
		return 1;
	}

	private static function _aggressPre($uid, $fid, &$homeSideRet, &$enemySideRet, &$enemyId)
	{
	    if ($uid == $fid || empty($fid)) {
            return -200;
	    }

	    if (!Hapyfish2_Platform_Bll_Friend::isFriend($uid, $fid)) {
            return -105;//not friend
	    }
	    $botUid = explode(',',BOTFRIEND);
		if(in_array($fid, $botUid)){
			$occupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getBotInfo($uid);
	        if (!$occupyInfo) {
//	            return -600;
	        }
	
		    $tarOccupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($uid);
	        if (!$tarOccupyInfo) {
	            return -600;
	        }
			$nowTm = time();
			if ($occupyInfo[$fid]['status'] == 0 && ($nowTm - $occupyInfo[$fid]['tm'] < self::ATT_PROTECT_INTERVAL)) {
	            return -609;//being proteced by this uid
	        }
	        $usedMerc = $tarOccupyInfo['corps_used'];
		}else{
			$occupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($uid);
	        if (!$occupyInfo) {
	            return -600;
	        }
	
		    $tarOccupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($fid);
	        if (!$tarOccupyInfo) {
	            return -600;
	        }
	
	        $nowTm = time();
	        if ($nowTm - $tarOccupyInfo['last_protect_open_tm'] < self::PROTECT_INTERVAL) {
	            return -608;//protect is in use
	        }
	
		    if ($tarOccupyInfo['passive'] && $tarOccupyInfo['passive']['status'] != self::STATUS_FREE) {//0-free 1-being attack 2-being occupied
		        if ($tarOccupyInfo['passive']['beingUid'] != $uid) {
		            return -615;//being occupied,can not attack
		        }
	        }
	
	        if ($tarOccupyInfo['passive']['status'] == 0 && ($nowTm - $tarOccupyInfo['passive']['tm'] < self::ATT_PROTECT_INTERVAL)) {
	            return -609;//being proteced by this uid
	        }
	        $usedMerc = $occupyInfo['corps_used'];
		}
		$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($fid);
		$tarLev = $userMercenary['level'];
        if ($tarLev < self::PROTECT_LEVEL) {
            return -610;//level too low
        }
        $fmercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($fid);
        $myLev = $fmercenary['level'];
        if ($myLev > $tarLev && $myLev - $tarLev > self::PROTECT_LEVEL_DIFF) {
            return -611;//level diff too much
        }

//		if (Hapyfish2_Alchemy_HFC_User::getUserCoin($uid) < self::ATTACK_NEED_COIN) {
//		    return -207;//coin not enough
//		}
//		$usp = Hapyfish2_Alchemy_HFC_User::getUserSp($uid);
//	    if ($usp['sp'] < self::ATTACK_NEED_SP) {
//		    return -208;//sp not enough
//		}

        //begin to select fighter
	    //homeside
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		foreach ($homeSide as $data) {
		    $data['coolTm'] = 0;
            if ($usedMerc && isset($usedMerc[$data['id']])) {
                if ($nowTm - (int)$usedMerc[$data['id']] < self::CORPS_COOLDOWN_INTERVAL) {
                    $data['coolTm'] = (int)$usedMerc[$data['id']];
                }
            }
            if ($data['coolTm'] == 0) {
                unset($data['coolTm']);
                $homeSideRet[] = $data;
            }
		}
		if (!$homeSideRet) {
            return -612;//corps not enough
		}

		//enemyside
		$enemySideRet = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($fid);
		$enemyId = $fid;
		return 1;
	}

	/**
	 * 反抗抽人初始化
	 * @param int $uid
     * @return int
	 */
	public static function gainst($uid)
	{
        $homeSide = array();
        $enemySide = array();
        $ret = self::_gainstPre($uid, $homeSide, $enemySide, $enemyId);
        if ($ret != 1) {
            return $ret;
        }

	    $homeSideVo = self::_genLottoPreRolesVo($uid, $homeSide);
        $enemySideVo = self::_genLottoPreRolesVo($fid, $enemySide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'myRoles', $homeSideVo);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'enemyRoles', $enemySideVo);
		return 1;
	}

	private static function _gainstPre($uid, &$homeSideRet, &$enemySideRet, &$enemyId)
	{
	    $occupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($uid);
        if (!$occupyInfo) {
            return -600;
        }

        if (!$occupyInfo['passive'] || ($occupyInfo['passive'] && $occupyInfo['passive']['status'] != self::STATUS_BEING_OCCUPIED)) {//0-free 1-being attack 2-being occupied
            return -613;//needn't gainst
        }

        $fid = $occupyInfo['passive']['uid'];
        $occuMerc = $occupyInfo['passive']['merc'];
	    $tarOccupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($fid);
        if (!$tarOccupyInfo) {
            return -600;
        }

        //begin to select fighter
        $usedMerc = $occupyInfo['corps_used'];

        $nowTm = time();
        //homeside
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		foreach ($homeSide as $data) {
		    $data['coolTm'] = 0;
		    $id = $data['id'];
            if ($usedMerc && isset($usedMerc[$id])) {
                if ($nowTm - (int)$usedMerc[$id] < self::CORPS_COOLDOWN_INTERVAL) {
                    $data['coolTm'] = (int)$usedMerc[$id];
                }
            }
            if ($data['coolTm'] == 0) {
                unset($data['coolTm']);
                $homeSideRet[] = $data;
            }
		}

		if (!$homeSideRet) {
            return -612;//corps not enough
		}

		//enemyside
		$mecenaryList = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($fid);
		foreach ($mecenaryList as $data) {
		    $id = $data['id'];
		    if (in_array($id, $occuMerc)) {
		        $enemySideRet[] = $data;
		    }
		}
		$enemyId = $fid;

		return 1;
	}

	/**
	 * 援助抽人初始化
	 * @param int $uid
	 * @param int $fid
     * @return int
	 */
	public static function succor($uid, $fid)
	{
        $homeSide = array();
        $enemySide = array();
        $ret = self::_succorPre($uid, $fid, $homeSide, $enemySide, $enemyId);
        if ($ret != 1) {
            return $ret;
        }

	    $homeSideVo = self::_genLottoPreRolesVo($uid, $homeSide);
        $enemySideVo = self::_genLottoPreRolesVo($fid, $enemySide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'myRoles', $homeSideVo);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'enemyRoles', $enemySideVo);
		return 1;
	}

	private static function _succorPre($uid, $fid, &$homeSideRet, &$enemySideRet, &$enemyId)
	{
        if ($uid == $fid || empty($fid)) {
            return -200;
	    }

	    if (!Hapyfish2_Platform_Bll_Friend::isFriend($uid, $fid)) {
            return -105;//not friend
	    }

	    $occupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($uid);
        if (!$occupyInfo) {
            return -600;
        }

	    $tarOccupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($fid);
        if (!$tarOccupyInfo) {
            return -600;
        }

        $nowTm = time();
	    if (!$tarOccupyInfo['passive'] || ($tarOccupyInfo['passive'] && $tarOccupyInfo['passive']['status'] != self::STATUS_BEING_OCCUPIED)) {//0-free 1-being attack 2-being occupied
            return -614;//needn't succor
        }

        $tarFid = $tarOccupyInfo['passive']['uid'];//for succor who
        $occuMerc = $tarOccupyInfo['passive']['merc'];

        //begin to select fighter
        $usedMerc = $occupyInfo['corps_used'];

	    //homeside
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		foreach ($homeSide as $data) {
		    $data['coolTm'] = 0;
		    $id = $data['id'];
            if ($usedMerc && isset($usedMerc[$id])) {
                if ($nowTm - (int)$usedMerc[$id] < self::CORPS_COOLDOWN_INTERVAL) {
                    $data['coolTm'] = (int)$usedMerc[$id];
                }
            }
            if ($data['coolTm'] == 0) {
                unset($data['coolTm']);
                $homeSideRet[] = $data;
            }
		}

		if (!$homeSideRet) {
            return -612;//corps not enough
		}

	    //enemyside
		$mecenaryList = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($tarFid);
		foreach ($mecenaryList as $data) {
		    $id = $data['id'];
		    if (in_array($id, $occuMerc)) {
		        $enemySideRet[] = $data;
		    }
		}
		$enemyId = $fid;

		return 1;
	}

	/**
	 * 拉霸抽人，开始战斗
	 * @param int $uid
	 * @param int $fid
	 * @param int $houseId
	 * @param int $mode 1-侵略aggress 2-反抗gainst 3-援助succor
	 * @param int $num 主动方参战人数
     * @return int
	 */
	public static function startLotto($uid, $fid=null, $houseId = 0, $mode, $num=3)
	{
        $num = empty($num) ? 3 : (int)$num;

	    $homeSide = array();
        $enemySide = array();
        $enemyId = 0;
	    if (self::MODE_AGGRESS == $mode) {
            $ret = self::_aggressPre($uid, $fid, $homeSide, $enemySide, $enemyId);
	    }
	    else if (self::MODE_GAINST == $mode) {
            $ret = self::_gainstPre($uid, $homeSide, $enemySide, $enemyId);
            $fid = $enemyId;
	    }
	    else if (self::MODE_SUCCOR == $mode) {
            $ret = self::_succorPre($uid, $fid, $homeSide, $enemySide, $enemyId);
	    }
	    else {
	        return -200;
	    }

	    if ($ret != 1) {
            return $ret;
        }

        $nowTm = time();
//        if (self::MODE_AGGRESS == $mode) {
//            Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, self::ATTACK_NEED_COIN);
//	        Hapyfish2_Alchemy_HFC_User::decUserSp($uid, self::ATTACK_NEED_SP);
//        }

        if (!$enemySide) {
            //没有出战佣兵  直接占领了 战斗结束
            $homeCnt = count($homeSide);
            $randKeys1 = array_rand($homeSide, ($num>$homeCnt?$homeCnt:$num));
            if (!is_array($randKeys1)) {
                $tmp = $randKeys1;
                $randKeys1 = array($tmp);
            }
            $selIds = array();
            $selMerc = array();
            foreach ($randKeys1 as $idx) {
                $tmpData = $homeSide[$idx];
                $selIds[] = (int)$tmpData['id'];
                $selMerc[] = array('id' => (int)$tmpData['id']);
            }
            //update attacker occupy info
            $occupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($uid);
            foreach ($selIds as $mid) {
                $occupyInfo['corps_used'][$mid] = $nowTm;
            }
            Hapyfish2_Alchemy_HFC_FightOccupy::save($uid, $occupyInfo);

            self::_winOccupy($uid, $fid, $houseId, $mode, $selMerc);
            return 1;
        }

        //随机选择出战佣兵
        $homeCnt = count($homeSide);
        $enemyCnt = count($enemySide);
        $randKeys1 = array_rand($homeSide, ($num>$homeCnt?$homeCnt:$num));
        $randKeys2 = array_rand($enemySide, (3>$enemyCnt?$enemyCnt:3));

	    if (!is_array($randKeys1)) {
            $tmp = $randKeys1;
            $randKeys1 = array($tmp);
        }

	    if (!is_array($randKeys2)) {
            $tmp = $randKeys2;
            $randKeys2 = array($tmp);
        }

        $selHomeSide = array();
        $mypos = 9;
        $selIds = array();
        $totLev1 = 0;
        foreach ($randKeys1 as $idx) {
            $tmpData = $homeSide[$idx];
            $tmpData['matrix_pos'] = $mypos;
            $tmpData['hp'] = $tmpData['hp_max'];
            $tmpData['mp'] = $tmpData['mp_max'];
            $selHomeSide[$mypos] = $tmpData;
            $selIds[] = (int)$tmpData['id'];
            $totLev1 += $tmpData['level'];
            $mypos ++;
        }

        $selEnemySide = array();
        $selIds2 = array();
        $totLev2 = 0;
        foreach ($randKeys2 as $idx) {
            $selEnemySide[] = $enemySide[$idx];
            $selIds2[] = (int)$enemySide[$idx]['id'];
            $totLev2 += $enemySide[$idx]['level'];
        }

        $aryPos = Hapyfish2_Alchemy_Bll_MapCopy::_arrangeEnemyFightPosAi($selEnemySide);
	    if (count($aryPos) != count($selEnemySide)) {
            info_log('_arrangeEnemyFightPosAi failed', 'Bll-FightOccupy');
	        return -200;
        }
        $selEnemySidePos = array();
        foreach ($aryPos as $pos=>$id) {
            $tmpData = $selEnemySide[$id];
            $tmpData['matrix_pos'] = $pos;
            $tmpData['hp'] = $tmpData['hp_max'];
            $tmpData['mp'] = $tmpData['mp_max'];
            $selEnemySidePos[$pos] = $tmpData;
        }

        $info = array();
		$info['uid'] = $uid;
		$info['fid'] = Hapyfish2_Alchemy_Bll_Fight::getNewId($uid);
		$info['type'] = $mode;
		$info['enemy_id'] = $enemyId. '-' . $houseId;
		$info['status'] = 0;
		$aryRnd = array();
		for ($i=0; $i<20; $i++) {
            $aryRnd[] = mt_rand(1,1000);
		}
		$info['rnd_element'] = $aryRnd;
		$info['home_side'] = $selHomeSide;
		$info['enemy_side'] = $selEnemySidePos;
		$info['content'] = array();
		$info['create_time'] = $nowTm;

		$saveDb = (defined('ENABLE_FIGHT_DB_LOG') && ENABLE_FIGHT_DB_LOG);
        Hapyfish2_Alchemy_Cache_Fight::saveFightInfo($uid, $info, $saveDb);

        //update target occupy info
	    if (self::MODE_AGGRESS == $mode) {
	    	$botUid = explode(',',BOTFRIEND);
			if(in_array($fid, $botUid)){
				$tarOccupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getBotInfo($uid);
	            $tarOccupyInfo[$fid]['status'] = self::STATUS_BEING_ATTACKED;
	            $tarOccupyInfo[$fid]['beingTm'] = time();
	            Hapyfish2_Alchemy_HFC_FightOccupy::saveBot($uid, $tarOccupyInfo);
			}else{
				$tarOccupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($fid);
	            $tarOccupyInfo['passive']['status'] = self::STATUS_BEING_ATTACKED;
	            $tarOccupyInfo['passive']['beingUid'] = $uid;
	            $tarOccupyInfo['passive']['beingTm'] = time();
	            Hapyfish2_Alchemy_HFC_FightOccupy::save($fid, $tarOccupyInfo);
			}
        }

        //update attacker occupy info
        $occupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($uid);
        foreach ($selIds as $mid) {
            $occupyInfo['corps_used'][$mid] = $nowTm;
        }
        Hapyfish2_Alchemy_HFC_FightOccupy::save($uid, $occupyInfo);

        //拼接Vo数据返回前端
        //我方
        $roleList1 = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $info['home_side']);
        //敌方
        $roleList2 = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($fid, $info['enemy_side']);
//		$winAward = array(
//			array('type'=>2,
//				  'id'  =>'coin',
//				  'num' =>self::WIN_AWARD_COIN		
//			),
//			array('type'=>1,
//				  'id'  =>self::FEATS_ITEM_CID,
//				  'num' =>self::WIN_AWARD_FEATS	
//				
//			)
//		
//		);
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		$skip = $vip->getVipSkip($uid);
		$jumpTime = $skip['max'] - $skip['num'] >0 ? $skip['max'] - $skip['num']:0;
		$invite = Hapyfish2_Alchemy_HFC_User::getTotalInvite($uid);
		$isInvite = 0;
		if($invite > 0){
			$isInvite = 1;
		}
        $battle = array(
            'id' => $info['fid'],
            'bgClassName' => 'fbg.1.cunzhuang',
            'roleList' => array_merge($roleList1, $roleList2),
        	'talk' => array(),
//            'friendSkill' => array(),
            'assCnt' => 0,
            'extCnt' => 0,
        	'jumpTimes'=>$jumpTime,
        	'isInvite'=>$isInvite
        	
        );

        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'myRolesId', $selIds);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'enemyRolesId', $selIds2);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'BattleVo', $battle);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'RndNums', $aryRnd);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'levdiff', $totLev1.'|'.$totLev2);
        
        //重置左侧时间导航栏-侵略冷却时间
        Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'occCd');
        
        return 1;
	}

	/**
	 * 战斗结果
	 * @param int $uid
	 * @param array $fightInfo
	 * @param array $fightRst
     * @return int
	 */
	public static function completeOccupy($uid, $fightInfo, $fightRst)
	{

	    list($fid, $houseId) = explode('-', $fightInfo['enemy_id']);
	    //胜利
        if ($fightRst['result'] == Hapyfish2_Alchemy_Bll_Fight_Simulator::RESULT_WIN) {
	        self::_winOccupy($uid, $fid, $houseId, $fightInfo['type'], $fightRst['data']['corps']);
        }

        //失利
        else {
            $tarOccupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($fid);
            if (self::MODE_AGGRESS == $fightInfo['type']) {
	             $botUid = explode(',',BOTFRIEND);
				if(in_array($fid, $botUid)){
					$tarOccupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getBotInfo($uid);
					unset($tarOccupyInfo[$fid]['beingTm']);
					Hapyfish2_Alchemy_HFC_FightOccupy::saveBot($fid, $tarOccupyInfo);
				}else{
					$tarOccupyInfo['passive']['status'] = self::STATUS_FREE;
	                unset($tarOccupyInfo['passive']['beingUid']);
	                unset($tarOccupyInfo['passive']['beingTm']);
	                Hapyfish2_Alchemy_HFC_FightOccupy::save($fid, $tarOccupyInfo);
				}
            }

//            Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, self::LOSE_AWARD_COIN);
//            Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, self::FEATS_ITEM_CID, self::LOSE_AWARD_FEATS);
	        //Hapyfish2_Alchemy_HFC_User::incUserFeats($uid, self::LOSE_AWARD_FEATS);
        }

        //统计分析log
        $log = Hapyfish2_Util_Log::getInstance();
        $userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
        $userHomeLevel = Hapyfish2_Alchemy_HFC_User::getUserHomeLevel($fid);
	    $coin = floor(100*pow($userHomeLevel, 1.2));
        //侵略
        if (self::MODE_AGGRESS == $fightInfo['type']) {
            $log->report('226', array($uid, $userLevel, $fightRst['result']));
            
            //侵略成功
            if ($fightRst['result'] == Hapyfish2_Alchemy_Bll_Fight_Simulator::RESULT_WIN) {
            	$nowTime = time();
				//insert minifeed
				$minifeed1 = array('uid' => $uid,
		                          'template_id' => 7,
		                          'actor' => $uid,
		                          'target' => $fid,
		                          'title' => array('num' => 3, 'coin' => $coin),
		                          'type' => 1,
		                          'create_time' => $nowTime);
				Hapyfish2_Alchemy_Bll_Feed::insertMiniFeed($minifeed1);
				
				//insert minifeed
				$minifeed2 = array('uid' => $fid,
		                          'template_id' => 8,
		                          'actor' => $uid,
		                          'target' => $fid,
		                          'title' => array(),
		                          'type' => 1,
		                          'create_time' => $nowTime);
				Hapyfish2_Alchemy_Bll_Feed::insertMiniFeed($minifeed2);
            }
        }
        //反抗
        else if (self::MODE_GAINST == $fightInfo['type']) {
            $log->report('227', array($uid, $userLevel, $fightRst['result']));
        }
        //救援
        else {
            $log->report('225', array($uid, $userLevel, $fightRst['result']));
            
            //救援成功
            if ($fightRst['result'] == Hapyfish2_Alchemy_Bll_Fight_Simulator::RESULT_WIN) {
            	$nowTime = time();
				//insert minifeed
				$minifeed1 = array('uid' => $uid,
		                          'template_id' => 9,
		                          'actor' => $uid,
		                          'target' => $fid,
		                          'title' => array('exp' => 100, 'coin' => $coin),
		                          'type' => 1,
		                          'create_time' => $nowTime);
				Hapyfish2_Alchemy_Bll_Feed::insertMiniFeed($minifeed1);
				
				//insert minifeed
				$minifeed2 = array('uid' => $fid,
		                          'template_id' => 10,
		                          'actor' => $uid,
		                          'target' => $fid,
		                          'title' => array(),
		                          'type' => 1,
		                          'create_time' => $nowTime);
				Hapyfish2_Alchemy_Bll_Feed::insertMiniFeed($minifeed2);
            }
        }
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
	    return 1;
	}

	/**
	 * 占领战胜利
	 * @param int $uid
	 * @param int $fid
	 * @param int $houseId
	 * @param int $mode
     * @return int
	 */
	public static function _winOccupy($uid, $fid, $houseId, $mode, $occMerc)
	{
	    $occupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($uid);
        $tarOccupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($fid);
        $nowTm = time();

        if (self::MODE_AGGRESS == $mode) {
            $usedMerc = array();
            foreach ($occMerc as $data) {
                $usedMerc[] = (int)$data['id'];
            }
            $botUid = explode(',',BOTFRIEND);
			if(in_array($fid, $botUid)){
				 $tarOccupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getBotInfo($uid);
				 $tarOccupyInfo[$fid] = array(
	                'status' => self::STATUS_BEING_OCCUPIED,
	                'uid' => $uid,
	                'tm' => $nowTm,
	                'merc' => $usedMerc,
	                'taxTm' => array($nowTm-7200),
	                'house' => (int)$houseId
	            );
            	Hapyfish2_Alchemy_HFC_FightOccupy::saveBot($uid, $tarOccupyInfo);
			}else{
				$tarOccupyInfo['passive'] = array(
                'status' => self::STATUS_BEING_OCCUPIED,
                'uid' => $uid,
                'tm' => $nowTm,
                'merc' => $usedMerc,
                'taxTm' => array($nowTm-7200),
                'house' => (int)$houseId
	            );
	            Hapyfish2_Alchemy_HFC_FightOccupy::save($fid, $tarOccupyInfo);
			}
            $occupyInfo['initiative'][$fid] = array(
                'tm' => $nowTm,
                'taxTm' => array($nowTm-7200),
            	'house' => (int)$houseId
            );
            Hapyfish2_Alchemy_HFC_FightOccupy::save($uid, $occupyInfo);
        }

        else if (self::MODE_GAINST == $mode) {
            $occupyInfo['passive']['status'] = self::STATUS_FREE;
            unset($occupyInfo['passive']['beingUid']);
            unset($occupyInfo['passive']['beingTm']);
            Hapyfish2_Alchemy_HFC_FightOccupy::save($uid, $occupyInfo);

            unset($tarOccupyInfo['initiative'][$uid]);
            Hapyfish2_Alchemy_HFC_FightOccupy::save($fid, $tarOccupyInfo);
        }

        else if (self::MODE_SUCCOR == $mode) {
            $tarOccupyInfo['passive']['status'] = self::STATUS_FREE;
            Hapyfish2_Alchemy_HFC_FightOccupy::save($fid, $tarOccupyInfo);

            $ownUid = $tarOccupyInfo['passive']['uid'];
            if ($ownUid) {
                $ownOccupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($ownUid);
                unset($ownOccupyInfo['initiative'][$fid]);
                Hapyfish2_Alchemy_HFC_FightOccupy::save($ownUid, $ownOccupyInfo);
            }
        }

        //胜利奖励
        $userHomeLevel = Hapyfish2_Alchemy_HFC_User::getUserHomeLevel($fid);
	    $coin = floor(100*pow($userHomeLevel, 1.2));
        Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $coin);
        //Hapyfish2_Alchemy_HFC_User::incUserFeats($uid, self::WIN_AWARD_FEATS);
        Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, self::FEATS_ITEM_CID, self::WIN_AWARD_FEATS);
        $winAward = array(
					array('type'=>2,
						  'id'  =>'coin',
						  'num' =>$coin		
					),
					array('type'=>1,
						  'id'  =>self::FEATS_ITEM_CID,
						  'num' =>self::WIN_AWARD_FEATS	
					)
				);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'extraItems', $winAward);

        $uInfo = Hapyfish2_Platform_Bll_User::getUser($uid);
        $occChange = array(
            'type' => $mode,
            'uid' => $fid,
            'ownerUid' => $uid,
            'ownerFace' => $uInfo['figureurl'],
            'ownerBuildId' => $houseId,
            'ownerEndTime' => $nowTm + self::MAX_OCCUPY_INTERVAL,
            'ownerAwardTime' => $nowTm
        );
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'occChange', $occChange);

        return;
	}

    private static function _genLottoPreRolesVo($uid, $aryRole)
	{
	    $nowTm = time();
	    $retVo = array();
	    if ($aryRole) {
    	    foreach ($aryRole as $data) {
    	        $id = (int)$data['id'];
    		    $role = array(
                    	'id' => $id,
                    	'name' => $data['name'],
                    	'sex' => (int)$data['sex'],
                    	'label' => '',
                    	'className' => $data['class_name'],
                    	'faceClass' => $data['face_class_name'],
                    	'sFaceClass' => $data['s_face_class_name'],
                    	'profession' => (int)$data['job'],
                    	'prop' => (int)$data['element'],
                        'level' => (int)$data['level']
                );

                /*if (Hapyfish2_Alchemy_Bll_Fight_Simulator::ROLE_SELF == $data['id']) {
                    $role['label'] = 'MR';
                    $uInfo = Hapyfish2_Platform_Bll_User::getUser($uid);
                    $role['name'] = $uInfo['name'];
                }*/
                $retVo[] = $role;
    		}
	    }
	    return $retVo;
	}
}