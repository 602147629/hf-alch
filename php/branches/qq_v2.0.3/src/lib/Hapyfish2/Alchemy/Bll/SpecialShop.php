<?php

class Hapyfish2_Alchemy_Bll_SpecialShop
{
	private static $_autoDown = array(
		array(288,192,360,144,576),
		array(96,82,115,76,64),
		array(48,58,43,69,58)
	); 
	//商品自动减少
	
	public static function initSpecialShop($uid,$type)
	{
		$userFight = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$fightLevel = $userFight['level'];
		$userSpecialShop = Hapyfish2_Alchemy_Cache_SpecialShop::getUserSpecialShop($uid);
		$shopDetail = Hapyfish2_Alchemy_Cache_SpecialShop::getSpecialShopDetail($type);
		$total = Hapyfish2_Alchemy_Cache_SpecialShop::getTotalSpecialShop();
		$detail = Hapyfish2_Alchemy_Cache_SpecialShop::getDetail($type, $fightLevel);
		$has = $shopDetail['can_buy'] - $userSpecialShop['list'][$type-1];
		$itemShopTotal = $total['list'][$type-1] + $total['auto'][$type-1];
		$itemShopTotal = $itemShopTotal>$shopDetail['total']?$shopDetail['total']:$itemShopTotal;
		$cur = $shopDetail['total'] - $itemShopTotal;
		$priceArr = array($detail['price'],$detail['newPrice'],0,0);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'cur', $cur);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'total', $shopDetail['total']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'has', $has);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'price', $priceArr);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'items', $detail['awards']);
		return 1;
	}
	
	public static function autoDown($data,$date)
	{
		$time = time();
		$h = date('G');
		if($data['updateTime'] == 0){
			$updateTime = strtotime($date);
		}else{
			$updateTime = $data['updateTime'];
		}
		if($h < 8){
			$downList = self::$_autoDown[0];
		}else if($h < 16){
			$downList = self::$_autoDown[1];
		}else{
			$downList = self::$_autoDown[2];
		}
		$passTime = $time - $updateTime;
		$change = false;
		foreach($downList as $k=>$v){
			$num = floor($passTime/$v);
			if($num > 0){
				$data['auto'][$k] += $num;
				$change = true;
			}
		}
		if($change){
			$data['updateTime'] = $time;
			Hapyfish2_Alchemy_Cache_SpecialShop::updateTotalSpecialShop($data);
		}
		return $data;
	}
	
	public static function buySpecialShop($uid,$type)
	{
		$userFight = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$fightLevel = $userFight['level'];
		$userSpecialShop = Hapyfish2_Alchemy_Cache_SpecialShop::getUserSpecialShop($uid);
		$shopDetail = Hapyfish2_Alchemy_Cache_SpecialShop::getSpecialShopDetail($type);
		$total = Hapyfish2_Alchemy_Cache_SpecialShop::getTotalSpecialShop();
		$detail = Hapyfish2_Alchemy_Cache_SpecialShop::getDetail($type, $fightLevel);
		$needGem = $detail['newPrice'];
		$userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
		if ($userGem < $needGem) {
			return -206;
		}
		$has = $shopDetail['can_buy'] - $userSpecialShop['list'][$type-1];
		if($has < 1){
			return -722;
		}
		
		if($shopDetail['buyMore'] == 1 && $total['list'][$type-1] >= $shopDetail['buyLimit']){
			return - 723;
		}
		$itemShopTotal = $shopDetail['total'] - $total['list'][$type-1] - $total['auto'][$type-1];
		$itemShopTotal = $itemShopTotal>0?$itemShopTotal:0;
		if($itemShopTotal == 0){
			return - 723;
		}
		$totalHave = $total['list'][$type-1] + $total['auto'][$type-1];
		$totalHave = $totalHave>$shopDetail['total']?$shopDetail['total']:$totalHave;
		
		Hapyfish2_Alchemy_Bll_MapCopy::awardCondition($uid, $detail['awards']);
		$userFight = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$fightLevelNew = $userFight['level'];
		$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
    	$gemInfo = array(
    			'uid' => $uid,
    			'cost' => $needGem,
    			'summary' => LANG_PLATFORM_BASE_TXT_10,
    			'user_level' => $userLevel,
    			'cid' => 33,
    			'num' => 1
    	);
    	Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo);
    	if($fightLevelNew > $fightLevel){
    		$details = Hapyfish2_Alchemy_Cache_SpecialShop::getDetail($type, $fightLevelNew);
    		$awardsNew = $details['awards'];
    	}else{
    		$awardsNew = $detail['awards'];
    	}
    	$userSpecialShop['list'][$type-1] += 1;
    	$total['list'][$type-1] += 1;
    	Hapyfish2_Alchemy_Cache_SpecialShop::updateUserSpecialShop($uid, $userSpecialShop);
    	Hapyfish2_Alchemy_Cache_SpecialShop::updateTotalSpecialShop($total);
    	$priceArr = array($detail['price'],$detail['newPrice'],0,0);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'cur', $itemShopTotal);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'total', $shopDetail['total']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'has', $has-1);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'price', $priceArr);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'items', $awardsNew);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'giftAward', $detail['awards']);
    	return 1;
		
	}
	
	public static function buyQQSpecialShop($uid,$type)
	{
		$userFight = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$fightLevel = $userFight['level'];
		$userSpecialShop = Hapyfish2_Alchemy_Cache_SpecialShop::getUserSpecialShop($uid);
		$shopDetail = Hapyfish2_Alchemy_Cache_SpecialShop::getSpecialShopDetail($type);
		$total = Hapyfish2_Alchemy_Cache_SpecialShop::getTotalSpecialShop();
		$detail = Hapyfish2_Alchemy_Cache_SpecialShop::getDetail($type, $fightLevel);
		$needGem = $detail['newPrice'];
		$has = $shopDetail['can_buy'] - $userSpecialShop['list'][$type-1];
		if($has < 1){
			return -722;
		}
		
		if($shopDetail['buyMore'] == 1 && $total['list'][$type-1] >= $shopDetail['buyLimit']){
			return - 723;
		}
		$itemShopTotal = $shopDetail['total'] - $total['list'][$type-1] - $total['auto'][$type-1];
		$itemShopTotal = $itemShopTotal>0?$itemShopTotal:0;
		if($itemShopTotal == 0){
			return - 723;
		}
		if($needGem > 0){
			$goodsInfo = array();
	    	$goodsInfo['goodsmeta'] = '每日优惠礼包*每日优惠礼包';
	    	$goodsInfo['goodsurl'] = STATIC_HOST.'/alchemy/image/item/qpoint.jpg';
	    	$goodsInfo['payitem'] = 'p017*'.$needGem.'*1';
			$token = Hapyfish2_Platform_Bll_QqpayByToken::getToken($uid,$goodsInfo);
			if($token){
				Hapyfish2_Alchemy_Bll_UserResult::setYellow($uid);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'payJs', $token['url_params']);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'token', $token['token']);
				$payNeedData = array('type'=>$type);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'payNeedData', $payNeedData);
			}
		}
    	return 1;
		
	}
	
	public static function initstatic()
	{
		$data = array();
		$list = Hapyfish2_Alchemy_Cache_SpecialShop::getSpecialShopItem();
		foreach($list as $k=>$v){
			$data[$v['id']][] = $v['level'];
		}
		return array('type1'=>$data[1],'type2'=>$data[2],'type3'=>$data[3],'type4'=>$data[4],'type5'=>$data[5]);
	}
	
}