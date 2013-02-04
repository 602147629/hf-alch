<?php

class Hapyfish2_Alchemy_Cache_ChristmasEvents {

    protected static $_cacheKeyPrex = 'alchemy:bas:';

    public static function getKey($word) {
        return self::$_cacheKeyPrex . $word;
    }

    public static function getBasicMC() {
        $key = 'mc_0';
        return Hapyfish2_Cache_Factory::getBasicMC($key);
    }

    /**
     *   用户 祝福缓存
     * @param type $uid
     * @return null
     */
    public static function getUserBlessing($uid, $blesstime) {
        $list = array();
        $key = 'userBlessing:' . $uid;
        $key = self::getKey($key);
        $cache = self::getBasicMC();
        $info = $cache->get($key);
        $dal = Hapyfish2_Alchemy_Dal_ChristmasEvents::getDefaultInstance();
        if ($info == false) {
            $info = $dal->getUserByBless($uid, $blesstime);
            if ($info) {
                // 判断时间  数据库存储时间 如小于 现在时间 则重置好友数据
                if ($info['bless_time'] < $blesstime) {
                    $info['bless_time'] = $blesstime;
                    $info['bless_count'] = 0;
                    $info['fid'] = '';
                    //$info['uid'] = $uid;
                } else {
                    $info['bless_time'] = $info['bless_time'];
                    $info['bless_count'] = count(json_decode($info['fid']));
                    $info['fid'] = json_decode($info['fid']);
                    //$info['uid'] = $uid;
                }
                $cache->set($key, $info);
            } else {
                //如数据库查询不到 则插入数据   更新缓存
                $info['bless_time'] = $blesstime;
                $info['bless_count'] = 0;
                $info['fid'] = '';
                //$info['uid'] = $uid;
                $dal->InsertUserBless($uid, $info);
                $cache->set($key, $info);
            }
        } else {
            if ($info['bless_time'] < $blesstime) {
                $info['bless_time'] = $blesstime;
                $info['bless_count'] = 0;
                $info['fid'] = '';
                //$info['uid'] = $uid;
                $cache->set($key, $info);
                $dal->updateInfo($uid, $info);
            }
            if($info['bless_time'] > $blesstime){
                $info['bless_time'] = $blesstime;
                $cache->set($key, $info);
                $dal->updateInfo($uid, $info);
            }
        }
        return $info;
    }

    /**
     * 根据 缓存 修改 数据库
     * 
     */
    public static function updateUserBless($uid, $blessinfo) {
        $key = 'userBlessing:' . $uid;
        $key = self::getKey($key);
        $cache = self::getBasicMC();
        $data = $cache->get($key);
        $dal = Hapyfish2_Alchemy_Dal_ChristmasEvents::getDefaultInstance();
        $cache->set($key, $blessinfo);
        try {
            $dalUser = Hapyfish2_Alchemy_Dal_ChristmasEvents::getDefaultInstance();
            unset($blessinfo['uid']);
            if (count($blessinfo['fid']) > 0) {
                $blessinfo['fid'] = json_encode($blessinfo['fid']);
            } else {
                $blessinfo['fid'] = '';
            }
            $dalUser->updateInfo($uid, $blessinfo);
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_Cache_ChristmasEvents::updateUserBless:' . $uid . ']' . $e->getMessage(), 'db.err');
        }
    }

    /**
     *  连续登录 获取
     * logTime   int  当前时间
     */
    public static function getUseChistmasLogin($uid, $loginTime) {
        $key = 'userChistmasLogin:' . $uid;
        $key = self::getKey($key);
        $cache = self::getBasicMC();
        $info = $cache->get($key);
        $dal = Hapyfish2_Alchemy_Dal_ChristmasEvents::getDefaultInstance();
        if ($info == null) {
            $info = $dal->getUserChistmasLogin($uid);
            if ($info) {
                //判断时间 如果当前时间减去数据库存储时间等于1的话 则用户为连续登陆 次数加1  否则就为0
                //判断当前时间 和 数据库存储时间 的时间差为0 则 不修改数据
                if ($loginTime - $info['login_time'] == 1) {
                    $info['login_times'] +=1;
                } else if ($loginTime - $info['login_time'] > 1) {
                    $info['login_times'] = 0;
                }
                $info['login_time'] = $loginTime;
                $dal->updateChistmasLoginInfo($uid, $info);
            } else {
                $info['login_times'] = 1;
                $info['login_time'] = $loginTime;
                $dal->InsertUserChistmasLogin($uid, $info);
            }
            $cache->set($key, $info);
        } else {
            if ($loginTime - $info['login_time'] == 1) {
                $info['login_times'] +=1;
            } else if ($loginTime - $info['login_time'] > 1) {
                $info['login_times'] = 0;
            }
            $info['login_time'] = $loginTime;
            self::updateUserChistLogin($uid, $info);
        }
        return $info;
    }

    /**
     * 根据 缓存 修改 数据库
     * 
     */
    public static function updateUserChistLogin($uid, $info) {
        $key = 'userChistmasLogin:' . $uid;
        $key = self::getKey($key);
        $cache = self::getBasicMC();
        $data = $cache->get($key);
        $dal = Hapyfish2_Alchemy_Dal_ChristmasEvents::getDefaultInstance();
        $cache->set($key, $info);
        try {
            $dal = Hapyfish2_Alchemy_Dal_ChristmasEvents::getDefaultInstance();
            $dal->updateChistmasLoginInfo($uid, $info);
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_Cache_ChristmasEvents::updateUserBless:' . $uid . ']' . $e->getMessage(), 'db.err');
        }
    }

    /**
     * 玩家摇奖次数 缓存
     */
    public static function getUserChirstmasErnie($uid, $ernieTime, $sina = 0) {
        $key = 'userChistmasErnie:' . $uid;
        $key = self::getKey($key);
        $cache = self::getBasicMC();
        $data = $cache->get($key);
        $dal = Hapyfish2_Alchemy_Dal_ChristmasEvents::getDefaultInstance();
        if ($data == null) {
            $data = $dal->getUserChistmasInfo($uid);
            if ($data) {
                
            } else {
                $data['user_ernie_time'] = 0;
                $data['user_ernie_times'] = 0;
                $data['user_ernie_count'] = 3;
                $data['user_ernie_bless'] = 0;
                $data['user_sina_attention'] = $sina;
                $data['user_christmas_crystallization'] = 0;
                $dal->InsertUserChistmasErnie($uid, $data);
            }
            $cache->set($key, $data);
        }
        return $data;
    }

    /**
     * 重置 玩家 数据
     */
    public static function resetUserChristmas($uid, $ernieTime, $sina, $count, $user_christmas_crystallization) {
        $data['user_ernie_time'] = $ernieTime;
        $data['user_ernie_count'] = $count;
        $data['user_ernie_times'] = 0;
        $data['user_sina_attention'] = $sina;
        $data['user_ernie_bless'] = 0;
        $data['user_christmas_crystallization'] = $user_christmas_crystallization;
        self::updateUserChistInfo($uid, $data);
        return $data;
    }

    /**
     * 根据 缓存 修改 数据库
     * 
     */
    public static function updateUserChistInfo($uid, $info) {
        $key = 'userChistmasErnie:' . $uid;
        $key = self::getKey($key);
        $cache = self::getBasicMC();
        $data = $cache->get($key);
        $dal = Hapyfish2_Alchemy_Dal_ChristmasEvents::getDefaultInstance();
        $cache->set($key, $info);
        try {
            unset($info['user_ernie_uid']);
            $dal->updateChistmasUserInfo($uid, $info);
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_Cache_ChristmasEvents::updateUserBless:' . $uid . ']' . $e->getMessage(), 'db.err');
        }
    }

    /**
     *   接口设置时间
     * 
     */
    public static function updateUserChistmasTime($time,$uid) {
        $key = 'userChistmasTime:' . $uid;
        $key = self::getKey($key);
        $cache = self::getBasicMC();
        $cache->set($key, $time);
        $data = self::getUserChistmasTime($uid);
        return $data;
    }

    /**
     *   接口获取时间
     * 
     */
    public static function getUserChistmasTime($uid) {
        $key = 'userChistmasTime:' . $uid;
        $key = self::getKey($key);
        $cache = self::getBasicMC();
        $data = $cache->get($key);
        return $data;
    }

}