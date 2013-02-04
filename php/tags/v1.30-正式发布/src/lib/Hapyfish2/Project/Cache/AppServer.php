<?php

class Hapyfish2_Project_Cache_AppServer
{
	const NAMESPACE = 'project:appserver:';

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
		return Hapyfish2_Project_Dal_AppServer::getDefaultInstance();
	}

	public static function getList()
	{
		$key = self::getKey('list');
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		if (!$data) {
			$data = self::loadList();
		}
		return $data;
	}
	
	public static function getWebList()
	{
		$list = self::getList();
		if ($list) {
			$webList = array();
			foreach ($list as $id => $item) {
				if ($item['type'] == 'WEB') {
					$webList[$id] = $item;
				}
			}
			return $webList;
		}
		
		return null;
	}
	
	public static function loadList()
	{
		$db = self::getBasicDB();
		$data = $db->getServerList();
		if ($data) {
			$key = self::getKey('list');
			$cache = self::getBasicMC();
			$cache->set($key, $data);
		}

		return $data;
	}
}