<?php

class MaintenanceapiController extends Zend_Controller_Action
{
	function vaild()
	{

	}

	/*function check()
	{
		$uid = $this->_request->getParam('uid');
		if (empty($uid)) {
			$this->echoError(1001, 'uid can not empty');
		}

		$isAppUser = Hapyfish2_Island_Cache_User::isAppUser($uid);
		if (!$isAppUser) {
			$this->echoError(1002, 'uid error, not app user');
			exit;
		}

		return $uid;
	}*/

    protected function echoResult($data)
    {
    	$data['errno'] = 0;
    	echo json_encode($data);
    	exit;
    }

    protected function echoError($errno, $errmsg)
    {
    	$result = array('errno' => $errno, 'errmsg' => $errmsg);
    	echo json_encode($result);
    	exit;
    }

	public function loaddataAction()
	{
		Hapyfish2_Alchemy_Cache_Basic::loadMixList();
		Hapyfish2_Alchemy_Cache_Basic::loadGoodsList();
		Hapyfish2_Alchemy_Cache_Basic::loadScrollList();
		Hapyfish2_Alchemy_Cache_Basic::loadStuffList();
		Hapyfish2_Alchemy_Cache_Basic::loadFurnaceList();
		Hapyfish2_Alchemy_Cache_Basic::loadDecorList();
		Hapyfish2_Alchemy_Cache_Basic::loadWeaponList();
		Hapyfish2_Alchemy_Cache_Basic::loadIllustrationsList();
		Hapyfish2_Alchemy_Cache_Basic::loadUserLevelList();
		Hapyfish2_Alchemy_Cache_Basic::loadRoomLevelList();
		Hapyfish2_Alchemy_Cache_Basic::loadAvatarList();
		Hapyfish2_Alchemy_Cache_Basic::loadSceneList();

		$data = array('result' => 'OK');
		$this->echoResult($data);
	}

	public function loadlocaldataAction()
	{
	    $isdel = (int)$this->_request->getParam('isdel');

		$cache = Hapyfish2_Alchemy_Cache_Basic::getBasicMC();
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$key = self::getKey('mix');
		$list = $cache->get($key);
		$localcache->set($key, $list, false);

		$key = self::getKey('goods');
		$list = $cache->get($key);
		$localcache->set($key, $list, false);

		$key = self::getKey('scroll');
		$list = $cache->get($key);
		$localcache->set($key, $list, false);

		$key = self::getKey('stuff');
		$list = $cache->get($key);
		$localcache->set($key, $list, false);

		$key = self::getKey('furnace');
		$list = $cache->get($key);
		$localcache->set($key, $list, false);
		
		$key = self::getKey('decor');
		$list = $cache->get($key);
		$localcache->set($key, $list, false);
		
		$key = self::getKey('weapon');
		$list = $cache->get($key);
		$localcache->set($key, $list, false);
		
		$key = self::getKey('illustrations');
		$list = $cache->get($key);
		$localcache->set($key, $list, false);
		
		$key = self::getKey('userlevellist');
		$list = $cache->get($key);
		$localcache->set($key, $list, false);
		
		$key = self::getKey('roomlevellist');
		$list = $cache->get($key);
		$localcache->set($key, $list, false);
		
		$key = self::getKey('avatar');
		$list = $cache->get($key);
		$localcache->set($key, $list, false);
		
		$key = self::getKey('scene');
		$list = $cache->get($key);
		$localcache->set($key, $list, false);
				
		if ($isdel) {
		    Hapyfish2_Alchemy_Bll_BasicInfo::removeDumpFile();
		}

		$data = array('result' => SERVER_ID.' OK');
		$this->echoResult($data);
	}

	public static function getKey($word)
	{
	    $prex = 'alchemy:bas:';
		return $prex . $word;
	}
}