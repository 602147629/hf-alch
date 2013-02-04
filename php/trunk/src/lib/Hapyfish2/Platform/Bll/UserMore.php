<?php

class Hapyfish2_Platform_Bll_UserMore
{

    public static function getInfo($uid)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Bll_Entry_Qzone_UserMore::getInfo($uid);
                break;
            default:
                return Hapyfish2_Platform_Bll_Entry_Default_UserMore::getInfo($uid);
                break;
        }
    }

    public static function updateInfo($uid, $user, $savedb = false)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Bll_Entry_Qzone_UserMore::updateInfo($uid, $user, $savedb);
                break;
            default:
                return Hapyfish2_Platform_Bll_Entry_Default_UserMore::updateInfo($uid, $user, $savedb);
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
                return Hapyfish2_Platform_Bll_Entry_Qzone_UserMore::addUser($user);
                break;
            default:
                return Hapyfish2_Platform_Bll_Entry_Default_UserMore::addUser($user);
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
                return Hapyfish2_Platform_Bll_Entry_Qzone_UserMore::updateUserSessionKey($uid, $sessionKey, $savedb);
                break;
            default:
                return Hapyfish2_Platform_Bll_Entry_Default_UserMore::updateUserSessionKey($uid, $sessionKey, $savedb);
                break;
        }
    }
}