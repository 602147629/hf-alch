<?php

class Hapyfish2_Alchemy_HFC_FloorWall
{
    public static function getFloorWall($uid)
    {
        $key = 'a:u:block:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dalFloorWall = Hapyfish2_Alchemy_Dal_FloorWall::getDefaultInstance();
	            $data = $dalFloorWall->get($uid);
	            if ($data) {
	            	$cache->add($key, $data);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		info_log('[Hapyfish2_Alchemy_HFC_FloorWall::getFloorWall]' . $uid . $e->getMessage(), 'hfc.err');
        		return null;
        	}
        }

       	return array('floor' => $data[0], 'wall' => $data[1]);
    }
	    
    public static function updateFloorWall($uid, $info, $savedb = true)
    {
		$data = array($info['floor'], $info['wall']);

    	$key = 'a:u:block:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
        			$dalFloorWall = Hapyfish2_Alchemy_Dal_FloorWall::getDefaultInstance();
        			$dalFloorWall->update($uid, $info['floor'], $info['wall']);
        		} catch (Exception $e) {
        			info_log('[Hapyfish2_Alchemy_HFC_FloorWall::updateUserSp]' . $uid . $e->getMessage(), 'hfc.err');
        		}
        	}
        	return $ok;
        } else {
        	return $cache->update($key, $data);
        }
    }
}