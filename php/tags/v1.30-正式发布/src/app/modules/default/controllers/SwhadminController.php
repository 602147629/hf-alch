<?php

class SwhadminController extends Zend_Controller_Action
{

  //Admin Username & Password
    private $_admins = array('majun'=>'junma',
                       );

    private $_curAdmin;

	public function init()
	{
	    $loginU = $_SERVER['PHP_AUTH_USER'];
	    $loginP = $_SERVER['PHP_AUTH_PW'];
		if (!isset($loginU) || !isset($loginP)
            || !array_key_exists($loginU, $this->_admins) || $this->_admins[$loginU] != $loginP) {
			Header("WWW-Authenticate: Basic realm=Happy magic admin, Please Login");
			Header("HTTP/1.0 401 Unauthorized");

			echo <<<EOB
				<html><body>
				<h1>Rejected!</h1>
				<big>Wrong Username or Password!</big>
				</body></html>
EOB;
			exit;
		}

		$appInfo = Hapyfish2_Project_Bll_AppInfo::getAdvanceInfo();
	    $this->view->appTitle = $appInfo['app_title'];
		$this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
        $this->view->appId = APP_ID;
        $this->view->appKey = APP_KEY;
        $this->view->adminName = $loginU;
        $this->_curAdmin = $loginU;
	}

    protected function echoResult($data)
    {
        header("Cache-Control: no-store, no-cache, must-revalidate");
        echo json_encode($data);
        exit();
    }

	function indexAction()
	{
		$this->render();
	}
	
	function timegiftAction()
	{
		$zhanshi = array();
		$gongshou = array();
		$fashi = array();
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$list = $dal->getTimeGift();
		foreach($list as $k => $v){
			if($v['type'] == 1){
				$zhanshi[] = $v;
			}else if ($v['type'] == 2){
				$gongshou[] = $v;
			}else if ($v['type'] == 3){
				$fashi[] = $v;
			}
		}
		$this->view->zhanshi = $zhanshi;
        $this->view->gongshou = $gongshou;
        $this->view->fashi = $fashi;
		$this->render();
	}
	
	function addtAction()
	{
		$data['id'] = $this->_request->getParam('id');
		$data['next_id']  = $this->_request->getParam('next_id');
		$data['time']  = $this->_request->getParam('time');
		$data['list']  = $this->_request->getParam('list');
		$data['type']  = $this->_request->getParam('type');
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$dal->insertTgift($data);
		exit;
	}
	
	function deletetAction()
	{
		$data['id'] = $this->_request->getParam('id');
		$data['type']  = $this->_request->getParam('type');
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$dal->deleteg($data);
		exit;
	}
	
	 function levelgiftAction()
	 {
	 	$zhanshi = array();
		$gongshou = array();
		$fashi = array();
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$list = $dal->getLevelGift();
		foreach($list as $k => $v){
			if($v['type'] == 1){
				$zhanshi[] = $v;
			}else if ($v['type'] == 2){
				$gongshou[] = $v;
			}else if ($v['type'] == 3){
				$fashi[] = $v;
			}
		}
		$this->view->zhanshi = $zhanshi;
        $this->view->gongshou = $gongshou;
        $this->view->fashi = $fashi;
		$this->render();
	 }
	 
	function addlAction()
	{
		$data['level'] = $this->_request->getParam('id');
		$data['nextLevel']  = $this->_request->getParam('next_id');
		$data['awards']  = $this->_request->getParam('list');
		$data['type']  = $this->_request->getParam('type');
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$dal->insertLgift($data);
		exit;
	}
	
	function deletelAction()
	{
		$data['level'] = $this->_request->getParam('id');
		$data['type']  = $this->_request->getParam('type');
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$dal->deletel($data);
		exit;
	}
	
	function sevengiftAction()
	{
		$zhanshi = array();
		$gongshou = array();
		$fashi = array();
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$list = $dal->getSevenGift();
		foreach($list as $k => $v){
			if($v['type'] == 1){
				$zhanshi[] = $v;
			}else if ($v['type'] == 2){
				$gongshou[] = $v;
			}else if ($v['type'] == 3){
				$fashi[] = $v;
			}
		}
		$this->view->zhanshi = $zhanshi;
        $this->view->gongshou = $gongshou;
        $this->view->fashi = $fashi;
		$this->render();
	}
	
	function addsAction()
	{
		$data['day'] = $this->_request->getParam('id');
		$data['awards']  = $this->_request->getParam('list');
		$data['type']  = $this->_request->getParam('type');
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$dal->insertSgift($data);
		exit;
	}
	
	function deletesAction()
	{
		$data['day'] = $this->_request->getParam('id');
		$data['type']  = $this->_request->getParam('type');
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$dal->deletes($data);
		exit;
	}
	
	function packageAction()
	{
		$zhanshi = array();
		$gongshou = array();
		$fashi = array();
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$list = $dal->getpackage();
		foreach($list as $k => $v){
			if($v['type'] == 1){
				$zhanshi[] = $v;
			}else if ($v['type'] == 2){
				$gongshou[] = $v;
			}else if ($v['type'] == 3){
				$fashi[] = $v;
			}
		}
		$this->view->zhanshi = $zhanshi;
        $this->view->gongshou = $gongshou;
        $this->view->fashi = $fashi;
		$this->render();
	}
	
	function addpAction()
	{
		$data['cid'] = $this->_request->getParam('cid');
		$data['awards']  = $this->_request->getParam('awards');
		$data['type']  = $this->_request->getParam('type');
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$dal->insertpackage($data);
		exit;
	}
	
	function deletepAction()
	{
		$cid = $this->_request->getParam('cid');
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$dal->deletep($cid);
		exit;
	}
	
	function daliytaskAction()
	{
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$list = $dal->getdaliyactivity();
		$aclist = $dal->getactivityaward();
		$this->view->list = $list;
		$this->view->aclist = $aclist;
		$this->render();
	}
	
	function adddAction()
	{
		$data['tid'] = $this->_request->getParam('tid');
		$data['activity']  = $this->_request->getParam('activity');
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$dal->insertDaliyTask($data);
		exit;
	}
	
	function deletedAction()
	{
		$tid = $this->_request->getParam('tid');
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$dal->deleted($tid);
		exit;
	}
	
	function deleteaAction()
	{
		$id = $this->_request->getParam('id');
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$dal->deletea($id);
		exit;
	}
	
	function addaAction()
	{
		$data['awards'] = $this->_request->getParam('awards');
		$data['activity']  = $this->_request->getParam('ac');
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$dal->insertActivityAwards($data);
		exit;
	}
	
	function updateaAction()
	{
		$data['id']  = $this->_request->getParam('id');
		$data['awards'] = $this->_request->getParam('awards');
		$data['activity']  = $this->_request->getParam('activity');
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$dal->updateActivityAwards($data);
		exit;
	}
	
	function updatetocacheAction()
	{
		$type = $this->_request->getParam('type');
		$cache = Hapyfish2_Alchemy_Cache_Activity::getBasicMC();
		if($type == 'activity' ){
			$key = 'a:u:activity:';
			$key1 = 'a:u:activity:award:';
			$cache->delete($key);
			$cache->delete($key1);
		}else if($type == 'package'){
			$key = 'alchemy:bas:package:';
			$cache->delete($key);
		}
		echo "ok";
		exit;
	}
}