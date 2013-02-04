<?php

class Hapyfish2_Alchemy_Bll_ChristmasEvents {

    /**
     *   圣诞摇摇乐  初始化 计算摇奖次数
     * @param type $uid
     */
    public static function init($uid, $loginTime) {
        //连续登陆 奖励 
        $loginChristmasCount = self::blessContinuousLogin($uid, $loginTime);
        // 好友祝福
        $userBlessing = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserBlessing($uid, $loginTime);
        //
       
        $finaChristmas = $userBlessing['bless_count'] + $loginChristmasCount;
        return $finaChristmas + 3;
    }

    /**
     *  圣诞摇奖  数据调用  接口
     */
    public static function initData($uid, $time) {
        // 新浪微博 关注 接口
        $sina = self::initSinaAttention();
        //$sina = 0;
        $userChristmasInfo = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserChirstmasErnie($uid, $time, $sina);
        //当前时间 大于 缓存时间 则重置数据  获取获奖次数
        
        if ($time > $userChristmasInfo['user_ernie_time']) {
            $count = self::init($uid, $time);
            $count += $sina;
            $userChristmasInfo = Hapyfish2_Alchemy_Cache_ChristmasEvents::resetUserChristmas($uid, $time, $sina, $count, $userChristmasInfo['user_christmas_crystallization']);
        } else {
            //判断玩家缓存中的新浪微博是否已经关注 如果关注 或 取消关注 则加减一个次数
            if ($userChristmasInfo['user_sina_attention'] != $sina) {
                $userChristmasInfo['user_sina_attention'] = $sina;
                if ($sina > 0) {
                    $userChristmasInfo['user_ernie_count'] +=1;
                } else {
                    $userChristmasInfo['user_ernie_count'] -=1;
                }
                Hapyfish2_Alchemy_Cache_ChristmasEvents::updateUserChistInfo($uid, $userChristmasInfo);
            }
        }
    }

    /**
     *   圣诞摇奖  玩家数据 初始
     */
    public static function ChristmasInit($uid, $time) {
        $sina = self::initSinaAttention();
        $data = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserChistmasTime($uid);
        if (!empty($data)) {
            $time = $data;
        }
        if($time >= 1356624000){
            return -1304;
        }
        //$sina = 0;
        // 初始化 玩家数据 
        self::initData($uid, $time);
        $userChristmasInfo = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserChirstmasErnie($uid, $time);
        $loginChristmasCount = self::blessContinuousLogin($uid, $time);
        //$userIsBless = $userChristmasBless['bless_count'] > 3 ? 0 : 1;
        $freindList = array();
        $fids = Hapyfish2_Platform_Bll_Friend::getFriendIds($uid);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'test', $userChristmasInfo);
        array_push($fids, $uid);
        if (count($fids) > 0) {
            foreach ($fids as $k => $v) {
                $info = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserBlessing($v, $time);
                $freindList[$k][0] = $v;
                $blessFriend = $info['fid'];
                if (count($blessFriend) > 0) {
                    if (in_array($uid, $blessFriend)) {
                        $isZhufu = 0;
                    } else {
                        $isZhufu = 1;
                    }
                } else {
                    $isZhufu = 1;
                }
                $freindList[$k][1] = $isZhufu;
                $freindList[$k][2] = 3 - $info['bless_count'];
                unset($v);
            }
        } else {
            $info = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserBlessing($uid, $time);
            $freindList[0][0] = $uid;
            $blessFriend = $info['fid'];
            if (count($blessFriend) > 0) {
                if (in_array($uid, $blessFriend)) {
                    $isZhufu = 0;
                } else {
                    $isZhufu = 1;
                }
            } else {
                $isZhufu = 1;
            }
            $freindList[0][1] = $isZhufu;
            $freindList[0][2] = 3 - $info['bless_count'];
        }
        $ChristmasCommonVo['currentCrystallizeNum'] = $userChristmasInfo['user_christmas_crystallization'];
        $ChristmasCommonVo['freindList'] = $freindList;
        $ChristmasCommonVo['isFans'] = $sina;
        $ChristmasCommonVo['continuousLandingDay'] = $loginChristmasCount;
        $ChristmasCommonVo['getAwardNum'] = $userChristmasInfo['user_ernie_count'] - $userChristmasInfo['user_ernie_times'];
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'christmasCommonVo', $ChristmasCommonVo);
    }

    /**
     *
     *  圣诞摇摇乐 活动 调用新浪微博 游戏微博 关注接口
     */
    public static function initSinaAttention() {
        $context = Hapyfish2_Util_Context::getDefaultInstance();
        $sessionKey = $context->get('session_key');
        $rest = OpenApi_SinaWeibo_Client::getInstance();
        $rest->setUser($sessionKey);
        $data = $rest->isFans();
        if ($data != 1) {
            return 0;
        } else {
            return 1;
        }
    }

    /**
     *   祝福接口  
     */
    public static function ChristmasBlessing($uid, $fid, $time) {
        $sina = self::initSinaAttention();
        //$sina = 0;
        $data = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserChistmasTime($uid);
        if (!empty($data)) {
            $time = $data;
        }
        $userBlessing = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserBlessing($fid, $time);
        // 判断次数是否小于3次  如小于3次 则更新缓存和数据库
        if ($userBlessing['bless_count'] <= 2) {
            //判断该祝福的好友是否已经祝福过了
            $blessFriend = $userBlessing['fid'];
            if (in_array($uid, $blessFriend)) {
                return -1300;  //好友已经祝福
            } else {
                if (is_array($blessFriend)) {
                    array_push($blessFriend, $uid);
                } else {
                    $blessFriend[] = $uid;
                }
                $userBlessing['bless_count']+=1;
                $userBlessing['fid'] = $blessFriend;
                $userBlessing['bless_time'] = $time;
            }
            //祝福的人 增加5k 金币
            $userChristmasInfoUid = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserChirstmasErnie($uid, $time, $sina);
            if($userChristmasInfoUid['user_ernie_bless'] <=2){
                Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, 5000);
            }
            // 更新数据库 和 缓存
            Hapyfish2_Alchemy_Cache_ChristmasEvents::updateUserBless($fid, $userBlessing);
            //更新玩家摇奖数据库   添加 玩家访问好友次数 如超过3次  不加金币
            $userChristmasInfo = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserChirstmasErnie($fid, $time, $sina);
            $userChristmasInfo['user_ernie_count']+=1;
            $userChristmasInfoUid = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserChirstmasErnie($uid, $time, $sina);
            $userChristmasInfoUid['user_ernie_bless']+=1;
            Hapyfish2_Alchemy_Cache_ChristmasEvents::updateUserChistInfo($uid, $userChristmasInfoUid);
            //return $userBlessing['bless_count'];
        } else {
            return -1301; //该好友已经达到最高祝福次数
        }
    }

    /**
     * 连续登陆 计算
     */
    public static function blessContinuousLogin($uid, $loginTime) {
        $userContinuousLogin = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUseChistmasLogin($uid, $loginTime);
        return $userContinuousLogin['login_times'];
    }

    /**
     *   圣诞摇摇乐  圣诞结晶  获取
     */
    public static function ChristmasChange($uid, $time) {
        $data = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserChistmasTime();
        if (!empty($data)) {
            $time = $data;
        }
        $userChristmasInfo = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserChirstmasErnie($uid, $time, $sina);
        // 判断玩家剩余次数
        if ($userChristmasInfo['user_ernie_count'] - $userChristmasInfo['user_ernie_times'] > 0) {
            //根据随机数 获取 获取圣诞结晶 个数
            $randChristmas = mt_rand(1, 100);
            if ($randChristmas > 0 && $randChristmas <= 50) {
                $christmas = 8;
            } else if ($randChristmas >= 51 && $randChristmas <= 60) {
                $christmas = 6;
            } else if ($randChristmas >= 61 && $randChristmas <= 70) {
                $christmas = 7;
            } else if ($randChristmas >= 71 && $randChristmas <= 80) {
                $christmas = 5;
            } else if ($randChristmas >= 81 && $randChristmas <= 90) {
                $christmas = 9;
            } else if ($randChristmas >= 91) {
                $christmas = 10;
            }
            //增加结晶个数 更新缓存和数据库
            $userChristmasInfo['user_christmas_crystallization'] += $christmas;
            $userChristmasInfo['user_ernie_times']+=1;
            Hapyfish2_Alchemy_Cache_ChristmasEvents::updateUserChistInfo($uid, $userChristmasInfo);
            Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'num', $christmas);
            $log = Hapyfish2_Util_Log::getInstance();
            $log->report('ChristmasChange', array($uid, $christmas));
        } else {
            return -1303; //超过摇奖次数
        }
    }

    /**
     * 圣诞摇摇乐 结晶兑换  奖品
     */
    public static function ChristmasGetAward($uid, $awardType, $time) {
        $data = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserChistmasTime();
        if (!empty($data)) {
            $time = $data;
        }
        $award = array('[9215,50]', '[9415,50]', '[8115,20]', '[3001115,100]', '[2715,15]', '[3815,50]', '[3915,100]', '[4015,200]');
        $awardPlus = $award[$awardType - 1];
        if (!empty($awardPlus)) {
            $userChristmasInfo = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserChirstmasErnie($uid, $time);
            $awardPlusInfo = json_decode($awardPlus, true);
            if ($awardPlusInfo[1] <= $userChristmasInfo['user_christmas_crystallization']) {
                $userChristmasInfo['user_christmas_crystallization'] -= $awardPlusInfo[1];
                Hapyfish2_Alchemy_Cache_ChristmasEvents::updateUserChistInfo($uid, $userChristmasInfo);
                Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $awardPlusInfo[0], 1);
                $log = Hapyfish2_Util_Log::getInstance();
                $log->report('ChristmasAward', array($uid, $awardPlusInfo[1]));
            } else {
                return -1302;  // 结晶不够
            }
        }
    }

    /**
     * 圣诞摇摇乐 结晶接口
     */
    public static function Christmas($uid, $time) {
        $userChristmasInfo = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserChirstmasErnie($uid, $time);
        $userChristmasInfo['user_christmas_crystallization'] += 100;
        Hapyfish2_Alchemy_Cache_ChristmasEvents::updateUserChistInfo($uid, $userChristmasInfo);
        $userChristmasInfo = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserChirstmasErnie($uid, $time);
        print_r($userChristmasInfo);
    }

    /**
     * 圣诞摇摇乐  清除玩家摇奖接口
     */
    public static function ChristmasUserCount($uid, $time) {
        $userChristmasInfo = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserChirstmasErnie($uid, $time);
        $userChristmasInfo['user_ernie_times'] = 0;
        $userChristmasInfo['user_ernie_time'] = 0;
        $userChristmasInfo['user_ernie_count'] = 3;
        $userChristmasInfo['user_ernie_bless'] = 0;
        print_r($userChristmasInfo);
        Hapyfish2_Alchemy_Cache_ChristmasEvents::updateUserChistInfo($uid, $userChristmasInfo);
        $userChristmasInfo = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserChirstmasErnie($uid, $time);
        print_r($userChristmasInfo);
    }

    /**
     * 圣诞摇摇乐  清除好友祝福接口
     */
    public static function ChristmasUserFidCount($uid, $time) {
        $userBlessing = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserBlessing($uid, $time);
        print_r($userBlessing);
        unset($userBlessing['uid']);
        $userBlessing['bless_count'] = 0;
        $userBlessing['bless_time'] = 0;
        $userBlessing['fid'] = '';
        Hapyfish2_Alchemy_Cache_ChristmasEvents::updateUserBless($uid, $userBlessing);
        $userBlessing1 = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUserBlessing($uid, $time);
        print_r($userBlessing1);
    }

    /**
     * 圣诞摇摇乐  连续登陆次数
     */
    public static function ChristmasUserLoginCount($uid, $time) {
        $userContinuousLogin = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUseChistmasLogin($uid, $time);
        unset($userContinuousLogin['uid']);
        $userContinuousLogin['login_times'] +=1;
        print_r($userContinuousLogin);
        Hapyfish2_Alchemy_Cache_ChristmasEvents::updateUserChistLogin($uid, $userContinuousLogin);
        $userContinuousLogin = Hapyfish2_Alchemy_Cache_ChristmasEvents::getUseChistmasLogin($uid, $time);
        print_r($userContinuousLogin);
    }

}
