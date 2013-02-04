<?php

class Hapyfish2_Alchemy_Cache_Illustrations
{

    public static function getUserIllustrations($uid)
    {
    	$key = 'a:u:illusts:';
    	$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;

    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->get($key);
    	if ($data === false) {
    		try {
	    		$dal = Hapyfish2_Alchemy_Dal_Illustrations::getDefaultInstance();
	    		$info = $dal->get($uid);
	    		if ($info) {
	    			$data = array();
		    		$info = json_decode($info['id']);
		    		foreach ( $info as $v ) {
		    			$data[$v[0]] = array('id' => $v[0], 'new' => $v[1]);
		    		}
	    			$cache->add($key, $data);
	    		}
    		} catch (Exception $e) {
    		    err_log('Hapyfish2_Alchemy_Cache_Illustrations:getUserIllustrations:'.$e->getMessage());
    			return null;
    		}
    	}
    	
    	return $data;
    }

    public static function addUserIllustrations($uid, $id, $illustrations = NULL)
    {
        if (!$illustrations) {
            $illustrations = self::getUserIllustrations($uid);
        }
        
        if ( !isset($illustrations[$id]) ) {
            $illustrations[$id] = array('id' => $id, 'new' => 1);
        }
        else {
        	return false;
        }
        return self::updateUserIllustrations($uid, $illustrations);
    }
    
    public static function updateUserIllustrations($uid, $illustrations, $savedb = true)
    {
    	$key = 'a:u:illusts:';
    	$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        
        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $data = $illustrations;
            $ok = $cache->update($key, $data);
            if ($ok) {
                try {
                    $dal = Hapyfish2_Alchemy_Dal_Illustrations::getDefaultInstance();
                    foreach ($data as $id => $item) {
                        $info[] = array((int)$item['id'], (int)$item['new']);
                    }
                    $info = json_encode($info);
					$update = array('id' => $info);
                    $dal->update($uid, $update);
                } catch (Exception $e) {
                    
                }
            }
            
            return $ok;
        } else {
            $data = $illustrations;
            return $cache->update($key, $data);
        }
    }
    
    
}