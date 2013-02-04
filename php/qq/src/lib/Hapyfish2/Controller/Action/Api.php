<?php

/**
 * Api Base Controller
 * user must login, identity not empty
 *
 * @package  Hapyfish2_Controller_Action
 */
class Hapyfish2_Controller_Action_Api extends Zend_Controller_Action
{
    protected $uid;

    protected $info;

    protected $skey;

    /**
     * initialize basic data
     * @return void
     */
    public function init()
    {
    	$info = $this->vailid();
        if (!$info) {
        	$result = array('status' => '-1', 'content' => 'vaild error');
			$this->echoResult($result);
        }

        $this->info = $info;
        $this->uid = $info['uid'];

        if (!Hapyfish2_Alchemy_Cache_User::isSinglePointLogin($this->uid, $this->skey)) {
        	$vaildKey = Hapyfish2_Alchemy_Cache_User::getLoginUserSkeyWatch($this->uid);
            if ($this->skey != $vaildKey) {
            	info_log($this->uid . ':' . $vaildKey . ':' . $this->skey, 'single.login');
                $this->echoError(-100);
            }
        }
        //Hapyfish2_Project_Bll_AppInfo::checkStatus($info['uid'], true, true);
        $data = array('uid' => $info['uid'], 'puid' => $info['puid'], 'session_key' => $info['session_key']);
    	if ( PLATFORM == 'pengyou' || PLATFORM == 'qzone' ) {
			$pfkey = $_COOKIE['pfkey'];
			$data = array('uid' => $info['uid'], 'puid' => $info['puid'], 'session_key' => $info['session_key'],'pfkey'=>$pfkey);
        }
        $context = Hapyfish2_Util_Context::getDefaultInstance();
        $context->setData($data);
        Hapyfish2_Alchemy_Bll_UserResult::setUser($info['uid']);

    	$controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
    }

    protected function vailid()
    {
    	$skey = $_COOKIE['hf_skey'];
    	$this->skey = $skey;
    	return Hapyfish2_Project_Bll_UserCertify::checkKey($skey, APP_SECRET);
    }

	protected function flush()
	{
    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	$data = Hapyfish2_Alchemy_Bll_UserResult::flush();
    	echo json_encode($data);
    	exit;
	}

    protected function echoResult($data)
    {
    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	echo json_encode($data);
    	exit;
    }

    protected function echoError($status, $content = null)
    {
 		if ($content === null) {
			$content = 'serverWord_' . abs($status);
		}
		$result = array('status' => $status, 'content' => $content);

    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	echo json_encode(array('result' => $result));
    	exit;
    }

    /**
     * proxy for undefined methods
     * override
     * @param string $methodName
     * @param array $args
     */
    public function __call($methodName, $args)
    {
        echo 'No This Method:' . $methodName;
    }
}