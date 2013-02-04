<?php

class PayController extends Hapyfish2_Controller_Action_Page
{
    protected $uid;

    protected $info;

    public function indexAction()
    {
		$uid = $this->uid;
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		$user['face'] = $user['figureurl'];
		$user['gem'] = Hapyfish2_Alchemy_Bll_Gem::get($uid);
		$this->view->user = $user;
		
		$section = array();
		$note = '';
		$settingInfo = Hapyfish2_Alchemy_Bll_Paysetting::getInfo($uid);
		if ($settingInfo) {
			$section = $settingInfo['section'];
			$note = $settingInfo['note'];
		}
		$this->view->section = $section;
		$this->view->note = $note;
		$this->render();
    }
    
	public function orderAction()
	{
		$uid = $this->uid;
		$type = $this->_request->getParam('type');
		if (empty($type)) {
			exit;
		}
		
		$order = Hapyfish2_Alchemy_Bll_Payment::createOrder($uid, $type);
		if ($order) {
			$params = array(
				'pname' => $order['pname'],
				'pnumber' => $order['pnumber'],
				'pcode' => $order['pcode'],
				'amount' => $order['amount'],
				'orderid' => $order['orderid'],
				'app_id' => APP_ID,
				'callback' => HOST . '/callback/pay',
			);
			$params['sig'] = md5($order['amount'].'&'.$order['orderid'].'&'.APP_SECRET);
			$result = array('status' => 1, 'para' => $params);
			$this->echoResult($result);
		}
		
		exit;
	}
}