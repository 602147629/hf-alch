<?php

class Hapyfish2_Alchemy_HFC_Strengthen
{
    public static function get($uid)
    {
    	$key = 'a:u:strthen:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	return array();
        }
        
        return $data;
    }
    
    public static function update($uid, $array)
    {
        $key = 'a:u:strthen:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $result = $cache->update($key, $array);
        
    	return $result;
    }
    

}