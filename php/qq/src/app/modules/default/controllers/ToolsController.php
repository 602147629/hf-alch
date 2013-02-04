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
    	$uid = $this->_request->getParam('uid', -1);
    	$num = $this->_request->getParam('num', -1);
    	$ok = Hapyfish2_Alchemy_Bll_Activity::addUserActivity($uid, $num);
    	$this->echoResult($ok);
    }
    
    public function sendcoinAction()
    {
    	$num = 0;
    	$userlist = array(10055,10134,11055,11221,11353,11384,11592,11761,11960,11990,12083,12225,12383,12833,13220,13373,13857,14097,14231,14357,14803,14994,15722,16077,16174,16324,16533,16715,16982,17392,17442,17551,17594,18130,18205,18267,18422,18483,18980,19094,19153,19292,19505,19605,20184,20460,21082,21174,21295,21402,21467,21527,21644,21727,21767,21854,22232,22672,22872,22941,23182,23281,24083,24105,24272,24331,24465,24535,25064,25942,26040,27240,28265,28354,28384,28870,30171,30710,31450,32934,32950,35237,35837,36493,36503,36732,36945,37374,37772,38270,38594,38882,39131,40845,40882,41190,41481,41651,41822,42333,42964,43157,43340,43677,44534,44705,44823,45015,45710,45842,46074,46124,47061,47310,47820);
    	foreach($userlist as $uid){
    		$num += 1;
    		$ok = Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, 5000);
    		Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, 6115, 7);
    		info_log($uid, 'sendCoin');
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
//		$totalPay = Hapyfish2_Alchemy_HFC_User::getTotalPay($uid);
//		$totalPay += $num;
//		Hapyfish2_Alchemy_HFC_User::updateTotalPay($uid, $totalPay);
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		$vip->addGrowUp($uid, $num);
//		Hapyfish2_Alchemy_Bll_EventGift::checkGift($uid,$num);
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
    
    public function getallrankAction()
    {
	    Hapyfish2_Platform_Bll_WeiboRank::getTotalRank();
    }
    
    public function clearuserstatusAction()
    {
    	$uid = $this->_request->getParam('uid', -1);
    	$date =date('Ymd');
    	$data['date'] = $date;
    	$data['status'] = 0;
    	Hapyfish2_Alchemy_HFC_User::updateUserStatus($uid, $data);
    	echo "ok";
    	
    }
    
    public function clearactivityAction()
    {
    	$uid = $this->_request->getParam('uid', -1);
    	$key = 'a:u:activity:' . $uid;
    	$key1 = 'a:u:taskdly:' . $uid;        
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->delete($key);
        $cache->delete($key1);
        echo "ok";
        exit;
    }
    
    public function sendpackageAction()
    {
    	$num = 0;
    	$userlist = array(16054,13844,46224,18272,15515,11761,38870,25450,28870,30327,46621);
    	foreach($userlist as $uid){
    		$num += 1;
    		$ok = Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, 30000);
    		Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, 2715, 3);
    		Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, 1001415, 1);
    		Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, 1001715, 1);
    		Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, 9215, 1);
    		info_log($uid, 'sendPackage');
    	}
    	echo $num;
    	exit;
    }
}