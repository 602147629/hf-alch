<?php

/**
 * callback method for kaixin action callback
 * @author liju.hu@hapyfish.com
 */
class CallbackController extends Zend_Controller_Action
{

	/**
	 * check sig
	 *
	 * @param string $sig
	 * @param string $secret
	 * @return bool true|false
	 */
	protected function checkSign($sig, $secret)
	{
		$queryStr = $_SERVER['QUERY_STRING'];
		$params = $_GET;
		unset($params['callback/action']);
		unset($params['sig']);
		ksort($params);
		$query = http_build_query($params);
		$localsig = md5($query.$secret);
		if ($localsig != $sig) {
			info_log($queryStr, 'callback.sig.err');
			return false;
		}
		return true;
	}
	
	/**
	 * handle payment
	 *
	 */
	protected function payment()
	{
		info_log('payment', 'pay');
		$puid = $this->_request->getParam('uid');
		$orderid = $this->_request->getParam('orderid');
		$from = $this->_request->getParam('from');
		$status = $this->_request->getParam('status');
		$pid = $this->_request->getParam('pid');
		$test = $this->_request->getParam('test');
		$ctime = $this->_request->getParam('ctime');
		
		$params = array(
			'uid' => $puid,
			'orderid' => $orderid,
			'from' => $from,
			'status' => $status,
			'pid' => $pid,
			'test' => $test,
			'ctime' => $ctime,
			'callbackkey' => 'pay'
		);
		
		info_log($orderid, 'callback.pay.ok');
		if ($status != '1') {
			echo 'ok';
			exit;
		}
		if ($test == '2') {
			info_log(json_encode($params), 'callback.pay.istest');
		}
		$uidInfo = Hapyfish2_Platform_Cache_UidMap::getUser($puid);
		if (!$uidInfo) {
			info_log(json_encode($params), 'callback.pay.err.puid');
			echo 'false';
			exit;
		}
		if (empty($pid)) {
			$pid = '';
		}
		$uid = $uidInfo['uid'];
		$result = Hapyfish2_Alchemy_Bll_Payment::completeOrder($uid, $orderid, array('pid' => $pid));
		
		if ($result == 0) {
			echo 'ok';
		} else if ($result == 3) {
			echo 'ok';
		} else {
			echo 'false';
		}
	}
	
	/**
	 * default callback action,for kaixin
	 *
	 */
	public function actionAction()
	{
		$sig = $this->_request->getParam('sig');
		if (empty($sig)) {
			exit;
		}
		$puid = $this->_request->getParam('uid');
		$uidInfo = Hapyfish2_Platform_Cache_UidMap::getUser($puid);
		$uid = $uidInfo['uid'];
		unset($_GET['callback/action']);
		
		$ok = $this->checkSign($sig, APP_SECRET);
		if (!$ok) {
			exit;
		}
		
		$callbackkey = $this->_request->getParam('callbackkey');
		
		$log = Hapyfish2_Util_Log::getInstance();
		$log->report($callbackkey, $_GET);
		
		switch ($callbackkey) {
			case 'pay':
				$this->payment();
				break;
			case 'invite':
				$num = $this->_request->getParam('num');
				$event = array('uid' => $uid, 'data' => $num);
				Hapyfish2_Alchemy_Bll_TaskMonitor::inviteFriend($event);
				$repot = array($uid);
				$log->report('410', $repot);
				break;
			case 'newsfeed':
				break;
			case 'sysnews':
				break;
			default:
				break;
		}
		
		exit;
	}

	//pay done for sinawb
	public function paydoneAction()
	{
		//支付安全密码  t000141
		$orderId = $_POST['order_id'];
		$appkey = $_POST['appkey'];
		$puid = $_POST['order_uid'];
		$amount = $_POST['amount'];
		$sign = $_POST['sign'];
	
		header("HTTP/1.0 401 Invalid");
		/*if ($sig != md5($orderId.'|'.$appkey.'|'.$puid.'|'.$amount.'|'.APP_SECRET)) {
		 echo 'validate failed';
		exit;
		}*/

		$rowUser = Hapyfish2_Platform_Bll_UidMap::getUser($puid);
		if (empty($rowUser)) {
			echo 'user not exist';
			exit;
		}
	
		//get status from platform api
		$rest = OpenApi_SinaWeibo_Client::getInstance();
		//$rest->setUser($this->info['session_key']);
		$sign = md5($orderId .'|'. APP_SECRET);

		if ( APP_SERVER_TYPE == 1 ) {
			$payStatus = $rest->getPayStatus($orderId, $puid, APP_KEY, $sign);
		}
		else {
			$payStatus = $rest->getPayStatusTest($orderId, $puid, APP_KEY, $sign);
		}
		
		info_log('$APP_KEY:'.APP_KEY, 'pay');
		if (empty($payStatus) || $payStatus['order_status']!=1) {
			echo 'not finished';
			exit;
		}
	
		info_log(Zend_Json::encode($_POST), 'payment_cb');
		$ok = Hapyfish2_Alchemy_Bll_Payment::completeOrder($rowUser['uid'], $orderId);
		info_log('ok:'.$ok, 'pay');
		if ($ok == 0 || $ok == 3) {
			if ($ok == 0) {
				//file log
				$log = Hapyfish2_Util_Log::getInstance();
				$log->report('wbpaydone', array($rowUser['uid'], $orderId, $amount, $puid));
			}
			header("HTTP/1.0 200 OK");
			echo 'OK';
			exit;
		}
	
		echo 'failed';
		exit;
	}
	
	public function completepayAction()
	{
		$openid = $this->_request->getParam('openid');
        $appid = $this->_request->getParam('appid');
        $ts = $this->_request->getParam('ts');
        $payitem = $this->_request->getParam('payitem');
        $token = $this->_request->getParam('token');
        $billno = $this->_request->getParam('billno');
        $version = $this->_request->getParam('version');
        $zoneid = $this->_request->getParam('zoneid');
        $providetype = $this->_request->getParam('providetype');
        $amt = $this->_request->getParam('amt');
        $payamt_coins = $this->_request->getParam('payamt_coins');
        $pubacct_payamt_coins = $this->_request->getParam('pubacct_payamt_coins');
        $acceptsig = $this->_request->getParam('sig');
		if ($appid != APP_ID) {
            $rst = array('ret'=>4, 'msg'=>'请求参数错误：appid');
            echo json_encode($rst);
    	    exit;
        }
        $params = array();
        $params['appid'] = $appid;
        $params['openid'] = $openid;
        $params['ts'] = $ts;
        $params['payitem'] = $payitem;
        $params['token'] = $token;
        $params['billno'] = $billno;
        $params['version'] = $version;
        $params['zoneid'] = $zoneid;
        $params['providetype'] = $providetype;
        $params['amt'] = $amt;
        $params['payamt_coins'] = $payamt_coins;
        $params['pubacct_payamt_coins'] = $pubacct_payamt_coins;
        
        $secret = APP_SECRET . '&';
        $sig = SnsSigCheck::makeSig( 'GET', '/callback/completepay', $params, $secret);
        if($sig != $acceptsig){
        	$rst = array('ret'=>4, 'msg'=>'请求参数错误：sig');
            echo json_encode($rst);
    	    exit;
        }
        $userInfo = Hapyfish2_Platform_Cache_UidMap::getUser($openid);
        if(empty($userInfo)){
        	$rst = array('ret'=>4, 'msg'=>'请求参数错误：openid');
            echo json_encode($rst);
    	    exit;
        }
        $result = Hapyfish2_Platform_Bll_QqpayByToken::completeBuy($uid, $params);
         if ($result == 0) {
            $rst = array('ret'=>0, 'msg'=>'OK');
        }
        else {
            if ($result == 2) {
                $rst = array('ret'=>2, 'msg'=>'token已过期');
            }
            else if ($result == 3) {
                $rst = array('ret'=>3, 'msg'=>'token不存在');
            }
            else {
                $rst = array('ret'=>1, 'msg'=>'系统繁忙');
            }
        }
        echo json_encode($rst);
    	exit;
	}
}