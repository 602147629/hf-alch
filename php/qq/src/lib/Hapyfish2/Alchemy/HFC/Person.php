<?php

class Hapyfish2_Alchemy_HFC_Person
{
    /**
     * 地图中NPC动态显示，隐藏
     * @param int $uid
     */
    public static function getPerson($uid)
    {
    	$key = 'a:u:person:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
		if($data === false) {
			try {
			    $dal= Hapyfish2_Alchemy_Dal_Person::getDefaultInstance();
	            $data = $dal->get($uid);
	            
	            //fortest
	            if ( $data == null ) {
	            	$dal->init($uid);
	            }
	            
	            if ($data) {
	            	$cache->add($key, $data);
	            } else {
	            	return null;
	            }
			}catch (Exception $e) {
				err_log($e->getMessage());
				return null;
			}
		}
		$data = json_decode($data, true);
        return $data;
    }

    public static function updatePerson($uid, $personInfo, $savedb = true)
    {
		$key = 'a:u:person:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    
		$data = json_encode($personInfo);
        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
        }
		
        if ($savedb) {
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
        			$info = array(
        				'list' => $data
        			);
        			$dal = Hapyfish2_Alchemy_Dal_Person::getDefaultInstance();
        			$dal->update($uid, $info);
        		} catch (Exception $e) {
        			err_log($e->getMessage());
        		}
        	}
        	
        	return $ok;
        } else {
        	return $cache->update($key, $data);
        }
    }


}