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
		$todayTm = strtotime(date('Ymd'));
    	$endTm = 1345651200;  //2012-08-23 00:00:00
    	$sendTm = 1345086000; //2012-08-16 11:00:00

    	$recoupMixEndTm = 1349020800;	//2012-09-15 00:00:00
//    	if($nowTm < 1349020800){
    		self::recoupUserMix($uid);
//    	}
//    	if ( $nowTm < $recoupMixEndTm ) {
    		self::recoupMix($uid);
//    	}
    	
    	$recoupIllEndTm = 1349020800;	//2012-09-04 12:00:00
//    	if ( $nowTm < $recoupIllEndTm ) {
    		self::recoupIll($uid);
//    	}
    	
    	//到指定日期结束
    	if ( $nowTm >= $endTm ) {
    		return;
    	}
    	
    	$dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
    	$createTime = $dalUser->getCreateTime($uid);
    	
    	//2012-08-16 11:00:00 以后注册的玩家不发
    	if ( $createTime > $sendTm ) {
    		return;
    	}
    	
    	//主角战斗等级小于2的不发
    	$roleInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
    	if ( $roleInfo['level'] < 2 ) {
    		return;
    	}
    	
    	$illList = array(1462, 2363, 862, 1534, 2033, 1933, 1634);
    	foreach ( $illList as $v ) {
			//添加图鉴
			Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $v);
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
	
    /**
     * 重置用户佣兵属性
     * @param $uid
     * @param $mid,佣兵实例id
     */
    public static function resetMercenary($uid, $mid)
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
    
}