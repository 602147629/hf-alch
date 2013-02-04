<?php
require_once 'Hapyfish2/Application/Abstract.php';

class Hapyfish2_Application_SinaWeibo extends Hapyfish2_Application_Abstract
{
    public $wyx_params;
    
    /**
     * Singleton instance, if null create an new one instance.
     *
     * @param Zend_Controller_Action $actionController
     * @return Hapyfish2_Application_SinaWeibo
     */
    public static function newInstance(Zend_Controller_Action $actionController)
    {
        if (null === self::$_instance) {
            self::$_instance = new Hapyfish2_Application_SinaWeibo($actionController);
        }
        return self::$_instance;
    }
    
    protected function _getWYXParams()
    {
        $this->wyx_params = array();
        $sessionKey = $_REQUEST['wyx_session_key'];
        $puid = $_REQUEST['wyx_user_id'];
        $signature = $_REQUEST['wyx_signature'];
        if (empty($sessionKey)) {
            return -1;
        }
        if (empty($puid)) {
            return -2;
        }
        if (empty($signature)) {
            return -3;
        }
        
        $this->wyx_params = array(
            'user_id' => $puid,
            'session_key' => $sessionKey,
            'signature' => $signature,
        );
        return 1;
    }

    protected function _getUser($data)
    {
        $user = array();
        $user['uid'] = $this->_userId;
        $user['puid'] = $data['uid'];
        $user['name'] = $data['name'];
        $user['gender'] = $data['gender'];
        $user['verified'] = $data['verified'];
        $user['figureurl'] = $data['headurl'];
        $user['vuid'] = $data['vuid'];
        $user['identity'] = $data['identity'];
        return $user;
    }

    protected function _updateInfo()
    {
        $userData = $this->_rest->getUser($this->_puid);
        if (! $userData) {
            $ptUser = Hapyfish2_Platform_Bll_UidMap::getUser($this->_puid);
            if ($ptUser) {
                $this->uid = $ptUser['uid'];
                $log = Hapyfish2_Util_Log::getInstance();
                $log->report('appTempEnter', array($this->_puid, $this->uid));
                return;
            }
            throw new Hapyfish2_Application_Exception('get user info error');
        }
        $puid = $this->_puid;
        if ($puid != $userData['uid']) {
            throw new Hapyfish2_Application_Exception('platform uid error');
        }
        try {
            $uidInfo = Hapyfish2_Platform_Cache_UidMap::getUser($puid);
            //first coming
            if (! $uidInfo) {
                $uidInfo = Hapyfish2_Platform_Cache_UidMap::newUser($puid);
                if (! $uidInfo) {
                    throw new Hapyfish2_Application_Exception('generate user id error');
                }
                $this->newuser = true;
            }
        }
        catch (Exception $e) {
            throw new Hapyfish2_Application_Exception('get user id error:' . $e->getMessage());
        }
        $uid = $uidInfo['uid'];
        if (! $uid) {
            throw new Hapyfish2_Application_Exception('user id error');
        }
        $this->_userId = $uid;
        $user = $this->_getUser($userData);
        if ($this->newuser) {
            Hapyfish2_Platform_Bll_User::addUser($user);
            //add log
            $logger = Hapyfish2_Util_Log::getInstance();
            $logger->report('100', array(
                $uid , $puid , $user['gender']
            ));
        } else {
            Hapyfish2_Platform_Bll_User::updateUser($uid, $user, true);
        }
        $fids = $this->_rest->getAppFriendIds();
        if ($fids !== null) {
            //这块可能会出现效率问题，fids很多的时候，memcacehd get次数会很多
            //优化方案，先根据fid切分到相应的memcached组，用getMulti方法，减少次数
            $fids = Hapyfish2_Platform_Bll_User::getUids($fids);
            if ($this->newuser) {
                Hapyfish2_Platform_Bll_Friend::addFriend($uid, $fids);
            } else {
                Hapyfish2_Platform_Bll_Friend::updateFriend($uid, $fids);
                //Hapyfish2_Platform_Bll_Friend::addFriend($uid, $fids);
            }
        }
        //是否新浪V用户
        $verified = Hapyfish2_Platform_Cache_User::getVerified($uid);
        $vuid = Hapyfish2_Platform_Cache_User::getVUID($uid);
        if ($verified != $user['verified']) {
            Hapyfish2_Platform_Cache_User::updateVerified($uid, $user['verified']);
        }
        if ($vuid != $user['vuid']) {
            Hapyfish2_Platform_Cache_User::updateVUID($uid, $user['vuid']);
        }
        $identity = Hapyfish2_Platform_Cache_User::getIdentity($uid);
        if ($identity != $user['identity']) {
            Hapyfish2_Platform_Cache_User::updateIdentity($uid, $user['identity']);
        }
    }

    /**
     * _init()
     *
     * @return void
     */
    protected function _init()
    {
        $code = $this->_getWYXParams();
        if ($code < 0) {
            throw new Exception('parameter validate failed (' . $code . ')');
        }
        
        try {
            $this->_rest = OpenApi_RestFactory::getRest();
        }
        catch (Exception $e) {
            throw new Exception('Hapyfish2_Application_SinaWeibo Rest Init Err:' . $e->getMessage());
        }
        
        $sessionKey = $this->wyx_params['session_key'];
        $puid = $this->wyx_params['user_id'];
        $this->_rest->setUser($sessionKey);
        $this->_puid = $puid;
        $this->_session_key = $sessionKey;
        $this->_appId = APP_ID;
        $this->_appName = APP_NAME;
        $this->newuser = false;
    }

    /**
     * run() - main mothed
     *
     * @return void
     */
    public function run()
    {
        $this->_updateInfo();
        $this->setAuthCookie();
    }
}