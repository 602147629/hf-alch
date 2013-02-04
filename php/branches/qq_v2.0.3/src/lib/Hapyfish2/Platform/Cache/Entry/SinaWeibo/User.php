<?php

class Hapyfish2_Platform_Cache_Entry_SinaWeibo_User
{

    public static function getIdentity($uid)
    {
        $key = 'p:u:identity:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data === false) {
            if ($cache->isNotFound()) {
                try {
                    $dalUser = Hapyfish2_Platform_Dal_Entry_SinaWeibo_User::getDefaultInstance();
                    $data = $dalUser->getIdentity($uid);
                    if ($data) {
                        $cache->add($key, $data);
                    } else {
                        $data = 0;
                    }
                }
                catch (Exception $e) {
                    $data = 0;
                }
            } else {
                $data = 0;
            }
        }
        return $data;
    }

    public static function getVerified($uid)
    {
        $key = 'p:u:verif:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data === false) {
            if ($cache->isNotFound()) {
                try {
                    $dalUser = Hapyfish2_Platform_Dal_Entry_SinaWeibo_User::getDefaultInstance();
                    $data = $dalUser->getVerified($uid);
                    if ($data) {
                        $cache->add($key, $data);
                    } else {
                        $data = 0;
                    }
                }
                catch (Exception $e) {
                    $data = 0;
                }
            } else {
                $data = 0;
            }
        }
        return $data;
    }

    public static function updateVerified($uid, $data)
    {
        $key = 'p:u:verif:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $result = $cache->set($key, $data);
        try {
            $dalUser = Hapyfish2_Platform_Dal_Entry_SinaWeibo_User::getDefaultInstance();
            $dalUser->update($uid, array(
                'verified' => $data
            ));
        }
        catch (Exception $e) {
            err_log('Hapyfish2_Platform_Cache_User::updateVerified Err' . $e->getMessage());
        }
        return $result;
    }

    public static function updateIdentity($uid, $data)
    {
        $key = 'p:u:identity:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $result = $cache->set($key, $data);
        try {
            $dalUser = Hapyfish2_Platform_Dal_Entry_SinaWeibo_User::getDefaultInstance();
            $dalUser->update($uid, array(
                'identity' => $data
            ));
        }
        catch (Exception $e) {
            err_log('Hapyfish2_Platform_Cache_User::updateidentity Err' . $e->getMessage());
        }
        return $result;
    }

    public static function updateOnlineTime($uid, $data)
    {
        $key = 'p:u:online:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $result = $cache->set($key, $data);
    }

    public static function getOnlineTime($uid)
    {
        $key = 'p:u:online:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        $date = date('Ymd');
        if ($data === false || $data['date'] != $date) {
            $data['date'] = $date;
            $data['onlineTime'] = 0;
        }
        return $data;
    }

    public static function getUserV($uid)
    {
        $vuid = Hapyfish2_Platform_Cache_Entry_Default_User::getVUID($uid);
        $verified = self::getVerified($uid);
        if ($vuid == - 1) {
            return 1;
        }
        if ($vuid == 200) {
            return 1;
        }
        if ($vuid == 0) {
            return 3;
        }
        if ($vuid == 220) {
            return 4;
        }
        return 2;
    }
}