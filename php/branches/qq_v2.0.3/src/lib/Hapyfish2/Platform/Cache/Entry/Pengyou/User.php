<?php

class Hapyfish2_Platform_Cache_Entry_Pengyou_User
{

    public static function getYellowInfo($uid)
    {
    	$key = 'p:u:ylv:' . $uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $result = $cache->get($key);
        if ($result === false) {
            if ($cache->isNotFound()) {
                try {
                    $dalUser = Hapyfish2_Platform_Dal_Entry_Pengyou_User::getDefaultInstance();
                    $result = $dalUser->getYellowInfo($uid);
                    if ($result) {
                        $cache->add($key, $result);
                    } else {
                        return array(
                            'is_yellow_vip' => 0, 
                            'is_yellow_year_vip' => 0, 
                            'yellow_vip_level' => 0
                        );
                    }
                }
                catch (Exception $e) {
                    info_log('[Hapyfish2_Platform_Cache_Entry_Pengyou_User::getYellowInfo]:' . $e->getMessage(), 'db.err');
                    return null;
                }
            } else {
                return null;
            }
        }
        return array(
            'is_yellow_vip' => $result[0], 
            'is_yellow_year_vip' => $result[1], 
            'yellow_vip_level' => $result[2]
        );
    }
   
    public static function updateYellowInfo($uid, $info, $savedb = false)
    {
        $key = 'p:u:ylv:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = array(
        	$info['is_yellow_vip'], 
            $info['is_yellow_year_vip'], 
            $info['yellow_vip_level']
        );
        if (! $savedb) {
            $savedb = $cache->canSaveToDB($key, 3600);
        }
        if ($savedb) {
            $ok = $cache->save($key, $data);
            if ($ok) {
                try {
                    $datainfo = array(
                        'is_yellow_vip' => $info['is_yellow_vip'], 
                        'is_yellow_year_vip' => $info['is_yellow_year_vip'], 
                        'yellow_vip_level' => $info['yellow_vip_level']
                    );
                    $dalUser = Hapyfish2_Platform_Dal_Entry_Pengyou_User::getDefaultInstance();
                    $dalUser->update($uid, $datainfo);
                }
                catch (Exception $e) {
                    info_log('[Hapyfish2_Platform_Cache_Entry_Pengyou_User::updateYellowInfo]:' . $e->getMessage(), 'db.err');
                }
            } else {
                $ok = $cache->update($key, $data);
            }
        }
        return $ok;
    }
}