<?php
class Hapyfish2_Alchemy_Cache_payUserResult
{
	public static function addPayResult($uid,$result)
	{
		$key = 'a:u:qq:y:p:r:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $result);
	}
	
	public static function getPayResult($uid)
	{
		$key = 'a:u:qq:y:p:r:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$result = $cache->get($key);
		return $result;
	}
}
