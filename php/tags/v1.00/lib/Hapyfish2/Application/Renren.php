<?php

require_once 'Hapyfish2/Application/Abstract.php';

class Hapyfish2_Application_Renren extends Hapyfish2_Application_Abstract
{
    protected $_rest;
    protected $_puid;
    protected $_session_key;
    protected $_hfskey;
    protected $newuser;
    public $xn_params;
    public $hf_params;

    /**
     * Singleton instance, if null create an new one instance.
     *
     * @param Zend_Controller_Action $actionController
     * @return Hapyfish2_Application_Renren
     */
    public static function newInstance(Zend_Controller_Action $actionController)
    {
        if (null === self::$_instance) {
            self::$_instance = new Hapyfish2_Application_Renren($actionController);
        }
        return self::$_instance;
    }

    public function get_valid_xn_params($params, $timeout = null, $namespace = 'xn_sig')
    {
        if (empty($params)) {
            return array();
        }
        $prefix = $namespace . '_';
        $prefix_len = strlen($prefix);
        $xn_params = array();
        foreach ($params as $name => $val) {
            if (strpos($name, $prefix) === 0) {
                $xn_params[substr($name, $prefix_len)] = $val;
            }
        }
        // validate that the request hasn't expired. this is most likely
        // for params that come from $_COOKIE
        if ($timeout &&
         (! isset($xn_params['time']) || time() - $xn_params['time'] > $timeout)) {
            return array();
        }
        // validate that the params match the signature
        $signature = isset($params[$namespace]) ? $params[$namespace] : null;
        if (! $signature || (! $this->_rest->verifySignature($xn_params, $signature))) {
            return array();
        }
        return $xn_params;
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

    public function validate_xn_params()
    {
        $this->xn_params = $this->get_valid_xn_params($_GET, 48 * 3600, 'xn_sig');
        if (! $this->xn_params) {
            $this->xn_params = $this->get_valid_xn_params($_POST, 48 * 3600, 'xn_sig');
        }
        return ! empty($this->xn_params);
    }

    public function getPlatformUid()
    {
        return $this->_puid;
    }

    public function getRest()
    {
        return $this->_rest;
    }

    public function getSKey()
    {
        return $this->_hfskey;
    }

    public function isNewUser()
    {
        return $this->newuser;
    }
    
    public function getSKey()
    {
    	return $this->_hfskey;
    }

    /**
     * _init()
     *
     * @return void
     */
    protected function _init()
    {
        $request = $this->getRequest();
        if (! $this->validate_xn_params()) {
            throw new Exception('parameter validate failed');
        }
        if (! isset($this->xn_params['user_src'])) {
            $this->xn_params['user_src'] = 'rr';
        }
        
        if ($this->xn_params['added'] == 0) {
            $this->requireAuthPage();
        }

        $uid = $this->xn_params['user'];
        $this->hf_params = $this->get_hf_params($_GET);
        $this->_rest = OpenApi_RestFactory::getRest();
        if (! $this->_rest) {
            throw new Exception('system error');
        }
        $this->_rest->setLocation($this->xn_params['domain'], $this->xn_params['user_src']);

        //OK
        $this->_rest->setUser($this->xn_params['user'], $this->xn_params['session_key']);
        $this->_session_key = $this->xn_params['session_key'];
        $this->_puid = $this->xn_params['user'];
        $this->_appId = $request->getParam('xn_sig_app_id');
        $this->_appName = $this->_rest->app_name;
        $this->newuser = false;
    }

    public function requireAuthPage()
    {
        $next = 'http://apps.renren.com/' . APP_NAME . '/';
        $pageUrl = 'http://page.renren.com/' . APP_NAME;
        $staticUrl = STATIC_HOST;
        $imgUrl = $staticUrl . '/alchemy/image/install-bg.jpg';
        $appId = APP_ID;

        if (!empty($this->hf_params)) {
            $next .= '?hf_params=' . base64_encode(http_build_query($this->hf_params));
        }

        $content = <<<EOD
<div style="padding-bottom:10px;"><img border="0" src="$imgUrl" /></div>
<script type="text/javascript" src="$staticUrl/js/renren.js"></script>
<script type="text/javascript">
    var uiOpts = {
        url : "http://graph.renren.com/oauth/authorize",
        display : "iframe",
        style : {top: 110},
        params : {"response_type":"token","client_id":"$appId","scope":"publish_feed,publish_share,send_invitation,send_request,operate_like,photo_upload,create_album"},
        onSuccess: function(r){
            top.location = "$next";
        },
        onFailure: function(r){
        	top.location = "$pageUrl";
    	}
    };
    Renren.ui(uiOpts);
</script>
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
        }
        else {
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