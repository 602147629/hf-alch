<?php

class Hapyfish2_Alchemy_HFC_MapCopy
{
	public static function getAllIds($uid)
	{
    	$key = 'a:u:mapcopy:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$item = $cache->get($key);

    	if ($item === false) {
    		try {
	    		$dal = Hapyfish2_Alchemy_Dal_MapCopy::getDefaultInstance();
	    		$item = $dal->getAllIds($uid);
	    		if ($item) {
	    			$cache->add($key, $item);
	    		}
	    		else {
	    			return null;
	    		}
    		}
    		catch (Exception $e) {
    		    err_log('Hapyfish2_Alchemy_HFC_MapCopy::getAllIds:'.$e->getMessage());
    			return null;
    		}
    	}

    	return $item;
	}
	
	public static function loadAllIds($uid)
	{
		try {
	    	$dal = Hapyfish2_Alchemy_Dal_MapCopy::getDefaultInstance();
	    	$item = $dal->getAllIds($uid);
	    	if ($item) {
	    		$key = 'a:u:mapcopy:' . $uid;
	    		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
	    		$cache->save($key, $item);
	    	}
	    	else {
	    		return null;
	    	}
		}
		catch (Exception $e) {
		    err_log('Hapyfish2_Alchemy_HFC_MapCopy::getAllIds:'.$e->getMessage());
			return null;
		}

    	return $item;
	}
	
    public static function getInfo($uid, $mapId)
    {
    	$key = 'a:u:mapcopy:' . $uid . ':' . $mapId;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$item = $cache->get($key);

    	if ($item === false) {
    		try {
	    		$dal = Hapyfish2_Alchemy_Dal_MapCopy::getDefaultInstance();
	    		$item = $dal->getOne($uid, $mapId);
	    		if ($item) {
	    		    $item[1] = json_decode($item[1], true);
	    			$cache->add($key, $item);
	    		}
	    		else {
	    			return null;
	    		}
    		}
    		catch (Exception $e) {
    		    err_log('Hapyfish2_Alchemy_HFC_MapCopy:getInfo:'.$e->getMessage());
    			return null;
    		}
    	}

    	return array(
        	'map_id' => $item[0],
        	'data' => $item[1],
        	'map_ver' => $item[2],
        	'enter_time' => $item[3]
        );
    }

    public static function loadInfo($uid, $mapId)
    {
		try {
	    	$dal = Hapyfish2_Alchemy_Dal_MapCopy::getDefaultInstance();
	    	$item = $dal->getOne($uid, $mapId);
	    	if ($item) {
	    	    $item[1] = json_decode($item[1], true);
	    		$key = 'a:u:mapcopy:' . $uid . ':' . $mapId;
	    		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
	    		$cache->save($key, $item);
	    	}
	    	else {
	    		return null;
	    	}
		}
		catch (Exception $e) {
		    err_log('Hapyfish2_Alchemy_HFC_MapCopy:loadInfo:'.$e->getMessage());
			return null;
		}

    	return array(
        	'map_id' => $item[0],
        	'data' => $item[1],
        	'map_ver' => $item[2],
    	    'enter_time' => $item[3]
        );
    }

    public static function saveInfo($uid, $mapId, $info)
    {
		try {
            $info['data'] = json_encode($info['data']);
    		$dal = Hapyfish2_Alchemy_Dal_MapCopy::getDefaultInstance();
    		$dal->insUpd($uid, $info);
    		//self::loadAllIds($uid);
    	}
    	catch (Exception $e) {
    	    err_log('Hapyfish2_Alchemy_HFC_MapCopy:saveInfo:'.$e->getMessage());
    	    return false;
    	}
    	return true;
    }

    public static function updateInfo($uid, $mapId, $info, $savedb = false)
    {
    	$key = 'a:u:mapcopy:' . $uid . ':' . $mapId;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$data = array(
    		$info['map_id'], $info['data'], $info['map_ver'], $info['enter_time']
    	);

    	if (!$savedb) {
    		$savedb = $cache->canSaveToDB($key, 900);
    	}

    	if ($savedb) {
    		$ok = $cache->save($key, $data);
    		if ($ok) {
	    		//save to db
	    		self::saveInfo($uid, $mapId, $info);
    		}
    	}
    	else {
    		$ok = $cache->update($key, $data);
    	}

    	return $ok;
    }

	/**
	 * 已解锁门信息
	 * @param int $uid
	 */
	public static function getOpenPortal($uid)
    {
    	$key = 'a:u:openportal:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
		if($data === false) {
			try {
			    $dal= Hapyfish2_Alchemy_Dal_OpenPortal::getDefaultInstance();
	            $data = $dal->get($uid);
	            
	            if ( $data ) {
	            	$cache->add($key, $data);
	            } else {
	            	return array();
	            }
			}catch (Exception $e) {
				err_log($e->getMessage());
				return array();
			}
		}
		
		if ( $data == "" ) {
			$result = array();
		}
		else {
			$result = explode(',', $data);
		}
        return $result;
    }
    
    public static function updateOpenPortal($uid, $portal, $savedb = true)
    {
        $key = 'a:u:openportal:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        
        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        
        $data = implode(',', $portal);
        if ($savedb) {
            $ok = $cache->save($key, $data);
            if ($ok) {
                //save to db
                try {
                    $info = array(
                        'open_portal' => $data
                    );
                    
                    $dal = Hapyfish2_Alchemy_Dal_OpenPortal::getDefaultInstance();
                    $dal->insUpd($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_MapCopy::updateOpenPortal]' . $e->getMessage(), 'db.err');
                }
            }
        } else {
            $ok = $cache->update($key, $data);
        }
        
        return $ok;
    }
    
    /**
     * 解锁门
     * @param int $uid
     * @param int $pid,门id
     */
    public static function addOpenPortal($uid, $pid)
    {
    	$list = self::getOpenPortal($uid);
    	if ( !in_array($pid, $list) ) {
    		$list[] = $pid;
    		return self::updateOpenPortal($uid, $list);
    	}
    	return;
    }
    
	/**
	 * 已打开宝箱信息
	 * @param int $uid
	 */
	public static function getOpenMine($uid)
    {
    	$key = 'a:u:openmine:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
		if($data === false) {
			try {
			    $dal= Hapyfish2_Alchemy_Dal_OpenMine::getDefaultInstance();
	            $data = $dal->get($uid);
	            
	            if ( $data ) {
	            	$cache->add($key, $data);
	            } else {
	            	return array();
	            }
			}catch (Exception $e) {
				err_log($e->getMessage());
				return array();
			}
		}
		
		if ( $data == "" ) {
			$result = array();
		}
		else {
			$result = explode(',', $data);
		}
        return $result;
    }
    
    public static function updateOpenMine($uid, $mine, $savedb = false)
    {
        $key = 'a:u:openmine:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        
        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        
        $data = implode(',', $mine);
        if ($savedb) {
            $ok = $cache->save($key, $data);
            if ($ok) {
                //save to db
                try {
                    $info = array(
                        'open_mine' => $data
                    );
                    
                    $dal = Hapyfish2_Alchemy_Dal_OpenMine::getDefaultInstance();
                    $dal->insUpd($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_MapCopy::updateOpenMine]' . $e->getMessage(), 'db.err');
                }
            }
        } else {
            $ok = $cache->update($key, $data);
        }
        
        return $ok;
    }
    
    /**
     * 解锁门
     * @param int $uid
     * @param int $pid,门id
     */
    public static function addOpenMine($uid, $pid)
    {
    	$list = self::getOpenMine($uid);
    	if ( !in_array($pid, $list) ) {
    		$list[] = $pid;
    		return self::updateOpenMine($uid, $list);
    	}
    	return;
    }
    
}