<?php

class Hapyfish2_Alchemy_Cache_Fight
{

    public static function getFightInfo($uid)
    {
    	$key = 'a:u:fight:';
    	$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;

    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
    	if ($data === false) {
			return null;
    	}

    	return $data;
    }

    public static function loadFightInfo($uid, $id)
    {
    	try {
    		$dal = Hapyfish2_Alchemy_Dal_Fight::getDefaultInstance();
    		$data = $dal->getOne($uid, $id);
			if (!$data) {
        		return null;
            }

            $data['rnd_element'] = json_decode($data['rnd_element'], true);
            $data['home_side'] = json_decode($data['home_side'], true);
	        $data['enemy_side'] = json_decode($data['enemy_side'], true);
	        $data['content'] = json_decode($data['content'], true);
            return $data;
    	}
    	catch (Exception $e) {
    	    err_log('Hapyfish2_Alchemy_Cache_Fight:loadFightInfo:'.$e->getMessage());
    		return null;
    	}
    }

    public static function saveFightInfo($uid, $data, $saveDb = false)
    {
    	try {
    		$key = 'a:u:fight:';
    		$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
            $key = $key . $uid;
    		$cache = Hapyfish2_Cache_Factory::getMC($uid);
        	$cache->set($key, $data);

        	if ($saveDb) {
        	    $data['rnd_element'] = json_encode($data['rnd_element']);
        	    $data['home_side'] = json_encode($data['home_side']);
    	        $data['enemy_side'] = json_encode($data['enemy_side']);
    	        $data['content'] = json_encode($data['content']);
        	    $dal = Hapyfish2_Alchemy_Dal_Fight::getDefaultInstance();
    		    $rst = $dal->insUpd($uid, $data);
        	}

            return $data;
    	}catch (Exception $e) {
    	    err_log('Hapyfish2_Alchemy_Cache_Fight:saveFightInfo:'.$e->getMessage());
    		return $data;
    	}
    }



    public static function getFightFriendAssistInfo($uid)
    {
    	$key = 'a:u:fight:friendass:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
    	if ($data === false) {
			return null;
    	}

    	return $data;
    }

    public static function setFightFriendAssistInfo($uid, $info)
    {
        $key = 'a:u:fight:friendass:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, $info);
        return;
    }
}