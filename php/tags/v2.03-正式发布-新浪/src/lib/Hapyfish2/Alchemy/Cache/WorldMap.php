<?php

class Hapyfish2_Alchemy_Cache_WorldMap
{

    public static function getInfo($uid)
    {
    	$key = 'a:u:worldmap:';
    	$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;

    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
    	if ($data === false) {
    		try {
	    		$dal = Hapyfish2_Alchemy_Dal_WorldMap::getDefaultInstance();
	    		$mapIds = $dal->getInfo($uid);
	    		if ($mapIds) {
	    		    $data = json_decode($mapIds, true);
	    			$cache->add($key, $data);
	    		} else {
	    			return null;
	    		}
    		} catch (Exception $e) {
    		    err_log('Hapyfish2_Alchemy_Cache_WorldMap:getInfo:'.$e->getMessage());
    			return null;
    		}
    	}

    	return $data;
    }

    public static function loadInfo($uid)
    {
    	try {
    		$dal = Hapyfish2_Alchemy_Dal_WorldMap::getDefaultInstance();
    		$mapIds = $dal->getInfo($uid);
			if ($mapIds) {
			    $data = json_decode($mapIds, true);
        		$key = 'a:u:worldmap:';
        		$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
                $key = $key . $uid;
        		$cache = Hapyfish2_Cache_Factory::getMC($uid);
            	$cache->set($key, $data);
            } else {
            	return null;
            }

            return $data;
    	}catch (Exception $e) {
    	    err_log('Hapyfish2_Alchemy_Cache_WorldMap:loadInfo:'.$e->getMessage());
    		return null;
    	}
    }

    public static function saveInfo($uid, $data)
    {
    	try {
    	    $dal = Hapyfish2_Alchemy_Dal_WorldMap::getDefaultInstance();
            $info = array('map_ids' => json_encode($data));
    		$rst = $dal->update($uid, $info);

    	    $key = 'a:u:worldmap:';
    		$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
            $key = $key . $uid;
    		$cache = Hapyfish2_Cache_Factory::getMC($uid);
        	$cache->set($key, $data);

            return true;
    	}catch (Exception $e) {
    	    err_log('Hapyfish2_Alchemy_Cache_WorldMap:saveInfo:'.$e->getMessage());
    		return null;
    	}
    }

}