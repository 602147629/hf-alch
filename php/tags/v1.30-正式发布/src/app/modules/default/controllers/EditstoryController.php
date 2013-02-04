<?php

/**
 * Alchemy editstory controller,剧情编辑器专用
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/02    Nick
 */
class EditstoryController extends Zend_Controller_Action
{
	protected $uid;
	
    public function init()
    {
        $this->uid = 10019;
        Hapyfish2_Alchemy_Bll_UserResult::setUser($this->uid);
        
    	$controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
    }

    protected function echoResult($data)
    {
        header("Cache-Control: no-store, no-cache, must-revalidate");
        echo json_encode($data);
        exit;
    }
    
    protected function echoError($status, $content = null)
    {
 		if ($content === null) {
			$content = 'serverWord_' . abs($status);
		}
		$result = array('status' => $status, 'content' => $content);

    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	echo json_encode(array('result' => $result));
    	exit;
    }
    
	protected function flush()
	{
    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	$data = Hapyfish2_Alchemy_Bll_UserResult::flush();
    	echo json_encode($data);
    	exit;
	}

    /**
     * proxy for undefined methods
     * override
     * @param string $methodName
     * @param array $args
     */
    public function __call($methodName, $args)
    {
        echo 'No This Method:' . $methodName;
    }

	/**
     * init swf
     *
     */
    public function initswfAction()
    {
        include_once(CONFIG_DIR . '/swfconfig.php');
        $apiBasicVer = Hapyfish2_Alchemy_Cache_Basic::getBasicVersion();
        $swfConfig['interfaces']['loadstatic'] .= '?v='.$apiBasicVer;
        $swfConfig['interfaces']['GiftGetActInitStatic'] .= '?v='.$apiBasicVer;
        if (defined('ENABLE_STATIC_GZ') && ENABLE_STATIC_GZ) {
             $swfConfig['interfaces']['loadstatic'].= '&gz=1&org=hf';
        }
        $this->echoResult($swfConfig);
    }
    
    /**
     * 初始化静态信息
     */
    public function initstaticAction()
    {
        header('Cache-Control: max-age=31104000');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31104000). ' GMT');
		$gz = $this->_request->getParam('gz', 0);
		if ($gz == 1) {
			header('Content-Type: application/octet-stream');
			echo Hapyfish2_Alchemy_Bll_BasicInfo::getInitVoData('1.0', true);
		}
		else {
			echo Hapyfish2_Alchemy_Bll_BasicInfo::getInitVoData();
		}
		exit;
    }
    
    public function inituserinfoAction()
    {
		$uid = $this->uid;

    	$data = array();

    	//连续登录信息 login time upd
        $loginInfo = Hapyfish2_Alchemy_Bll_User::updateLoginTime($uid);

    	//读取所有订单(已接，未接)，并更新满意度
		$orders = Hapyfish2_Alchemy_Bll_Order::getOrderList($uid);

		//get user info
        $data['user'] = Hapyfish2_Alchemy_Bll_User::getUserInit($uid);
		$data['items'] = Hapyfish2_Alchemy_Bll_Bag::getAll($uid);
		//$data['tasks'] = $temp->tasks;
		//$data['diarys'] = $temp->diarys;
		//$data['guides'] = $temp->guides;
		$data['mixs'] = Hapyfish2_Alchemy_HFC_Mix::getUserMix($uid);
		$data['curMixs'] = Hapyfish2_Alchemy_HFC_Furnace::getCurMixs($uid);
		$data['illustrations'] = Hapyfish2_Alchemy_Bll_Illustrations::getUserIllustrations($uid);

		//世界地图
		$hasNewWorld = false;
		$data['curOpenScene'] = Hapyfish2_Alchemy_Bll_WorldMap::getOpenWorldMap($uid, $hasNewWorld);
		$data['isnewworldmap'] = $hasNewWorld ? 1 : 0;
		$data['acts'] = Hapyfish2_Alchemy_Bll_Act::get($uid);

		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$data['roles'] = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);

		//任务数据
		$data['tasks'] = Hapyfish2_Alchemy_Bll_Task::getCurTaskList($uid);
		$data['systime'] = time();

		//订单数据
		$data['orders'] = $orders;

		//$todayLogin = Hapyfish2_Alchemy_Bll_User::updateUserTodayInfo($uid);

		//第一次进入游戏播放剧情
		$storyVo = Hapyfish2_Alchemy_Bll_Story::startStory($uid, 11);
		if ( $storyVo != -200) {
			$data['story'] = $storyVo;
		}

    	$result = $data;

        $this->echoResult($result);
    }
    
    /* 探险 */
	/**
     * 回主场景 or 去好友主场景
     */
    public function gohomeAction()
    {
        $uid = $this->uid;
        $fid = (int)$this->_request->getParam('fid');
        $status = Hapyfish2_Alchemy_Bll_Scene::goHomeScene($uid, $fid);
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
	/**
     * 进入副本
     */
    public function entermapAction()
    {
        $uid = $this->uid;
        $fid = (int)$this->_request->getParam('fid');
        $mapId = (int)$this->_request->getParam('sceneId');
        $portalId = (int)$this->_request->getParam('portalId');
        $storyId = (int)$this->_request->getParam('storyId');

        if ($fid && $fid != $uid) {
            $status = Hapyfish2_Alchemy_Bll_Editstory::enterFriendMap($uid, $fid, $mapId);
        }
        else {
            if ($mapId < 100) {
                $status = Hapyfish2_Alchemy_Bll_Editstory::enterHomeOrVila($uid, $mapId);
            }
            else {
                $status = Hapyfish2_Alchemy_Bll_Editstory::enterMap($uid, $mapId, $portalId, $storyId);
            }
        }
		if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
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
    
    
    
    
    
    
}