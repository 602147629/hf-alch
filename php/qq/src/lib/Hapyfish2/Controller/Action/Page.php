<?php

/**
 * Page Base Controller
 * user must login, identity not empty
 *
 * @package  Hapyfish2_Controller_Action
 */
class Hapyfish2_Controller_Action_Page extends Zend_Controller_Action
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
    	/*
    	$appInfo = Hapyfish2_Project_Bll_AppInfo::getAdvanceInfo();
        if (!$info) {
			if ($appInfo) {
				Hapyfish2_Project_Bll_AppInfo::redirect($appInfo['app_link'], true);
			}
			exit;
        }

        Hapyfish2_Project_Bll_AppInfo::checkStatus($info['uid'], true, true, $appInfo);
		*/

        $this->info = $info;
        $this->uid = $info['uid'];

        $data = array('uid' => $info['uid'], 'puid' => $info['puid'], 'session_key' => $info['session_key']);
        $context = Hapyfish2_Util_Context::getDefaultInstance();
        $context->setData($data);

        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
        $this->view->skey = $this->skey;
        $this->view->appId = APP_ID;
        $this->view->appKey = APP_KEY;
    }

    protected function vailid()
    {
    	$skey = $_COOKIE['hf_skey'];
    	return Hapyfish2_Project_Bll_UserCertify::checkKey($skey, APP_SECRET);
    }

    protected function echoResult($data)
    {
    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	echo json_encode($data);
    	exit;
    }

    protected function renderHTML($html)
    {
    	echo '<html><body>' . $html . '</body></html>';
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
        echo 'No This Method';
    }
}