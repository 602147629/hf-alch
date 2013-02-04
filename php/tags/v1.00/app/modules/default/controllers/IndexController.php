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
    
    public function indexAction()
    {
    	//uid = 0的时候，不要强制，因为可能是开发者/测试者开放模式，需要后面再检查uid
    	//$appInfo = Hapyfish2_Project_Bll_AppInfo::checkStatus(0, true, false);
    	
    	try {
    		if ('renren' == PLATFORM) {
    			$application = Hapyfish2_Application_Renren::newInstance($this);
    		} else if ('kaixin' == PLATFORM) {
    			$application = Hapyfish2_Application_Kaixin::newInstance($this);
    		} else {
    			exit;
    		}
        	$application->run();
    	} catch (Exception $e) {
    		err_log($e->getMessage());
    		//echo '加载数据出错，请重新进入。';
    		echo '<div style="text-align:center;margin-top:10px;"><img src="' . STATIC_HOST . '/maintance/images/problem1.jpg" alt="加载数据出错，请重新进入" /></div>';
    		exit;
    	}

    	$uid = $application->getUserId();
        $isnew = $application->isNewUser();
        $platformUid = $application->getPlatformUid();

        if ($isnew) {
			$ok = Hapyfish2_Alchemy_Bll_User::joinUser($uid);
        	if (!$ok) {
    			echo '创建初始化数据出错，请重新进入。';
    			exit;
        	}

        	//invite flow TODO::
        }
        else {
        	$isAppUser = Hapyfish2_Alchemy_Cache_User::isAppUser($uid);
        	if (!$isAppUser) {
        		$ok = Hapyfish2_Alchemy_Bll_User::joinUser($uid);
        	    if (!$ok) {
    				echo '创建初始化数据出错，请重新进入。';
    				exit;
        		}
        		$isnew = true;
        	}
        	else {
        		$status = Hapyfish2_Platform_Cache_User::getStatus($uid);
        		if ($status > 0) {
        			if ($status == 1) {
        				$msg = '该帐号(门牌号:' . $uid . ')因使用外挂或违规已被封禁，有问题请联系管理员QQ:800004811';
        			} else if ($status == 2) {
        				$msg = '该帐号(门牌号:' . $uid . ')因数据出现异常被暂停使用，有问题请联系管理员QQ:800004811';
        			} else if ($status == 3)  {
        				$msg = '该帐号(门牌号:' . $uid . ')因利用bug被暂停使用[待处理后恢复]，有问题请联系管理员QQ:800004811';
        			} else {
        				$msg = '该帐号(门牌号:' . $uid . ')暂时不能访问，有问题请联系管理员QQ:800004811';
        			}

        			echo $msg;
        			exit;
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
        if (!$avatar) {
        	$this->view->piantou = '';
        	$this->view->createUrl = HOST . '/api/initavatar';
        	$this->view->createModule = STATIC_HOST . '/swf/createPlayer.swf?v=1';
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

		//tips
        $tipsStr = '佣兵有水火风三种属性，水克火、火克风、风克水||和NPC多说话，可以接到很多的任务||采集到的材料通过炼金合成能得到卖高价的商品哟||虽然是叫一样名字的武器，也有破烂和完美之分哦||怪物打的越多升级就越快，这是三岁小孩都知道的事||神勇点可是雇佣高品质佣兵必要的条件哦||战士克弓手、弓手克法师、法师克战士';
        $this->view->tipsStr = $tipsStr;
		
        $this->view->uid = $uid;
        $this->view->puid = $platformUid;
        $this->view->showpay = true;
        $this->view->newuser = $isnew ? 1 : 0;
        $this->view->skey = $skey;
        $this->render();
    }

	public function maintanceAction()
	{
		$appInfo = Hapyfish2_Project_Bll_AppInfo::getAdvanceInfo();
		$this->view->notice = $appInfo['maintance_notice'];
		$this->render();
	}

    public function assetlistAction()
    {
    	header("Cache-Control: no-store, no-cache, must-revalidate");
		require (CONFIG_DIR . '/asset.php');
    	echo json_encode($assetResult);
    	exit;
    }

 	protected function echoResult($data)
    {
    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	echo json_encode($data);
    	exit;
    }

    public function testAction()
    {
        echo 'hello alchemy';
        exit;
    }
}