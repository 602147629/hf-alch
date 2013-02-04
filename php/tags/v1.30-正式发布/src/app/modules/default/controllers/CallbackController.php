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
		$fix = strpos($queryStr, '&&');
		if ($fix === false) {
			$queryStrArr = explode('&sig=', $queryStr);
		} else {
			$tmp = explode('&&', $queryStr);
			$queryStrArr = explode('&sig=', $tmp[1]);
		}
		
		$localsig = md5($queryStrArr[0].$secret);
		if ($localsig != $sig) {
			info_log($queryStr, 'callback.sig.err');
		}
		return $localsig == $sig;
	}
	
	/**
	 * handle payment
	 *
	 */
	protected function payment()
	{
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
		//$userGold = Hapyfish2_Island_HFC_User::getUserGold($uid);
		//$result = Hapyfish2_Island_Bll_Payment::confirm($uid, $orderid, array('pid' => $pid, 'pay_before_gold' => $userGold));
		
		if ($result == 0) {
			echo 'ok';
		} else if ($result == '3') {
			echo 'ok';
		} else {
			echo 'false';
		}
	}
	
	/**
	 * default callback action
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
	
	/**
	 * callback action for payment
	 *
	 */
	public function payAction()
	{
		//[uid] => 42086 //用户uid
		//[orderid] => 70613 //订单号
		//[from] => client
		//[status] => 1 //订单支付状态 1为支付成功
		//[pid] => 9317 //流水号 用于对帐
		//[test] => 2 //test为2说明该笔交易是用测试开心币支付的
		//[ctime] => 1297754870 //回调时间
		//[callbackkey] => pay //接口名称
		//[sig] => bc69d85df11476fd98fb601b8bf486bb //sig 用来判断该回调信息是由开心网发送的
		
		$sig = $this->_request->getParam('sig');
		if (empty($sig)) {
			exit;
		}
		
		unset($_GET['callback/pay']);
		
		$ok = $this->checkSign($sig, APP_SECRET);
		if (!$ok) {
			exit;
		}
		
		$callbackkey = $this->_request->getParam('callbackkey');
		if ($callbackkey != 'pay') {
			info_log($callbackkey, 'callback.pay.err.callbackkey');
			exit;
		}
		
		$log = Hapyfish2_Util_Log::getInstance();
		$log->report('pay', $_GET);
		
		$this->payment();
		
		exit;
	}
	
}