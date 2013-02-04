<?php

require_once 'Hapyfish2/Application/Abstract.php';

class Hapyfish2_Application_Kaixin extends Hapyfish2_Application_Abstract
{
    protected $_rest;
    protected $_puid;
    protected $_session_key;
    protected $_hfskey;
    protected $newuser;
    public $kx_params;
    public $hf_params;

    /**
     * Singleton instance, if null create an new one instance.
     *
     * @param Zend_Controller_Action $actionController
     * @return Hapyfish2_Application_Kaixin
     */
    public static function newInstance(Zend_Controller_Action $actionController)
    {
        if (null === self::$_instance) {
            self::$_instance = new Hapyfish2_Application_Kaixin($actionController);
        }
        return self::$_instance;
    }
    
    public function get_hf_params($params, $namespace = 'hf')
    {
        if (empty($params)) {
            return array();
        }
        $prefix = $namespace . '_';
        $prefix_len = strlen($prefix);
        $hf_params = array();
        foreach ($params as $name => $val) {
            if (strpos($name, $prefix) === 0) {
                $hf_params[$name] = $val;
            }
        }
        return $hf_params;
    }
    
    protected function _parseSignedRequest()
    {
        $this->kx_params = array();
        $signed_request = $_POST['signed_request'];
        if (empty($signed_request)) {
        	return -1;
        }
    	list($encoded_sig, $payload) = explode('.', $signed_request, 2);
        $sig = self::base64UrlDecode($encoded_sig);
        $data = json_decode(self::base64UrlDecode($payload), true);
        if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
            return -2;
        }
        $expected_sig = hash_hmac('sha256', $payload, APP_SECRET, true);
        if ($sig !== $expected_sig) {
        	return -3;
        }
        $this->kx_params = $data;
        return 1;
    }
    
    static function base64UrlDecode($str)
    {
        return base64_decode(strtr($str . str_repeat('=', (4 - strlen($str) % 4)), '-_', '+/'));
    }

    public function getPlatformUid()
    {
    	return $this->_puid;
    }

    public function getRest()
    {
    	return $this->_rest;
    }

    public function isNewUser()
    {
    	return $this->newuser;
    }
    
    public function getSKey()
    {
    	return $this->_hfskey;
    }

    protected function _getUser($data)
    {
        $user = array();
        $user['uid'] = '' . $this->_userId;
        $user['puid'] = $data['uid'];
        $user['name'] = $data['name'];
        $user['figureurl'] = $data['logo50'];
        $sex = isset($data['gender']) ? $data['gender'] : '';
        if ($sex == '1') {
            $gender = 0;
        } else if ($sex == '0') {
            $gender = 1;
        } else {
            $gender = -1;
        }
        $user['gender'] = $gender;

        return $user;
    }

    /**
     * _init()
     *
     * @return void
     */
    protected function _init()
    {
        $code = $this->_parseSignedRequest();
        if ($code < 0) {
        	throw new Exception('parameter validate failed (' . $code . ')');
        }

        if (APP_SERVER_TYPE != 1) {
        	info_log(json_encode($_POST), 'kxparams');
        	info_log(json_encode($this->kx_params), 'kxparams');
        }
        
        if (!isset($this->kx_params['user_id'])) {
        	$this->requireAuthPage();
        }
        
        $this->hf_params = $this->get_hf_params($_GET);
        $session_key = $this->kx_params['oauth_token'];
        $puid = $this->kx_params['user_id'];
        $this->_rest = OpenApi_RestFactory::getRest();
        if (! $this->_rest) {
            throw new Exception('system error');
        }
        $this->_rest->setUser($puid, $session_key);
        
        $this->_puid = $puid;
        $this->_session_key = $session_key;
        $this->_appId = $this->_rest->app_id;
        $this->_appName = $this->_rest->app_name;
        $this->newuser = false;
    }
    
    public function requireAuthPage()
    {
        $title = '授权 | 炼金大冒险';
        $appId = APP_ID;
        $imgUrl = STATIC_HOST . '/alchemy/image/install-bg.jpg';

        $content = <<<EOD
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>$title</title>
<style>#bg{background:url("$imgUrl");width:800px;height:600px;}</style>
</head>
<script src="http://s.kaixin001.com.cn/js/openapp-8.js" language="javascript"></script>
<script>
function openAuthDlg(){
var p = {"app_id":$appId,"display":"iframe","scope":"basic create_records friends_intro send_message send_feed send_sysnews"};
KX.login(p);
}
</script>
<body onload="openAuthDlg();"><div id="bg"></div></body>
</html>
EOD;
        echo $content;
        exit();
    }

    protected function _updateInfo()
    {
    	$user = $this->_rest->usersGetInfo();
    	
        if (!$user) {
            throw new Hapyfish2_Application_Exception('get user info error');
        }
        $puid = $this->_puid;
        if ($puid != $user['puid']) {
            throw new Hapyfish2_Application_Exception('platform uid error');
        }

    	try {
    		$uidInfo = Hapyfish2_Platform_Cache_UidMap::getUser($puid);
    		//first coming
    		if (!$uidInfo) {
    			$uidInfo = Hapyfish2_Platform_Cache_UidMap::newUser($puid);
    			if (!$uidInfo) {
    				throw new Hapyfish2_Application_Exception('generate user id error');
    			}
    			$this->newuser = true;
    		}
    	} catch (Exception $e) {
    		info_log($e->getMessage(), 'app-kaixin');
    		throw new Hapyfish2_Application_Exception('get user id error');
    	}

        $uid = $uidInfo['uid'];
        if (!$uid) {
        	throw new Hapyfish2_Application_Exception('user id error');
        }
        $this->_userId = $uid;
        $user['uid'] = $uid;

        if ($this->newuser) {
        	Hapyfish2_Platform_Bll_User::addUser($user);
        	//add log
        	$logger = Hapyfish2_Util_Log::getInstance();
        	$logger->report('100', array($uid, $puid, $user['gender']));
        } else {
        	Hapyfish2_Platform_Bll_User::updateUser($uid, $user, true);
        }

        $fids = $this->_rest->friendGetAppFriends();
        if ($fids !== null) {
        	//这块可能会出现效率问题，fids很多的时候，memcacehd get次数会很多
        	//优化方案，先根据fid切分到相应的memcached组，用getMulti方法，减少次数
        	$fids = Hapyfish2_Platform_Bll_User::getUids($fids);
			if ($this->newuser) {
        		Hapyfish2_Platform_Bll_Friend::addFriend($uid, $fids);
        	} else {
        		Hapyfish2_Platform_Bll_Friend::updateFriend($uid, $fids);
        	}
        }
    }

    /**
     * run() - main mothed
     *
     * @return void
     */
    public function run()
    {
		$this->_updateInfo();
        //P3P privacy policy to use for the iframe document
        //for IE
        header('P3P: CP=CAO PSA OUR');
        $uid = $this->_userId;
        $puid = $this->_puid;
        $session_key = $this->_session_key;
        $t = time();
        $rnd = mt_rand(1, ECODE_NUM);

        //$sig = md5($uid . $puid . $session_key . $t . $rnd . APP_SECRET);
        //$skey = $uid . '.' . $puid . '.' . base64_encode($session_key) . '.' . $t . '.' . $rnd . '.' . $sig;
        $skey = Hapyfish2_Project_Bll_UserCertify::generateKey($uid, $puid, $session_key, $t, $rnd, APP_SECRET);
        $this->_hfskey = $skey;
        setcookie('hf_skey', $skey, 0, '/', str_replace('http://', '.', HOST));
    }
}