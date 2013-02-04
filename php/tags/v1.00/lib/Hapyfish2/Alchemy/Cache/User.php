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
        $data = $cache->get($key);
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
}