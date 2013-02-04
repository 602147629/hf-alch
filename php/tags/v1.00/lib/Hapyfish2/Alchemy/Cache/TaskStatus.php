<?php

class Hapyfish2_Alchemy_Cache_TaskStatus
{
    public static function get($uid, $taskList)
    {
    	$key = 'a:u:taskstatus:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		$status = array();
		if ($data === false) {
			foreach ($taskList as $tid) {
				$status[$tid] = 0;
			}
			$cache->add($key, $status);
		} else {
			foreach ($taskList as $tid) {
				if (!isset($data[$tid])) {
					$status[$tid] = 0;
				} else {
					$status[$tid] = $data[$tid];
				}
			}
		}

		return $status;
    }

    public static function update($uid, $taskList, $taskId)
    {
		$status = self::get($uid, $taskList);

    	if (isset($status[$taskId]) && $status[$taskId] == 0) {
			$status[$taskId] = 1;
    		$key = 'a:u:taskstatus:' . $uid;
        	$cache = Hapyfish2_Cache_Factory::getMC($uid);
        	$cache->set($key, $status);
        	return 0;
		} else {
			return 1;
		}
    }
}