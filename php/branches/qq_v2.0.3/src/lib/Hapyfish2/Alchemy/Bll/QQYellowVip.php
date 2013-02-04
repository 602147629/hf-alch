<?php

class Hapyfish2_Alchemy_Bll_QQYellowVip
{
	/**
	 * 获取QQ黄钻用户 每日奖励
	 *
	 * @param int $uid
	 */
	public static function getDailyGift($uid)
	{
		//用户平台信息
		$user = Hapyfish2_Platform_Bll_User::getYellowInfo($uid);
		
		//用户是否是黄钻
		if ( $user['is_yellow_vip'] == 0 ) {
			return -731;
		}

		//判断今天是否已经领取过QQ黄钻每日礼包，每日只可一次
		$canGet = Hapyfish2_Alchemy_Cache_User::canGetTodayQQYellowVipGift($uid);
		if ( $canGet != 'Y' ) {
			return -734;
		}
		
		//对应黄钻等级的 每日礼包信息
		$yellowVipGift = Hapyfish2_Alchemy_Cache_BasicExt::getQQYellowVipGift($user['yellow_vip_level']);
		if ( !$yellowVipGift ) {
			return -732;
		}
		
		$awards = json_decode($yellowVipGift['awards'], true);
		if ( empty($awards) ) {
			return -733;
		}

		/**start - send awards**/
		$ok = Hapyfish2_Alchemy_Bll_Item::sendAwards($uid, $awards);
		if ( !$ok ) {
			return -200;
		}

		//用户是否是年费黄钻，年费额外奖励
		if ( $user['is_yellow_year_vip'] == 1 ) {
			$yearYellowVipGift = Hapyfish2_Alchemy_Cache_BasicExt::getQQYellowVipGift(0);
			$yearVipAwards = json_decode($yearYellowVipGift['awards'], true);
			Hapyfish2_Alchemy_Bll_Item::sendAwards($uid, $yearVipAwards);
		}
		
		Hapyfish2_Alchemy_Cache_User::updateTodayQQYellowVipGift($uid, 'N');
		
		return 1;
	}

	/**
	 * 是否已经领取过QQ黄钻每日礼包
	 * @param int $uid
	 * @return int
	 */
	public static function hasDailyGift($uid)
	{
		//判断今天是否已经领取过QQ黄钻每日礼包，每日只可一次
		$canGet = Hapyfish2_Alchemy_Cache_User::canGetTodayQQYellowVipGift($uid);
		
		if ( $canGet == 'Y' ) {
			$hasGift = 1;
		}
		else {
			$hasGift = 0;
		}

		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'has', $hasGift);
		return $canGet;
	}

	/**
	 * 是否可以领取QQ黄钻新手礼包
	 * @param int $uid
	 * @return array
	 */
	public static function hasGuideGift($uid)
	{
		//判断今天是否可以领取QQ黄钻新手礼包，每日只可一次
		$canGet = Hapyfish2_Alchemy_Cache_User::canGetGuideQQYellowVipGift($uid);
		
		if ( $canGet == 'Y' ) {
			$hasGet = 1;
		}
		else {
			$hasGet = 0;
		}
		
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'type', $hasGet);
		return $canGet;
	}
	
	/**
	 * 领取QQ黄钻新手礼包
	 * 
	 * @param int $uid
	 * @return array
	 */
	public static function getGuideGift($uid)
	{
		//用户平台信息
		$user = Hapyfish2_Platform_Bll_User::getYellowInfo($uid);
		
		//用户是否是黄钻
		if ( $user['is_yellow_vip'] == 0 ) {
			return -731;
		}
		
		//判断是否已经领取过QQ黄钻新手礼包
		$canGet = Hapyfish2_Alchemy_Cache_User::canGetGuideQQYellowVipGift($uid);
		if ( $canGet != 'Y' ) {
			return -735;
		}
		
		$guideAwards = '[{"type":"2","id":"coin","num":"5000"},{"type":"1","id":"5015","num":"5"},{"type":"1","id":"271","num":"5"},{"type":"1","id":"4415","num":"2"},{"type":"1","id":"4515","num":"2"}]';
		$awards = json_decode($guideAwards, true);

		/**start - send awards**/
		$ok = Hapyfish2_Alchemy_Bll_Item::sendAwards($uid, $awards);
		if ( !$ok ) {
			return -200;
		}
		
		Hapyfish2_Alchemy_Cache_User::updateGuideQQYellowVipGift($uid, 'N');
		
		return 1;
	}
	
	
	
	
}