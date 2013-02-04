<?php

class Hapyfish2_Alchemy_HFC_Mix
{
	public static function getUserMix($uid)
    {
        $key = 'a:u:mix:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
    	
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dalMix = Hapyfish2_Alchemy_Dal_Mix::getDefaultInstance();
	            $result = $dalMix->get($uid);
	            if ($result) {
	            	$data = json_decode($result['mix_cids']);
	            	$cache->add($key, $data);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		
        	}
        }
        
        return $data;
    }
    
    public static function updateUserMix($uid, $mixCids, $savedb = true)
    {
        $key = 'a:u:mix:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        
        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
        }

        $data = $mixCids;
        if ($savedb) {
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
        			$info = array('mix_cids'=>json_encode($mixCids));
	        		$dalMix = Hapyfish2_Alchemy_Dal_Mix::getDefaultInstance();
	        		$dalMix->update($uid, $info);
        		} catch (Exception $e) {
        			
        		}
        	}
        	return $ok;
        } else {
        	return $cache->update($key, $data);
        }
    }
    
    public static function addUserMix($uid, $mixCid, $mixCids = null)
    {
    	if (!$mixCids) {
	    	$mixCids = self::getUserMix($uid);
    	}
    	
    	$mixCids[] = (int)$mixCid;
    	return self::updateUserMix($uid, $mixCids);
    }
    
}