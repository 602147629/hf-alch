<?php

class Hapyfish2_Alchemy_Bll_Weapons
{

	/**
	 * 战斗结束消耗 角色装备耐久度
	 * @param int $uid
	 * @param int $id,佣兵实例id
	 * @return boolean
	 */
	public static function calcDurableAfterFight($uid, $id, $fightResult = null)
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
		    info_log('minusWeaponsDurable:mercenary info not found', 'Bll_Weapons');
			return false;
		}

		$minusWeapon = array();
		foreach($userMercenary['weapon'] as $wid) {
			if ($wid > 0) {
				if ( $fightResult == Hapyfish2_Alchemy_Bll_Fight_Simulator::RESULT_LOSE ) {
					$minus = mt_rand(50, 100);
				}
				else {
			    	$minus = mt_rand(0, 3);
				}
			    if ($minus) {
				    $minusWeapon[$wid] = $minus;
			    }
			}
		}

		if ($minusWeapon) {
		    $damagedWeapon = array();
		    foreach($minusWeapon as $wid=>$minusVal) {
	            $damagedCid = 0;
                self::minusDurable($uid, $wid, $minusVal, $damagedCid);
                if ($damagedCid) {
                    $damagedWeapon[] = $damagedCid;
                }
		    }

		    //重新计算属性值
		    if ($damagedWeapon) {
                self::affectProp($uid, $id, $damagedWeapon, false, $userMercenary);
		    }
		}

		return true;
	}

	/**
	 * 修复装备
	 * @param int $uid
	 * @param int $id,佣兵实例id
	 * @param int $wid,装备实例id
	 * @return boolean
	 */
	public static function repairDurable($uid, $id, $wid)
	{
        //TODO: repair pay and cost , repair val
	    $addVal = 100;
	    $repairCid = 0;
        self::addDurable($uid, $wid, $addVal, $repairCid);
        if ($repairCid) {
            self::affectProp($uid, $id, $repairCid, true);
        }

        return;
	}

	/**
	 * 消耗 装备耐久度
	 * @param int $uid
	 * @param int $wid,装备实例id
	 * @param int $minusVal,消耗值
	 * @param int $damagedCid,已损坏装备的类cid
	 * @return void
	 */
	public static function minusDurable($uid, $wid, $minusVal, &$damagedCid)
	{
        $rowWeapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $wid);
        $basWeapon = Hapyfish2_Alchemy_Cache_Basic::getWeaponInfo($rowWeapon['cid']);
        if ($basWeapon && (int)$basWeapon['durability']>0 && $rowWeapon['status'] != 0) {
    		$rowWeapon['durability'] -= $minusVal;
    		if ($rowWeapon['durability'] < 0) {
    		    $rowWeapon['durability'] = 0;
    		}
    		if ($rowWeapon['durability'] == 0) {
    		    $damagedCid = $rowWeapon['cid'];
    		}
    		Hapyfish2_Alchemy_HFC_Weapon::updateOne($uid, $wid, $rowWeapon);
        }
		return;
	}

	/**
	 * 恢复 装备耐久度
	 * @param int $uid
	 * @param int $wid,装备实例id
	 * @param int $addVal,恢复值
	 * @param int $repairCid,已恢复装备的类cid
	 * @return void
	 */
	public static function addDurable($uid, $wid, $addVal, &$repairCid)
	{
        $rowWeapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $wid);
	    if ($rowWeapon['durability'] < 0) {
		    $rowWeapon['durability'] = 0;
		}
        if ($rowWeapon['durability'] == 0) {
		    $repairCid = $rowWeapon['cid'];
		}
        $rowWeapon['durability'] += abs($addVal);
	    if ($rowWeapon['durability'] > 1000) {
		    $rowWeapon['durability'] = 1000;
		}
		Hapyfish2_Alchemy_HFC_Weapon::updateOne($uid, $wid, $rowWeapon);

		return;
	}


	/**
	 * 增加/减少 装备所影响的属性值
	 * @param int $uid
	 * @param int $id,佣兵实例id
	 * @param mixed $cids,装备的类id 【array or int】
	 * @param boolean $isOn, 装备发挥/失去 作用
	 * @param array $userMercenary
	 * @return void
	 */
	public static function affectProp($uid, $id, $cids, $isOn, $userMercenary=null)
	{
	    if (!$userMercenary) {
    	    //用户佣兵信息
    		if ( $id == 0 ) {
    			//主角信息
            	$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
    		}
    		else {
    			//佣兵雇佣信息
    			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
    		}
	    }

	    if (is_array($cids)) {
	        $cidList = $cids;
	    }
	    else {
	        $cidList[] = $cids;
	    }

	    $paChg = $pdChg = $maChg = $mdChg = $speedChg = $hpChg = $mpChg = $criChg = $dodChg = 0;
	    $basWeapon = Hapyfish2_Alchemy_Cache_Basic::getWeaponList();
	    foreach ($cidList as $cid) {
            $weaponInfo = $basWeapon[$cid];
			$paChg += (int)$weaponInfo['pa'];
			$pdChg += (int)$weaponInfo['pd'];
			$maChg += (int)$weaponInfo['ma'];
			$mdChg += (int)$weaponInfo['md'];
			$speedChg += (int)$weaponInfo['speed'];
			$hpChg += (int)$weaponInfo['hp'];
			$mpChg += (int)$weaponInfo['mp'];
			$criChg += (int)$weaponInfo['cri'];
			$dodChg += (int)$weaponInfo['dod'];
		}

		if (!$isOn) {
		    $paChg = 0-$paChg;
			$pdChg = 0-$pdChg;
			$maChg = 0-$maChg;
			$mdChg = 0-$mdChg;
			$speedChg = 0-$speedChg;
			$hpChg = 0-$hpChg;
			$mpChg = 0-$mpChg;
			$criChg = 0-$criChg;
			$dodChg = 0-$dodChg;
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
			Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary);
		}
		else {
			Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $id, $userMercenary);
		}

		return;
	}
}