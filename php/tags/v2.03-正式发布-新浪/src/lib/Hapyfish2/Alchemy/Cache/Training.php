<?php

class Hapyfish2_Alchemy_Cache_Training
{

	public static function getIds($uid)
	{
		$key = 'a:u:trainingids:';
		$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
		$key = $key . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
	
		$ids = $cache->get($key);
		if ($ids === false) {
			try {
				$dal = Hapyfish2_Alchemy_Dal_Training::getDefaultInstance();
				$ids = $dal->getIds($uid);
				if ($ids != null) {
					$cache->add($key, $ids);
				} else {
					return null;
				}
			} catch (Exception $e) {
				return null;
			}
		}
	
		return explode(',', $ids);
	}
	
	public static function reloadIds($uid)
	{
		try {
			$dal = Hapyfish2_Alchemy_Dal_Training::getDefaultInstance();
			$ids = $dal->getIds($uid);
			if ($ids) {
				$key = 'a:u:trainingids:';
				$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
				$key = $key . $uid;
				$cache = Hapyfish2_Cache_Factory::getMC($uid);
				$cache->set($key, $ids);
			} else {
				return null;
			}
		} catch (Exception $e) {
			return null;
		}
	
		return explode(',', $ids);
	}
	
}