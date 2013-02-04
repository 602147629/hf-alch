<?php

class Hapyfish2_Alchemy_Cache_User
{
	public static function isAppUser($uid)
    {
        $key = 'a:u:isapp:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);

        if ($data === false) {
			if ($cache->isNotFound()) {
				$levelInfo = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
				if (!$levelInfo) {
					return false;
				} else {
					$data = 'Y';
					$cache->set($key, $data);
					return true;
				}
			} else {
				return false;
			}
        }

        if ($data == 'Y') {
        	return true;
        } else {
        	return false;
        }
    }

    public static function setAppUser($uid)
    {
        $key = 'a:u:isapp:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, 'Y');
    }

    public static function isSinglePointLogin($uid, $skey)
    {
        $key = 'a:u:hfskey:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $savedSkey = $cache->get($key);
        if (empty($savedSkey)) {
            return false;
        }
        if ($savedSkey != $skey) {
            return false;
        }
        return true;
    }

    public static function setLoginUserSkey($uid, $skey)
    {
        $key = 'a:u:hfskey:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, $skey);
        return;
    }

    public static function getLoginUserSkeyWatch($uid)
    {
        $key = 'a:u:hfskeywch:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $skey = $cache->get($key);
        return $skey;
    }

    public static function setLoginUserSkeyWatch($uid, $skey)
    {
        $key = 'a:u:hfskeywch:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, $skey, 86400);
        return;
    }
    
	public static function getCreateTime($uid)
    {
        $key = 'a:u:createtm:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);

        if ($data === false) {
			$dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
			$data = $dalUser->getCreateTime($uid);
			if (!$data) {
				return false;
			} else {
				$cache->set($key, $data, 86400);
			}
        }
        return $data;
    }
    
    public static function getAccess($uid)
    {
    	$key = 'a:u:access:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
		$date = date('Ymd');
        if ($data === false || $data['date'] != $date) {
        	$data = array(
        		'list'=>array(),
        		'date'=>$date
        	);
			
        }
        return $data;
    }
    
    public static function updateAccess($uid, $data)
    {
    	$key = 'a:u:access:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, $data);
    }

    /**
     * 今天已经使用的免费传送次数
     * @param int $uid
     * @return number
     */
    public static function getTodayUsedFreeTransportCnt($uid)
    {
    	$key = 'a:u:todaytranscnt:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$data = $cache->get($key);
    	if ( $data === false ) {
    		$data = 0;
    	}
    	return $data;
    }

    /**
     * 更新今天已经使用的免费传送次数
     * @param int $uid
     * @param int $data
     * @return number
     */
    public static function updateTodayFreeTransportCnt($uid, $data)
    {
    	$key = 'a:u:todaytranscnt:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$cache->set($key, $data, 86400);
    	return;
    }

    /**
     * 建筑升级冷却结束时间
     * @param int $uid
     * @return number
     */
    public static function getBuildingCdTm($uid)
    {
    	$key = 'a:u:buildcdtm:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$data = $cache->get($key);
    	if ( $data === false ) {
    		$data = 0;
    	}
    	return $data;
    }
    
    /**
     * 更新建筑升级冷却结束时间
     * @param int $uid
     * @param int $data
     * @return number
     */
    public static function updateBuildingCdTm($uid, $data)
    {
    	$key = 'a:u:buildcdtm:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$cache->set($key, $data, 86400);
    	return true;
    }

    /**
     * 玩家是否第一次培养佣兵
     * @param int $uid
     * @return number
     */
    public static function isFirstStrengthen($uid)
    {
    	$key = 'a:u:isfirststrth:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$data = $cache->get($key);
    	if ( $data === false ) {
    		$data = 'Y';
    	}
    	return $data;
    }
    
    /**
     * 第一次培养佣兵
     * @param int $uid
     * @param int $data
     * @return number
     */
    public static function setFirstStrengthen($uid, $data = 'N')
    {
    	$key = 'a:u:isfirststrth:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getMC($uid);
    	$cache->set($key, $data);
    }
    
    
    
}