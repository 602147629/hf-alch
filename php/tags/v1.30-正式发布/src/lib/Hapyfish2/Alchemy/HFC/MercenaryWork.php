<?php

class Hapyfish2_Alchemy_HFC_MercenaryWork
{
    public static function getAll($uid)
    {
    	$ids = Hapyfish2_Alchemy_Cache_MercenaryWork::getAllIds($uid);
		if (!$ids) {
            return array();
        }
        
        $keys = array();
        foreach ($ids as $id) {
            $keys[] = 'a:u:merwork:' . $uid . ':' . $id;
        }
        
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->getMulti($keys);
        if ($data === false) {
            return array();
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
                $dalWork = Hapyfish2_Alchemy_Dal_MercenaryWork::getDefaultInstance();
                $result = $dalWork->getAll($uid);
                if ($result) {
                    $data = array();
                    foreach ($result as $item) {
                        $key = 'a:u:merwork:' . $uid . ':' . $item[0];
                        $data[$key] = $item;
                    }
                    $cache->addMulti($data);
                } else {
                    return null;
                }
            } catch (Exception $e) {
            	info_log('[Hapyfish2_Alchemy_HFC_MercenaryWork::getAll]' . $e->getMessage(), 'db.err');
                return null;
            }
        } else if (!empty($nocacheKeys)) {
            foreach ($nocacheKeys as $key) {
                $tmp = explode(':', $key);
                $data[$key] = self::loadOne($uid, $tmp[4]);
            }
        }
        
		if (empty($data)) {
			return array();
		}
		
        $works = array();
        foreach ($data as $item) {
                $works[$item[0]] = array(
                    'uid' => $uid,
                    'id' => $item[0],
                    'finish_time' => $item[1],
                    'role_ids' => split(',', $item[2]),
                    'awards' => $item[3],
                	'state' => $item[4]
                );
        }
        
        return $works;
    }
	    
    public static function addOne($uid, $work)
    {
    	$result = false;
    	try {
    		$dalWork = Hapyfish2_Alchemy_Dal_MercenaryWork::getDefaultInstance();
    		$dalWork->insert($uid, $work);
    		self::loadOne($uid, $work['id']);
			Hapyfish2_Alchemy_Cache_MercenaryWork::reloadAllIds($uid);
    		$result = true;
    	} catch (Exception $e) {
    		info_log('[Hapyfish2_Alchemy_HFC_MercenaryWork::addOne]' . $e->getMessage(), 'db.err');
    	}
    	return $result;
    }
    
    public static function loadOne($uid, $id)
    {
        try {
            $dalWork = Hapyfish2_Alchemy_Dal_MercenaryWork::getDefaultInstance();
            $item = $dalWork->getOne($uid, $id);
            if ($item) {
	            $key = 'a:u:merwork:' . $uid . ':' . $id;
                $cache = Hapyfish2_Cache_Factory::getHFC($uid);
                $cache->save($key, $item);
                return $item;
            }
            
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_MercenaryWork::loadOne]' . $e->getMessage(), 'db.err');
        }
        
        return null;
    }
    
    public static function getOne($uid, $id)
    {
        $key = 'a:u:merwork:' . $uid . ':' . $id;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $item = $cache->get($key);
        
        if ($item === false) {
            try {
                $dalWork = Hapyfish2_Alchemy_Dal_MercenaryWork::getDefaultInstance();
                $item = $dalWork->getOne($uid, $id);
                
                //for test
                if ( $id == 100 ) {
                	if ( !$item ) {
	                    Hapyfish2_Alchemy_Bll_MercenaryWork::setWorkOpened($uid, 100);
	                    $item = $dalWork->getOne($uid, $id);
                	}
                }
                else if ( $id == 2 ) {
                	if ( !$item ) {
	                    Hapyfish2_Alchemy_Bll_MercenaryWork::setWorkOpened($uid, 2);
	                    $item = $dalWork->getOne($uid, $id);
                	}
                }
                
                if ($item) {
                    $cache->add($key, $item);
                } else {
                    return null;
                }
            } catch (Exception $e) {
            	info_log('[Hapyfish2_Alchemy_HFC_MercenaryWork::getOne]' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        
        $data = array('uid' => $uid,
                      'id' => $id,
                      'finish_time' => $item[1],
                      'role_ids' => $item[2],
                      'awards' => $item[3],
                      'state' => $item[4]);
        
        return $data;
    }
    
    public static function updateOne($uid, $id, $work, $savedb = true)
    {
        $key = 'a:u:merwork:' . $uid . ':' . $id;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = array(
            $work['id'], $work['finish_time'], $work['role_ids'], $work['awards'], $work['state']
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
                        'finish_time' => $work['finish_time'],
                        'role_ids' => $work['role_ids'],
                        'awards' => $work['awards'],
                        'state' => $work['state']
                    );
                    
                    $dalWork = Hapyfish2_Alchemy_Dal_MercenaryWork::getDefaultInstance();
                    $dalWork->update($uid, $id, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_MercenaryWork::updateOne]' . $e->getMessage(), 'db.err');
                }
            }
        } else {
            $ok = $cache->update($key, $data);
        }
        
        return $ok;
    }
        
}