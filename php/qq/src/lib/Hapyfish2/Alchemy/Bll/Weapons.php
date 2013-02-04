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
			$rowWeapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $wid);
			if ($wid > 0 && $rowWeapon['durability'] > 0) {
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
	            $damagedId = 0;
                self::minusDurable($uid, $wid, $minusVal, $damagedId);
                if ($damagedId) {
                    $damagedWeapon[] = $damagedId;
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
	    $repairId = 0;
        self::addDurable($uid, $wid, $addVal, $repairId);
        if ($repairId) {
            self::affectProp($uid, $id, $repairId, true);
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
	public static function minusDurable($uid, $wid, $minusVal, &$damagedId)
	{
        $rowWeapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $wid);
        $basWeapon = Hapyfish2_Alchemy_Cache_Basic::getWeaponInfo($rowWeapon['cid']);
        if ($basWeapon && (int)$basWeapon['durability']>0 && $rowWeapon['status'] != 0) {
    		$rowWeapon['durability'] -= $minusVal;
    		if ($rowWeapon['durability'] < 0) {
    		    $rowWeapon['durability'] = 0;
    		}
    		if ($rowWeapon['durability'] == 0) {
    		    $damagedId = $wid;
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
	public static function addDurable($uid, $wid, $addVal, &$repairId)
	{
        $rowWeapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $wid);
	    if ($rowWeapon['durability'] < 0) {
		    $rowWeapon['durability'] = 0;
		}
        if ($rowWeapon['durability'] == 0) {
		    $repairCid = $wid;
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

	    $paChg = $pdChg = $maChg = $mdChg = $speedChg = $hpChg = $mpChg = $criChg = $dodChg = $hitChg = $touChg = 0;
	    foreach ($cidList as $wid) {
            $weaponInfo = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $wid);
			$paChg += (int)$weaponInfo['pa'];
			$pdChg += (int)$weaponInfo['pd'];
			$maChg += (int)$weaponInfo['ma'];
			$mdChg += (int)$weaponInfo['md'];
			$speedChg += (int)$weaponInfo['speed'];
			$hpChg += (int)$weaponInfo['hp'];
			$mpChg += (int)$weaponInfo['mp'];
			$criChg += (int)$weaponInfo['cri'];
			$dodChg += (int)$weaponInfo['dod'];
			$hitChg += (int)$weaponInfo['hit'];
			$touChg += (int)$weaponInfo['tou'];
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
			$hitChg = 0-$hitChg;
			$touChg = 0-$touChg;
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
			Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary);
		}
		else {
			Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $id, $userMercenary);
		}

		return;
	}
	
	public static function strengthenequipment1($uid,$id1,$id2)
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
		$detail = array_slice($info, 4, 11);
		$detail = array_values($detail);
		foreach($detail as $k=>$v){
			$v = json_decode($v);
			$limit += $v[1];
			$minlist[] = $v[0];
			$maxlist[] = $v[1];
		}
    	$Weapon1 = array_values($Weapon1);
		$Weapon1 = array_slice($Weapon1, 4, 11);
		$limit1 = array_sum($Weapon1);
		$Weapon2 = array_values($Weapon2);
		$Weapon2 = array_slice($Weapon2, 4, 11);
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
		$singleLimit = array();
		foreach($ulist as $k=>&$v){
			$alimit = $dlimit[$k] - $minlist[$k];
			$add = rand(0, $alimit);
			$uadd = $maxlist[$k] - $v;
			$add = $uadd >= $add ? $add : $uadd;
			$up[] = $add;
			$v += $add;
			if($v >= $maxlist[$k]){
				$singleLimit[] = 1;
			}else{
				$singleLimit[] = 0;
			}
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
	        $data['hit'] = $ulist[9];
	        $data['tou'] = $ulist[10];
	        $data['uppa']= $up[0];
	        $data['uppd'] = $up[1];
	        $data['upma'] = $up[2];
	        $data['upmd'] = $up[3];
	        $data['upspeed'] = $up[4];
	        $data['uphp'] = $up[5];
	        $data['upmp'] = $up[6];
	        $data['upcrit'] = $up[7];
	        $data['updodge'] = $up[8];
	        $data['uphit'] = $up[9];
	        $data['uptou'] = $up[10];
	        $data['type'] = $ulist[11];
	        $data['isMaxPa'] = $singleLimit[0];
	        $data['isMaxPd'] = $singleLimit[1];
	        $data['isMaxMa'] = $singleLimit[2];
	        $data['isMaxMd'] = $singleLimit[3];
	        $data['isMaxHp'] = $singleLimit[5];
	        $data['isMaxMp'] = $singleLimit[6];
	        $data['isMaxSpeed'] = $singleLimit[4];
	        $data['isMaxCrit'] = $singleLimit[7];
	        $data['isMaxDodge'] = $singleLimit[8];
	        $data['isMaxHit'] = $singleLimit[9];
	        $data['isMaxTou'] = $singleLimit[10];
	        
	        $data['percentage'] = $percentage;
	        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'strengthenEquipmentVo', $data);
	        $event = array('uid' => $uid, 'data' => 1);
	        Hapyfish2_Alchemy_Bll_TaskMonitor::strengthenequipment($event);
        }
       
        return $ok;
	}
	
	public static function initStrengthenEquipment($uid)
	{
		$coolTime = self::getStrCoolTime($uid);
		$face = 'facenpc.47.laotiejiang';
		$content = array('我能让你的装备更强~');
		$k = array_rand($content);
		$data['faceClass'] = $face;
		$data['content'] = $content[$k];
		$data['coolTime'] = $coolTime['coolTime'];
		$data['isStrengthen'] = $coolTime['canStr'];
		return array('strengthenEquipmentInitVo'=>$data);
	}
	
	public static function strengthenequipment($uid, $roleId, $wid, $useType = 0)
	{
		$nowTime = time();
		$strCoolTime = self::getStrCoolTime($uid);
		if($strCoolTime['canStr'] == 0){
			return -714;
		}
		$userInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		if ( $roleId == 0 ) {
			//主角信息
        	$userMercenary = $userInfo;
		}
		else {
			//佣兵雇佣信息
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $roleId);
		}
		if (!$userMercenary) {
			return -222;
		}
		$weapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $wid);
		if(!$weapon){
			return -200;
		}
		if(!in_array($wid, $userMercenary['weapon'])){
			return -100;
		}
		if($weapon['strLevel'] >= $userInfo['level']){
			return -706;
		}
		$cid = $weapon['cid'];
		$info = Hapyfish2_Alchemy_Cache_Basic::getWeaponInfo($cid);
		$costBasic = $info['costCoin'];
		$strBasic = json_decode($info['strGrow'], true);
		$itemLevel = $info['itemLevel'];
		$strStatus = self::strRate($weapon['strLevel'],$useType);
		$cost = floor(self::getCost($costBasic,$itemLevel,$weapon['strLevel'],$weapon['type']));
		$userCoin = Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
		if($userCoin < $cost){
			return -207;
		}
		if($useType > 0){
			if($useType == 1){
				$cid = 8115;
			}
			if($useType == 2){
				$cid = 8215;
			}
			$userGoods  = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
			if (empty($userGoods) || !isset($userGoods[$cid]) || $userGoods[$cid]['count'] < 1) {
    				if($useType == 1){
    					return -707;
    				}
    				if($useType == 2){
    					return -708;
    				}
    		}
    		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, 1, $userGoods);
		}
		Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $cost);
		if($strStatus){
			$addpa = self::getStr($strBasic[0], $itemLevel, $weapon['type']);
			$addpd = self::getStr($strBasic[1], $itemLevel, $weapon['type']);
			$addma = self::getStr($strBasic[2], $itemLevel, $weapon['type']);
			$addmd = self::getStr($strBasic[3], $itemLevel, $weapon['type']);
			$addspeed = self::getStr($strBasic[4], $itemLevel, $weapon['type']);
			$addhp = self::getStr($strBasic[5], $itemLevel, $weapon['type']);
			$addmp = self::getStr($strBasic[6], $itemLevel, $weapon['type']);
			$addcri = self::getStr($strBasic[7], $itemLevel, $weapon['type']);
			$adddod = self::getStr($strBasic[8], $itemLevel, $weapon['type']);
			$addhit = self::getStr($strBasic[9], $itemLevel, $weapon['type']);
			$addtou = self::getStr($strBasic[10], $itemLevel, $weapon['type']);
			$weapon['pa'] += $addpa;
			$weapon['pd'] += $addpd;
			$weapon['ma'] += $addma;
			$weapon['md'] += $addmd;
			$weapon['speed'] += $addspeed;
			$weapon['hp'] += $addhp;
			$weapon['mp'] += $addmp;
			$weapon['cri'] += $addcri;
			$weapon['dod'] += $adddod;
			$weapon['hit'] += $addhit;
			$weapon['tou'] += $addtou;
			$weapon['strLevel'] += 1;
			Hapyfish2_Alchemy_HFC_Weapon::updateOne($uid, $wid, $weapon);
			if($weapon['durability'] > 0){
				$userMercenary['hp_max'] += $addhp;
				$userMercenary['mp_max'] += $addmp;
				$userMercenary['phy_att'] += $addpa;
				$userMercenary['phy_def'] += $addpd;
				$userMercenary['mag_att'] += $addma;
				$userMercenary['mag_def'] += $addmd;
				$userMercenary['agility'] += $addspeed;
				$userMercenary['crit'] += $addcri;
				$userMercenary['dodge'] += $adddod;
				$userMercenary['hit'] += $addhit;
				$userMercenary['tou'] += $addtou;
				if ( $roleId == 0 ) {
					$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
				}
				else {
					$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $roleId, $userMercenary);
				}
			}
			$vip = new Hapyfish2_Alchemy_Bll_Vip();
			$vipInfo = $vip->getVipInfo($uid);
			if($vipInfo['level'] < 1 || $vipInfo['vipStatus'] == 0){
				$userCoolTime = Hapyfish2_Alchemy_HFC_User::getStrCoolTime($uid);
				if($userCoolTime['endtime'] > $nowTime){
					$userCoolTime['endtime'] += 60;
				}else{
					$userCoolTime['endtime'] = $nowTime + 60;
				}
				$strCoolTime['coolTime'] += 60;
				if($strCoolTime['coolTime'] >= 900){
					$userCoolTime['canStr'] = 0;
				}
				Hapyfish2_Alchemy_HFC_User::updateStrCoolTime($uid,$userCoolTime);
				Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'strengthenCd');
			}
			
			$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
			$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'coolTime', $strCoolTime['coolTime']);
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'isStrengthen', $userCoolTime['canStr']);
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'issuccess', 1);
			return 1;
		}else{
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'issuccess', 0);
		}
		
		
		
	}
	
	public static function strRate($itemLevel, $type)
	{
		$add = 0;
		if($type == 1){
			$add = 30;
		}else if ($type == 2){
			$add = 80;
		}
		$rate = rand(1, 100);
		$strRate = round(((1-($itemLevel)*0.02/(1+0.02*($itemLevel)))- floor(($itemLevel+1)/5)*0.05),2)*100;
		if($strRate < 5){
			$strRate = 5;
		}
		if($rate <= $strRate + $add){
			return true;
		}
		return false;
	}
	
	public static function getCost($costBasic, $itemLevel,$strLevel, $type)
	{
		$cost = (($costBasic*($itemLevel+2*$type+5)/$itemLevel+5)*(1+floor(($strLevel)/5)*0.05));
		return $cost;
	}
	
	public static function getStr($basic,$itemLevel,$type)
	{
		$add = floor($basic*($itemLevel+$type*2+5)/($itemLevel+5));
		return $add;
	}
	
	public static function getStrCoolTime($uid)
	{
		$data = Hapyfish2_Alchemy_HFC_User::getStrCoolTime($uid);
	 	$time = time();
       	$coolTime = $data['endtime'] - $time > 0?$data['endtime'] - $time:0;
         if( $data['canStr'] == 0){
	        if($coolTime == 0){
	        	$data['canStr'] = 1;
	        	Hapyfish2_Alchemy_HFC_User::updateStrCoolTime($uid,$data);
	        }
         }
         return array('coolTime'=>$coolTime, 'canStr'=>$data['canStr']);
	}
	
	public static function clearCoolTime($uid)
	{
		$coolStatus = self::getStrCoolTime($uid);
		if($coolStatus['coolTime'] <= 0){
			return -200;
		}
		$userStr = Hapyfish2_Alchemy_HFC_User::getStrCoolTime($uid);
		$cost = 2*ceil($coolStatus['coolTime']/60);
		$userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
		if ( $userGem <	$cost ) {
			return -206;
		}
		$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
		//扣除用户宝石
		$gemInfo = array(
        		'uid' => $uid,
        		'cost' => $cost,
        		'summary' => LANG_PLATFORM_BASE_TXT_5,
        		'user_level' => $userLevel,
        		'cid' => 0,
        		'num' => 1
        	);
		Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo, 4);
		$userStr['endtime'] = 0;
		$userStr['canStr'] = 1;
		Hapyfish2_Alchemy_HFC_User::updateStrCoolTime($uid,$userStr);
		Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'strengthenCd');
		return 1;
	}
}