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
	
	public function t1Action()
	{
		$uid = 1011;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$key = 'a:test:t1:1';
		$cache->set($key, '123456');
		$a = $cache->get($key);
		var_dump($a);
		exit;
	}
	
	public function testloginAction()
	{
		sleep(20);
		echo json_encode($_POST);
		exit;
	}
}