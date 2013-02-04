<?php

class Hapyfish2_Alchemy_Bll_EverySign {

    /**
     *   每日签到 初始化 接口 
     * @param type $uid
     */
    public static function init($uid) {
        $info = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryInfo($uid);
        $notice = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryNoticeInfo();
        if ($info['update_time'] > 0) {
            $time = $info['update_time'];
        } else {
            $time = mktime(0, 0, 0);
        }
        $week = date('w', $time);
        if ($week == 0) {
            $week = 7;
        }
        $oneday = ($time + ((7 - $week) * 86400)) - 6 * 86400;
        //判断时间 如果最后签到时间 大于等于 这个礼拜一 则不重置数据  否则重置 玩家 签到的 数据  更新缓存后 写入数据库
        if ($info['sign_time'] >= $oneday) {
            
        } else {
            $info['every_day'] = array('0', '0', '0', '0', '0', '0', '0');
            $info['award'] = array('0', '0', '0', '0', '0', '0', '0');
            $info['sign_time'] = $info['sign_time'];
            $info['award_five_sign'] = 0;
            $info['award_seven_sign'] = 0;
            $info['update_time'] = 0;
            Hapyfish2_Alchemy_HFC_EverySign::updateUserEvery($uid, $info);
        }
        foreach ($info['every_day'] as $k => $v) {
            $everyDaySignInVo[$k] = array('day' => $k + 1, 'isGetAward' => $v);
            $finaSignDay +=$v;
        }
        if ($finaSignDay == 7) {
            if ($info['award_seven_sign'] == 1) {
                $seven_sign = 0;
            } else {
                $seven_sign = 1;
            }
            if ($info['award_five_sign'] == 1) {
                $five_sign = 0;
            } else {
                $five_sign = 1;
            }
        } else if ($finaSignDay <= 6 && $finaSignDay >= 5) {
            if ($info['award_five_sign'] == 1) {
                $five_sign = 0;
            } else {
                $five_sign = 1;
            }
        }  else{
            $five_sign = 0;$seven_sign = 0;
        }
        $everyDaySignInCommonVo['currentDay'] = $week;
        if ($info['award'][$week - 1] == 0 && $info['every_day'][$week - 1] == 1) {
            $getaward = 1;
        } else {
            $getaward = 0;
        }
        $everyDaySignInCommonVo['getawardnum'] = $getaward;
        $everyDaySignInCommonVo['signDay'] = $finaSignDay;
        $everyDaySignInCommonVo['isFiveBox'] = $five_sign;
        $everyDaySignInCommonVo['isSevenBox'] = $seven_sign;
        if (count($notice) > 0) {
            foreach ($notice as $k => $v) {
                $everyDaySignInCommonVo['word' . ($k + 1)] = $v['sign_notice'];
            }
        }

        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'test', $info);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'everyDaySignInVo', $everyDaySignInVo);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'everyDaySignInCommonVo', $everyDaySignInCommonVo);
    }

    /**
     *   获取每日签到 拉霸 奖励
     * @param type $uid
     */
    public static function labaAward($uid) {  //获取拉霸奖励
        if ($info['update_time'] > 0) {
            $time = $info['update_time'];
        } else {
            $time = mktime(0, 0, 0);
        }
        $week = date('w', $time);
        if ($week == 0) {
            $week = 7;
        }
        $i = 3;
        $labaAward = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryActielaba($uid, $week);
        $userEverySign = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryInfo($uid);
        //随机抽奖奖励
        $array = array('normal_award', 'vip_frist_award', 'vip_second_award');
        for ($a = 1; $a <= $i; $a++) {
            $award = json_decode($labaAward[$array[$a - 1]]);
            foreach ($award as $k => $v) {
                switch ($v['1']) {
                    case '1' : $type = 'coin';
                        break;
                    case '2' :$type = 'gem';
                        break;
                    case '3';
                        $type = $v[0];
                        break;
                }
                $labaAwardNew[] = array('id' => $k + 1, 'cid' => $type, 'num' => $v[2], 'type' => $a);
            }
        }
//        $userEverySign['award'][$week - 1] = $labaAwardNewTemp;
//        $ok = Hapyfish2_Alchemy_HFC_EverySign::updateUserEvery($uid, $userEverySign);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'everyDaySignInStaticVo', $labaAwardNew);
    }

    /**
     *   领取拉霸奖励
     * @param type $uid
     * @return type
     */
    public static function getAwardLaBa($uid) {
        $info = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryInfo($uid);
        if ($info['update_time'] > 0) {
            $time = $info['update_time'];
        } else {
            $time = mktime(0, 0, 0);
        }
        $week = date('w', $time);
        if ($week == 0) {
            $week = 7;
        }
        $userEverySign = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryInfo($uid);
        $labaAward = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryActielaba($uid, $week);
        if ($userEverySign['award'][$week - 1] == 0) {
            $array = array('normal_award', 'vip_frist_award', 'vip_second_award');
            //获取玩家是否为vip1 和 vip3  如果是 则 多获取奖励
            $vip = new Hapyfish2_Alchemy_Bll_Vip();
            $vipInfo = $vip->getVipInfo($uid);
            $i = 1;
            if ($vipInfo['level'] >= 1 && $vipInfo['vipStatus'] == 1) {
                $i++;
                if ($vipInfo['level'] >= 3) {
                    $i++;
                }
            }
            for ($a = 1; $a <= $i; $a++) {
                $award = json_decode($labaAward[$array[$a - 1]]);
                $randNumber = array_rand($award);
                $LabaAwardNmber = $randNumber + 1;
                $awardResult = $award[$randNumber];
                if ($awardResult[1] == 1) {
                    if ($awardResult[2] > 0) {
                        Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $awardResult[2],24);
                    }
                } else if ($awardResult[1] == 2) {
                    if ($awardResult[2] > 0) {
                        $gemInfo = array('gem' => $awardResult[2]);
                        Hapyfish2_Alchemy_Bll_Gem::add($uid, $gemInfo,12);
                        Hapyfish2_Alchemy_Bll_UserResult::mergeGem($uid, $awardResult[2]);
                    }
                } else if ($awardResult[1] == 3) {
                    if ($awardResult[2] > 0) {
                        $itemType = substr($awardResult[0], -2, 1);
                        if ($itemType == 6) {
                            Hapyfish2_Alchemy_Bll_EventGift::addUserWeapon($uid, $awardResult[0], $awardResult[3]);
                        } else {
                            Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $awardResult[0], $awardResult[2]);
                        }
                    }
                }
                $dayArray[] = $LabaAwardNmber;
            }
            $array_sign = $userEverySign['award'];
            foreach ($array_sign as $k => $v) {
                if ($k == $week - 1) {
                    $tempSignArray[$k] = 1;
                } else {
                    $tempSignArray[$k] = $v;
                }
            }
        } else {
            return -1101;
        }

        $info['every_day'] = $userEverySign['every_day'];
        $info['award'] = $tempSignArray;
        $info['sign_time'] = $time;
        $info['award_five_sign'] = $userEverySign['award_five_sign'];
        $info['award_seven_sign'] = $userEverySign['award_seven_sign'];
        $info['update_time'] = $userEverySign['update_time'];
        Hapyfish2_Alchemy_HFC_EverySign::updateUserEvery($uid, $info);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'id', $dayArray);
    }

    /**
     *   每日签到 接口
     * @param type $uid
     */
    public static function signDay($uid) {  //签到接口
        $userEverySign = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryInfo($uid);
        if ($userEverySign['update_time'] > 0) {
            $time = $userEverySign['update_time'];
        } else {
            $time = mktime(0, 0, 0);
        }
        $day = date('w', $time);
        if ($day == 0) {
            $day = 7;
        }
        $signToday = $day - 1;
        $userEverySign = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryInfo($uid);
        if ($userEverySign) {
            $array_sign = $userEverySign['every_day'];
            foreach ($array_sign as $k => $v) {
                if ($k == $signToday) {
                    $tempSignArray[$k] = 1;
                } else {
                    $tempSignArray[$k] = $v;
                }
            }
            $info['every_day'] = $tempSignArray;
            $info['award'] = $userEverySign['award'];
            $info['sign_time'] = $time;
            $info['award_five_sign'] = $userEverySign['award_five_sign'];
            $info['award_seven_sign'] = $userEverySign['award_seven_sign'];
            $info['update_time'] = $userEverySign['update_time'];
            Hapyfish2_Alchemy_HFC_EverySign::updateUserEvery($uid, $info);
            $log = Hapyfish2_Util_Log::getInstance();
            $log->report('signDay', array($uid, 1));
        }
    }

    /**
     *  每日签到 补签接口
     * @param int $day  为 补签日期
     * @param type $uid
     * @return type
     */
    public static function signRetroactive($day, $uid) {
        $day = $day - 1;
        $userEverySign = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryInfo($uid);
        if ($userEverySign['update_time'] > 0) {
            $time = $userEverySign['update_time'];
        } else {
            $time = mktime(0, 0, 0);
        }
        $userEverySign = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryInfo($uid);
        if ($userEverySign) {
            $userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
            if ($userGem < 20) {
                 return -206;
            }
            $array_sign = $userEverySign['every_day'];
            foreach ($array_sign as $k => $v) {
                if ($k == $day) {
                    $tempSignArray[$k] = 1;
                } else {
                    $tempSignArray[$k] = $v;
                }
            }
            $info['every_day'] = $tempSignArray;
            $info['award'] = $userEverySign['award'];
            $info['sign_time'] = $time;
            $info['award_five_sign'] = $userEverySign['award_five_sign'];
            $info['award_seven_sign'] = $userEverySign['award_seven_sign'];
            $info['update_time'] = $userEverySign['update_time'];
            Hapyfish2_Alchemy_HFC_EverySign::updateUserEvery($uid, $info);
            $cost['cost'] = 20;
            $ok2 = Hapyfish2_Alchemy_Bll_Gem::consume($uid, $cost);
            if (!$ok2) {
                info_log('signRetroactive-consume:failed:' . $uid, 'Bll_Training');
                return -200;
            }
            $log = Hapyfish2_Util_Log::getInstance();
            $log->report('signRetroactive', array($uid, 1));
        }
    }
    
    public static function signQQRetroactive($day, $uid) 
    {
        $day = $day - 1;
        $userEverySign = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryInfo($uid);
        if ($userEverySign['update_time'] > 0) {
            $time = $userEverySign['update_time'];
        } else {
            $time = mktime(0, 0, 0);
        }
        $userEverySign = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryInfo($uid);
        $needGem = 20;
        if ($userEverySign) {
        	$goodsInfo = array();
	    	$goodsInfo['goodsmeta'] = '签到补签*签到补签';
	    	$goodsInfo['goodsurl'] = STATIC_HOST.'/alchemy/image/item/qpoint.jpg';
	    	$goodsInfo['payitem'] = 'p015*'.$needGem.'*1';
			$token = Hapyfish2_Platform_Bll_QqpayByToken::getToken($uid,$goodsInfo);
			if($token){
				Hapyfish2_Alchemy_Bll_UserResult::setYellow($uid);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'payJs', $token['url_params']);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'token', $token['token']);
				$payNeedData = array('day'=>$day);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'payNeedData', $payNeedData);
			}
           return 1;
        }
    }

    /**
     * 5天  7天 活跃奖励
     * @param type $type  1为5天  2为7天
     * @param type $uid
     */
    public static function signActiveAward($type, $uid) {
        $everyActiveAward = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryActieInfo($uid, $type);
        $awardResult = json_decode($everyActiveAward['award']);
        foreach ($awardResult as $k => $v) {
            if ($v[1] == 1) {
                if ($v[2] > 0) {
                    Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $v[2],25);
                }
            } else if ($v[1] == 2) {
                if ($v[2] > 0) {
                    $gemInfo = array('gem' => $v[2]);
                    Hapyfish2_Alchemy_Bll_Gem::add($uid, $gemInfo,13);
                    Hapyfish2_Alchemy_Bll_UserResult::mergeGem($uid, $v[2],13);
                }
            } else if ($v[1] == 3) {
                if ($v[2] > 0) {
                    $itemType = substr($v[0], -2, 1);
                    if ($itemType == 6) {
                        Hapyfish2_Alchemy_Bll_EventGift::addUserWeapon($uid, $v[0], $v[3]);
                    } else {
                        Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $v[0], $v[2]);
                    }
                }
            }
        }
        $userEverySign = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryInfo($uid);
        if ($type == 1) {
            $userEverySign['award_five_sign'] = 1;
        } else if ($type == 2) {
            $userEverySign['award_seven_sign'] = 1;
        }
        Hapyfish2_Alchemy_HFC_EverySign::updateUserEvery($uid, $userEverySign);
        $log = Hapyfish2_Util_Log::getInstance();
        $log->report('signActiveAward', array($uid, $type));
    }

    /**
     *   重置玩家数据
     */
    public static function signActiveRest($uid) {
        $userEverySign = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryInfo($uid);
        print_r($userEverySign);
        $everyActiveAward = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryActieInfo($uid, 1);
        print_r($everyActiveAward);
        $everyActiveAward1 = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryActieInfo($uid, 2);
        print_r($everyActiveAward1);
        $info['every_day'] = array('0', '0', '0', '0', '0', '0', '0');
        $info['award'] = array('0', '0', '0', '0', '0', '0', '0');
        $info['sign_time'] = 0;
        $info['award_five_sign'] = 0;
        $info['award_seven_sign'] = 0;
        $info['update_time'] = 0;
        $a = Hapyfish2_Alchemy_HFC_EverySign::updateUserEvery($uid, $info);
        $userEverySign = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryInfo($uid);
        print_r($userEverySign);
    }

    public static function signActiveTime($uid, $time) {
        $result = explode('-', $time);
        $userEverySign = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryInfo($uid);
        $userEverySign['update_time'] = mktime(0, 0, 0, $result[1], $result[2], $result[0]);
        $a = Hapyfish2_Alchemy_HFC_EverySign::updateUserEvery($uid, $userEverySign);
        $userEverySign = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryInfo($uid);
        print_r($userEverySign);
    }

}
