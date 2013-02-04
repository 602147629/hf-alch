<?php

class Hapyfish2_Project_Cache_AppCacheVersion
{
	const NAMESPACE = 'project:appcacheversion:';

	public static function getKey($word)
	{
		return self::NAMESPACE . $word;
	}

	public static function getBasicMC()
	{
		$key = 'mc_0';
		return Hapyfish2_Cache_Factory::getBasicMC($key);
	}

	public static function getBasicDB()
	{
		return Hapyfish2_Project_Dal_AppCacheVersion::getDefaultInstance();
	}

	public static function get($namespace = 'def')
	{
		$key = self::getKey($namespace);
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$data = $localcache->get($key);
		if (!$data) {
			$cache = self::getBasicMC();
			$data = $cache->get($key);
			if (!$data) {
				$data = self::load($namespace);
			}
			if ($data) {
				$localcache->set($key, $data, false, 900);
			}
		}

		return $data;
	}
	
	public static function load($namespace = 'def')
	{
		$db = self::getBasicDB();
		$data = $db->get($namespace);
		if ($data) {
			$data = json_decode($data, true);
			$key = self::getKey($namespace);
			$cache = self::getBasicMC();
			$cache->set($key, $data);
		}

		return $data;
	}
	
	public static function reset($namespace = 'def')
	{
		$key = self::getKey($namespace);
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		if (!$data) {
			$data = self::load($namespace);
		}
		$localcache->set($key, $data, false, 900);
		return $data;
	}
	
	public static function update($vaule, $namespace = 'def')
	{
		$db = self::getBasicDB();
		try {
			$db->update($namespace, $vaule);
		} catch(Exception $e) {
			return false;
		}
		
		self::load($namespace);
		return true;
	}

}