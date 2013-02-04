<?php

/**
 * recoup
 *
 * @copyright  Copyright (c) 2012 HapyFish
 * @create      2012/08   Nick
 */
class Hapyfish2_Alchemy_Bll_Recoup
{
    /**
     * 临时补偿
     * @param int $uid
     */
    public static function recoupUser($uid)
    {
    	$nowTm = time();
		//$todayTm = strtotime(date('Ymd'));

    	self::recoupUserMix($uid);
    	self::recoupMix($uid);
    	self::recoupIll($uid);
    	/* $recoupMixEndTm = 1349020800;	//2012-09-15 00:00:00
    	if($nowTm < 1349020800){
    		self::recoupUserMix($uid);
    	}
    	if ( $nowTm < $recoupMixEndTm ) {
    		self::recoupMix($uid);
	   	}
    	
    	$recoupIllEndTm = 1349020800;	//2012-09-04 12:00:00
 	  	if ( $nowTm < $recoupIllEndTm ) {
    		self::recoupIll($uid);
 	  	} */

    	$recoupHireScoreEndTm = 1356796800;	//2012-12-30 00:00:00
    	if ( $nowTm < $recoupHireScoreEndTm ) {
    		$isFirstRecoupHireScore = Hapyfish2_Alchemy_Cache_User::isFirstRecoupHireScore($uid);
    		if ( $isFirstRecoupHireScore == 'Y' ) {
    			self::recoupHireScore($uid);

    			Hapyfish2_Alchemy_Cache_User::setFirstRecoupHireScore($uid, 'N');
    		}
    	}
    	
    	$recoupWorldMapEndTm = 1356796800;	//2012-12-30 00:00:00
    	if ( $nowTm < $recoupWorldMapEndTm ) {
    		self::recoupWorldMap($uid, 3191, 4101);
    	}
    	
    	$recoupTaskEndTm = 1354291200;	//2012-12-01 00:00:00
    	if ( $nowTm < $recoupTaskEndTm ) {
    		self::recoupUserTask($uid, 61, 51);
    	}
    	
    	$recoupTaskForNewHelpEndTm = 1355241600;//2012-12-12 00:00:00
    	if ( $nowTm < $recoupTaskForNewHelpEndTm ) {
    		$userCreateTime = Hapyfish2_Alchemy_Cache_User::getCreateTime($uid);
    		
    		//修复此事件节点以前注册的用户 1354152900  - 2012-11-29 09:35:00
    		if ( $userCreateTime <= 1354152900 ) {
	    		$isFirstRecoupTask = Hapyfish2_Alchemy_Cache_User::isFirstRecoupTaskForNewHelp($uid);
	    		if ( $isFirstRecoupTask == 'Y' ) {
			    	$oldTaskAry = array(141,151,161,171,181,191,201,211,221,231,241,251,261,271,281,291,301,311,321,331,341,351,361,371,381,391,401,411,421,511,521,531,541);
			    	foreach ( $oldTaskAry as $k ) {
			    		self::recoupUserTaskForNewHelp($uid, $k, 131);
			    	}
			    	Hapyfish2_Alchemy_Cache_User::setFirstRecoupTaskForNewHelp($uid, 'N');
	    		}
    		}
    	}
    	
    	return;
    }
    
    public static function recoupWeapon($uid)
    {
    	$nowTm = time();
    	$recoupIllEndTm = 1346342400;  //2012-08-31 00:00:00
    	if ( $nowTm > $recoupIllEndTm ) {
    		return;
    	}
    	$weapon = Hapyfish2_Alchemy_HFC_Weapon::getAll($uid);
    	if($weapon){
	    	foreach($weapon as $k => $v){
	    		if(in_array($v['cid'], array(161,561,10461,11561,11061,1161,1761,2661))  && $v['ma'] > 0){
	    			Hapyfish2_Alchemy_HFC_Weapon::delWeapon($uid, $v['wid']);
	    			Hapyfish2_Alchemy_Bll_EventGift::addUserWeapon($uid, $v['cid'], $v['type']);
	    		}
	    	}
    	}
    }
    
    public static function recoupMix($uid)
    {
    	$mixs = Hapyfish2_Alchemy_HFC_Mix::getUserMix($uid);
    	$mixList = Hapyfish2_Alchemy_Cache_Basic::getMixList();
    	
    	$userMix = array();
    	$change = false;
    	foreach ( $mixs as $k=>$m ) {
    		if ( !isset($mixList[$m]) ) {
    			unset($mixs[$k]);
    			$change = true;
    		}
    		else {
    			$userMix[] = (int)$m;
    		}
    	}
    	
    	if ( $change ) {
    		Hapyfish2_Alchemy_HFC_Mix::updateUserMix($uid, $userMix);
    	}
    	return;
    }
	
    public static function recoupUserMix($uid)
    {
    	$num = 0;
    	$mixs = Hapyfish2_Alchemy_HFC_Mix::getUserMix($uid);
    	$userIllustrations = Hapyfish2_Alchemy_Cache_Illustrations::getUserIllustrations($uid);
    	$basicIll = Hapyfish2_Alchemy_Cache_Basic::getIllustrationsList();
    	foreach($basicIll as $k => $v){
    		if(isset($userIllustrations[$k])){
    			$mixId = $v['mix_cid'];
    			if(!in_array($mixId, $mixs) && $mixId != 0){
    				$num += 1;
    				$mixs[] = (int)$mixId;
    			}
    		}
    	}
    	if($num > 0){
    		Hapyfish2_Alchemy_HFC_Mix::updateUserMix($uid, $mixs);
    	}
    }
    
    public static function recoupIll($uid)
    {
    	$userIllustrations = Hapyfish2_Alchemy_Cache_Illustrations::getUserIllustrations($uid);
    	$basicIll = Hapyfish2_Alchemy_Cache_Basic::getIllustrationsList();
    	
    	$change = false;
    	foreach ( $userIllustrations as $key => $v ) {
    		$id = $v['id'];
    		if ( !isset($basicIll[$id]) ) {
    			unset($userIllustrations[$key]);
    			$change = true;
    			info_log('ill-Id:'.$id.'-uid:'.$uid, 'recoupIll');
    		}
    	}
    	
    	if ( $change ) {
    		Hapyfish2_Alchemy_Cache_Illustrations::updateUserIllustrations($uid, $userIllustrations);
    	}
    	return;
    }
	
    public static function resetMercenary($uid, $mid)
    {
    	if ( $mid == 0 ) {
        	$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
    	}
    	else {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $mid);
    	}

    	//************佣兵基础属性************
    	//佣兵模型信息
    	$modelInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryInfoByJob($userMercenary['job']);
    	//成长信息
    	$growInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryGrowInfoByJob($userMercenary['job']);
    	
    	//成长值
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

    	//************晋级后，各属性增加************
    	$growChange = array();
    	foreach ( $fieldAry as $field ) {
    		$qField = 'q_'.$field;
    		$qualityList[$qField] = $userMercenary[$qField];
    		
    		//升级后数值
    		$growChange[$field] = Hapyfish2_Alchemy_Bll_Mercenary::_getProperty($modelInfo[$field], $growInfo[$field], $userMercenary['rp'], $userMercenary[$qField], $userMercenary['level']);

    		//培养总数值-4属性
    		$sField = 's_'.$field;
    		//培养数值
    		$growChange[$field] += $userMercenary[$sField];
    	}
    	
    	//************培养数值更新-11属性************
    	$proConList = Hapyfish2_Alchemy_Cache_Basic::getMercenaryProContrast($userMercenary['job']);
    	$strInfo = $proConList[1];
    	$dexInfo = $proConList[2];
    	$magInfo = $proConList[3];
    	$phyInfo = $proConList[4];
    	
    	$strChg = $userMercenary['s_str'];
    	$dexChg = $userMercenary['s_dex'];
    	$magChg = $userMercenary['s_mag'];
    	$phyChg = $userMercenary['s_phy'];
    	
    	$paChg = $strChg*$strInfo['phy_att'] + $dexChg*$dexInfo['phy_att'] + $magChg*$magInfo['phy_att'] + $phyChg*$phyInfo['phy_att'];
    	$pdChg = $strChg*$strInfo['phy_def'] + $dexChg*$dexInfo['phy_def'] + $magChg*$magInfo['phy_def'] + $phyChg*$phyInfo['phy_def'];
    	$maChg = $strChg*$strInfo['mag_att'] + $dexChg*$dexInfo['mag_att'] + $magChg*$magInfo['mag_att'] + $phyChg*$phyInfo['mag_att'];
    	$mdChg = $strChg*$strInfo['mag_def'] + $dexChg*$dexInfo['mag_def'] + $magChg*$magInfo['mag_def'] + $phyChg*$phyInfo['mag_def'];
    	$agilityChg = $strChg*$strInfo['agility'] + $dexChg*$dexInfo['agility'] + $magChg*$magInfo['agility'] + $phyChg*$phyInfo['agility'];
    	$critChg = $strChg*$strInfo['crit'] + $dexChg*$dexInfo['crit'] + $magChg*$magInfo['crit'] + $phyChg*$phyInfo['crit'];
    	$dodgeChg = $strChg*$strInfo['dodge'] + $dexChg*$dexInfo['dodge'] + $magChg*$magInfo['dodge'] + $phyChg*$phyInfo['dodge'];
    	$hitChg = $strChg*$strInfo['hit'] + $dexChg*$dexInfo['hit'] + $magChg*$magInfo['hit'] + $phyChg*$phyInfo['hit'];
    	$touChg = $strChg*$strInfo['tou'] + $dexChg*$dexInfo['tou'] + $magChg*$magInfo['tou'] + $phyChg*$phyInfo['tou'];
    	$hpChg = $strChg*$strInfo['hp'] + $dexChg*$dexInfo['hp'] + $magChg*$magInfo['hp'] + $phyChg*$phyInfo['hp'];
    	$mpChg = $strChg*$strInfo['mp'] + $dexChg*$dexInfo['mp'] + $magChg*$magInfo['mp'] + $phyChg*$phyInfo['mp'];
    	
    	$growChange['phy_att'] += round($paChg/100);
    	$growChange['phy_def'] += round($pdChg/100);
    	$growChange['mag_att'] += round($maChg/100);
    	$growChange['mag_def'] += round($mdChg/100);
    	$growChange['agility'] += round($agilityChg/100);
    	$growChange['crit'] += round($critChg/100);
    	$growChange['dodge'] += round($dodgeChg/100);
    	$growChange['hit'] += round($hitChg/100);
    	$growChange['tou'] += round($touChg/100);
    	$growChange['hp'] += round($hpChg/100);
    	$growChange['mp'] += round($mpChg/100);
    	
		//************装备数值************
    	$userWeapon = $userMercenary['weapon'];
		if (!empty($userWeapon)) {
			foreach ( $userWeapon as $weaponId ) {
				if ( $weaponId > 0 ) {
					$oldWeapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $weaponId);
					if($oldWeapon['durability'] > 0){
						$growChange['hp'] += $oldWeapon['hp'];
						$growChange['mp'] += $oldWeapon['mp'];
						$growChange['phy_att'] += $oldWeapon['pa'];
						$growChange['phy_def'] += $oldWeapon['pd'];
						$growChange['mag_att'] += $oldWeapon['ma'];
						$growChange['mag_def'] += $oldWeapon['md'];
						$growChange['agility'] += $oldWeapon['speed'];
						$growChange['crit'] += $oldWeapon['cri'];
						$growChange['dodge'] += $oldWeapon['dod'];
						$growChange['hit'] += $oldWeapon['hit'];
						$growChange['tou'] += $oldWeapon['tou'];
					}
				}
			}
		}
    	
		//************训练营属性加成************
		//该佣兵训练信息
		$roleTra = Hapyfish2_Alchemy_HFC_Training::getOne($uid, $mid);
		$growChange['hp'] += $roleTra['add_hp'];
		$growChange['mp'] += $roleTra['add_mp'];
		$growChange['phy_att'] += $roleTra['add_pa'];
		$growChange['phy_def'] += $roleTra['add_pd'];
		$growChange['mag_att'] += $roleTra['add_ma'];
		$growChange['mag_def'] += $roleTra['add_md'];
		$growChange['agility'] += $roleTra['add_agility'];
		$growChange['crit'] += $roleTra['add_crit'];
		$growChange['dodge'] += $roleTra['add_dodge'];
		$growChange['hit'] += $roleTra['add_hit'];
		$growChange['tou'] += $roleTra['add_tou'];
		
		//重新赋值
		foreach ( $fieldAry as $field ) {
			$userMercenary[$field] = $growChange[$field];
		}
		$userMercenary['hp_max'] = $growChange['hp'];
		$userMercenary['mp_max'] = $growChange['mp'];
		
		$userMercenary['hp'] = $userMercenary['hp_max'];
		$userMercenary['mp'] = $userMercenary['mp_max'];
		
		if ( $mid == 0 ) {
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
		}
		else {
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $mid, $userMercenary);
		}
    	
		return $ok;
    }
    
    /**
     * 临时调整用户任务信息（配合系统任务调整、修复玩家数据）
     * @param int $uid
     * @param int $oldId
     * @param int $newId
     */
    public static function recoupUserTask($uid, $oldId, $newId)
    {
    	//用户当前任务列表
    	$openTask = Hapyfish2_Alchemy_HFC_TaskOpen::getInfo($uid);
    	if ( !$openTask['list'] || $openTask['list'] != array($oldId) ) {
    		return;
    	}
    	
    	//新任务是否已完成
    	$isCompleted = Hapyfish2_Alchemy_Cache_Task::isCompletedTask($uid, $newId);
    	if ( $isCompleted ) {
    		Hapyfish2_Alchemy_Cache_Task::delCompleteTask($uid, $newId);
    	}
    	
    	$openTask['list'] = array(51);

    	$ok = Hapyfish2_Alchemy_HFC_TaskOpen::update($uid, $openTask);
    	if ( $ok ) {
    		Hapyfish2_Alchemy_Bll_MapCopy::refreshMapCopyFree($uid, '矿洞通道');
    	}
    	
    	return $ok;
    }
    
    public static function recoupUserTaskForNewHelp($uid, $oldId, $newId)
    {
    	//用户当前任务列表
    	$openTask = Hapyfish2_Alchemy_HFC_TaskOpen::getInfo($uid);

    	if ( !$openTask['list'] || $openTask['list'] != array($oldId) ) {
    		return;
    	}
    	
    	//清楚已完成任务记录
    	$delComTaskAry = array(131,141,151,261,271,511,521,531,541,191,201,281,291,301,311,321,331,341,351,361,371,381,401,391);
    	foreach ( $delComTaskAry as $k ) {
    		Hapyfish2_Alchemy_Cache_Task::delCompleteTask($uid, $k);
    	}
    	
    	//清楚已完成剧情记录
    	$delStoryAry = array(321,431,331,371,441,151,241,191);
    	foreach ( $delStoryAry as $j ) {
    		Hapyfish2_Alchemy_HFC_Story::delStory($uid, $j);
    	}
    	
    	$openTask['list'] = array((int)$newId);

    	$ok = Hapyfish2_Alchemy_HFC_TaskOpen::update($uid, $openTask);
    	if ( $ok ) {
    		Hapyfish2_Alchemy_Bll_MapCopy::refreshMapCopyFree($uid, '矿洞通道');
    	}
    	
    	return $ok;
    }

    /**
     * 修复用户世界地图未解锁的问题
     * @param int $uid
     * @param int $storyId
     * @param int $worldMapId
     */
    public static function recoupWorldMap($uid, $taskId, $storyId)
    {
    	//任务是否已完成
    	$isCompleted = Hapyfish2_Alchemy_Cache_Task::isCompletedTask($uid, $taskId);
    	if ( $isCompleted ) {
    		$storyVo = Hapyfish2_Alchemy_Bll_Story::startStory($uid, $storyId);
    		info_log('uid:'.$uid, 'recoupWorldMap');
    	}
    	
    	return;
    }

    /**
     * 修复用户酒馆人气值
     * 1,玩家所有人气值清空
     * 2,原有玩家人气值 按 数量/100 向下取整方式，折合成 美酒：3001115 道具
     * 
     * @param int $uid
     * @param int $storyId
     * @param int $worldMapId
     */
    public static function recoupHireScore($uid)
    {
		$userHireData = Hapyfish2_Alchemy_HFC_Hire::getHireData($uid);

    	//用户当前拥有的 酒馆积分
    	$curScore = $userHireData['score'];
    	
    	$addCount = floor($curScore/100);
    	
    	$ok = true;
    	if ( $addCount > 0 ) {
    		$ok = Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, 3001115, $addCount);
    	}
    	
    	if ( $ok ) {
    		$userHireData['score'] = 0;
    		Hapyfish2_Alchemy_HFC_Hire::updateHireData($uid, $userHireData);
    	}

    	info_log('uid:'.$uid .' ---cnt:'.$addCount, 'recoupHireScore');
    	
    	return;
    }
    
    public static function recoupUserWeapons($uid)
    {
    	$all = Hapyfish2_Alchemy_HFC_Weapon::getAll($uid);
    	foreach($all as $k=>$v){
    		$update =array();
    		$info = Hapyfish2_Alchemy_Cache_Basic::getWeaponInfo($v['cid']);
			$strBasic = json_decode($info['strGrow'], true);
			$itemLevel = $info['itemLevel'];
			$newInfo = self::getQuality($v['cid'], $info, $v['type']);
			$update['pa'] = $newInfo[0];
			$update['pd'] = $newInfo[1];
			$update['ma'] = $newInfo[2];
			$update['md'] = $newInfo[3];
			$update['speed'] = $newInfo[4];
			$update['hp'] = $newInfo[5];
			$update['mp'] = $newInfo[6];
			$update['cri'] = $newInfo[7];
			$update['dod'] = $newInfo[8];
			$update['hit'] = $newInfo[9];
			$update['tou'] = $newInfo[10];
			$addpa = Hapyfish2_Alchemy_Bll_Weapons::getStr($strBasic[0], $itemLevel, $v['type']);
			$addpd = Hapyfish2_Alchemy_Bll_Weapons::getStr($strBasic[1], $itemLevel, $v['type']);
			$addma = Hapyfish2_Alchemy_Bll_Weapons::getStr($strBasic[2], $itemLevel, $v['type']);
			$addmd = Hapyfish2_Alchemy_Bll_Weapons::getStr($strBasic[3], $itemLevel, $v['type']);
			$addspeed = Hapyfish2_Alchemy_Bll_Weapons::getStr($strBasic[4], $itemLevel, $v['type']);
			$addhp = Hapyfish2_Alchemy_Bll_Weapons::getStr($strBasic[5], $itemLevel, $v['type']);
			$addmp = Hapyfish2_Alchemy_Bll_Weapons::getStr($strBasic[6], $itemLevel, $v['type']);
			$addcri = Hapyfish2_Alchemy_Bll_Weapons::getStr($strBasic[7], $itemLevel, $v['type']);
			$adddod = Hapyfish2_Alchemy_Bll_Weapons::getStr($strBasic[8], $itemLevel, $v['type']);
			$addhit = Hapyfish2_Alchemy_Bll_Weapons::getStr($strBasic[9], $itemLevel, $v['type']);
			$addtou = Hapyfish2_Alchemy_Bll_Weapons::getStr($strBasic[10], $itemLevel, $v['type']);
			$update['pa'] += $addpa*$v['strLevel'];
			$update['pd'] += $addpd*$v['strLevel'];
			$update['ma'] += $addma*$v['strLevel'];
			$update['md'] += $addmd*$v['strLevel'];
			$update['speed'] += $addspeed*$v['strLevel'];
			$update['hp'] += $addhp*$v['strLevel'];
			$update['mp'] += $addmp*$v['strLevel'];
			$update['cri'] += $addcri*$v['strLevel'];
			$update['dod'] += $adddod*$v['strLevel'];
			$update['hit'] += $addhit*$v['strLevel'];
			$update['tou'] += $addtou*$v['strLevel'];
			$pachange = $update['pa'] - $v['pa'];
			$pdchange = $update['pd'] - $v['pd'];
			$machange = $update['ma'] - $v['ma'];
			$mdchange = $update['md'] - $v['md'];
			$speedchange = $update['speed'] - $v['speed'];
			$hpchange = $update['hp'] - $v['hp'];
			$mpchange = $update['mp'] - $v['mp'];
			$crichange = $update['cri'] - $v['cri'];
			$dodchange = $update['dod'] - $v['dod'];
			$hitchange = $update['hit'] - $v['hit'];
			$touchange = $update['tou'] - $v['tou'];
			$weapons = $v;
			$weapons['pa'] = $update['pa'];
			$weapons['pd'] = $update['pd'];
			$weapons['ma'] = $update['ma'];
			$weapons['md'] = $update['md'];
			$weapons['speed'] = $update['speed'];
			$weapons['hp'] = $update['hp'];
			$weapons['mp'] = $update['mp'];
			$weapons['cri'] = $update['cri'];
			$weapons['dod'] = $update['dod'];
			$weapons['hit'] = $update['hit'];
			$weapons['tou'] = $update['tou'];
			Hapyfish2_Alchemy_HFC_Weapon::updateOne($uid, $v['wid'], $weapons);
    		if ( $v['status'] == -1 ) {
				$roleId = 0;
			}
			else if ( $v['status'] == 0 ) {
				$roleId = -1;
			}
			else {
				$roleId = $v['status'];
			}
			if($roleId != -1){
				if ( $roleId == 0 ) {
	        		$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		    	}
		    	else {
					$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $roleId);
		    	}
		    	$userMercenary['hp'] += $hpchange;
				$userMercenary['mp'] += $mpchange;
				$userMercenary['phy_att'] += $pachange;
				$userMercenary['phy_def'] += $pdchange;
				$userMercenary['mag_att'] += $machange;
				$userMercenary['mag_def'] += $mdchange;
				$userMercenary['agility'] += $speedchange;
				$userMercenary['crit'] += $crichange;
				$userMercenary['dodge'] += $dodchange;
				$userMercenary['hit'] += $hitchange;
				$userMercenary['tou'] += $touchange;
				$userMercenary['hp_max'] += $hpchange;
				$userMercenary['mp_max'] += $mpchange;
				if ( $roleId == 0 ) {
					$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
				}
				else {
					$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $roleId, $userMercenary);
				}
			}
    	}
    }
    
    public static function getQuality($cid, $detail, $type) 
    {
        $info = array();
        if ($type == 4) {
            $rate = 100;
        } else if ($type == 3) {
            $rate = 99;
        } else if ($type == 2) {
            $rate = 66;
        } else {
            $rate = 33;
        }
        $detail = array_values($detail);
        $detail = array_slice($detail, 4, 11);
        foreach ($detail as $k => $v) {
            $dnum = json_decode($v, true);
            $info[] = $dnum[0] + floor(($dnum[1] - $dnum[0]) * $rate / 100);
        }
        $info[] = $type;
        return $info;
    }
    
}