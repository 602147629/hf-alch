<?php

class ManageapiController extends Hapyfish2_Controller_Action_External
{    
	function check()
	{
		$uid = $this->_request->getParam('uid');
		if (empty($uid)) {
			$this->echoError(1001, 'uid can not empty');
		}
		
		$isAppUser = Hapyfish2_Ipanda_Cache_User::isAppUser($uid);
		if (!$isAppUser) {
			$this->echoError(1002, 'uid error, not app user');
			exit;
		}
		
		return $uid;
	}
	
	public function refreshAction()
	{
		$name = $this->_request->getParam('name');
		if (empty($name)) {
			$this->echoError(1001, 'name can not empty');
		}
		
		$result = Hapyfish2_Project_Bll_Refresh::all($name);
		$data = array('result' => $result);
		$this->echoResult($data);
	}
	
	public function refreshnoticeAction()
	{
		$type = $this->_request->getParam('type', '0');
		if ($type == '1') {
			Hapyfish2_Project_Bll_Notice::loadToMemcached();
		}
		Hapyfish2_Project_Bll_Notice::loadToAPC();
		$data = array('result' => 1);
		$this->echoResult($data);
	}
	
	public function updatenoticeAction()
	{
		$id = $this->_request->getParam('id');
		if (empty($id)) {
			$this->echoError(1001, 'id can not empty');
		}
		
		$params = $this->_request->getParams();
		$info = array();
		if (isset($params['position'])) {
			$info['position'] = $params['position'];
		}
		if (isset($params['title'])) {
			$info['title'] = $params['title'];
		}
		if (isset($params['link'])) {
			$info['link'] = $params['link'];
		}
		if (isset($params['priority'])) {
			$info['priority'] = $params['priority'];
		}
		if (isset($params['opened'])) {
			$info['opened'] = $params['opened'];
		}
		$info['create_time'] = time();
		
		$ok = Hapyfish2_Project_Bll_Notice::update($id, $info);
		$result = $ok ? 1 : 0;
		$data = array('result' => $result);
		$this->echoResult($data);
	}
	
	public function addnoticeAction()
	{
		$params = $this->_request->getParams();
		$info = array();
		if (isset($params['position'])) {
			$info['position'] = $params['position'];
		}
		if (isset($params['title'])) {
			$info['title'] = $params['title'];
		}
		if (isset($params['link'])) {
			$info['link'] = $params['link'];
		}
		if (isset($params['priority'])) {
			$info['priority'] = $params['priority'];
		}
		if (isset($params['opened'])) {
			$info['opened'] = $params['opened'];
		}
		if (isset($params['time'])) {
			$info['create_time'] = $params['time'];
		} else {
			$info['create_time'] = time();
		}
		
		$ok = Hapyfish2_Project_Bll_Notice::add($info);
		$result = $ok ? 1 : 0;
		$data = array('result' => $result);
		$this->echoResult($data);
	}
	
	public function getnoticeAction()
	{
		$notice = Hapyfish2_Project_Cache_Notice::getNoticeList();
		$data = array('notice' => $notice);
		$this->echoResult($data);
	}
	
	public function changeuserstatusAction()
	{
		$uid = $this->check();
		$status = $this->_request->getParam('status', 0);
		$ok = Hapyfish2_Platform_Cache_User::updateStatus($uid, $status, true);
		$result = $ok ? 1 : 0;
		$data = array('result' => $result);
		$this->echoResult($data);
	}
	
	public function blockuserAction()
	{
		$uid = $this->check();
		$status = Hapyfish2_Platform_Cache_User::getStatus($uid);
		if ($status != 0) {
			$this->echoError(1101, 'user status is not normal');
		}
		$ok = Hapyfish2_Alchemy_Bll_Block::add($uid, 1, 1);
		$result = $ok ? 1 : 0;
		$data = array('result' => $result);
		$this->echoResult($data);
	}
	
	public function unblockuserAction()
	{
		$uid = $this->check();
		$status = Hapyfish2_Platform_Cache_User::getStatus($uid);
		if ($status <= 0) {
			$this->echoError(1102, 'user status is normal');
		}
		$ok = Hapyfish2_Alchemy_Bll_Block::add($uid, 0, 1);
		$result = $ok ? 1 : 0;
		$data = array('result' => $result);
		$this->echoResult($data);
	}
	
	public function getuserstatusAction()
	{
		$uid = $this->check();
		$status = Hapyfish2_Platform_Cache_User::getStatus($uid);
		$data = array('uid' => $uid, 'status' => $status);
		$this->echoResult($data);
	}
	
	public function clearuserAction()
	{
		$uid = $this->check();
		$security = $this->_request->getParam('security', '');
		$ok = Hapyfish2_Alchemy_Bll_Manage::clearUser($uid);
		
		$result = $ok ? 1 : 0;
		$data = array('result' => $result);
		$this->echoResult($data);
	}
	
	private function trimString($str)
	{
		$str = trim($str);
		$str = str_replace("\r", '', $str);
		$str = str_replace("\n", '', $str);
		$str = str_replace("\t", '', $str);
		$str = str_replace(" ", '', $str);
		return trim($str);
	}
		
	public function refreshappinfoAction()
	{
		Hapyfish2_Project_Bll_AppInfo::loadToAPC();
		$data = array('result' => 1);
		$this->echoResult($data);
	}
	
	public function updateappinfoAction()
	{		
		$info = array();
		$params = $this->_request->getParams();
		if (!isset($params['app_id']) || empty($params['app_id'])) {
			$this->echoError(1001, 'app_id can not empty');
		}
		
		$appId = $params['app_id'];
		
		if (isset($params['app_name'])) {
			$info['app_name'] = $this->trimString($params['app_name']);
		}
		if (isset($params['app_title'])) {
			$info['app_title'] = $this->trimString($params['app_title']);
		}
		if (isset($params['app_link'])) {
			$info['app_link'] = $params['app_link'];
		}
		if (isset($params['app_host'])) {
			$info['app_host'] = $params['app_host'];
		}
		if (isset($params['app_status'])) {
			$info['app_status'] = $params['app_status'];
		}
		if (isset($params['maintance_notice'])) {
			$info['maintance_notice'] = $params['maintance_notice'];
		}
		if (isset($params['white_ip_list'])) {
			$info['white_ip_list'] = $this->trimString($params['white_ip_list']);
		}
		if (isset($params['black_ip_list'])) {
			$info['black_ip_list'] = $this->trimString($params['black_ip_list']);
		}
		if (isset($params['dev_id_list'])) {
			$info['dev_id_list'] = $this->trimString($params['dev_id_list']);
		}
		if (isset($params['test_id_list'])) {
			$info['test_id_list'] = $this->trimString($params['test_id_list']);
		}
		if (isset($params['external_api_key'])) {
			$info['external_api_key'] = $this->trimString($params['external_api_key']);
		}
		if (isset($params['external_api_secret'])) {
			$info['external_api_secret'] = $this->trimString($params['external_api_secret']);
		}
		if (isset($params['external_open'])) {
			$info['external_open'] = $params['external_open'];
		}
		$info['update_time'] = time();
		
		$ok = Hapyfish2_Project_Bll_AppInfo::update($appId, $info);
		if ($ok) {
			Hapyfish2_Project_Bll_Refresh::all('appinfo');
		}
		$result = $ok ? 1 : 0;
		$data = array('result' => $result);
		$this->echoResult($data);
	}
}