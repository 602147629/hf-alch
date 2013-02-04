<?php

class Hapyfish2_Alchemy_Bll_Mercenary
{
	public static function setHireOpened($uid, $id)
	{
		//所有雇佣位信息
		$hireList = Hapyfish2_Alchemy_HFC_Hire::getAll($uid);
		$hire = $hireList[$id];
		
		if ( !$hire['cid'] ) {
			
			//获取一个新的雇佣佣兵信息
			$newHire = self::getNewHire($uid, $id, $hire, 0);
			$newHire['start_time'] = 0;
			
			Hapyfish2_Alchemy_HFC_Hire::updateOne($uid, $id, $newHire);
			$hire = self::getHireVo($newHire);
		}
		return $hire;
	}
	
	/*
	 * 读取雇佣位信息
	 */
	public static function getHire($uid)
	{
		//所有雇佣位信息
		$hireList = Hapyfish2_Alchemy_HFC_Hire::getAll($uid);

		$list = array();
		foreach ( $hireList as $hire ) {
			if ( isset($hire['cid']) && $hire['cid'] > 0 ) {
				$hireVo = self::getHireVo($hire);
			}
			else {
				$hireVo = array();
			}
			$list[] = $hireVo;
		}

		$nowTm = time();
		//用户酒馆佣兵信息
		$userHireData = Hapyfish2_Alchemy_HFC_Hire::getHireData($uid);
		$userHireInfo = $userHireData['data'];
		$refreshList = array();
		
		foreach ( $userHireInfo as $k => $hire ) {

			$hire['remainingTime'] = $hire['endTime'] - $nowTm;
			$hire['remainingTime'] = $hire['remainingTime'] < 0 ? 0 : $hire['remainingTime'];
			
			if ( $k != 0 ) {
				$hire['times'] = -1;
			}
			
			//是否有加速刷新奖励
			$hire['first'] = 0;
			if ( $k == 2 && $userHireData['first_refresh_2'] == 0 ) {
				$hire['first'] = 1;
			}
			else if ( $k == 3 && $userHireData['first_refresh_3'] == 0 ) {
				$hire['first'] = 1;
			}
			unset($hire['endTime']);
			$refreshList[] = $hire;

		}
		
		return array('result' => array('status' => 1),
					 'list' => $list,
					 'refreshList' => $refreshList);
	}

	/**
	 * 刷新酒馆
	 * @param int $uid
	 * @param int $type，0-3对应4种刷新级别
	 * @param int $fast，不传为普通刷新;1 快速刷新.
	 */
	public static function refreshHire($uid, $type, $fast = 0)
	{
		if ( !in_array($type, array(0,1,2,3))) {
			return -200;
		}
		
		$needGem = 0;
		$needCoin = 0;
		$hireType = 0;
		//是否加速刷新
		if ( $fast == 1 ) {
			switch ( $type ) {
				case 0 :
					$needGem = 10;
					$hireType = 4;
					break;
				case 1 :
					$needGem = 50;
					$hireType = 5;
					break;
				case 2 :
					$needGem = 200;
					$hireType = 6;
					break;
				case 3 :
					$needGem = 500;
					$hireType = 7;
					break;
			}
		}
		else {
			switch ( $type ) {
				case 0 :
					$needCoin = 200;
					$hireType = 0;
					break;
				case 1 :
					$needCoin = 200;
					$hireType = 1;
					break;
				case 2 :
					$needCoin = 100;
					$hireType = 2;
					break;
				case 3 :
					$needCoin = 50;
					$hireType = 3;
					break;
			}
		}
		
		//判断刷新需要金币、宝石
		if ( $needCoin > 0 ) {
			$userCoin = Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
			if ($userCoin < $needCoin) {
				return -207;
			}
		}
	    if ( $needGem > 0 ) {
			$userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
			if ($userGem < $needGem) {
				return -206;
			}
	    }
		
	    //用户酒馆佣兵信息
		$userHireData = Hapyfish2_Alchemy_HFC_Hire::getHireData($uid);
		$userHireInfo = $userHireData['data'];
		
	    $hireInfo = $userHireInfo[$type];
	    $nowTm = time();
		if ( $fast != 1 ) {
			$remainTm = $hireInfo['endTime'] - $nowTm;
			if ( $remainTm > 0 ) {
				return -340;
			}
		}

		//金币刷新剩余次数判断
		if ( $type == 0 && $hireInfo['times'] < 1 ) {
			return -342;
		}
		
		$firstRefresh = 0;
		//判断 钻石、至尊 首次加速刷新
		if ( $type == 2 && $fast == 1 ) {
			$userHireData['first_refresh_2'];
			if ( $userHireData['first_refresh_2'] != 1 ) {
				$firstRefresh = 2;
				$userHireData['first_refresh_2'] = 1;
			}
		}
		else if ( $type == 3 && $fast == 1 ) {
			if ( $userHireData['first_refresh_3'] != 1 ) {
				$firstRefresh = 3;
				$userHireData['first_refresh_3'] = 1;
			}
		}
		
		/**start - refresh hire **/
		$hireVo = array();
		//所有雇佣位信息
		$hireList = Hapyfish2_Alchemy_HFC_Hire::getAll($uid);
		for ( $i=1;$i<4;$i++ ) {
			//首刷奖励佣兵
			if ( $i == 2 && $firstRefresh > 0 ) {
				$newHire = self::getNewHireByFirstRef($uid, $i, $hireList[$i], $firstRefresh);
				if ( !$newHire ) {
					$newHire = self::getNewHire($uid, $i, $hireList[$i], $hireType);
				}
			}
			else {
				//获取一个新的雇佣佣兵信息
				$newHire = self::getNewHire($uid, $i, $hireList[$i], $hireType);
			}
			
			$ok = Hapyfish2_Alchemy_HFC_Hire::updateOne($uid, $i, $newHire);
			if (!$ok) {
				return -200;
			}
			$hireVo[] = self::getHireVo($newHire);
		}
		
		//更新剩余次数
		$hireInfo['times'] -= 1;
		$hireInfo['times'] = $hireInfo['times'] < 0 ? 0 : $hireInfo['times'];
		switch ( $type ) {
			case 0 :
				$needTm = 10*60;
				break;
			case 1 :
				$needTm = 24*60*60;
				break;
			case 2 :
				$needTm = 2*24*60*60;
				break;
			case 3 :
				$needTm = 5*24*60*60;
				break;
		}
		//计算完成时间
		$hireInfo['endTime'] = $nowTm + $needTm;
		$userHireInfo[$type] = $hireInfo;
		
		$userHireData['data'] = $userHireInfo;
	    //更新用户酒馆佣兵信息
	    $okUpdate = Hapyfish2_Alchemy_HFC_Hire::updateHireData($uid, $userHireData);
	    if ( !$okUpdate ) {
	    	return -200;
	    }
	    
	    if ( $needCoin > 0 ) {
	    	Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $needCoin);
	    }
	    if ( $needGem > 0 ) {
			$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
	    	$gemInfo = array(
	    			'uid' => $uid,
	    			'cost' => $needGem,
	    			'summary' => LANG_PLATFORM_BASE_TXT_10,
	    			'user_level' => $userLevel,
	    			'cid' => $type,
	    			'num' => 1
	    	);
	    	Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo);
	    }
	    
		$refreshList = array();
		foreach ( $userHireInfo as $k => $hire ) {
			$hire['remainingTime'] = $hire['endTime'] - $nowTm;
			$hire['remainingTime'] = $hire['remainingTime'] < 0 ? 0 : $hire['remainingTime'];
			
			if ( $k != 0 ) {
				$hire['times'] = -1;
			}
			//是否有加速刷新奖励
			$hire['first'] = 0;
			if ( $k == 2 && $userHireData['first_refresh_2'] == 0 ) {
				$hire['first'] = 1;
			}
			else if ( $k == 3 && $userHireData['first_refresh_3'] == 0 ) {
				$hire['first'] = 1;
			}
			unset($hire['endTime']);
			$refreshList[] = $hire;
		}
		
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'list', $hireVo);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'refreshList', $refreshList);

		Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'hireCd');
		
		//report log,统计玩家雇佣佣兵信息-刷新佣兵次数
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('302', array($uid));
		
		return 1;
	}

	/**
	 * 雇佣酒馆中佣兵
	 * @param uid $uid
	 * @param id $id,酒馆位置id
	 */
	public static function hireMercenary($uid, $id)
	{
		//所有雇佣位信息
		$hireList = Hapyfish2_Alchemy_HFC_Hire::getAll($uid);

		if (!isset($hireList[$id])) {
			return -220;
		}
		if (empty($hireList[$id])) {
			return -220;
		}
	
		$hireInfo = $hireList[$id];

		if ( $hireInfo['cid'] == 0 ) {
			return -220;
		}

		//用户当前佣兵列表
		$userMercenaryList = Hapyfish2_Alchemy_HFC_FightMercenary::getAll($uid);
		//用户当前最大佣兵数
		$userMaxCount = Hapyfish2_Alchemy_HFC_User::getUserMaxMercenaryCount($uid);
		
		if ( count($userMercenaryList) >= ($userMaxCount - 1) ) {
			return -221;
		}

		$userCoin = Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
		$needCoin = $hireInfo['coin'];
		if ( $userCoin < $needCoin ) {
			return -207;
		}
		
		//神勇点
		/* $needFeats = $hireInfo['feats'];
		if ( $needFeats > 0 ) {
			$featsCid = FEATS_CID;
			$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
			$userFeats = isset($userGoods[$featsCid]['count']) ? $userGoods[$featsCid]['count'] : 0;
			if ( $userFeats < $needFeats ) {
				return -253;
			}
		} */

		$newId = self::getId($uid);
		$info = array(
	        'uid' => $uid,
            'mid' => $newId,
            'cid' => $hireInfo['cid'],
            'gid' => $hireInfo['gid'],
            'rp' => $hireInfo['rp'],
            'job' => $hireInfo['job'],
            'name' => $hireInfo['name'],
            'class_name' => $hireInfo['class_name'],
            'face_class_name' => $hireInfo['face_class_name'],
            's_face_class_name' => $hireInfo['s_face_class_name'],
            'scene_player_class' => $hireInfo['scene_player_class'],
            'sex' => $hireInfo['sex'],
            'element' => $hireInfo['element'],
            'exp' => 0,
            'level' => 1,
			
            'hp' => $hireInfo['hp'],
            'hp_max' => $hireInfo['hp'],
            'mp' => $hireInfo['mp'],
            'mp_max' => $hireInfo['mp'],
            'phy_att' => $hireInfo['phy_att'],
            'phy_def' => $hireInfo['phy_def'],
            'mag_att' => $hireInfo['mag_att'],
            'mag_def' => $hireInfo['mag_def'],
            'agility' => $hireInfo['agility'],
            'crit' => $hireInfo['crit'],
            'dodge' => $hireInfo['dodge'],
            'hit' => $hireInfo['hit'],
            'tou' => $hireInfo['tou'],
            'str' => $hireInfo['str'],
            'dex' => $hireInfo['dex'],
            'mag' => $hireInfo['mag'],
            'phy' => $hireInfo['phy'],
			
			'q_hp' => $hireInfo['q_hp'],
			'q_mp' => $hireInfo['q_mp'],
			'q_phy_att' => $hireInfo['q_phy_att'],
			'q_phy_def' => $hireInfo['q_phy_def'],
			'q_mag_att' => $hireInfo['q_mag_att'],
			'q_mag_def' => $hireInfo['q_mag_def'],
			'q_agility' => $hireInfo['q_agility'],
			'q_crit' => $hireInfo['q_crit'],
			'q_dodge' => $hireInfo['q_dodge'],
			'q_hit' => $hireInfo['q_hit'],
			'q_tou' => $hireInfo['q_tou'],
			'q_str' => $hireInfo['q_str'],
			'q_dex' => $hireInfo['q_dex'],
			'q_mag' => $hireInfo['q_mag'],
			'q_phy' => $hireInfo['q_phy'],
			'q_role' => $hireInfo['q_role'],
			
            'skill' => $hireInfo['skill'],
            'weapon' => array()
	    );
	
		//更新雇佣位
		//$newHire = self::getNewHire($uid, $id, $hireInfo);
		$hireInfo['cid'] = 0;
		$ok = Hapyfish2_Alchemy_HFC_Hire::updateOne($uid, $id, $hireInfo);
		if (!$ok) {
			return -2001;
		}
		
		//添加佣兵
		$ok = Hapyfish2_Alchemy_HFC_FightMercenary::addOne($uid, $info);
		if (!$ok) {
			return -2002;
		}
		
		//扣除金币
		Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $needCoin);
		
		//扣除神勇点
		/* if ( $needFeats > 0 ) {
			Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $featsCid, $needFeats);
		} */
		
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'roleId', $newId);
        
		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
		
		//触发任务处理
    	//$event = array('uid' => $uid, 'data' => array(2,1,9))   job,element,star
        $event = array('uid' => $uid, 'data' => array($hireInfo['job'], $hireInfo['element'], $hireInfo['rp']));
    	Hapyfish2_Alchemy_Bll_TaskMonitor::hireMercenary($event);
    	
    	//重置左侧时间导航栏-侵略冷却时间
    	Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'occCd');
    	
		$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
        $selfMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		//report log,统计玩家雇佣佣兵信息
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('301', array($uid, $userLevel, $selfMercenary['level'], $hireInfo['rp'], $hireInfo['job'], $hireInfo['element'], $needCoin));
    	
		return 1;
	}

	/**
	 * 立刻完成雇佣时间
	 * @param uid $uid
	 * @param id $id,酒馆位置id
	 * @param id $npcId,酒馆位置id
	 * @param id $type,花费类型：1-默认酒道具，2-金币，3-酒
	 */
	public static function completeHire($uid, $id, $npcId, $type = 1)
	{
		if ( $npcId == 2 ) {
			$id += 2;
		}
		else if ( $npcId == 3 ) {
			$id += 4;
		}
	
		//所有雇佣位信息
		$hireList = Hapyfish2_Alchemy_HFC_Hire::getAll($uid);
	
		if (!isset($hireList[$id])) {
			return -220;
		}
		if (empty($hireList[$id])) {
			return -220;
		}
	
		$hireInfo = $hireList[$id];
		if ( $hireInfo['remainingTime'] == 0 ) {
			return -220;
		}
	
		//使用免费的酒
		if ( $type == 3 ) {
			$userWine = Hapyfish2_Alchemy_HFC_Hire::getWine($uid);
			$wineCount = count($userWine['list']);
			$used = $userWine['used'];
			if ( $wineCount < 3 ) {
				return -248;
			}
			if ( $used == 1 ) {
				return -249;
			}
			$userWine['used'] = 1;
			$ok = Hapyfish2_Alchemy_HFC_Hire::updateWine($uid, $userWine);
		}//使用金币
		else if ( $type == 2 ) {
			$userCoin = Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
				
			$hireInfo['level'];
			if ( $hireInfo['type'] == 1 ) {
				$tavernId = TAVERN_ID;
			}
			else if ( $hireInfo['type'] == 2 ) {
				$tavernId = TAVERN_CITY_ID;
			}
			$tavernInfo = Hapyfish2_Alchemy_Cache_Basic::getTavernLevel($hireInfo['level'], $tavernId);
			$needCoin = $tavernInfo['refresh_price'];
				
			if ( $userCoin < $needCoin	) {
				return -207;
			}
			$ok = Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $needCoin);
		}//使用酒道具
		else {
			$needCid = 1115;
			$needCount = 1;
			$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
			if ( $userGoods[$needCid]['count'] < $needCount	) {
				return -204;
			}
			$ok = Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $needCid, $needCount);
				
			//report log,统计玩家雇佣佣兵信息-使用道具次数
			$logger = Hapyfish2_Util_Log::getInstance();
			$logger->report('303', array($uid));
		}
	
		//完成雇佣时间
		if ($ok) {
			$hireInfo['start_time'] = 0;
			Hapyfish2_Alchemy_HFC_Hire::updateOne($uid, $id, $hireInfo);
		}
		$event = array('uid' => $uid, 'data' => 1);
		Hapyfish2_Alchemy_Bll_TaskMonitor::refreshHire($event);

		return 1;
	}
	
	/**
	 * 解雇佣兵
	 * @param int $uid
	 * @param int $id,佣兵实例id
	 */
	public static function dismissMercenary($uid, $id)
	{
		//特殊佣兵不能解雇
		if ( $id < 100 ) {
			return -222;
		}
		//用户佣兵信息
		$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
		if (!$userMercenary) {
			return -222;
		}

		//该佣兵训练信息
		$roleTra = Hapyfish2_Alchemy_HFC_Training::getOne($uid, $id);
		if ( isset($roleTra['id']) && $roleTra['id'] != 0 ) {
			return -283;
		}

		if (!empty($userMercenary['weapon']) ) {
			if ( $userMercenary['weapon'] != array(0,0,0,0) && $userMercenary['weapon'] != array() ) {
				//return -223;
				$weaponList = array();
				foreach ( $userMercenary['weapon'] as $weapon ) {
					if ($weapon > 0) {
						$userWeapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $weapon);
						$userWeapon['status'] = 0;
						Hapyfish2_Alchemy_HFC_Weapon::updateOne($uid, $weapon, $userWeapon);
					}
				}
			}
		}

		$ok = Hapyfish2_Alchemy_HFC_FightMercenary::delOne($uid, $id);
		if (!$ok) {
			return -200;
		}
		//清除佣兵训练信息
		Hapyfish2_Alchemy_HFC_Training::delOne($uid, $id);
		
		//解雇返还经验卷轴
//		$returnCount = self::_getReturnExpCnt($userMercenary['rp'], $userMercenary['exp']);
		$returnCount = floor($userMercenary['exp'] * 0.3 / 200);
//		if ( $returnCount > 0 ) {
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
    	$vipInfo = $vip->getVipInfo($uid);
    	if($vipInfo['level'] >= 5 && $vipInfo['vipStatus'] == 1){
    		$gameData = Hapyfish2_Alchemy_Bll_BasicInfo::getGameData();
			$returnCid = $gameData['expScrollCid'];
			Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, $returnCid, $returnCount);
    	}
		
//		}
		
    	//更新站位信息
    	$fightCorps = Hapyfish2_Alchemy_Cache_FightCorps::getFightCorpsInfo($uid);
    	$oldFightCorps = array();
    	foreach ( $fightCorps as $kC => $vC ) {
    		$oldFightCorps[$kC] = $vC;
    	}
    	foreach ( $oldFightCorps as $pos => $v ) {
    		if ( $v == $id ) {
    			unset($oldFightCorps[$pos]);
    		}
    	}
    	Hapyfish2_Alchemy_Cache_FightCorps::saveFightCorpsInfo($uid, $oldFightCorps);
    	
		$removeRoles = array($id);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeRoles', $removeRoles);

		//重置左侧时间导航栏-侵略冷却时间
		Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'occCd');
		
		//report log,统计玩家雇佣佣兵信息-解雇次数
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('304', array($uid));
		
		return 1;
	}

	public static function _getReturnExpCnt($rp, $exp)
	{
		$proAry = array('1'  => 0.10,
						'2'  => 0.12,
						'3'  => 0.14,
						'4'  => 0.16,
						'5'  => 0.18,
						'6'  => 0.20,
						'7'  => 0.22,
						'8'  => 0.24,
						'9'  => 0.26,
						'10' => 0.28);
		$pro = $proAry[$rp];
		$count = floor(($exp*$pro)/50);
		return $count;
	}
	
	/**
	 * 更换佣兵装备
	 * @param int $uid
	 * @param int $id,佣兵实例id
	 * @param int $equipId,装备实例id
	 */
	public static function replaceEquip($uid, $id, $wid)
	{
		if ( $id == 0 ) {
			//主角信息
        	$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		}
		else {
			//用户佣兵信息
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
		}

		if (!$userMercenary) {
			return -222;
		}

		//新装备信息
		$newWeapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $wid);
		if (!$newWeapon) {
			return -224;
		}
		if ( $newWeapon['status'] != 0 ) {
			return -224;
		}
		if ( $newWeapon['durability'] < 1 ) {
			return -227;
		}
		$weaponInfo = Hapyfish2_Alchemy_Cache_Basic::getWeaponInfo($newWeapon['cid']);
		if ( $userMercenary['level'] < $weaponInfo['level'] ) {
			return -225;
		}

		$weaponJobs = explode(',', $weaponInfo['jobs']);
		if ( !in_array($userMercenary['job'], $weaponJobs) ) {
			return -226;
		}

		$userWeapon = $userMercenary['weapon'];
		if (empty($userWeapon)) {
			$userWeapon = array(0,0,0,0);
		}
		$weaponType = substr($weaponInfo['cid'], -1, 1);
		$field = $weaponType - 1;
		$oldWeaponId = (int)$userWeapon[$field];
		
		if ( $oldWeaponId != 0 ) {
			$oldWeapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $oldWeaponId);
			$oldWeaponInfo = Hapyfish2_Alchemy_Cache_Basic::getWeaponInfo($oldWeapon['cid']);
			if($oldWeapon['durability'] > 0){
				$paChg = $newWeapon['pa'] - $oldWeapon['pa'];
				$pdChg = $newWeapon['pd'] - $oldWeapon['pd'];
				$maChg = $newWeapon['ma'] - $oldWeapon['ma'];
				$mdChg = $newWeapon['md'] - $oldWeapon['md'];
				$speedChg = $newWeapon['speed'] - $oldWeapon['speed'];
				$hpChg = $newWeapon['hp'] - $oldWeapon['hp'];
				$mpChg = $newWeapon['mp'] - $oldWeapon['mp'];
				$criChg = $newWeapon['cri'] - $oldWeapon['cri'];
				$dodChg = $newWeapon['dod'] - $oldWeapon['dod'];
				$hitChg = $newWeapon['hit'] - $oldWeapon['hit'];
				$touChg = $newWeapon['tou'] - $oldWeapon['tou'];
			}
		}
		else {
			$paChg = $newWeapon['pa'];
			$pdChg = $newWeapon['pd'];
			$maChg = $newWeapon['ma'];
			$mdChg = $newWeapon['md'];
			$speedChg = $newWeapon['speed'];
			$hpChg = $newWeapon['hp'];
			$mpChg = $newWeapon['mp'];
			$criChg = $newWeapon['cri'];
			$dodChg = $newWeapon['dod'];
			$hitChg = $newWeapon['hit'];
			$touChg = $newWeapon['tou'];
		}
		
		$userMercenary['hp_max'] += $hpChg;
		$userMercenary['mp_max'] += $mpChg;
		$userMercenary['phy_att'] += $paChg;
		$userMercenary['phy_def'] += $pdChg;
		$userMercenary['mag_att'] += $maChg;
		$userMercenary['mag_def'] += $mdChg;
		$userMercenary['agility'] += $speedChg;
		$userMercenary['crit'] += $criChg;
		$userMercenary['dodge'] += $dodChg;
		$userMercenary['hit'] += $hitChg;
		$userMercenary['tou'] += $touChg;

		$userMercenary['hp'] = $userMercenary['hp'] > $userMercenary['hp_max'] ? $userMercenary['hp_max'] : $userMercenary['hp'];
		$userMercenary['mp'] = $userMercenary['mp'] > $userMercenary['mp_max'] ? $userMercenary['mp_max'] : $userMercenary['mp'];
		
		//新的佣兵装备信息
		$userWeapon[$field] = $wid;
		$userMercenary['weapon'] = $userWeapon;

		if ( $id == 0 ) {
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
			
			$weaponRoleId = -1;
		}
		else {
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $id, $userMercenary);
			$weaponRoleId = $id;
		}
		if (!$ok) {
			return -200;
		}
		
		$newWeapon['status'] = $weaponRoleId;
		Hapyfish2_Alchemy_HFC_Weapon::updateOne($uid, $newWeapon['wid'], $newWeapon);

		$oldWeapon['status'] = 0;
		Hapyfish2_Alchemy_HFC_Weapon::updateOne($uid, $oldWeapon['wid'], $oldWeapon);
		
		$weaponTemp = $userMercenary['weapon'];
        $weaponList = array();
        foreach ( $weaponTemp as $wid ) {
            if ( $wid != 0 ) {
            	$weapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $wid);
            	$info = array_values($weapon);
	            array_splice($info, 2, 1);
	           
            	$weaponList[] = $info;
            }
            else {
            	$weaponList[] = null;
            }
        }
        
		$roleChange = array('id' => $id,
							'maxHp' => $userMercenary['hp_max'],
							'hp' => $userMercenary['hp'],
							'maxMp' => $userMercenary['mp_max'],
							'mp' => $userMercenary['mp'],
							'speed' => $userMercenary['agility'],
							'phyAtk' => $userMercenary['phy_att'],
							'phyDef' => $userMercenary['phy_def'],
							'magAtk' => $userMercenary['mag_att'],
							'magDef' => $userMercenary['mag_def'],
							'baseCrit' => $userMercenary['crit'],
							'baseDodge' => $userMercenary['dodge'],
							'hitRate' => $userMercenary['hit'],
							'lucky' => $userMercenary['tou'],
							'str' => $userMercenary['str'],
							'dex' => $userMercenary['dex'],
							'mag' => $userMercenary['mag'],
							'phy' => $userMercenary['phy'],
							'equipments' => $weaponList);
		$rolesChange = array($roleChange);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);

		//触发任务处理
        $event = array('uid' => $uid, 'data' => 1);
        Hapyfish2_Alchemy_Bll_TaskMonitor::changeWeapon($event);

		return 1;
	}

	/**
	 * 一键卸下所有装备
	 * @param int $uid
	 * @param int $id,佣兵实例id
	 */
	public static function stripAllEquip($uid, $id)
	{
		//用户佣兵信息
		if ( $id == 0 ) {
			//主角信息
        	$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		}
		else {
			//佣兵雇佣信息
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
		}
		if (!$userMercenary) {
			return -222;
		}

		$weaponList = array();
		foreach ( $userMercenary['weapon'] as $weapon ) {
			if ($weapon > 0) {
				$weaponList[] = $weapon;
			}
		}

		//卸下装备
		$userMercenary['weapon'] = array(0,0,0,0);

		$paChg = $pdChg = $maChg = $mdChg = $speedChg = $hpChg = $mpChg = $criChg = $dodChg = 0;

		foreach ( $weaponList as $wid ) {
			$userWeapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $wid);
			$userWeapon['status'] = 0;
			Hapyfish2_Alchemy_HFC_Weapon::updateOne($uid, $wid, $userWeapon);
			$weaponInfo = Hapyfish2_Alchemy_Cache_Basic::getWeaponInfo($userWeapon['cid']);
			if($userWeapon['durability'] > 0){
				$paChg -= $userWeapon['pa'];
				$pdChg -= $userWeapon['pd'];
				$maChg -= $userWeapon['ma'];
				$mdChg -= $userWeapon['md'];
				$speedChg -= $userWeapon['speed'];
				$hpChg -= $userWeapon['hp'];
				$mpChg -= $userWeapon['mp'];
				$criChg -= $userWeapon['cri'];
				$dodChg -= $userWeapon['dod'];
				$hitChg -= $userWeapon['hit'];
				$touChg -= $userWeapon['tou'];
			}
		}

		$userMercenary['hp_max'] += $hpChg;
		$userMercenary['mp_max'] += $mpChg;
		$userMercenary['phy_att'] += $paChg;
		$userMercenary['phy_def'] += $pdChg;
		$userMercenary['mag_att'] += $maChg;
		$userMercenary['mag_def'] += $mdChg;
		$userMercenary['agility'] += $speedChg;
		$userMercenary['crit'] += $criChg;
		$userMercenary['dodge'] += $dodChg;
		$userMercenary['hit'] += $hitChg;
		$userMercenary['tou'] += $touChg;

		$userMercenary['hp'] = $userMercenary['hp'] > $userMercenary['hp_max'] ? $userMercenary['hp_max'] : $userMercenary['hp'];
		$userMercenary['mp'] = $userMercenary['mp'] > $userMercenary['mp_max'] ? $userMercenary['mp_max'] : $userMercenary['mp'];
		
		if ( $id == 0 ) {
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
		}
		else {
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $id, $userMercenary);
		}
		if (!$ok) {
			return -200;
		}

		$roleChange = array('id' => $id,
							'maxHp' => $userMercenary['hp_max'],
							'hp' => $userMercenary['hp'],
							'maxMp' => $userMercenary['mp_max'],
							'mp' => $userMercenary['mp'],
							'speed' => $userMercenary['agility'],
							'phyAtk' => $userMercenary['phy_att'],
							'phyDef' => $userMercenary['phy_def'],
							'magAtk' => $userMercenary['mag_att'],
							'magDef' => $userMercenary['mag_def'],
							'baseCrit' => $userMercenary['crit'],
							'baseDodge' => $userMercenary['dodge'],
							'hitRate' => $userMercenary['hit'],
							'lucky' => $userMercenary['tou'],
							'str' => $userMercenary['str'],
							'dex' => $userMercenary['dex'],
							'mag' => $userMercenary['mag'],
							'phy' => $userMercenary['phy'],
							'equipments' => array(null, null, null, null));
		$rolesChange = array($roleChange);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);

		return 1;
	}

	/**
	 * 更换技能
	 * @param int $uid
	 * @param int $id，佣兵id
	 * @param int $idx，技能位置：1,2,3,4
	 * @param int $cid，技能卷轴cid
	 */
	public static function replaceSkill($uid, $id, $idx, $cid)
	{
		//用户佣兵信息
		if ( $id == 0 ) {
			if ( !in_array($idx, array(1,2,3,4,5)) ) {
				return -200;
			}
			//主角信息
        	$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		}
		else {
			if ( !in_array($idx, array(1,2,3,4,5)) ) {
				return -200;
			}
			//佣兵雇佣信息
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
		}
		
		if (!$userMercenary) {
			return -222;
		}

		//技能卷轴信息
		$scrollInfo = Hapyfish2_Alchemy_Cache_Basic::getScrollInfo($cid);

		//职业匹配
		$scrollJobs = explode(',', $scrollInfo['jobs']);
		if ( !in_array($userMercenary['job'], $scrollJobs) ) {
			return -228;
		}

		//属性匹配
		$scrollElements = explode(',', $scrollInfo['element']);
		if ( !in_array($userMercenary['element'], $scrollElements) ) {
			return -228;
		}
		
		//剩余技能数
		$userScroll	= Hapyfish2_Alchemy_HFC_Scroll::getUserScroll($uid);
		if ( $userScroll[$cid]['count']	< 1 ) {
			return -229;
		}
		
		//需求佣兵等级
		/* if ( $userMercenary['level'] < $scrollInfo['need_level'] ) {
			return -254;
		} */
		
		$skill = $userMercenary['skill'];
		if (empty($skill)) {
			$skill = array(0,0,0,-1,-1);
		}
		$field = $idx - 1;
		/* if ( $idx == 1 && $skill[0] != 0 ) {
			return -200;
		} */
		//未解锁
		if ( $skill[$field] == -1 ) {
			return -230;
		}
		//重复技能
		if ( $skill[$field] == $cid ) {
			return -242;
		}
		
		//技能位置是否解锁，通过佣兵等级判断
		if ( $idx == 2 ) {
			if ( $userMercenary['level'] < 15 ) {
				return -254;
			}
		}
		else if ( $idx == 3 ) {
			if ( $userMercenary['level'] < 30 ) {
				return -254;
			}
		}
		 $oldCid = $skill[$field];
		 $skill[$field] = $cid;
		 $userMercenary['skill'] = $skill;
		 $rolechange = false;
		if($oldCid > 0){
			$oldInfo = Hapyfish2_Alchemy_Cache_Basic::getScrollInfo($oldCid);
			if($oldInfo['skill_type'] == 97){
				$userMercenary = self::reloadSgain($uid, $userMercenary, $oldCid, 1);
				$rolechange = true;
			}
		} 
		if($scrollInfo['skill_type'] == 97){
			$userMercenary = self::reloadSgain($uid, $userMercenary, $cid, 2);
			$rolechange = true;
		}
		//更新技能信息
		if ( $id == 0 ) {
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
		}
		else {
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $id, $userMercenary);
		}
		if (!$ok) {
			return -200;
		}
		//消耗卷轴
		Hapyfish2_Alchemy_HFC_Scroll::useUserScroll($uid, $cid, 1);

		//触发任务处理
        $event = array('uid' => $uid, 'data' => 1);
        Hapyfish2_Alchemy_Bll_TaskMonitor::changeSkill($event);
        
		//report log,统计玩家佣兵技能分布
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('207', array($uid, $cid, $idx));
        if($rolechange){
        	$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
			$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
        }
		return 1;
	}
	

	/**
	 * 解锁技能位
	 * @param int $uid
	 * @param int $id,佣兵实例id
	 * @param int $idx,技能位置：1,2,3,4,5
	 */
	public static function unlockSkill($uid, $id, $idx)
	{
		if ( !in_array($idx, array(1,2,3,4,5)) ) {
			return -200;
		}

		//用户佣兵信息
		if ( $id == 0 ) {
			//主角信息
        	$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		}
		else {
			//佣兵雇佣信息
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
		}
		
		if (!$userMercenary) {
			return -222;
		}

		$skill = $userMercenary['skill'];
		if (empty($skill)) {
			$skill = array(0,0,0,-1,-1);
		}
		$field = $idx - 1;
		if ( $skill[$field] != -1 ) {
			return -200;
		}
		$skill[$field] = 0;
		$userMercenary['skill'] = $skill;

		$needCoin = 0;
		$needGem = 0;
		$needLevel = 0;
		
		if ( $idx == 4 ) {
			$needGem = 10;
		}
		else if ( $idx == 5 ) {
			$needGem = 50;
		}

		if ( $needLevel > $userMercenary['level'] ) {
			return -234;
		}

		if ( $needCoin > 0 ) {
			$userCoin =	Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
			if ( $userCoin < $needCoin ) {
				return -207;
			}
		}
		if ( $needGem > 0 ) {
			$userGem =	Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
			if ( $userGem < $needGem ) {
				return -206;
			}
		}
	
		//更新技能信息
		if ( $id == 0 ) {
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
		}
		else {
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $id, $userMercenary);
		}
		
		if (!$ok) {
			return -200;
		}

		if ( $needCoin > 0 ) {
			Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $needCoin);
		}
		if ( $needGem > 0 ) {
			$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
			//扣除用户宝石
			$gemInfo = array(
	        		'uid' => $uid,
	        		'cost' => $needGem,
	        		'summary' => LANG_PLATFORM_BASE_TXT_3,
	        		'user_level' => $userLevel,
	        		'cid' => $idx,
	        		'num' => 1
	        	);
			Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo);
		}

		return 1;
	}

	/**
	 * 扩大佣兵最大数
	 * @param int $uid
	 */
	public static function expandRoleLimit($uid, $payType)
	{
		//用户当前最大佣兵数
		$userMaxCount = Hapyfish2_Alchemy_HFC_User::getUserMaxMercenaryCount($uid);

		$maxCount = 30;

		if ( $userMaxCount >= $maxCount ) {
			return -231;
		}
		$newCount = $userMaxCount + 1;
		
		$needCoin = 0;
		$needGem = 0;
		//扩展位置信息
		$positionInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryPosition($newCount);
		if ( $payType == 1 ) {
			$needCoin = $positionInfo['coin'];
		}
		else {
			$needGem = $positionInfo['gem'];
		}
		$needHomeLevel = $positionInfo['level'];
		
		//用户自宅等级
		$userHomeLevel = Hapyfish2_Alchemy_HFC_User::getUserHomeLevel($uid);
		if ( $userHomeLevel < $needHomeLevel ) {
			return -250;
		}
		
		if ( $needCoin > 0 ) {
			$userCoin =	Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
			if ( $userCoin < $needCoin ) {
				return -207;
			}
		}
		if ( $needGem > 0 ) {
			$userGem =	Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
			if ( $userGem < $needGem ) {
				return -206;
			}
		}

		$ok = Hapyfish2_Alchemy_HFC_User::updateUserMaxMercenaryCount($uid, $newCount);
		if (!$ok) {
			return -200;
		}

		if ( $needCoin > 0 ) {
			Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $needCoin);
		}
		if ( $needGem > 0 ) {
			$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
			//扣除用户宝石
			$gemInfo = array(
	        		'uid' => $uid,
	        		'cost' => $needGem,
	        		'summary' => LANG_PLATFORM_BASE_TXT_4,
	        		'user_level' => $userLevel,
	        		'cid' => $newCount,
	        		'num' => 1
	        	);
			Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo);
		}

		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'max', $newCount);

		return 1;
	}

	/**
	 * 更换阵型
	 * @param int $uid
	 * @param array $map,阵型信息
	 */
	public static function updateMatrix($uid, $map)
	{
		if (count($map) > 3) {
			return -232;
		}

		$tempAry = array();
		$hasRole = false;
		$mercenaryAry = array();
		//mecenarycorps fight info
		$mecenaryList = Hapyfish2_Alchemy_HFC_FightMercenary::getAll($uid);
		foreach ( $map as $k=>$v ) {
			$tempAry[$k] = $v;
			//判断阵型位置合理性
			if ( $k < 9 || $k > 17 ) {
				return -233;
			}
		
			//佣兵数据正确性
			if ( $v != 0) {
				if ( !isset($mecenaryList[$v]) ) {
					return -233;
				}
				$mercenaryAry[] = $mecenaryList[$v];
			}
			else {
				$hasRole = true;
			}
		}

		//阵型正确性验证
		if ( empty($tempAry) || count($tempAry) > 3 || !$hasRole ) {
			return -233;
		}
		
		//get current map id
		$userVo = Hapyfish2_Alchemy_Bll_User::getUserInit($uid);
		$curMapId = $userVo['currentSceneId'];

		//basic map copy info
		$basMapData = Hapyfish2_Alchemy_Cache_Basic::getMapCopyTranscriptList($curMapId);
		if (isset($basMapData['condition']) && !empty($basMapData['condition'])) {
			foreach ($basMapData['condition'] as $condi) {
				//副本中，出征佣兵等级限制
				if (isset($condi['roleLevel'])) {
					foreach ( $mercenaryAry as $mercenary ) {
						if ( $mercenary['level'] < $condi['roleLevel'] ) {
							return -310;
						}
					}
				}
			}
		}
		
		Hapyfish2_Alchemy_Cache_FightCorps::saveFightCorpsInfo($uid, $map);
		
		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
		
		return 1;
	}

	/**
	 * 回家后补满佣兵血量
	 * @param int $uid
	 */
	public static function resumeHp($uid)
	{
		$needNum = 0;
		
		$list = Hapyfish2_Alchemy_HFC_FightMercenary::getAll($uid);
		foreach ( $list as $v ) {
			$change = 0;
			if ( $v['hp'] < $v['hp_max'] ) {
				$change = 1;
				$needNum += $v['hp_max'] - $v['hp'];
				$v['hp'] = $v['hp_max'];
			}
			if ( $v['mp'] < $v['mp_max'] ) {
				$change = 1;
				$needNum += $v['mp_max'] - $v['mp'];
				$v['mp'] = $v['mp_max'];
			}
			if ( $change == 1 ) {
				Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $v['mid'], $v);
			}
		}
		
		$selfChange = 0;
		//主角信息
        $selfInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
        if ( $selfInfo['hp'] < $selfInfo['hp_max'] ) {
        	$selfChange = 1;
			$needNum += $selfInfo['hp_max'] - $selfInfo['hp'];
        	$selfInfo['hp'] = $selfInfo['hp_max'];
        }
        if ( $selfInfo['mp'] < $selfInfo['mp_max'] ) {
        	$selfChange = 1;
			$needNum += $selfInfo['mp_max'] - $selfInfo['mp'];
        	$selfInfo['mp'] = $selfInfo['mp_max'];
        }
        if ( $selfChange == 1 ) {
			Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $selfInfo, true);
        }
		
        $needCoin = $needNum;
        if ( $needCoin > 0 ) {
			$userCoin = Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
			if ($userCoin < $needCoin) {
				return -207;
			}
			Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $needCoin);
        }
        
		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
		
		return 1;
	}
	
	/**
	 * 主角晋级
	 * @param int $uid
	 */
	public static function roleUpgrade($uid)
	{
		//主角信息
        $userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
        $nextRp = $userMercenary['rp'] + 1;
        if ( $nextRp > 10 ) {
        	return -200;
        }
        
		//下一级信息
		$levelInfo = Hapyfish2_Alchemy_Cache_Basic::getRoleLevel($nextRp);
		if (!$levelInfo) {
			return -200;
		}
		
		//需求经营等级
		$needLevel = $levelInfo['need_level'];
		$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
		if ( $userLevel < $needLevel ) {
			return -243;
		}
		
		//需求主角等级
		$needRoleLevel = $levelInfo['need_role_level'];
		if ( $userMercenary['level'] < $needRoleLevel ) {
			return -251;
		}
		
		$userCoin = Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
		if ( $userCoin < $levelInfo['need_coin'] ) {
			return -207;
		}
			
		//判断材料是否足够
		$needs = json_decode($levelInfo['need_items']);
		$needGoods = array();
		$needScroll	= array();
		$needStuff = array();
		$needDecorInBag	= array();
		$needWeapon	= array();
		foreach	( $needs as	$need )	{
			$needCid = $need[0];
			$needCount = $need[1];
			$needType =	substr($needCid, -2, 1);
			//1->物品,2->卷轴,3->材料,4->工作台,5->装修,6->装备
			if ( $needType == 1	) {
				$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
				if ( $userGoods[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needGoods[] = array('cid' => $needCid,	'count'	=> $needCount);
			}
			else if	( $needType	== 2 ) {
				$userScroll	= Hapyfish2_Alchemy_HFC_Scroll::getUserScroll($uid);
				if ( $userScroll[$needCid]['count']	< $needCount ) {
					return -204;
				}
				$needScroll[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
			else if	( $needType	== 3 ) {
				$userStuff = Hapyfish2_Alchemy_HFC_Stuff::getUserStuff($uid);
				if ( $userStuff[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needStuff[] = array('cid' => $needCid,	'count'	=> $needCount);
			}
			else if	( $needType	== 5 ) {
				$userDecorInBag	= Hapyfish2_Alchemy_HFC_Decor::getBag($uid);
				if ( $userDecorInBag[$needCid]['count']	< $needCount ) {
					return -204;
				}
				$needDecorInBag[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
			else if	( $needType	== 6 ) {
				$userNewWeapon = Hapyfish2_Alchemy_HFC_Weapon::getNewWeapon($uid);
				if ( $userNewWeapon[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needWeapon[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
		}
		
		//完成升级
		//佣兵模型信息
		$modelInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryInfoByJob($userMercenary['job']);
		//成长信息
		$growInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryGrowInfoByJob($userMercenary['job']);

		$fieldAry = array('0' => 'hp',
						  '1' => 'mp',
						  '2' => 'phy_att',
						  '3' => 'phy_def',
						  '4' => 'mag_att',
						  '5' => 'mag_def',
						  '6' => 'agility',
						  '7' => 'crit',
						  '8' => 'dodge',
						  '9' => 'hit',
						  '10' => 'tou',
						  '11' => 'str',
						  '12' => 'dex',
						  '13' => 'mag',
						  '14' => 'phy');
		
		//随机新佣兵属性品质，及获得初始属性
		$qualityList = self::getRandQuality($nextRp, $userMercenary['job']);
		
		//晋级后，各属性增加
		$growChange = array();
		foreach ( $fieldAry as $field ) {
			$qField = 'q_'.$field;
			
			//升级后数值
			$growChange[$field] = self::_getProperty($modelInfo[$field], $growInfo[$field], $nextRp, $qualityList[$qField], $userMercenary['level']);
			//升级前数值，差值
			$growChange[$field] -= self::_getProperty($modelInfo[$field], $growInfo[$field], $userMercenary['rp'], $userMercenary[$qField], $userMercenary['level']);
			
			//主角新的各属性资质信息
			$userMercenary[$qField] = $qualityList[$qField];
		}
		
		//主角晋级后新属性信息
		$userMercenary['hp_max'] += $growChange['hp'];	
		$userMercenary['hp'] = $userMercenary['hp_max'];
		$userMercenary['mp_max'] += $growChange['mp'];
		$userMercenary['mp'] = $userMercenary['mp_max'];
		$userMercenary['phy_att'] += $growChange['phy_att'];
		$userMercenary['phy_def'] += $growChange['phy_def'];
		$userMercenary['mag_att'] += $growChange['mag_att'];
		$userMercenary['mag_def'] += $growChange['mag_def'];
		$userMercenary['agility'] += $growChange['agility'];
		$userMercenary['crit'] += $growChange['crit'];
		$userMercenary['dodge'] += $growChange['dodge'];
		$userMercenary['hit'] += $growChange['hit'];
		$userMercenary['tou'] += $growChange['tou'];
		$userMercenary['str'] += $growChange['str'];
		$userMercenary['dex'] += $growChange['dex'];
		$userMercenary['mag'] += $growChange['mag'];
		$userMercenary['phy'] += $growChange['phy'];
		
		$userMercenary['rp'] = $nextRp;
		
		$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
		if (!$ok) {
			return -200;
		}
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'quality', $nextRp);
		
		//扣除金币
		Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $levelInfo['need_coin']);
		
		$removeItems = array();
		//扣除需求物品
		if ( !empty($needGoods)	) {
			foreach	( $needGoods as	$goods ) {
				$userGoods[$goods['cid']]['count'] -= $goods['count'];
				$userGoods[$goods['cid']]['update'] = 1;
				$removeItems[] = array($goods['cid'], $goods['count'], 0);
			}
			Hapyfish2_Alchemy_HFC_Goods::updateUserGoods($uid, $userGoods);
		}
		if ( !empty($needScroll) ) {
			foreach	( $needScroll as $scroll ) {
				$userScroll[$scroll['cid']]['count'] -= $scroll['count'];
				$userScroll[$scroll['cid']]['update'] = 1;
				$removeItems[] = array($scroll['cid'], $scroll['count'], 0);
			}
			Hapyfish2_Alchemy_HFC_Scroll::updateUserScroll($uid, $userScroll);
		}
		if ( !empty($needStuff)	) {
			foreach	( $needStuff as	$stuff ) {
				$userStuff[$stuff['cid']]['count'] -= $stuff['count'];
				$userStuff[$stuff['cid']]['update'] = 1;
				$removeItems[] = array($stuff['cid'], $stuff['count'], 0);
			}
			Hapyfish2_Alchemy_HFC_Stuff::updateUserStuff($uid, $userStuff);
		}
		if ( !empty($needDecorInBag) ) {
			foreach	( $needDecorInBag as $decor	) {
				$userDecorInBag[$decor['cid']]['count'] -= $decor['count'];
				$userDecorInBag[$decor['cid']]['update'] = 1;
				$removeItems[] = array($decor['cid'], $decor['count'], 0);
			}
			Hapyfish2_Alchemy_HFC_Decor::updateBag($uid, $userDecorInBag);
		}
		if ( !empty($needWeapon) ) {
			foreach	( $needWeapon as $weapon ) {
				for	( $i=0;$i<$weapon['count'];$i++	) {
					Hapyfish2_Alchemy_HFC_Weapon::delWeaponByCid($uid, $weapon['cid']);
				}
			}
		}
		
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeItems', $removeItems);
		
		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
		
		self::getRoleQuality($uid);
		
		//platform feed
		Hapyfish2_Alchemy_Bll_PlatformFeed_Feed::userRolePromotion($uid, $nextRp);
		
		return 1;
	}

	/**
	 * 获取主角各属性资质信息，是否为最大值
	 * @param int $uid
	 * @return array
	 */
	public static function getRoleQuality($uid)
	{
		//主角信息
		$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		if ( !$userMercenary ) {
			return -200;
		}
		
		$array = array(
				'isBasehpMax' => $userMercenary['q_hp'] == 5 ? 1 : 0,
				'isBasempMax' => $userMercenary['q_mp'] == 5 ? 1 : 0,
				'isBasepaMax' => $userMercenary['q_phy_att'] == 5 ? 1 : 0,
				'isBasepdMax' => $userMercenary['q_phy_def'] == 5 ? 1 : 0,
				'isBasemaMax' => $userMercenary['q_mag_att'] == 5 ? 1 : 0,
				'isBasemdMax' => $userMercenary['q_mag_def'] == 5 ? 1 : 0,
				'isBasespeedMax' => $userMercenary['q_agility'] == 5 ? 1 : 0,
				'isBasecritMax' => $userMercenary['q_crit'] == 5 ? 1 : 0,
				'isBasedodgeMax' => $userMercenary['q_dodge'] == 5 ? 1 : 0,
				'isBasehitRateMax' => $userMercenary['q_hit'] == 5 ? 1 : 0,
				'isBaseluckyMax' => $userMercenary['q_tou'] == 5 ? 1 : 0);
		
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'roleResetInitVo', $array);
		
		return 1;
	}
	
	/**
	 * 重置主角各属性资质
	 * @param int $uid
	 * @param array $locks
	 */
	public static function resetRoleQuality($uid, $locks)
	{
		//$fieldAry = array('hp', 'mp', 'phy_att', 'phy_def', 'mag_att', 'mag_def', 'agility', 'crit', 'dodge', 'hit', 'tou');
		$fieldAry = array('1' => 'hp',
						'2' => 'mp',
						'3' => 'phy_att',
						'4' => 'phy_def',
						'5' => 'mag_att',
						'6' => 'mag_def',
						'7' => 'agility',
						'8' => 'crit',
						'9' => 'dodge',
						'10' => 'hit',
						'11' => 'tou');
		
		foreach ( $locks as $k ) {
			if ( !isset($fieldAry[$k]) ) {
				return -200;
			}
		}
		
		//锁定属性花费宝石数
		$lockCnt = count($locks);
		$lockPrice = self::_getLockPrice($lockCnt);
		
		$needGem = $lockPrice + 5;
		
		//判断用户宝石
		$userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
		if ($userGem < $needGem) {
			return -206;
		}
		
		//主角信息
		$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		if ( !$userMercenary ) {
			return -200;
		}
		
		//VIP 2级以上开放重置功能 - 关闭VIP限制
		/* $bllVip = new Hapyfish2_Alchemy_Bll_Vip();
		$userVipInfo = $bllVip->getVipInfo($uid);
		if ( $userVipInfo['level'] < 2 ) {
			return -341;
		} */
		
		/**start - reset role quality**/
		$fieldAry[] = 'str';
		$fieldAry[] = 'dex';
		$fieldAry[] = 'mag';
		$fieldAry[] = 'phy';
		
		//佣兵模型信息
		$modelInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryInfoByJob($userMercenary['job']);
		//成长信息
		$growInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryGrowInfoByJob($userMercenary['job']);
		
		//随机新佣兵属性品质，及获得初始属性
		$qualityList = self::getRandQuality($userMercenary['rp'], $userMercenary['job'], 0, $locks, $userMercenary);
		
		//已锁定的属性，则保持不变
		foreach ( $locks as $key ) {
			$qField = 'q_'.$fieldAry[$key];
			$qualityList[$qField] = $userMercenary[$qField];
		}
		
		//刷新后各属性变化
		$growChange = array();
		foreach ( $fieldAry as $field ) {
			$qField = 'q_'.$field;
			
			//刷新后的数值
			$growChange[$field] = self::_getProperty($modelInfo[$field], $growInfo[$field], $userMercenary['rp'], $qualityList[$qField], $userMercenary['level']);
			
			//刷新前数值，差值
			$growChange[$field] -= self::_getProperty($modelInfo[$field], $growInfo[$field], $userMercenary['rp'], $userMercenary[$qField], $userMercenary['level']);
			
			//主角新的各属性资质信息
			$userMercenary[$qField] = $qualityList[$qField];
		}
		
		//主角晋级后新属性信息
		$userMercenary['hp_max'] += $growChange['hp'];
		$userMercenary['hp'] = $userMercenary['hp_max'];
		$userMercenary['mp_max'] += $growChange['mp'];
		$userMercenary['mp'] = $userMercenary['mp_max'];
		$userMercenary['phy_att'] += $growChange['phy_att'];
		$userMercenary['phy_def'] += $growChange['phy_def'];
		$userMercenary['mag_att'] += $growChange['mag_att'];
		$userMercenary['mag_def'] += $growChange['mag_def'];
		$userMercenary['agility'] += $growChange['agility'];
		$userMercenary['crit'] += $growChange['crit'];
		$userMercenary['dodge'] += $growChange['dodge'];
		$userMercenary['hit'] += $growChange['hit'];
		$userMercenary['tou'] += $growChange['tou'];
		$userMercenary['str'] += $growChange['str'];
		$userMercenary['dex'] += $growChange['dex'];
		$userMercenary['mag'] += $growChange['mag'];
		$userMercenary['phy'] += $growChange['phy'];
		$userMercenary['q_role'] = $qualityList['q_role'];

		try {
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
			if (!$ok) {
				return -200;
			}
	
			$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
			
			$gemInfo = array(
					'uid' => $uid,
					'cost' => $needGem,
					'summary' => LANG_PLATFORM_BASE_TXT_9 . $userMercenary['rp'],
					'user_level' => $userLevel,
					'cid' => $userMercenary['rp'],
					'num' => 1
			);
			Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo);
		}
		catch ( Exception $e ) {
			info_log('resetRoleQuality:failed'.$e->getMessage(), 'Bll_Mercenary');
    	}
		
		$RoleResetVo = array('hps' => $growChange['hp'],
							 'mps' => $growChange['mp'],
							 'pas' => $growChange['phy_att'],
							 'pds' => $growChange['phy_def'],
							 'mas' => $growChange['mag_att'],
							 'mds' => $growChange['mag_def'],
							 'speeds' => $growChange['agility'],
							 'crits' => $growChange['crit'],
							 'dodges' => $growChange['dodge'],
							 'hitRates' => $growChange['hit'],
							 'luckys' => $growChange['tou'],

							 'ishpMax' => $qualityList['q_hp'] == 5 ? 1 : 0,
							 'ismpMax' => $qualityList['q_mp'] == 5 ? 1 : 0,
							 'ispaMax' => $qualityList['q_phy_att'] == 5 ? 1 : 0,
							 'ispdMax' => $qualityList['q_phy_def'] == 5 ? 1 : 0,
							 'ismaMax' => $qualityList['q_mag_att'] == 5 ? 1 : 0,
							 'ismdMax' => $qualityList['q_mag_def'] == 5 ? 1 : 0,
							 'isspeedMax' => $qualityList['q_agility'] == 5 ? 1 : 0,
							 'iscritMax' => $qualityList['q_crit'] == 5 ? 1 : 0,
							 'isdodgeMax' => $qualityList['q_dodge'] == 5 ? 1 : 0,
							 'ishitRateMax' => $qualityList['q_hit'] == 5 ? 1 : 0,
							 'isluckyMax' => $qualityList['q_tou'] == 5 ? 1 : 0);

		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'roleResetVo', $RoleResetVo);

		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
		
		return 1;
		
	}
	
	/**
	 * 锁定属性需要花费宝石数
	 * 
	 * @param int $lockCnt
	 */
	private static function _getLockPrice($lockCnt)
	{
		switch ( $lockCnt ) {
			case 1 :
				$needGem = 10;
				break;
			case 2 :
				$needGem = 20;
				break;
			case 3 :
				$needGem = 40;
				break;
			case 4 :
				$needGem = 80;
				break;
			case 5 :
				$needGem = 160;
				break;
			case 6 :
				$needGem = 320;
				break;
			case 7 :
				$needGem = 640;
				break;
			case 8 :
				$needGem = 1280;
				break;
			case 9 :
				$needGem = 2560;
				break;
			case 10 :
				$needGem = 5120;
				break;
			case 11 :
				$needGem = 10240;
				break;
			default:
				$needGem = 0;
				break;
		}
		return $needGem;
	}
	
	public static function getNewGrowForRoleUpgrade($rp, $job)
	{
		//随机成长曲线
		$growList = Hapyfish2_Alchemy_Cache_Basic::getMercenaryGrowByRp($rp);
		$list = array();
		foreach ( $growList as $v ) {
			if ( $v['job'] == $job ) {
				$list[] = $v;
			}
		}
		$grow = self::array_random($list);
		return $grow['id'];
	}
	
/********************************************************************************************/
	/**
	 * 获取新的雇佣位信息
	 * @param int $uid
	 * @param int $id
	 * @param int $hire
	 * @param int $type,酒馆刷新类型，0-3 普通，白金...
	 */
	public static function getNewHire($uid, $id, $hire, $type)
	{
		$maxCount = 21;
	
		//随机星级,职业,元素属性:风火水
		$rp = self::getRandRp($hire['level'], $type);
		$job = rand(1, 3);
		$element = rand(1, 3);
		
		//玩家佣兵名称列表,已雇佣+酒馆中
		$userMercenaryList = Hapyfish2_Alchemy_HFC_FightMercenary::getAll($uid);
		$userNameList = array();
		foreach ( $userMercenaryList as $mercenary ) {
			$userNameList[] = $mercenary['name'];
		}
		$userHireList = Hapyfish2_Alchemy_HFC_Hire::getAll($uid);
		foreach ( $userHireList as $v ) {
			$userNameList[] = $v['name'];
		}

		//随机名字
		$modelInfo = array();
		$modelInfo = self::getRandModelWithName($modelInfo, $userNameList, $maxCount, $job, $hire);
		
		//随机获得初始技能
		$skill = '[0,0,0,-1,-1]';
		$skill = self::getRandSkill($skill, $hire['type'], $hire['level'], $rp, $job, $element);

		//随机新佣兵属性品质，及获得初始属性
		$qualityList = self::getRandQuality($rp, $job, $type);

		//获取佣兵各属性值
		$propertyList = self::getNewHireProperty($job, $rp, $qualityList);

		//计算雇佣需要价格，神勇点
		$q = 1;
		$p = 1;
		foreach ( $qualityList as $v ) {

			$m = (1 + $v * 0.03);
			$q = $m * $q;
			
			$n = (1 + $v * 0.03);
			$p = $n * $p;
		}

		$coin = 130 * (1 + pow($rp, 2.5) *$q);
		$coin = ceil($coin / 50) * 50;

		/* $feats = 2 * (pow(($rp - 1), 2) * $p);
		$feats = ceil($feats / 5) * 5; */
		
		$hire['id'] = $id;
		$hire['type'] = $hire['type'];
		$hire['level'] = $hire['level'];
		$hire['cid'] = 1;
		$hire['gid'] = 1;
		$hire['rp'] = $rp;
		$hire['job'] = $job;
		$hire['element'] = $element;
		$hire['name'] = $modelInfo['name'];
		$hire['content'] = $modelInfo['content'];
		$hire['sex'] = $modelInfo['sex'];
		$hire['class_name'] = $modelInfo['class_name'];
		$hire['face_class_name'] = $modelInfo['face_class_name'];
		$hire['s_face_class_name'] = $modelInfo['s_face_class_name'];
		$hire['scene_player_class'] = $modelInfo['scene_player_class'];
		$hire['skill'] = $skill;
		$hire['start_time'] = time();
		
		$hire['hp'] = $propertyList['hp'];
		$hire['mp'] = $propertyList['mp'];
		$hire['phy_att'] = $propertyList['phy_att'];
		$hire['phy_def'] = $propertyList['phy_def'];
		$hire['mag_att'] = $propertyList['mag_att'];
		$hire['mag_def'] = $propertyList['mag_def'];
		$hire['agility'] = $propertyList['agility'];
		$hire['crit'] = $propertyList['crit'];
		$hire['dodge'] = $propertyList['dodge'];
		$hire['hit'] = $propertyList['hit'];
		$hire['tou'] = $propertyList['tou'];
		$hire['str'] = $propertyList['str'];
		$hire['dex'] = $propertyList['dex'];
		$hire['mag'] = $propertyList['mag'];
		$hire['phy'] = $propertyList['phy'];
		
		$hire['q_hp'] = $qualityList['q_hp'];
		$hire['q_mp'] = $qualityList['q_mp'];
		$hire['q_phy_att'] = $qualityList['q_phy_att'];
		$hire['q_phy_def'] = $qualityList['q_phy_def'];
		$hire['q_mag_att'] = $qualityList['q_mag_att'];
		$hire['q_mag_def'] = $qualityList['q_mag_def'];
		$hire['q_agility'] = $qualityList['q_agility'];
		$hire['q_crit'] = $qualityList['q_crit'];
		$hire['q_dodge'] = $qualityList['q_dodge'];
		$hire['q_hit'] = $qualityList['q_hit'];
		$hire['q_tou'] = $qualityList['q_tou'];
		$hire['q_str'] = $qualityList['q_str'];
		$hire['q_dex'] = $qualityList['q_dex'];
		$hire['q_mag'] = $qualityList['q_mag'];
		$hire['q_phy'] = $qualityList['q_phy'];
		$hire['q_role'] = $qualityList['q_role'];
		
		$hire['coin'] = $coin;
		$hire['feats'] = 0;
		
		return $hire;
	}
	
	/**
	 * 获取新的雇佣位信息，快速刷新酒馆奖励
	 * @param int $uid
	 * @param int $id
	 * @param int $hire
	 * @param int $type,快速刷新类型,2:钻石刷新，3:至尊刷新
	 */
	public static function getNewHireByFirstRef($uid, $id, $hire, $type)
	{
		if ( $type == 2 ) {
			$roleId = 10;
		}
		else if ( $type == 3 ) {
			$roleId = 11;
		}
		
		$info = Hapyfish2_Alchemy_Cache_Basic::getInitRole($roleId);
		if ($info == null) {
			return false;
		}
		
		$hire['id'] = $id;
		$hire['type'] = $hire['type'];
		$hire['level'] = $hire['level'];
		$hire['cid'] = 10;
		$hire['gid'] = $info['gid'];
		$hire['rp'] = $info['rp'];
		$hire['job'] = $info['job'];
		$hire['element'] = $info['element'];
		$hire['name'] = $info['name'];
		$hire['content'] = null;
		$hire['sex'] = $info['sex'];
		$hire['class_name'] = $info['class_name'];
		$hire['face_class_name'] = $info['face_class_name'];
		$hire['s_face_class_name'] = $info['s_face_class_name'];
		$hire['scene_player_class'] = $info['scene_player_class'];
		$hire['skill'] = json_decode($info['skill'], true);
		$hire['start_time'] = 0;
	
		$hire['hp'] = $info['hp'];
		$hire['mp'] = $info['mp'];
		$hire['phy_att'] = $info['phy_att'];
		$hire['phy_def'] = $info['phy_def'];
		$hire['mag_att'] = $info['mag_att'];
		$hire['mag_def'] = $info['mag_def'];
		$hire['agility'] = $info['agility'];
		$hire['crit'] = $info['crit'];
		$hire['dodge'] = $info['dodge'];
		$hire['hit'] = $info['hit'];
		$hire['tou'] = $info['tou'];
		$hire['str'] = $info['str'];
		$hire['dex'] = $info['dex'];
		$hire['mag'] = $info['mag'];
		$hire['phy'] = $info['phy'];
	
		$hire['q_hp'] = $info['q_hp'];
		$hire['q_mp'] = $info['q_mp'];
		$hire['q_phy_att'] = $info['q_phy_att'];
		$hire['q_phy_def'] = $info['q_phy_def'];
		$hire['q_mag_att'] = $info['q_mag_att'];
		$hire['q_mag_def'] = $info['q_mag_def'];
		$hire['q_agility'] = $info['q_agility'];
		$hire['q_crit'] = $info['q_crit'];
		$hire['q_dodge'] = $info['q_dodge'];
		$hire['q_hit'] = $info['q_hit'];
		$hire['q_tou'] = $info['q_tou'];
		$hire['q_str'] = $info['q_str'];
		$hire['q_dex'] = $info['q_dex'];
		$hire['q_mag'] = $info['q_mag'];
		$hire['q_phy'] = $info['q_phy'];
	
		$hire['coin'] = 750;
		$hire['feats'] = 0;
	
		return $hire;
	}
	
	/**
	 * 获取佣兵初始技能（随机）
	 * 
	 * @param array $skill,默认技能列表“[0,0,0,-1,-1]”
	 * @param int $hireType,可以出现在哪个酒馆:1,2,3
	 * @param int $hireLevel,需要酒馆等级
	 * @param int $rp,对应佣兵星级
	 * @param int $job,对应佣兵职业,1:战士,2:弓手,3:法师
	 * @param int $element,对应佣兵属性,1:风,2:火,3:水
	 */
	public static function getRandSkill($skill, $hireType, $hireLevel, $rp, $job, $element)
	{
		$skill = json_decode($skill);
		if ( $skill[0] != 0 ) {
			return $skill;
		}
		
		$scrollList = Hapyfish2_Alchemy_Cache_Basic::getScrollList();
		foreach ( $scrollList as $k => $scroll ) {
			if ( $scroll['type'] != 22 ) {
				unset($scrollList[$k]);
			}
			if ( $scroll['skill_level'] != 1 ) {
				unset($scrollList[$k]);
			}
			/* if ( $scroll['hire_type'] != $hireType && $scroll['hire_type'] != '1,2,3' ) {
				unset($scrollList[$k]);
			}
			if ( $scroll['hire_level'] > $hireLevel ) {
				unset($scrollList[$k]);
			}
			if ( $scroll['rp'] > $rp ) {
				unset($scrollList[$k]);
			} */
			if ( $scroll['jobs'] != $job && $scroll['jobs'] != '1,2,3' ) {
				unset($scrollList[$k]);
			}
			if ( $scroll['element'] != $element && $scroll['element'] != '1,2,3' ) {
				unset($scrollList[$k]);
			}
		}

		//根据各技能权重随机获取
		/* $randArray = array();
		foreach ( $scrollList as $v ) {
			for ( $i=0; $i<$v['probability'];$i++ ) {
				$randArray[] = $v['cid'];
			}
		}
		$rand = array_rand($randArray);
		$randSkill = $randArray[$rand]; */
		
		//根据随机数，计算当前佣兵获得技能个数
		$randSkillCnt = rand(1, 100);
		if ( $randSkillCnt <= 50 ) {
			$skillNum = 1;
		}
		else if ( $randSkillCnt <= 80 ) {
			$skillNum = 2;
		}
		else {
			$skillNum = 3;
		}
		
		$lockSkill = array();
		for ( $i=0;$i<$skillNum;$i++ ) {
			if ( $i == 0 ) {
				$scrollList1 = $scrollList;
				foreach ( $scrollList1 as $k => $scroll ) {
					if ( !in_array($scroll['skill_type'], array(1,2,3,4)) ) {
						unset($scrollList1[$k]);
					}
				}
				$randSkill1 = array_rand($scrollList1);
				$lockSkill[$randSkill1] = $randSkill1;
				$skill[0] = $randSkill1;
			}
			else if ( $i == 1 ) {
				$scrollList2 = $scrollList;
				foreach ( $scrollList2 as $k => $scroll ) {
					if ( !in_array($scroll['skill_type'], array(1,2,3,4,5,6,7,8,9)) ) {
						unset($scrollList2[$k]);
					}
					if ( in_array($k, $lockSkill) ) {
						unset($scrollList2[$k]);
					}
				}
				$randSkill2 = array_rand($scrollList2);
				$lockSkill[$randSkill2] = $randSkill2;
				$skill[1] = $randSkill2;
			}
			else if ( $i == 2 ) {
				$scrollList3 = $scrollList;
				foreach ( $scrollList3 as $k => $scroll ) {
					if ( !in_array($scroll['skill_type'], array(1,2,3,4,5,6,7,8,9,10,12,13)) ) {
						unset($scrollList3[$k]);
					}
					if ( in_array($k, $lockSkill) ) {
						unset($scrollList3[$k]);
					}
				}
				$randSkill3 = array_rand($scrollList3);
				$lockSkill[$randSkill3] = $randSkill3;
				$skill[2] = $randSkill3;
			}
		}
		
		return $skill;
	}
	
	/**
	 * 获取模型随机名字
	 * 
	 * @param array $modelInfo , 模型信息
	 * @param array $userNameList , 用户已有佣兵名字列表
	 * @param int $maxCount , 用户可拥有最大佣兵数
	 * @param int $job , 佣兵职业
	 */
	public static function getRandModelWithName($modelInfo, $userNameList, $maxCount, $job, $hire)
	{
		$modelNameList = Hapyfish2_Alchemy_Cache_Basic::getMercenaryNameList($maxCount, $job);

		//根据酒馆类型（自宅，村庄，王城） 过滤,酒馆等级过滤		
		$nameList = array();
		foreach ( $modelNameList as $key=>$model ) {
			$nameHireType = explode(',', $model['hire_type']);
			if ( !in_array($hire['type'], $nameHireType) || $hire['level'] < $model['hire_level'] ) {
				unset($modelAllList[$kModel]);
			}
			$nameList[$model['id']] = $model['name'];
		}
		$tempNameList = array_diff($nameList, $userNameList);
		$nameId = array_rand($tempNameList);
		$nameInfo = $modelNameList[$nameId];
		$modelInfo = array_merge($modelInfo, $nameInfo);

		if ( $modelInfo['sex'] == 0 ) {
			$modelInfo['sex'] = rand(1, 2);
		}
		if ( $modelInfo['sex'] == 1 ) {
			$modelInfo['class_name'] = $modelInfo['class_name_1'];
			$modelInfo['face_class_name'] = $modelInfo['face_class_name_1'];
			$modelInfo['s_face_class_name'] = $modelInfo['s_face_class_name_1'];
			$modelInfo['scene_player_class'] = $modelInfo['scene_player_class_1'];
		}
		else {
			$modelInfo['class_name'] = $modelInfo['class_name_2'];
			$modelInfo['face_class_name'] = $modelInfo['face_class_name_2'];
			$modelInfo['s_face_class_name'] = $modelInfo['s_face_class_name_2'];
			$modelInfo['scene_player_class'] = $modelInfo['scene_player_class_2'];
		}
		return $modelInfo;
	}

	public static function getHireVo($hire)
	{
		/* if (isset($hire['start_time'])) {
			$needTime = self::getNeedTime($hire['level'], $hire['type']);
			$remainTime = ($hire['start_time'] + $needTime) - time();
			$remainTime = $remainTime < 0 ? 0 : $remainTime;
		}
		else {
			$remainTime = $hire['remainingTime'];
		} */

		$startTime = $hire['time'];
		$hireVo = array('id' => (int)$hire['id'],
						'price' => (int)$hire['coin'],
						//'brave' => $hire['feats'],
						//'remainingTime' => $remainTime,
						//'dialog' => $hire['content'],
						'name' => $hire['name'],
						'prop' => (int)$hire['element'],
						'className' => $hire['class_name'],
						'faceClass' => $hire['face_class_name'],
						'sFaceClass' => $hire['s_face_class_name'],
						'profession' => (int)$hire['job'],
				
						'hp' => (int)$hire['hp'],
						'maxHp' => (int)$hire['hp'],
						'mp' => (int)$hire['mp'],
						'maxMp' => (int)$hire['mp'],
						'speed' => (int)$hire['agility'],
						'phyAtk' => (int)$hire['phy_att'],
						'phyDef' => (int)$hire['phy_def'],
						'magAtk' => (int)$hire['mag_att'],
						'magDef' => (int)$hire['mag_def'],
						'baseCrit' => (int)$hire['crit'],
						'baseDodge' => (int)$hire['dodge'],
						'hitRate' => (int)$hire['hit'],
						'lucky' => (int)$hire['tou'],
						'str' => (int)$hire['str'],
						'dex' => (int)$hire['dex'],
						'mag' => (int)$hire['mag'],
						'phy' => (int)$hire['phy'],
				
						'sStr' => (int)$hire['q_str'],
						'sDex' => (int)$hire['q_dex'],
						'sMag' => (int)$hire['q_mag'],
						'sPhy' => (int)$hire['q_phy'],
						//佣兵整体品质
						//'sabcd' => $hire['q_role'],
						
						'skills' => json_encode($hire['skill']),
						'quality' => (int)$hire['rp'],
						'level' => 1,
						'exp' => 0,
						'maxExp' => 9);
		
		return $hireVo;
	}

	/**
	 * 获取新佣兵等级
	 * @param int $uid
	 * @param int $level,酒馆等级
	 * @param int $type,酒馆刷新类型
	 */
	public static function getRandRp($level, $type)
	{
		$randArray = Hapyfish2_Alchemy_Cache_Basic::getMercenaryRpRandList();
		foreach ( $randArray as $v ) {
			if ( $v['level'] == $level && $v['type'] == $type ) {
				$randData = $v;
			}
		}

		$rand = rand(1, 1000);
		if ( $rand <= $randData['pro1'] ) {
			$rp = 1;
		}
		else if ( $rand <= $randData['pro2'] ) {
			$rp = 2;
		}
		else if ( $rand <= $randData['pro3'] ) {
			$rp = 3;
		}
		else if ( $rand <= $randData['pro4'] ) {
			$rp = 4;
		}
		else if ( $rand <= $randData['pro5'] ) {
			$rp = 5;
		}
		else if ( $rand <= $randData['pro6'] ) {
			$rp = 6;
		}
		else if ( $rand <= $randData['pro7'] ) {
			$rp = 7;
		}
		else if ( $rand <= $randData['pro8'] ) {
			$rp = 8;
		}
		else if ( $rand <= $randData['pro9'] ) {
			$rp = 9;
		}
		else if ( $rand <= $randData['pro10'] ) {
			$rp = 10;
		}
		return $rp;
	}

	/**
	 * 获取新佣兵各属性品质
	 * @param int $rp,佣兵星级
	 * @return array
	 */
	public static function getRandQuality($rp, $job, $type = 0, $locks = array(), $userMercenary = array())
	{
		//根据星级随机各属性品质
		$qualityInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryQuality($rp, $type);
		
		$fieldAry = array('1' => 'q_hp',
						  '2' => 'q_mp',
						  '3' => 'q_phy_att',
						  '4' => 'q_phy_def',
						  '5' => 'q_mag_att',
						  '6' => 'q_mag_def',
						  '7' => 'q_agility',
						  '8' => 'q_crit',
						  '9' => 'q_dodge',
						  '10' => 'q_hit',
						  '11' => 'q_tou');

		$list = array();
		$qualitySum = 0;
		foreach ( $fieldAry as $key => $field ) {
			$randNum = rand(1, 100);
			if ( $randNum <= $qualityInfo['quality_1'] ) {
				$type = 1;
			}
			else if ( $randNum <= $qualityInfo['quality_2'] ) {
				$type = 2;
			}
			else if ( $randNum <= $qualityInfo['quality_3'] ) {
				$type = 3;
			}
			else if ( $randNum <= $qualityInfo['quality_4'] ) {
				$type = 4;
			}
			else {
				$type = 5;
			}
			
			//已锁定的属性，则保持不变
			if ( in_array($key, $locks) ) {
				$list[$field] = $userMercenary[$field];
			}
			else {
				$list[$field] = $type;
			}
			
			$qualitySum += $list[$field];
		}
		
		//佣兵 属性对照表列表
		$proConList = Hapyfish2_Alchemy_Cache_Basic::getMercenaryProContrast($job);
		$strInfo = $proConList[1];
		$dexInfo = $proConList[2];
		$magInfo = $proConList[3];
		$phyInfo = $proConList[4];
		
		$strNum = $dexNum = $magNum = $phyNum = 0;
		foreach ( $list as $k => $v ) {
			$fil = substr($k, 2);
			
			$strNum += $strInfo[$fil] / $strInfo['sum'] * $v;
			$dexNum += $dexInfo[$fil] / $dexInfo['sum'] * $v;
			$magNum += $magInfo[$fil] / $magInfo['sum'] * $v;
			$phyNum += $phyInfo[$fil] / $phyInfo['sum'] * $v;
		}

		//根据属性公式计算的数值
		$list['q_str'] = self::_getQualityIdByNum($strNum);
		$list['q_dex'] = self::_getQualityIdByNum($dexNum);
		$list['q_mag'] = self::_getQualityIdByNum($magNum);
		$list['q_phy'] = self::_getQualityIdByNum($phyNum);

		//佣兵整体品质，根据佣兵11个属性的品质计算得出
		switch ( $qualitySum ) {
			case $qualitySum <= 19 :
				$qRole = 1;
				break;
			case $qualitySum <= 29 :
				$qRole = 2;
				break;
			case $qualitySum <= 39 :
				$qRole = 3;
				break;
			case $qualitySum <= 49 :
				$qRole = 4;
				break;
			case $qualitySum <= 55 :
				$qRole = 5;
				break;
			default:
				$qRole = 1;
				break;
		}
		$list['q_role'] = $qRole;
		
		return $list;
	}
		
	/**
	 * 对照属性品质名称，返回字符串
	 * @param int $id
	 * @return varchar
	 */
	private static function _getQualityType($id)
	{
		switch ( $id ) {
			case 1 :
				$type = 'D';
				break;
			case 2 :
				$type = 'C';
				break;
			case 3 :
				$type = 'B';
				break;
			case 4 :
				$type = 'A';
				break;
			case 5 :
				$type = 'S';
				break;
		}
		return $type;
	}

	/**
	 * 根据4个主属性的品质值，计算品质类型
	 * @param int $num
	 * @return int
	 */
	private static function _getQualityIdByNum($num)
	{

		switch ( $num ) {
			case $num < 1.8 :
				$id = 1;
				break;
			case $num < 2.6 :
				$id = 2;
				break;
			case $num < 3.4 :
				$id = 3;
				break;
			case $num < 4.2 :
				$id = 4;
				break;
			case $num <= 5.0 :
				$id = 5;
				break;
			default:
				$id = 1;
				break;
		}
		return $id;
	}
	
	/**
	 * 获取新佣兵各属性数值
	 * @param int $job,星级
	 * @param array $qualityList,各属性品质
	 */
	public static function getNewHireProperty($job, $rp, $qualityList)
	{
		$propertyList = array();
		//佣兵模型信息
		$modelInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryInfoByJob($job);
		//成长信息
		$growInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryGrowInfoByJob($job);
		
		$propertyList['hp'] = self::_getProperty($modelInfo['hp'], $growInfo['hp'], $rp, $qualityList['q_hp']);
		$propertyList['mp'] = self::_getProperty($modelInfo['mp'], $growInfo['mp'], $rp, $qualityList['q_mp']);
		$propertyList['phy_att'] = self::_getProperty($modelInfo['phy_att'], $growInfo['phy_att'], $rp, $qualityList['q_phy_att']);
		$propertyList['phy_def'] = self::_getProperty($modelInfo['phy_def'], $growInfo['phy_def'], $rp, $qualityList['q_phy_def']);
		$propertyList['mag_att'] = self::_getProperty($modelInfo['mag_att'], $growInfo['mag_att'], $rp, $qualityList['q_mag_att']);
		$propertyList['mag_def'] = self::_getProperty($modelInfo['mag_def'], $growInfo['mag_def'], $rp, $qualityList['q_mag_def']);
		$propertyList['agility'] = self::_getProperty($modelInfo['agility'], $growInfo['agility'], $rp, $qualityList['q_agility']);
		$propertyList['crit'] = self::_getProperty($modelInfo['crit'], $growInfo['crit'], $rp, $qualityList['q_crit']);
		$propertyList['dodge'] = self::_getProperty($modelInfo['dodge'], $growInfo['dodge'], $rp, $qualityList['q_dodge']);
		$propertyList['hit'] = self::_getProperty($modelInfo['hit'], $growInfo['hit'], $rp, $qualityList['q_hit']);
		$propertyList['tou'] = self::_getProperty($modelInfo['tou'], $growInfo['tou'], $rp, $qualityList['q_tou']);
		$propertyList['str'] = self::_getProperty($modelInfo['str'], $growInfo['str'], $rp, $qualityList['q_str']);
		$propertyList['dex'] = self::_getProperty($modelInfo['dex'], $growInfo['dex'], $rp, $qualityList['q_dex']);
		$propertyList['mag'] = self::_getProperty($modelInfo['mag'], $growInfo['mag'], $rp, $qualityList['q_mag']);
		$propertyList['phy'] = self::_getProperty($modelInfo['phy'], $growInfo['phy'], $rp, $qualityList['q_phy']);
		
		return $propertyList;
	}
	
	/**
	 * 计算新佣兵各属性初始值
	 * @param int $model，职业模型初始值
	 * @param int $grow，职业成长初始值
	 * @param int $rp，佣兵星级
	 * @param int $quality，属性品质：S=5，A=4，B=3，C=2，D=1
	 * @param int $level，佣兵当前等级
	 * @return int
	 */
	public static function _getProperty($model, $grow, $rp, $quality, $level = 1)
	{
		return round($model + $grow *((( $rp - 1 ) * 5 + $quality)/50))*( $level + 5);
	}
	
	/**
	 * 佣兵更新需要时间
	 * @param int $level,酒馆等级
	 * @param int $hireType,酒馆类型id，1-自宅，2-王城,3-外村
	 */
	public static function getNeedTime($level, $hireType = 1)
	{
		if ( $hireType == 1 ) {
			$tavernId = TAVERN_ID;
		}
		else if ( $hireType == 2 ) {
			$tavernId = TAVERN_CITY_ID;
		}
		$tavernInfo = Hapyfish2_Alchemy_Cache_Basic::getTavernLevel($level, $tavernId);
		
		return $tavernInfo['need_time'];
	}

	public static function array_random($arr, $num = 1) 
	{
	    shuffle($arr);
	    $r = array();
	    for ($i = 0; $i < $num; $i++) {
	        $r[] = $arr[$i];
	    }
	    return $num == 1 ? $r[0] : $r;
	}

	public static function array_random_assoc($arr, $num = 1) 
	{
	    $keys = array_keys($arr);
	    shuffle($keys);

	    $r = array();
	    for ($i = 0; $i < $num; $i++) {
	        $r[$keys[$i]] = $arr[$keys[$i]];
	    }
	    return $r;
	}

	public static function getId($uid)
	{
		$dal = Hapyfish2_Alchemy_Dal_UserSequence::getDefaultInstance();
		return $dal->getId($uid, 'mercenary');
	}
	
	/**
	 * 检查佣兵升级
	 * @param int $uid
	 * @param int $roleId
	 * @param array $userMercenary
	 * @return boolean
	 */
	public static function checkMercenaryLevelUp($uid, $roleId, $userMercenary = null)
	{
        $levelUp = false;
        
		if ( !$userMercenary ) {
			if ( $roleId == 0 ) {
				$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
			}
			else {
				$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $roleId);
			}
		}
		$nowLevel = $userMercenary['level'];
		$nowExp = $userMercenary['exp'];
		
		//根据当前经验，查询最大等级
		$nextLevelInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryNextLevelByExp($nowExp);
		$newLevel = $nextLevelInfo['level'];
		$nextLevelExp = $nextLevelInfo['exp'];
		
		if ( $newLevel <= $nowLevel ) {
			return $levelUp;
		}
		
		if ( $nowExp < $nextLevelExp ) {
			return $levelUp;
		}
		
		$levelUp = true;
		$userMercenary['level'] = $newLevel;

		//佣兵模型信息
		$modelInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryInfoByJob($userMercenary['job']);
		//成长信息
		$growInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryGrowInfoByJob($userMercenary['job']);
		
		$fieldAry = array('0' => 'hp',
						'1' => 'mp',
						'2' => 'phy_att',
						'3' => 'phy_def',
						'4' => 'mag_att',
						'5' => 'mag_def',
						'6' => 'agility',
						'7' => 'crit',
						'8' => 'dodge',
						'9' => 'hit',
						'10' => 'tou',
						'11' => 'str',
						'12' => 'dex',
						'13' => 'mag',
						'14' => 'phy');
		//晋级后，各属性增加
		$growChange = array();
		foreach ( $fieldAry as $field ) {
			$qField = 'q_'.$field;
			
			//升级后数值
			$growChange[$field] = self::_getProperty($modelInfo[$field], $growInfo[$field], $userMercenary['rp'], $userMercenary[$qField], $newLevel);
			//升级前数值，差值
			$growChange[$field] -= self::_getProperty($modelInfo[$field], $growInfo[$field], $userMercenary['rp'], $userMercenary[$qField], $nowLevel);
		}

		//成长信息
		$userMercenary['hp_max'] += $growChange['hp'];
		$userMercenary['hp'] = $userMercenary['hp_max'];
		$userMercenary['mp_max'] += $growChange['mp'];
		$userMercenary['mp'] = $userMercenary['mp_max'];
		$userMercenary['phy_att'] += $growChange['phy_att'];
		$userMercenary['phy_def'] += $growChange['phy_def'];
		$userMercenary['mag_att'] += $growChange['mag_att'];
		$userMercenary['mag_def'] += $growChange['mag_def'];
		$userMercenary['agility'] += $growChange['agility'];
		$userMercenary['crit'] += $growChange['crit'];
		$userMercenary['dodge'] += $growChange['dodge'];
		$userMercenary['hit'] += $growChange['hit'];
		$userMercenary['tou'] += $growChange['tou'];
		$userMercenary['str'] += $growChange['str'];
		$userMercenary['dex'] += $growChange['dex'];
		$userMercenary['mag'] += $growChange['mag'];
		$userMercenary['phy'] += $growChange['phy'];
		
		if ( $roleId == 0 ) {
//			$vip = new Hapyfish2_Alchemy_Bll_Vip();
//			$vip->sendUpAward($uid, $newLevel);
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
			if ( $ok ) {
				//触发任务处理
	            $event = array('uid' => $uid, 'data' => $newLevel);
	            Hapyfish2_Alchemy_Bll_TaskMonitor::fightLevelUp($event);
			}
			if('sinaweibo' == PLATFORM){
				Hapyfish2_Platform_Bll_WeiboAchieve::checkLevelAchieveId($uid, $newLevel);
			}
		}
		else {
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $roleId, $userMercenary, true);
		}
		if (!$ok) {
			return false;
		}
		
		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::removeField($uid, 'rolesChange');
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
			
		return $levelUp;
	}
	
	/**
	 * 好友互动，加酒
	 * @param int $uid
	 * @param int $fid
	 */
	public static function addWine($uid, $fid)
	{
		$isFriend = Hapyfish2_Platform_Bll_Friend::isFriend($uid, $fid);
		if ( !$isFriend ) {
			return -245;
		}
		$botUid = explode(',',BOTFRIEND);
		if(in_array($fid, $botUid)){
			$botWine = Hapyfish2_Alchemy_HFC_Hire::getBotWine($uid);
			if(in_array($fid, $botWine['list'])){
				return -247;
			}
			array_push($botWine['list'], $fid);
			$newCount = count($botWine['list']);
			$ok = Hapyfish2_Alchemy_HFC_Hire::updateBotWine($uid, $botWine);
		}else{
			$friendWine = Hapyfish2_Alchemy_HFC_Hire::getWine($fid);
			//数据结构，$data = array('list'=>array(10018,10019),'used'=>0);
			
			if ( count($friendWine['list']) >= 3 ) {
				return -246;
			}
			
			if ( in_array($uid, $friendWine['list']) ) {
				return -247;
			}
			//开始加酒
			array_push($friendWine['list'], $uid);
			$newCount = count($friendWine['list']);
			$ok = Hapyfish2_Alchemy_HFC_Hire::updateWine($fid, $friendWine);
		}
		if (!$ok) {
			return -200;
		}
		
		//发放奖励
		$addCoin = 50;
		$addExp = 1;
		if ( $addCoin > 0 ) {
			Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $addCoin);		
		}
		if ( $addExp > 0 ) {
			Hapyfish2_Alchemy_HFC_User::incUserExp($uid, $addExp);		
		}
		
		//日志消息
		$nowTime = time();
		$minifeed1 = array('uid' => $uid,
                          'template_id' => 4,
                          'actor' => $uid,
                          'target' => $fid,
                          'title' => array('coin'=>$addCoin, 'exp'=>$addExp),
                          'type' => 1,
                          'create_time' => $nowTime);
		Hapyfish2_Alchemy_Bll_Feed::insertMiniFeed($minifeed1);
		if ( $newCount >= 3 ) {
			$minifeed2 = array('uid' => $fid,
	                          'template_id' => 6,
	                          'actor' => $uid,
	                          'target' => $fid,
	                          'title' => array(),
	                          'type' => 1,
	                          'create_time' => $nowTime);
			Hapyfish2_Alchemy_Bll_Feed::insertMiniFeed($minifeed2);
		}
		else {
			$num = 3 - $newCount;
			$minifeed3 = array('uid' => $fid,
	                          'template_id' => 5,
	                          'actor' => $uid,
	                          'target' => $fid,
	                          'title' => array('num' => $num),
	                          'type' => 1,
	                          'create_time' => $nowTime);
			Hapyfish2_Alchemy_Bll_Feed::insertMiniFeed($minifeed3);
		}
		
		return 1;
	}
	
	/**
	 * 培养：生成培养数据
	 * @param int $uid
	 * @param int $id,佣兵id
	 * @param int $type,培养花费类型
	 */
	public static function strengthenStart($uid, $id, $type)
	{
		if ( $id == 0 ) {
			//主角信息
        	$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		}
		else {
			//用户佣兵信息
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
		}

		if (!$userMercenary) {
			return -222;
		}

		//玩家是否第一次佣兵培养
		$isFirstStr = Hapyfish2_Alchemy_Cache_User::isFirstStrengthen($uid);
		
		$needCoin = 0;
		$needGem = 0;
		
		if ( $isFirstStr != 'Y' ) {
			if ( $type == 2 ) {
				$needGem = 2;
			}
			else if ( $type == 3 ) {
				$needGem = 10;
			}
			else if ( $type == 4 ) {
				$bllVip = new Hapyfish2_Alchemy_Bll_Vip();
				$userVipInfo = $bllVip->getVipInfo($uid);
				if ( $userVipInfo['level'] < 1 || $userVipInfo['vipStatus'] == 0) {
					return -269;
				}
				$needGem = 50;
			}
			else {
				$needCoin = 10*$userMercenary['level']*$userMercenary['level'];
			}
		
			if ( $needCoin > 0 ) {
				$userCoin =	Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
				if ( $userCoin < $needCoin )	{
					return -207;
				}
			}
			if ( $needGem > 0 ) {
				$userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
				if ( $userGem < $needGem ) {
					return -206;
				}
			}
		}
		else {
			Hapyfish2_Alchemy_Cache_User::setFirstStrengthen($uid, 'N');
		}
		
		//获取培养随机数据
		$strengthen = self::getStrengthen($userMercenary['job'], $userMercenary['level'], $type);
		
		$userStrthen = array();
		$userStrthen[$id] = $strengthen;
		$ok = Hapyfish2_Alchemy_HFC_Strengthen::update($uid, $userStrthen);
		if ( !$ok ) {
			return -200;
		}
		
		if ( $needCoin > 0 ) {
			Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $needCoin);
		}
		if ( $needGem > 0 ) {
			$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
			//扣除用户宝石
			$gemInfo = array(
	        		'uid' => $uid,
	        		'cost' => $needGem,
	        		'summary' => LANG_PLATFORM_BASE_TXT_6,
	        		'user_level' => $userLevel,
	        		'cid' => $id,
	        		'num' => 1
	        	);
			Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo,7);
		}
		
		$roleVo = array('id' => $id,
						'sStr' => $strengthen['s_str'],
                    	'sDex' => $strengthen['s_dex'],
                    	'sMag' => $strengthen['s_mag'],
                    	'sPhy' => $strengthen['s_phy']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'roleVo', $roleVo);
		//触发任务
		$event = array('uid' => $uid, 'data' => 1);
		Hapyfish2_Alchemy_Bll_TaskMonitor::strengthenStart($event);
		//report log,统计玩家佣兵培养信息-培养次数
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('305', array($uid, $needCoin, $needGem, $userMercenary['level']));
		
		return 1;
	}
	
	/**
	 * 保存培养数据
	 * @param $uid
	 * @param $id
	 */
	public static function strengthenComplete($uid, $id)
	{
		if ( $id == 0 ) {
			//主角信息
        	$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		}
		else {
			//用户佣兵信息
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
		}

		if (!$userMercenary) {
			return -222;
		}
		
		$userStrthen = Hapyfish2_Alchemy_HFC_Strengthen::get($uid);
		if ( !isset($userStrthen[$id]) ) {
			return -200;
		}

		//开始保存
		//计算此次变化值
		$strChg = $userStrthen[$id]['s_str'] - $userMercenary['s_str'];
		$dexChg = $userStrthen[$id]['s_dex'] - $userMercenary['s_dex'];
		$magChg = $userStrthen[$id]['s_mag'] - $userMercenary['s_mag'];
		$phyChg = $userStrthen[$id]['s_phy'] - $userMercenary['s_phy'];
		
		//新的培养属性
		$userMercenary['s_str'] = $userStrthen[$id]['s_str'];
		$userMercenary['s_dex'] = $userStrthen[$id]['s_dex'];
		$userMercenary['s_mag'] = $userStrthen[$id]['s_mag'];
		$userMercenary['s_phy'] = $userStrthen[$id]['s_phy'];

		//佣兵 属性对照表列表
		$proConList = Hapyfish2_Alchemy_Cache_Basic::getMercenaryProContrast($userMercenary['job']);
		$strInfo = $proConList[1];
		$dexInfo = $proConList[2];
		$magInfo = $proConList[3];
		$phyInfo = $proConList[4];
		
		$paChg = $strChg*$strInfo['phy_att'] + $dexChg*$dexInfo['phy_att'] + $magChg*$magInfo['phy_att'] + $phyChg*$phyInfo['phy_att'];
		$pdChg = $strChg*$strInfo['phy_def'] + $dexChg*$dexInfo['phy_def'] + $magChg*$magInfo['phy_def'] + $phyChg*$phyInfo['phy_def'];
		$maChg = $strChg*$strInfo['mag_att'] + $dexChg*$dexInfo['mag_att'] + $magChg*$magInfo['mag_att'] + $phyChg*$phyInfo['mag_att'];
		$mdChg = $strChg*$strInfo['mag_def'] + $dexChg*$dexInfo['mag_def'] + $magChg*$magInfo['mag_def'] + $phyChg*$phyInfo['mag_def'];
		$agilityChg = $strChg*$strInfo['agility'] + $dexChg*$dexInfo['agility'] + $magChg*$magInfo['agility'] + $phyChg*$phyInfo['agility'];
		$critChg = $strChg*$strInfo['crit'] + $dexChg*$dexInfo['crit'] + $magChg*$magInfo['crit'] + $phyChg*$phyInfo['crit'];
		$dodgeChg = $strChg*$strInfo['dodge'] + $dexChg*$dexInfo['dodge'] + $magChg*$magInfo['dodge'] + $phyChg*$phyInfo['dodge'];
		$hitChg = $strChg*$strInfo['hit'] + $dexChg*$dexInfo['hit'] + $magChg*$magInfo['hit'] + $phyChg*$phyInfo['hit'];
		$touChg = $strChg*$strInfo['tou'] + $dexChg*$dexInfo['tou'] + $magChg*$magInfo['tou'] + $phyChg*$phyInfo['tou'];
		$hpChg = $strChg*$strInfo['hp_max'] + $dexChg*$dexInfo['hp_max'] + $magChg*$magInfo['hp_max'] + $phyChg*$phyInfo['hp_max'];
		$mpChg = $strChg*$strInfo['mp_max'] + $dexChg*$dexInfo['mp_max'] + $magChg*$magInfo['mp_max'] + $phyChg*$phyInfo['mp_max'];

		//对总属性的更改
		$userMercenary['phy_att'] += round($paChg/100);
		$userMercenary['phy_def'] += round($pdChg/100);
		$userMercenary['mag_att'] += round($maChg/100);
		$userMercenary['mag_def'] += round($mdChg/100);
		$userMercenary['agility'] += round($agilityChg/100);
		$userMercenary['crit'] += round($critChg/100);
		$userMercenary['dodge'] += round($dodgeChg/100);
		$userMercenary['hit'] += round($hitChg/100);
		$userMercenary['tou'] += round($touChg/100);
		$userMercenary['hp_max'] += round($hpChg/100);
		$userMercenary['mp_max'] += round($mpChg/100);
		$userMercenary['hp'] = $userMercenary['hp_max'];
		$userMercenary['mp'] = $userMercenary['mp_max'];
		$userMercenary['str'] += $strChg;
		$userMercenary['dex'] += $dexChg;
		$userMercenary['mag'] += $magChg;
		$userMercenary['phy'] += $phyChg;
		
		if ( $id == 0 ) {
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
		}
		else {
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $id, $userMercenary);
		}
		if (!$ok) {
			return -200;
		}
		
		//重置培养临时数据
		Hapyfish2_Alchemy_HFC_Strengthen::update($uid, array());
		
		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
		
		return 1;
	}
	
	/**
	 * 获取培养随机数据
	 * @param int $job,佣兵职业
	 * @param int $level,佣兵等级
	 * @param int $type,培养付费方式
	 */
	public static function getStrengthen($job, $level, $type)
	{
		//获取对应培养信息
		$strengthen = Hapyfish2_Alchemy_Cache_Basic::getMercenaryStrengthen($job, $level, $type);
		
		/* $sPhyAtt = rand($strengthen['min_phy_att'], $strengthen['max_phy_att']);
		$sPhyDef = rand($strengthen['min_phy_def'], $strengthen['max_phy_def']);
		$sMagAtt = rand($strengthen['min_mag_att'], $strengthen['max_mag_att']);
		$sMagDef = rand($strengthen['min_mag_def'], $strengthen['max_mag_def']);
		$sAgility = rand($strengthen['min_agility'], $strengthen['max_agility']);
		$sCrit = rand($strengthen['min_crit'], 	$strengthen['max_crit']);
		$sDodge = rand($strengthen['min_dodge'], 	$strengthen['max_dodge']);
		$sHit = rand($strengthen['min_hit'], 	$strengthen['max_hit']);
		$sTou = rand($strengthen['min_tou'], 	$strengthen['max_tou']);
		$sHp = rand($strengthen['min_hp'], 	$strengthen['max_hp']);
		$sMp = rand($strengthen['min_mp'], 	$strengthen['max_mp']); 

		$strengthen = array('s_phy_att' => round($sPhyAtt/1000),
				's_phy_def' => round($sPhyDef/1000),
				's_mag_att' => round($sMagAtt/1000),
				's_mag_def' => round($sMagDef/1000),
				's_agility' => round($sAgility/1000),
				's_crit' => round($sCrit/1000),
				's_dodge' => round($sDodge/1000),
				's_hit' => round($sHit/1000),
				's_tou' => round($sTou/1000),
				's_hp' => round($sHp/1000),
				's_mp' => round($sMp/1000));*/
		
		$sStr = rand($strengthen['min_str'], $strengthen['max_str']);
		$sDex = rand($strengthen['min_dex'], $strengthen['max_dex']);
		$sMag = rand($strengthen['min_mag'], $strengthen['max_mag']);
		$sPhy = rand($strengthen['min_phy'], $strengthen['max_phy']);

		$strengthen = array('s_str' => round($sStr/1000),
							's_dex' => round($sDex/1000),
							's_mag' => round($sMag/1000),
							's_phy' => round($sPhy/1000));
		
		return $strengthen;
	}
	
	public static function reloadSgain($uid, $userMercenary, $cid, $type)
	{
		$skillInfo = Hapyfish2_Alchemy_Cache_Basic::getEffectInfo($cid);
		if(!$skillInfo){
			return $userMercenary;
		}
		$skillArr = array(
			'hp_max' => 0,
			'mp_max' => 1,
			'phy_att' => 2,
			'phy_def' => 3,
			'mag_att' => 4,
			'mag_def' => 5,
			'crit' => 6,
			'dodge' => 7,
			'hit' =>8,
			'tou' =>9  
		);
		$effect = json_decode($skillInfo['effect'], true);
		$eftype = $effect[0]['type'];
		if($type == 1){
			$userMercenary[$eftype] -= $userMercenary['skill_gain'][$skillArr[$eftype]];
			$userMercenary['skill_gain'][$skillArr[$eftype]] = 0;
		}else{
			$add = floor($userMercenary[$eftype]*$effect[0]['value']);
			 $userMercenary[$eftype] += $add;
			 $userMercenary['skill_gain'][$skillArr[$eftype]] = $add;
		}
		return $userMercenary;
	}
}