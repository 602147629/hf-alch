<?php

class Hapyfish2_Alchemy_Cache_BasicExt
{

    public static $cacheKeyPrex = 'alchemy:bas:';

	public static function getKey($word)
	{
		return self::$cacheKeyPrex.$word;
	}

	public static function getBasicMC()
	{
		$key = 'mc_0';
		return Hapyfish2_Cache_Factory::getBasicMC($key);
	}

	public static function getBasicDB()
	{
		return Hapyfish2_Alchemy_Dal_Basic::getDefaultInstance();
	}

    public static function getBasicVersion()
	{
        $key = 'alchemy:basicstaticver';
		$cache = self::getBasicMC();
		$ver = $cache->get($key);
		if (!$ver) {
		    $ver = date('Ymd').'1';
		}
		return $ver;
	}

	/**
	 * 竞技场战斗奖励表
	 */
	public static function getArenaAwardList()
	{
		$key = self::getKey('arenaaward');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadArenaAwardList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}
	
	public static function getArenaAwardListByType($type)
	{
		$list = self::getArenaAwardList();
		$info = array();
		foreach ( $list as $v ) {
			if ( $v['type'] == $type ) {
				$info[] = $v;
			}
		}
		return $info;
	}

	public static function loadArenaAwardList()
	{
		$db = self::getBasicDB();
		$list = $db->getArenaAward();
		if ($list) {
			$key = self::getKey('arenaaward');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}
		return $list;
	}

	/**
	 * 训练营升级列表
	 */
	public static function getTrainingList()
	{
		$key = self::getKey('traininglist');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
	
		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadTrainingList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}
	
		return $list;
	}
	
	public static function getTrainingInfo($id)
	{
		$list = self::getTrainingList();
		$info = array();
		foreach ( $list as $v ) {
			if ( $v['id'] == $id ) {
				$info = $v;
			}
		}
		return $info;
	}
	
	public static function loadTrainingList()
	{
		$db = self::getBasicDB();
		$list = $db->getTrainingList();
		if ($list) {
			$key = self::getKey('traininglist');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}
		return $list;
	}
	
	
    /**
     * 统计-用户操作记录
     */
    public static function getStatUserAction()
    {
        $key = self::getKey('statuseraction');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadStatUserAction();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }
    
    public static function loadStatUserAction()
    {
        $db = self::getBasicDB();
        $list = $db->getStatUserAction();
        if ($list) {
            $key = self::getKey('statuseraction');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }
	
}