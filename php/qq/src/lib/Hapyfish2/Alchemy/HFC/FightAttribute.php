<?php

class Hapyfish2_Alchemy_HFC_FightAttribute
{

    public static function loadInfo($uid)
    {
		try {
	    	$dal = Hapyfish2_Alchemy_Dal_FightAttribute::getDefaultInstance();
	    	$data = $dal->getInfo($uid);
	    	if ($data) {
	    	    $data['weapon'] = json_decode($data['weapon'], true);
                $data['skill'] = json_decode($data['skill'], true);
				$data['skill_gain'] = json_decode($data['skill_gain'], true);
	    		$key = 'a:u:fightattrib:';
        		//$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
                $key = $key . $uid;
	    		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
	    		$cache->save($key, $data);
	    	} else {
	    		return null;
	    	}

	    	return $data;
		}
		catch (Exception $e) {
			err_log('Hapyfish2_Alchemy_HFC_FightAttribute:loadInfo:'.$e->getMessage());
			return null;
		}
    }

	public static function getInfo($uid)
    {
    	$key = 'a:u:fightattrib:';
    	//$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$data = $cache->get($key);

    	if ($data === false || $data == null) {
    		try {
	    		$dal = Hapyfish2_Alchemy_Dal_FightAttribute::getDefaultInstance();
	    		$data = $dal->getInfo($uid);
	    		if ($data) {
	    		    $data['weapon'] = json_decode($data['weapon'], true);
                    $data['skill'] = json_decode($data['skill'], true);
					$data['skill_gain'] = json_decode($data['skill_gain'], true);
	    			$cache->add($key, $data);
	    		} else {
	    			return null;
	    		}
    		}
    		catch (Exception $e) {
    		    err_log('Hapyfish2_Alchemy_HFC_FightAttribute:getInfo:'.$e->getMessage());
    			return null;
    		}
    	}

    	return $data;
    }

    public static function updateInfo($uid, $info, $savedb = false)
    {
    	$key = 'a:u:fightattrib:';
		//$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);

    	if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
    	}

    	if ($savedb) {
    		$ok = $cache->save($key, $info);
    		if ($ok) {
	    		//save to db
	    		try {
	    		    $info['weapon'] = json_encode($info['weapon']);
                    $info['skill'] = json_encode($info['skill']);
                    $info['skill_gain'] = json_encode($info['skill_gain']);
	    			$dal = Hapyfish2_Alchemy_Dal_FightAttribute::getDefaultInstance();
	    			$dal->update($uid, $info);
	    		}
	    		catch (Exception $e) {
                    err_log('Hapyfish2_Alchemy_HFC_FightAttribute:updateInfo:'.$e->getMessage());
	    		}
    		}
    	}
    	else {
    		$ok = $cache->update($key, $info);
    	}

    	return $ok;
    }

}