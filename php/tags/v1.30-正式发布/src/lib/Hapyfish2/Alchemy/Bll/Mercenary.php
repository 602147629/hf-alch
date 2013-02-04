<?php

class Hapyfish2_Alchemy_Bll_Mercenary
{
	public static function setHireOpened($uid, $id)
	{
		//所有雇佣位信息
		$hireList = Hapyfish2_Alchemy_HFC_Hire::getAll($uid);
		$hire = $hireList[$id];
		
		if ( $id == 1 ) {
			$mid = 500;
			$gid = 511;
			$nid = 13;
		}
		else if ( $id == 2 ) {
			$mid = 515;
			$gid = 511;
			$nid = 103;
		}
		else if ( $id == 3 ) {
			$mid = 1322;
			$gid = 1321;
			$nid = 73;
		}
		else if ( $id == 4 ) {
			$mid = 1343;
			$gid = 1321;
			$nid = 163;
		}
		
		if ( !$hire['cid'] ) {
			//佣兵模型信息
			$modelInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryInfo($mid);
			//成长信息
			$growInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryGrowInfo($gid);
			//名字信息
			$nameInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryNameInfo($nid);
		
			$modelInfo = array_merge($modelInfo, $nameInfo);
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
				
			$newHire = array();
			$newHire['id'] = $id;
			$newHire['type'] = $hire['type'];
			$newHire['level'] = $hire['level'];
			$newHire['cid'] = $modelInfo['cid'];
			$newHire['gid'] = $growInfo['id'];
			$newHire['rp'] = $modelInfo['rp'];
			$newHire['job'] = $growInfo['job'];
			$newHire['element'] = rand(1,3);
			$newHire['name'] = $modelInfo['name'];
			$newHire['content'] = $modelInfo['content'];
			$newHire['sex'] = $modelInfo['sex'];
			$newHire['class_name'] = $modelInfo['class_name'];
			$newHire['face_class_name'] = $modelInfo['face_class_name'];
			$newHire['s_face_class_name'] = $modelInfo['s_face_class_name'];
			$newHire['scene_player_class'] = $modelInfo['scene_player_class'];
			$newHire['start_time'] = 0;
			$newHire['skill'] = json_decode($growInfo['skill']);
			
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
			if ( $hire['cid'] > 0 ) {
				$mercenary = Hapyfish2_Alchemy_Cache_Basic::getMercenaryInfo($hire['cid']);
				$role = $mercenary;
			}
			else {
				$role = array();
			}

			if ( $hire['id'] < 3 ) {
				if ( $hire['cid'] ) {
					$hireVo = self::getHireVo($hire);
				}
				else {
					$hireVo = self::setHireOpened($uid, $hire['id']);
				}
				$list[0][] = $hireVo;
			}
			else if ( $hire['id'] < 5 ) {
				if ( $hire['cid'] ) {
					$hireVo = self::getHireVo($hire);
				}
				else {
					//$hireVo = array();
					$hireVo = self::setHireOpened($uid, $hire['id']);
				}
				$list[1][] = $hireVo;
			}
			else if ( $hire['id'] < 7 ) {
				if ( $hire['cid'] ) {
					$hireVo = self::getHireVo($hire);
				}
				else {
					//$hireVo = array();
					$hireVo = self::setHireOpened($uid, $hire['id']);
				}
				$list[2][] = $hireVo;
			}
		}
		return array('result' => array('status' => 1),
					 'list' => $list);
	}

	/**
	 * 刷新雇佣（放弃）
	 * @param int $uid
	 * @param int $id,酒馆位置id
	 */
	public static function refreshHire($uid, $id, $npcId)
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
		if ( $hireInfo['remainingTime'] > 0 ) {
			return -220;
		}

		//获取一个新的雇佣佣兵信息
		$newHire = self::getNewHire($uid, $id, $hireInfo);

		$ok = Hapyfish2_Alchemy_HFC_Hire::updateOne($uid, $id, $newHire);
		if (!$ok) {
			return -200;
		}

		$hireVo = self::getHireVo($newHire);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'hireVo', $hireVo);

		//report log,统计玩家雇佣佣兵信息-刷新佣兵次数
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('302', array($uid));
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
			
			//$needCoin = 2000;
			
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
	 * 雇佣酒馆中佣兵
	 * @param uid $uid
	 * @param id $id,酒馆位置id
	 */
	public static function hireMercenary($uid, $id, $npcId)
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
		if ( $hireInfo['remainingTime'] > 0 ) {
			return -220;
		}

		//用户当前佣兵列表
		$userMercenaryList = Hapyfish2_Alchemy_HFC_FightMercenary::getAll($uid);
		//用户当前最大佣兵数
		$userMaxCount = Hapyfish2_Alchemy_HFC_User::getUserMaxMercenaryCount($uid);
		
		if ( count($userMercenaryList) >= ($userMaxCount - 1) ) {
			return -221;
		}

		//佣兵模型信息
		$modelInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryInfo($hireInfo['cid']);
		//成长信息
		$growInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryGrowInfo($hireInfo['gid']);

		$userCoin = Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
		$needCoin = $growInfo['coin'];
		if ( $userCoin < $needCoin ) {
			return -207;
		}
		
		//神勇点
		$needFeats = $growInfo['feats'];
		if ( $needFeats > 0 ) {
			$featsCid = FEATS_CID;
			$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
			$userFeats = isset($userGoods[$featsCid]['count']) ? $userGoods[$featsCid]['count'] : 0;
			if ( $userFeats < $needFeats ) {
				return -253;
			}
		}

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
            'hp' => $modelInfo['hp'],
            'hp_max' => $modelInfo['hp'],
            'mp' => $modelInfo['mp'],
            'mp_max' => $modelInfo['mp'],
            'phy_att' => $modelInfo['phy_att'],
            'phy_def' => $modelInfo['phy_def'],
            'mag_att' => $modelInfo['mag_att'],
            'mag_def' => $modelInfo['mag_def'],
            'agility' => $modelInfo['agility'],
            'crit' => $modelInfo['crit'],
            'dodge' => $modelInfo['dodge'],
            'skill' => $hireInfo['skill'],
            'weapon' => array()
	    );
	
		//更新雇佣位
		$newHire = self::getNewHire($uid, $id, $hireInfo);
		$ok = Hapyfish2_Alchemy_HFC_Hire::updateOne($uid, $id, $newHire);
		if (!$ok) {
			return -200;
		}
		
		$ok = Hapyfish2_Alchemy_HFC_FightMercenary::addOne($uid, $info);
		if (!$ok) {
			return -200;
		}
		
		//扣除金币
		Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $needCoin);
		
		//扣除神勇点
		if ( $needFeats > 0 ) {
			Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $featsCid, $needFeats);
		}
		
		$hireVo = self::getHireVo($newHire);
		
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'hireVo', $hireVo);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'roleId', $newId);
        
		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
		
		//触发任务处理
    	//$event = array('uid' => $uid, 'data' => array(2,1,9))   job,element,star
        $event = array('uid' => $uid, 'data' => array($newHire['job'], $newHire['element'], $newHire['rp']));
    	Hapyfish2_Alchemy_Bll_TaskMonitor::hireMercenary($event);
    	
		$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
        $selfMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		//report log,统计玩家雇佣佣兵信息
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('301', array($uid, $userLevel, $selfMercenary['level'], $newHire['rp'], $newHire['job'], $newHire['element'], $needCoin));
    	
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
		
		//解雇返还经验卷轴
		$returnCount = self::_getReturnExpCnt($userMercenary['rp'], $userMercenary['exp']);
		if ( $returnCount > 0 ) {
			$gameData = Hapyfish2_Alchemy_Bll_BasicInfo::getGameData();
			$returnCid = $gameData['expScrollCid'];
			Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, $returnCid, $returnCount);
		}
		
		$removeRoles = array($id);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeRoles', $removeRoles);

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
			$paChg = $newWeapon['pa'] - $oldWeapon['pa'];
			$pdChg = $newWeapon['pd'] - $oldWeapon['pd'];
			$maChg = $newWeapon['ma'] - $oldWeapon['ma'];
			$mdChg = $newWeapon['md'] - $oldWeapon['md'];
			$speedChg = $newWeapon['speed'] - $oldWeapon['speed'];
			$hpChg = $newWeapon['hp'] - $oldWeapon['hp'];
			$mpChg = $newWeapon['mp'] - $oldWeapon['mp'];
			$criChg = $newWeapon['cri'] - $oldWeapon['cri'];
			$dodChg = $newWeapon['dod'] - $oldWeapon['dod'];
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
							'maxHp' => $userMercenary['hp_max'],
							'mp' => $userMercenary['mp'],
							'speed' => $userMercenary['agility'],
							'phyAtk' => $userMercenary['phy_att'],
							'phyDef' => $userMercenary['phy_def'],
							'magAtk' => $userMercenary['mag_att'],
							'magDef' => $userMercenary['mag_def'],
							'baseCrit' => $userMercenary['crit'],
							'baseDodge' => $userMercenary['dodge'],
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
			$paChg -= $userWeapon['pa'];
			$pdChg -= $userWeapon['pd'];
			$maChg -= $userWeapon['ma'];
			$mdChg -= $userWeapon['md'];
			$speedChg -= $userWeapon['speed'];
			$hpChg -= $userWeapon['hp'];
			$mpChg -= $userWeapon['mp'];
			$criChg -= $userWeapon['cri'];
			$dodChg -= $userWeapon['dod'];
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
							'maxHp' => $userMercenary['hp_max'],
							'mp' => $userMercenary['mp'],
							'speed' => $userMercenary['agility'],
							'phyAtk' => $userMercenary['phy_att'],
							'phyDef' => $userMercenary['phy_def'],
							'magAtk' => $userMercenary['mag_att'],
							'magDef' => $userMercenary['mag_def'],
							'baseCrit' => $userMercenary['crit'],
							'baseDodge' => $userMercenary['dodge'],
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
			if ( !in_array($idx, array(1,2,3,4)) ) {
				return -200;
			}
			//主角信息
        	$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		}
		else {
			if ( !in_array($idx, array(1,2,3,4)) ) {
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

		$scrollJobs = explode(',', $scrollInfo['jobs']);
		if ( !in_array($userMercenary['job'], $scrollJobs) ) {
			return -228;
		}

		$scrollElements = explode(',', $scrollInfo['element']);
		if ( !in_array($userMercenary['element'], $scrollElements) ) {
			return -228;
		}
		
		$userScroll	= Hapyfish2_Alchemy_HFC_Scroll::getUserScroll($uid);
		if ( $userScroll[$cid]['count']	< 1 ) {
			return -229;
		}
		
		if ( $userMercenary['level'] < $scrollInfo['need_level'] ) {
			return -254;
		}
		
		$skill = $userMercenary['skill'];
		if (empty($skill)) {
			$skill = array(0,-1,-1,-1);
		}
		$field = $idx - 1;
		if ( $idx == 1 && $skill[0] != 0 ) {
			return -200;
		}
		if ( $skill[$field] == -1 ) {
			return -230;
		}
		if ( $skill[$field] == $cid ) {
			return -242;
		}
		
		$skill[$field] = $cid;
		$userMercenary['skill'] = $skill;

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
        
		return 1;
	}

	/**
	 * 解锁技能位
	 * @param int $uid
	 * @param int $id,佣兵实例id
	 * @param int $idx,技能位置：1,2,3,4
	 */
	public static function unlockSkill($uid, $id, $idx)
	{
		if ( !in_array($idx, array(1,2,3,4)) ) {
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
			$skill = array(0,-1,-1,-1);
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
		if ( $idx == 2 ) {
			$needLevel = 5;
			$needCoin = 500;
		}
		else if ( $idx == 3 ) {
			$needLevel = 15;
			$needCoin = 5000;
		}
		else if ( $idx == 4 ) {
			$needGem = 10;
		}

		//fortest
		/*if ( $needLevel > $userMercenary['level'] ) {
			return -234;
		}*/

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

        //mecenarycorps fight info
        $mecenaryList = Hapyfish2_Alchemy_HFC_FightMercenary::getAll($uid);
		foreach ( $map as $k=>$v ) {
			if ( $k < 9 || $k > 17 ) {
				return -233;
			}

			if ( $v != 0) {
				if ( !isset($mecenaryList[$v]) ) {
					return -233;
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
				$needNum += $v['hp_max'] - $v['hp'];
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
			$needNum += $selfInfo['hp_max'] - $selfInfo['hp'];
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
        $nextLevel = $userMercenary['rp'] + 1;
        if ( $nextLevel > 10 ) {
        	return -200;
        }
        
		//下一级信息
		$levelInfo = Hapyfish2_Alchemy_Cache_Basic::getRoleLevel($nextLevel);
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
		//已升级别
		$upLevel = $userMercenary['level'] - 1;
		//新成长曲线及数值
		$newGrowId = self::getNewGrowForRoleUpgrade($nextLevel, $userMercenary['job']);
		$newGrowBasic = Hapyfish2_Alchemy_Cache_Basic::getMercenaryGrowInfo($newGrowId);
		$growChange = array();
		$growChange['hp'] = $newGrowBasic['hp'] * $upLevel;
		$growChange['mp'] = $newGrowBasic['mp'] * $upLevel;
		$growChange['phy_att'] = $newGrowBasic['phy_att'] * $upLevel;
		$growChange['phy_def'] = $newGrowBasic['phy_def'] * $upLevel;
		$growChange['mag_att'] = $newGrowBasic['mag_att'] * $upLevel;
		$growChange['mag_def'] = $newGrowBasic['mag_def'] * $upLevel;
		$growChange['agility'] = $newGrowBasic['agility'] * $upLevel;
		$growChange['crit'] = $newGrowBasic['crit'] * $upLevel;
		$growChange['dodge'] = $newGrowBasic['dodge'] * $upLevel;
		
		//当前成长曲线
		$nowGrowBasic = Hapyfish2_Alchemy_Cache_Basic::getMercenaryGrowInfo($userMercenary['gid']);
		$growChange['hp'] -= $nowGrowBasic['hp'] * $upLevel;
		$growChange['mp'] -= $nowGrowBasic['mp'] * $upLevel;
		$growChange['phy_att'] -= $nowGrowBasic['phy_att'] * $upLevel;
		$growChange['phy_def'] -= $nowGrowBasic['phy_def'] * $upLevel;
		$growChange['mag_att'] -= $nowGrowBasic['mag_att'] * $upLevel;
		$growChange['mag_def'] -= $nowGrowBasic['mag_def'] * $upLevel;
		$growChange['agility'] -= $nowGrowBasic['agility'] * $upLevel;
		$growChange['crit'] -= $nowGrowBasic['crit'] * $upLevel;
		$growChange['dodge'] -= $nowGrowBasic['dodge'] * $upLevel;
		
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
		
		$userMercenary['rp'] = $nextLevel;
		$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
		if (!$ok) {
			return -200;
		}
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'quality', $nextLevel);
		
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
		
		//platform feed
		Hapyfish2_Alchemy_Bll_PlatformFeed_Kaixin::userRolePromotion($uid, $nextLevel);
		
		return 1;
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
	 */
	public static function getNewHire($uid, $id, $hire)
	{
		$maxCount = 21;
	
		$rp = self::getRandRp($hire['level']);

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

		//随机成长曲线
		$growList = Hapyfish2_Alchemy_Cache_Basic::getMercenaryGrowByRp($rp);
		//根据酒馆类型（自宅，村庄，王城） 过滤
		foreach ( $growList as $kGrow => $vGrow ) {
			$growHireType = explode(',', $vGrow['hire_type']);
			if ( !in_array($hire['type'], $growHireType) ) {
				unset($growList[$kGrow]);
			}
		}
		$growInfo = self::array_random($growList);
		
		//根据rp获取所有模型列表
		$modelAllList =  Hapyfish2_Alchemy_Cache_Basic::getMercenaryModelByRp($rp, $growInfo['job']);
		$modelInfo = self::array_random($modelAllList);

		$modelInfo['name_id'] = 0;
		//随机名字，指定名字
		if ( $modelInfo['name_id'] == 0 ) {
			$modelInfo = self::getRandModelWithName($modelInfo, $userNameList, $maxCount, $growInfo['job'], $hire);
		}
		else {
			//根据rp获取21个固定名字的模型
			$modelList = Hapyfish2_Alchemy_Cache_Basic::getMercenaryModelByRp($rp, $growInfo['job'], $maxCount);
			$modelNameList = array();
			foreach ( $modelList as $model ) {
				$modelNameInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryNameInfo($model['name_id']);
				$modelNameList[$model['cid']] = $modelNameInfo['name'];
			}

			$tempNameList = array_diff($modelNameList, $userNameList);
			if (!empty($tempNameList)) {
				$cid = array_rand($tempNameList);
				$modelInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryInfo($cid);
				$nameInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryNameInfo($modelInfo['name_id']);
				$modelInfo = array_merge($modelInfo, $nameInfo);
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
			}
			else {
				$modelInfo = self::getRandModelWithName($modelInfo, $userNameList, $maxCount, $growInfo['job'], $hire);
			}
		}

		//元素属性，风火水
		$element = rand(1, 3);
		
		//随机获得初始技能
		$skill = self::getRandSkill($growInfo['skill'], $hire['type'], $hire['level'], $rp, $growInfo['job'], $element);

		$hire['id'] = $id;
		$hire['type'] = $hire['type'];
		$hire['level'] = $hire['level'];
		$hire['cid'] = $modelInfo['cid'];
		$hire['gid'] = $growInfo['id'];
		$hire['rp'] = $modelInfo['rp'];
		$hire['job'] = $growInfo['job'];
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
		return $hire;
	}

	/**
	 * 获取佣兵初始技能（随机）
	 * 
	 * @param array $skill,默认技能列表“[0,-1,-1,-1]”
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
			if ( $scroll['hire_type'] != $hireType && $scroll['hire_type'] != '1,2,3' ) {
				unset($scrollList[$k]);
			}
			if ( $scroll['hire_level'] > $hireLevel ) {
				unset($scrollList[$k]);
			}
			if ( $scroll['rp'] > $rp ) {
				unset($scrollList[$k]);
			}
			if ( $scroll['jobs'] != $job && $scroll['jobs'] != '1,2,3' ) {
				unset($scrollList[$k]);
			}
			if ( $scroll['element'] != $element ) {
				unset($scrollList[$k]);
			}
		}
		
		//根据各技能权重随机获取
		$randArray = array();
		foreach ( $scrollList as $v ) {
			for ( $i=0; $i<$v['probability'];$i++ ) {
				$randArray[] = $v['cid'];
			}
		}
		$rand = array_rand($randArray);
		$randSkill = $randArray[$rand];
		
		if ( !$randSkill ) {
			$randSkill = 5122;
		}
		$skill[0] = $randSkill;
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
		//模型信息
		$modelInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryInfo($hire['cid']);
		//成长信息
		$growInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryGrowInfo($hire['gid']);

		if (isset($hire['start_time'])) {
			$needTime = self::getNeedTime($hire['level']);
			$remainTime = ($hire['start_time'] + $needTime) - time();
			$remainTime = $remainTime < 0 ? 0 : $remainTime;
		}
		else {
			$remainTime = $hire['remainingTime'];
		}

		$startTime = $hire['time'];
		$hireVo = array('id' => $hire['id'],
						'price' => $growInfo['coin'],
						'brave' => $growInfo['feats'],
						'remainingTime' => $remainTime,
						'dialog' => $hire['content'],
						'name' => $hire['name'],
						'prop' => $hire['element'],
						'className' => $hire['class_name'],
						'faceClass' => $hire['face_class_name'],
						'sFaceClass' => $hire['s_face_class_name'],
						'profession' => $hire['job'],
						'hp' => $modelInfo['hp'],
						'maxHp' => $modelInfo['hp'],
						'mp' => $modelInfo['mp'],
						'maxMp' => $modelInfo['mp'],
						'speed' => $modelInfo['agility'],
						'phyAtk' => $modelInfo['phy_att'],
						'phyDef' => $modelInfo['phy_def'],
						'magAtk' => $modelInfo['mag_att'],
						'magDef' => $modelInfo['mag_def'],
						'baseCrit' => $modelInfo['crit'],
						'baseDodge' => $modelInfo['dodge'],
						'skills' => json_encode($hire['skill']),
						'quality' => $hire['rp'],
						'level' => 1,
						'exp' => 0,
						'maxExp' => 9);
		
		$CompareAry = array('hp' => 'growHp',
						    'mp' => 'growMp',
						    'phy_att' => 'growPhyAtk',
						    'phy_def' => 'growPhyDef',
						    'mag_att' => 'growMagAtk',
						    'mag_def' => 'growMagDef',
						    'agility' => 'growSpeed');
		//模板数值
		$growClass = Hapyfish2_Alchemy_Cache_Basic::getMercenaryGrowClass($hire['rp'], $hire['job']);
		foreach ( $CompareAry as $k => $v ) {
			if ( $growInfo[$k] > $growClass[$k] ) {
				$hireVo[$v] = 1;
			}
			else if ( $growInfo[$k] == $growClass[$k] ) {
				$hireVo[$v] = 0;
			}
			else {
				$hireVo[$v] = -1;
			}
		}
		
		return $hireVo;
	}

	/**
	 * 获取新佣兵等级
	 * @param int $uid
	 * @param int $level,酒馆等级
	 */
	public static function getRandRp($level)
	{
		/* $randArray = array(
						'1'  => array('pro1' => 1000, 'pro2' => 0, 'pro3' => 0, 'pro4' => 0, 'pro5' => 0, 'pro6' => 0, 'pro7' => 0, 'pro8' => 0, 'pro9' => 0, 'pro10' => 0),
						'2'  => array('pro1' => 900, 'pro2' => 1000, 'pro3' => 0, 'pro4' => 0, 'pro5' => 0, 'pro6' => 0, 'pro7' => 0, 'pro8' => 0, 'pro9' => 0, 'pro10' => 0),
						'3'  => array('pro1' => 870, 'pro2' => 9800, 'pro3' => 1000, 'pro4' => 0, 'pro5' => 0, 'pro6' => 0, 'pro7' => 0, 'pro8' => 0, 'pro9' => 0, 'pro10' => 0),
						'4'  => array('pro1' => 840, 'pro2' => 960, 'pro3' => 990, 'pro4' => 1000, 'pro5' => 0, 'pro6' => 0, 'pro7' => 0, 'pro8' => 0, 'pro9' => 0, 'pro10' => 0),
						'5'  => array('pro1' => 809, 'pro2' => 939, 'pro3' => 979, 'pro4' => 999, 'pro5' => 1000, 'pro6' => 0, 'pro7' => 0, 'pro8' => 0, 'pro9' => 0, 'pro10' => 0),
						'6'  => array('pro1' => 777, 'pro2' => 917, 'pro3' => 967, 'pro4' => 997, 'pro5' => 999, 'pro6' => 1000, 'pro7' => 0, 'pro8' => 0, 'pro9' => 0, 'pro10' => 0),
						'7'  => array('pro1' => 744, 'pro2' => 894, 'pro3' => 954, 'pro4' => 994, 'pro5' => 997, 'pro6' => 999, 'pro7' => 1000, 'pro8' => 0, 'pro9' => 0, 'pro10' => 0),
						'8'  => array('pro1' => 710, 'pro2' => 870, 'pro3' => 940, 'pro4' => 990, 'pro5' => 994, 'pro6' => 997, 'pro7' => 999, 'pro8' => 1000, 'pro9' => 0, 'pro10' => 0),
						'9'  => array('pro1' => 675, 'pro2' => 845, 'pro3' => 925, 'pro4' => 985, 'pro5' => 990, 'pro6' => 994, 'pro7' => 997, 'pro8' => 999, 'pro9' => 1000, 'pro10' => 0),
						'10' => array('pro1' => 641, 'pro2' => 821, 'pro3' => 911, 'pro4' => 981, 'pro5' => 987, 'pro6' => 992, 'pro7' => 996, 'pro8' => 999, 'pro9' => 1000, 'pro10' => 0)
					); */

		$randArray = Hapyfish2_Alchemy_Cache_Basic::getMercenaryRpRandList();

		$randData = $randArray[$level];
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
	 * 佣兵更新需要时间
	 * @param int $level,酒馆等级
	 */
	public static function getNeedTime($level)
	{
		switch ( $level ) {
			case 1 :
				$need = 24*60*60;
				break;
			case 2 :
				$need = 20*60*60;
				break;
			case 3 :
				$need = 16*60*60;
				break;
			case 4 :
				$need = 12*60*60;
				break;
			case 5 :
				$need = 8*60*60;
				break;
		}
		return $need;
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
		$newLevel = $nowLevel + 1;
		
		$nextLevelExp = Hapyfish2_Alchemy_Cache_Basic::getMercenaryLevelExp($newLevel);
		if (!$nextLevelExp) {
			return $levelUp;
		}

		if ( $nowExp < $nextLevelExp ) {
			return $levelUp;
		}
		
		$levelUp = true;
		$userMercenary['level'] = $newLevel;
		
		//成长信息
		$growInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryGrowInfo($userMercenary['gid']);
		$userMercenary['hp_max'] += $growInfo['hp'];	
		$userMercenary['hp'] = $userMercenary['hp_max'];
		$userMercenary['mp_max'] += $growInfo['mp'];
		$userMercenary['mp'] = $userMercenary['mp_max'];
		$userMercenary['phy_att'] += $growInfo['phy_att'];
		$userMercenary['phy_def'] += $growInfo['phy_def'];
		$userMercenary['mag_att'] += $growInfo['mag_att'];
		$userMercenary['mag_def'] += $growInfo['mag_def'];
		$userMercenary['agility'] += $growInfo['agility'];
		$userMercenary['crit'] += $growInfo['crit'];
		$userMercenary['dodge'] += $growInfo['dodge'];

		if ( $roleId == 0 ) {
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
			if ( $ok ) {
				//触发任务处理
	            $event = array('uid' => $uid, 'data' => $newLevel);
	            Hapyfish2_Alchemy_Bll_TaskMonitor::fightLevelUp($event);
			}
		}
		else {
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $roleId, $userMercenary);
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
		
		//用户加酒信息
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
	 * 强化：生成强化数据
	 * @param int $uid
	 * @param int $id,佣兵id
	 * @param int $type,强化花费类型
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

		$needCoin = 0;
		$needGem = 0;
		if ( $type == 2 ) {
			$needGem = 2;
		}
		else if ( $type == 3 ) {
			$needGem = 10;
		}
		else if ( $type == 4 ) {
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
		
		//获取强化随机数据
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
			Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo);
		}
		
		$roleVo = array('id' => $id,
						'sPhyAtk' => $strengthen['s_phy_att'],
                    	'sPhyDef' => $strengthen['s_phy_def'],
                    	'sMagAtk' => $strengthen['s_mag_att'],
                    	'sMagDef' => $strengthen['s_mag_def'],
                    	'sSpeed'  => $strengthen['s_agility']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'roleVo', $roleVo);
		$event = array('uid' => $uid, 'data' => 1);
		Hapyfish2_Alchemy_Bll_TaskMonitor::strengthenStart($event);
		//report log,统计玩家佣兵培养信息-培养次数
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('305', array($uid, $needCoin, $needGem, $userMercenary['level']));
		
		return 1;
	}
	
	/**
	 * 保存强化数据
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
		$paChg = $userStrthen[$id]['s_phy_att'] - $userMercenary['s_phy_att'];
		$pdChg = $userStrthen[$id]['s_phy_def'] - $userMercenary['s_phy_def'];
		$maChg = $userStrthen[$id]['s_mag_att'] - $userMercenary['s_mag_att'];
		$mdChg = $userStrthen[$id]['s_mag_def'] - $userMercenary['s_mag_def'];
		$agilityChg = $userStrthen[$id]['s_agility'] - $userMercenary['s_agility'];
		$critChg = $userStrthen[$id]['s_crit'] - $userMercenary['s_crit'];
		$dodgeChg = $userStrthen[$id]['s_dodge'] - $userMercenary['s_dodge'];
		
		//新的强化属性
		$userMercenary['s_phy_att'] = $userStrthen[$id]['s_phy_att'];
		$userMercenary['s_phy_def'] = $userStrthen[$id]['s_phy_def'];
		$userMercenary['s_mag_att'] = $userStrthen[$id]['s_mag_att'];
		$userMercenary['s_mag_def'] = $userStrthen[$id]['s_mag_def'];
		$userMercenary['s_agility'] = $userStrthen[$id]['s_agility'];
		$userMercenary['s_crit'] = $userStrthen[$id]['s_crit'];
		$userMercenary['s_dodge'] = $userStrthen[$id]['s_dodge'];
		
		//对总属性的更改
		$userMercenary['phy_att'] += $paChg;
		$userMercenary['phy_def'] += $pdChg;
		$userMercenary['mag_att'] += $maChg;
		$userMercenary['mag_def'] += $mdChg;
		$userMercenary['agility'] += $agilityChg;
		$userMercenary['crit'] += $critChg;
		$userMercenary['dodge'] += $dodgeChg;
		
		if ( $id == 0 ) {
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
		}
		else {
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $id, $userMercenary);
		}
		if (!$ok) {
			return -200;
		}
		
		//重置强化临时数据
		Hapyfish2_Alchemy_HFC_Strengthen::update($uid, array());
		
		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
		
		return 1;
	}
	
	/**
	 * 获取强化随机数据
	 * @param int $job,佣兵职业
	 * @param int $level,佣兵等级
	 * @param int $type,强化付费方式
	 */
	public static function getStrengthen($job, $level, $type)
	{
		//获取对应强化信息
		$strengthen = Hapyfish2_Alchemy_Cache_Basic::getMercenaryStrengthen($job, $level, $type);
		
		$sPhyAtt = rand($strengthen['min_phy_att'], $strengthen['max_phy_att']);
		$sPhyDef = rand($strengthen['min_phy_def'], $strengthen['max_phy_def']);
		$sMagAtt = rand($strengthen['min_mag_att'], $strengthen['max_mag_att']);
		$sMagDef = rand($strengthen['min_mag_def'], $strengthen['max_mag_def']);
		$sAgility = rand($strengthen['min_agility'], $strengthen['max_agility']);
		$sCrit = rand($strengthen['min_crit'], 	$strengthen['max_crit']);
		$sDodge = rand($strengthen['min_dodge'], 	$strengthen['max_dodge']);
		
		$strengthen = array('s_phy_att' => round($sPhyAtt/1000),
							's_phy_def' => round($sPhyDef/1000),
							's_mag_att' => round($sMagAtt/1000),
							's_mag_def' => round($sMagDef/1000),
							's_agility' => round($sAgility/1000),
							's_crit' => round($sCrit/1000),
							's_dodge' => round($sDodge/1000));
		return $strengthen;
	}
}