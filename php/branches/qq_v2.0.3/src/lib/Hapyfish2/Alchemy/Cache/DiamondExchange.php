<?php

class Hapyfish2_Alchemy_Cache_DiamondExchange {

    protected static $_cacheKeyPrex = 'alchemy:bas:';

    public static function getKey($word) {
        return self::$_cacheKeyPrex . $word;
    }

    public static function getBasicMC() {
        $key = 'mc_0';
        return Hapyfish2_Cache_Factory::getBasicMC($key);
    }

    public static function getDiamodExchange($level) {
        $list = array();
        $key = 'diamodExchangeInfo';
        $key = self::getKey($key);
        $cache = self::getBasicMC();
        $data = $cache->get($key);
        if ($data == false) {
            $dal = Hapyfish2_Alchemy_Dal_DiamondExchange::getDefaultInstance();
            $data = $dal->getDiamondExchange();
            if ($data) {
                foreach ($data as $t => &$info) {
                    $info['gold_change'] = json_decode($info['gold_change']);
                    $info['brova_change'] = json_decode($info['brova_change']);
                    $info['gold_diamondcost'] = json_decode($info['gold_diamondcost']);
                    $info['brova_diamondcost'] = json_decode($info['brova_diamondcost']);
                }
                $cache->set($key, $data);
            } else {
                return null;
            }
        }
        if ($data) {
            foreach ($data as $v) {
                if ($v['user_level'] == $level) {
                    $list[] = $v;
                }
            }
        }
        return $list;
    }

    public static function getUserDiamodExchange($uid) {
        $key = 'diamodExchange:' . $uid;
        $key = self::getKey($key);
        $cache = self::getBasicMC();
        $data = $cache->get($key);
        if ($data == false) {
            $dal = Hapyfish2_Alchemy_Dal_DiamondExchange::getDefaultInstance();
            $data = $dal->getUserDiamondExchange($uid);
            if ($data) {
                //判断是否大于等于当前时间 如不大于 则 重置数据
                if ($data['change_data'] < date('Ymd', mktime(0, 0, 0))) {
                    $data['goldchanger'] = 0;
                    $data['bravechanger'] = 0;
                    $cache->set($key, $data);
                    self::updateUserDiamodExchange($uid, $data);
                }
            } else {
                $data['goldchanger'] = 0;
                $data['bravechanger'] = 0;
                $data['change_data'] = date('Ymd', mktime(0, 0, 0));
                $dal->InsertUserDiamondExchange($uid, $data);
            }
        } else {
            if ($data['change_data'] < date('Ymd', mktime(0, 0, 0))) {
                $data['goldchanger'] = 0;
                $data['bravechanger'] = 0;
                $cache->set($key, $data);
                self::updateUserDiamodExchange($uid, $data);
            }
        }
        return $data;
    }

    public static function updateUserDiamodExchange($uid, $info) {
        $key = 'diamodExchange:' . $uid;
        $key = self::getKey($key);
        $cache = self::getBasicMC();
        $cache->set($key, $info);
        try {
            $dalUser = Hapyfish2_Alchemy_Dal_DiamondExchange::getDefaultInstance();
            $dalUser->updateInfo($uid, $info);
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_EverySign::updateUserEvery:' . $uid . ']' . $e->getMessage(), 'db.err');
        }
    }

    public static function updateUserDiamodExchangeSet($uid) {
        $key = 'diamodExchange:' . $uid;
        $key = self::getKey($key);
        $cache = self::getBasicMC();
        $cache->delete($key);
        $dalUser = Hapyfish2_Alchemy_Dal_DiamondExchange::getDefaultInstance();
        $info['goldchanger'] = 0;
        $info['bravechanger'] = 0;
        $info['change_data'] = 0;
        $info['uid'] = $uid;
        $dalUser->updateInfo($uid, $info);
    }

    public static function updateDiamodExchangeSet() {
        echo 123;
        $key = 'diamodExchangeInfo';
        $key = self::getKey($key);
        $cache = self::getBasicMC();
        $cache->delete($key);
    }

}