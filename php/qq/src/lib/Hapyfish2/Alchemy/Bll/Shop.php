<?php
require_once(CONFIG_DIR . '/language.php');

class Hapyfish2_Alchemy_Bll_Shop
{
	/**
	 *购买物品到仓库-单物品
	 */
	public static function buyOneItem($uid, $cid, $num)
	{
		$needCoin =	0;
		$needGem = 0;

		$num = (int)$num;
		if ( $num < 1 || !is_int($num) ) {
			return -213;
		}
		$itemType =	substr($cid, -2, 1);

		//匹配购买物品信息
		if ( $itemType == 1	) {
			$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getGoodsInfo($cid);
		}
		else if	( $itemType	== 2 ) {
			$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getScrollInfo($cid);
			
			//拥有对应图鉴的卷轴，才可以购买
			$userIllList = Hapyfish2_Alchemy_Cache_Illustrations::getUserIllustrations($uid);
			if ( !isset($userIllList[$cid]) ) {
				return -200;
			}
		}
		else if	( $itemType	== 3 ) {
			$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getStuffInfo($cid);
		}
		else if	( $itemType	== 5 ) {
			$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getDecorInfo($cid);
		}
		else if	( $itemType	== 6 ) {
			$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getWeaponInfo($cid);
		}

		if ( !$itemInfo	|| $itemInfo['can_buy']	!= 1 ) {
			return -213;
		}
		
		//VIP打折商店
		$bllVip = new Hapyfish2_Alchemy_Bll_Vip();
		$userVipInfo = $bllVip->getVipInfo($uid);
		if ( $itemInfo['buy_coin'] > 0 ) {
			if ( $userVipInfo['level'] > 0 && $userVipInfo['vipStatus'] == 1 && isset($itemInfo['buy_coin_vip']) && $itemInfo['buy_coin_vip'] > 0 ) {
				$itemInfo['buy_coin'] = $itemInfo['buy_coin_vip'];
			}
		}
		if ( $itemInfo['buy_gem'] >	0 )	{
			if ( $userVipInfo['level'] > 0 && $userVipInfo['vipStatus'] == 1 && isset($itemInfo['buy_gem_vip']) && $itemInfo['buy_gem_vip'] > 0 ) {
				$itemInfo['buy_gem'] = $itemInfo['buy_gem_vip'];
			}
		}
		
		if(substr($cid, -2) == '22'){
			$userIll = Hapyfish2_Alchemy_Cache_Illustrations::getUserIllustrations($uid);
			if(!isset($userIll[$cid])){
				return -213;
			}
		}
		if ( $itemInfo['buy_coin'] > 0 ) {
			$needCoin = $itemInfo['buy_coin'] * $num;
		}
		if ( $itemInfo['buy_gem'] >	0 )	{
			$needGem =	$itemInfo['buy_gem'] * $num;
		}
		//购买需要经营等级
		$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
		if ( $itemInfo['buy_level'] > $userLevel ) {
			return -200;
		}

		$buyItem = array('cid' => $cid,
						'itemType' => $itemType,
						'count' => $num,
						'coin' => $itemInfo['buy_coin'],
						'gem' => $itemInfo['buy_gem'],
						'name' => $itemInfo['name']);

		//判断价格是否正常
		if ( $needCoin == 0 && $needGem == 0 ) {
			return -213;
		}

		if ($needCoin > 0) {
			$userCoin = Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
			if ($userCoin < $needCoin) {
				return -207;
			}
			Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $needCoin);
		}
		else if ($needGem > 0) {
			$userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
			if ($userGem < $needGem) {
				return -206;
			}
			$gemInfo = array(
	        		'uid' => $uid,
	        		'cost' => $needGem,
	        		'summary' => LANG_PLATFORM_BASE_TXT_1 . $itemInfo['name'],
	        		'user_level' => $userLevel,
	        		'cid' => $cid,
	        		'num' => $num
	        	);
	        $type = Hapyfish2_Alchemy_Bll_Gem::getGemType($cid);
			Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo, $type);
		}

		if ( $itemType == 1 ) {
			$ok = Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, $cid, $num);
		}
		else if ( $itemType == 2 ) {
			$ok = Hapyfish2_Alchemy_HFC_Scroll::addUserScroll($uid, $cid, $num);
		}
		else if ( $itemType == 3 ) {
			$ok = Hapyfish2_Alchemy_HFC_Stuff::addUserStuff($uid, $cid, $num);
		}
		else if ( $itemType == 5 ) {
			$ok = Hapyfish2_Alchemy_HFC_Decor::addBag($uid, $cid, $num);
		}
		else if ( $itemType == 6 ) {
			$ok = Hapyfish2_Alchemy_HFC_Weapon::addUserWeapon($uid, $cid, $num);
		}

		if (!$ok) {
			return -200;
		}

		//触发任务处理
        $event = array('uid' => $uid, 'data' => array($cid=>$num));
		Hapyfish2_Alchemy_Bll_TaskMonitor::exgStuffGain($event);
		$userFight = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$fightLevel = $userFight['level'];
		//report log,统计玩家商店购买物品信息
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('331', array($uid, $cid, $itemInfo['name'], $num, $needCoin, $needGem));
        $logger->report('buyLevel', array($uid, $fightLevel, $itemInfo['cid'], $num));
	
		return 1;
	}

	/**
	 * 购买物品到仓库-多物品
	 *
	 * @param int $uid
	 * @param array $items
	 */
	public static function buyItems($uid, $items)
	{
		$needCoin =	0;
		$needGem = 0;
		$buyItem =	array();
		$userIllList = Hapyfish2_Alchemy_Cache_Illustrations::getUserIllustrations($uid);

		foreach	($items as	$item )	{
			//$cid = $item['cid'];
			//$count = $item['count'];
			$cid = $item['0'];
			$count = $item['1'];
			//$itemType,1->物品,2->卷轴,3->材料,4->工作台,5->装修,6->装备
			$itemType =	substr($cid, -2, 1);
			if ( $count	< 1	|| !is_int($count) ) {
				return -213;
			}

			//匹配购买物品信息
			if ( $itemType == 1	) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getGoodsInfo($cid);
			}
			else if	( $itemType	== 2 ) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getScrollInfo($cid);
				
				//拥有对应图鉴的卷轴，才可以购买
				if ( !isset($userIllList[$cid]) ) {
					return -200;
				}
			}
			else if	( $itemType	== 3 ) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getStuffInfo($cid);
			}
			else if	( $itemType	== 5 ) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getDecorInfo($cid);
			}
			else if	( $itemType	== 6 ) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getWeaponInfo($cid);
			}

			if ( !$itemInfo	|| $itemInfo['can_buy']	!= 1 ) {
				return -213;
			}
			
			//VIP打折商店
			$bllVip = new Hapyfish2_Alchemy_Bll_Vip();
			$userVipInfo = $bllVip->getVipInfo($uid);
			if ( $itemInfo['buy_coin'] > 0 ) {
				if ( $userVipInfo['level'] > 0 && $userVipInfo['vipStatus'] == 1 && isset($itemInfo['buy_coin_vip']) && $itemInfo['buy_coin_vip'] > 0 ) {
					$itemInfo['buy_coin'] = $itemInfo['buy_coin_vip'];
				}
				$needCoin += $itemInfo['buy_coin'] * $count;
			}
			if ( $itemInfo['buy_gem'] >	0 )	{
				if ( $userVipInfo['level'] > 0 && isset($itemInfo['buy_gem_vip']) && $itemInfo['buy_gem_vip'] > 0 ) {
					$itemInfo['buy_gem'] = $itemInfo['buy_gem_vip'];
				}
				$needGem +=	$itemInfo['buy_gem'] * $count;
			}
			
			$buyItem[]	= array('cid' => $cid,
								'itemType' => $itemType,
								'count' => $count,
								'coin' => $itemInfo['buy_coin'],
								'gem' => $itemInfo['buy_gem'],
								'name' => $itemInfo['name']);
		}

		//判断价格是否正常
		if ( $needCoin == 0 && $needGem == 0 ) {
			return -213;
		}

		if ($needCoin > 0) {
			$userCoin = Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
			if ($userCoin < $needCoin) {
				return -207;
			}
		}
		if ($needGem > 0) {
			$userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
			if ($userGem < $needGem) {
				return -206;
			}
		}

		$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
		self::buyItem($uid, $buyItem, $userLevel);
		return 1;
	}

	public static function buyItem($uid, $buyItem, $userLevel)
	{
		foreach ( $buyItem as $item ) {
			if ( $item['coin'] > 0 ) {
				$needCoin = $item['coin'] * $item['count'];
				Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $needCoin);
			}
			else if ( $item['gem'] > 0 ) {
				$needGem = $item['gem'] * $item['count'];
				$gemInfo = array(
		        		'uid' => $uid,
		        		'cost' => $needGem,
		        		'summary' => LANG_PLATFORM_BASE_TXT_1 . $item['name'],
		        		'user_level' => $userLevel,
		        		'cid' => $item['cid'],
		        		'num' => $item['count']
		        	);
		        $type = Hapyfish2_Alchemy_Bll_Gem::getGemType($item['cid']);
				Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo, $type);
			}
			if ( $item['itemType'] == 1 ) {
				Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, $item['cid'], $item['count']);
			}
			else if ( $item['itemType'] == 2 ) {
				Hapyfish2_Alchemy_HFC_Scroll::addUserScroll($uid, $item['cid'], $item['count']);
			}
			else if ( $item['itemType'] == 3 ) {
				Hapyfish2_Alchemy_HFC_Stuff::addUserStuff($uid, $item['cid'], $item['count']);
			}
			else if ( $item['itemType'] == 5 ) {
				Hapyfish2_Alchemy_HFC_Decor::addBag($uid, $item['cid'], $item['count']);
			}
			else if ( $item['itemType'] == 6 ) {
				Hapyfish2_Alchemy_HFC_Weapon::addUserWeapon($uid, $item['cid'], $item['count']);
			}
			$userFight = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
			$fightLevel = $userFight['level'];
    		$log = Hapyfish2_Util_Log::getInstance();
        	$log->report('buyLevel', array($uid, $fightLevel, $item['cid'], $item['count']));
			//触发任务处理
            $event = array('uid' => $uid, 'data' => array($item['cid']=>$item['count']));
    		Hapyfish2_Alchemy_Bll_TaskMonitor::exgStuffGain($event);
		}
		return true;
	}

	/**
	 * 售出物品-多物品
	 *
	 * @param int $uid
	 * @param array $items
	 */
	public static function saleItems($uid, $items)
	{
		$saleItem =	array();
		$saleCoin = 0;
	
		foreach	($items as $item )	{
			$cid = $item['0'];
			$count = $item['1'];
			//$itemType,1->物品,2->卷轴,3->材料,4->工作台,5->装修,6->装备
			$itemType =	substr($cid, -2, 1);
			if ( $count	< 1	|| !is_int($count) ) {
				return -213;
			}
	
			//匹配售出物品信息
			if ( $itemType == 1	) {
				$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
		    	if ( !isset($userGoods[$cid]) || $userGoods[$cid]['count'] < $count ) {
		    		return -338;
		    	}
				
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getGoodsInfo($cid);
			}
			else if	( $itemType	== 2 ) {
				$userScroll = Hapyfish2_Alchemy_HFC_Scroll::getUserScroll($uid);
		    	if ( !isset($userScroll[$cid]) || $userScroll[$cid]['count'] < $count )	{
		    		return -338;
		    	}

		    	$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getScrollInfo($cid);
			}
			else if	( $itemType	== 3 ) {
				$userStuff = Hapyfish2_Alchemy_HFC_Stuff::getUserStuff($uid);
		    	if ( !isset($userStuff[$cid]) || $userStuff[$cid]['count'] < $count ) {
		    		return -338;
		    	}
				
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getStuffInfo($cid);
			}
			else if	( $itemType	== 5 ) {
				$userDecorInBag	= Hapyfish2_Alchemy_HFC_Decor::getBag($uid);
				if ( !isset($userDecorInBag[$cid]) || $userDecorInBag[$cid]['count'] < $count ) {
					return -338;
				}
				
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getDecorInfo($cid);
			}
			else if	( $itemType	== 6 ) {
				$count = 1;
				//玩家装备信息
				$userWeapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $cid);
				if ( !$userWeapon ) {
					return -338;
				}
				
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getWeaponInfo($userWeapon['cid']);
			}
	
			if ( !$itemInfo	|| $itemInfo['sale_coin'] == 0 ) {
				return -338;
			}
			if ( $itemInfo['sale_coin'] > 0 ) {
				$saleCoin += $itemInfo['sale_coin'] * $count;
			}
	
			$saleItem[]	= array('cid' => $cid,
								'itemType' => $itemType,
								'count' => $count,
								'coin' => $itemInfo['sale_coin'],
								'name' => $itemInfo['name']);
		}
	
		//判断价格是否正常
		if ( $saleCoin == 0 ) {
			return -338;
		}
		
		$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
		self::saleItem($uid, $saleItem, $userLevel);
		return 1;
	}

	public static function saleItem($uid, $saleItem, $userLevel)
	{
		foreach ( $saleItem as $item ) {
			if ( $item['coin'] > 0 ) {
				$saleCoin = $item['coin'] * $item['count'];
				Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $saleCoin);
			}
			
			if ( $item['itemType'] == 1 ) {
				Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $item['cid'], $item['count']);
			}
			else if ( $item['itemType'] == 2 ) {
				Hapyfish2_Alchemy_HFC_Scroll::useUserScroll($uid, $item['cid'], $item['count']);
			}
			else if ( $item['itemType'] == 3 ) {
				Hapyfish2_Alchemy_HFC_Stuff::useUserStuff($uid, $item['cid'], $item['count']);
			}
			else if ( $item['itemType'] == 5 ) {
				Hapyfish2_Alchemy_HFC_Decor::useBag($uid, $item['cid'], $item['count']);
			}
			else if ( $item['itemType'] == 6 ) {
				$wid = $item['cid'];
				$delOk = Hapyfish2_Alchemy_HFC_Weapon::delWeapon($uid, $wid);
				if ( $delOk ) {
					$cid = substr($wid, -7, 7);
					$removeItems = array((int)$cid, 1, (int)$wid);
					Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeItems', array($removeItems));
				}
			}
		}
		return true;
	}
	
}