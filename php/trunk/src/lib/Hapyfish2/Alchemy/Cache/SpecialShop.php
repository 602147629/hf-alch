<?php

class Hapyfish2_Alchemy_Cache_SpecialShop
{
	public static function getBasicMC()
	{
		$key = 'mc_0';
		return Hapyfish2_Cache_Factory::getBasicMC($key);
	}
	
    public static $cacheKeyPrex = 'alchemy:bas:';

	public static function getKey($word)
	{
		return self::$cacheKeyPrex.$word;
	}
	
	public static function getSpecialShop()
	{
		$key = self::getKey('SpecialShop');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if(!$list){
				$list = self::loadSpecialShop();
			}
			if($list){
				$localcache->set($key, $list);
			}
		}
		return $list;
	}
	
	public static function loadSpecialShop()
	{
		$db = Hapyfish2_Alchemy_Dal_SpecialShop::getDefaultInstance();
		$list = $db->getSpecialShop();
		if ($list) {
			$key = self::getKey('SpecialShop');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}
		return $list;
	}
	
	public static function getSpecialShopItem()
	{
		$key = self::getKey('SpecialShopItem');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if(!$list){
				$list = self::loadSpecialShopItem();
			}
			if($list){
				$localcache->set($key, $list);
			}
		}
		return $list;
	}
	
	public static function loadSpecialShopItem()
	{
		$db = Hapyfish2_Alchemy_Dal_SpecialShop::getDefaultInstance();
		$list = $db->getSpecialShopItem();
		if ($list) {
			foreach($list as $k=>&$v){
				$v['awards'] = json_decode($v['awards'], true);
			}
			$key = self::getKey('SpecialShopItem');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}
		return $list;
	}
	
	public static function getSpecialShopDetail($type)
	{
		$list = self::getSpecialShop();
		if(isset($list[$type])){
			return $list[$type];
		}
		return null;
	}
	
	public static function getDetail($id, $level)
	{
		$list = self::getSpecialShopItem();
		foreach($list as $k => $v){
			if($v['id'] == $id && $v['level'] == $level){
				return $v;
			}
		}
		return null;
	}
	
	public static function getUserSpecialShop($uid)
	{
		$key = 'a:u:SpecialShop:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		$date = date('Ymd');
		if($data === false || $data['date'] != $date){
			return array(
						'date'=>$date,
						'list'=>array(0,0,0,0,0)
			);
		}
		return $data;
	}
	
	public static function updateUserSpecialShop($uid,$data)
	{
		$key = 'a:u:SpecialShop:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key,$data);
	}
	
	public static function getTotalSpecialShop()
	{
		$key = 'a:b:Total:SpecialShop';
		$cache = Hapyfish2_Cache_Factory::getMemcached('mc_0');
		$data = $cache->get($key);
		$date = date('Ymd');
		if($data === false || $data['date'] != $date){
			$data =  array(
						'date'=>$date,
						'list'=>array(0,0,0,0,0),
						'updateTime'=>0,
						'auto'=>array(0,0,0,0,0)
			);
		}
		$data = Hapyfish2_Alchemy_Bll_SpecialShop::autoDown($data,$date);
		return $data;
	}
	
	public static function updateTotalSpecialShop($info)
	{
		$try = 3;
    	$null = null;
    	$first = false;
		$key = 'a:b:Total:SpecialShop';
		$cache = Hapyfish2_Cache_Factory::getMemcached('mc_0');
    	while($try > 0) {
    	    $data = $cache->get($key, $null, $token);

    	    if ($data === false) {
    	        if ($cache->getResultCode() == Memcached::RES_NOTFOUND) {
    				$first = true;
    			} else {
    				break;
    			}
    	    }

    		if ($first) {
    			$cache->add($key, $info, 0);
    		} else {
    			$cache->cas($token, $key, $info, 0);
    		}

			if ($cache->getResultCode() == Memcached::RES_SUCCESS) {
				$ok = true;
				break;
			}
			$try--;
    	}
	}
	
	
}