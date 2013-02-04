<?php

class Hapyfish2_Alchemy_Bll_Repair
{
	/**
	 * 修理装备
	 * @param int $uid
	 * @param int $wid，装备id
	 * @param int $type，修理类型，1:全部修理；2:仅修理已装备
	 */
	public static function repairWeapon($uid, $wid, $type)
	{
		if ( $type == 1 ) {
			$allList = Hapyfish2_Alchemy_HFC_Weapon::getAll($uid);
			$repairList = array();
			foreach ( $allList as $v ) {
				if ( $v['durability'] < 1000 ) {
					$repairList[] = $v;
				}
			}
		}
		else if ( $type == 2 ) {
			$allList = Hapyfish2_Alchemy_HFC_Weapon::getAll($uid);
			$repairList = array();
			foreach ( $allList as $v ) {
				if ( $v['status'] != 0  && $v['durability'] < 1000 ) {
					$repairList[] = $v;
				}
			}
		}
		else {
			$weapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $wid);
			if ( $weapon['durability'] >= 1000 ) {
				return -260;
			}
			$repairList = array();
			$repairList[] = $weapon;
		}
		
		if ( empty($repairList) ) {
			return -260;
		}
		
		//铁匠铺等级
		$userSmithyLevel = Hapyfish2_Alchemy_HFC_User::getUserSmithyLevel($uid);
		
		$needCoin = 0;
		foreach ( $repairList as $k => $r ) {
			$repairNum = 1000 - $r['durability'];
			$weapon = Hapyfish2_Alchemy_Cache_Basic::getWeaponInfo($r['cid']);
			if ( $weapon['can_repair'] == 1 && $weapon['repair_level'] <= $userSmithyLevel ) {
				$need = ceil($weapon['repair_price'] * 0.001  * $repairNum);
				$needCoin += $need;
			}
			else {
				unset($repairList[$k]);
			}
		}
		
		if ( empty($repairList) ) {
			return -260;
		}

		//铁匠铺当前等级信息
		$smithyInfo = Hapyfish2_Alchemy_Cache_Basic::getSmithyLevel($userSmithyLevel);
		
		//根据铁匠铺等级，降低修理费用（打折）
		$needCoin -= $needCoin * $smithyInfo['discount']/100;
		$needCoin = $needCoin < 1 ? 1 : round($needCoin);
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		$vipInfo = $vip->getVipInfo($uid);
		if($vipInfo['level'] >= 5 && $vipInfo['vipStatus'] == 1){
			$needCoin = 0;
		}
		$userCoin = Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
		if ( $userCoin < $needCoin )	{
			return -207;
		}
		
		$roleChange = false;
		//开始修理
		foreach ( $repairList as $i ) {
			if ( $i['durability'] < 1 ) {
				if ( $i['status'] == -1 ) {
					$roleId = 0;
				}
				else if ( $i['status'] == 0 ) {
					$roleId = -1;
				}
				else {
					$roleId = $i['status'];
				}
				
				//修理佣兵身上的破损装备
				if ( $roleId != -1 ) {
					$ok = self::repairMercenryWeapon($uid, $roleId, $i['wid']);
					if ( !$ok ) {
						return -200;
					}
					$roleChange = true;
				}
			}
			$i['durability'] = 1000;
			Hapyfish2_Alchemy_HFC_Weapon::updateOne($uid, $i['wid'], $i);
		}
		
		if ( $needCoin > 0 ) {
			$needCoin = Hapyfish2_Alchemy_Bll_VipWelfare::getVipRepair($uid, $needCoin);
			Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $needCoin,11);
		}
		$log = Hapyfish2_Util_Log::getInstance();
		$log->report('229', array($uid, $needCoin));
		
		if ( $roleChange ) {
			//佣兵与主角数据
			$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
			$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
		}
		
		return 1;
	}
	
	/**
	 * 佣兵身上的装备，耐久度从0修复后，恢复属性效果
	 * @param int $roleId
	 * @param int $cid
	 * @param int $wid
	 */
	public static function repairMercenryWeapon($uid, $roleId, $wid)
	{
		if ( $roleId == 0 ) {
			//主角信息
        	$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		}
		else {
			//用户佣兵信息
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $roleId);
		}
		if ( !$userMercenary ) {
			return false;
		}
		
		$useWeapon = false;
		//判断装备是否是该佣兵身上装备的
		foreach ( $userMercenary['weapon'] as $weapon ) {
			if ( $weapon == $wid ) {
				$useWeapon = true;
			}
		}
		if ( !$useWeapon ) {
			return false;
		}
		
		$weaponInfo = $weapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $wid);
		
		$userMercenary['hp_max'] += $weaponInfo['hp'];
		$userMercenary['mp_max'] += $weaponInfo['mp'];
		$userMercenary['phy_att'] += $weaponInfo['pa'];
		$userMercenary['phy_def'] += $weaponInfo['pd'];
		$userMercenary['mag_att'] += $weaponInfo['ma'];
		$userMercenary['mag_def'] += $weaponInfo['md'];
		$userMercenary['agility'] += $weaponInfo['speed'];
		$userMercenary['crit'] += $weaponInfo['cri'];
		$userMercenary['dodge'] += $weaponInfo['dod'];
		$userMercenary['hit'] += $weaponInfo['hit'];
		$userMercenary['tou'] += $weaponInfo['tou'];
		
		$userMercenary['hp'] = $userMercenary['hp'] > $userMercenary['hp_max'] ? $userMercenary['hp_max'] : $userMercenary['hp'];
		$userMercenary['mp'] = $userMercenary['mp'] > $userMercenary['mp_max'] ? $userMercenary['mp_max'] : $userMercenary['mp'];
	
		if ( $roleId == 0 ) {
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
		}
		else {
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $roleId, $userMercenary);
		}
		return $ok;
	}
}