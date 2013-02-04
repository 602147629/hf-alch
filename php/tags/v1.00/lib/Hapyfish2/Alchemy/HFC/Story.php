<?php

class Hapyfish2_Alchemy_HFC_Story
{
	/**
	 * 剧情-脚本
	 * @param int $uid
	 */
    public static function getStory($uid)
    {
    	$key = 'a:u:storylist:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
		if($data === false) {
			try {
			    $dal= Hapyfish2_Alchemy_Dal_Story::getDefaultInstance();
	            $data = $dal->get($uid);
	            if ($data) {
	            	$cache->add($key, $data);
	            } else {
	            	return array();
	            }
			}catch (Exception $e) {
				err_log($e->getMessage());
				return array();
			}
		}
		$list = json_decode($data[0], true);
        return $list;
    }
    
    public static function updateStory($uid, $storyInfo, $savedb = false)
    {
		$key = 'a:u:storylist:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = array($storyInfo['list']);
		
        if (!$savedb) {
        	$savedb = $cache->canSaveToDB($key, 900);
        }
		
        if ($savedb) {
        	$ok = $cache->save($key, $data);
        	if ($ok) {
        		try {
        			$info = array(
        				'list' => $storyInfo['list']
        			);
        			$dal = Hapyfish2_Alchemy_Dal_Story::getDefaultInstance();
        			
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
    
    public static function gainStory($uid, $storyId, $savedb = false)
    {
    	$userStory = self::getStory($uid);
    	/*if (!$userStory) {
    		return false;
    	}*/
    	
    	if (!empty($userStory)) {
    		if ( isset($userStory[$storyId]) ) {
    			return false;
    		}
    	} else {
    		$userStory = array();
    	}
    	
    	$userStory[$storyId] = 0;
    	
    	$newUserStory = array('list' => json_encode($userStory));
    	
		return self::updateStory($uid, $newUserStory, $savedb);
    }
        
    public static function delStory($uid, $story, $savedb = false)
    {
    	$userStory = self::getStory($uid);
    	/*if (!$userStory) {
    		return false;
    	}*/
    	
    	if (!empty($userStory)) {
    		$tmp = split(',', $userStory);
    		foreach($tmp as $k=>$v) {
    			if($v == $story) {
    				unset($tmp[$k]);
    			}
    		}
    	} else {
    		$tmp = array();
    	}
    	$newUserStory = array('list' => join(',', $tmp));
    	
		return self::updateStory($uid, $newUserStory, $savedb);
    }
    
    /**
     * 剧情-对白
     * @param int $uid
     */
    public static function getDialog($uid)
    {
    	$key = 'a:u:dialoglist:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
		if($data === false) {
			try {
			    $dal= Hapyfish2_Alchemy_Dal_Story::getDefaultInstance();
	            $data = $dal->get($uid);
	            
	            if ( $data == null ) {
	            	$data = '{"2":{"122":1,"322":1,"422":1,"522":1}}';
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

    public static function updateDialog($uid, $dialogInfo, $savedb = false)
    {
		$key = 'a:u:dialoglist:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getHFC($uid);
		
		$data = json_encode($dialogInfo);
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
        			$dal = Hapyfish2_Alchemy_Dal_Story::getDefaultInstance();
        			$dal->updateDialog($uid, $info);
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