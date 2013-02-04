<?php

class Hapyfish2_Alchemy_Cache_MapCopy
{
	public static function getMapCopySeries($uid)
    {
        $key = 'a:u:mapcopy:series:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $info = $cache->get($key);
        return $info;
    }

    public static function setMapCopySeries($uid, $info)
    {
        $key = 'a:u:mapcopy:series:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        return $cache->set($key, $info);
    }

    public static function clearMapCopySeries($uid)
    {
        $key = 'a:u:mapcopy:series:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        return $cache->delete($key);
    }

    public static function getMapCopySeriesById($uid, $id)
    {
        $key = 'a:u:mapcopy:series:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $info = $cache->get($key);
        if ($info && isset($info[$id])) {
            return $info[$id];
        }
        else {
            return null;
        }
    }

    public static function setMapCopySeriesById($uid, $id, $data)
    {
        $key = 'a:u:mapcopy:series:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $info = $cache->get($key);
        if (!$info) {
            $info = array();
        }
        $info[$id] = $data;
        return $cache->set($key, $info);
    }

    public static function clearMapCopySeriesById($uid, $id)
    {
        $key = 'a:u:mapcopy:series:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $info = $cache->get($key);
        if ($info) {
            $info[$id] = array();
        }
        return $cache->set($key, $info);
    }
}