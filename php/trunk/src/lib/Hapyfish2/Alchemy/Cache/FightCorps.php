<?php

class Hapyfish2_Alchemy_Cache_FightCorps
{

    public static function getFightCorpsInfo($uid)
    {
    	$key = 'a:u:fightcorps:';
    	$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;

    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
    	if ($data === false) {
    		try {
	    		$dal = Hapyfish2_Alchemy_Dal_FightCorps::getDefaultInstance();
	    		$data = $dal->getCorpsInfo($uid);
	    		if ($data) {
	    		    $data = json_decode($data, true);
	    			$cache->add($key, $data);
	    		} else {
	    			return null;
	    		}
    		} catch (Exception $e) {
    		    err_log('Hapyfish2_Alchemy_Cache_FightCorps:getFightCorpsInfo:'.$e->getMessage());
    			return null;
    		}
    	}

    	return $data;
    }

    public static function loadFightCorpsInfo($uid)
    {
    	try {
    		$dal = Hapyfish2_Alchemy_Dal_FightCorps::getDefaultInstance();
    		$data = $dal->getCorpsInfo($uid);
			if ($data) {
			    $data = json_decode($data, true);
        		$key = 'a:u:fightcorps:';
        		$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
                $key = $key . $uid;
        		$cache = Hapyfish2_Cache_Factory::getMC($uid);
            	$cache->set($key, $data);
            } else {
            	return null;
            }

            return $data;
    	}catch (Exception $e) {
    	    err_log('Hapyfish2_Alchemy_Cache_FightCorps:loadFightCorpsInfo:'.$e->getMessage());
    		return null;
    	}
    }

    public static function saveFightCorpsInfo($uid, $data)
    {
    	try {
    	    $key = 'a:u:fightcorps:';
    		$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
            $key = $key . $uid;
    		$cache = Hapyfish2_Cache_Factory::getMC($uid);
        	$cache->set($key, $data);

    	    $dal = Hapyfish2_Alchemy_Dal_FightCorps::getDefaultInstance();
    		$data = json_encode($data);
    		$dal->insUpd($uid, $data);

            return true;
    	}catch (Exception $e) {
    	    err_log('Hapyfish2_Alchemy_Cache_FightCorps:saveFightCorpsInfo:'.$e->getMessage());
    		return null;
    	}
    }

}