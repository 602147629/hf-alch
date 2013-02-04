<?php

class Hapyfish2_Alchemy_Cache_Helltower
{
	public static function getUserWaterStatus($uid)
	{
		$key = 'a:u:waterstatus:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			$dal = Hapyfish2_Alchemy_Dal_Helltower::getDefaultInstance();
			$data = $dal->get($uid, 1);
			if($data){
				$cache->set($key, $data);
			}else{
				return array(
					'uid'=>$uid,
					'type'=>1,
					'refreshTime'=>0,
					'current'=>1,
					'max'=>0,
					'totalexp'=>0,
					'totalcoin'=>0,
					'open' => 0
				);
			}
		}
		return $data;
	}
	
	public static function getUserAbyssStatus($uid)
	{
		$key = 'a:u:abyssstatus:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			$dal = Hapyfish2_Alchemy_Dal_Helltower::getDefaultInstance();
			$data = $dal->get($uid, 2);
			if($data){
				$cache->set($key, $data);
			}else{
				return array(
					'uid'=>$uid,
					'type'=>2,
					'refreshTime'=>0,
					'current'=>1,
					'max'=>0,
					'totalexp'=>0,
					'totalcoin'=>0,
					'open'=>0
				);
			}
		}
		return $data;
	}
	
	
	public static function getBasicMC()
	{
		$key = 'mc_0';
		return Hapyfish2_Cache_Factory::getBasicMC($key);
	}
	
	public static function getUserWaterRank($uid)
	{
		$rank = -2;
		$userwater = self::getUserWaterStatus($uid);
		if($userwater['max'] > 0){
			$rank = -1;
		}
		$list = self::getWaterRank();
		if($list){
			foreach($list as $k=>$v){
				if($uid == $v[0]){
					return ($k+1);
				}
			}
		}
		return $rank;
	}
	
	public static function getUserAbyssRank($uid)
	{
		$rank = -2;
		$userAbyss = self::getUserAbyssStatus($uid);
		if($userAbyss['max'] > 0){
			$rank = -1;
		}
		$list = self::getAbyssRank();
		if($list){
			foreach($list as $k=>$v){
				if($uid == $v[0]){
					return ($k+1);
				}
			}
		}
		return $rank;
	}
	
	public static function updateUserWaterStatus($uid, $data)
	{
		$key = 'a:u:waterstatus:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, $data);
        try {
        	$dal = Hapyfish2_Alchemy_Dal_Helltower::getDefaultInstance();
        	$dal->insert($data);
        	
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_Cache_Helltower::updateUserWaterStatus]' . $e->getMessage(), 'db.err');
            return null;
        }
        return true;
	}
	
	public static function updateUserAbyssStatus($uid, $data)
	{
		$key = 'a:u:abyssstatus:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, $data);
        try {
        	$dal = Hapyfish2_Alchemy_Dal_Helltower::getDefaultInstance();
        	$dal->insert($data);
        	
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_Cache_Helltower::updateUserWaterStatus]' . $e->getMessage(), 'db.err');
            return null;
        }
        return true;
	}
	
	public static function getAbyssRank()
	{
		$mkey = 'a:b:abyss:rank';
		$eventRank = Hapyfish2_Cache_Factory::getEventRank();
		$list = $eventRank->get($mkey);
		return $list;
	}
	
	public static function getWaterRank()
	{
		$mkey = 'a:b:water:rank';
		$eventRank = Hapyfish2_Cache_Factory::getEventRank();
		$list = $eventRank->get($mkey);
		return $list;
	}
	
	public static function getWaterSweepStart($uid)
	{
		$key = 'a:u:watersweep:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        return $data;
	}
	
	public static function resetWaterSweepStart($uid,$data)
	{
		$key = 'a:u:watersweep:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key,$data);
	}
	
	public static function getAbyssSweepStart($uid)
	{
		$key = 'a:u:abysssweep:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        return $data;
	}
	
	public static function resetAbyssSweepStart($uid,$data)
	{
		$key = 'a:u:abysssweep:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key,$data);
	}
	
	public static function getUserWaterRefiesh($uid)
	{
		$date = date('Ymd');
		$key = 'a:u:waterrefiesh:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if(!$data || $data['date'] != $date){
        	$data['refresh'] = 0;
        	$data['date'] = $date;
        	$data['data'] = self::getUserWaterStatus($uid);
        	$data['status'] = 1;
        }
        return $data;
	}
	
	public static function getUserAbyssRefiesh($uid)
	{
		$date = date('Ymd');
		$key = 'a:u:abyssrefiesh:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if(!$data || $data['date'] != $date){
        	$data['refresh'] = 0;
        	$data['date'] = $date;
        	$data['data'] = self::getUserAbyssStatus($uid);
        	$data['status'] = 1;
        }
        return $data;
	}
	
	public static function updateUserWaterRefiesh($uid,$data)
	{
		$key = 'a:u:waterrefiesh:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, $data);
	}
	
	public static function updateUserAbyssRefiesh($uid,$data)
	{
		$key = 'a:u:abyssrefiesh:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, $data);
	}
}