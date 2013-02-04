<?php

class Hapyfish2_Platform_Cache_User
{

    public static function getUser($uid, $entryId = 0)
    {
        if ($entryId == 0 && defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Cache_Entry_Qzone_User::getUser($uid);
                break;
            default:
                return Hapyfish2_Platform_Cache_Entry_Default_User::getUser($uid);
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
                return Hapyfish2_Platform_Cache_Entry_Qzone_User::updateUser($uid, $user, $savedb);
                break;
            default:
                return Hapyfish2_Platform_Cache_Entry_Default_User::updateUser($uid, $user, $savedb);
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
                return Hapyfish2_Platform_Cache_Entry_Qzone_User::addUser($user);
                break;
            default:
                return Hapyfish2_Platform_Cache_Entry_Default_User::addUser($user);
                break;
        }
    }

    public static function getStatus($uid)
    {
        $statusInfo = self::getStatus2($uid);
        return $statusInfo['status'];
    }

    public static function getStatus2($uid)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Cache_Entry_Qzone_User::getStatus2($uid);
                break;
            default:
                return Hapyfish2_Platform_Cache_Entry_Default_User::getStatus2($uid);
                break;
        }
    }

    public static function updateStatus($uid, $status, $savedb = true)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Cache_Entry_Qzone_User::updateStatus($uid, $status, $savedb);
                break;
            default:
                return Hapyfish2_Platform_Cache_Entry_Default_User::updateStatus($uid, $status, $savedb);
                break;
        }
    }

    public static function getVUID($uid)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Cache_Entry_Qzone_User::getVUID($uid);
                break;
            default:
                return Hapyfish2_Platform_Cache_Entry_Default_User::getVUID($uid);
                break;
        }
    }

    public static function getIdentity($uid)
    {
        if (PLATFORM == 'sinaweibo') {
            return Hapyfish2_Platform_Cache_Entry_SinaWeibo_User::getIdentity($uid);
        }
        return null;
    }

    public static function updateVUID($uid, $vuid)
    {
        $entryId = 0;
        if (defined('PLATFORM_ENTRY_ID')) {
            $entryId = PLATFORM_ENTRY_ID;
        }
        switch ($entryId) {
            case 2:
                return Hapyfish2_Platform_Cache_Entry_Qzone_User::updateVUID($uid, $vuid);
                break;
            default:
                return Hapyfish2_Platform_Cache_Entry_Default_User::updateVUID($uid, $vuid);
                break;
        }
    }

    public static function getVerified($uid)
    {
        if (PLATFORM == 'sinaweibo') {
            return Hapyfish2_Platform_Cache_Entry_SinaWeibo_User::getVerified($uid);
        }
        return null;
    }

    public static function updateVerified($uid, $data)
    {
        if (PLATFORM == 'sinaweibo') {
            return Hapyfish2_Platform_Cache_Entry_SinaWeibo_User::updateVerified($uid, $data);
        }
        return null;
    }

    public static function updateIdentity($uid, $data)
    {
        if (PLATFORM == 'sinaweibo') {
            return Hapyfish2_Platform_Cache_Entry_SinaWeibo_User::updateIdentity($uid, $data);
        }
        return null;
    }

    public static function updateOnlineTime($uid, $data)
    {
        if (PLATFORM == 'sinaweibo') {
            return Hapyfish2_Platform_Cache_Entry_SinaWeibo_User::updateOnlineTime($uid, $data);
        }
        return null;
    }

    public static function getOnlineTime($uid)
    {
        if (PLATFORM == 'sinaweibo') {
            return Hapyfish2_Platform_Cache_Entry_SinaWeibo_User::getOnlineTime($uid);
        }
        return null;
    }
}