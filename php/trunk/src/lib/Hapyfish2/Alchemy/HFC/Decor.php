<?php

class Hapyfish2_Alchemy_HFC_Decor
{
	//getUserDecorInBag
	public static function getBag($uid)
    {
        $key = 'a:u:decor:bag:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
		
        if ($data === false) {
			try {
	            $dalDecor = Hapyfish2_Alchemy_Dal_Decor::getDefaultInstance();
	            $result = $dalDecor->getInBag($uid);
	            $data = array();
	            if ($result) {
                    foreach ($result as $cid => $count) {
                        $data[(int)$cid] = array((int)$count, 0);
                    }
                }
                $cache->add($key, $data);
            } catch (Exception $e) {
            	info_log('[Hapyfish2_Alchemy_HFC_Decor::getBag:'. $uid. ']' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        
        $decor = array();
        if ( is_array($data) ) {
            foreach ($data as $cid => $item) {
                $decor[$cid] = array('count' => $item[0], 'update' => $item[1]);
            }
        }
        
        return $decor;
    }

    //updateUserDecorInBag
    public static function updateBag($uid, $decor, $savedb = false)
    {
        $key = 'a:u:decor:bag:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        
        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }

        if ($savedb) {
            $data = array();
            foreach ($decor as $cid => $item) {
                $data[(int)$cid] = array((int)$item['count'], 0);
            }
            $ok = $cache->save($key, $data);
            if ($ok) {
                try {
                    $dalDecor = Hapyfish2_Alchemy_Dal_Decor::getDefaultInstance();
                    foreach ($decor as $cid => $item) {
                        if ($item['update']) {
                            $dalDecor->updateInBag($uid, $cid, $item['count']);
                        }
                    }
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_Decor::updateBag:'. $uid. ']' . $e->getMessage(), 'db.err');
                }
            }
            
            return $ok;
        } else {
            $data = array();
            foreach ($decor as $cid => $item) {
                $data[$cid] = array($item['count'], $item['update']);
            }
            return $cache->update($key, $data);
        }
    }
    
    //addUserDecorInBag
    public static function addBag($uid, $cid, $count = 1, $decor = null)
    {
        if (!$decor) {
            $decor = self::getBag($uid);
            if ($decor === null) {
                return false;
            }
        }
        
        $cid = (int)$cid;
        $count = (int)$count;
        
        if (isset($decor[$cid])) {
            $decor[$cid]['count'] += $count;
            $decor[$cid]['update'] = 1;
        } else {
            $decor[$cid] = array('count' => $count, 'update' => 1);
        }

        $ok = self::updateBag($uid, $decor);
        if ($ok) {
    		$addItem = array($cid, $count);
    		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'addItems', array($addItem));
    		
			//添加图鉴
			Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $cid);
        }
        return $ok;
    }
    
    //useUserDecorInBag
    public static function useBag($uid, $cid, $count = 1, $decor = null)
    {
        if (!$decor) {
            $decor = self::getBag($uid);
            if (!$decor) {
                return false;
            }
        }
        
        $cid = (int)$cid;
        $count = (int)$count;

        if (!isset($decor[$cid]) || $decor[$cid]['count'] < $count) {
            return false;
        } else {
            $decor[$cid]['count'] -= $count;
            $decor[$cid]['update'] = 1;
            
            $ok = self::updateBag($uid, $decor, true);
    		if ($ok) {
    			$removeItems = array($cid, $count);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeItems', array($removeItems));
    		}
    		return $ok;
        }
    }
    
	public static function getScene($uid)
    {
    	$key = 'a:u:decor:scene:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
		
        if ($data === false) {
			try {
	            $dalDecor = Hapyfish2_Alchemy_Dal_Decor::getDefaultInstance();
	            $result = $dalDecor->getInScene($uid);
	            $data = array();
	            if ($result) {
                    $data = array();
                    foreach ($result as $row) {
                    	$id = (int)$row['id'];
                        $data[$id] = array(
                        	0,//修改标记位
                        	$id, 
                        	(int)$row['cid'],
                        	(int)$row['x'],
                        	(int)$row['z'],
                        	(int)$row['m']
                        );
                    }
                }
                $cache->add($key, $data);
            } catch (Exception $e) {
            	info_log('[Hapyfish2_Alchemy_HFC_Decor::getScene:'. $uid. ']' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        
        $decor = array();
        if ( is_array($data) ) {
            foreach ($data as $id => $item) {
                $decor[$id] = array(
                	'update' => $item[0],
                	'id' => $item[1],
                	'cid' => $item[2],
                	'x' => $item[3],
                	'z' => $item[4],
                	'm' => $item[5]
                );
            }
        }
        
        return $decor;
    }
    
    public static function updateScene($uid, $decor, $savedb = true)
    {
        $key = 'a:u:decor:scene:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        
        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }

        if ($savedb) {
            $data = array();
            foreach ($decor as $id => $item) {
                $data[$id] = array(
                	0,
                	$item['id'],
                	$item['cid'],
                	$item['x'],
                	$item['z'],
                	$item['m']
                );
            }
            $ok = $cache->save($key, $data);
            if ($ok) {
                try {
                    $dalDecor = Hapyfish2_Alchemy_Dal_Decor::getDefaultInstance();
                    foreach ($decor as $id => $item) {
                        if ($item['update']) {
                        	$itemData = array(
                        		'x' => $item['x'],
                        		'z' => $item['z'],
                        		'm' => $item['m']
                        	);
                            $dalDecor->updateInScene($uid, $id, $itemData);
                        }
                    }
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_Decor::updateScene:'. $uid. ']' . $e->getMessage(), 'db.err');
                }
            }
            
            return $ok;
        } else {
            $data = array();
            foreach ($decor as $id => $item) {
                $data[$id] = array(
                	$item['update'],
                	$item['id'],
                	$item['cid'],
                	$item['x'],
                	$item['z'],
                	$item['m']
                );
            }
            return $cache->update($key, $data);
        }
    }
    
    public static function getEmptyDecorId($uid)
    {
        try {
        	$dalDecor = Hapyfish2_Alchemy_Dal_Decor::getDefaultInstance();
        	$id = $dalDecor->getSceneEmptyId($uid);
        	if (!$id) {
				$id = 0;
        	}
        	return $id;
        } catch (Exception $e) {
        	info_log('[Hapyfish2_Alchemy_HFC_Decor::getEmptyDecorId:'. $uid. ']' . $e->getMessage(), 'db.err');
        }
        
        return 0;
    }
    
    public static function getNewDecorId($uid)
    {
        try {
            $dalUserSequence = Hapyfish2_Alchemy_Dal_UserSequence::getDefaultInstance();
            return $dalUserSequence->get($uid, 'd', 1);
        } catch (Exception $e) {
        	info_log('[Hapyfish2_Alchemy_HFC_Decor::getNewDecorId:'. $uid. ']' . $e->getMessage(), 'db.err');
        }
        return 0;
    }
    
    public static function addScene($uid, &$data)
    {
    	$result = false;
    	try {
    		$id = self::getEmptyDecorId($uid);
    		if ($id > 0) {
    			$decorData = array(
    				'uid' => $uid,
    				'cid' => $data['cid'],
    				'x' => $data['x'],
    				'z' => $data['z'],
    				'm' => $data['m'],
    				's' => 1
    			);
    			$dalDecor = Hapyfish2_Alchemy_Dal_Decor::getDefaultInstance();
    			$dalDecor->updateInScene($uid, $id, $decorData);
    			$data['id'] = $id;
    			$result = true;
    		} else {
    			$id = self::getNewDecorId($uid);
    			if ($id > 0) {
	    			$decorData = array(
	    				'uid' => $uid,
	    				'id' => $id,
	    				'cid' => $data['cid'],
	    				'x' => $data['x'],
	    				'z' => $data['z'],
	    				'm' => $data['m'],
	    				's' => 1
	    			);
    				$dalDecor = Hapyfish2_Alchemy_Dal_Decor::getDefaultInstance();
    				$dalDecor->insertInScene($uid, $decorData);
    				$data['id'] = $id;
    				$result = true;
    			}
    		}
    	} catch (Exception $e) {
    		info_log('[Hapyfish2_Alchemy_HFC_Decor::addScene:'. $uid. ']' . $e->getMessage(), 'db');
    	}
    	
    	if ($result) {
    		//更新缓存
			$key = 'a:u:decor:scene:' . $uid;
	        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
			$sceneData = $cache->get($key);
			
	        if ($sceneData !== false && is_array($sceneData)) {
				$sceneData[$id] = array(
					0,//修改标记位
					(int)$id, 
					(int)$decorData['cid'],
					(int)$decorData['x'],
					(int)$decorData['z'],
					(int)$decorData['m']
				);
				$ok = $cache->update($key, $sceneData);
				if (!$ok) {
					info_log('[Hapyfish2_Alchemy_HFC_Decor::addScene-updateCache:'. $uid. ']:' . $id .  ',' . json_encode($decorData), 'cache.err');
				}
	    	}
    	}
    	
    	return $result;
    }
    
    public static function removeScene($uid, $id)
    {
    	$result = false;
    	try {
   			$decorData = array('s' => 0);
   			$dalDecor = Hapyfish2_Alchemy_Dal_Decor::getDefaultInstance();
   			$dalDecor->updateInScene($uid, $id, $decorData);
   			$result = true;
    	} catch (Exception $e) {
    		info_log('[Hapyfish2_Alchemy_HFC_Decor::addScene:'. $uid. ']' . $e->getMessage(), 'db');
    	}
    	
    	if ($result) {
    		//更新缓存
			$key = 'a:u:decor:scene:' . $uid;
	        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
			$data = $cache->get($key);
			
	        if ($data !== false && is_array($data)) {
	        	unset($data[$id]);
				$ok = $cache->update($key, $data);
				if (!$ok) {
					info_log('[Hapyfish2_Alchemy_HFC_Decor::removeScene-updateCache:'. $uid. ']:' . $id, 'cache.err');
				}
	    	}
    	}
    	
    	return $result;
    }
}