<?php

/**
 * Alchemy tools controller
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/02    Nick
 */
class ToolsController extends Hapyfish2_Controller_Action_Api
{
	

	protected function getClientIP()
	{
		$ip = false;
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) {
				array_unshift($ips, $ip);
				$ip = false;
			}
			for ($i = 0, $n = count($ips); $i < $n; $i++) {
				if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])) {
					$ip = $ips[$i];
					break;
				}
			}
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
	
		return $ip;
	}
	
//	public function __construct()
//	{
//		$stop = true;
//		$ip = $this->getClientIP();
//		if ( $ip == '180.168.126.10' ) {
//			$stop = false;
//		}
//	
//		if ($stop) {
//			echo '-100';
//			exit;
//		}
//	}
	
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

    //重置跳过战斗次数
    public function resetskipAction()
    {
    	$uid = $this->_request->getParam('uid');

    	$vip = new Hapyfish2_Alchemy_Bll_Vip();
    	$jump = $vip->vipSkip($uid);
    	
    	$skip = $vip->getVipSkip($uid);
    	$skip['num'] = 0;

    	Hapyfish2_Alchemy_Cache_Vip::updateUserSkip($uid, $skip);
    	
    	$result = 1;
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
        $uid = $this->uid;
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
       	$uid = $this->uid;
        $coin = $this->_request->getParam('coin', 1);
    	$ok = Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $coin);
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
    	$uid = $this->uid;
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
    
    public function additemAction()
    {
    	$uid = $this->_request->getParam('uid', -1);
//    	$uid = $this->uid;
        $id = $this->_request->getParam('id', -1);
        $num = $this->_request->getParam('num', -1);
       	$ok =  Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $id, $num);
       	$result['error'] = $ok;
       	$this->echoResult($result);
    }
    
    public function updatelevelAction()
    {
    	$uid = $this->uid;
    	$level = $this->_request->getParam('level', -1);
    	
    	$levelExp = Hapyfish2_Alchemy_Cache_Basic::getUserLevelExp($level);
    	$userExp = $levelExp + 1;
    	Hapyfish2_Alchemy_HFC_User::updateUserExp($uid, $userExp);
    	
    	$ok = Hapyfish2_Alchemy_HFC_User::updateUserLevel($uid, $level);
    	$this->echoResult($ok);
    }
    
    public function addactivityAction()
    {
    	$uid = $this->uid;
    	$num = $this->_request->getParam('num', -1);
    	$ok = Hapyfish2_Alchemy_Bll_Activity::addUserActivity($uid, $num);
    	$this->echoResult($ok);
    }
    
    public function sendcoinAction()
    {
    	$num = 0;
    	$userlist = array(10024,10057,10065,10103,10114,10136,10139,10184,10207,10222,10227,10242,10266,10290,10310,10328,10359,10363,10364,10365,10371,10393,10406,10409,10437,10438,10442,10483,10489,10496,10503,10519,10523,10528,10537,10564,10584,10585,10591,10627,10637,10640,10657,10660,10664,10696,10715,10722,10726,10729,10738,10746,10748,10789,10797,10843,10845,10861,10874,10881,10910,10975,11003,11041,11055,11070,11073,11099,11125,11128,11143,11144,11153,11166,11255,11258,11271,11311,11331,11349,11353,11390,11405,11434,11436,11478,11574,11598,11619,11644,11645,11651,11661,11664,11700,11702,11703,11715,11720,11743,11753,11779,11803,11844,11847,11850,11854,11887,11896,11945,11954,11996,12010,12023,12024,12030,12034,12046,12051,12071,12171,12188,12240,12287,12300,12333,12499,12570,12600,12610,12613,12670,12686,12857,12950,12989,13094);
    	foreach($userlist as $uid){
    		$num += 1;
    		$ok = Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, 5000);
    	}
    	echo $num;
    	exit;
    }
    
    public function clearuservipAction()
    {
    	$uid  = $this->_request->getParam('uid', -1);
    	$key = 'a:u:vip:'.$uid;
    	$key1 = 'a:u:vip:skip:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$dal = Hapyfish2_Alchemy_Dal_Vip::getDefaultInstance();
		$dal->clearVip($uid);
		$cache->delete($key);
		$cache->delete($key1);
		echo "ok";
		exit;
    }
    
    public function addzhuangbeiAction()
    {
    	$uid  = $this->_request->getParam('uid', -1);
    	$cid  = $this->_request->getParam('cid', -1);
    	$type  = $this->_request->getParam('type', -1);
    	Hapyfish2_Alchemy_Bll_EventGift::addUserWeapon($uid, $cid, $type);
    	echo "ok";
    	exit;
    }
    
    public function clearadditionAction()
    {
    	$key = 'alchemy:bas:vip';
    	$cache=Hapyfish2_Alchemy_Cache_Vip::getBasicMC();
    	$cache->delete($key);
    	$vip = new Hapyfish2_Alchemy_Bll_Vip();
    	$data = $vip->getVipStatic();
    	print_r($data);
    	exit;
    }
    
    public function testachieveAction()
    {
    	$uid = $this->_request->getParam('uid', -1);
    	$level = $this->_request->getParam('level', -1);
    	Hapyfish2_Platform_Bll_WeiboAchieve::checkLevelAchieveId($uid,$level);
    	$context = Hapyfish2_Util_Context::getDefaultInstance();
        $sessionKey = $context->get('session_key');
        $rest = OpenApi_SinaWeibo_Client::getInstance();
        $rest->setUser($sessionKey);
        $lstGained = $rest->listAchieve();
        print_r($lstGained);
        exit;
    }
    
    public function testrankAction()
    {
    	$uid = $this->_request->getParam('uid', -1);
    	$level = $this->_request->getParam('level', -1);
    	Hapyfish2_Platform_Bll_WeiboRank::setRank($uid, 1, $level);
    	$context = Hapyfish2_Util_Context::getDefaultInstance();
        $sessionKey = $context->get('session_key');
        $rest = OpenApi_SinaWeibo_Client::getInstance();
        $rest->setUser($sessionKey);
        $lstGained = $rest->listAchieve();
        print_r($lstGained);
        exit;
    }
    
    public function clearspnumAction()
    {
    	$uid = $this->_request->getParam('uid', -1);
    	$key = 'a:u:vip:add:sp:'.$uid;
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$cache->delete($key);
    	echo "ok";
    	exit;
    }
    
    public function clearqinlueAction()
    {
    	$uid = $this->_request->getParam('uid', -1);
    	$key = 'a:u:fightoccupy:bot:' . $uid;
    	$key1 = 'a:u:fightoccupy:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$info = array(
    	'corps_used'=>array(),
    	'passive' => array(),
    	'initiative'=>array(),
    	'last_protect_open_tm'=>0
    	);
    	Hapyfish2_Alchemy_HFC_FightOccupy::save($uid, $info);
    	$cache->delete($key);
    	$cache->delete($key1);
    	echo "ok";
    	exit;
    }
    
    public function clearfangwenAction()
    {
    	$uid = $this->_request->getParam('uid', -1);
    	$key = 'a:u:access:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$cache->delete($key);
    	echo "ok";
    	exit;
    }
    
    public function testpayAction()
    {
    	$uid = $this->_request->getParam('uid', -1);
    	$num = $this->_request->getParam('num', -1);
    	$totalPay = Hapyfish2_Alchemy_HFC_User::getTotalPay($uid);
		$totalPay += $num;
		Hapyfish2_Alchemy_HFC_User::updateTotalPay($uid, $totalPay);
		Hapyfish2_Alchemy_Bll_VipWelfare::checkVip($uid, $totalPay);
		echo "ok";
		exit;
    }
    
    public function clearoldorderAction()
    {
    	$uid = $this->_request->getParam('uid', -1);
    	$key = 'a:u:orderlist:'.$uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$cache->delete($key);
    	echo "ok";
    	exit;
    }
    
    public function testinviteAction()
    {
    	$uid = $this->_request->getParam('uid', -1);
    	$invite = Hapyfish2_Alchemy_HFC_User::getTotalInvite($uid);
		$invite += 1;
		Hapyfish2_Alchemy_HFC_User::addTotalInvite($uid, $invite);
		echo "ok";
		exit;
    }
    public function testboxAction()
    {
    	$uid = $this->_request->getParam('uid', -1);
    	$dayBox = Hapyfish2_Alchemy_Cache_EventGift::getDayFriendBox($uid);
    	$dayBox['get'] = 1;
    	Hapyfish2_Alchemy_Cache_EventGift::updateFriendBox($uid, $dayBox);
		echo "ok";
		exit;
    }
    
    public function clearopenboxAction()
    {
    	$uid = $this->_request->getParam('uid', -1);
    	$dayBox = Hapyfish2_Alchemy_Cache_EventGift::getDayFriendBox($uid);
    	$dayBox['open'] = 0;
    	Hapyfish2_Alchemy_Cache_EventGift::updateFriendBox($uid, $dayBox);
		echo "ok";
		exit;
    }
    
    public function cleargetboxAction()
    {
    	$uid = $this->_request->getParam('uid', -1);
    	$dayBox = Hapyfish2_Alchemy_Cache_EventGift::getDayFriendBox($uid);
    	$dayBox['get'] = 0;
    	Hapyfish2_Alchemy_Cache_EventGift::updateFriendBox($uid, $dayBox);
		echo "ok";
		exit;
    }
}