<?php

class Hapyfish2_Platform_Cache_Friend
{

    public static function getFriend($uid, $entryId = 0)
    {
        if ($entryId == 0 && defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Cache_Entry_Qzone_Friend::getFriend($uid);
                break;
            default:
                return Hapyfish2_Platform_Cache_Entry_Default_Friend::getFriend($uid);
                break;
        }
    }

    public static function updateFriend($uid, $fids, $count)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Cache_Entry_Qzone_Friend::updateFriend($uid, $fids, $count);
                break;
            default:
                return Hapyfish2_Platform_Cache_Entry_Default_Friend::updateFriend($uid, $fids, $count);
                break;
        }
    }

    public static function addFriend($uid, $fids, $count)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Cache_Entry_Qzone_Friend::addFriend($uid, $fids, $count);
                break;
            default:
                return Hapyfish2_Platform_Cache_Entry_Default_Friend::addFriend($uid, $fids, $count);
                break;
        }
    }
}