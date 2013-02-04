<?php

class Hapyfish2_Platform_Bll_Entry_SinaWeibo_User
{

    public static function getIdentity($uid)
    {
        $online = array();
        $userIdentity = Hapyfish2_Platform_Cache_Entry_SinaWeibo_User::getIdentity($uid);
        if ($userIdentity == 2) {
            $online['online_time'] = 0;
        } else {
            $context = Hapyfish2_Util_Context::getDefaultInstance();
            $session_key = $context->get('session_key');
            $ob = OpenApi_SinaWeibo_Client::getInstance();
            $ob->setUser($session_key);
            $online = $ob->getIdentity();
        }
        $date = date('Ymd');
        $data['date'] = $date;
        $data['onlineTime'] = $online['online_time'];
        Hapyfish2_Platform_Cache_Entry_SinaWeibo_User::updateOnlineTime($uid, $data);
        return array(
            'sinaAntiState' => $userIdentity, 
            'antiTime' => $online['online_time']
        );
    }

    public static function getIdentityExp($uid, $exp)
    {
        $onlineTime = Hapyfish2_Platform_Cache_Entry_SinaWeibo_User::getOnlineTime($uid);
        if ($onlineTime['onlineTime'] >= 18000) {
            return 0;
        }
        if ($onlineTime['onlineTime'] >= 10800) {
            return ceil($exp / 2);
        }
        return $exp;
    }
}