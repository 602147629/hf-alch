<?php

class HutestController extends Hapyfish2_Controller_Action_External
{
	public function sceneAction()
	{
		$uid = 1011;
		$data = Hapyfish2_Alchemy_Bll_Scene::getRoomData($uid);
		print_r($data);
		
		//$decorData = Hapyfish2_Alchemy_HFC_Decor::getScene($uid);
		//print_r($decorData);
		
		$data = Hapyfish2_Alchemy_HFC_FloorWall::getFloorWall($uid);
		var_dump($data);
		exit;
	}
	
	public function clearmcAction()
	{
   		$mc = new Memcached();
   		$mc->addServer('192.168.1.249', 11611);
   		$mc->flush();
   		echo 'OK';
   		exit;
	}
	
	public function clearapcAction()
	{
		apc_clear_cache('user');
   		echo 'OK';
   		exit;
	}
	
	public function clearcacheAction()
	{
		$uid = $this->_getParam('uid');
		$keys = array(
			'a:u:exp:' . $uid,
			'a:u:coin:' . $uid,
			'a:u:gem:' . $uid,
			'a:u:level:' . $uid,
			'a:u:scene:' . $uid,
			'a:u:avatar:' . $uid,
			'a:u:sp:' . $uid,
			'a:u:block:' . $uid,
			'a:u:decor:bag:' . $uid,
			'a:u:decor:scene:' . $uid,
			'a:u:goods:' . $uid,
			'a:u:scroll:' . $uid,
			'a:u:stuff:' . $uid,
			'a:u:weapon:' . $uid
		);
		
		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
		foreach ($keys as $key) {
			$ok = $cache->delete($key);
			echo $key . ':' . ($ok ? 'true':'false') . '<br/>';
		}
		
	    $ids = Hapyfish2_Alchemy_Cache_Furnace::getAllIds($uid); 
        if ($ids) {
	        foreach ($ids as $id) {
	            $key0 = 'a:u:furnace:' . $uid . ':' . $id;
	            $ok = $cache->delete($key0);
	            echo $key0 . ':' . ($ok ? 'true':'false') . '<br/>';
	        }
        }
		exit;
	}
	
	public function refreshfeedtplAction()
	{
		Hapyfish2_Alchemy_Cache_Basic::loadPlatformFeedTemplate();
		$key = Hapyfish2_Alchemy_Cache_Basic::getKey('PlatformFeedTemplate');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->delete($key);
		echo 'ok';
		exit;
	}
	
	public function feedAction()
	{
		$type = $this->_getParam('type');
		$uid = 10042;
		Hapyfish2_Alchemy_Bll_UserResult::setUser($uid);
		if ($type == 1) {
			Hapyfish2_Alchemy_Bll_PlatformFeed_Kaixin::taskCompleted($uid, 221, '消灭黑色史莱姆');
		} else if ($type == 2) {
			Hapyfish2_Alchemy_Bll_PlatformFeed_Kaixin::giftWish($uid, 111);
		} else if ($type == 3) {
			Hapyfish2_Alchemy_Bll_PlatformFeed_Kaixin::killBoss($uid, 10971);
		} else if ($type == 4) {
			Hapyfish2_Alchemy_Bll_PlatformFeed_Kaixin::buildingLevelup($uid, 1);
		} else if ($type == 5) {
			Hapyfish2_Alchemy_Bll_PlatformFeed_Kaixin::occupyHelp($uid, 1);
		} else if ($type == 6) {
			Hapyfish2_Alchemy_Bll_PlatformFeed_Kaixin::userRolePromotion($uid, 3);
		}
		
		$feedList = Hapyfish2_Alchemy_Bll_UserResult::getPlatformFeed();
		echo json_encode($feedList[0]);
		exit;
	}
	
	public function feedawardAction()
	{
		$uid = 10042;
		$ret = Hapyfish2_Alchemy_Cache_PlatformFeedAward::checkout($uid);
		$data = array('ret' => $ret);
		echo json_encode($data);
		exit;
	}
	
	public function clearwishAction()
	{
		$uid = $this->_getParam('uid');
        $dalGift = Hapyfish2_Alchemy_Dal_Gift::getDefaultInstance();
        $dalGift->deleteWish($uid);
		$mkey = 'a:u:gift:wish:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->delete($mkey);
		echo 'ok';
		exit;
	}
}