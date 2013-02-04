<?php

class Hapyfish2_Platform_Bll_Entry_Pengyou_User
{
    
    public static function updateYellowInfo($uid, $info, $savedb = false)
    {
        //get
        $old = Hapyfish2_Platform_Cache_Entry_Pengyou_User::getUser($uid);
        if ($old) {
            if ($old['is_yellow_vip'] == $info['is_yellow_vip'] && $old['is_yellow_year_vip'] == $info['is_yellow_year_vip'] && $old['yellow_vip_level'] == $info['yellow_vip_level']) {
                return false;
            }
        }
        $res = Hapyfish2_Platform_Cache_Entry_Pengyou_User::updateYellowInfo($uid, $info, $savedb);
        return $res;
    }
}