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
	
	public static function strengthenequipment($uid,$id1,$id2)
	{
		$ok =false;
		$Weapon1 = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $id1);
		$Weapon2 = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $id2);
		$limit = 0;
		$minlist = array();
		$maxlist = array();
		if(!$Weapon1 || !$Weapon2){
			return -200;
		}
		if($id1 == $id2){
			return -200;
		}
		if($Weapon1['durability'] < 1000 || $Weapon2['durability']< 1000){
			return -255;
		}
		if($Weapon1['cid'] != $Weapon2['cid']){
			return -256;
		}
		$cid = $Weapon1['cid'];
		$info = Hapyfish2_Alchemy_Cache_Basic::getWeaponInfo($cid);
		$needCoin = $info['level']*20;
		$userCoin =	Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
		if ( $userCoin < $needCoin ){
			return -207;
		}
		$detail = array_slice($info, 4, 9);
		$detail = array_values($detail);
		foreach($detail as $k=>$v){
			$v = json_decode($v);
			$limit += $v[1];
			$minlist[] = $v[0];
			$maxlist[] = $v[1];
		}
    	$Weapon1 = array_values($Weapon1);
		$Weapon1 = array_slice($Weapon1, 4, 9);
		$limit1 = array_sum($Weapon1);
		$Weapon2 = array_values($Weapon2);
		$Weapon2 = array_slice($Weapon2, 4, 9);
		$limit2 = array_sum($Weapon2);
		if($limit1 >= $limit2){
			$ulimit = $limit1;
			$ulist = $Weapon1;
			$dlimit = $Weapon2;
		}else{
			$ulimit = $limit2;
			$ulist = $Weapon2;
			$dlimit = $Weapon1;
		}
		if($ulimit >= $limit){
			return -257;
		}
		foreach($ulist as $k=>&$v){
			$alimit = $dlimit[$k] - $minlist[$k];
			$add = rand(0, $alimit);
			$uadd = $maxlist[$k] - $v;
			$add = $uadd >= $add ? $add : $uadd;
			$up[] = $add;
			$v += $add;
		}
		unset($v);
		$type = Hapyfish2_Alchemy_HFC_Weapon::getWeaponQuality($info, $ulist);
		$newlimit = array_sum($ulist);
		$percentage = round($newlimit/$limit,2)*100;
		$ulist[] = $type;
		$widTemp = Hapyfish2_Alchemy_HFC_Weapon::getNewWeaponId($uid);
		$cidTemp = str_pad($cid, 7, 0, STR_PAD_LEFT);
		$wid = $widTemp . $cidTemp;
		$binfo = array((int)$wid, 0, 1000);
		$binfo = array_merge($binfo, $ulist);
		 try {
            $dalWeapon = Hapyfish2_Alchemy_Dal_Weapon::getDefaultInstance();
        	$oldWeapon = $dalWeapon->getWeaponByCid($uid, $cid);
        	if (!$oldWeapon) {
        		$newData = array();
        		$newData[] = $binfo;
        		$newWeapon = array('uid'=>$uid, 'cid'=>$cid, 'count'=>1,'data'=>json_encode($ulist));
                $dalWeapon->insert($uid, $newWeapon);
        	}
        	else {
        		$oldData = json_decode($oldWeapon['data']);
        		$oldData[] = $binfo;
        		$newCount = $oldWeapon['count'] + 1;
        		$newWeapon = array('count' => $newCount, 'data' => json_encode($oldData));
        		$dalWeapon->update($uid, $cid, $newWeapon);
        	}
        	Hapyfish2_Alchemy_HFC_Weapon::loadAll($uid);
        	$ok = true;
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_Weapon::addOne:'. $uid. ']' . $e->getMessage(), 'db.err');
        }
        
        if ($ok) {
        	Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $needCoin);
        	Hapyfish2_Alchemy_HFC_Weapon::delWeapon($uid, $id1);
        	Hapyfish2_Alchemy_HFC_Weapon::delWeapon($uid, $id2);
    		$addItem = array($cid, 1, $wid, 1000);
    		$addItem = array_merge($addItem, $ulist);
    		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'addItems', array($addItem));
    		$removeItems = array($cid, 1, $id1, 1000);
    		$removeItems1 = array($cid, 1, $id2, 1000);
    		$removeItems = array_merge($removeItems, $Weapon1);
    		$removeItems1 = array_merge($removeItems1, $Weapon2);
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeItems', array($removeItems,$removeItems1));
			$data['id']= $wid;
			$data['pa']= $ulist[0];
	        $data['pd'] = $ulist[1];
	        $data['ma'] = $ulist[2];
	        $data['md'] = $ulist[3];
	        $data['speed'] = $ulist[4];
	        $data['hp'] = $ulist[5];
	        $data['mp'] = $ulist[6];
	        $data['cri'] = $ulist[7];
	        $data['dod'] = $ulist[8];
	        $data['uppa']= $up[0];
	        $data['uppd'] = $up[1];
	        $data['upma'] = $up[2];
	        $data['upmd'] = $up[3];
	        $data['upspeed'] = $up[4];
	        $data['uphp'] = $up[5];
	        $data['upmp'] = $up[6];
	        $data['upcri'] = $up[7];
	        $data['updod'] = $up[8];
	        $data['type'] = $ulist[9];
	        $data['percentage'] = $percentage;
	        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'strengthenEquipmentVo', $data);
	        $event = array('uid' => $uid, 'data' => 1);
	        Hapyfish2_Alchemy_Bll_TaskMonitor::strengthenequipment($event);
        }
       
        return $ok;
	}
	
	public static function initStrengthenEquipment()
	{
		$face = 'facenpc.47.laotiejiang';
		$content = array('我能让你的装备更强~');
		$k = array_rand($content);
		$data['faceClass'] = $face;
		$data['content'] =$content[$k];
		return array('strengthenEquipmentInitVo'=>$data);
	}
}