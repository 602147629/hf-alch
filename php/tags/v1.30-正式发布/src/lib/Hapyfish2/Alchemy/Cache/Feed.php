<?php

class Hapyfish2_Alchemy_Cache_Feed
{
	public static function getFeedData($uid)
	{
		$key = 'i:u:feed:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getFeed($uid);
		return $cache->get($key);
	}
	
	public static function flush($uid)
	{
		$key = 'i:u:feed:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getFeed($uid);
		$cache->set($key, array());
	}
	
	public static function insertMiniFeed($uid, $feed)
    {
        $key = 'i:u:feed:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getFeed($uid);
        $cache->insertMiniFeed($key, $feed);
    }
    
    public static function getNewMiniFeedCount($uid)
    {
		$key = 'i:u:feed:count:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getFeed($uid);
		$count = $cache->get($key);
		if ($count === false) {
			$count = 0;
		}
		
		return $count;
    }
    
	public static function incNewMiniFeedCount($uid)
	{
		$key = 'i:u:feed:count:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getFeed($uid);
		$cache->increment($key, 1);
	}
    
	public static function clearNewMiniFeedCount($uid)
	{
		$key = 'i:u:feed:count:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getFeed($uid);
		$cache->set($key, 0);
	}
    

}