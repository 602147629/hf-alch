<?php

class OpenapiController extends Hapyfish2_Controller_Action_External
{    
	function check()
	{
		$uid = $this->_request->getParam('uid');
		if (empty($uid)) {
			$this->echoError(1001, 'uid can not empty');
		}
		
		$isAppUser = Hapyfish2_Alchemy_Cache_User::isAppUser($uid);
		if (!$isAppUser) {
			$this->echoError(1002, 'uid error, not app user');
			exit;
		}
		
		return $uid;
	}
	
	public function noopAction()
    {
    	$data = array('id' => SERVER_ID, 'time' => time());
    	$this->echoResult($data);
    }
	
	public function watchuserAction()
    {
		$uid = $this->check();
		$t = time();
		$sig = md5($uid . $t . APP_SECRET);
		
		$url = HOST . '/watch?uid=' . $uid . '&t=' . $t . '&sig=' . $sig;
		$data = array('url' => $url);
		$this->echoResult($data);
    }
	
	public function userinfoAction()
	{
		$uid = $this->check();
		$platformUser = Hapyfish2_Platform_Bll_User::getUser($uid);
		$alchemyUser = Hapyfish2_Alchemy_HFC_User::getUser($uid, array('exp' => 1, 'coin' => 1, 'level' => 1));
		$data = array(
			'face' => $platformUser['figureurl'],
			'puid' => $platformUser['puid'],
			'uid' => $uid,
			'nickname' => $platformUser['name'],
			'gender' => $platformUser['gender'],
			'level' => $alchemyUser['level'],
			'exp' => $alchemyUser['exp'],
			'coin' => $alchemyUser['coin'],
			'homeurl' => 'http://www.kaixin001.com/home/?uid=' . $platformUser['puid']
		);

		$data['status'] = Hapyfish2_Platform_Cache_User::getStatus($uid);
		
		$this->echoResult($data);
	}
	
	public function userinfobypuidAction()
	{
		$puid = $this->_request->getParam('puid');
		if (empty($puid)) {
			$this->echoError(1001, 'puid can not empty');
		}
		
		$platformUidInfo = Hapyfish2_Platform_Cache_UidMap::getUser($puid);

		if (!$platformUidInfo) {
			$this->echoError(1002, 'puid error, not app user');
			exit;
		}
		$uid = $platformUidInfo['uid'];
		
		$platformUser = Hapyfish2_Platform_Bll_User::getUser($uid);
		$alchemyUser = Hapyfish2_Alchemy_HFC_User::getUser($uid, array('exp' => 1, 'coin' => 1, 'level' => 1, 'gem' => 1));
		$data = array(
			'face' => $platformUser['figureurl'],
			'puid' => $platformUser['puid'],
			'uid' => $uid,
			'nickname' => $platformUser['name'],
			'gender' => $platformUser['gender'],
			'level' => $alchemyUser['level'],
			'exp' => $alchemyUser['exp'],
			'coin' => $alchemyUser['coin'],
			'gem' => $alchemyUser['gem'],
			'homeurl' => 'http://www.kaixin001.com/home/?uid=' . $platformUser['puid']
		);

		$data['status'] = Hapyfish2_Platform_Cache_User::getStatus($uid);
		
		$this->echoResult($data);
	}
	
	public function leveluplogAction()
	{
		$uid = $this->check();
		$logs = Hapyfish2_Alchemy_Bll_LevelUpLog::getAll($uid);
		if (!$logs) {
			$logs = array();
		}
		$data = array('logs' => $logs);
		$this->echoResult($data);
	}
		
	public function logininfoAction()
	{
		$uid = $this->check();
		$data = Hapyfish2_Alchemy_HFC_User::getUserLoginInfo($uid);
		$this->echoResult($data);
	}
	
	public function appinfoAction()
	{
		$info = Hapyfish2_Project_Cache_AppInfo::getInfo();
		$this->echoResult($info);
	}
	
	public function checkappstatusAction()
	{
		$uid = $this->_request->getParam('uid');
		if (empty($uid)) {
			$uid = 0;
		} else {
			$uid = $this->check();
		}
		$redirect = $this->_request->getParam('redirect');
		if ($redirect == '1') {
			$redirect = true;
		} else {
			$redirect = false;
		}
		$force = $this->_request->getParam('force');
		if ($force == '0') {
			$force = false;
		} else {
			$force = true;
		}
		
		$info = Hapyfish2_Project_Bll_AppInfo::checkStatus($uid, $redirect, $force);
		$this->echoResult($info);
	}
	
	public function userplatforminfoAction()
	{
		$uid = $this->check();
		$info = Hapyfish2_Platform_Bll_UserMore::getInfo($uid);
		$this->echoResult($info);
	}
	
}