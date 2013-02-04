<?php

class Hapyfish2_Alchemy_Bll_Payment
{
    //模拟支付id
	public static $wbPreTestPayId = 't000141';
    //正式支付id
	public static $wbPrePayId = '1210009';
    
    //微博正服支付安全密码
    public static $wbPrePayPw = 'hf496700';

    /**
     * search pay order 查询订单
     *
     * @param  int $uid
     * @param  string $orderid
     * @return array
     */
	public static function getOrder($uid, $orderid)
	{
		try {
			$dalPayOrder = Hapyfish2_Alchemy_Dal_PayOrder::getDefaultInstance();
			return $dalPayOrder->getOrder($uid, $orderid);
		} catch (Exception $e) {
		    info_log('getOrder-Err:'.$e->getMessage(), 'Bll_Payment_Err');
			return null;
		}
	}

    public static function getOrderList($uid)
	{
		try {
			$dalPayOrder = Hapyfish2_Alchemy_Dal_PayOrder::getDefaultInstance();
			return $dalPayOrder->listOrder($uid);
		} catch (Exception $e) {
		    info_log('getOrderList-Err:'.$e->getMessage(), 'Bll_Payment_Err');
			return null;
		}
	}
	
    public static function createOrderId($uid)
    {
        //seconds 10 lens
        $ticks = time();

        //server id, 1 lens 0~9
        if (defined('SERVER_ID')) {
            $serverid = SERVER_ID%10;
        } else {
            $serverid = '0';
        }

        return $ticks . '_' . $serverid . '_' . $uid;
    }

    public static function createOrderIdForSinawb($uid)
    {
    	try {
			$dalSeq = Hapyfish2_Platform_Dal_SeqPayorder::getDefaultInstance();
            $seqId = $dalSeq->getSequence($uid);
    		
    		$dbNo = $uid % DATABASE_NODE_NUM;
    		$orderId = self::$wbPrePayId . str_pad($seqId, 8, '0', STR_PAD_LEFT) . $dbNo;
    	} catch (Exception $e) {
    		info_log('Hapyfish2_Alchemy_Bll_Payment:createOrderId:' . $e->getMessage(), 'payment-err');
    		$orderId = '';
    	}
    	return $orderId;
    }
    
	/**
     * create pay order 创建订单
     *
     * @param  int $uid
     * @param  int $type
     * @return array
     */
	public static function createOrder($uid, $type)
	{
		$settingInfo = Hapyfish2_Alchemy_Bll_Paysetting::getInfo($uid);
	    if (empty($settingInfo)) {
        	return null;
        }

        $section = $settingInfo['section'];
		if (!isset($section[$type])) {
			return null;
		}

		$select = $section[$type];
		if ($select['open'] != 1) {
			return null;
		}
		$amount = $select['amount'];
		$price = $amount;
        $gold = $select['gold']['num'];
        $itemName = $select['gold']['title'];
        if ($price <= 0 || empty($itemName)) {
            return null;
        }

        $orderId = self::createOrderId($uid);
        $code = $type*100 + 2;
        
		$order = array(
			'pname' => $itemName,
			'pnumber' => 1,
			'pcode' => $code,
			'amount' => $price,
			'orderid' => $orderId
		);
		
		//add db
		$info = array(
			'orderid' => $order['orderid'],
			'amount' => $order['amount'],
			'gold' => $gold,
			'order_time' => time(),
			'uid' => $uid
		);
		
		try {
			$info['user_level'] = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
            $dalPayOrder = Hapyfish2_Alchemy_Dal_PayOrder::getDefaultInstance();
            $dalPayOrder->regOrder($uid, $info);
	    } catch (Exception $e) {
		    info_log('createOrder-Err:'.$e->getMessage(), 'Hapyfish2_Alchemy_Bll_Payment');
			return null;
		}

        return $order;
	}

	/**
	 * create pay order 创建订单-初始化订单-新浪微博使用
	 *
	 * @param  int $uid
	 * @param  int $type
	 * @return array
	 */
	public static function regOrderForSinawb($uid, $type)
	{
		$settingInfo = Hapyfish2_Alchemy_Bll_Paysetting::getInfo($uid);
		if (empty($settingInfo)) {
			return null;
		}
	
		$section = $settingInfo['section'];
		if (!isset($section[$type])) {
			return null;
		}
	
		$select = $section[$type];
		if ($select['open'] != 1) {
			return null;
		}
		$amount = $select['amount'];
		$price = $amount;
		$gold = $select['gold']['num'];
		$itemName = $select['gold']['title'];
		if ($price <= 0 || empty($itemName)) {
			return null;
		}
	
		$orderId = self::createOrderIdForSinawb($uid);
		$code = $type*100 + 2;
	
		$order = array(
				'pname' => $itemName,
				'pnumber' => 1,
				'pcode' => $code,
				'amount' => $price,
				'orderid' => $orderId,
				'gold' => $gold
		);
		
		return $order;
	}
	

	/**
	 * add pay order 创建订单-订单添加数据库-新浪微博使用
	 *
	 * @param  int $uid
	 * @param  int $type
	 * @return array
	 */
	public static function addOrderForSinawb($uid, $order)
	{
		//add db
		$info = array(
				'orderid' => $order['orderid'],
				'amount' => $order['amount'],
				'gold' => $order['gold'],
				'order_time' => time(),
				'uid' => $uid,
				'trade_no' => $order['token']
		);
		
		try {
			$info['user_level'] = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
			$dalPayOrder = Hapyfish2_Alchemy_Dal_PayOrder::getDefaultInstance();
			$dalPayOrder->regOrder($uid, $info);
		} catch (Exception $e) {
			info_log('createOrder-Err:'.$e->getMessage(), 'Hapyfish2_Alchemy_Bll_Payment');
			return null;
		}
		
		return true;
	}
	
	/**
     * complete pay order 完成订单
     *
     * @param  int $uid
     * @param  string $orderid
     * @return int [0-success 1-has already completed 2-not found 3-failed]
     */
    public static function completeOrder($uid, $orderid, $param = array())
    {
        $order = self::getOrder($uid, $orderid);

        if (empty($order)) {
            return 2;
        }
        if ($order['status'] != 0) {
            return 1;
        }

        $ok = false;
	    $userGold = Hapyfish2_Alchemy_Bll_Gem::get($uid);
	    $orderid = $order['orderid'];
	    $gold = (int)$order['gold'];
	    $ok = Hapyfish2_Alchemy_Bll_Gem::add($uid, array('gem' => $gold, 'type' => 101));

        if ($ok) {
            $isFirstPay = 0;
            $time = time();
    		//更新订单状态
    		$updateinfo = array('status' => 1, 'complete_time' => $time);
    		$pid = '';
    		if (isset($param['pid'])) {
    			$updateinfo['trade_no'] = $param['pid'];
    			$pid = $param['pid'];
    		}
			try {
				$dalPayOrder = Hapyfish2_Alchemy_Dal_PayOrder::getDefaultInstance();
				$dalPayOrder->completeOrder($uid, $orderid, $updateinfo);

    			$dalPayLog = Hapyfish2_Alchemy_Dal_PayLog::getDefaultInstance();
    			$listPayed = $dalPayLog->listPayLog($uid, 1);
    			if (!$listPayed) {
                    $isFirstPay = 1;
    			}

    			$extraGold = self::getExtraGold($uid, $order['amount']);

    			//更新充值记录
    			$loginfo = array(
    				'uid' => $uid, 'orderid' => $orderid, 'pid' => $pid,
    				'amount' => $order['amount'], 'gold' => $gold, 'extra_gold' => $extraGold,
    				'create_time' => $time, 'user_level' => $order['user_level'],
    				'pay_before_gold' => $userGold, 'is_first_pay' => $isFirstPay,
    				'summary' => $order['amount'] . '|' . $gold
			    );
			    if ($extraGold > 0) {
			    	$loginfo['summary'] .= '+' . $extraGold;
			    }

    			$dalPayLog->insert($uid, $loginfo);
    			$userFight = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
				$fightLevel = $userFight['level'];
    			$log = Hapyfish2_Util_Log::getInstance();
        		$log->report('payLevel', array($uid, $fightLevel, $order['amount']));
			} catch (Exception $e) {
				info_log('[' . $uid . ':' . $orderid . ']' . $e->getMessage(), 'payment.err.completeorder.2');
			}
			$totalPay = Hapyfish2_Alchemy_HFC_User::getTotalPay($uid);
			$totalPay += $order['amount']/100;
			Hapyfish2_Alchemy_HFC_User::updateTotalPay($uid, $totalPay);
			$vip = new Hapyfish2_Alchemy_Bll_Vip();
			$vip->addGrowUp($uid, $order['amount']/100);
			Hapyfish2_Alchemy_Bll_EventGift::checkGift($uid,$order['amount']/100);
			//充值送
			self::chargeGift($uid, $order['amount']);
			
			if ('kaixin' == PLATFORM) {
				//开心平台-首充
				self::firstPay($uid, $order['amount']);
			}
			
			return 0;
		}

		info_log('[' . $uid . ':' . $orderid . ']' . 'completeOrderFailed', 'payment.err.confirm.3');
		return 3;
    }

    private static function getInclude($uid, $amount)
    {
    	$include = null;
   	    $settingInfo = Hapyfish2_Alchemy_Bll_Paysetting::getInfo($uid);
        if ($settingInfo) {
       		$section = $settingInfo['section'];
       		if (!empty($section)) {
       		    foreach ($section as $item) {
	        		if ($item['amount'] == $amount) {
	        			$include = $item['include'];
	        			break;
	        		}
	        	}
       		}
       	}
       	
       	return $include;
    }
    
    public static function getExtraGold($uid, $amount)
    {
    	$extraGold = 0;
    	$include = self::getInclude($uid, $amount);
    	if ($include != null && !empty($include)) {
    	    foreach ($include as $item) {
       			if ($item['cid'] == 1) {
       				$extraGold += $item['num'];
       			}
       		}
    	}
    	return $extraGold;
    }

    public static function chargeGift($uid, $amount)
	{
	    $content = '';
	    Hapyfish2_Alchemy_Event_Bll_Guild::addPayPoint($uid, $amount);
       	$include = self::getInclude($uid, $amount);
       	if ($include != null && !empty($include)) {
       		foreach ($include as $item) {
       			if ($item['cid'] > 0 && $item['num'] > 0) {
       				if ($item['cid'] == 1) {
       					Hapyfish2_Alchemy_Bll_Gem::add($uid, array('gem' => $item['num'], 'type' => 102));
       					$content[] = $item['num'].'宝石';
       				} else if ($item['cid'] == 2) {
       					Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $item['num']);
       					$content[] = $item['num'].'金币';
       				}
       				else {
       					Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $item['cid'], $item['num']);
       				}
       			}
       		}
       		if ($content) {
       			$content = '送' . join(',', $content);
       		} else {
       			$content = '';
       		}
       		info_log($uid . ':' . json_encode($include), 'pay-include');
       	}

	    return $content;
	}
	
	public static function firstPay($uid, $amount)
	{
		$cid1 = 1000415;
		$cid2 = 1000515;
		$cid3 = 1000615;
		$cid4 = 1000715;
		$get = array();
		$userGet =  Hapyfish2_Alchemy_Cache_EventGift::getFirshPay($uid);
		if($userGet){
			$get = json_decode($userGet['step'],true);
		}
		if(in_array($amount, $get)){
			return ;
		}
		if ($amount == 10) {
			Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $cid1, 1);
        } else if ($amount == 50) {
			Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $cid2, 1);
        } else if ($amount == 100) {
			Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $cid3, 1);
        } else if ($amount == 500) {
			Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $cid4, 1);
        }
        $get[] = $amount;
        $data['uid'] = $uid;
        $data['step'] = json_encode($get);
        $data['type'] = 2;
        Hapyfish2_Alchemy_Cache_EventGift::updateFrishPay($data);
	    return true;
	}
}