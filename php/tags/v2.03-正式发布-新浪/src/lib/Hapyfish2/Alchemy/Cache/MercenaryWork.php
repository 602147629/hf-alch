<?php

class Hapyfish2_Alchemy_Cache_MercenaryWork
{
	/**
	 * 查询所有打工点id列表
	 * 
	 * @param int $uid
	 */
    public static function getAllIds($uid)
    {
        $key = 'a:u:merwork:ids:' . $uid;        
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $ids = $cache->get($key);

        if ($ids === false) {
            try {
                $dalFurnace = Hapyfish2_Alchemy_Dal_MercenaryWork::getDefaultInstance();
                $ids = $dalFurnace->getAllIds($uid);
                if (!empty($ids)) {
                    $cache->add($key, $ids);
                } else {
                    $cache->add($key, array());
                    return array();
                }
            } catch (Exception $e) {
            	info_log('[Hapyfish2_Alchemy_Cache_MercenaryWork::getAllIds]' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        
        return $ids;
    }
    
    public static function reloadAllIds($uid)
    {
        try {
            $dalFurnace = Hapyfish2_Alchemy_Dal_MercenaryWork::getDefaultInstance();
            $ids = $dalFurnace->getAllIds($uid);

            $key = 'a:u:merwork:ids:' . $uid;
            $cache = Hapyfish2_Cache_Factory::getMC($uid);
            if (!empty($ids)) {
                $cache->set($key, $ids);
                return $ids;
            } else {
                $cache->set($key, array());
                return array();
            }
        } catch (Exception $e) {
        	info_log('[Hapyfish2_Alchemy_Cache_MercenaryWork::reloadAllIds]' . $e->getMessage(), 'db.err');
        }
        
        return null;
    }
    
    public static function getRolesCdList($uid)
    {
    	$key = 'a:u:rolesworkcd:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	return array();
        }
        
        return $data;
    }
    
    public static function updateRolesCdList($uid, $list)
    {
        $key = 'a:u:rolesworkcd:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $result = $cache->update($key, $list);
        
    	return $result;
    }
        
}