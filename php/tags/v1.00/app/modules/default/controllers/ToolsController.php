<?php

/**
 * Alchemy tools controller
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/02    Nick
 */
class ToolsController extends Zend_Controller_Action
{
    public function init()
    {
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
    
    //用户添加合成术
    public function addusermixAction()
    {
        $uid = $this->_request->getParam('uid');
        $mixCid = $this->_request->getParam('mixCid');
    	
        $result = Hapyfish2_Alchemy_HFC_Mix::addUserMix($uid, $mixCid);
        
        if ( $result ) {
        	
        	echo 'OK';
        	exit;
        }
        echo 'False';
        exit;
    }
    
    //清空用户合成术
    public function clearusermixAction()
    {
        $uid = $this->_request->getParam('uid');

        $result = Hapyfish2_Alchemy_HFC_Mix::updateUserMix($uid, array());

        if ( $result ) {
        	echo 'OK';
        	exit;
        }
        echo 'False';
        exit;
    }
    
    //添加用户物品
    public function adduseritemAction()
    {
        $uid = $this->_request->getParam('uid');
        $cid = $this->_request->getParam('cid');
        $count = $this->_request->getParam('count', 1);
    	
        $itemType = substr($cid, -2, 1);
        if ( $itemType == 4 ) {
        	$furnace = array('uid' => $uid,
        					 'furnace_id' => $cid,
        					 'x' => 1,
        					 'y' => 1,
        					 'cid' => 0,
        					 'start_time' => 0,
        					 'remaining_time' => 0,
        					 'cur_probability' => 0,
        					 'num' => 0,
        					 'status' => 1);
        	
        	$result = Hapyfish2_Alchemy_HFC_Furnace::addOne($uid, $furnace);
        }
        else {
        	$result = Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $cid, $count);
        }
        
        
		$data['items'] = Hapyfish2_Alchemy_Bll_Bag::getAll($uid);
        $this->echoResult($data);
        /*if ( $result ) {
        	echo 'OK';
        	exit;
        }
        echo 'False';
        exit;*/
    }
    
    public function delweaponAction()
    {
        $uid = $this->_request->getParam('uid');
        $id = $this->_request->getParam('id');
        $result = Hapyfish2_Alchemy_HFC_Weapon::delWeapon($uid, $id);
        
        if ( $result ) {
        	echo 'OK';
        	exit;
        }
        echo 'False';
        exit;
    }
    
    public function clearweaponAction()
    {
        $uid = $this->_request->getParam('uid');
        $cid = $this->_request->getParam('cid');
        $result = Hapyfish2_Alchemy_HFC_Weapon::clearWeaponByCid($uid, $cid);
        
        if ( $result ) {
        	echo 'OK';
        	exit;
        }
        echo 'False';
        exit;
    }
    
    //注册用户
	public function registerAction()
	{
	    $puid = $this->_request->getParam('puid');
		$uidInfo = Hapyfish2_Platform_Cache_UidMap::getUser($puid);
		if (!$uidInfo) {
    		$uidInfo = Hapyfish2_Platform_Cache_UidMap::newUser($puid);
    		if (!$uidInfo) {
    			echo 'inituser error: 1';
    			exit;
    		}
		}
		$uid = $uidInfo['uid'];
		$name = $this->_request->getParam('name');
		if (empty($name)) {
			$name = '测试' . $uid;
		}
		$id = $this->_request->getParam('id');
		if (empty($id)) {
			$id = 1;
		} else {
			$id = (int)$id;
			if ($id <= 0 || $id > 6) {
				$id = 1;
			}
		}
		$figureurl = $this->_request->getParam('figureurl');
		if (empty($figureurl)) {
			$figureurl = 'http://hdn.xnimg.cn/photos/hdn521/20091210/1355/tiny_E7Io_11729b019116.jpg';
		}

        $user = array();
        $user['uid'] = $uid;
        $user['puid'] = $puid;
        $user['name'] = $name;
        $user['figureurl'] = $figureurl;
        $user['gender'] = rand(0,1);
		Hapyfish2_Platform_Bll_User::addUser($user);

		$ok = Hapyfish2_Alchemy_Bll_User::joinUser($uid);
		if ($ok) {
			//Hapyfish2_Alchemy_Bll_User::initRole($uid, $id);
			echo 'Success: ' . $uid;
		} else {
			echo 'Failure';
		}
		exit;
	}

	public function  updatefriendAction()
	{
		$uid = $this->_request->getParam('uid');
		$fids = $this->_request->getParam('fids');
		$fids = explode(',', $fids);

		Hapyfish2_Platform_Bll_Friend::updateFriend($uid, $fids);
		echo '好友列表更新';
		exit;
	}
    
    public function getfurnaceAction()
    {
    	$uid = $this->_request->getParam('uid', 1011);
        $fid = $this->_request->getParam('fid', 0);
        if ($fid>0) {
	    	$result = Hapyfish2_Alchemy_HFC_Furnace::getOne($uid, $fid);
        }
        else if ($fid == -1) {
	    	$result = Hapyfish2_Alchemy_HFC_Furnace::getCurMixs($uid);
        }
        else {
	    	$result = Hapyfish2_Alchemy_HFC_Furnace::getOnRoom($uid);
        }
    	$this->echoResult(array('result' => $result));
    }
    
    //添加宝石
    public function addgemAction()
    {
        $uid = $this->_request->getParam('uid');
        $count = $this->_request->getParam('count', 1);
        
        $gemInfo = array('gem' => $count);
        
    	$ok = Hapyfish2_Alchemy_Bll_Gem::add($uid, $gemInfo);
    	if ( $ok ) {
	    	echo 'OK';
	    	exit;
    	}
    	echo 'False';
    	exit;
    }

    //添加金币
    public function addcoinAction()
    {
        $uid = $this->_request->getParam('uid');
        $count = $this->_request->getParam('count', 1);
                
    	$ok = Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $count);
    	if ( $ok ) {
	    	echo 'OK';
	    	exit;
    	}
    	echo 'False';
    	exit;
    }

    //行动力
    public function addspAction()
    {
        $uid = $this->_request->getParam('uid');
        $count = $this->_request->getParam('count', 1);
        $maxcount = $this->_request->getParam('maxcount', $count);
                
        $spInfo = array('sp' => $count, 'max_sp'=>$maxcount, 'sp_set_time'=>time());
    	$ok = Hapyfish2_Alchemy_HFC_User::updateUserSp($uid, $spInfo);
    	if ( $ok ) {
	    	echo 'OK';
	    	exit;
    	}
    	echo 'False';
    	exit;
    }
    
    public function addfriendAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$fid = $this->_request->getParam('fid');
    	    	
        $fids = Hapyfish2_Platform_Bll_Friend::getFriendIds($uid);

        if ($fids !== null) {
        	if (!in_array($fid, $fids)) {
	        	$fids[] = $fid;
	        	Hapyfish2_Platform_Bll_Friend::updateFriend($uid, $fids);
        	}
        }
        else {
        	$fids = array();
        	$fids[] = $fid;
        	Hapyfish2_Platform_Bll_Friend::addFriend($uid, $fids);
        }
        
        $friendFids = Hapyfish2_Platform_Bll_Friend::getFriendIds($fid);
    
        if ($friendFids !== null) {
        	if (!in_array($uid, $friendFids)) {
	        	$friendFids[] = $uid;
	        	Hapyfish2_Platform_Bll_Friend::updateFriend($fid, $friendFids);
        	}
        }
        else {
        	$friendFids = array();
        	$friendFids[] = $uid;
        	Hapyfish2_Platform_Bll_Friend::addFriend($fid, $friendFids);
        }
        
    	echo 'ok';
    	exit;
    }
    
    public function delfriendAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$fid = $this->_request->getParam('fid');
    	
        $fids = Hapyfish2_Platform_Bll_Friend::getFriendIds($uid);
        
        foreach ( $fids as $k=>$v ) {
        	if ($v==$fid) {
        		unset($fids[$k]);
        	}
        }
        Hapyfish2_Platform_Bll_Friend::updateFriend($uid, $fids);
        
    	echo 'ok';
    	exit;
    }
    
    //重置剧情
    public function clearstoryAction()
    {
        $uid = $this->_request->getParam('uid');
        $id = $this->_request->getParam('id', -1);
                
        if ($id == -1) {
        	$ok = Hapyfish2_Alchemy_HFC_Story::updateStory($uid, array(), true);
        }
        else {
        	$ok = Hapyfish2_Alchemy_HFC_Story::delStory($uid, $id);
        }
        
    	if ( $ok ) {
	    	echo 'OK';
	    	exit;
    	}
    	echo 'False';
    	exit;
    }
    
}