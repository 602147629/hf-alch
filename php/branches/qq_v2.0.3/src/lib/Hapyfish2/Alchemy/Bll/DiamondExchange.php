<?php

class Hapyfish2_Alchemy_Bll_DiamondExchange {

    /**
     *   聚神点金 初始化 接口 
     * @param type $uid
     */
    public static function init($uid) {
        // 获取 主角 战斗等级 的   相关 金币 和 神勇点 兑换 
        //$diamondExchange = Hapyfish2_Alchemy_Cache_DiamondExchange::getDiamodExchange($level);
        // 获取 主角 兑换 金币 和 神勇点的次数
        $userDiamondExchange = Hapyfish2_Alchemy_Cache_DiamondExchange::getUserDiamodExchange($uid);
        $cashCowCommonVo['bravePointNum'] = 10 - $userDiamondExchange['bravechanger'];
        $cashCowCommonVo['coinNum'] = 10 - $userDiamondExchange['goldchanger'];
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'cashCowCommonVo', $cashCowCommonVo);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'test', $userDiamondExchange);
    }

    /**
     *  聚神点金 静态数据
     */
    public static function CashCowStatic($uid) {
        $cowStatic = array();
        $selfInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
        $level = $selfInfo['level'];
        $diamondExchange = Hapyfish2_Alchemy_Cache_DiamondExchange::getDiamodExchange($level);
        foreach ($diamondExchange[0]['gold_change'] as $k => $v) {
            $cowStaticTemp['num'] = $k + 1;
            $cowStaticTemp['type'] = 1;
            $cowStaticTemp['exchangeNum'] = $v;
            $cowStaticTemp['consumptionNum'] = $diamondExchange[0]['gold_diamondcost'][$k];
            array_push($cowStatic, $cowStaticTemp);
            unset($v);
        }
        foreach ($diamondExchange[0]['brova_change'] as $k_brova => $v_brova) {
            $cowStaticTemp['num'] = $k_brova + 1;
            $cowStaticTemp['type'] = 2;
            $cowStaticTemp['exchangeNum'] = $v_brova;
            $cowStaticTemp['consumptionNum'] = $diamondExchange[0]['brova_diamondcost'][$k_brova];
            array_push($cowStatic, $cowStaticTemp);
            unset($v_brova);
        }
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'cashCowStaticVo', $cowStatic);
    }

    /**
     *  聚神点金 数据 请求 
     *  type  为1 金币  2 为神勇点
     */
    public static function CashCowCommand($uid, $type) {
        $selfInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
        $level = $selfInfo['level'];
        // 获取 主角 战斗等级 的   相关 金币 和 神勇点 兑换 
        $diamondExchange = Hapyfish2_Alchemy_Cache_DiamondExchange::getDiamodExchange($level);
        // 获取 主角 兑换 金币 和 神勇点的次数
        $userDiamondExchange = Hapyfish2_Alchemy_Cache_DiamondExchange::getUserDiamodExchange($uid);
        if ($type == 1) {
            $exchangeData = $diamondExchange[0]['gold_change'];
            $diamondCost = $diamondExchange[0]['gold_diamondcost'];
            $count = $userDiamondExchange['goldchanger'] + 1;
        } else {
            $exchangeData = $diamondExchange[0]['brova_change'];
            $diamondCost = $diamondExchange[0]['brova_diamondcost'];
            $count = $userDiamondExchange['bravechanger'] + 1;
        }
        $awardData = $exchangeData[$count - 1];
        if ($count <= 10) {
            $diamondCost = $diamondCost[$count - 1];
            $userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
            if ($userGem < $diamondCost) {
                return -206;
            }
            //按照类型 增加金币 和 神勇点  扣减宝石
            if ($type == 1) {
                Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $awardData);
                $userDiamondExchange['goldchanger'] += 1;
            } else {
                Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, 3315, $awardData);
                $userDiamondExchange['bravechanger'] += 1;
            }
            $cost['cost'] = $diamondCost;
            Hapyfish2_Alchemy_Bll_Gem::consume($uid, $cost, 19);
            $userDiamondExchange['change_data'] = date('Ymd', mktime(0, 0, 0));
            Hapyfish2_Alchemy_Cache_DiamondExchange::updateUserDiamodExchange($uid, $userDiamondExchange);
            $log = Hapyfish2_Util_Log::getInstance();
            $log->report('DiamondExchange', array($uid, $type));
            $log->report('DiamondExchangeCost', array($uid, $type, $diamondCost));
        } else {

            //return -1200;
        }
    }
    
    /**
     *  聚神点金 数据 请求 
     *  type  为1 金币  2 为神勇点
     */
    public static function CashQQCowCommand($uid, $type) 
    {
        $selfInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
        $level = $selfInfo['level'];
        // 获取 主角 战斗等级 的   相关 金币 和 神勇点 兑换 
        $diamondExchange = Hapyfish2_Alchemy_Cache_DiamondExchange::getDiamodExchange($level);
        // 获取 主角 兑换 金币 和 神勇点的次数
        $userDiamondExchange = Hapyfish2_Alchemy_Cache_DiamondExchange::getUserDiamodExchange($uid);
        if ($type == 1) {
            $exchangeData = $diamondExchange[0]['gold_change'];
            $diamondCost = $diamondExchange[0]['gold_diamondcost'];
            $count = $userDiamondExchange['goldchanger'] + 1;
        } else {
            $exchangeData = $diamondExchange[0]['brova_change'];
            $diamondCost = $diamondExchange[0]['brova_diamondcost'];
            $count = $userDiamondExchange['bravechanger'] + 1;
        }
        $awardData = $exchangeData[$count - 1];
        if ($count <= 10) {
            $diamondCost = $diamondCost[$count - 1];
           	$needGem = $diamondCost;
           if($needGem > 0){
	           	$goodsInfo = array();
		    	$goodsInfo['goodsmeta'] = '聚神点金*聚神点金';
		    	$goodsInfo['goodsurl'] = STATIC_HOST.'/alchemy/image/item/qpoint.jpg';
		    	$goodsInfo['payitem'] = 'p016*'.$needGem.'*1';
				$token = Hapyfish2_Platform_Bll_QqpayByToken::getToken($uid,$goodsInfo);
				if($token){
					Hapyfish2_Alchemy_Bll_UserResult::setYellow($uid);
					Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'payJs', $token['url_params']);
					Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'token', $token['token']);
					$payNeedData = array('type'=>$type);
					Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'payNeedData', $payNeedData);
				}
	           return 1;
           }
           
        } else {

            //return -1200;
        }
     
    }

    /**
     *  重置 聚神点金 缓存和 相关缓存
     */
    public static function cshcowrest($uid, $type) {
        $userDiamondExchange = Hapyfish2_Alchemy_Cache_DiamondExchange::getUserDiamodExchange($uid);
        print_r($userDiamondExchange);
        Hapyfish2_Alchemy_Cache_DiamondExchange::updateUserDiamodExchangeSet($uid);
        Hapyfish2_Alchemy_Cache_DiamondExchange::updateDiamodExchangeSet();
//        switch ($type) {
//            case '1':
//                //删除玩家缓存
//                Hapyfish2_Alchemy_Cache_DiamondExchange::updateUserDiamodExchangeSet($uid);
//                break;
//            case '2':
//                //删除兑换数据缓存
//                Hapyfish2_Alchemy_Cache_DiamondExchange::updateDiamodExchangeSet();
//                break;
//        }
    }

}
