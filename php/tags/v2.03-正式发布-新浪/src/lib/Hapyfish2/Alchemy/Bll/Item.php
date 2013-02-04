<?php

/**
 * item
 *
 * @copyright  Copyright (c) 2012 HapyFish
 * @create      2012/08   Nick
 */
class Hapyfish2_Alchemy_Bll_Item
{
	
    /**
     * 检查需求物品条件
     * 
     * @param int $uid
     * @param array $needs,
     * 例：[{"type":"1","id":"1515","num":"10"},{"type":"2","id":"coin","num":"150"},{"type":"2","id":"gem","num":"10"},{"type":"2","id":"level-炼金等级","num":"10"}]
     * type=1,道具；
     * type=2,包含：coin,gem,sp
     */
    public static function checkNeeds($uid, $needs)
    {
    	$basGoods = Hapyfish2_Alchemy_Cache_Basic::getGoodsList();
    	
    	foreach ($needs as $k => $v) {
    		//道具
    		if ($v['type'] == 1) {
    			$cid = $v['id'];
    			$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
    			if (empty($userGoods) || !isset($userGoods[$cid]) || $userGoods[$cid]['count'] <= 0) {
    				//return 'item_not_enough';
    					if ( $cid == 3315 ) {
    						return -253;
    					}
    					else {
    						return -204;
    					}
    			}
    	
    			if ($basGoods[$cid]['lose']) {
    				if ($userGoods[$cid]['count']<$v['num']) {
    					//return 'item_not_enough';
    					if ( $cid == 3315 ) {
    						return -253;
    					}
    					else {
    						return -204;
    					}
    				}
    			}
    		}
    		//玩家属性
    		else if ($v['type'] == 2) {
    			if ($v['id'] == 'coin') {
    				$userCoin = Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
    				if ($userCoin < $v['num']) {
    					//return 'coin_not_enough';
    					return -207;
    				}
    			}
    			else if ($v['id'] == 'gem') {
    				$userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
    				if ($userGem < $v['num']) {
    					return -206;
    				}
    			}
    			else if ($v['id'] == 'sp') {
    				$userSp = Hapyfish2_Alchemy_HFC_User::getUserSp($uid);
    				if ($userSp['sp'] < $v['num']) {
    					//return 'sp_not_enough';
    					return -208;
    				}
    			}//炼金等级
    			else if ($v['id'] == 'level') {
    				$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
    				if ($userLevel < $v['num']) {
    					//return 'level_not_enough';
    					return -243;
    				}
    			}
    		}
    	}
    	
    	return 1;
    }
    
    /**
     * 消耗需求物品
     * @param int $uid
     * @param array $needs,
     * 例：[{"type":"1","id":"1515","num":"10"},{"type":"2","id":"coin","num":"150"},{"type":"2","id":"gem","num":"10"}]
     * type=1,道具；
     * type=2,包含：coin,gem,sp
     */
    public static function consumeNeeds($uid, $needs)
    {
    	$coinChange = $spChange = 0;
    	
    	$basGoods = Hapyfish2_Alchemy_Cache_Basic::getGoodsList();
    	 
    	foreach ($needs as $k => $v) {
    		//道具
    		if ($v['type'] == 1) {
    			$cid = $v['id'];
    			$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
    			if (empty($userGoods) || !isset($userGoods[$cid]) || $userGoods[$cid]['count'] <= 0) {
    				//return 'item_not_enough';
    				return -204;
    			}
    			 
    			if ($basGoods[$cid]['lose']) {
    				if ($userGoods[$cid]['count']<$v['num']) {
    					//return 'item_not_enough';
    					return -204;
    				}
    				Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $v['id'], $v['num'], $userGoods);
    			}
    		}
    		//玩家属性
    		else if ($v['type'] == 2) {
    			if ($v['id'] == 'coin') {
    				$userCoin = Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
    				if ($userCoin < $v['num']) {
    					//return 'coin_not_enough';
    					return -207;
    				}
    				else {
    					Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $v['num']);
    				}
    			}
    			else if ($v['id'] == 'gem') {
    				$userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
    				if ($userGem < $v['num']) {
    					return -206;
    				}
    				else {
    					//购买需要经营等级
    					$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
    					$gemInfo = array(
    							'uid' => $uid,
    							'cost' => $v['num'],
    							'summary' => '',
    							'user_level' => $userLevel,
    							'cid' => 0,
    							'num' => 0
    					);
    					Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo);
    				}
    			}
    			else if ($v['id'] == 'sp') {
    				$userSp = Hapyfish2_Alchemy_HFC_User::getUserSp($uid);
    				if ($userSp['sp'] < $v['num']) {
    					//return 'sp_not_enough';
    					return -208;
    				}
    				else {
    					Hapyfish2_Alchemy_HFC_User::decUserSp($uid, $v['num']);
    				}
    			}
    		}
    	}
    	 
    	return 1;
    }
}