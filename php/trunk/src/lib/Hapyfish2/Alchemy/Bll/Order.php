<?php
require_once(CONFIG_DIR . '/language.php');
class Hapyfish2_Alchemy_Bll_Order
{
	
    const ORDER_STRY_TIME = 60;
    
	public static function getOrderList($uid)
	{
		$list = Hapyfish2_Alchemy_HFC_Order::getOrderList($uid);

		$orderList = array();
		foreach ( $list as $v ) {
			$orderList[] = $v;
		}
		
		$requestList = self::getAllRequest($uid);
		foreach ( $requestList as $m ) {
			$orderList[] = $m;
		}
		
		return $orderList;
	}

	public static function getRequestOrder($uid, $id)
	{
		$order = Hapyfish2_Alchemy_HFC_Order::getRequestOne($uid, $id);
		return $order;
	}

	public static function delRequestOrder($uid, $id)
	{
		$order = Hapyfish2_Alchemy_HFC_Order::delRequestOne($uid, $id);
		return $order;
	}

	/**
	 * 获取所有临时订单
	 * 
	 */
	public static function getAllRequest($uid)
	{
		$result = self::clearRequestOrder($uid);
		
		$nowTime = time();
		$list = array();
		foreach ( $result['list'] as $order ) {
			$order['remainingTime'] = ($order['startTime'] + $order['outTime']) - $nowTime;
			$order['overTime'] = $order['startTime'] + self::ORDER_STRY_TIME;
			unset($order['startTime']);
			unset($order['cid']);
			$order['id'] = (string)$order['id'];
			$list[] = $order;
		}
		
		return $list;
	}
	
	/**
	 * 请求订单
	 *
	 * @param int $uid
	 */
	public static function requestOrder($uid)
	{
		$lastRequestTime = Hapyfish2_Alchemy_HFC_Order::getLastRequestTime($uid);
		$nowTime = time();
		//每10秒一个
		if ( $nowTime - $lastRequestTime < 10 ) {
			return -219;
		}

		//新手引导信息，判断是否第一次请求订单
		$userHelp = Hapyfish2_Alchemy_HFC_Help::get($uid);
		
		//随机订单信息
		$order = self::getRandOrder($uid, -1, $userHelp['id']);

		$newId = Hapyfish2_Alchemy_HFC_Order::getNewOrderId($uid);
		if ( $userHelp['id'] == 3 ) {
			$newId .= 'h';
		}
		
		//
		$newId = Hapyfish2_Alchemy_HFC_Order::requestOne($uid, $order, $newId);
		if ( $newId > 0 ) {
			Hapyfish2_Alchemy_HFC_Order::updateLastRequestTime($uid, $nowTime);

			$order['id'] = $newId;
			$order['remainingTime'] = ($order['startTime'] + $order['outTime']) - $nowTime;
			$order['overTime'] = $order['startTime'] + self::ORDER_STRY_TIME;
			unset($order['startTime']);
			unset($order['cid']);
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'order', $order);
		}
		else {
			return -200;
		}

		return 1;
	}

	/**
	 * 接受订单
	 *
	 * @param int $uid
	 * @param int $id,订单实例id
	 */
	public static function acceptOrder($uid, $id)
	{
		//订单信息
		$orderInfo = Hapyfish2_Alchemy_HFC_Order::getRequestOne($uid, $id);
		if ( !$orderInfo ) {
			return -214;
		}

		//用户订单数上限
		$userMaxOrderCount = Hapyfish2_Alchemy_HFC_User::getUserMaxOrderCount($uid);

		$userOrderList = Hapyfish2_Alchemy_HFC_Order::getOrderList($uid);
		$userCurOrderCount = count($userOrderList);

		if ( $userCurOrderCount >= $userMaxOrderCount ) {
			return -215;
		}

		//接受订单
		$nowTime = time();
		$orderInfo['state'] = 2;
		$orderInfo['startTime'] = $nowTime;

		$userOrderList[] = $orderInfo;
		$ok = Hapyfish2_Alchemy_HFC_Order::updateOrderList($uid, $userOrderList);
		if ( $ok ) {
			Hapyfish2_Alchemy_HFC_Order::delRequestOne($uid, $id);
			//Hapyfish2_Alchemy_HFC_Order::updateLastRequestTime($uid, $nowTime);
		}

		//如果是接好友家订单，则记录好友信息
		$friendOrder = substr($id, -1, 1);
		if ( $friendOrder == 'f' ) {
			$fid = substr($id, 0, -1);
			$orderFids[$fid] = 1;
			Hapyfish2_Alchemy_HFC_Order::addOrderFids($uid, $fid);	
			
			//insert minifeed
			$minifeeda = array('uid' => $uid,
	                          'template_id' => 2,
	                          'actor' => $uid,
	                          'target' => $fid,
	                          'title' => array(),
	                          'type' => 1,
	                          'create_time' => time());
			Hapyfish2_Alchemy_Bll_Feed::insertMiniFeed($minifeeda);
			$minifeedt = array('uid' => $fid,
	                          'template_id' => 3,
	                          'actor' => $uid,
	                          'target' => $fid,
	                          'title' => array(),
	                          'type' => 1,
	                          'create_time' => time());
			Hapyfish2_Alchemy_Bll_Feed::insertMiniFeed($minifeedt);
		}
		
		//触发任务处理
        $event = array('uid' => $uid, 'data' => 1);
        Hapyfish2_Alchemy_Bll_TaskMonitor::acceptOrder($event);
		//report log,统计玩家接受订单分布
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('311', array($uid, $orderInfo['cid']));
        
		return 1;
	}

	/**
	 * 完成订单
	 *
	 * @param $uid
	 * @param $id
	 */
	public static function completeOrder($uid, $id, $isFailed, $wid)
	{
		//订单信息
		$orderList = Hapyfish2_Alchemy_HFC_Order::getOrderList($uid);
		if ( !isset($orderList[$id]) ) {
			return -214;
		}
		$orderInfo = $orderList[$id];

		//用户当前满意度
		$userCurStatisfaction = Hapyfish2_Alchemy_HFC_Order::getSatisfaction($uid);

		//放弃过期订单
		if ( $isFailed == 1 ) {
			$satisfactionChg = -20;
			if ( $userCurStatisfaction < 20 ) {
				$satisfactionChg = -$userCurStatisfaction;
			}

			if ( $orderInfo['remainingTime'] > 0 ) {
				return -217;
			}

			unset($orderList[$id]);
			$ok = Hapyfish2_Alchemy_HFC_Order::updateOrderList($uid, $orderList);
			if ( $ok ) {
//				$exp = Hapyfish2_Alchemy_Bll_VipWelfare::getVipOrderExp($uid, 1);
				Hapyfish2_Alchemy_HFC_User::incUserExp($uid, 1);		//加经验
				Hapyfish2_Alchemy_HFC_Order::updateSatisfaction($uid, $satisfactionChg);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'satisfaction', $satisfactionChg);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'tip', 0);
				
				//report log,统计玩家放弃过期订单分布
				$logger = Hapyfish2_Util_Log::getInstance();
				$logger->report('313', array($uid, $orderInfo['cid']));
				
				return 1;
			}
		}

		//是否已过期
		if ( $orderInfo['remainingTime'] < 0 ) {
			return -216;
		}

		//判断材料是否足够
		$needs = json_decode($orderInfo['needs']);
		$needGoods = array();
		$needScroll	= array();
		$needStuff = array();
		$needDecorInBag	= array();
		$needWeapon	= array();
		foreach	( $needs as	$need )	{
			$needCid = $need[0];
			$needCount = $need[1];
			$needType =	substr($needCid, -2, 1);
			//1->物品,2->卷轴,3->材料,4->工作台,5->装修,6->装备
			if ( $needType == 1	) {
				$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
				if ( !isset($userGoods[$needCid]) || $userGoods[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needGoods[] = array('cid' => $needCid,	'count'	=> $needCount);
			}
			else if	( $needType	== 2 ) {
				$userScroll	= Hapyfish2_Alchemy_HFC_Scroll::getUserScroll($uid);
				if ( !isset($userScroll[$needCid]) || $userScroll[$needCid]['count']	< $needCount ) {
					return -204;
				}
				$needScroll[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
			else if	( $needType	== 3 ) {
				$userStuff = Hapyfish2_Alchemy_HFC_Stuff::getUserStuff($uid);
				if ( !isset($userStuff[$needCid]) || $userStuff[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needStuff[] = array('cid' => $needCid,	'count'	=> $needCount);
			}
			else if	( $needType	== 5 ) {
				$userDecorInBag	= Hapyfish2_Alchemy_HFC_Decor::getBag($uid);
				if ( !isset($userDecorInBag[$needCid]) || $userDecorInBag[$needCid]['count']	< $needCount ) {
					return -204;
				}
				$needDecorInBag[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
			else if	( $needType	== 6 ) {
				$ids = explode(',', $wid);
				foreach($ids as $wids){
					if($wids != ''){
						$userNewWeapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $wids);
						if ( !$userNewWeapon || $userNewWeapon['durability'] < 1000) {
							return -204;
						}
						$needWeapon[] =	array('wid'	=> $wids, 'count' =>1, 'cid'=>$userNewWeapon['cid']);
					}
				}
				
			}
		}

		$awards = json_decode($orderInfo['awards']);

		//扣除需求物品
		if ( !empty($needGoods)	) {
			foreach	( $needGoods as	$goods ) {
				$userGoods[$goods['cid']]['count'] -= $goods['count'];
				$userGoods[$goods['cid']]['update'] = 1;
				$removeItems[] = array($goods['cid'], $goods['count'], 0);
			}
			Hapyfish2_Alchemy_HFC_Goods::updateUserGoods($uid, $userGoods);
		}
		if ( !empty($needScroll) ) {
			foreach	( $needScroll as $scroll ) {
				$userScroll[$scroll['cid']]['count'] -= $scroll['count'];
				$userScroll[$scroll['cid']]['update'] = 1;
				$removeItems[] = array($scroll['cid'], $scroll['count'], 0);
			}
			Hapyfish2_Alchemy_HFC_Scroll::updateUserScroll($uid, $userScroll);
		}
		if ( !empty($needStuff)	) {
			foreach	( $needStuff as	$stuff ) {
				$userStuff[$stuff['cid']]['count'] -= $stuff['count'];
				$userStuff[$stuff['cid']]['update'] = 1;
				$removeItems[] = array($stuff['cid'], $stuff['count'], 0);
			}
			Hapyfish2_Alchemy_HFC_Stuff::updateUserStuff($uid, $userStuff);
		}
		if ( !empty($needDecorInBag) ) {
			foreach	( $needDecorInBag as $decor	) {
				$userDecorInBag[$decor['cid']]['count'] -= $decor['count'];
				$userDecorInBag[$decor['cid']]['update'] = 1;
				$removeItems[] = array($decor['cid'], $decor['count'], 0);
			}
			Hapyfish2_Alchemy_HFC_Decor::updateBag($uid, $userDecorInBag);
		}
		if ( !empty($needWeapon) ) {
			foreach	( $needWeapon as $weapon ) {
				$ok = Hapyfish2_Alchemy_HFC_Weapon::delWeapon($uid, $weapon['wid']);
				if ( $ok ) {
					$removeItems[] = array($weapon['cid'], 1, $weapon['wid']);
				}
			}
		}

		$satisfactionChg = $orderInfo['satisfaction'];
		if ( $userCurStatisfaction > 99 ) {
			$satisfactionChg = 0;
		}

		//发送完成奖励
		foreach ( $awards as $v ) {
			$cid = $v[0];
			$count = $v[1];
			Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $cid, $count);
		}
		unset($orderList[$id]);
		$ok = Hapyfish2_Alchemy_HFC_Order::updateOrderList($uid, $orderList);
		if ( $ok ) {
			Hapyfish2_Alchemy_HFC_Order::updateSatisfaction($uid, $satisfactionChg);

			//根据满意度计算额外消费收入
			$tip = self::getTip($orderInfo['coin'], $userCurStatisfaction);
			$addCoin = $tip + $orderInfo['coin'];
			$addCoin = Hapyfish2_Alchemy_Bll_VipWelfare::getVipOrderCoin($uid, $addCoin);
			Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $addCoin);		//加金币
//			$exp = Hapyfish2_Alchemy_Bll_VipWelfare::getVipOrderExp($uid, $orderInfo['exp']);
			Hapyfish2_Alchemy_HFC_User::incUserExp($uid, $orderInfo['exp']);		//加经验

			if ( $tip > 0 ) {
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'tip', $tip);						//小费
			}
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'satisfaction', $satisfactionChg);	//满意度变化
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeItems', $removeItems);		//扣除物品

			//触发任务处理
            $event = array('uid' => $uid, 'data' => 1);
            Hapyfish2_Alchemy_Bll_TaskMonitor::completeOrder($event);
            
			//report log,统计玩家完成订单分布
			$logger = Hapyfish2_Util_Log::getInstance();
			$logger->report('312', array($uid, $orderInfo['cid'], $addCoin));
		}

		return 1;
	}

	/**
	 * 放弃订单
	 *
	 * @param $uid
	 * @param $id
	 * @param $request,是否请求订单:1:请求订单,放弃订单时用;0:不请求订单,超时自己离开时用
	 */
	public static function rejectOrder($uid, $id, $request)
	{
		//订单信息
		$orderInfo = Hapyfish2_Alchemy_HFC_Order::getRequestOne($uid, $id);
		if ( !$orderInfo ) {
			return -214;
		}

		if ( $orderInfo['state'] > 1 ) {
			return -218;
		}
		
		$userCurStatisfaction = Hapyfish2_Alchemy_HFC_Order::getSatisfaction($uid);
		if ( $request == 1 ) {
			$satisfactionChg = -1;
			if ( $userCurStatisfaction < 1 ) {
				$satisfactionChg = -$userCurStatisfaction;
			}
		}
		else {
			$satisfactionChg = -5;
			if ( $userCurStatisfaction < 5 ) {
				$satisfactionChg = -$userCurStatisfaction;
			}
		}

		$needSP = 1;
		$userSp = Hapyfish2_Alchemy_HFC_User::getUserSp($uid);
		if ( $userSp['sp'] < $needSP ) {
			return -208;
		}

		//放弃订单
    	/*$list = Hapyfish2_Alchemy_HFC_Order::getRequestList($uid);
    	unset($list[$id]);    	
    	$ok = Hapyfish2_Alchemy_HFC_Order::updateRequestList($uid, $list);
    	if ( !$ok ) {
    		return -200;
    	}*/
    	
		Hapyfish2_Alchemy_HFC_Order::updateSatisfaction($uid, $satisfactionChg);
		//Hapyfish2_Alchemy_HFC_Order::updateLastRequestTime($uid, 0);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'satisfaction', $satisfactionChg);	//满意度变化
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'tip', 0);						//小费

		if ( $request == 1 ) {
			Hapyfish2_Alchemy_HFC_User::decUserSp($uid, $needSP);
		}
		
		//report log,统计玩家直接放弃订单分布
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('205', array($uid, $orderInfo['cid']));
		
		return 1;
	}

	/**
	 * 宝石刷新订单
	 * 
	 * @param int $uid
	 */
	public static function refreshOrder($uid)
	{
		$gameData = Hapyfish2_Alchemy_Bll_BasicInfo::getGameData();
		$needGem = $gameData['orderRefreshGem'];
		$userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
		if ( $userGem <	$needGem ) {
			return -206;
		}
		
		Hapyfish2_Alchemy_HFC_Order::updateRequestList($uid, array());
		
		//最大订单数
		$userMaxOrderCount = Hapyfish2_Alchemy_HFC_User::getUserMaxOrderCount($uid);
		
		//增加订单
		for ( $i=0;$i<$userMaxOrderCount;$i++ ) {
			$order = self::getRandOrder($uid, -2);
			Hapyfish2_Alchemy_HFC_Order::requestOne($uid, $order);
		}
		
		$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
		//扣除用户宝石
		$gemInfo = array(
        		'uid' => $uid,
        		'cost' => $needGem,
        		'summary' => LANG_PLATFORM_BASE_TXT_5,
        		'user_level' => $userLevel,
        		'cid' => 0,
        		'num' => 1
        	);
		Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo, 9);
		
		$nowTime = time();
		//更新订单刷新时间
		Hapyfish2_Alchemy_HFC_Order::updateLastRequestTime($uid, $nowTime);

		//返回列表
		$list = self::getAllRequest($uid);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'list', $list);
		
		//report log,统计玩家接受订单分布
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('314', array($uid, $needGem));
        
		return 1;
	}
	
	/**
	 * 清理过期临时订单
	 *
	 * @param int $uid
	 */
	public static function clearRequestOrder($uid)
	{
		$requestList = Hapyfish2_Alchemy_HFC_Order::getRequestList($uid);
		$list = $requestList;

		$nowTime = time();
		$clearCount = 0;
		$satisfactionChg = 0;

		foreach ( $requestList as $v => $order ) {
			//好友家订单
			$friendOrder = substr($order['id'], -1, 1);
			if ( $friendOrder == 'f' ) {
				unset($list[$v]);
			}//自家过期订单
			else if ( $nowTime - $order['startTime'] >= self::ORDER_STRY_TIME ) {
				unset($requestList[$v]);
				unset($list[$v]);
				$clearCount++;
			}
		}
		if ( $clearCount > 0 ) {
			Hapyfish2_Alchemy_HFC_Order::updateRequestList($uid, $requestList);

			//用户当前满意度
			$userCurStatisfaction = Hapyfish2_Alchemy_HFC_Order::getSatisfaction($uid);
			$satisfactionChg = -5 * $clearCount;
			if ( $userCurStatisfaction < 5 * $clearCount ) {
				$satisfactionChg = -$userCurStatisfaction;
			}
			Hapyfish2_Alchemy_HFC_Order::updateSatisfaction($uid, $satisfactionChg);
		}
		
		return array('list'=>$list, 'satisfactionChg'=>$satisfactionChg);
	}

	/**
	 * 获取新的随机订单
	 * 
	 * @param int $uid
	 * @param int $awardType，订单奖励类型，1:白色,2:蓝色,3:紫色,4:橙色
	 */
	public static function getRandOrder($uid, $awardType, $userHelpId)
	{
		if ( $userHelpId == 3 ) {
			$order = self::_getFirstOrderForHelp();
		}
		else {
			//获取新的随机订单id，及信息
			$order = self::_getRandOrderInfo($uid, $awardType);
		}

		//随机订单角色
		$avatarIds = explode(',', $order['avatar_ids']);
		$randId = array_rand($avatarIds);
		$avatarId = $avatarIds[$randId];
		$avatarInfo = Hapyfish2_Alchemy_Cache_Basic::getAvatarInfo($avatarId);

		//随机订单名称及对白
		$avatarNames = explode(',', $order['avatar_name']);
		$randName = array_rand($avatarNames);
		$avatarNameId = $avatarNames[$randName];
		$avatarName = Hapyfish2_Alchemy_Cache_Basic::getAvatarName($avatarNameId);
		$rand1 = rand(1, 3);
		$rand2 = rand(1, 3);
		$rand3 = rand(1, 3);
		$field1 = 'start_dialog_'.$rand1;
		$field2 = 'finish_dialog_'.$rand2;
		$field3 = 'drop_dialog_'.$rand3;
		$dialog = $order[$field1].'&&'.$order[$field2].'&&'.$order[$field3];

		if ( $order['level'] == 1 ) {
			$satisfaction = 1;
		}
		else if ( $order['level'] == 2 ) {
			$satisfaction = 2;
		}
		else if ( $order['level'] == 3 ) {
			$satisfaction = 4;
		}
		else if ( $order['level'] == 4 ) {
			$satisfaction = 6;
		}
		else if ( $order['level'] == 5 ) {
			$satisfaction = 10;
		}
		
		$data = array('avatarFaceClass' => $avatarInfo['face'],
					  'avatarClassName' => $avatarInfo['class_name'],
					  'avatarName' => $avatarName,
					  'needs' => $order['needs'],
					  'awards' => $order['awards'],
					  'outTime' => $order['out_time'],
					  'startTime' => time(),
					  'totalTime' => $order['out_time'],
					  'dialog' => $dialog,
					  'coin' => $order['coin'],
					  'awardType' => $order['award_type'],
					  'exp' => $order['exp'],
					  'satisfaction' => $satisfaction,
					  'state' => 1,
					  'cid' => $order['cid']);
	
        if ( $userHelpId == 3 ) {
            $data['totalTime'] = -1;
        }
		return $data;
	}

	public static function _getFirstOrderForHelp()
	{
		$firstId = 10;
		$order = Hapyfish2_Alchemy_Cache_Basic::getOrderInfo($firstId);
		return $order;
	}
	
	/**
	 * 获取新的随机订单，订单ID
	 * 
	 * @param int $uid
	 * @param int $userLevel
	 */
	public static function _getRandOrderInfo($uid, $awardType)
	{
		$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
		
		//根据用户等级获取订单等级
		$orderPro = Hapyfish2_Alchemy_Cache_Basic::getOrderProByLevel($userLevel);
		$randArray = array();
		for ( $level = 1; $level < 11; $level++ ) {
			$field = 'level_'.$level;
			for ( $i=0; $i<$orderPro[$field]; $i++ ) {
				$randArray[] = $level;
			}
		}
		$randLevel = array_rand($randArray);
		$orderLevel = $randArray[$randLevel];

		//对应等级下，所有订单列表(用户等级，订单等级，奖励等级)
		$orderListByLevel = Hapyfish2_Alchemy_Cache_Basic::getOrderListByLevel($orderLevel);
		$allOrderList = array();
		foreach  ( $orderListByLevel as $n ) {
			if ( $n['need_level'] <= $userLevel && $n['award_type'] == $awardType ) {
				$allOrderList[$n['cid']] = $n;
			}
		}
		
		//所有订单对应的 需求图鉴id
		$allOrderIid = array();
		//以需求图鉴id为下标的订单临时数组
		$tempOrderList = array();
		foreach ( $allOrderList as $m ) {
			$iid = explode(',', $m['need_iid']);
			$allOrderIid = array_merge($allOrderIid, $iid);
			foreach ( $iid as $j ) {
				$tempOrderList[$j][] = $m;
			}
		}
		
		//用户已拥有图鉴列表
		$userIllustrations = Hapyfish2_Alchemy_Cache_Illustrations::getUserIllustrations($uid);

		//用户可用的订单列表（已有图鉴对应的订单）
		$orderList = array();
		foreach ( $userIllustrations as $v ) {
			$cid = $v['id'];
			if ( in_array($cid, $allOrderIid)) {
				foreach ( $tempOrderList[$cid] as $o ) {
					$orderList[] = $o;
				}
			}
		}
		$randOrder = array_rand($orderList);
		$order = $orderList[$randOrder];
	
		if ( $order == null ) {
			$tempAry = array_slice($orderListByLevel, 0, 1);
			$order = $tempAry[0];
		}
		return $order;
	}

	public static function _getRandAwardType()
	{
		//-1:自家普通订单,65/20/10/5
			$randAward = rand(1, 100);
			if ( $randAward <= 65) {
				$awardType = 1;
			}
			else if ( $randAward <= 85 ) {
				$awardType = 2;
			}
			else if ( $randAward <= 95 ) {
				$awardType = 3;
			}
			else {
				$awardType = 4;
			}
		return $awardType;
	}
	
	public static function getTip($coin, $satisfaction)
	{
		$level = floor($satisfaction/10);
		$addLevel = 0.02 * $level;
		$tip = round($coin * $addLevel);
		return $tip;
	}

	/**
	 * 查看好友家订单
	 * @param int $uid
	 * @param int $fid
	 */
	public static function getFriendOrder($uid, $fid)
	{
		/*$isFriend = Hapyfish2_Platform_Bll_Friend::isFriend($uid, $fid);
		if ( !$isFriend ) {
			return false;
		}*/
		
		/*$userLevel = Hapyfish2_Alchemy_HFC_User::getUserlevel($uid);
		if ( $userLevel < 15 ) {
			return false;
		}
		
		$friendLevel = Hapyfish2_Alchemy_HFC_User::getUserlevel($fid);
		if ( $friendLevel < 15 ) {
			return false;
		}*/
		
		$orderFids = Hapyfish2_Alchemy_HFC_Order::getOrderFids($uid);
		if ( count($orderFids) >= 10 ) {
			return false;
		}

		if ( isset($orderFids[$fid]) ) {
			return false;
		}
		
		$newId = $fid.'f';
		
		//是否已经生成订单
		$requestList = Hapyfish2_Alchemy_HFC_Order::getRequestList($uid);
		
		if ( isset($requestList[$newId]) ) {
			$order = $requestList[$newId];
			$order['id'] = (string)$order['id'];
		}
		else {
			//橙色订单
			$order = self::getRandOrder($uid, 4);
			//在临时订单列表记录好友家订单信息
			Hapyfish2_Alchemy_HFC_Order::requestOne($uid, $order, $newId);
			$order['id'] = (string)$newId;
		}
		
		return $order;
	}
	
	public static function getNeworder($uid,$orderStatus)
	{
		$list = array();
		for($i=0;$i<3;$i++){
			$awardType = self::_getRandAwardType();
			$newId = Hapyfish2_Alchemy_HFC_Order::getNewOrderId($uid);
			$order = self::_getRandOrderInfo($uid, $awardType);
			$avatarIds = explode(',', $order['avatar_ids']);
			$randId = array_rand($avatarIds);
			$avatarId = $avatarIds[$randId];
			$avatarInfo = Hapyfish2_Alchemy_Cache_Basic::getAvatarInfo($avatarId);
	
			//随机订单名称及对白
			$avatarNames = explode(',', $order['avatar_name']);
			$randName = array_rand($avatarNames);
			$avatarNameId = $avatarNames[$randName];
			$avatarName = Hapyfish2_Alchemy_Cache_Basic::getAvatarName($avatarNameId);
			$rand1 = rand(1, 3);
			$rand2 = rand(1, 3);
			$rand3 = rand(1, 3);
			$field1 = 'start_dialog_'.$rand1;
			$field2 = 'finish_dialog_'.$rand2;
			$field3 = 'drop_dialog_'.$rand3;
			$dialog = $order[$field1].'&&'.$order[$field2].'&&'.$order[$field3];
	
			if ( $order['level'] == 1 ) {
				$satisfaction = 1;
			}
			else if ( $order['level'] == 2 ) {
				$satisfaction = 2;
			}
			else if ( $order['level'] == 3 ) {
				$satisfaction = 4;
			}
			else if ( $order['level'] == 4 ) {
				$satisfaction = 6;
			}
			else if ( $order['level'] == 5 ) {
				$satisfaction = 10;
			}
			 $data = array('uid' => $uid,
	                      'id' => $newId,
	                      'needs' => $order['needs'],
	                      'outTime' => $order['out_time'],
	                      'startTime' => time(),
	                      'state' => 0,
	                      'awards' => $order['awards'],
	                      'dialog' => $dialog,
	                      'satisfaction' => $satisfaction,
	                      'coin' => $order['coin'],
	                      'avatarName' => $avatarName,
	                      'avatarFaceClass' =>$avatarInfo['face'],
	                      'avatarClassName' =>  $avatarInfo['class_name'],
	                      'exp' => $order['exp'],
	                      'cid' => $order['cid'],
	                      'totalTime' => $order['out_time'],
	                      'awardType' => $order['award_type']);
		$list[]= $data;
		}
		Hapyfish2_Alchemy_HFC_Order::updateOrderList($uid,$list);
		$orderStatus['lastTime'] = time();
		Hapyfish2_Alchemy_HFC_Order::updateOrderStatus($uid,$orderStatus);
	}
	
	public static function initorder($uid, $type)
	{
		$orderStatus = Hapyfish2_Alchemy_HFC_Order::getOrderStatus($uid);
		$time = time();
		if($type == 1){
			if($time - $orderStatus['lastTime'] >= 1200){
				return -709;
			}
			if($orderStatus['refreshCount'] >= 2){
				$userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
				if ( $userGem <	2 ) {
					return -206;
				}
				$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
				//扣除用户宝石
				$gemInfo = array(
		        		'uid' => $uid,
		        		'cost' => 2,
		        		'summary' => LANG_PLATFORM_BASE_TXT_5,
		        		'user_level' => $userLevel,
		        		'cid' => 0,
		        		'num' => 1
		        	);
				Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo, 10);
			}
			$orderStatus['refreshCount'] += 1;
			self::getNeworder($uid, $orderStatus);
			
		}else{
			if($time - $orderStatus['lastTime'] >= 1200){
				self::getNeworder($uid, $orderStatus);
			}
		}
//		$lastNum = $orderStatus['limit'] - $orderStatus['num'];
		$newOrders = array();
		$userOrder = Hapyfish2_Alchemy_HFC_Order::getOrderList($uid);
		$orderStatus = Hapyfish2_Alchemy_HFC_Order::getOrderStatus($uid);
		if($userOrder){
			foreach($userOrder as $orders){
				$newOrders[] = $orders;
			}
		}
		$reTime = $orderStatus['lastTime']+1200-$time>0?$orderStatus['lastTime']+1200-$time:0;
		$last = 2-$orderStatus['refreshCount']>0?2-$orderStatus['refreshCount']:0;
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'NewOrders', $newOrders);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'maxDeliverTimes', $orderStatus['limit']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'deliverTimes', $orderStatus['num']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'refreshTime', $reTime);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'freeRefreshTimes', $last);
		return 1;
	}
	
	public static function completeNewOrder($uid, $id, $wid)
	{
		$orderStatus = Hapyfish2_Alchemy_HFC_Order::getOrderStatus($uid);
		if($orderStatus['num'] >= $orderStatus['limit']){
			return -710;
		}
		$orderList = Hapyfish2_Alchemy_HFC_Order::getOrderList($uid);
		if ( !isset($orderList[$id]) ) {
			return -214;
		}
		$orderInfo = $orderList[$id];

		//用户当前满意度
		$userCurStatisfaction = Hapyfish2_Alchemy_HFC_Order::getSatisfaction($uid);

		//判断材料是否足够
		$needs = json_decode($orderInfo['needs']);
		$needGoods = array();
		$needScroll	= array();
		$needStuff = array();
		$needDecorInBag	= array();
		$needWeapon	= array();
		foreach	( $needs as	$need )	{
			$needCid = $need[0];
			$needCount = $need[1];
			$needType =	substr($needCid, -2, 1);
			//1->物品,2->卷轴,3->材料,4->工作台,5->装修,6->装备
			if ( $needType == 1	) {
				$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
				if ( !isset($userGoods[$needCid]) || $userGoods[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needGoods[] = array('cid' => $needCid,	'count'	=> $needCount);
			}
			else if	( $needType	== 2 ) {
				$userScroll	= Hapyfish2_Alchemy_HFC_Scroll::getUserScroll($uid);
				if ( !isset($userScroll[$needCid]) || $userScroll[$needCid]['count']	< $needCount ) {
					return -204;
				}
				$needScroll[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
			else if	( $needType	== 3 ) {
				$userStuff = Hapyfish2_Alchemy_HFC_Stuff::getUserStuff($uid);
				if ( !isset($userStuff[$needCid]) || $userStuff[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needStuff[] = array('cid' => $needCid,	'count'	=> $needCount);
			}
			else if	( $needType	== 5 ) {
				$userDecorInBag	= Hapyfish2_Alchemy_HFC_Decor::getBag($uid);
				if ( !isset($userDecorInBag[$needCid]) || $userDecorInBag[$needCid]['count']	< $needCount ) {
					return -204;
				}
				$needDecorInBag[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
			else if	( $needType	== 6 ) {
				$ids = explode(',', $wid);
				foreach($ids as $wids){
					if($wids != ''){
						$userNewWeapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $wids);
						if ( !$userNewWeapon || $userNewWeapon['durability'] < 1000) {
							return -204;
						}
						$needWeapon[] =	array('wid'	=> $wids, 'count' =>1, 'cid'=>$userNewWeapon['cid']);
					}
				}
				
			}
		}

		$awards = json_decode($orderInfo['awards']);

		//扣除需求物品
		if ( !empty($needGoods)	) {
			foreach	( $needGoods as	$goods ) {
				$userGoods[$goods['cid']]['count'] -= $goods['count'];
				$userGoods[$goods['cid']]['update'] = 1;
				$removeItems[] = array($goods['cid'], $goods['count'], 0);
			}
			Hapyfish2_Alchemy_HFC_Goods::updateUserGoods($uid, $userGoods);
		}
		if ( !empty($needScroll) ) {
			foreach	( $needScroll as $scroll ) {
				$userScroll[$scroll['cid']]['count'] -= $scroll['count'];
				$userScroll[$scroll['cid']]['update'] = 1;
				$removeItems[] = array($scroll['cid'], $scroll['count'], 0);
			}
			Hapyfish2_Alchemy_HFC_Scroll::updateUserScroll($uid, $userScroll);
		}
		if ( !empty($needStuff)	) {
			foreach	( $needStuff as	$stuff ) {
				$userStuff[$stuff['cid']]['count'] -= $stuff['count'];
				$userStuff[$stuff['cid']]['update'] = 1;
				$removeItems[] = array($stuff['cid'], $stuff['count'], 0);
			}
			Hapyfish2_Alchemy_HFC_Stuff::updateUserStuff($uid, $userStuff);
		}
		if ( !empty($needDecorInBag) ) {
			foreach	( $needDecorInBag as $decor	) {
				$userDecorInBag[$decor['cid']]['count'] -= $decor['count'];
				$userDecorInBag[$decor['cid']]['update'] = 1;
				$removeItems[] = array($decor['cid'], $decor['count'], 0);
			}
			Hapyfish2_Alchemy_HFC_Decor::updateBag($uid, $userDecorInBag);
		}
		if ( !empty($needWeapon) ) {
			foreach	( $needWeapon as $weapon ) {
				$ok = Hapyfish2_Alchemy_HFC_Weapon::delWeapon($uid, $weapon['wid']);
				if ( $ok ) {
					$removeItems[] = array($weapon['cid'], 1, $weapon['wid']);
				}
			}
		}

		$satisfactionChg = $orderInfo['satisfaction'];
		if ( $userCurStatisfaction > 99 ) {
			$satisfactionChg = 0;
		}

		//发送完成奖励
		foreach ( $awards as $v ) {
			$cid = $v[0];
			$count = $v[1];
			Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $cid, $count);
		}
		$orderList[$id]['state'] = 1;
		$ok = Hapyfish2_Alchemy_HFC_Order::updateOrderList($uid, $orderList);
		if ( $ok ) {
			Hapyfish2_Alchemy_HFC_Order::updateSatisfaction($uid, $satisfactionChg);

			//根据满意度计算额外消费收入
			$tip = self::getTip($orderInfo['coin'], $userCurStatisfaction);
			$addCoin = $orderInfo['coin'];
			$addCoin = Hapyfish2_Alchemy_Bll_VipWelfare::getVipOrderCoin($uid, $addCoin);
			Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $addCoin,15);		//加金币
//			$exp = Hapyfish2_Alchemy_Bll_VipWelfare::getVipOrderExp($uid, $orderInfo['exp']);
			if($orderInfo['exp'] > 0){
				Hapyfish2_Alchemy_HFC_User::incUserExp($uid, $orderInfo['exp']);		//加经验
			}
//			if ( $tip > 0 ) {
//				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'tip', $tip);						//小费
//			}
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'satisfaction', $satisfactionChg);	//满意度变化
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeItems', $removeItems);		//扣除物品

			//触发任务处理
            $event = array('uid' => $uid, 'data' => 1);
            Hapyfish2_Alchemy_Bll_TaskMonitor::completeOrder($event);
            
			//report log,统计玩家完成订单分布
			$logger = Hapyfish2_Util_Log::getInstance();
			$logger->report('312', array($uid, $orderInfo['cid'], $addCoin));
		}
		$orderStatus['num'] += 1;
		Hapyfish2_Alchemy_HFC_Order::updateOrderStatus($uid,$orderStatus);
		$newOrders = array();
		$userOrder = Hapyfish2_Alchemy_HFC_Order::getOrderList($uid);
		if($userOrder){
			foreach($userOrder as $orders){
				$newOrders[] = $orders;
			}
		}
		$last = 2-$orderStatus['refreshCount']>0?2-$orderStatus['refreshCount']:0;
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'NewOrders', $newOrders);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'maxDeliverTimes', $orderStatus['limit']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'deliverTimes', $orderStatus['num']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'refreshTime', 1200);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'freeRefreshTimes', $last);
		return 1;
	}
	
}