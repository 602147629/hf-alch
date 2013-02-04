<?php

class Hapyfish2_Platform_Cache_UserMore
{

    public static function getUser($uid, $entryId = 0)
    {
        if ($entryId == 0 && defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Cache_Entry_Qzone_UserMore::getUser($uid);
                break;
            default:
                return Hapyfish2_Platform_Cache_Entry_Default_UserMore::getUser($uid);
                break;
        }
    }

    public static function updateUser($uid, $user, $savedb = false)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Cache_Entry_Qzone_UserMore::updateUser($uid, $user, $savedb);
                break;
            default:
                return Hapyfish2_Platform_Cache_Entry_Default_UserMore::updateUser($uid, $user, $savedb);
                break;
        }
    }

    public static function addUser($user)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Cache_Entry_Qzone_UserMore::addUser($user);
                break;
            default:
                return Hapyfish2_Platform_Cache_Entry_Default_UserMore::addUser($user);
                break;
        }
    }

    public static function getUserSessionKey($uid)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Cache_Entry_Qzone_UserMore::getUserSessionKey($uid);
                break;
            default:
                return Hapyfish2_Platform_Cache_Entry_Default_UserMore::getUserSessionKey($uid);
                break;
        }
    }

    public static function updateUserSessionKey($uid, $sessionKey, $savedb = false)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Cache_Entry_Qzone_UserMore::updateUserSessionKey($uid, $sessionKey, $savedb);
                break;
            default:
                return Hapyfish2_Platform_Cache_Entry_Default_UserMore::updateUserSessionKey($uid, $sessionKey, $savedb);
                break;
        }
    }
}