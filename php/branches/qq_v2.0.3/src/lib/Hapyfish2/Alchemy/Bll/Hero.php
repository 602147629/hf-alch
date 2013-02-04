<?php

class Hapyfish2_Alchemy_Bll_Hero
{
	/**
	 * 添加临时英雄佣兵
	 */
	public static function addHeroMercenary($uid, $heroId)
	{
		$basicHero = Hapyfish2_Alchemy_Cache_Basic::getInitRole($heroId);
		if (!$basicHero) {
			return -100;
		}
		//佣兵雇佣信息
		$userHero = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $heroId);
		
		if ($userHero) {
			return -100;
		}
		
		$weaponList = json_decode($basicHero['weapon']);
		$heroWeapon = array();
	    foreach ( $weaponList as $key => $weapon ) {
	    	if ( $weapon > 0 ) {
	    		$wid = Hapyfish2_Alchemy_HFC_Weapon::addMercenaryWeapon($uid, $weapon, $heroId);
	    		$heroWeapon[] = $wid;
				//装备增加属性值
				$weaponInfo = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $wid);

	    		$basicHero['hp_max'] += $weaponInfo['hp'];
	    		$basicHero['mp_max'] += $weaponInfo['mp'];
	    		$basicHero['phy_att'] += $weaponInfo['pa'];
	    		$basicHero['phy_def'] += $weaponInfo['pd'];
	    		$basicHero['mag_att'] += $weaponInfo['ma'];
	    		$basicHero['mag_def'] += $weaponInfo['md'];
	    		$basicHero['agility'] += $weaponInfo['speed'];
	    		$basicHero['crit'] += $weaponInfo['cri'];
	    		$basicHero['dodge'] += $weaponInfo['dod'];
	    		$basicHero['hit'] += $weaponInfo['hit'];
	    		$basicHero['tou'] += $weaponInfo['tou'];
	    	}
	    }
	    for ( $i = 0; $i < 4 ;$i++ ) {
	    	if (!isset($heroWeapon[$i])) {
	    		$heroWeapon[$i] = 0;
	    	}
	    }

	    $qualitySum = $basicHero['q_hp'] + $basicHero['q_mp'] + $basicHero['q_phy_att'] + $basicHero['q_phy_def'] + $basicHero['q_mag_att'] + $basicHero['q_mag_def']
	    			+ $basicHero['q_agility'] + $basicHero['q_crit'] + $basicHero['q_dodge'] + $basicHero['q_hit'] + $basicHero['q_tou'];
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
	    
	    //特殊佣兵夏洛特，作为正式佣兵加入阵型
	    if ( $heroId == 9 ) {
			$heroId = Hapyfish2_Alchemy_Bll_Mercenary::getId($uid);
	    }
	    
		$info = array(
	        'uid' => $uid,
            'mid' => $heroId,
            'cid' => $heroId,
            'gid' => $basicHero['gid'],
            'rp' => $basicHero['rp'],
            'job' => $basicHero['job'],
            'name' => $basicHero['name'],
            'class_name' => $basicHero['class_name'],
            'face_class_name' => $basicHero['face_class_name'],
            's_face_class_name' => $basicHero['s_face_class_name'],
            'scene_player_class' => $basicHero['scene_player_class'],
            'sex' => $basicHero['sex'],
            'element' => $basicHero['element'],
            'exp' => 0,
            'level' => 1,
            'hp' => $basicHero['hp'],
            'hp_max' => $basicHero['hp'],
            'mp' => $basicHero['mp'],
            'mp_max' => $basicHero['mp'],
            'phy_att' => $basicHero['phy_att'],
            'phy_def' => $basicHero['phy_def'],
            'mag_att' => $basicHero['mag_att'],
            'mag_def' => $basicHero['mag_def'],
            'agility' => $basicHero['agility'],
            'crit' => $basicHero['crit'],
            'dodge' => $basicHero['dodge'],
			'hit' => $basicHero['hit'],
			'tou' => $basicHero['tou'],
			'str' => $basicHero['str'],
			'dex' => $basicHero['dex'],
			'mag' => $basicHero['mag'],
			'phy' => $basicHero['phy'],

			'q_hp' => $basicHero['q_hp'],
			'q_mp' => $basicHero['q_mp'],
			'q_phy_att' => $basicHero['q_phy_att'],
			'q_phy_def' => $basicHero['q_phy_def'],
			'q_mag_att' => $basicHero['q_mag_att'],
			'q_mag_def' => $basicHero['q_mag_def'],
			'q_agility' => $basicHero['q_agility'],
			'q_crit' => $basicHero['q_crit'],
			'q_dodge' => $basicHero['q_dodge'],
			'q_hit' => $basicHero['q_hit'],
			'q_tou' => $basicHero['q_tou'],
			'q_str' => $basicHero['q_str'],
			'q_dex' => $basicHero['q_dex'],
			'q_mag' => $basicHero['q_mag'],
			'q_phy' => $basicHero['q_phy'],
			'q_role' => $qRole,
			
            'skill' => json_decode($basicHero['skill']),
            'weapon' => $heroWeapon
	    );
		$ok = Hapyfish2_Alchemy_HFC_FightMercenary::addOne($uid, $info);
		if (!$ok) {
			return -200;
		}
		
		//原有站位信息
		$fightCorps = Hapyfish2_Alchemy_Cache_FightCorps::getFightCorpsInfo($uid);
		$oldFightCorps = array();
		$oldCorpsList = array();
		foreach ( $fightCorps as $kC => $vC ) {
			$oldFightCorps[$kC] = $vC;
			$oldCorpsList[] = $kC;
		}
		
        $newCorps = null;
        //固定站位信息
		if ($basicHero['fight_corps'] > 0) {
			if (!in_array($basicHero['fight_corps'], $oldCorpsList)) {
				$newCorps = $basicHero['fight_corps'];
			}
		}
		//随机站位信息
		if ( !$newCorps ) {
            $corpsList = array(9,10,11,12,13,14,15,16,17);
            foreach ( $oldFightCorps as $corps ) {
                $key = array_search($corps, $corpsList);
                unset($corpsList[$key]);
            }
            $rand = array_rand($corpsList);
            $newCorps = $corpsList[$rand];
		}
		//更新站位
		$newMap = array();
		foreach ( $oldFightCorps as $key => $v ) {
			$newMap[$key] = $v;
		}
		$newMap[$newCorps] = $heroId;
		Hapyfish2_Alchemy_Cache_FightCorps::saveFightCorpsInfo($uid, $newMap);
		
		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
		
		return 1;
	}
	
	/**
	 * 删除临时英雄佣兵
	 */
	public static function removeHeroMercenary($uid, $heroId)
	{
		//佣兵雇佣信息
		$userHero = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $heroId);
		if (!$userHero) {
			return -100;
		}
		
		//自动卸装
		if (!empty($userHero['weapon']) ) {
			if ( $userHero['weapon'] != array(0,0,0,0) && $userHero['weapon'] != array() ) {
				$weaponList = array();
				foreach ( $userHero['weapon'] as $weapon ) {
					if ($weapon > 0) {
						$userWeapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $weapon);
						$userWeapon['status'] = 0;
						Hapyfish2_Alchemy_HFC_Weapon::updateOne($uid, $weapon, $userWeapon);
					}
				}
			}
		}

		$ok = Hapyfish2_Alchemy_HFC_FightMercenary::delOne($uid, $heroId);
		if (!$ok) {
			return -200;
		}
		
		//更新站位信息
		$fightCorps = Hapyfish2_Alchemy_Cache_FightCorps::getFightCorpsInfo($uid);
		$oldFightCorps = array();
		foreach ( $fightCorps as $kC => $vC ) {
			$oldFightCorps[$kC] = $vC;
		}
		foreach ( $oldFightCorps as $pos => $v ) {
			if ( $v == $heroId ) {
				unset($oldFightCorps[$pos]);
			}
		}
		Hapyfish2_Alchemy_Cache_FightCorps::saveFightCorpsInfo($uid, $oldFightCorps);
		
		$removeRoles = array($heroId);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeRoles', $removeRoles);

		return 1;
	}
	
}