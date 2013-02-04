<?php

class InvitesinawbController extends Hapyfish2_Controller_Action_Page
{
    protected $uid;

    protected $info;

    public function init()
    {
    	$info = $this->vailid();
    	if (!$info) {
    		echo '<html><body><script type="text/javascript">window.top.location="http://game.weibo.com/'.APP_NAME.'/";</script></body></html>';
    		exit;
    	}
    
    	$this->info = $info;
    	$this->uid = $info['uid'];
    
    	$this->view->baseUrl = $this->_request->getBaseUrl();
    	$this->view->staticUrl = STATIC_HOST;
    	$this->view->hostUrl = HOST;
    	$this->view->appId = APP_ID;
    	$this->view->appKey = APP_KEY;
    	$this->view->uid = $info['uid'];
    	$this->view->platformUid = $info['puid'];
    }

    protected function vailid()
    {
    	$skey = isset($_COOKIE['hf_skey'])?$_COOKIE['hf_skey']:'';
    	if (!$skey) {
    		return false;
    	}
    
    	$tmp = explode('.', $skey);
    	if (empty($tmp)) {
    		return false;
    	}
    	$count = count($tmp);
    	if ($count != 5 && $count != 6) {
    		return false;
    	}
    
    	$uid = $tmp[0];
    	$puid = $tmp[1];
    	$session_key = base64_decode($tmp[2]);
    	$t = $tmp[3];
    
    	$rnd = -1;
    	if ($count == 5) {
    		$sig = $tmp[4];
    		$vsig = md5($uid . $puid . $session_key . $t . APP_SECRET);
    		if ($sig != $vsig) {
    			return false;
    		}
    	} else if ($count == 6) {
    		$rnd = $tmp[4];
    		$sig = $tmp[5];
    		$vsig = md5($uid . $puid . $session_key . $t . $rnd . APP_SECRET);
    		if ($sig != $vsig) {
    			return false;
    		}
    	}
    
    	//max long time one day
    	if (time() > $t + 86400) {
    		return false;
    	}
    
    	return array('uid' => $uid, 'puid' => $puid, 'session_key' => $session_key,  't' => $t, 'rnd' => $rnd);
    }
    
    public function indexAction()
    {
		$uid = $this->uid;
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		$user['face'] = $user['figureurl'];
		$user['gem'] = Hapyfish2_Alchemy_Bll_Gem::get($uid);
		$this->view->user = $user;
		
		$this->render();
    }

    public function sendAction()
    {
    
    	echo '<script type="text/javascript">parent.initInvite("1");</script>邀请发送成功！';
    	exit;
    
    	$uid = $this->uid;
    	$puids = $this->_request->getParam('ids');
    	$aryPuid = explode(',', $puids);
    	if (empty($aryPuid)) {
    		echo 'Failed';
    		exit();
    	}
    	//info_log($puids,'inviteSends');
    	//info_log(json_encode($_REQUEST), 'inviteSends');
    	echo '<html><body>邀请已发送。3秒后自动跳转。<script type="text/javascript">window.setTimeout(function(){window.top.location="http://game.weibo.com/'.APP_NAME.'/";},3000);</script></body></html>';
    	//echo $puids.' invitation sended.<a href="http://game.weibo.com/'.APP_NAME.'/">back</a>';
    	exit;
    
    	try {
    		//invite send logs
    		/* $dalInvite = Hapyfish2_Island_Event_Dal_InviteSend::getDefaultInstance();
    		$now = time();
    		foreach ($aryPuid as $puid) {
    			$rowInvite = $dalInvite->getInviteSend($puid, $uid);
    			if (empty($rowInvite)) {
    				$dalInvite->insert($puid, array('invite_puid' => $puid, 'uid' => $uid, 'create_time' => $now));
    			}
    		} */

    		$num = count($aryPuid);
    		if ( $num > 0 ) {
	    		$event = array('uid' => $uid, 'data' => $num);
	    		Hapyfish2_Alchemy_Bll_TaskMonitor::inviteFriend($event);
	    		$repot = array($uid);
	    		$log = Hapyfish2_Util_Log::getInstance();
	    		$log->report('410', $repot);
    		}
    	}
    	catch (Exception $e) {
    		info_log($e->getMessage(), 'send-invite-err');
    	}
    
    	echo '<a href="javascript:void(0);" onclick="HFApp.invite();" target="_top">Back&gt;&gt;</a>';
    	exit();
    }
}