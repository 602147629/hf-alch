<?php

class Hapyfish2_Alchemy_Bll_Payment
{

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

	/**
     * create pay order 创建订单
     *
     * @param  int $uid
     * @param  string $orderid
     * @param  int $amount
     * @param  int $gold
     * @param  int $time
     * @param  string $token
     * @return array
     */
	public static function createOrder($uid, $orderid, $amount, $gold, $time, $token='')
	{
	    try {
    	    $order = array();
    	    $userLevelInfo = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
    	    $order['orderid'] = $orderid;
    	    $order['amount'] = $amount;
    	    $order['gold'] = $gold;
    	    $order['token'] = $token;
    	    $order['order_time'] = $time;
    	    $order['status'] = 0;
    	    $order['uid'] = $uid;
    	    $order['user_level'] = $userLevelInfo;

            $dalPayOrder = Hapyfish2_Alchemy_Dal_PayOrder::getDefaultInstance();
            $dalPayOrder->regOrder($uid, $order);
	    } catch (Exception $e) {
		    info_log('createOrder-Err:'.$e->getMessage(), 'Bll_Payment_Err');
			return null;
		}

        return $order;
	}

	/**
     * complete pay order 完成订单
     *
     * @param  int $uid
     * @param  string $orderid
     * @return int [0-success 1-has already completed 2-not found 3-failed]
     */
    public static function completeOrder($uid, $orderid)
    {
        $order = self::getOrder($uid, $orderid);

        if (empty($order)) {
            return 2;
        }
        if ($order['status'] != 0) {
            return 1;
        }

        $ok = false;
        $extraGold = self::chargeExtraGold($uid, $order['amount']);
		try {
		    $userGold = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
		    $orderid = $order['orderid'];
		    $gold = (int)$order['gold'];
			$dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
			$dalUser->incGem($uid, $gold + $extraGold);
			Hapyfish2_Alchemy_HFC_User::reloadUserGem($uid);
			$ok = true;
		} catch (Exception $e) {
			info_log('[' . $uid . ':' . $orderid . ']' . $e->getMessage(), 'payment.err.confirm.1');
			return 3;
		}

        if ($ok) {
            $isFirstPay = 0;
            $time = time();
    		//更新订单状态
    		$updateinfo = array('status' => 1, 'complete_time' => $time);
            try {
                $dalPayOrder = Hapyfish2_Alchemy_Dal_PayOrder::getDefaultInstance();
    			$dalPayOrder->completeOrder($uid, $orderid, $updateinfo);

    			$dalPayLog = Hapyfish2_Alchemy_Dal_PayLog::getDefaultInstance();
    			$listPayed = $dalPayLog->listPayLog($uid, 1);
    			if (!$listPayed) {
                    $isFirstPay = 1;
    			}
    			//更新充值记录
    			$loginfo = array(
    				'uid' => $uid, 'orderid' => $orderid, 'pid' => $order['token'],
    				'amount' => $order['amount'], 'gold' => $gold, 'extra_gold' => $extraGold,
    				'create_time' => $time, 'user_level' => $order['user_level'],
    				'pay_before_gold' => $userGold, 'is_first_pay' => $isFirstPay,
    				'summary' => $order['amount'].'|'.$gold.'+'.$extraGold
			    );

    			$dalPayLog->insert($uid, $loginfo);
    		} catch (Exception $e) {
    			info_log('[' . $uid . ':' . $orderid . ']' . $e->getMessage(), 'payment.err.confirm.2');
    		}

			//充值送
			self::chargeGift($uid, $order['amount']);
			return 0;
		}

		info_log('[' . $uid . ':' . $orderid . ']' . 'completeOrderFailed', 'payment.err.confirm.3');
		return 3;
    }

    public static function chargeExtraGold($uid, $amount)
	{
	    $extra = 0;
	    if ($amount == 10) {

        } else if ($amount == 20) {

        } else if ($amount == 50) {

        } else if ($amount == 100) {

        }
	    return $extra;
	}

    public  static function chargeGift($uid, $amount)
	{
	    if ($amount == 10) {

        } else if ($amount == 20) {

        } else if ($amount == 50) {

        } else if ($amount == 100) {

        }
	    return true;
	}
}