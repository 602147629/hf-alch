<?php

class Hapyfish2_Alchemy_HFC_FightOccupy
{

    public static function loadInfo($uid)
    {
		try {
	    	$dal = Hapyfish2_Alchemy_Dal_FightOccupy::getDefaultInstance();
	    	$data = $dal->getInfo($uid);
	    	if ($data) {
	    		$key = 'a:u:fightoccupy:';
                $key = $key . $uid;
	    		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
	    		$cache->save($key, $data);
	    	} else {
	    		return null;
	    	}

	    	$info =  array(
            	'corps_used' => json_decode($data[0], true),
            	'passive' => json_decode($data[1], true),
            	'initiative' => json_decode($data[2], true),
            	'last_protect_open_tm' => $data[3]
            );

            return $info;
		}
		catch (Exception $e) {
			err_log('Hapyfish2_Alchemy_HFC_FightOccupy:loadInfo:'.$e->getMessage());
			return null;
		}
    }

	public static function getInfo($uid)
    {
    	if (!defined('ENABLE_HFC') || !ENABLE_HFC) {
    	    try {
            	$dal = Hapyfish2_Alchemy_Dal_FightOccupy::getDefaultInstance();
            	$data = $dal->getInfo($uid);
	            if (!$data) {
	            	return null;
	            }
        	}
        	catch (Exception $e) {
        		return null;
        	}
    	}
    	else {
	    	$key = 'a:u:fightoccupy:' . $uid;
	        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
	        $data = $cache->get($key);

	        if ($data === false) {
	        	try {
	            	$dal = Hapyfish2_Alchemy_Dal_FightOccupy::getDefaultInstance();
            	    $data = $dal->getInfo($uid);
		            if ($data) {
		            	$cache->add($key, $data);
		            }
		            else {
		            	return null;
		            }
	        	}
	        	catch (Exception $e) {
	        		return null;
	        	}
	        }
    	}

        $info =  array(
        	'corps_used' => json_decode($data[0], true),
        	'passive' => json_decode($data[1], true),
        	'initiative' => json_decode($data[2], true),
        	'last_protect_open_tm' => $data[3]
        );

        return $info;
    }

    public static function save($uid, $info, $savedb = false)
    {
    	$key = 'a:u:fightoccupy:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
        if (is_array($info['corps_used'])) {
    		$info['corps_used'] = json_encode($info['corps_used']);
    	}
        if (is_array($info['passive'])) {
    		$info['passive'] = json_encode($info['passive']);
    	}
        if (is_array($info['initiative'])) {
    		$info['initiative'] = json_encode($info['initiative']);
    	}

    	$data = array(
    		$info['corps_used'], $info['passive'], $info['initiative'], $info['last_protect_open_tm']
    	);

    	if (!$savedb) {
    		$savedb = $cache->canSaveToDB($key, 900);
    	}

    	if ($savedb) {
    		$ok = $cache->save($key, $data);
    		if ($ok) {
				try {
	            	$dal = Hapyfish2_Alchemy_Dal_FightOccupy::getDefaultInstance();
	            	$dal->update($uid, $info);
	        	}
	        	catch (Exception $e) {
	        		info_log('HFC_FightOccupy:save:'.$e->getMessage(), 'err.db');
	        	}
    		}
    	}
    	else {
    		$ok = $cache->update($key, $data);
    	}

    	return $ok;
    }
    
    public static function getBotInfo($uid)
    {
    	$key = 'a:u:fightoccupy:bot:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
	    $data = $cache->get($key);
	    if($data === false){
	    	return array();
	    }
	    return $data;
    }
    
    public static function saveBot($uid, $info)
    {
    	$key = 'a:u:fightoccupy:bot:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$cache->update($key, $info);
    }

}