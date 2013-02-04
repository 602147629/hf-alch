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
	 * 竞技场排名奖励表
	 */
	public static function getArenaPrizeList()
	{
		$key = self::getKey('arenaprize');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
	
		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadArenaPrizeList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}
	
		return $list;
	}
	
	public static function getArenaPrizeByRank($rank)
	{
		$list = self::getArenaPrizeList();
		$info = array();
		foreach ( $list as $v ) {
			if ( $v['rank'] > $rank ) {
				break;
			}
			$info = $v;
		}
		return $info;
	}
	
	public static function loadArenaPrizeList()
	{
		$db = self::getBasicDB();
		$list = $db->getArenaPrize();
		if ($list) {
			$key = self::getKey('arenaprize');
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
	 * 酒馆积分 对应的 佣兵星级 信息
	 */
	public static function getHireScoreRpList()
	{
		$key = self::getKey('hirescorerplist');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
	
		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadHireScoreRpList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}
	
		return $list;
	}
	
	public static function getHireScoreRpByScore($score)
	{
		$list = self::getHireScoreRpList();
		$rp = 1;
		foreach ( $list as $v ) {
			if ( $score >= $v['max_score'] ) {
				$rp = $v['rp'];
			}
		}
		return $rp;
	}

	public static function getHireScoreRp($rp)
	{
		$list = self::getHireScoreRpList();
		$info = array();
		foreach ( $list as $v ) {
			if ( $rp >= $v['rp'] ) {
				$info = $v;
			}
		}
		return $info;
	}
	
	public static function loadHireScoreRpList()
	{
		$db = self::getBasicDB();
		$list = $db->getHireScoreRpList();
		if ($list) {
			$key = self::getKey('hirescorerplist');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}
		return $list;
	}

	/**
	 * 佣兵星级 对应的酒馆刷新信息
	 */
	public static function getHireRpList()
	{
		$key = self::getKey('hirerplist');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
	
		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadHireRpList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}
	
		return $list;
	}
	
	public static function getHireRp($rp, $type)
	{
		$list = self::getHireRpList();
		$info = array();

		foreach ( $list as $v ) {
			if ( $rp == $v['rp'] && $type == $v['type'] ) {
				$info = $v;
			}
		}
		return $info;
	}
	
	public static function loadHireRpList()
	{
		$db = self::getBasicDB();
		$list = $db->getHireRpList();
		if ($list) {
			$key = self::getKey('hirerplist');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}
		return $list;
	}
	
	/**
	 * 酒馆积分 对应 佣兵星级的 积分公式
	 */
	public static function getHireRpLevel()
	{
		$list = self::getHireScoreRpList();
		$info = array();
		$info[] = "0";
		foreach ( $list as $v ) {
			$info[] = $v['max_score'];
		}
		return $info;
	}

	/**
	 * 建筑升级tips
	 */
	public static function getBuildUpgradeList()
	{
		$key = self::getKey('buildupgradetips');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
	
		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadBuildUpgradeList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}
	
		return $list;
	}
	
	public static function loadBuildUpgradeList()
	{
		$db = self::getBasicDB();
		$list = $db->getBuildUpgradeList();
		if ($list) {
			$key = self::getKey('buildupgradetips');
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
	
    
/************************************* QQ黄钻部分 *******************************************************/
    /**
     * QQ黄钻等级 每日礼包
     */
    public static function getQQYellowVipGiftList()
    {
    	$key = self::getKey('qqyellowvipgift');
    	$localcache = Hapyfish2_Cache_LocalCache::getInstance();
    
    	$list = $localcache->get($key);
    	if (!$list) {
    		$cache = self::getBasicMC();
    		$list = $cache->get($key);
    		if (!$list) {
    			$list = self::loadQQYellowVipGiftList();
    		}
    		if ($list) {
    			$localcache->set($key, $list);
    		}
    	}
    
    	return $list;
    }
    
    public static function getQQYellowVipGift($id)
    {
    	$list = self::getQQYellowVipGiftList();
    	$info = array();
    	foreach ( $list as $v ) {
    		if ( $v['level'] == $id ) {
    			$info = $v;
    		}
    	}
    	return $info;
    }
    
    public static function loadQQYellowVipGiftList()
    {
    	$db = self::getBasicDB();
    	$list = $db->getQQYellowVipGiftList();
    	if ($list) {
    		$key = self::getKey('qqyellowvipgift');
    		$cache = self::getBasicMC();
    		$cache->set($key, $list);
    	}
    	return $list;
    }
    
    
    
    
}