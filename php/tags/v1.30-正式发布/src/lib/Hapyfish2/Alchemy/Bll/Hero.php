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
	    		$heroWeapon[] = Hapyfish2_Alchemy_HFC_Weapon::addMercenaryWeapon($uid, $weapon, $heroId);
	    	}
	    }
	    for ( $i = 0; $i < 4 ;$i++ ) {
	    	if (!isset($heroWeapon[$i])) {
	    		$heroWeapon[$i] = 0;
	    	}
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
		//随机站位信息
		if ($basicHero['fight_corps'] > 0) {
			if (!in_array($basicHero['fight_corps'], $oldCorpsList)) {
				$newCorps = $basicHero['fight_corps'];;
			}
		}
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
		
		$removeRoles = array($heroId);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeRoles', $removeRoles);

		return 1;
	}
	
}