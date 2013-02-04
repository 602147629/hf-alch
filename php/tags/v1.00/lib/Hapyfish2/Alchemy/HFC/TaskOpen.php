<?php

class Hapyfish2_Alchemy_HFC_TaskOpen
{
	public static function getInfo($uid)
    {
    	if (!defined('ENABLE_HFC') || !ENABLE_HFC) {
    	    try {
            	$dalTaskOpen = Hapyfish2_Alchemy_Dal_TaskOpen::getDefaultInstance();
            	$data = $dalTaskOpen->get($uid);
	            if (!$data) {
	            	return null;
	            }
        	}
        	catch (Exception $e) {
        		return null;
        	}
    	}
    	else {
	    	$key = 'a:u:taskopen:' . $uid;
	        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
	        $data = $cache->get($key);

	        if ($data === false) {
	        	try {
	            	$dalTaskOpen = Hapyfish2_Alchemy_Dal_TaskOpen::getDefaultInstance();
	            	$data = $dalTaskOpen->get($uid);
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
        	'list' => json_decode($data[0], true),
        	'list2' => json_decode($data[1], true),
        	'data' => json_decode($data[2], true),
        	'buffer_list' => json_decode($data[3], true)
        );

        return $info;
    }

    public static function update($uid, $info)
    {
    	$taskOpenInfo = self::getInfo($uid);
        if ($taskOpenInfo) {
        	foreach ($info as $k => $v) {
        		if (isset($taskOpenInfo[$k])) {
    				$taskOpenInfo[$k] = $v;
    			}
        	}

    		return self::save($uid, $taskOpenInfo);
    	}
    	else {
    	    return 0;
    	}
    }

    public static function save($uid, $taskOpenInfo, $savedb = true)
    {
    	$key = 'a:u:taskopen:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
        if (is_array($taskOpenInfo['list'])) {
    		$taskOpenInfo['list'] = json_encode($taskOpenInfo['list']);
    	}
        if (is_array($taskOpenInfo['list2'])) {
    		$taskOpenInfo['list2'] = json_encode($taskOpenInfo['list2']);
    	}
    	if (is_array($taskOpenInfo['data'])) {
    		$taskOpenInfo['data'] = json_encode($taskOpenInfo['data']);
    	}
        if (is_array($taskOpenInfo['buffer_list'])) {
    		$taskOpenInfo['buffer_list'] = json_encode($taskOpenInfo['buffer_list']);
    	}

    	$data = array(
    		$taskOpenInfo['list'], $taskOpenInfo['list2'], $taskOpenInfo['data'], $taskOpenInfo['buffer_list']
    	);

    	if (!$savedb) {
    		$savedb = $cache->canSaveToDB($key, 900);
    	}

    	if ($savedb) {
    		$ok = $cache->save($key, $data);
    		if ($ok) {
				try {
					$info = array(
    					'list' => $taskOpenInfo['list'],
						'list2' => $taskOpenInfo['list2'],
    					'data' => $taskOpenInfo['data'],
						'buffer_list' => $taskOpenInfo['buffer_list']
					);
	            	$dalTaskOpen = Hapyfish2_Alchemy_Dal_TaskOpen::getDefaultInstance();
	            	$dalTaskOpen->update($uid, $info);
	        	}
	        	catch (Exception $e) {
	        		info_log('HFC_TaskOpen:save:'.$e->getMessage(), 'err.db');
	        	}
    		}
    	}
    	else {
    		$ok = $cache->update($key, $data);
    	}

    	return $ok;
    }

}