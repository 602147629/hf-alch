<?php

class Hapyfish2_Platform_Bll_User
{

    public static function getUser($uid)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Bll_Entry_Qzone_User::getUser($uid);
                break;
            default:
                return Hapyfish2_Platform_Bll_Entry_Default_User::getUser($uid);
                break;
        }
    }

    public static function getMultiUser($fids)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Bll_Entry_Qzone_User::getMultiUser($fids);
                break;
            default:
                return Hapyfish2_Platform_Bll_Entry_Default_User::getMultiUser($fids);
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
                return Hapyfish2_Platform_Bll_Entry_Qzone_User::updateUser($uid, $user, $savedb);
                break;
            default:
                return Hapyfish2_Platform_Bll_Entry_Default_User::updateUser($uid, $user, $savedb);
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
                return Hapyfish2_Platform_Bll_Entry_Qzone_User::addUser($user);
                break;
            default:
                return Hapyfish2_Platform_Bll_Entry_Default_User::addUser($user);
                break;
        }
    }

    public static function getUids($pids)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Bll_Entry_Qzone_User::getUids($pids);
                break;
            default:
                return Hapyfish2_Platform_Bll_Entry_Default_User::getUids($pids);
                break;
        }
    }

    public static function getIdentity($uid)
    {
        if (PLATFORM == 'sinaweibo') {
            return Hapyfish2_Platform_Bll_Entry_SinaWeibo_User::getIdentity($uid);
        }
        return null;
    }

    public static function getIdentityExp($uid, $exp)
    {
        if (PLATFORM == 'sinaweibo') {
            return Hapyfish2_Platform_Bll_Entry_SinaWeibo_User::getIdentityExp($uid, $exp);
        }
        return 0;
    }
    
    public static function getYellowInfo($uid)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 1:
                return Hapyfish2_Platform_Cache_Entry_Pengyou_User::getYellowInfo($uid);
                break;
            case 2:
                return Hapyfish2_Platform_Cache_Entry_Qzone_User::getYellowInfo($uid);
                break;
            default:
                return null;
                break;
        }
    }
    
    public static function updateYellowInfo($uid, $info, $savedb = false)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 1:
                return Hapyfish2_Platform_Cache_Entry_Pengyou_User::updateYellowInfo($uid, $info, $savedb);
                break;
            case 2:
                return Hapyfish2_Platform_Cache_Entry_Qzone_User::updateYellowInfo($uid, $info, $savedb);
                break;
            default:
                break;
        }
    }
}