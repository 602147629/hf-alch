<?php
class Hapyfish2_Alchemy_Cache_Vip
{	
	public static function getBasicMC()
	{
		$key = 'mc_0';
		return Hapyfish2_Cache_Factory::getBasicMC($key);
	}
	
	public static function updateVip($data)
	{
		$key = 'a:u:vip:'.$data['uid'];
		$cache = Hapyfish2_Cache_Factory::getMC($data['uid']);
		$cache->set($key, $data);
		try{
			$dal = Hapyfish2_Alchemy_Dal_Vip::getDefaultInstance();
			$dal->insertVip($data);
		} catch (Exception $e) {
            return "fatal error:".$e->getMessage();
	    }
	}
	
	public static function getUserInfo($uid)
	{
		$key = 'a:u:vip:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			$dal = Hapyfish2_Alchemy_Dal_Vip::getDefaultInstance();
			$data = $dal->getUserInfo($uid);
			if($data){
				$cache->set($key, $data);
			}else{
				return null;
			}
		}
		return $data;
	}
	
	public static function getVipAddition()
	{
		$key = 'alchemy:bas:vip';
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		if($data === false){
			$dal = Hapyfish2_Alchemy_Dal_Vip::getDefaultInstance();
			$data = $dal->getVipAddition();
			if($data){
				foreach($data as $k => &$v){
					$v['addition'] = json_decode($v['addition'], true);
					$v['daliy'] = json_decode($v['daliy'], true);
				}
				unset($v);
				$cache->set($key, $data);
			}else{
				return null;
			}
		}
		return $data;
	}
	
	/******************************************************/
	

    public static function getVipDailyAward($uid)
    {
        $key = 'a:u:vipdailyaward:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        $date = date('Ymd');
        if ($data === false || $data['date'] != $date) {
            $data['date'] = $date;
            $data['get'] = 0;
            $cache->set($key, $data);
        }
        return $data;
    }

    public static function setVipDailyAward($uid, $data)
    {
        $key = 'a:u:vipdailyaward:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, $data);
        return true;
    }
	
	public static function getVipLevelAward($uid)
	{
		$key = 'a:u:vip:level:award:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			$dal = Hapyfish2_Alchemy_Dal_Vip::getDefaultInstance();
			$data = $dal->getLevelAward($uid);
			if($data){
				$cache->set($key, $data);
			}else{
				return null;
			}
		}
		return $data;
	}
	
	public static function getUserSkip($uid)
	{
		$key = 'a:u:vip:skip:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		$date = date('Ymd');
		if($data === false){
			$data['uid'] = $uid;
			$data['date'] = $date;
			$data['num'] = 0;
			$data['max'] = 10;
		}
		if($data['date'] != $date){
			$data['date'] = $date;
			$data['num'] = 0;
		}
		return $data;
	}
	
	public static function updateUserSkip($uid, $data)
	{
		$key = 'a:u:vip:skip:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $data);
	}
	
	public static function getUserAddSp($uid)
	{
		$key = 'a:u:vip:add:sp:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		$date = date('Ymd');
		if($data === false){
			$data['uid'] = $uid;
			$data['date'] = $date;
			$data['num'] = 0;
		}
		if($data['date'] != $date){
			$data['date'] = $date;
			$data['num'] = 0;
		}
		return $data;
	}
	
	public static function updateUserAddSp($uid, $data)
	{
		$key = 'a:u:vip:add:sp:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $data);
	}
}