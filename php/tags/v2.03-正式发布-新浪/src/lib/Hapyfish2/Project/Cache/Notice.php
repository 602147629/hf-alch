<?php

class Hapyfish2_Project_Cache_Notice
{
	const NAMESPACE = 'project:notice:';

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
		return Hapyfish2_Project_Dal_Notice::getDefaultInstance();
	}

	public static function getNoticeKey()
	{
		return self::getKey('noticelist');
	}
	
	public static function getNoticeList()
	{
		$key = self::getNoticeKey();
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = array();
			}
			$localcache->set($key, $list, false, 900);
		}
		
		return $list;
	}
	
	public static function loadNoticeList()
	{
		$db = self::getBasicDB();
		$list = $db->getNoticeList();
		if ($list) {
			$main = array();
			$sub = array();
			$pic = array();
			foreach ($list as $item){
				if ($item['position'] == 1) {
					$main[] = $item;
				} else if($item['position'] == 2){
					$sub[] = $item;
		 	} else if($item['position'] == 3){
					$pic[] = $item;
				}
			}
			$info = array('main' => $main, 'sub' => $sub, 'pic' => $pic);
			
			$key = self::getNoticeKey();
			$cache = self::getBasicMC();
			$cache->set($key, $info);
		} else {
			$info = array();
		}
		return $info;
	}
}