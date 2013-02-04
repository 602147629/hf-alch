<?php

/**
 * Normal Base Controller
 *
 * @package  Hapyfish2_Controller_Action
 */
class Hapyfish2_Controller_Action_Normal extends Zend_Controller_Action
{
    protected $appInfo;

    /**
     * initialize basic data
     * @return void
     */
    public function init()
    {
    	$this->appInfo = Hapyfish2_Project_Bll_AppInfo::getAdvanceInfo();
    }

    protected function echoResult($data)
    {
    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	echo json_encode($data);
    	exit;
    }

    protected function echoError($errno, $errmsg)
    {
    	$result = array('errno' => $errno, 'errmsg' => $errmsg);
    	$this->echoResult($result);
    	exit;
    }

    public function noopAction()
    {
    	$data = array('id' => SERVER_ID, 'time' => time());
    	$this->echoResult($data);
    }

    protected function renderHTML($html)
    {
    	echo '<html><body>' . $html . '</body></html>';
    	exit;
    }

    protected function addCookie($name, $value)
    {
    	setcookie($name, $value, 0, '/', str_replace('http://', '.', HOST));
    }

    protected function enterGame($params = null, $url = null)
    {
    	if ($url == null) {
    		$url = $this->appInfo['app_link'];
    	}
    	if (!empty($params)) {
    		if (strpos($url, '?') === false) {
            	$url .= '?';
        	} else {
            	$url .= '&';
        	}
    		$url .= http_build_query($params);
    	}

    	Hapyfish2_Project_Bll_AppInfo::redirect($url);
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