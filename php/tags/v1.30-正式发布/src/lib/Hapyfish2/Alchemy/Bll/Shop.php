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
			Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo);
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
		
		//report log,统计玩家商店购买物品信息
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('331', array($uid, $cid, $itemInfo['name'], $num, $needCoin, $needGem));
	
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
			if ( $itemInfo['buy_coin'] > 0 ) {
				$needCoin += $itemInfo['buy_coin'] * $count;
			}
			if ( $itemInfo['buy_gem'] >	0 )	{
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
				Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo);
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

			//触发任务处理
            $event = array('uid' => $uid, 'data' => array($item['cid']=>$item['count']));
    		Hapyfish2_Alchemy_Bll_TaskMonitor::exgStuffGain($event);
		}
		return true;
	}
}