<?php
require_once 'Hapyfish2/Application/Abstract.php';

class Hapyfish2_Application_QQ extends Hapyfish2_Application_Abstract
{
    protected $_platform;
    protected $_pfkey;
    public $qq_params;

    /**
     * Singleton instance, if null create an new one instance.
     *
     * @param Zend_Controller_Action $actionController
     * @return Bll_Application
     */
    public static function newInstance(Zend_Controller_Action $actionController)
    {
        if (null === self::$_instance) {
            self::$_instance = new Hapyfish2_Application_QQ($actionController);
        }
        return self::$_instance;
    }

    public function getQQParams()
    {
        $openid = $_GET['openid'];
        $openkey = $_GET['openkey'];
        $pf = $_GET['pf'];
        $pfkey = $_GET['pfkey'];
        
        if (empty($openid)) {
            return -1;
        }
        if (empty($openkey)) {
            return -2;
        }
        if (empty($pf)) {
            return -3;
        }
        if (empty($pfkey)) {
            return -4;
        }
        
        $this->qq_params = array(
            'openid' => $openid,
            'openkey' => $openkey,
            'pf' => $pf,
            'pfkey' => $pfkey,
        );
        return 1;
    }

    protected function _getUser($data)
    {
        $user = array();
        $user['uid'] = '' . $this->_userId;
        $user['puid'] = $this->_puid;
        $user['name'] = $data['nickname'];
        $faceUrl = $data['figureurl'];
        if (strpos($data['figureurl'], 'http://') === false) {
            $faceUrl = 'http://' . $faceUrl;
        }
        $user['figureurl'] = $faceUrl;
        $sex = isset($data['gender']) ? $data['gender'] : '';
        if ($sex == '男') {
            $gender = 1;
        } else 
            if ($sex == '女') {
                $gender = 0;
            } else {
                $gender = - 1;
            }
        $user['gender'] = $gender;
        
        $user['is_yellow_vip'] = 0;
        if (isset($data['is_yellow_vip']) && $data['is_yellow_vip']) {
            $user['is_yellow_vip'] = 1;
        }
        $user['is_yellow_year_vip'] = 0;
        if (isset($data['is_yellow_year_vip']) && $data['is_yellow_year_vip']) {
            $user['is_yellow_year_vip'] = 1;
        }
        $user['yellow_vip_level'] = 0;
        if (isset($data['yellow_vip_level']) && $data['yellow_vip_level']) {
            $user['yellow_vip_level'] = $data['yellow_vip_level'];
        }
        return $user;
    }

    /**
     * _init()
     *
     * @return void
     */
    protected function _init()
    {
        $code = $this->getQQParams();
        if ($code < 0) {
            throw new Exception('parameter validate failed (' . $code . ')');
        }
        $openid = $this->qq_params['openid'];
        $openkey = $this->qq_params['openkey'];
        $pf = $this->qq_params['pf'];
        $pfkey = $this->qq_params['pfkey'];
        $this->_rest = OpenApi_RestFactory::getRest();
        $this->_rest->setPlatform($pf);
        $this->_rest->setUser($openid, $openkey);
        $this->_puid = $openid;
        $this->_session_key = $openkey;
        $this->_platform = $pf;
        $this->_pfkey = $pfkey;
        $this->_appId = $this->_rest->app_id;
        $this->_appName = $this->_rest->app_name;
    }

    protected function _updateInfo()
    {
        $userData = $this->_rest->getUser();
        if ($userData['ret'] != 0) {
            if ($userData['ret'] > 3000) {
                $ptUser = Hapyfish2_Platform_Bll_UidMap::getUser($this->_puid);
                if ($ptUser) {
                    $this->uid = $ptUser['uid'];
                    $log = Hapyfish2_Util_Log::getInstance();
                    $log->report('appTempEnter', array($this->_puid, $this->uid));
                    return;
                }
            }
            throw new Hapyfish2_Application_Exception('get user info error:' . $userData['ret']);
        }
        $puid = $this->_puid;
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
        if (!$uid) {
            throw new Hapyfish2_Application_Exception('user id error');
        }
        $this->_userId = $uid;
        //$platformType = $this->_isQzone ? self::QZONE_CODE : self::PENGYOU_CODE;
        $user = $this->_getUser($userData);
        if ($this->newuser) {
            //Hapyfish2_Platform_Bll_Factory::addUser($user);
            Hapyfish2_Platform_Bll_User::addUser($user);
            //add log
            $logger = Hapyfish2_Util_Log::getInstance();
            $logger->report('100', array(
                $uid , $puid , $user['gender'] , $this->_platform
            ));
            //isource =2 xiaoyou  =1 qzone
        //$logInfo = array('openid' => $puid, 'iSource' => $this->_platform, 'iCmd' => 100, 'iState' => 0, 'ownerUid' => $uid );
        //$logger = OpenApi_Qzone_Log::getInstance();
        //$logger->setLogFile(LOG_DIR . '/report.log');
        //$logger->report($uid, $logInfo);
        } else {
            $pUser = Hapyfish2_Platform_Bll_User::getUser($uid);
            if (empty($pUser) || empty($pUser['puid'])) {
                Hapyfish2_Platform_Bll_User::addUser($user);
            } else {
                Hapyfish2_Platform_Bll_User::updateUser($uid, $user, true);
            }
        }
        $fids = $this->_rest->getAppFriendIds();
        if ($fids !== null) {
            //这块可能会出现效率问题，fids很多的时候，memcacehd get次数会很多
            //优化方案，先根据fid切分到相应的memcached组，用getMulti方法，减少次数
            //$fids = Hapyfish2_Platform_Bll_Factory::getUids($fids);
            $fids = Hapyfish2_Platform_Bll_User::getUids($fids);
            if ($this->newuser) {
                //Hapyfish2_Platform_Bll_Factory::addFriend($uid, $fids);
                Hapyfish2_Platform_Bll_Friend::addFriend($uid, $fids);
            } else {
                //$pFriend = Hapyfish2_Platform_Bll_Factory::getFriend($uid);
                $pFriend = Hapyfish2_Platform_Bll_Friend::getFriend($uid);
                if (empty($pFriend)) {
                    //Hapyfish2_Platform_Bll_Factory::addFriend($uid, $fids);
                    Hapyfish2_Platform_Bll_Friend::addFriend($uid, $fids);
                } else {
                    //Hapyfish2_Platform_Bll_Factory::updateFriend($uid, $fids);
                    Hapyfish2_Platform_Bll_Friend::updateFriend($uid, $fids);
                }
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
        $this->setAuthCookie();
        $this->setpfkey();
    }
    
    public function setpfkey()
    {
    	 header('P3P: CP=CAO PSA OUR');
         setcookie('pfkey', $this->_pfkey, 0, '/', str_replace('http://', '.', HOST));
    }
}