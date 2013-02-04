<?php

class Hapyfish2_Alchemy_HFC_Help
{
	public static function get($uid)
    {
    	$key = 'a:u:help:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
		if($data === false) {
			try {
			    $dal= Hapyfish2_Alchemy_Dal_Help::getDefaultInstance();
	            $data = $dal->get($uid);
	            	            
	            if ($data) {
	            	$cache->add($key, $data);
	            } else {
	            	//fortest
        			$new = array('uid'=>$uid, 'id' => 10, 'idx'=>1, 'status'=>0, 'finish_ids'=> '1,2,3,4,5,6,7,8,9,10');
	            	$dal->insert($uid, $new);
	            	$data = $dal->get($uid);
	            	
	            	//return array();
	            }
			}catch (Exception $e) {
				err_log($e->getMessage());
				return null;
			}
		}
		$info = array('id' => $data[0],
					  'idx' => $data[1],
					  'status' => $data[2],
					  'finish_ids' => $data[3]);
        return $info;
    }
    
    public static function update($uid, $help, $savedb = false)
    {
        $key = 'a:u:help:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = array(
            $help['id'], $help['idx'], $help['status'], $help['finish_ids']
        );
        
        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        
        if ($savedb) {
            $ok = $cache->save($key, $data);
            if ($ok) {
                //save to db
                try {
                    $info = array(
                        'id' => $help['id'],
                        'idx' => $help['idx'],
                        'status' => $help['status'],
                    	'finish_ids' => $help['finish_ids']
                    );
                    
                    $dalFurnace = Hapyfish2_Alchemy_Dal_Help::getDefaultInstance();
                    $dalFurnace->update($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_Help::update]' . $e->getMessage(), 'db.err');
                }
            }
        } else {
            $ok = $cache->update($key, $data);
        }
        
        return $ok;
    }
    
	public static function getUnlockFunc($uid)
    {
    	$key = 'a:u:unlockfunc:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
		if($data === false) {
			try {
			    $dal= Hapyfish2_Alchemy_Dal_UnlockFunc::getDefaultInstance();
	            $data = $dal->get($uid);
	            
	            if ($data) {
	            	$cache->add($key, $data);
	            } else {
	            	//for test
	            	$dal->init($uid);
	            	$data = $dal->get($uid);
	            	return array();
	            }
			}catch (Exception $e) {
				err_log($e->getMessage());
				return null;
			}
		}
        return explode(',', $data);
    }
    
    public static function updateUnlockFunc($uid, $func, $savedb = false)
    {
        $key = 'a:u:unlockfunc:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        
        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        
        $data = implode(',', $func);
        if ($savedb) {
            $ok = $cache->save($key, $data);
            if ($ok) {
                //save to db
                try {
                    $info = array(
                        'func_list' => $data
                    );
                    
                    $dalFurnace = Hapyfish2_Alchemy_Dal_UnlockFunc::getDefaultInstance();
                    $dalFurnace->update($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_Help::updateUnlockFunc]' . $e->getMessage(), 'db.err');
                }
            }
        } else {
            $ok = $cache->update($key, $data);
        }
        
        return $ok;
    }
    
}