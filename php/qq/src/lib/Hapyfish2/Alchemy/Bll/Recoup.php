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

    	$recoupTaskEndTm = 1354291200;	//2012-12-01 00:00:00
    	if ( $nowTm < $recoupTaskEndTm ) {
    		self::recoupUserTask($uid, 61, 51);
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

    	//晋级后，各属性增加
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
    	
    	//培养数值更新-11属性
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
    	$hpChg = $strChg*$strInfo['hp_max'] + $dexChg*$dexInfo['hp_max'] + $magChg*$magInfo['hp_max'] + $phyChg*$phyInfo['hp_max'];
    	$mpChg = $strChg*$strInfo['mp_max'] + $dexChg*$dexInfo['mp_max'] + $magChg*$magInfo['mp_max'] + $phyChg*$phyInfo['mp_max'];

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
    	
		//装备数值
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
     * 重置用户佣兵属性
     * @param $uid
     * @param $mid,佣兵实例id
     */
    public static function resetMercenaryOld($uid, $mid)
    {
    	if ( $mid == 0 ) {
        	$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
    		$modelId = $userMercenary['cid'];
    		$modelInfo = Hapyfish2_Alchemy_Cache_Basic::getInitRole($modelId);
    		$growId = $userMercenary['gid'];
    		$rp = $userMercenary['rp'];
    		if ( $rp > 1 ) {
    			$growId = Hapyfish2_Alchemy_Bll_Mercenary::getNewGrowForRoleUpgrade($rp, $userMercenary['job']);
    		}
    	}
    	else {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $mid);
			
    		$nameAry = array('琳', '铁锤十四世', '探索者塞弗尔');
			if ( in_array($userMercenary['name'], $nameAry) ) {
				$cardInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryCardInfoByName($userMercenary['name']);
				$modelInfo = $cardInfo;
			}
			else {
	    		$modelId = $userMercenary['cid'];
	    		$modelInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryInfo($modelId);
			}
    		$growId = $userMercenary['gid'];
    	}
    	
    	$level = $userMercenary['level'];
    	$levelUpCnt = $level - 1;
    	
    	$growInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryGrowInfo($growId);
    	
    	//初始模型值+升级成长值
		$hp = $modelInfo['hp'] + $growInfo['hp'] * $levelUpCnt;
		$mp = $modelInfo['mp'] + $growInfo['mp'] * $levelUpCnt;
    	$pa = $modelInfo['phy_att'] + $growInfo['phy_att'] * $levelUpCnt;
		$pd = $modelInfo['phy_def'] + $growInfo['phy_def'] * $levelUpCnt;
		$ma = $modelInfo['mag_att'] + $growInfo['mag_att'] * $levelUpCnt;
		$md = $modelInfo['mag_def'] + $growInfo['mag_def'] * $levelUpCnt;
		$speed = $modelInfo['agility'] + $growInfo['agility'] * $levelUpCnt;
		$cri = $modelInfo['crit'] + $growInfo['crit'] * $levelUpCnt;
		$dod = $modelInfo['dodge'] + $growInfo['dodge'] * $levelUpCnt;
    	
		$userMercenary['hp_max']  = $hp;
		$userMercenary['mp_max']  = $mp;
		$userMercenary['phy_att'] = $pa + $userMercenary['s_phy_att'];
		$userMercenary['phy_def'] = $pd + $userMercenary['s_phy_def'];
		$userMercenary['mag_att'] = $ma + $userMercenary['s_mag_att'];
		$userMercenary['mag_def'] = $md + $userMercenary['s_mag_def'];
		$userMercenary['agility'] = $speed + $userMercenary['s_agility'];
		$userMercenary['crit'] 	  = $cri + $userMercenary['s_crit'];
		$userMercenary['dodge']   = $dod + $userMercenary['s_dodge'];
    	
		$userMercenary['hp'] = $userMercenary['hp_max'];
		$userMercenary['mp'] = $userMercenary['mp_max'];
    
		if ( $mid == 0 ) {
			$userMercenary['gid'] = $growId;
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
		}
		else {
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $mid, $userMercenary, true);
		}
    	if ( !$ok ) {
    		err_log('Hapyfish2_Alchemy_Bll_Recoup:resetMercenary:uid:'.$uid.':mid:'.$mid.':');
    	}
    	return true;
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
    
    
}