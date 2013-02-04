<?php

class Hapyfish2_Alchemy_Cache_Task
{
	public static function getIds($uid)
    {
        if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE==3) {
    	    try {
    	    	$dalTask = Hapyfish2_Alchemy_Dal_Task::getDefaultInstance();
	            $tids = $dalTask->getAll($uid);
        	} catch (Exception $e) {
        		return null;
        	}
    	} else {
	    	$key = 'a:u:alltask:' . $uid;
	        $cache = Hapyfish2_Cache_Factory::getMC($uid);
	        $tids = $cache->get($key);

	        if ($tids === false) {
	        	try {
		            $dalTask = Hapyfish2_Alchemy_Dal_Task::getDefaultInstance();
		            $tids = $dalTask->getAll($uid);
		            if ($tids) {
		            	$cache->add($key, $tids);
		            } else {
		            	return null;
		            }
	        	} catch (Exception $e) {
	        		return null;
	        	}
	        }
    	}

        return $tids;
    }

    public static function loadIds($uid)
    {
		try {
	    	$dalTask = Hapyfish2_Alchemy_Dal_Task::getDefaultInstance();
			$tids = $dalTask->getAll($uid);

			if ($tids) {
				$key = 'a:u:alltask:' . $uid;
				$cache = Hapyfish2_Cache_Factory::getMC($uid);
				$cache->set($key, $tids);
			} else {
				return null;
			}

			return $tids;
		} catch (Exception $e) {
			return null;
		}
    }

    public static function insertId($uid, $tid)
    {
        $key = 'a:u:alltask:' . $uid;

        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $tids = $cache->get($key);

        if ($tids === false) {
			return false;
        }

        $tids[] = $tid;

        return $cache->replace($key, $tids);
    }

    public static function isCompletedTask($uid, $tid)
    {
    	$tids = self::getIds($uid);
    	if (is_array($tids) && in_array($tid, $tids)) {
    		return true;
    	}

    	return false;
    }

    public static function completeTask($uid, $tid, $time = null)
    {
    	if (!$time) {
    		$time = time();
    	}

    	$completed = false;
    	try {
    		$dalTask = Hapyfish2_Alchemy_Dal_Task::getDefaultInstance();
    		$dalTask->insert($uid, $tid, $time);
			self::loadIds($uid);
    		$completed = true;
    	}catch (Exception $e) {
    		info_log('Cache_Task:completeTask:'.$e->getMessage(), 'err.db');
    	}

    	return $completed;
    }

    public static function delCompleteTask($uid, $tid)
    {
    	$del = false;
    	try {
    		$dalTask = Hapyfish2_Alchemy_Dal_Task::getDefaultInstance();
    		$dalTask->del($uid, $tid);
			self::loadIds($uid);
    		$del = true;
    	}catch (Exception $e) {
    		info_log('Cache_Task:delCompleteTask:'.$e->getMessage(), 'err.db');
    	}

    	return $del;
    }
    
}