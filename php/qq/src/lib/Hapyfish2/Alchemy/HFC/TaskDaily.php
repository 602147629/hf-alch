<?php

class Hapyfish2_Alchemy_HFC_TaskDaily
{
	public static function getInfo($uid)
    {
    	if (!defined('ENABLE_HFC') || !ENABLE_HFC) {
    	    try {
            	$dal = Hapyfish2_Alchemy_Dal_TaskDaily::getDefaultInstance();
            	$data = $dal->get($uid);
	            if (!$data) {
	            	return null;
	            }
        	}
        	catch (Exception $e) {
        		return null;
        	}
    	}
    	else {
	    	$key = 'a:u:taskdly:' . $uid;
	        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
	        $data = $cache->get($key);

	        if ($data === false) {
	        	try {
	            	$dal = Hapyfish2_Alchemy_Dal_TaskDaily::getDefaultInstance();
            	    $data = $dal->get($uid);
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
		$nowTm = time();
        $taskDaily =  array(
        	'list' => json_decode($data[0], true),
        	'data' => json_decode($data[1], true),
        	'finish' => json_decode($data[2], true),
        	'refresh_tm' => $data[3]
        );
                //if need refresh task
        if ( !$taskDaily['refresh_tm'] || date('Ymd',$taskDaily['refresh_tm'])!=date('Ymd',$nowTm)|| empty($taskDaily['list']) ) {
            $ids = Hapyfish2_Alchemy_Cache_Basic::getDailyTaskIds();
            $taskDaily['list'] = $ids;
            $taskDaily['finish'] = array();
            $taskDaily['data'] = array();
            $taskDaily['refresh_tm'] = $nowTm;
            Hapyfish2_Alchemy_HFC_TaskDaily::save($uid, $taskDaily, true);
        }
        return $taskDaily;
    }

    public static function update($uid, $info)
    {
    	$taskDailyInfo = self::getInfo($uid);
        if ($taskDailyInfo) {
        	foreach ($info as $k => $v) {
        		if (isset($taskDailyInfo[$k])) {
    				$taskDailyInfo[$k] = $v;
    			}
        	}

    		return self::save($uid, $taskDailyInfo);
    	}
    	else {
    	    return 0;
    	}
    }

    public static function save($uid, $taskDailyInfo, $savedb = true)
    {
    	$key = 'a:u:taskdly:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
        if (is_array($taskDailyInfo['list'])) {
    		$taskDailyInfo['list'] = json_encode($taskDailyInfo['list']);
    	}
    	if (is_array($taskDailyInfo['data'])) {
    		$taskDailyInfo['data'] = json_encode($taskDailyInfo['data']);
    	}
    	if (is_array($taskDailyInfo['finish'])) {
    		$taskDailyInfo['finish'] = json_encode($taskDailyInfo['finish']);
    	}

    	$data = array(
    		$taskDailyInfo['list'], $taskDailyInfo['data'],$taskDailyInfo['finish'], $taskDailyInfo['refresh_tm']
    	);

    	if (!$savedb) {
    		$savedb = $cache->canSaveToDB($key, 900);
    	}

    	if ($savedb) {
    		$ok = $cache->save($key, $data);
    		if ($ok) {
				try {
					$info = array(
    					'list' => $taskDailyInfo['list'],
    					'data' => $taskDailyInfo['data'],
						'finish'=> $taskDailyInfo['finish'],
						'refresh_tm' => (int)$taskDailyInfo['refresh_tm']
					);
	            	$dal = Hapyfish2_Alchemy_Dal_TaskDaily::getDefaultInstance();
	            	$dal->update($uid, $info);
	        	}
	        	catch (Exception $e) {
	        		info_log('HFC_TaskDaily:save:'.$e->getMessage(), 'err.db');
	        	}
    		}
    	}
    	else {
    		$ok = $cache->update($key, $data);
    	}

    	return $ok;
    }

}