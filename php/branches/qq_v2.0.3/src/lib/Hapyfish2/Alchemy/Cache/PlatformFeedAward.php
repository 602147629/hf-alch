<?php

class Hapyfish2_Alchemy_Cache_PlatformFeedAward
{
    const MAX_COUNT = 10;
	
	public static function add($uid)
    {
		$key = 'i:u:platformfeedaward:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if (!$data) {
        	$data = 1;
        } else {
        	$count = self::MAX_COUNT;
			if ($data < $count) {
				$data++;
        	}
        }        
        return $cache->set($key, $data);
    }
    
    public static function checkout($uid)
    {
    	$key = 'i:u:platformfeedaward:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$data = $cache->get($key);
    	if (!$data) {
    		return -1;
    	}
    	
    	if ($data > 0) {
    		$data--;
    		$cache->set($key, $data);
    		
    		//奖励,100金币
    		Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, 100,22);
    		return 1;
    	}
    	
    	return 0;
    }
    
}