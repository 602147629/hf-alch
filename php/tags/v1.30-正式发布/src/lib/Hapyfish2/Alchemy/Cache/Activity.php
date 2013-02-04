<?php

class Hapyfish2_Alchemy_Cache_Activity
{
	/**
	 * 
	 * 
	 * @param int $uid
	 */
	public static function getBasicMC()
	{
		$key = 'mc_0';
		return Hapyfish2_Cache_Factory::getBasicMC($key);
	}
    public static function get($uid)
    {
        $key = 'a:u:activity:' . $uid;        
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        $date = date('Ymd');
        if ($data === false) {
            try {
                $dal = Hapyfish2_Alchemy_Dal_Activity::getDefaultInstance();
                $data = $dal->get($uid);
                if (!empty($data)) {
                	$cache->set($key,$data);
                } else {
                	$data['uid'] = $uid;
                	$data['activity'] = 0;
                	$data['step'] = '[]';
                	$data['update_time'] = $date;
                	self::update($uid,$data);
                }
            } catch (Exception $e) {
            	info_log('[Hapyfish2_Alchemy_Cache_Activity::get]' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        if($data['update_time'] != $date || !$data['update_time']){
        	$data['update_time'] = $date;
        	$data['uid'] = $uid;
        	$data['activity'] = 0;
        	$data['step'] = '[]';
        	self::update($uid,$data);
        }
        return $data;
    }
    
    public static function update($uid, $data)
    {
    	$key = 'a:u:activity:' . $uid; 
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$dal = Hapyfish2_Alchemy_Dal_Activity::getDefaultInstance();
    	$cache->set($key, $data);
    	 try {
    		$dal->update($uid,$data);
    	 } catch (Exception $e) {
            	info_log('[Hapyfish2_Alchemy_dal_Activity::update]' . $e->getMessage(), 'db.err');
         }
    }
    
    
    public static function getList()
    {
    	$key = 'a:u:activity:';
    	$cache = self::getBasicMC();
    	$list = $cache->get($key);
    	if($list === false){
    		try{
    			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
    			$list = $dal->getdaliyactivity();
    			$cache->set($key, $list);
    		} catch (Exception $e) {
            	info_log('[Hapyfish2_Alchemy_Dal_EventGift::getdaliyactivity]' . $e->getMessage(), 'db.err');
         	}
    	}
    	return $list;
    }
    
    public static function getInfo($id)
    {
    	$list = self::getList();
    	if(isset($list[$id])){
    		return $list[$id];
    	}
    	return null;
    }
    
    public static function getActivityAward()
    {
    	$key = 'a:u:activity:award:';
    	$cache = self::getBasicMC();
    	$list = $cache->get($key);
    	if($list === false){
    		try{
    			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
    			$list = $dal->getactivityAward();
    			$cache->set($key, $list);
    		} catch (Exception $e) {
            	info_log('[Hapyfish2_Alchemy_Dal_EventGift::getdaliyactivity]' . $e->getMessage(), 'db.err');
         	}
    	}
    	return $list;
    }
    
	public static function getAwardInfo($id)
    {
    	$list = self::getActivityAward();
    	if(isset($list[$id])){
    		return $list[$id];
    	}
    	return null;
    }
}