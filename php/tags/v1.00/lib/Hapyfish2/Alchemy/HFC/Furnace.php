<?php

class Hapyfish2_Alchemy_HFC_Furnace
{
    public static function getAll($uid)
    {
    	$ids = Hapyfish2_Alchemy_Cache_Furnace::getAllIds($uid);
		if (!$ids) {
            return array();
        }
        
        $keys = array();
        foreach ($ids as $id) {
            $keys[] = 'a:u:furnace:' . $uid . ':' . $id;
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
                $dalFurnace = Hapyfish2_Alchemy_Dal_Furnace::getDefaultInstance();
                $result = $dalFurnace->getAll($uid);
                if ($result) {
                    $data = array();
                    foreach ($result as $item) {
                        $key = 'a:u:furnace:' . $uid . ':' . $item[0];
                        $data[$key] = $item;
                    }
                    $cache->addMulti($data);
                } else {
                    return null;
                }
            } catch (Exception $e) {
            	info_log('[Hapyfish2_Alchemy_HFC_Furnace::getAll]' . $e->getMessage(), 'db.err');
                return null;
            }
        } else if (!empty($nocacheKeys)) {
            foreach ($nocacheKeys as $key) {
                $tmp = split(':', $key);
                $data[$key] = self::loadOne($uid, $tmp[4]);
            }
        }
        
        return $data;
    }
	
	//工作台,type:4
    public static function getOnRoom($uid, $savehighcache = false)
    {
		$data = self::getAll($uid);
		if (empty($data)) {
			return array();
		}
        
        $furnaces = array();
        $vaildIds = array();
        foreach ($data as $item) {
            if ($item && $item[10] == 1) {
                $id = $item[0];
                $vaildIds[] = $id;
                $list = array(
                    'uid' => $uid,
                    'id' => $id,
                    'furnace_id' => $item[1],
                    'x' => $item[2],
                    'z' => $item[3],
                	'm' => $item[4],
                    'cid' => $item[5],
                    'start_time' => $item[6],
                    'need_time' => $item[7],
                    'cur_probability' => $item[8],
                    'num' => $item[9],
                    'status' => $item[10]
                );
                $furnaces[] = $list;
            }
        }
        
        $data = array('ids' => $vaildIds, 'furnaces' => $furnaces);
        
        if ($savehighcache) {
	        $key = 'a:u:fun:onroom:' . $uid;
            $hc = Hapyfish2_Cache_HighCache::getInstance();
            $hc->set($key, $data);
        }
        
        return $data;
    }
    
	//工作台,type:4
    public static function getInBag($uid)
    {
		$data = self::getAll($uid);
		if (empty($data)) {
			return array();
		}
        
        $furnaces = array();
        foreach ($data as $item) {
            if ($item && $item[10] == 0) {
                $furnaces[$item[0]] = array(
                    'uid' => $uid,
                    'id' => $item[0],
                    'furnace_id' => $item[1],
                    'x' => $item[2],
                    'z' => $item[3],
                	'm' => $item[4],
                    'cid' => $item[5],
                    'start_time' => $item[6],
                    'need_time' => $item[7],
                    'cur_probability' => $item[8],
                    'num' => $item[9],
                    'status' => $item[10]
                );
            }
        }
        
        return $furnaces;
    }
    
    public static function getBagByCid($uid, $cid)
    {
    	$data = self::getAll($uid);
		if (empty($data)) {
			return null;
		}
		
        foreach ($data as $item) {
            if ($item && $item[10] == 0 && $item[1] == $cid) {
                $furnace = array(
                    'uid' => $uid,
                    'id' => $item[0],
                    'furnace_id' => $item[1],
                    'x' => $item[2],
                    'z' => $item[3],
                	'm' => $item[4],
                    'cid' => $item[5],
                    'start_time' => $item[6],
                    'need_time' => $item[7],
                    'cur_probability' => $item[8],
                    'num' => $item[9],
                    'status' => $item[10]
                );
                return $furnace;
            }
        }
        
        return null;
    }
    
    public static function addOne($uid, $furnace)
    {
    	$result = false;
    	try {
    		$id = self::getNewFurnaceId($uid);
    		if ($id > 0) {
    			$furnace['id'] = $id;
	    		$dalFurnace = Hapyfish2_Alchemy_Dal_Furnace::getDefaultInstance();
	    		$dalFurnace->insert($uid, $furnace);
	    		self::loadOne($uid, $id);
				Hapyfish2_Alchemy_Cache_Furnace::reloadAllIds($uid);
	    		$result = true;
	    		
	    		$addItem = array($furnace['furnace_id'], 1);
	    		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'addItems', array($addItem));
    		}
    	} catch (Exception $e) {
    		info_log('[Hapyfish2_Alchemy_HFC_Furnace::addOne]' . $e->getMessage(), 'db.err');
    	}
    	return $result;
    }
    
    public static function loadOne($uid, $id)
    {
        try {
            $dalFurnace = Hapyfish2_Alchemy_Dal_Furnace::getDefaultInstance();
            $item = $dalFurnace->getOne($uid, $id);
            if ($item) {
	            $key = 'a:u:furnace:' . $uid . ':' . $id;
                $cache = Hapyfish2_Cache_Factory::getHFC($uid);
                $cache->save($key, $item);
                return $item;
            }
            
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_Furnace::loadOne]' . $e->getMessage(), 'db.err');
        }
        
        return null;
    }
    
    public static function getOne($uid, $id)
    {
        $key = 'a:u:furnace:' . $uid . ':' . $id;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $item = $cache->get($key);
        
        if ($item === false) {
            try {
                $dalFurnace = Hapyfish2_Alchemy_Dal_Furnace::getDefaultInstance();
                $item = $dalFurnace->getOne($uid, $id);
                if ($item) {
                    $cache->add($key, $item);
                } else {
                    return null;
                }
            } catch (Exception $e) {
            	info_log('[Hapyfish2_Alchemy_HFC_Furnace::getOne]' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        
        $data = array('uid' => $uid,
                      'id' => $id,
                      'furnace_id' => $item[1],
                      'x' => $item[2],
                      'z' => $item[3],
        			  'm' => $item[4],
                      'cid' => $item[5],
                      'start_time' => $item[6],
                      'need_time' => $item[7],
                      'cur_probability' => $item[8],
                      'num' => $item[9],
                      'status' => $item[10]);
        return $data;
    }
    
    public static function updateOne($uid, $id, $furnace, $savedb = true)
    {
        $key = 'a:u:furnace:' . $uid . ':' . $id;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = array(
            $furnace['id'], $furnace['furnace_id'], $furnace['x'], $furnace['z'], $furnace['m'], $furnace['cid'],
            $furnace['start_time'], $furnace['need_time'], $furnace['cur_probability'], $furnace['num'], $furnace['status']
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
                        'furnace_id' => $furnace['furnace_id'],
                        'x' => $furnace['x'],
                        'z' => $furnace['z'],
                    	'm' => $furnace['m'],
                        'cid' => $furnace['cid'],
                        'start_time' => $furnace['start_time'],
                        'need_time' => $furnace['need_time'],
                        'cur_probability' => $furnace['cur_probability'],
                        'num' => $furnace['num'],
                        'status' => $furnace['status']
                    );
                    
                    $dalFurnace = Hapyfish2_Alchemy_Dal_Furnace::getDefaultInstance();
                    $dalFurnace->update($uid, $id, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_Furnace::updateOne]' . $e->getMessage(), 'db.err');
                }
            }
        } else {
            $ok = $cache->update($key, $data);
        }
        
        return $ok;
    }
    
    public static function getNewFurnaceId($uid)
    {
        try {
            $dalUserSequence = Hapyfish2_Alchemy_Dal_UserSequence::getDefaultInstance();
            return $dalUserSequence->get($uid, 'h', 1);
        } catch (Exception $e) {
        	info_log('[Hapyfish2_Alchemy_HFC_Furnace::getNewFurnaceId]' . $e->getMessage(), 'db.err');
        }
        return 0;
    }
    
    /**
     * 用户当前合成术信息
     * @param int $uid
     */
    public static function getCurMixs($uid)
    {
    	$curMixs = array();
    	$furnace = self::getOnRoom($uid);
    	if (empty($furnace)) {
    		return $curMixs;
    	}
    	
    	$furnacesList = $furnace['furnaces'];    	
    	$nowTime = time();
    	foreach ( $furnacesList as $v ) {
    		if ( $v['cid'] > 0 && $v['status'] == 1 ) {
    			$remainTime = ($v['start_time'] + $v['need_time']) - $nowTime;
    			$remainTime = $remainTime > 0 ? $remainTime : 0;
    			$fid = $v['id'].'41';
    			$curMixs[] = array('furnaceId' => $fid,
    							   'cid' => $v['cid'],
    							   'remainingTime' => $remainTime,
    							   'needTime' => (int)$v['need_time'],
    							   'curProbability' => $v['cur_probability'],
    							   'num' => $v['num']);
    		}
    	}
    	return $curMixs;
    }
    
}