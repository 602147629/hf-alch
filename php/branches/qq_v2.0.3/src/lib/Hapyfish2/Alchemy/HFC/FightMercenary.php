<?php

class Hapyfish2_Alchemy_HFC_FightMercenary
{
    public static function getAll($uid)
    {
        $ids = Hapyfish2_Alchemy_Cache_FightMercenary::getMercenaryIds($uid);

    	if (!$ids) {
        	return null;
        }

        $keys = array();
        foreach ($ids as $id) {
        	$keys[] = 'a:u:mercenary:' . $uid . ':' . $id;
        }

        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->getMulti($keys);

        if ($data === false) {
        	return null;
        }

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
	            $dal = Hapyfish2_Alchemy_Dal_FightMercenary::getDefaultInstance();
	            $result = $dal->getAll($uid);
	            if ($result) {
	            	$data = array();
	            	foreach ($result as $item) {
	            		$key = 'a:u:mercenary:' . $uid . ':' . $item['mid'];
	            		$item['weapon'] = json_decode($item['weapon'], true);
                        $item['skill'] = json_decode($item['skill'], true);
                        $item['skill_gain'] = json_decode($item['skill_gain'], true);
                        $item['skill_list'] = json_decode($item['skill_list'], true);
	            		$data[$key] = $item;
	            	}
	            	$cache->addMulti($data);
	            } else {
	            	return null;
	            }
        	} catch (Exception $e) {
        		err_log('Hapyfish2_Alchemy_HFC_FightMercenary:getAll:'.$e->getMessage());
        		return null;
        	}
        } else if (!empty($nocacheKeys)) {
        	foreach ($nocacheKeys as $key) {
        		$tmp = split(':', $key);
        		$data[$key] = self::loadOne($uid, $tmp[4]);
        	}
        }

        $mercenarys = array();
        foreach ($data as &$item) {
        	if ($item) {
	        	$mercenarys[$item['mid']] = $item;
        	}
        }

		return $mercenarys;
    }

    public static function reloadAll($uid)
    {
        $ids = Hapyfish2_Alchemy_Cache_FightMercenary::reloadMercenaryIds($uid);
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
	    	$dal = Hapyfish2_Alchemy_Dal_FightMercenary::getDefaultInstance();
	    	$data = $dal->getOne($uid, $id);
	    	if ($data) {
	    		$key = 'a:u:mercenary:' . $uid . ':' . $id;
	    		$data['weapon'] = json_decode($data['weapon'], true);
                $data['skill'] = json_decode($data['skill'], true);
                $data['skill_gain'] = json_decode($data['skill_gain'], true);
                $data['skill_list'] = json_decode($data['skill_list'], true);
	    		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
	    		$cache->save($key, $data);
	    	} else {
	    		return null;
	    	}

	    	return $data;
		}catch (Exception $e) {
			err_log('Hapyfish2_Alchemy_HFC_FightMercenary:loadOne:'.$e->getMessage());
			return null;
		}
    }

	public static function getOne($uid, $id)
    {
    	$key = 'a:u:mercenary:' . $uid . ':' . $id;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$data = $cache->get($key);

    	if ($data === false) {
    		try {
	    		$dal = Hapyfish2_Alchemy_Dal_FightMercenary::getDefaultInstance();
	    		$data = $dal->getOne($uid, $id);
	    		if ($data) {
	    		    $data['weapon'] = json_decode($data['weapon'], true);
                    $data['skill'] = json_decode($data['skill'], true);
                    $data['skill_gain'] = json_decode($data['skill_gain'], true);
                    $data['skill_list'] = json_decode($data['skill_list'], true);
	    			$cache->add($key, $data);
	    		} else {
	    			return null;
	    		}
    		} catch (Exception $e) {
    		    err_log('Hapyfish2_Alchemy_HFC_FightMercenary:getOne:'.$e->getMessage());
    			return null;
    		}
    	}

    	return $data;
    }

    public static function updateOne($uid, $id, $info, $savedb = true)
    {
    	$key = 'a:u:mercenary:' . $uid . ':' . $id;
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
    	            $info['skill_list'] = json_encode($info['skill_list']);
	    			$dal = Hapyfish2_Alchemy_Dal_FightMercenary::getDefaultInstance();
	    			$dal->update($uid, $id, $info);
	    		}
	    		catch (Exception $e) {
                    err_log('Hapyfish2_Alchemy_HFC_FightMercenary:updateOne:'.$e->getMessage());
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

    	    $info['weapon'] = json_encode($info['weapon']);
            $info['skill'] = json_encode($info['skill']);
    		$dal = Hapyfish2_Alchemy_Dal_FightMercenary::getDefaultInstance();
    		$dal->insert($uid, $info);

    		Hapyfish2_Alchemy_Cache_FightMercenary::reloadMercenaryIds($uid);
    		self::loadOne($uid, $info['mid']);
    		$result = true;
    	} catch (Exception $e) {
            err_log('Hapyfish2_Alchemy_HFC_FightMercenary:addOne:'.$e->getMessage());
    	}

    	return $result;
    }

    public static function delOne($uid, $id)
    {
    	$result = false;
    	
    	$key = 'a:u:mercenary:' . $uid . ':' . $id;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);

    	try {
    		$dal = Hapyfish2_Alchemy_Dal_FightMercenary::getDefaultInstance();
    		$dal->delete($uid, $id);
    		$cache->delete($key);
    		self::reloadAll($uid);
    		$result = true;
    	}
    	catch (Exception $e) {
        	err_log('Hapyfish2_Alchemy_HFC_FightMercenary:delOne:'.$e->getMessage());
    	}

    	return $result;
    }
    
}