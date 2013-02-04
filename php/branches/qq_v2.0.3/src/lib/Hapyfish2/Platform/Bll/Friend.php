<?php

class Hapyfish2_Platform_Bll_Friend
{

    public static function getFriend($uid)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Bll_Entry_Qzone_Friend::getFriend($uid);
                break;
            default:
                return Hapyfish2_Platform_Bll_Entry_Default_Friend::getFriend($uid);
                break;
        }
    }

    public static function updateFriend($uid, $fids, $highcache = false)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Bll_Entry_Qzone_Friend::updateFriend($uid, $fids, $highcache);
                break;
            default:
                return Hapyfish2_Platform_Bll_Entry_Default_Friend::updateFriend($uid, $fids, $highcache);
                break;
        }
    }

    public static function addFriend($uid, $fids, $highcache = false)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Bll_Entry_Qzone_Friend::addFriend($uid, $fids, $highcache);
                break;
            default:
                return Hapyfish2_Platform_Bll_Entry_Default_Friend::addFriend($uid, $fids, $highcache);
                break;
        }
    }

    public static function getFriendIds($uid)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Bll_Entry_Qzone_Friend::getFriendIds($uid);
                break;
            default:
                return Hapyfish2_Platform_Bll_Entry_Default_Friend::getFriendIds($uid);
                break;
        }
    }

    public static function getFriendCount($uid)
    {
        $data = self::getFriend($uid);
        if (empty($data)) {
            return 0;
        }
        return $data['count'];
    }

    public static function isFriend($uid, $fid)
    {
        $fids = self::getFriendIds($uid);
        if (empty($fids)) {
            return false;
        }
        return in_array($fid, $fids);
    }
}