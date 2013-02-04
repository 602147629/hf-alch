<?php

class Hapyfish2_Alchemy_Bll_Card
{
	/**
	 * 使用道具
	 * @param int $uid
	 * @param int $cid，道具cid
	 */
	public static function useCard($uid, $cid, $roleId, $num)
	{
		$type = substr($cid, -2, 1);
        $smallType = substr($cid, -2, 2);
        if ( $smallType == 17 ) {
            $result = self::mercenaryCard($uid, $cid);
        }
		else if ( $type == 1 ) {
			$cardInfo = Hapyfish2_Alchemy_Cache_Basic::getGoodsInfo($cid);
			if (!$cardInfo) {
				return -200;
			}
			//需要用户等级
	        $userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
	        if ($userLevel < $cardInfo['need_level']) {
	            return -235;
	        }
			//剩余道具数
			$userCard = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
			if (!isset($userCard[$cid]) || $userCard[$cid]['count'] < $num) {
				return -239;
			}

			$result = 1;
			if ( in_array($cid, array(111, 212, 2112, 2212, 2811, 2911)) ) {
				$result = self::hpCard($uid, $cid, $cardInfo, $roleId);
			}
			else if ( $cid == 1611 ) {
				$result = self::mpCard($uid, $cid, $cardInfo, $roleId);
			}
			else if ( in_array($cid, array(515,615,715,815)) ) {
				$result = self::spCard($uid, $cid, $cardInfo);
			}
			else if ( in_array($cid, array(1415,1515)) ) {
				$result = self::expCard($uid, $cid, $cardInfo, $roleId);
			}
			//急救箱
			else if (in_array($cid, array(4415, 6615, 6815, 6915,9215,9515))) {
				$result = self::addBankCard($uid, $cid, 1);
			}
			//魔法药粉
			else if (in_array($cid, array(4515, 6715, 7715, 7815,9315,9415))) {
				$result = self::addBankCard($uid, $cid, 2);
			}
			//援助卷轴
			else if ($cid == 3115) {
                $result = self::assistCard($uid, $cid, $cardInfo);
			}else if($cardInfo['useType'] == 'gift'){
				$result = Hapyfish2_Alchemy_Bll_EventGift::useCard($uid, $cid);
			}
			else if($cardInfo['useType'] == 'vip'){
				$result = self::useVipCard($uid, $cid, $num);
			}
			//遗忘药水-重置主角训练属性
			else if (in_array($cid, array(7015, 7115, 7215, 7315, 7415, 7515, 7615, 8415, 8515, 8615, 8715))) {
				$result = self::resetTrainingCard($uid, $cid, $roleId);
			}
		}
		else if ( $type == 2 ) {
			$result = self::mixScroll($uid, $cid);
		}

		return $result;
	}

	/**
	 * 回血药剂
	 */
	public static function hpCard($uid, $cid, $cardInfo, $roleId)
	{
		if ( $roleId == 0 ) {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		}
		else {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $roleId);
		}
		if (!$userMercenary) {
			return -200;
		}

		if ( $userMercenary['hp'] >= $userMercenary['hp_max'] ) {
			return -238;
		}

		//不同道具，加血量不同
		if ( $cid == 111 ) {
			$addHp = 10;
		}
		else if ( $cid == 212 ) {
			$addHp = 15;
		}
		else if ( $cid == 2112 ) {
			$addHp = 50;
		}
		else if ( $cid == 2212 ) {
			$addHp = 11;
		}
		else if ( $cid == 2811 ) {
			$addHp = 40;
		}
		else if ( $cid == 2911 ) {
			$addHp = 100;
		}

		//不能超过最大值
		if ( ($userMercenary['hp'] + $addHp) > $userMercenary['hp_max'] ) {
			$addHp = $userMercenary['hp_max'] - $userMercenary['hp'];
		}

		$userMercenary['hp'] += $addHp;
		if ( $roleId == 0 ) {
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary);
		}
		else {
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $roleId, $userMercenary);
		}

		if (!$ok) {
			return -200;
		}

		$roleChange = array('id' => $roleId,
							'hp' => $userMercenary['hp']);
		$rolesChange = array($roleChange);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);

		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, 1);

		return 1;
	}

	/**
	 * 回蓝药剂
	 */
	public static function mpCard($uid, $cid, $cardInfo, $roleId)
	{
		if ( $roleId == 0 ) {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		}
		else {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $roleId);
		}
		if (!$userMercenary) {
			return -200;
		}

		if ( $userMercenary['mp'] >= $userMercenary['mp_max'] ) {
			return -238;
		}

		if ( $cid == 1611 ) {
			$addMp = 10;
		}
		else if ( $cid == 1712 ) {
			$addMp = 11;
		}
		else if ( $cid == 1812 ) {
			$addMp = 12;
		}
		else if ( $cid == 1912 ) {
			$addMp = 13;
		}

		if ( ($userMercenary['mp'] + $addMp) > $userMercenary['mp_max'] ) {
			$addMp = $userMercenary['mp_max'] - $userMercenary['mp'];
		}

		$userMercenary['mp'] += $addMp;
		if ( $roleId == 0 ) {
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary);
		}
		else {
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $roleId, $userMercenary);
		}

		if (!$ok) {
			return -200;
		}

		$roleChange = array('id' => $roleId,
							'mp' => $userMercenary['mp']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $roleChange);

		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, 1);

		return 1;
	}

	/**
	 * 行动力药剂
	 */
	public static function spCard($uid, $cid, $cardInfo)
	{
		$maxSp = Hapyfish2_Alchemy_Bll_VipWelfare::getVipMaxSp($uid);
		$userSp = Hapyfish2_Alchemy_HFC_User::getUserSp($uid);
		$useAddSp = Hapyfish2_Alchemy_Cache_Vip::getUserAddSp($uid);
		$useMax = Hapyfish2_Alchemy_Bll_VipWelfare::getVipMaxSpNum($uid);
		if($useAddSp['num'] >= $useMax){
			return -705;
		}
		if ( $userSp['sp'] >= $maxSp ) {
			return -236;
		}

		$addSp = 0;
		if ( $cid == 515 ) {
			$addSp = 2;
		}
		else if ( $cid == 615 ) {
			$addSp = 10;
		}
		else if ( $cid == 715 ) {
			$addSp = 50;
		}
		else if ( $cid == 815 ) {
			$addSp = $maxSp - $userSp['sp'];
		}

		$ok = Hapyfish2_Alchemy_HFC_User::incUserSp($uid, $addSp);
		if (!$ok) {
			return -200;
		}

		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, 1);
		$useAddSp['num'] += 1;
		Hapyfish2_Alchemy_Cache_Vip::updateUserAddSp($uid, $useAddSp);
		$useAddSp = Hapyfish2_Alchemy_Cache_Vip::getUserAddSp($uid);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'addLinePowerNum', $useAddSp['num']);
		
		return 1;
	}

	/**
	 * 经验卷-战斗经验
	 *
	 */
	public static function expCard($uid, $cid, $cardInfo, $roleId)
	{
		if ( $roleId == 0 ) {
			return -351;
			//$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		}
		else {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $roleId);
		}
		if (!$userMercenary) {
			return -200;
		}

		//不同道具，加经验不同
		if ( $cid == 1415 ) {
			$addExp = 10;
		}
		else if ( $cid == 1515 ) {
			$addExp = 200;
		}

		$userMercenary['exp'] += $addExp;
		if ( $roleId == 0 ) {
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary);
		}
		else {
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $roleId, $userMercenary, true);
		}
		if (!$ok) {
			return -200;
		}

		//判断是否升级
		$levelUp = Hapyfish2_Alchemy_Bll_Mercenary::checkMercenaryLevelUp($uid, $roleId, $userMercenary);
		if ( $levelUp ) {

		}

		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, 1);

		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
		
		return 1;
	}

	/**
	 * 援助卷轴
	 */
	public static function assistCard($uid, $cid, $cardInfo)
	{
        $assistInfo = Hapyfish2_Alchemy_HFC_User::getUserFightAssistInfo($uid);
        $assistInfo['assist_ext_count'] += 1;
        Hapyfish2_Alchemy_HFC_User::updateUserFightAssistInfo($uid, $assistInfo);
		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, 1);

		return 1;
	}

	/**
	 * 合成术卷轴
	 * @param int $uid
	 * @param int $cid
	 */
	public static function mixScroll($uid, $cid)
	{
		$scrollInfo = Hapyfish2_Alchemy_Cache_Basic::getScrollInfo($cid);
		if (!$scrollInfo) {
			return -200;
		}
		if ($scrollInfo['type']!= 21) {
			return -241;
		}

		/*$userScroll = Hapyfish2_Alchemy_HFC_Scroll::getUserScroll($uid);
		if ( !isset($userScroll[$cid]) || $userScroll[$cid]['count'] < 1 ) {
			return -200;
		}*/

		$mixCid = $scrollInfo['mix_cid'];
		$userMix = Hapyfish2_Alchemy_HFC_Mix::getUserMix($uid);
		if (in_array($mixCid, $userMix)) {
			return -237;
		}

        $ok = Hapyfish2_Alchemy_HFC_Mix::addUserMix($uid, $mixCid);
		if (!$ok) {
			return -200;
		}
		//Hapyfish2_Alchemy_HFC_Scroll::useUserScroll($uid, $cid, 1);

		$newmixs = array($mixCid);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'newmixs', $newmixs);
		
		return 1;
	}
	
    /**
     * 佣兵卡
     *
     */
    public static function mercenaryCard($uid, $cid)
    {
		$cardInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryCardInfo($cid);
        if (!$cardInfo) {
        	return -200;
        }

        //需要用户等级
        $userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
        if ($userLevel < $cardInfo['need_level']) {
        	return -235;
        }
        //剩余道具数
        $userCard = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
        if (!isset($userCard[$cid]) || $userCard[$cid]['count'] < 1) {
        	return -239;
        }
        
		//用户当前佣兵列表
		$userMercenaryList = Hapyfish2_Alchemy_HFC_FightMercenary::getAll($uid);
		//用户当前最大佣兵数
		$userMaxCount = Hapyfish2_Alchemy_HFC_User::getUserMaxMercenaryCount($uid);
		//佣兵位置已满
		if ( count($userMercenaryList) >= ($userMaxCount - 1) ) {
			return -221;
		}
        
		$mercenaryId = Hapyfish2_Alchemy_Bll_Mercenary::getId($uid);
		$weaponList = json_decode($cardInfo['weapon']);
		$mercenaryWeapon = array();
	    foreach ( $weaponList as $key => $weapon ) {
	    	if ( $weapon > 0 ) {
	    		$mercenaryWeapon[] = Hapyfish2_Alchemy_HFC_Weapon::addMercenaryWeapon($uid, $weapon, $mercenaryId);
	    	}
	    }
	    for ( $i = 0; $i < 4 ;$i++ ) {
	    	if (!isset($mercenaryWeapon[$i])) {
	    		$mercenaryWeapon[$i] = 0;
	    	}
	    }

	    $qualitySum = $cardInfo['q_hp'] + $cardInfo['q_mp'] + $cardInfo['q_phy_att'] + $cardInfo['q_phy_def'] + $cardInfo['q_mag_att'] + $cardInfo['q_mag_def']
	    			+ $cardInfo['q_agility'] + $cardInfo['q_crit'] + $cardInfo['q_dodge'] + $cardInfo['q_hit'] + $cardInfo['q_tou'];
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
	    
		$info = array(
	        'uid' => $uid,
            'mid' => $mercenaryId,
            'cid' => '900'.$cardInfo['cid'],	//900 表示佣兵卡
            'gid' => $cardInfo['gid'],
            'rp' => $cardInfo['rp'],
            'job' => $cardInfo['job'],
            'name' => $cardInfo['m_name'],
            'class_name' => $cardInfo['m_class_name'],
            'face_class_name' => $cardInfo['face_class_name'],
            's_face_class_name' => $cardInfo['s_face_class_name'],
            'scene_player_class' => $cardInfo['scene_player_class'],
            'sex' => $cardInfo['sex'],
            'element' => $cardInfo['element'],
            'exp' => 0,
            'level' => 1,
            'hp' => $cardInfo['hp'],
            'hp_max' => $cardInfo['hp'],
            'mp' => $cardInfo['mp'],
            'mp_max' => $cardInfo['mp'],
            'phy_att' => $cardInfo['phy_att'],
            'phy_def' => $cardInfo['phy_def'],
            'mag_att' => $cardInfo['mag_att'],
            'mag_def' => $cardInfo['mag_def'],
            'agility' => $cardInfo['agility'],
            'crit' => $cardInfo['crit'],
            'dodge' => $cardInfo['dodge'],
			'hit' => $cardInfo['hit'],
			'tou' => $cardInfo['tou'],
			'str' => $cardInfo['str'],
			'dex' => $cardInfo['dex'],
			'mag' => $cardInfo['mag'],
			'phy' => $cardInfo['phy'],
			
			'q_hp' => $cardInfo['q_hp'],
			'q_mp' => $cardInfo['q_mp'],
			'q_phy_att' => $cardInfo['q_phy_att'],
			'q_phy_def' => $cardInfo['q_phy_def'],
			'q_mag_att' => $cardInfo['q_mag_att'],
			'q_mag_def' => $cardInfo['q_mag_def'],
			'q_agility' => $cardInfo['q_agility'],
			'q_crit' => $cardInfo['q_crit'],
			'q_dodge' => $cardInfo['q_dodge'],
			'q_hit' => $cardInfo['q_hit'],
			'q_tou' => $cardInfo['q_tou'],
			'q_str' => $cardInfo['q_str'],
			'q_dex' => $cardInfo['q_dex'],
			'q_mag' => $cardInfo['q_mag'],
			'q_phy' => $cardInfo['q_phy'],
			'q_role' => $qRole,
			
            'skill' => json_decode($cardInfo['skill']),
            'weapon' => $mercenaryWeapon
	    );
		$ok = Hapyfish2_Alchemy_HFC_FightMercenary::addOne($uid, $info);
		if (!$ok) {
			return -200;
		}
		
		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, 1);
		
		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
		
        return 1;
    }
	
	public static function addBankCard($uid, $cid, $type)
	{
		$add = 0;
		$bank = Hapyfish2_Alchemy_HFC_Goods::getUserPond($uid);
		if($type == 1){
			if($cid == 4415){
				$add = 2000;
			}else if($cid == 9515){
				$add = 5000;
			}else if($cid == 6615){
				$add = 20000;
			}else if($cid == 9215){
				$add = 50000;
			}else if($cid == 6815){
				$add = 200000;
			}else if($cid == 6915){
				$add = 500000;
			}
			$bank['hp'] += $add;
		}else{
			if($cid == 4515){
				$add = 2000;
			}else if($cid == 9315){
				$add = 5000;
			}else if($cid == 6715){
				$add = 20000;
			}else if($cid == 9415){
				$add = 50000;
			}else if($cid == 7715){
				$add = 200000;
			}else if($cid == 7815){
				$add = 500000;
			}
			$bank['mp'] += $add;
		}
		Hapyfish2_Alchemy_HFC_Goods::updateUserPond($uid, $bank);
		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, 1);
		$selfInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$selfInfo['hp'] = Hapyfish2_Alchemy_Bll_Fight::autoSupply($uid, $selfInfo, $selfInfo['hp'], 1, 0);
		$selfInfo['mp'] = Hapyfish2_Alchemy_Bll_Fight::autoSupply($uid, $selfInfo, $selfInfo['mp'], 2, 0);
		Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $selfInfo);
		$ids = Hapyfish2_Alchemy_Cache_FightMercenary::getMercenaryIds($uid);
		foreach($ids as $k => $v){
			$mercInfo = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $v);
			if($mercInfo){
				$mercInfo['hp'] = Hapyfish2_Alchemy_Bll_Fight::autoSupply($uid, $mercInfo, $mercInfo['hp'], 1, $v);
        		$mercInfo['mp'] = Hapyfish2_Alchemy_Bll_Fight::autoSupply($uid, $mercInfo, $mercInfo['mp'], 2, $v);
        		Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $mercInfo['mid'], $mercInfo);
			}
		}
        $blood = Hapyfish2_Alchemy_HFC_Goods::getBloodVo($uid);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'bloodBank', $blood);
		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
		return 1;
	}
	
	public static function useVipCard($uid, $cid, $num)
	{
		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, $num);
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		$vip->userVipCard($uid, $cid, $num);
		return 1;
	}

	/**
	 * 遗忘药水-重置佣兵训练营数据
	 *
	 */
	 public static function resetTrainingCard($uid, $cid, $roleId)
	 {
	 	if ( $roleId == 0 ) {
 			$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
	 	}
	 	else {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $roleId);
	 	}
	 	
	 	if (!$userMercenary) {
	 		return -200;
	 	}
	 	
	 	//该佣兵训练信息
	 	$roleTra = Hapyfish2_Alchemy_HFC_Training::getOne($uid, $roleId);
	 	if ( !$roleTra ) {
	 		return -280;
	 	}
	 	
	 	//不同道具，对应属性不同,
	 	switch ( $cid ) {
	 		case 7015 :
    			$type = 1;
    			break;
    		case 7115 :
    			$type = 2;
    			break;
    		case 7215 :
    			$type = 3;
    			break;
    		case 7315 :
    			$type = 4;
    			break;
    		case 7415 :
    			$type = 5;
    			break;
    		case 7515 :
    			$type = 6;
    			break;
    		case 7615 :
    			$type = 7;
    			break;
    		case 8715 :
    			$type = 8;
    			break;
    		case 8515 :
    			$type = 9;
    			break;
    		case 8415 :
    			$type = 10;
    			break;
    		case 8615 :
    			$type = 11;
    			break;
	 	}

	 	//根据训练项目类型，区分内容
	 	$fieldInfo = Hapyfish2_Alchemy_Bll_Training::_getFieldByType($type);
	 	$fieldLev = $fieldInfo['fieldLev'];
	 	$fieldAdd = $fieldInfo['fieldAdd'];
	 	$fieldMer = $fieldInfo['fieldMer'];
	 	
	 	$decNum = $roleTra[$fieldAdd];
	 	if ( $decNum < 1 ) {
	 		return -280;
	 	}
	 	
	 	$roleTra[$fieldAdd] = 0;
	 	$roleTra[$fieldLev] = 0;
	 	$userMercenary[$fieldMer] -= $decNum;
	 	
	 	try {
		 	$ok = Hapyfish2_Alchemy_HFC_Training::updateOne($uid, $roleId, $roleTra);
		 	if (!$ok) {
		 		return -200;
		 	}

		 	if ( $roleId == 0 ) {
		 		$ok2 = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary);
		 	}
		 	else {
		 		$ok2 = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $roleId, $userMercenary);
		 	}
		 	if (!$ok2) {
		 		return -200;
		 	}
	 	}
	 	catch (Exception $e) {
	 		info_log('resetTrainingCard:failed'.$e->getMessage(), 'Bll_Card');
	 	}
	 	
	 	Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, 1);
	
	 	//佣兵与主角数据
	 	$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
	 	$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
	 	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
	
	 	return 1;
	 }
	
}