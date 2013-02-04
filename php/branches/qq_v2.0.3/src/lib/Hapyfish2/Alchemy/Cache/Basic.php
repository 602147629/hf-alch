<?php

class Hapyfish2_Alchemy_Cache_Basic
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

	/*
     * 静态文件版本
     */
    public static function setBasicVersion($ver)
	{
        $key = 'alchemy:basicstaticver';
		$cache = self::getBasicMC();
		$ok = $cache->set($key, $ver);
		return $ok;
	}

	/*
	 * 佣兵模型
	 */
	public static function getMercenaryList()
	{
		$key = self::getKey('mercenary');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMercenaryList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getMercenaryInfo($id)
	{
		$list = self::getMercenaryList();
		if (isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}

	public static function getMercenaryInfoByJob($id)
	{
		$list = self::getMercenaryList();
		if (isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}
	
	public static function getMercenaryModelByRp($rp, $job, $count = 0)
	{
		$data = self::getMercenaryList();
		$list = array();
		if ( $count == 0 ) {
			foreach ( $data as $v ) {
				if ( $v['rp'] == $rp ) {
					if ( $v['job'] == $job || $v['job'] == 0 ) {
						$list[] = $v;
					}
				}
			}
		}
		else {
			foreach ( $data as $k => $v ) {
				if ( $v['name_id'] == 0 || $v['rp'] != $rp ) {
					if ( $v['job'] != $job && $v['job'] != 0 ) {
						unset($data[$k]);
					}
				}
			}
			if ( $count < count($data) ) {
				$temp = Hapyfish2_Alchemy_Bll_Mercenary::array_random_assoc($data, $count);
			}
			else {
				$temp = $data;
			}

			foreach ( $temp as $t ) {
				$list[] = $t;
			}
		}
		return $list;
	}

	public static function loadMercenaryList()
	{
		$db = self::getBasicDB();
		$list = $db->getMercenary();
		if ($list) {
			$key = self::getKey('mercenary');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	/**
	 * 佣兵成长曲线-模板比较值
	 */
	public static function getMercenaryGrowClassList()
	{
		$key = self::getKey('mercenarygrowclass');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMercenaryGrowClassList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}
	
	public static function loadMercenaryGrowClassList()
	{
		$db = self::getBasicDB();
		$list = $db->getMercenaryGrowClass();
		if ($list) {
			$key = self::getKey('mercenarygrowclass');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}
		return $list;
	}
	
	public static function getMercenaryGrowClass($id, $job)
	{
		$list = self::getMercenaryGrowClassList();
		foreach ( $list as $v ) {
			if ( $v['rp'] == $id && $v['job'] == $job ) {
				return $v;
			}
		}
		return null;
	}
	
	/**
	 * 佣兵成长曲线
	 */
	public static function getMercenaryGrowList()
	{
		$key = self::getKey('mercenarygrow');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMercenaryGrowList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getMercenaryGrowInfo($id)
	{
		$list = self::getMercenaryGrowList();
		if (isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}

	public static function getMercenaryGrowInfoByJob($id)
	{
		$list = self::getMercenaryGrowList();
		if (isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}
	
	public static function getMercenaryGrowByRp($rp)
	{
		$data = self::getMercenaryGrowList();
		$list = array();
		foreach ( $data as $v ) {
			if ( $v['rp'] == $rp ) {
				$list[] = $v;
			}
		}

		return $list;
	}

	public static function loadMercenaryGrowList()
	{
		$db = self::getBasicDB();
		$list = $db->getMercenaryGrow();
		if ($list) {
			$key = self::getKey('mercenarygrow');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}
		return $list;
	}

	/**
	 * 佣兵星级随机表
	 */
	public static function getMercenaryRpRandList()
	{
		$key = self::getKey('mercenaryrprand');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
	
		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMercenaryRpRandList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}
	
		return $list;
	}
	
	public static function loadMercenaryRpRandList()
	{
		$db = self::getBasicDB();
		$list = $db->getMercenaryRpRand();
		if ($list) {
			$key = self::getKey('mercenaryrprand');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}
		return $list;
	}
	
	/**
	 * 佣兵等级经验
	 * @param $id
	 */
	public static function getMercenaryLevelList()
	{
		$key = self::getKey('mercenarylevel');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMercenaryLevelList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getMercenaryNextLevelByExp($exp)
	{
		$result = array();
		$list = self::getMercenaryLevelList();
		foreach ( $list as $v ) {
			if ( $v['exp'] <= $exp ) {
				$result = $v;
			}
		}
		return $result;
	}
	
	public static function getMercenaryLevelExp($level)
	{
		$list = self::getMercenaryLevelList();
		if (isset($list[$level])) {
			return $list[$level]['exp'];
		}
		return 999999999;
	}

	public static function loadMercenaryLevelList()
	{
		$db = self::getBasicDB();
		$list = $db->getMercenaryLevel();
		if ($list) {
			$key = 'mercenarylevel';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}
		return $list;
	}

	/**
	 * 佣兵名称信息
	 */
	public static function getMercenaryNameList($count = 0, $job = 0, $rp = 0)
	{
		$key = self::getKey('mercenaryname');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMercenaryNameList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		if ( $job > 0 ) {
			foreach ( $list as $k => $v ) {
				if ( $job != $v['job'] && 0 != $v['job'] ) {
					unset($list[$k]);
				}
			}
		}
		//10星级佣兵，特殊类型信息
		if ( $rp >= 10 ) {
			foreach ( $list as $m => $n ) {
				if ( $n['rp'] < 10 ) {
					unset($list[$m]);
				}
			}
		}
		else {
			foreach ( $list as $m => $n ) {
				if ( $n['rp'] == 10 ) {
					unset($list[$m]);
				}
			}
		}

		if ( $count == 0 ) {
			return $list;
		}
		else {
			if ( $count < count($list) ) {
				return Hapyfish2_Alchemy_Bll_Mercenary::array_random_assoc($list, $count);
			}
			else {
				return $list;
			}
		}
	}

	public static function getMercenaryNameInfo($id)
	{
		$list = self::getMercenaryNameList();
		if (isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}

	public static function loadMercenaryNameList()
	{
		$db = self::getBasicDB();
		$list = $db->getMercenaryName();
		if ($list) {
			$key = self::getKey('mercenaryname');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}
		return $list;
	}

	/**
	 * 佣兵强化列表
	 */
	public static function getMercenaryStrengthenList()
	{
		$key = self::getKey('mercenarystrthen');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMercenaryStrengthenList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getMercenaryStrengthen($job, $level, $type)
	{
		$list = self::getMercenaryStrengthenList();
		
		foreach ( $list as $k => $v ) {
			if ( $v['job'] == $job && $v['level'] == $level && $v['type'] == $type ) {
				return $v;
			}
		}
		return null;
	}

	public static function loadMercenaryStrengthenList()
	{
		$db = self::getBasicDB();
		$list = $db->getMercenaryStrengthenList();
		if ($list) {
			$key = 'mercenarystrthen';
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}
		return $list;
	}
	
	/**
	 * 佣兵位置扩展信息
	 */
	public static function getMercenaryPositionList()
	{
        $key = self::getKey('mercenarypos');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$tpl = $localcache->get($key);
		if (!$tpl) {
			$cache = self::getBasicMC();
			$tpl = $cache->get($key);
			if (!$tpl) {
				$tpl = self::loadMercenaryPositionList();
			}
			if ($tpl) {
				$localcache->set($key, $tpl);
			}
		}
		return $tpl;
	}

	public static function getMercenaryPosition($id)
	{
		$list = self::getMercenaryPositionList();
		if (isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}

	public static function loadMercenaryPositionList()
	{
        $db = self::getBasicDB();
		$tpl = $db->getMercenaryPositionList();
		if ($tpl) {
        	$key = self::getKey('mercenarypos');
			$cache = self::getBasicMC();
			$cache->set($key, $tpl);
		}
		return $tpl;
	}

	/**
	 * 佣兵各属性品质随机表
	 */
	public static function getMercenaryQualityList()
	{
		$key = self::getKey('mercenaryquality');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$tpl = $localcache->get($key);
		if (!$tpl) {
			$cache = self::getBasicMC();
			$tpl = $cache->get($key);
			if (!$tpl) {
				$tpl = self::loadMercenaryQualityList();
			}
			if ($tpl) {
				$localcache->set($key, $tpl);
			}
		}
		return $tpl;
	}
	
	public static function getMercenaryQuality($id, $type)
	{
		$list = self::getMercenaryQualityList();
		
		foreach ( $list as $v ) {
			if ( $v['rp'] == $id && $v['type'] == $type ) {
				return $v;
			}
		}
		return null;
	}
	
	public static function loadMercenaryQualityList()
	{
		$db = self::getBasicDB();
		$tpl = $db->getMercenaryQualityList();
		if ($tpl) {
			$key = self::getKey('mercenaryquality');
			$cache = self::getBasicMC();
			$cache->set($key, $tpl);
		}
		return $tpl;
	}

	/**
	 * 佣兵属性对照表(4属性 对应  11属性)
	 */
	public static function getMercenaryProContrastList()
	{
		$key = self::getKey('mercenaryprocontrast');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$tpl = $localcache->get($key);
		if (!$tpl) {
			$cache = self::getBasicMC();
			$tpl = $cache->get($key);
			if (!$tpl) {
				$tpl = self::loadMercenaryProContrastList();
			}
			if ($tpl) {
				$localcache->set($key, $tpl);
			}
		}
		return $tpl;
	}
	
	public static function getMercenaryProContrast($job)
	{
		$list = self::getMercenaryProContrastList();
		$info = array();
		foreach ( $list as $v ) {
			if ($v['job'] == $job) {
				$info[$v['type']] = $v;
			}
		}
		return $info;
	}
	
	public static function loadMercenaryProContrastList()
	{
		$db = self::getBasicDB();
		$tpl = $db->getMercenaryProContrastList();
		if ($tpl) {
			$key = self::getKey('mercenaryprocontrast');
			$cache = self::getBasicMC();
			$cache->set($key, $tpl);
		}
		return $tpl;
	}
	
	/**
	 * 佣兵派驻打工
	 */
	public static function getMercenaryWorkList()
	{
        $key = self::getKey('mercenaryworkls');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$tpl = $localcache->get($key);
		if (!$tpl) {
			$cache = self::getBasicMC();
			$tpl = $cache->get($key);
			if (!$tpl) {
				$tpl = self::loadMercenaryWorkList();
			}
			if ($tpl) {
				$localcache->set($key, $tpl);
			}
		}
		return $tpl;
	}

	public static function getMercenaryWork($id)
	{
		$list = self::getMercenaryWorkList();
		if (isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}

	public static function loadMercenaryWorkList()
	{
        $db = self::getBasicDB();
		$tpl = $db->getMercenaryWorkList();
		if ($tpl) {
        	$key = self::getKey('mercenaryworkls');
			$cache = self::getBasicMC();
			$cache->set($key, $tpl);
		}
		return $tpl;
	}
	
	/*
     * 怪物
     */
    public static function getMonsterList()
	{
		$key = self::getKey('monster');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMonsterList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getMonsterInfo($id)
	{
		$list = self::getMonsterList();
		if (isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}

	public static function loadMonsterList()
	{
		$db = self::getBasicDB();
		$list = $db->getMonster();
		if ($list) {
			$key = self::getKey('monster');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	/*
     * 矿
     */
    public static function getMineList()
	{
		$key = self::getKey('mine');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMineList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getMineInfo($id)
	{
		$list = self::getMineList();
		if (isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}

	public static function loadMineList()
	{
		$db = self::getBasicDB();
		$list = $db->getMine();
		if ($list) {
			$key = self::getKey('mine');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	/*
     * 技能&道具物品 效果
     */
    public static function getEffectList()
	{
		$key = self::getKey('effect');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadEffectList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getEffectInfo($id)
	{
		$list = self::getEffectList();
		if (isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}

	public static function loadEffectList()
	{
		$db = self::getBasicDB();
		$list = $db->getEffect();
		if ($list) {
			$key = self::getKey('effect');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	/*
     * 职业属性相克
     */
    public static function getFightRestrict()
	{
		$key = self::getKey('restrict');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadFightRestrict();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function loadFightRestrict()
	{
		$db = self::getBasicDB();
		$list = $db->getFightRestriction();
		if ($list[1]) {
			$key = self::getKey('restrict');
			$cache = self::getBasicMC();
			$cache->set($key, $list[1]);
		}

		return $list[1];
	}

	/*
     * 战斗开战宣言
     */
    public static function getFightDeclareList()
	{
		$key = self::getKey('declare');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadFightDeclareList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getFightDeclareInfo($id)
	{
		$list = self::getFightDeclareList();
		if (isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}

	public static function loadFightDeclareList()
	{
		$db = self::getBasicDB();
		$list = $db->getFightDeclaration();
		if ($list) {
			$key = self::getKey('declare');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

    public static function getFightDeclareByJob($job)
	{
		$list = self::getFightDeclareList();
		$talks = array();
		if ($list) {
			foreach ($list as $data) {
				if ($data['job'] == $job) {
					$talks[] = $data['talk'];
				}
			}
		}

		return $talks;
	}

	/*
     * 战斗怪物站位规则描述
     */
    public static function getFightMonsterMatixList()
	{
		$key = self::getKey('monstermatrix');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadFightMonsterMatixList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getFightMonsterMatixInfo($id)
	{
		$list = self::getFightMonsterMatixList();
		if (isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}

	public static function loadFightMonsterMatixList()
	{
		$db = self::getBasicDB();
		$list = $db->getFightMonsterMatrix();
		if ($list) {
			$key = self::getKey('monstermatrix');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	/*
     * 战斗援助攻击技能条件
     */
    public static function getFightAssistanceList()
	{
		$key = self::getKey('assistance');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadFightAssistanceList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getFightAssistanceInfo($id)
	{
		$list = self::getFightAssistanceList();
		if (isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}

	public static function loadFightAssistanceList()
	{
		$db = self::getBasicDB();
		$list = $db->getFightAssistance();
		if ($list) {
			$key = self::getKey('assistance');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	/*
     * 世界地图
     */
    public static function getWorldMapList()
	{
		$key = self::getKey('worldmap');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadWorldMapList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getWorldMapInfo($id)
	{
		$list = self::getWorldMapList();
		if (isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}

	public static function loadWorldMapList()
	{
		$db = self::getBasicDB();
		$list = $db->getWorldMap();
		if ($list) {
			$key = self::getKey('worldmap');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	/*
     * 地图副本 版本号
     */
    public static function getMapCopyVerList()
	{
		$key = self::getKey('mapcopyver');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMapCopyVerList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getMapCopyVerInfo($id)
	{
		$list = self::getMapCopyVerList();
		if (isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}

	public static function loadMapCopyVerList()
	{
		$db = self::getBasicDB();
		$list = $db->getMapCopyVer();
		if ($list) {
			$key = self::getKey('mapcopyver');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	/* map copy related */
    public static function getMapCopyTranscriptList($mapId)
	{
		$key = self::getKey('mapcopydetail:').$mapId;
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadMapCopyTranscriptList($mapId);
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getMapCopyTranscript($mapId, $listName)
	{
		$list = self::getMapCopyTranscriptList($mapId);
		return $list[$listName];
	}

    public static function loadMapCopyTranscriptList($mapId)
	{
		$db = Hapyfish2_Alchemy_Dal_Basic::getDefaultInstance();
		$data = $db->getMapCopy($mapId);

		$list = array(
            'decorList' => array(),
            'floorList' => array(),
            'mineList' => array(),
            'monsterList' => array(),
            'portalList' => array(),
		    'jump' => 0,
		    'condition' => array()
		);
		if ($data && $data['decor_data']) {
		    $list['decorList'] = json_decode($data['decor_data'], true);
		}
	    if ($data && $data['floor_data']) {
		    $list['floorList'] = json_decode($data['floor_data'], true);
		}
	    if ($data && $data['mine_data']) {
		    $list['mineList'] = json_decode($data['mine_data'], true);
		}
	    if ($data && $data['monster_data']) {
		    $list['monsterList'] = json_decode($data['monster_data'], true);
		}
	    if ($data && $data['portal_data']) {
		    $list['portalList'] = json_decode($data['portal_data'], true);
		}
		if ($data && $data['jump']) {
            $list['jump'] = (int)$data['jump'];
		}
	    if ($data && $data['condition']) {
            $list['condition'] = json_decode($data['condition'], true);
		}

		$key = self::getKey('mapcopydetail:').$mapId;
		$cache = self::getBasicMC();
		$cache->set($key, $list);
		return $list;
	}

	/*
     * 任务类型
     */
    public static function getTaskTypeList()
	{
		$key = self::getKey('tasktype');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadTaskTypeList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}
		return $list;
	}

	public static function getTaskTypeInfo($id)
	{
		$list = self::getTaskTypeList();
		if (isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}

	public static function loadTaskTypeList()
	{
		$db = self::getBasicDB();
		$list = $db->getTaskTypeList();
		if ($list) {
			$key = self::getKey('tasktype');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	/*
     * 任务条件
     */
    public static function getTaskConditionList()
	{
		$key = self::getKey('taskcondilist');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadTaskConditionList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	/**
	 * 任务条件很多，如果取一个都从一个整体列表里去取，内存开销有点大
	 * 细粒度到每个，但重新刷新的时候需要遍历更新
	 *
	 */
	public static function getTaskConditionInfo($id)
	{
		$key = self::getKey('taskcondi:' . $id);
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$info = $localcache->get($key);
		if (!$info) {
			$list = self::getTaskConditionList();
			if (isset($list[$id])) {
				$info = $list[$id];
				$localcache->set($key, $info);
			}
		}

		return $info;
	}

	public static function loadAllTaskConditionInfo()
	{
		$list = self::getTaskConditionList();
		if ($list) {
			$localcache = Hapyfish2_Cache_LocalCache::getInstance();
			foreach ($list as $item) {
				$key = self::getKey('taskcondi:' . $item['id']);
				$localcache->set($key, $item);
			}
			return true;
		}

		return false;
	}

	public static function loadTaskConditionList()
	{
		$db = self::getBasicDB();
		$list = $db->getTaskConditionList();
		if ($list) {
			$key = self::getKey('taskcondilist');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	/*
     * 任务
     */
    public static function getTaskList()
	{
		$key = self::getKey('tasklist');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadTaskList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getTaskInfo($id)
	{
		$key = self::getKey('task:' . $id);
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$info = $localcache->get($key);
		if (!$info) {
			$list = self::getTaskList();
			if (isset($list[$id])) {
				$info = $list[$id];
				$localcache->set($key, $info);
			}
		}

		return $info;
	}

	public static function loadAllTaskInfo()
	{
		$list = self::getTaskList();
		if ($list) {
			$localcache = Hapyfish2_Cache_LocalCache::getInstance();
			foreach ($list as $item) {
				$key = self::getKey('task:' . $item['id']);
				$localcache->set($key, $item);
			}
			return true;
		}

		return false;
	}

    public static function loadTaskList()
	{
		$db = self::getBasicDB();
		$list = $db->getTaskList();
		if ($list) {
			$key = self::getKey('tasklist');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}

	public static function getTaskIdsByLevel($level, $fightLevel)
	{
		$list = self::getTaskList();
		$ids = array();
		if ($list) {
			foreach ($list as $task) {
				if ($task['need_user_level'] <= $level && $task['need_fight_level'] <= $fightLevel && $task['front_task_id'] == '[]') {
					$ids[] = (int)$task['id'];
				}
			}
		}

		return $ids;
	}

    public static function getDailyTaskIds()
	{
		$list = self::getTaskList();
		$ids = array();
		if ($list) {
			foreach ($list as $task) {
				if ($task['label'] == 3) {
					$ids[] = (int)$task['id'];
				}
			}
		}
		return $ids;
	}

/**********************************************************************************************/

    /**
     * 合成术
     */
    public static function getMixList()
    {
        $key = self::getKey('mix');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadMixList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getMixInfo($id)
    {
        $list = self::getMixList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadMixList()
    {
        $db = self::getBasicDB();
        $list = $db->getMix();
        if ($list) {
            $key = self::getKey('mix');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * 物品
     */
    public static function getGoodsList()
    {
        $key = self::getKey('goods');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadGoodsList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getGoodsInfo($id)
    {
        $list = self::getGoodsList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadGoodsList()
    {
        $db = self::getBasicDB();
        $list = $db->getGoods();
        if ($list) {
            $key = self::getKey('goods');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * 物品-17-佣兵卡
     */
    public static function getMercenaryCardList()
    {
        $key = self::getKey('mercenarycard');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadMercenaryCardList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }
    
    public static function getMercenaryCardInfo($id)
    {
        $list = self::getMercenaryCardList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function getMercenaryCardInfoByName($name)
    {
        $list = self::getMercenaryCardList();
        foreach ( $list as $v ) {
        	if ( $v['m_name'] == $name ) {
        		return $v;
        	}
        }
        return null;
    }
    
    public static function loadMercenaryCardList()
    {
        $db = self::getBasicDB();
        $list = $db->getMercenaryCard();
        if ($list) {
            $key = self::getKey('mercenarycard');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }
    
    /**
     * 卷轴
     */
    public static function getScrollList()
    {
        $key = self::getKey('scroll');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadScrollList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getScrollInfo($id)
    {
        $list = self::getScrollList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadScrollList()
    {
        $db = self::getBasicDB();
        $list = $db->getScroll();
        if ($list) {
            $key = self::getKey('scroll');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * 材料
     */
    public static function getStuffList()
    {
        $key = self::getKey('stuff');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadStuffList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getStuffInfo($id)
    {
        $list = self::getStuffList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadStuffList()
    {
        $db = self::getBasicDB();
        $list = $db->getStuff();
        if ($list) {
            $key = self::getKey('stuff');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * 工作台
     */
    public static function getFurnaceList()
    {
        $key = self::getKey('furnace');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadFurnaceList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getFurnaceInfo($id)
    {
        $list = self::getFurnaceList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadFurnaceList()
    {
        $db = self::getBasicDB();
        $list = $db->getFurnace();
        if ($list) {
            $key = self::getKey('furnace');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * 装饰物
     */
    public static function getDecorList()
    {
        $key = self::getKey('decor');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadDecorList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getDecorInfo($id)
    {
        $list = self::getDecorList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadDecorList()
    {
        $db = self::getBasicDB();
        $list = $db->getDecor();
        if ($list) {
            $key = self::getKey('decor');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * 装备
     */
    public static function getWeaponList()
    {
        $key = self::getKey('weapon');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadWeaponList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getWeaponInfo($id)
    {
    	$key = self::getKey('weapon');
    	$key .= ':'. $id;
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$data = $localcache->get($key);
		if(!$data){
			 $list = self::getWeaponList();
			if (isset($list[$id])) {
	            $data = $list[$id];
				$localcache->set($key, $data);
				return $data;
	        }
		}else {
			return $data;
		}
        return null;
    }

    public static function loadWeaponList()
    {
        $db = self::getBasicDB();
        $list = $db->getWeapon();
        if ($list) {
            $key = self::getKey('weapon');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
       		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
			foreach($list as $k=>$v){
				$keys = $key . ':'. $v['cid'];
				$localcache->set($keys, $v);
			}
        }
        return $list;
    }

    /**
     * 图鉴
     */
    public static function getIllustrationsList()
    {
        $key = self::getKey('illustrations');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadIllustrationsList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getIllustrationsInfo($id)
    {
        $list = self::getIllustrationsList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    /**
     * 根据对应物品 item_cid 查询图鉴信息
     * @param int $cid
     */
    public static function getIllustInfoByCid($cid)
    {
        $list = self::getIllustrationsList();
        foreach ( $list as $v ) {
        	if ($v['item_cid'] == $cid) {
        		return $v;
        	}
        }
        return null;
    }

    public static function loadIllustrationsList()
    {
        $db = self::getBasicDB();
        $list = $db->getIllustrations();
        if ($list) {
            $key = self::getKey('illustrations');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * 用户等级
     */
	public static function getUserLevelList()
	{
		$key = self::getKey('userlevellist');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadUserLevelList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}
		return $list;
	}

	public static function getUserNextLevelByExp($exp)
	{
		$result = array();
		$list = self::getUserLevelList();
		foreach ( $list as $v ) {
			if ( $v['exp'] <= $exp ) {
				$result = $v;
			}
		}
		return $result;
	}
	
	public static function getUserLevelInfo($id)
	{
		$list = self::getUserLevelList();
		if (isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}

	public static function getUserLevelExp($id)
	{
		$list = self::getUserLevelList();
		if (isset($list[$id])) {
			return $list[$id]['exp'];
		}
		return 999999999;
	}

	public static function loadUserLevelList()
	{
		$db = self::getBasicDB();
		$list = $db->getUserLevelList();
		if ($list) {
			$key = self::getKey('userlevellist');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}
		return $list;
	}

    /**
     * 房间等级
     */
    public static function getRoomLevelList()
    {
        $key = self::getKey('roomlevellist');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadRoomLevelList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getRoomLevelInfo($id)
    {
        $list = self::getRoomLevelList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadRoomLevelList()
    {
        $db = self::getBasicDB();
        $list = $db->getRoomLevelList();
        if ($list) {
            $key = self::getKey('roomlevellist');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * avatar
     */
    public static function getAvatarList()
    {
        $key = self::getKey('avatar');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadAvatarList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getAvatarInfo($id)
    {
        $list = self::getAvatarList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadAvatarList()
    {
        $db = self::getBasicDB();
        $list = $db->getAvatar();
        if ($list) {
            $key = self::getKey('avatar');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * 场景
     */
    public static function getSceneList()
    {
        $key = self::getKey('scene');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadSceneList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getSceneInfo($id)
    {
        $list = self::getSceneList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadSceneList()
    {
        $db = self::getBasicDB();
        $list = $db->getScene();
        if ($list) {
            $key = self::getKey('scene');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * 订单
     */
    public static function getOrderList()
    {
        $key = self::getKey('order');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadOrderList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getOrderInfo($id)
    {
        $list = self::getOrderList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadOrderList()
    {
        $db = self::getBasicDB();
        $list = $db->getOrder();
        if ($list) {
            $key = self::getKey('order');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    public static function getOrderListByLevel($orderLevel)
    {
    	$list = self::getOrderList();
    	$data = array();
    	foreach ( $list as $v ) {
    		if ( $v['level'] == $orderLevel ) {
    			$data[$v['cid']] = $v;
    		}
    	}
    	return $data;
    }

    /**
     * 订单概率
     */
    public static function getOrderPro()
    {
        $key = self::getKey('orderpro');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadOrderPro();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getOrderProByLevel($level)
    {
        $list = self::getOrderPro();

        $id = ceil($level/5) * 5;
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadOrderPro()
    {
        $db = self::getBasicDB();
        $list = $db->getOrderPro();
        if ($list) {
            $key = self::getKey('orderpro');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * NPC名字
     */
    public static function getAvatarNameList()
    {
        $key = self::getKey('avatarname');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadAvatarNameList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getAvatarName($id)
    {
        $list = self::getAvatarNameList();
        if (isset($list[$id])) {
            return $list[$id]['name'];
        }
        return null;
    }

    public static function loadAvatarNameList()
    {
        $db = self::getBasicDB();
        $list = $db->getAvatarNameList();
        if ($list) {
            $key = self::getKey('avatarname');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * 剧情列表
     */
    public static function getStoryList()
    {
        $key = self::getKey('storylist');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadStoryList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getStory($id)
    {
        $list = self::getStoryList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadStoryList()
    {
        $db = self::getBasicDB();
        $list = $db->getStoryList();
        if ($list) {
            $key = self::getKey('storylist');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * 剧情-动作列表
     */
    public static function getStoryActionList()
    {
        $key = self::getKey('storyactionlist');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadStoryActionList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getStoryAction($id)
    {
        $list = self::getStoryActionList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadStoryActionList()
    {
        $db = self::getBasicDB();
        $list = $db->getStoryActionList();
        if ($list) {
            $key = self::getKey('storyactionlist');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * 剧情-演员列表
     */
    public static function getStoryNpcList()
    {
        $key = self::getKey('storynpclist');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadStoryNpcList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getStoryNpc($id)
    {
        $list = self::getStoryNpcList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadStoryNpcList()
    {
        $db = self::getBasicDB();
        $list = $db->getStoryNpcList();
        if ($list) {
            $key = self::getKey('storynpclist');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * 剧情-对白表
     */
    public static function getStoryDialogList()
    {
        $key = self::getKey('storydialoglist');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadStoryDialogList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getStoryDialog($id)
    {
        $list = self::getStoryDialogList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function getStoryDialogByUserLevel($level)
    {
        $list = self::getStoryDialogList();
        $data = array();
        foreach ( $list as $v ) {
        	if ($v['user_level'] == $level) {
        		$data[$v['id']] = $v;
        	}
        }
        return $data;
    }

    public static function getStoryDialogByFightLevel($level)
    {
        $list = self::getStoryDialogList();
        $data = array();
        foreach ( $list as $v ) {
        	if ($v['fight_level'] == $level) {
        		$data[$v['id']] = $v;
        	}
        }
        return $data;
    }

    public static function loadStoryDialogList()
    {
        $db = self::getBasicDB();
        $list = $db->getStoryDialogList();
        if ($list) {
            $key = self::getKey('storydialoglist');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * 酒馆等级列表
     */
    public static function getTavernLevelList()
    {
        $key = self::getKey('tavernlevel');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadTavernLevelList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getTavernLevel($level, $id = TAVERN_ID)
    {
        $list = self::getTavernLevelList();
        foreach ( $list as $v ) {
        	if ( $v['id'] == $id && $v['level'] == $level ) {
        		return $v;
        	}
        }
        return null;
    }

    public static function loadTavernLevelList()
    {
        $db = self::getBasicDB();
        $list = $db->getTavernLevelList();
        if ($list) {
            $key = self::getKey('tavernlevel');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * 铁匠铺等级列表
     */
    public static function getSmithyLevelList()
    {
        $key = self::getKey('smithylevel');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadSmithyLevelList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getSmithyLevel($id)
    {
        $list = self::getSmithyLevelList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadSmithyLevelList()
    {
        $db = self::getBasicDB();
        $list = $db->getSmithyLevelList();
        if ($list) {
            $key = self::getKey('smithylevel');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }
    
    /**
     * 竞技场等级列表
     */
    public static function getArenaLevelList()
    {
        $key = self::getKey('arenalevel');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadArenaLevelList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getArenaLevel($id)
    {
        $list = self::getArenaLevelList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadArenaLevelList()
    {
        $db = self::getBasicDB();
        $list = $db->getArenaLevelList();
        if ($list) {
            $key = self::getKey('arenalevel');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * 训练营等级列表
     */
    public static function getTrainingLevelList()
    {
    	$key = self::getKey('traininglevel');
    	$localcache = Hapyfish2_Cache_LocalCache::getInstance();
    
    	$list = $localcache->get($key);
    	if (!$list) {
    		$cache = self::getBasicMC();
    		$list = $cache->get($key);
    		if (!$list) {
    			$list = self::loadTrainingLevelList();
    		}
    		if ($list) {
    			$localcache->set($key, $list);
    		}
    	}
    	return $list;
    }
    
    public static function getTrainingLevel($id)
    {
    	$list = self::getTrainingLevelList();
    	if (isset($list[$id])) {
    		return $list[$id];
    	}
    	return null;
    }
    
    public static function loadTrainingLevelList()
    {
    	$db = self::getBasicDB();
    	$list = $db->getTrainingLevelList();
    	if ($list) {
    		$key = self::getKey('traininglevel');
    		$cache = self::getBasicMC();
    		$cache->set($key, $list);
    	}
    	return $list;
    }
    
    /**
     * 自宅等级列表
     */
    public static function getHomeLevelList()
    {
        $key = self::getKey('homelevel');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadHomeLevelList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getHomeLevel($id)
    {
        $list = self::getHomeLevelList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadHomeLevelList()
    {
        $db = self::getBasicDB();
        $list = $db->getHomeLevelList();
        if ($list) {
            $key = self::getKey('homelevel');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * 主角等级列表
     */
    public static function getRoleLevelList()
    {
        $key = self::getKey('rolelevel');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadRoleLevelList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getRoleLevel($id)
    {
        $list = self::getRoleLevelList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadRoleLevelList()
    {
        $db = self::getBasicDB();
        $list = $db->getRoleLevelList();
        if ($list) {
            $key = self::getKey('rolelevel');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }

    /**
     * 初始化用户数据
     */
    public static function getInitUserInfo()
    {
        $key = self::getKey('inituserinfo');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $data = $localcache->get($key);
        if (!$data) {
            $cache = self::getBasicMC();
            $data = $cache->get($key);
            if (!$data) {
                $data = self::loadInitUserInfo();
            }
            if ($data) {
                $localcache->set($key, $data);
            }
        }
        return $data;
    }

    public static function loadInitUserInfo()
    {
        $db = self::getBasicDB();
        $data = $db->getInitUserInfo();
        if ($data) {
        	$fields = array(
        		'goods','stuff','furnace','decor','open_scene',
        		'scroll','equipment','home_decor'
        	);
			foreach ($fields as $f) {
				if ($data[$f] != '') {
					$data[$f] = json_decode($data[$f], true);
				} else {
					$data[$f] = array();
				}
			}

            $key = self::getKey('inituserinfo');
            $cache = self::getBasicMC();
            $cache->set($key, $data);
        }
        return $data;
    }

    public static function getInitRoleList()
    {
        $key = self::getKey('initrolelist');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $data = $localcache->get($key);
        if (!$data) {
            $cache = self::getBasicMC();
            $data = $cache->get($key);
            if (!$data) {
                $data = self::loadInitRoleList();
            }
            if ($data) {
                $localcache->set($key, $data);
            }
        }
        return $data;
    }

    public static function getInitRole($id)
    {
        $list = self::getInitRoleList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadInitRoleList()
    {
        $db = self::getBasicDB();
        $data = $db->getInitRoleList();
        if ($data) {
            $key = self::getKey('initrolelist');
            $cache = self::getBasicMC();
            $cache->set($key, $data);
        }
        return $data;
    }

	public static function getActLoginInterval()
	{
        $key = self::getKey('actlogininterval');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$interval = $localcache->get($key);
		if (!$interval) {
			$cache = self::getBasicMC();
			$interval = $cache->get($key);
			if (!$interval) {
				//60 seconds
				$interval = 60;
			}
			$localcache->set($key, $interval, false);
		}

		return $interval;
	}

	/**
	 * 日志消息
	 */
	public static function getFeedTemplate()
	{
        $key = self::getKey('feedtemplate');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$tpl = $localcache->get($key);
		if (!$tpl) {
			$cache = self::getBasicMC();
			$tpl = $cache->get($key);
			if (!$tpl) {
				$tpl = self::loadFeedTemplate();
			}
			if ($tpl) {
				$localcache->set($key, $tpl);
			}
		}
		return $tpl;
	}

	public static function getFeedTemplateTitle($template_id)
	{
		$tpl = self::getFeedTemplate();
		if ($tpl && isset($tpl[$template_id])) {
			return $tpl[$template_id];
		}
		return null;
	}

	public static function loadFeedTemplate()
	{
        $db = self::getBasicDB();
		$tpl = $db->getFeedTemplate();
		if ($tpl) {
        	$key = self::getKey('feedtemplate');
			$cache = self::getBasicMC();
			$cache->set($key, $tpl);
		}
		return $tpl;
	}

	/**
	 * 新手引导
	 */
	public static function getHelpList()
	{
        $key = self::getKey('help');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$data = $localcache->get($key);
		if (!$data) {
			$cache = self::getBasicMC();
			$data = $cache->get($key);
			if (!$data) {
				$data = self::loadHelpList();
			}
			if ($data) {
				$localcache->set($key, $data);
			}
		}
		return $data;
	}

	public static function getHelp($id)
	{
		$list = self::getHelpList();
		if (isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}

	public static function loadHelpList()
	{
        $db = self::getBasicDB();
		$data = $db->getHelpList();
		if ($data) {
        	$key = self::getKey('help');
			$cache = self::getBasicMC();
			$cache->set($key, $data);
		}
		return $data;
	}
	
	/**
	 * 全地图NPC列表
	 */
	public static function getPersonList()
	{
        $key = self::getKey('person');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$data = $localcache->get($key);
		if (!$data) {
			$cache = self::getBasicMC();
			$data = $cache->get($key);
			if (!$data) {
				$data = self::loadPersonList();
			}
			if ($data) {
				$localcache->set($key, $data);
			}
		}
		return $data;
	}

	/**
	 * 指定地图，NPC列表
	 */
	public static function getPersonListByMap($mapId)
	{
		$list = self::getPersonList();
		$data = array();
		foreach ( $list as $v ) {
			if ( $v['map_id'] == $mapId ) {
				$data[] = $v;
			}
		}
		return $data;
	}
	
	public static function getPerson($id)
	{
		$list = self::getPersonList();
		if (isset($list[$id])) {
			return $list[$id];
		}
		return null;
	}

	public static function loadPersonList()
	{
        $db = self::getBasicDB();
		$data = $db->getPersonList();
		if ($data) {
        	$key = self::getKey('person');
			$cache = self::getBasicMC();
			$cache->set($key, $data);
		}
		return $data;
	}
	
    /**
     * 副本传送门信息
     */
    public static function getTransportList()
    {
        $key = self::getKey('transport');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadTransportList();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function getTransport($id)
    {
        $list = self::getTransportList();
        if (isset($list[$id])) {
            return $list[$id];
        }
        return null;
    }

    public static function loadTransportList()
    {
        $db = self::getBasicDB();
        $list = $db->getTransportList();
        if ($list) {
            $key = self::getKey('transport');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }
	
    /**
     * 副本传送门信息
     */
    public static function getFightExpLevDiff()
    {
        $key = self::getKey('fightexplevdiff');
        $localcache = Hapyfish2_Cache_LocalCache::getInstance();

        $list = $localcache->get($key);
        if (!$list) {
            $cache = self::getBasicMC();
            $list = $cache->get($key);
            if (!$list) {
                $list = self::loadFightExpLevDiff();
            }
            if ($list) {
                $localcache->set($key, $list);
            }
        }
        return $list;
    }

    public static function loadFightExpLevDiff()
    {
        $db = self::getBasicDB();
        $list = $db->getFightExpLevDiff();
        if ($list) {
            $key = self::getKey('fightexplevdiff');
            $cache = self::getBasicMC();
            $cache->set($key, $list);
        }
        return $list;
    }
    
	public static function getPlatformFeedTemplate()
	{
		$key = self::getKey('PlatformFeedTemplate');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$tpl = $localcache->get($key);
		if (!$tpl) {
			$cache = self::getBasicMC();
			$tpl = $cache->get($key);
			if (!$tpl) {
				$tpl = self::loadPlatformFeedTemplate();
			}
			if ($tpl) {
				$localcache->set($key, $tpl);
			}
		}
		
		return $tpl;
	}
	
	public static function getPlatformFeedTemplateInfo($template_id)
	{
		$tpl = self::getPlatformFeedTemplate();
		if ($tpl && isset($tpl[$template_id])) {
			return $tpl[$template_id];
		}
		
		return null;
	}
	
	public static function loadPlatformFeedTemplate()
	{
		$db = self::getBasicDB();
		$tpl = $db->getPlatformFeedTemplate();
		if ($tpl) {
			foreach ($tpl as &$item) {
				if ($item['award'] != '') {
					$item['award'] = json_decode($item['award'], true);
				} else {
					$item['award'] = array();
				}
			}
			$key = self::getKey('PlatformFeedTemplate');
			$cache = self::getBasicMC();
			$cache->set($key, $tpl);
		}
		
		return $tpl;
	}
	
	public static function getPaySettingList()
	{
		$key = self::getKey('PaySettingList');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();

		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadPaySettingList();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}

		return $list;
	}

	public static function getPaySettingInfo($id)
	{
		$list = self::getPaySettingList();
		if (isset($list[$id])) {
			return $list[$id];
		}

		return null;
	}
	
	public static function updatePaySettingInfo($id, $info)
	{
		$db = self::getBasicDB();
		try {
			$db->updatePaySetting($id, $info);
		} catch(Exception $e) {
			return false;
		}
		
		self::loadPaySettingList();
		return true;
	}
	
	public static function resetPaySettingLocalCache()
	{
		$key = self::getKey('PaySettingList');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$cache = self::getBasicMC();
		$list = $cache->get($key);
		if (!$list) {
			$list = self::loadPaySettingList();
		}
		$localcache->set($key, $list);
	}

	public static function loadPaySettingList()
	{
		$db = self::getBasicDB();
		$list = $db->getPaySettingList();
		if ($list) {
			foreach ($list as &$v) {
				if (!empty($v['section'])) {
					$v['section'] = json_decode($v['section'], true);
				}
			}
			$key = self::getKey('PaySettingList');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}
	
	public static function getMapIdsByGroup($group)
	{
		$list = array();
		$basic = self::getTransportList();
		foreach($basic as $transport){
			if($transport['group'] == $group){
				$list[] = $transport['map_id'];
			}
		}
		return $list;
	}
	
	public static function getGroupById($id)
	{
		$basic = self::getTransport($id);
		return $basic['group'];
	}
	
	public static function getHelltowerShop()
	{
		$key = self::getKey('HelltowerShop');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if(!$list){
				$list = self::loadHelltowerShop();
			}
			
			if($list){
				$localcache->set($key, $list);
			}
		}
		return $list;
		
	}
	
	public static function getHelltowerShopInfo($cid)
	{
		$list = self::getHelltowerShop();
		if(isset($list[$cid])){
			return $list[$cid];
		}
		return null;
	}
	
	public static function loadHelltowerShop()
	{
		$db = self::getBasicDB();
		$list = $db->getHelltowerShop();
		if ($list) {
			$key = self::getKey('HelltowerShop');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}
	
	public static function getHelltowerConfig($type)
	{
		$data = array();
		$key = self::getKey('HelltowerConfig');
		$localcache = Hapyfish2_Cache_LocalCache::getInstance();
		$cache = self::getBasicMC();
		$list = $localcache->get($key);
		if (!$list) {
			$cache = self::getBasicMC();
			$list = $cache->get($key);
			if (!$list) {
				$list = self::loadHelltowerConfig();
			}
			if ($list) {
				$localcache->set($key, $list);
			}
		}
		foreach($list as $key=>$v){
			if($v['type'] == $type){
				$data[] = $v;
			}
		}
		return $data;
	}
	
	public static function getHelltowerInfo($type, $num)
	{
		$list = self::getHelltowerConfig($type);
		foreach($list as $key => $v){
			if($num == $v['id']){
				return $v;
			}
		}
		return null;
	}
	
	public static function loadHelltowerConfig()
	{
		$db = self::getBasicDB();
		$list = $db->getHelltowerConfig();
		if ($list) {
			foreach($list as $k=>&$v){
				$v['awards'] = json_decode($v['awards'], true);
				$v['monster_stations'] = json_decode($v['monster_stations'], true);
			}
			$key = self::getKey('HelltowerConfig');
			$cache = self::getBasicMC();
			$cache->set($key, $list);
		}

		return $list;
	}
}