<?php
class Hapyfish2_Alchemy_Bll_VipWelfare
{
	public static function getFightVipAward($uid, $data)
	{
		$add = 0;
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		foreach ($data as $k => &$v) {
			if ($v['type'] == 1) {
    			$type =	substr($v['id'], -2, 1);
    			if($type == 3){
    				$add = $vip->getGain($uid, 'fright_material');
    			}
			}
			//玩家属性
			else if ($v['type'] == 2) {
				if ($v['id'] == 'coin') {
				    $add = $vip->getGain($uid, 'fright_coin');
				}
			}
			$v['num'] = ceil($v['num']*(1 + $add/100));
		}	
		unset($v);
		return $data;
	}
	
	public static function getVipFightExp($uid, $num)
	{
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		$add = $vip->getGain($uid, 'fright_exp');
		$addNum = ceil($num*($add/100));
		return $addNum;
	}
	
	public static function getVipOrderExp($uid, $num)
	{
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		$add = $vip->getGain($uid, 'order_skilled');
		$newNum = ceil($num*(1 + $add/100));
		return $newNum;
	}
	
	public static function getVipOrderCoin($uid, $num)
	{
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		$add = $vip->getGain($uid, 'order_coin');
		$newNum = ceil($num*(1 + $add/100));
		return $newNum;
	}
	
	public static function getVipRepair($uid, $num)
	{
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		$add = $vip->getGain($uid, 'repair_coin');
		$newNum = ceil($num*(1 - $add/100));
		return $newNum;
	}
	
	public static function getVipTaxescoin($uid, $num)
	{
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		$add = $vip->getGain($uid, 'taxes_coin');
		$newNum = ceil($num*(1 + $add/100));
		return $newNum;
	}
	
	public static function getVipMaxSp($uid)
	{
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		return $vip->getMaxSp($uid);
	}
	
	public static function initUserSp()
	{
		$re = array(
			array(
				'number'			=> 1,
				'vipLevel'			=> 0,
				'linePowerMax'		=> 50,
				'firstGemItemNum'	=> 5,
				'maxGemItemNum'		=> 20,
				'isDiscount'		=> 0
			),
			array(
				'number'			=> 2,
				'vipLevel'			=> 0,
				'linePowerMax'		=> 50,
				'firstGemItemNum'	=> 10,
				'maxGemItemNum'		=> 40,
				'isDiscount'		=> 0
			),
			array(
				'number'			=> 3,
				'vipLevel'			=> 0,
				'linePowerMax'		=> 50,
				'firstGemItemNum'	=> 15,
				'maxGemItemNum'		=> 60,
				'isDiscount'		=> 0
			),
			array(
				'number'			=> 4,
				'vipLevel'			=> 0,
				'linePowerMax'		=> 50,
				'firstGemItemNum'	=> 25,
				'maxGemItemNum'		=> 100,
				'isDiscount'		=> 0
			),
			array(
				'number'			=> 5,
				'vipLevel'			=> 0,
				'linePowerMax'		=> 50,
				'firstGemItemNum'	=> 40,
				'maxGemItemNum'		=> 160,
				'isDiscount'		=> 0
			),
			array(
				'number'			=> 6,
				'vipLevel'			=> 1,
				'linePowerMax'		=> 60,
				'firstGemItemNum'	=> 35,
				'maxGemItemNum'		=> 168,
				'isDiscount'		=> 0
			),
			array(
				'number'			=> 6,
				'vipLevel'			=> 2,
				'linePowerMax'		=> 70,
				'firstGemItemNum'	=> 30,
				'maxGemItemNum'		=> 168,
				'isDiscount'		=> 0
			),
			array(
				'number'			=> 7,
				'vipLevel'			=> 3,
				'linePowerMax'		=> 80,
				'firstGemItemNum'	=> 25,
				'maxGemItemNum'		=> 160,
				'isDiscount'		=> 0
			),
			array(
				'number'			=> 7,
				'vipLevel'			=> 4,
				'linePowerMax'		=> 90,
				'firstGemItemNum'	=> 20,
				'maxGemItemNum'		=> 144,
				'isDiscount'		=> 0
			),
			array(
				'number'			=> 8,
				'vipLevel'			=> 5,
				'linePowerMax'		=> 100,
				'firstGemItemNum'	=> 15,
				'maxGemItemNum'		=> 120,
				'isDiscount'		=> 0
			)
		);
		return $re;
	}
	
	public static function addSp($uid, $type)
	{
		$useMax = self::getVipMaxSpNum($uid);
		$maxSp = self::getVipMaxSp($uid);
		$useAddSp = Hapyfish2_Alchemy_Cache_Vip::getUserAddSp($uid);
		$userSp = Hapyfish2_Alchemy_HFC_User::getUserSp($uid);
		if($userSp['sp'] >= $maxSp)
		{
			return -236;
		}
		$needGem = 0;
		if($useAddSp['num'] >= $useMax){
			return -705;
		}
		$status = 1;
		if($type == 1){
			$cardInfo = Hapyfish2_Alchemy_Cache_Basic::getGoodsInfo(615);
			$status = Hapyfish2_Alchemy_Bll_Card::spCard($uid, 615, $cardInfo);
		} else if($type == 2){
			$cardInfo = Hapyfish2_Alchemy_Cache_Basic::getGoodsInfo(715);
			$status = Hapyfish2_Alchemy_Bll_Card::spCard($uid, 715, $cardInfo);
		}else if($type == 3){
			$info = self::initUserSp();
			$needGem = $info[$useAddSp['num']]['firstGemItemNum'];
			$userGem =	Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
			if ( $userGem < $needGem ) {
				return -206;
			}
			$addSp = 10;
			$ok = Hapyfish2_Alchemy_HFC_User::incUserSp($uid, $addSp);
			
		}else if($type == 4){
			$info = self::initUserSp();
			$needGem = $info[$useAddSp['num']]['maxGemItemNum'];
			$userGem =	Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
			if ( $userGem < $needGem ) {
				return -206;
			}
			$add = $maxSp - $userSp['sp'];
			$ok = Hapyfish2_Alchemy_HFC_User::incUserSp($uid, $add);
			
		}
		
		if ( $needGem > 0 ) {
			$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
			//扣除用户宝石
			$gemInfo = array(
	        		'uid' => $uid,
	        		'cost' => $needGem,
	        		'summary' => LANG_PLATFORM_BASE_TXT_3,
	        		'user_level' => $userLevel,
	        		'cid' => 1,
	        		'num' => 1
	        	);
			Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo);
		}
		$useAddSp['num'] += 1;
		Hapyfish2_Alchemy_Cache_Vip::updateUserAddSp($uid, $useAddSp);
		$useAddSp = Hapyfish2_Alchemy_Cache_Vip::getUserAddSp($uid);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'addLinePowerNum', $useAddSp['num']);
		return $status;
	}
	
	public static function getVipMaxSpNum($uid)
	{
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		return $vip->getMaxSpNum($uid);
	}
	
	public static function getOrderMax($uid)
	{
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		return $vip->getMaxOrder($uid);
	}
	
	public static function getRefreshTime($uid)
	{
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		return $vip->getRefreshTime($uid);
	}
	
	public static function checkVip($uid, $totalPay)
	{
		$data = array(
			'10'=>array(10,1),
			'200'=>array(600,20),
			'1000'=>array(1500,50),
			'3000'=>array(3000,100),
			'10000'=>array(6000,200)
		);
		$userPay = Hapyfish2_Alchemy_Cache_EventGift::getVipPay($uid);
		$growUp = false;
		$finish = json_decode($userPay['step'],true);
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		foreach($data as $pay=>$info){
			if($totalPay >= $pay){
				if(!in_array($pay,$finish)){
					$vip->addEndTime($uid,$info[1]);
					$vip->addGrowUp($uid, $info[0]);
					$finish[] = $pay;
					$growUp = true;
				}
			}
		}
		if($growUp){
			$userPay['uid'] = $uid;
			$userPay['step'] = json_encode($finish);
			$userPay['type'] = 3;
			Hapyfish2_Alchemy_Cache_EventGift::updateVipPay($userPay);
		}
	}
}