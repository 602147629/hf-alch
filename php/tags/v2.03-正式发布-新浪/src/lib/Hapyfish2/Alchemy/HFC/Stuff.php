<?php

class Hapyfish2_Alchemy_HFC_Stuff
{
    //材料,type:3
	public static function getUserStuff($uid)
    {
        $key = 'a:u:stuff:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dalStuff = Hapyfish2_Alchemy_Dal_Stuff::getDefaultInstance();
	            $result = $dalStuff->get($uid);
	            if ($result) {
                    $data = array();
                    foreach ($result as $cid => $count) {
                        $data[$cid] = array($count, 0);
                    }
                    $cache->add($key, $data);
                } else {
                    return null;
                }
            } catch (Exception $e) {
                info_log('[Hapyfish2_Alchemy_HFC_Stuff::getUserStuff:'. $uid. ']' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        
        $stuff = array();
        if ( is_array($data) ) {
            foreach ($data as $cid => $item) {
                $stuff[$cid] = array('count' => $item[0], 'update' => $item[1]);
            }
        }
        
        return $stuff;
    }

    
    public static function updateUserStuff($uid, $stuff, $savedb = true)
    {
        $key = 'a:u:stuff:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $data = array();
            foreach ($stuff as $cid => $item) {
                $data[$cid] = array($item['count'], 0);
            }
            $ok = $cache->save($key, $data);
            if ($ok) {
                try {
                    $dalStuff = Hapyfish2_Alchemy_Dal_Stuff::getDefaultInstance();
                    foreach ($stuff as $cid => $item) {
                        if ($item['update']) {
                            $dalStuff->update($uid, $cid, $item['count']);
                        }
                    }
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_Stuff::updateUserStuff:'. $uid. ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            $data = array();
            foreach ($stuff as $cid => $item) {
                $data[$cid] = array($item['count'], $item['update']);
            }
            return $cache->update($key, $data);
        }
    }
    
    public static function addUserStuff($uid, $cid, $count = 1, $stuff = null)
    {
        if (!$stuff) {
            $stuff = self::getUserStuff($uid);
            /*if ($stuff === null) {
                return false;
            }*/
        }
        
        $cid = (int)$cid;
        $count = (int)$count;
        
        if (isset($stuff[$cid])) {
            $stuff[$cid]['count'] += $count;
            $stuff[$cid]['update'] = 1;
        } else {
            $stuff[$cid] = array('count' => $count, 'update' => 1);
        }

        $ok = self::updateUserStuff($uid, $stuff, true);
        if ($ok) {
    		$addItem = array($cid, $count);
    		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'addItems', array($addItem));
			//添加图鉴
			Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $cid);
        }
        return $ok;
    }
    
    public static function useUserStuff($uid, $cid, $count = 1, $stuff = null)
    {
        if (!$stuff) {
            $stuff = self::getUserStuff($uid);
            if (!$stuff) {
                return false;
            }
        }
        
        $cid = (int)$cid;
        $count = (int)$count;

        if (!isset($stuff[$cid]) || $stuff[$cid]['count'] < $count) {
            return false;
        } else {
            $stuff[$cid]['count'] -= $count;
            $stuff[$cid]['update'] = 1;

            $ok = self::updateUserStuff($uid, $stuff, true);
    		if ($ok) {
    			$removeItems = array($cid, $count);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeItems', array($removeItems));
    		}
    		return $ok;
        }
    }
    
}