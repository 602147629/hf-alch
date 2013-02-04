<?php

class Hapyfish2_Alchemy_HFC_Training
{
    public static function getAll($uid)
    {
        $ids = Hapyfish2_Alchemy_Cache_Training::getIds($uid);

    	if (!$ids) {
        	return array();
        }

        $keys = array();
        foreach ($ids as $id) {
        	$keys[] = 'a:u:training:' . $uid . ':' . $id;
        }

        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->getMulti($keys);

        /* if ($data === false) {
        	return null;
        } */

        //check all in memory
        $nocacheKeys = array();
        $empty = true;
        foreach ($data as $k => $item) {
        	if ($item == null) {
        		$nocacheKeys[] = $k;
        	} else {
        		$empty = false;
        	}
        }

        if ($empty) {
        	try {
	            $dal = Hapyfish2_Alchemy_Dal_Training::getDefaultInstance();
	            $result = $dal->getAll($uid);
	            
	            if ($result) {
	            	$data = array();
	            	foreach ($result as $item) {
	            		$key = 'a:u:training:' . $uid . ':' . $item['mid'];
	            		
	            		$data[$key] = $item;
	            	}
	            	$cache->addMulti($data);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		err_log('Hapyfish2_Alchemy_HFC_Training:getAll:'.$e->getMessage());
        		return null;
        	}
        } else if (!empty($nocacheKeys)) {
        	foreach ($nocacheKeys as $key) {
        		$tmp = split(':', $key);
        		$data[$key] = self::loadOne($uid, $tmp[4]);
        	}
        }

        $currentTrainingNum = 0;
        $mercenarys = array();
        
        foreach ($data as $item) {
        	if ($item) {
	        	$mercenarys[$item['mid']] = $item;
	        	if ( $item['id'] > 0 ) {
	        		$currentTrainingNum ++;
	        	}
        	}
        }

        $result = array('list' => $mercenarys, 'curTraNum' => $currentTrainingNum);
		return $result;
    }

    public static function reloadAll($uid)
    {
        $ids = Hapyfish2_Alchemy_Cache_Training::reloadIds($uid);
        return self::loadMulti($uid, $ids);
    }

    public static function loadMulti($uid, $ids)
    {
    	$items = array();
    	if ( !empty($ids) ) {
    		foreach ($ids as $id) {
    			$items[$id] = self::loadOne($uid, $id);
    		}
    	}
    	
    	return $items;
    }

    public static function loadOne($uid, $id)
    {
		try {
	    	$dal = Hapyfish2_Alchemy_Dal_Training::getDefaultInstance();
	    	$data = $dal->getOne($uid, $id);
	    	if ($data) {
	    		$key = 'a:u:training:' . $uid . ':' . $id;
	    		
	    		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
	    		$cache->save($key, $data);
	    	} else {
	    		return null;
	    	}

	    	return $data;
		}catch (Exception $e) {
			err_log('Hapyfish2_Alchemy_HFC_Training:loadOne:'.$e->getMessage());
			return null;
		}
    }

	public static function getOne($uid, $id)
    {
    	$key = 'a:u:training:' . $uid . ':' . $id;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$data = $cache->get($key);

    	if ($data === false) {
    		try {
	    		$dal = Hapyfish2_Alchemy_Dal_Training::getDefaultInstance();
	    		$data = $dal->getOne($uid, $id);
	    		if ($data) {
	    			$cache->add($key, $data);
	    		} else {
	    			return null;
	    		}
    		} catch (Exception $e) {
    		    err_log('Hapyfish2_Alchemy_HFC_Training:getOne:'.$e->getMessage());
    			return null;
    		}
    	}

    	return $data;
    }

    public static function updateOne($uid, $id, $info, $savedb = true)
    {
    	$key = 'a:u:training:' . $uid . ':' . $id;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);

    	if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
    	}

    	if ($savedb) {
    		$ok = $cache->save($key, $info);
    		if ($ok) {
	    		//save to db
	    		try {
	    			$dal = Hapyfish2_Alchemy_Dal_Training::getDefaultInstance();
	    			$dal->update($uid, $id, $info);
	    		}
	    		catch (Exception $e) {
                    err_log('Hapyfish2_Alchemy_HFC_Training:updateOne:'.$e->getMessage());
	    		}
    		}
    	}
    	else {
    		$ok = $cache->update($key, $info);
    	}

    	return $ok;
    }

    public static function addOne($uid, $info)
    {
    	$result = false;
    	try {
    		$dal = Hapyfish2_Alchemy_Dal_Training::getDefaultInstance();
    		$dal->insert($uid, $info);

    		Hapyfish2_Alchemy_Cache_Training::reloadIds($uid);
    		self::loadOne($uid, $info['mid']);
    		$result = true;
    	} catch (Exception $e) {
            err_log('Hapyfish2_Alchemy_HFC_Training:addOne:'.$e->getMessage());
    	}

    	return $result;
    }

    public static function delOne($uid, $id)
    {
    	$result = false;
    	
    	$key = 'a:u:training:' . $uid . ':' . $id;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);

    	try {
    		$dal = Hapyfish2_Alchemy_Dal_Training::getDefaultInstance();
    		$dal->delete($uid, $id);
    		$cache->delete($key);
    		self::reloadAll($uid);
    		$result = true;
    	}
    	catch (Exception $e) {
        	err_log('Hapyfish2_Alchemy_HFC_Training:delOne:'.$e->getMessage());
    	}

    	return $result;
    }
    
}