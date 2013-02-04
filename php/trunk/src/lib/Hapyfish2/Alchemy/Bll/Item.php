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
    public static function consumeNeeds($uid, $needs, $channel = 36)
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
    					Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $v['num'], 36);
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
    					Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo,2);
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
    
    /**
     * 发放奖励物品
     * 
     * @param int $uid
     * @param array $awards,
     * 例：[{"type":"2","id":"battleExp","num":"20"},{"type":"2","id":"coin","num":"60"}]
     * type=1,道具；
     * type=2,包含：coin,sp,exp,activity,battleExp
     * type=3,图鉴
     */
    public static function sendAwards($uid, $awards)
    {
    	$coinChange = $spChange = $expChange = 0;
    	$gainItem = array();
    	
    	foreach ($awards as $k => $v) {
    		//道具
    		if ($v['type'] == 1) {
    			$cid = $v['id'];
    			$num = $v['num'];
    			$type =	substr($cid, -2, 1);
    
    			//1->物品,2->卷轴,3->材料,4->工作台,5->装修,6->装备
    			switch ($type) {
    				case 1:
    					Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, $cid, $num);
    					break;
    				case 2:
    					Hapyfish2_Alchemy_HFC_Scroll::addUserScroll($uid, $cid, $num);
    					break;
    				case 3:
    					Hapyfish2_Alchemy_HFC_Stuff::addUserStuff($uid, $cid, $num);
    					break;
    				case 4:
    					for ( $n = 0; $n < $num; $n++ ) {
    						$furnace = array('uid' => $uid,
    								'furnace_id' => $cid,
    								'status' => 0,
    								'idx' => 1);
    						Hapyfish2_Alchemy_HFC_Furnace::addOne($uid, $furnace);
    					}
    					break;
    				case 5:
    					Hapyfish2_Alchemy_HFC_Decor::addBag($uid, $cid, $num);
    					break;
    				case 6:
    					Hapyfish2_Alchemy_HFC_Weapon::addUserWeapon($uid, $cid, $num);
    					break;
    				default:
    			}
    			$gainItem[$cid] = $num;
    		}
    		//玩家属性
    		else if ($v['type'] == 2) {
    			if ($v['id'] == 'coin') {
    				$ok = Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $v['num'],26);
    			}
    			else if ($v['id'] == 'sp') {
    				$ok = Hapyfish2_Alchemy_HFC_User::incUserSp($uid, $v['num']);
    			}
    			else if ($v['id'] == 'exp') {
    				$ok = Hapyfish2_Alchemy_HFC_User::incUserExp($uid, $v['num']);
    			}
    			else if ($v['id'] == 'activity') {
    				$ok = Hapyfish2_Alchemy_Bll_Activity::addUserActivity($uid, $v['num']);
    			}
    			else if ($v['id'] == 'battleExp') {
    				$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
    				$userMercenary['exp'] += $v['num'];
    				$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary);
    				if ($ok) {
    					Hapyfish2_Alchemy_Bll_Mercenary::checkMercenaryLevelUp($uid, 0, $userMercenary);
    
    					//佣兵与主角数据
    					$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
    					$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
    					Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
    				}
    			}
    		}//奖励图鉴
    		else if ($v['type'] == 3) {
	    		$illCid = $v['id'];
	    		//添加图鉴
	    		Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $illCid);
	    	}
    	}
    	
    	return true;
    }
}