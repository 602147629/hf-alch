<?php

/**
 *  每日签到
 */
class Hapyfish2_Alchemy_HFC_EverySign {

    public static function getUserEveryInfo($uid) {
        $key = 'a:u:everyinfo:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        $EverySign = $cache->get($key);
        if ($EverySign == false) {
            $EverySign = self::loadUserEveryInfo($uid, $key);
            if ($EverySign == false) {
                return null;
            }
        }
        return $EverySign;
    }

    public static function getUserEveryActieInfo($uid, $type) {
        $key = 'a:u:everyactiveaward';
        $cache = Hapyfish2_Cache_Factory::getHFC(0);
        $EveryActieInfo = $cache->get($key);
        if ($EveryActieInfo == false) {
            $EveryActieInfo = self::loadEveryActieInfo($uid, $key);
            if ($EveryActieInfo == false) {
                return null;
            }
        }
        foreach ($EveryActieInfo as $v) {
            if ($v['id'] == $type) {
                return $v;
            }
        }
    }

    /**
     *  每日签到 每日趣闻 
     * @return null
     */
    public static function getUserEveryNoticeInfo() {
        $key = 'a:u:everynotice';
        $cache = Hapyfish2_Cache_Factory::getHFC(0);
        $EveryNoticeInfo = $cache->get($key);
        if ($EveryNoticeInfo == false) {
            $EveryNoticeInfo = self::loadEveryNoticeInfo();
            if ($EveryNoticeInfo == false) {
                return null;
            }
        }
        return $EveryNoticeInfo;
    }

    public static function loadEveryNoticeInfo() {
        try {
            $dalEveryNotice = Hapyfish2_Alchemy_Dal_EverySign::getDefaultInstance();
            $dalEveryNoticeData = $dalEveryNotice->getEveryNotice();
            return $dalEveryNoticeData;
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_EverySign::loadEveryNoticeInfo:]' . $e->getMessage(), 'db.err');
        }
        return false;
    }

    public static function getUserEveryActielaba($uid, $day) {
        $key = 'a:u:everylabaaward';
        $cache = Hapyfish2_Cache_Factory::getHFC(0);
        $cache->delete($key);
        $everyLabaInfo = $cache->get($key);
        if ($everyLabaInfo == false) {
            $everyLabaInfo = self::loadEveryLabaInfo($uid, $key);
            if ($everyLabaInfo == false) {
                return null;
            }
        }
        foreach ($everyLabaInfo as $v) {
            if ($v['day'] == $day) {
                return $v;
            }
        }
    }

    public static function loadEveryLabaInfo($uid, $key) {
        try {
            $dalEveryLaba = Hapyfish2_Alchemy_Dal_EverySign::getDefaultInstance();
            $dalEveryInfoAwardNew = $dalEveryLaba->getEvertLabaAward();
            return $dalEveryInfoAwardNew;
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_EverySign::loadUserEveryInfo:' . $uid . ']' . $e->getMessage(), 'db.err');
        }
        return false;
    }

    public static function loadEveryActieInfo($uid, $key) {
        try {
            $dalEveryInfoAward = Hapyfish2_Alchemy_Dal_EverySign::getDefaultInstance();
            $dalEveryInfoAwardNew = $dalEveryInfoAward->getEvertAward($uid);
            return $dalEveryInfoAwardNew;
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_EverySign::loadUserEveryInfo:' . $uid . ']' . $e->getMessage(), 'db.err');
        }
        return false;
    }

    public static function loadUserEveryInfo($uid, $key) {
        try {
            $dalUserEveryInfo = Hapyfish2_Alchemy_Dal_EverySign::getDefaultInstance();
            $dalUserInfo = $dalUserEveryInfo->getUserInfo($uid);
            if ($dalUserInfo == false || count($dalUserInfo) == 0) {
                //数据库 无 用户数据  则默认
                $info['every_day'] = array('0', '0', '0', '0', '0', '0', '0');
                $info['award'] = array('0', '0', '0', '0', '0', '0', '0');
                $info['sign_time'] = 0;
                $info['award_five_sign'] = 0;
                $info['award_seven_sign'] = 0;
                $cache = Hapyfish2_Cache_Factory::getHFC($uid);
                $cache->add($key, $info);
                $dalUserEveryInfo->InsertUserEvery($uid, $info);
            } else {
                $info['every_day'] = json_decode($dalUserInfo[0]['every_day']);
                $info['award'] = json_decode($dalUserInfo[0]['award']);
                $info['sign_time'] = $dalUserInfo[0]['sign_time'];
                $info['award_five_sign'] = $dalUserInfo[0]['award_five_sign'];
                $info['award_seven_sign'] = $dalUserInfo[0]['award_seven_sign'];
            }
            return $info;
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_EverySign::loadUserEveryInfo:' . $uid . ']' . $e->getMessage(), 'db.err');
        }
        return false;
    }

    public static function updateUserEvery($uid, $info, $savedb = false) {
        $key = 'a:u:everyinfo:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $ok = $cache->save($key, $info);
        if ($ok) {
            try {
                $keyInfo = array('every_day' => json_encode($info['every_day']), 'award' => json_encode($info['award']), 'sign_time' => $info['sign_time'], 'award_five_sign' => $info['award_five_sign'], 'award_seven_sign' => $info['award_seven_sign']);
                $dalUser = Hapyfish2_Alchemy_Dal_EverySign::getDefaultInstance();
                $dalUser->update($uid, $keyInfo);
            } catch (Exception $e) {
                info_log('[Hapyfish2_Alchemy_HFC_EverySign::updateUserEvery:' . $uid . ']' . $e->getMessage(), 'db.err');
            }
        }
        return $ok;
    }

}
