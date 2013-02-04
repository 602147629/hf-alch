<?php

class Hapyfish2_Alchemy_Cache_UniqueItem
{

    public static function getInfo($uid)
    {
    	$key = 'a:u:uniqueitem:';
    	//$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;

    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
    	if ($data === false) {
    		try {
	    		$dal = Hapyfish2_Alchemy_Dal_UniqueItem::getDefaultInstance();
	    		$ids = $dal->getInfo($uid);
	    		if ($ids) {
	    		    $data = json_decode($ids, true);
	    			$cache->add($key, $data);
	    		} else {
	    			return null;
	    		}
    		} catch (Exception $e) {
    		    err_log('Hapyfish2_Alchemy_Cache_UniqueItem:getInfo:'.$e->getMessage());
    			return null;
    		}
    	}

    	return $data;
    }

    public static function loadInfo($uid)
    {
    	try {
    		$dal = Hapyfish2_Alchemy_Dal_UniqueItem::getDefaultInstance();
    		$ids = $dal->getInfo($uid);
			if ($ids) {
			    $data = json_decode($ids, true);
        		$key = 'a:u:uniqueitem:';
        		//$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
                $key = $key . $uid;
        		$cache = Hapyfish2_Cache_Factory::getMC($uid);
            	$cache->set($key, $data);
            } else {
            	return null;
            }

            return $data;
    	}catch (Exception $e) {
    	    err_log('Hapyfish2_Alchemy_Cache_UniqueItem:loadInfo:'.$e->getMessage());
    		return null;
    	}
    }

    public static function saveInfo($uid, $data)
    {
    	try {
    	    $dal = Hapyfish2_Alchemy_Dal_UniqueItem::getDefaultInstance();
    	    $ids = $dal->getInfo($uid);
			if (!$ids) {
                $dal->init($uid);
			}
            $info = array('item_ids' => json_encode($data));
    		$rst = $dal->update($uid, $info);

    	    $key = 'a:u:uniqueitem:';
    		//$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
            $key = $key . $uid;
    		$cache = Hapyfish2_Cache_Factory::getMC($uid);
        	$cache->set($key, $data);

            return true;
    	}catch (Exception $e) {
    	    err_log('Hapyfish2_Alchemy_Cache_UniqueItem:saveInfo:'.$e->getMessage());
    		return null;
    	}
    }

}