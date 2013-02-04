<?php

class Hapyfish2_Project_Bll_Notice
{	
	public static function getNoticeList()
	{
		return Hapyfish2_Project_Cache_Notice::getNoticeList();
	}
	
	public static function add($info)
	{
		try {
			$db = Hapyfish2_Project_Dal_Notice::getDefaultInstance();
			$db->addNotice($info);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	public static function update($id, $info)
	{
		try {
			$db = Hapyfish2_Project_Dal_Notice::getDefaultInstance();
			$db->updateNotice($id, $info);
			Hapyfish2_Project_Cache_Notice::loadNoticeList();
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	public static function loadToMemcached()
	{
		return Hapyfish2_Project_Cache_Notice::loadNoticeList();
	}
	
	public static function loadToAPC()
	{
		$key = Hapyfish2_Project_Cache_Notice::getNoticeKey();
		$cache = Hapyfish2_Project_Cache_Notice::getBasicMC();
		$list = $cache->get($key);
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$localcache->set($key, $list, false, 900);
		return $list;
	}
	
	public static function getFromMemcached()
	{
		$key = Hapyfish2_Project_Cache_Notice::getNoticeKey();
		$cache = Hapyfish2_Ipanda_Cache_BasicInfo::getBasicMC();
		return $cache->get($key);
	}
	
	public static function getFromAPC()
	{
		$key = Hapyfish2_Project_Cache_Notice::getNoticeKey();
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		return $localcache->get($key);
	}
}