<?php
/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

class Hapyfish2_Application_Abstract
{
    /**
     * $_actionController - ActionController reference
     *
     * @var Zend_Controller_Action
     */
    protected $_actionController = null;
    /**
     * application id
     * @var string
     */
    protected $_appId = '';
    /**
     * application name
     * @var string
     */
    protected $_appName = '';
    /**
     * application owner id
     * @var string
     */
    protected $_userId = '';
    /**
     * Singleton instance
     *
     * Marked only as protected to allow extension of the class. To extend,
     * simply override {@link getInstance()}.
     *
     * @var Bll_Application
     */
    protected static $_instance = null;
    
    protected $_rest = null;
    protected $_puid = '';
    protected $_session_key = '';
    protected $_hfskey = '';
    protected $newuser = false;
    
    public $hf_params = array();

    /**
     * __construct() -
     *
     * @param Zend_Controller_Action $actionController
     * @return void
     */
    public function __construct(Zend_Controller_Action $actionController)
    {
        $this->_actionController = $actionController;
        $this->_init();
    }

    /**
     * get singleton instance
     *
     * @return Bll_Application
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            throw new Exception('Application instance has not been created! Please use "newInstance" to create one.');
        }
        return self::$_instance;
    }

    /**
     * Get request object
     *
     * @return Zend_Controller_Request_Abstract $request
     */
    public function getRequest()
    {
        return $this->_actionController->getRequest();
    }

    /**
     * get application id
     *
     * @return string
     */
    public function getAppId()
    {
        return $this->_appId;
    }

    public function getUserId()
    {
        return $this->_userId;
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

    public function getSessionKey()
    {
        return $this->_session_key;
    }
    
    public function isNewUser()
    {
        return $this->newuser;
    }
    
    public function get_hf_params($params, $namespace = 'hf')
    {
        if (empty($params)) {
            return array();
        }
        $prefix = $namespace . '_';
        $hf_params = array();
        foreach ($params as $name => $val) {
            if (strpos($name, $prefix) === 0) {
                $hf_params[$name] = $val;
            }
        }
        return $hf_params;
    }
    
    public function getPlatform()
    {
    	if (defined('PLATFORM')) return PLATFORM;
    	return '';
    }

    protected function _init()
    {
    }

    /**
     * run() - main mothed
     * 
     * @return void
     */
    public function run()
    {
    }

    /**
     * Redirect to another URL
     *
     * Proxies to {@link Zend_Controller_Action_Helper_Redirector::gotoUrl()}.
     *
     * @param string $url
     * @param array $options Options to be used when redirecting
     * @return void
     */
    public function redirect($url, array $options = array())
    {
        $redirector = $this->_actionController->getHelper('redirector');
        $redirector->gotoUrl($url, $options);
    }

    /**
     * Redirect to "/error/notfound"
     * 
     * @return void
     */
    public function redirect404()
    {
        $this->redirect('/error/notfound');
        exit();
    }
    
    public function setAuthCookie()
    {
        $uid = $this->_userId;
        $puid = $this->_puid;
        $session_key = $this->_session_key;
        $t = time();
        $rnd = mt_rand(1, ECODE_NUM);

        //$sig = md5($uid . $puid . $session_key . $t . $rnd . APP_SECRET);
        //$skey = $uid . '.' . $puid . '.' . base64_encode($session_key) . '.' . $t . '.' . $rnd . '.' . $sig;
        $skey = Hapyfish2_Project_Bll_UserCertify::generateKey($uid, $puid, $session_key, $t, $rnd, APP_SECRET);
        $this->_hfskey = $skey;
        
        //P3P privacy policy to use for the iframe document
        //for IE
        header('P3P: CP=CAO PSA OUR');
        setcookie('hf_skey', $skey, 0, '/', str_replace('http://', '.', HOST));
        return $skey;
    }
}