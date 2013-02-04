<?php

/**
 * Alchemy index controller
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2010/10    lijun.hu
 */
class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
        $this->view->appId = APP_ID;
        $this->view->appKey = APP_KEY;
    }
    
    public function renderLoadErr()
    {
        $alt = Hapyfish2_Util_Lang::getInstance()->getText('app', 'load_error');
        echo '<div style="text-align:center;margin-top:10px;"><img src="' 
            . STATIC_HOST . '/maintance/images/problem2.jpg" alt="' 
            . $alt . '" /></div>';
        exit();
    }

    public function indexAction()
    {
        //uid = 0的时候，不要强制，因为可能是开发者/测试者开放模式，需要后面再检查uid
        //$appInfo = Hapyfish2_Project_Bll_AppInfo::checkStatus(0, true, false);
        
        try {
            $application = Hapyfish2_Application_Factory::newApplication($this);
        }
        catch (Exception $e) {
            info_log($e->getMessage(), 'app-start-err');
            echo Hapyfish2_Util_Lang::getInstance()->getText('app', 'error');
            exit();
        }
        
        if (! $application) {
            exit();
        }
        
        try {
            $application->run();
        }
        catch (Exception $e) {
            $platformUid = $application->getPlatformUid();
            $log = Hapyfish2_Util_Log::getInstance();
            $errMsg = $e->getMessage();
            $log->report('appLoadErr', array($platformUid , $errMsg));
            $this->renderLoadErr();
        }
        $uid = $application->getUserId();
        $isnew = $application->isNewUser();
        $platformUid = $application->getPlatformUid();
        $sessionKey = $application->getSessionKey();
        $data = array(
            'uid' => $uid , 'puid' => $platformUid , 'session_key' => $sessionKey
        );
        $context = Hapyfish2_Util_Context::getDefaultInstance();
        $context->setData($data);
        
        if ($isnew) {
            $ok = Hapyfish2_Alchemy_Bll_User::joinUser($uid);
            if (! $ok) {
                echo Hapyfish2_Util_Lang::getInstance()->getText('app', 'create_data_error');
                exit();
            }
            //handle invite
            Hapyfish2_Alchemy_Bll_Invite_Factory::handle($application);
        } else {
            $isAppUser = Hapyfish2_Alchemy_Cache_User::isAppUser($uid);
            if (! $isAppUser) {
                $ok = Hapyfish2_Alchemy_Bll_User::joinUser($uid);
                if (! $ok) {
                    echo Hapyfish2_Util_Lang::getInstance()->getText('app', 'create_data_error');
                    exit();
                }
                $isnew = true;
            } else {
                $status = Hapyfish2_Platform_Cache_User::getStatus($uid);
                if ($status > 0) {
                    $lang = Hapyfish2_Util_Lang::getInstance();
                    if ($status == 1) {
                        $msg = $lang->getText('userstatus', 'status_1', $uid);
                    } else 
                        if ($status == 2) {
                            $msg = $lang->getText('userstatus', 'status_2', $uid);
                        } else 
                            if ($status == 3) {
                                $msg = $lang->getText('userstatus', 'status_3', $uid);
                            } else {
                                $msg = $lang->getText('userstatus', 'error', $uid);
                            }
                    echo $msg;
                    exit();
                }
            }
        }
        /*if ($appInfo) {
        	if ($appInfo['app_status'] == 2 || $appInfo['app_status'] == 3) {
		        //再次检查uid(开发者/测试者开放模式)
		        Hapyfish2_Project_Bll_AppInfo::checkStatus($uid, true, true, $appInfo);
        	}
        }*/
        $next = $this->_request->getParam('hf_next');
        if ($next) {
            $this->_redirect($next);
        }
        //开启浏览器单点登录
        $skey = $application->getSKey();
        Hapyfish2_Alchemy_Cache_User::setLoginUserSkey($uid, $skey);
        $avatar = Hapyfish2_Alchemy_HFC_User::getUserAvatar($uid);
        if (! $avatar) {
            //$this->view->piantou = STATIC_HOST . '/'. SWF_VER .'/piantou.swf?v=2012072401';
            $this->view->piantou = '';
            $this->view->createUrl = HOST . '/api/initavatar';
            $this->view->createModule = STATIC_HOST . '/' . SWF_VER . '/createPlayer.swf?v=1';
        } else {
            $this->view->piantou = '';
            $this->view->createUrl = '';
            $this->view->createModule = '';
        }
        $notice = Hapyfish2_Project_Bll_Notice::getNoticeList();
        if (empty($notice)) {
            $this->view->showNotice = false;
        } else {
            $this->view->showNotice = true;
            $this->view->notice = $notice['main'];
        }
        $this->view->title = Hapyfish2_Util_Lang::getInstance()->getText('alchemy', 'title');
        //tips
        $tipsStr = Hapyfish2_Util_Lang::getInstance()->getText('alchemy', 'memo');
        $this->view->tipsStr = $tipsStr;
        $this->view->uid = $uid;
        $this->view->puid = $platformUid;

        $this->view->platform = PLATFORM;
        
        $this->view->showpay = true;
        $this->view->newuser = $isnew ? 1 : 0;
        $this->view->skey = $skey;
        $trackFlag = true;
        $help = Hapyfish2_Alchemy_HFC_Help::get($uid);
        if ($help['finish_ids']) {
            $ids = explode(',', $help['finish_ids']);
            if (in_array(17, $ids)) {
                $trackFlag = false;
            }
        }
        $this->view->trackFlag = $trackFlag;
        $this->view->swf_ver = SWF_VER;

        //聊天室参数
        /*$platfromUser = Hapyfish2_Platform_Cache_User::getUser($uid);
        $timestamp = time();
        $ticket = md5($uid . $platfromUser['name'] . $timestamp . CHAT_SECRET);
        $this->view->chatUser = array(
            'uid' => $uid, 'name' => $platfromUser['name'], 'timestamp' => $timestamp, 'ticket' => $ticket
        );
        $this->view->chatGageway = array('ip' => GATEWAY_HOST, 'port' => GATEWAY_PORT);*/

        $this->render();
    }

    public function maintanceAction()
    {
        $appInfo = Hapyfish2_Project_Bll_AppInfo::getAdvanceInfo();
        $this->view->notice = $appInfo['maintance_notice'];
        $this->render();
    }
    
	/**
     * 静态地图副本数据
     */
    public function mapstaticAction()
    {
        header("Cache-Control: max-age=2592000");
        $mapId = (int)$this->_request->getParam('id');
    	echo Hapyfish2_Alchemy_Bll_BasicInfo::getMapStaticData($mapId);
		exit;
    }

    public function noopAction()
    {
        echo 'hello alchemy';
        exit();
    }

}