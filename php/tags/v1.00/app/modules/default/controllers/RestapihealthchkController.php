<?php

class RestapihealthchkController extends Zend_Controller_Action
{
    protected $uid;
    protected $info;
    protected $rest;

    public function init()
    {
        $info = $this->vaild();
        if (! $info) {
            $result = array('status' => '-1', 'content' => 'vaild error,pls enter game first.');
            $this->echoResult($result);
        }

        $this->info = $info;
        $this->uid = $info['uid'];

        $rest = Renren_Client::getInstance();
        $rest->setUser($info['puid'], $info['session_key']);
        $this->rest = $rest;
        //$data = array('uid' => $info['uid'], 'puid' => $info['puid'], 'session_key' => $info['session_key']);


        $controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
    }

    protected function vaild()
    {
        $skey = $_COOKIE['hf_skey'];
    	return Hapyfish2_Project_Bll_UserCertify::checkKey($skey, APP_SECRET);
    }

    protected function echoResult($data)
    {
        if ($data) {
            if (! is_array($data)) {
                $rst = $data;
                $data = array();
                $data['rst'] = $rst;
            }
            $data['errno'] = 0;
            echo json_encode($data);
        }
        else {
            $this->echoError(- 1, 'get failed');
        }
        exit();
    }

    protected function echoError($errno, $errmsg)
    {
        $result = array('errno' => $errno, 'errmsg' => $errmsg);
        echo json_encode($result);
        exit();
    }

    public function noopAction()
    {
        $data = array('id' => SERVER_ID, 'time' => time());
        $this->echoResult($data);
    }

    protected function getRestList()
    {
        $list = array(
        	'getuser' => 'getuser',
        	'getfriend' => 'getfriend',
        	'isfan' => 'isfan',
        	'gettopuser' => 'gettopuser',
        	'gettopcoin' => 'gettopcoin'
        );
        return $list;
    }

    public function listAction()
    {
        $list = $this->getRestList();
        $html = '';
        foreach ($list as $key=>$val) {
            $url = HOST . '/restapihealthchk/' . $val;
            $html .= "$key: <a href='$url' target='_blank'>click to test</a><br/>";
        }

        $html = 'Restful Api To Check:<br/>' . $html;
        echo $html;
        exit;
    }

    public function getuserAction()
    {
        $data = $this->rest->jianghu_getUser();
        $this->echoResult($data);
    }

    public function getfriendAction()
    {
        $data = $this->rest->jianghu_getFriends();
        $this->echoResult($data);
    }

    public function isfanAction()
    {
        $data = $this->rest->isFan();
        $this->echoResult($data);
    }

    public function gettopuserAction()
    {
        $data = $this->rest->top_getUser();
        $this->echoResult($data);
    }

    public function gettopcoinAction()
    {
        $data = $this->rest->jianghu_getCoinsSum();
        $this->echoResult($data);
    }

	/**
     * proxy for undefined methods
     * override
     * @param string $methodName
     * @param array $args
     */
    public function __call($methodName, $args)
    {
        echo 'No This `'.$methodName.'` Method';
    }
}