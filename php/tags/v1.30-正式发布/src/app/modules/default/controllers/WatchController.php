<?php

class WatchController extends Zend_Controller_Action
{
    public function init()
    {
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
    }

	function check()
	{
		$uid = $this->_request->getParam('uid');
		if (empty($uid)) {
			echo '1001';
			exit;
		}

		/*
		if (APP_SERVER_TYPE == 1) {
			$t = $this->_request->getParam('t');
			if (empty($t)) {
				echo '1001';
				exit;
			}

			$sig = $this->_request->getParam('sig');
			if (empty($sig)) {
				echo '1002';
				exit;
			}

			$validSig = md5($uid . $t . APP_SECRET);
			if ($sig != $validSig) {
				echo '1003';
				exit;
			}

			$now = time();
			if (abs($now - $t) > 1800) {
				echo '1004';
				exit;
			}
		}*/

		$isAppUser = Hapyfish2_Alchemy_Cache_User::isAppUser($uid);
		if (!$isAppUser) {
			echo 'uid error, not app user';
			exit;
		}

		return $uid;
	}

    /**
     * index Action
     *
     */
    public function indexAction()
    {
    	$uid = $this->check();
    	$user = Hapyfish2_Platform_Bll_User::getUser($uid);
        $puid = $user['puid'];
        $t = time();
        $rnd = mt_rand(1, ECODE_NUM);
        //simulate
        $session_key = md5($t);

        $sig = md5($uid . $puid . $session_key . $t . $rnd . APP_SECRET);

        $skey = $uid . '.' . $puid . '.' . base64_encode($session_key) . '.' . $t . '.' . $rnd . '.' . $sig;

        setcookie('hf_skey', $skey , 0, '/', str_replace('http://', '.', HOST));
        Hapyfish2_Alchemy_Cache_User::setLoginUserSkeyWatch($uid, $skey);

        $avatar = Hapyfish2_Alchemy_HFC_User::getUserAvatar($uid);
        if (!$avatar) {
        	//$this->view->piantou = STATIC_HOST . '/swf03/piantou.swf?v=2012072401';
        	$this->view->piantou = '';
        	$this->view->createUrl = HOST . '/api/initavatar';
        	$this->view->createModule = STATIC_HOST . '/swf03/createPlayer.swf?v=1';
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
        $this->view->puid = $puid;
        $this->view->skey = $skey;
        $this->view->newuser = 0;
        $trackFlag = true;
        $help = Hapyfish2_Alchemy_HFC_Help::get($uid);
        if($help['finish_ids']){
        	$ids = explode(',', $help['finish_ids']);
        	if(in_array(17,$ids)){
        		$trackFlag = false;
        	}
        }
        $this->view->trackFlag = $trackFlag;
        
        //存档记录
        $dalDump = Hapyfish2_Alchemy_Dal_UserDump::getDefaultInstance();
        $dumpList = $dalDump->get();
        $this->view->dumpList = $dumpList;
        
        //当前任务列表
        $this->view->taskList = Hapyfish2_Alchemy_Bll_Task::getCurTaskList($uid);
        
        
        $this->render();
    }
 }

