<?php

class Hapyfish2_Alchemy_Cache_Furnace
{
	/**
	 * 查询在房间内的工作台id列表
	 * 
	 * @param int $uid
	 */
    public static function getAllIds($uid)
    {
        $key = 'a:u:fun:ids:' . $uid;        
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $ids = $cache->get($key);

        if ($ids === false) {
            try {
                $dalFurnace = Hapyfish2_Alchemy_Dal_Furnace::getDefaultInstance();
                $ids = $dalFurnace->getAllIds($uid);
                if (!empty($ids)) {
                    $cache->add($key, $ids);
                } else {
                    $cache->add($key, array());
                    return array();
                }
            } catch (Exception $e) {
            	info_log('[Hapyfish2_Alchemy_Cache_Furnace::getAllIds]' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        
        return $ids;
    }
    
    public static function reloadAllIds($uid)
    {
        try {
            $dalFurnace = Hapyfish2_Alchemy_Dal_Furnace::getDefaultInstance();
            $ids = $dalFurnace->getAllIds($uid);

            $key = 'a:u:fun:ids:' . $uid;
            $cache = Hapyfish2_Cache_Factory::getMC($uid);
            if (!empty($ids)) {
                $cache->set($key, $ids);
                return $ids;
            } else {
                $cache->set($key, array());
                return array();
            }
        } catch (Exception $e) {
        	info_log('[Hapyfish2_Alchemy_Cache_Furnace::reloadAllIds]' . $e->getMessage(), 'db.err');
        }
        
        return null;
    }
    
}