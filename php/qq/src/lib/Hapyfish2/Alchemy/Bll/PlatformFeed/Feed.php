<?php

class Hapyfish2_Alchemy_Bll_PlatformFeed_Feed
{

	public static function taskCompleted($uid, $id, $title)
	{
		if ( 'kaixin' == PLATFORM ) {
			//platform feed
			Hapyfish2_Alchemy_Bll_PlatformFeed_Kaixin::taskCompleted($uid, $id, $title);
		}
		else if ( 'sinaweibo' == PLATFORM ) {
			//platform feed
			Hapyfish2_Alchemy_Bll_PlatformFeed_Sinawb::taskCompleted($uid, $id);
		}
	}

	public static function giftWish($uid, $id)
	{
		if ( 'kaixin' == PLATFORM ) {
			//platform feed
			Hapyfish2_Alchemy_Bll_PlatformFeed_Kaixin::giftWish($uid, $id);
		}
		else if ( 'sinaweibo' == PLATFORM ) {
			//platform feed
			Hapyfish2_Alchemy_Bll_PlatformFeed_Sinawb::giftWish($uid, $id);
		}
	}
	
	public static function killBoss($uid, $id)
	{
		if ( 'kaixin' == PLATFORM ) {
			//platform feed
			Hapyfish2_Alchemy_Bll_PlatformFeed_Kaixin::killBoss($uid, $id);
		}
		else if ( 'sinaweibo' == PLATFORM ) {
			//platform feed
			Hapyfish2_Alchemy_Bll_PlatformFeed_Sinawb::killBoss($uid, $id);
		}
	}

	public static function buildingLevelup($uid, $id)
	{
		if ( 'kaixin' == PLATFORM ) {
			//platform feed
			Hapyfish2_Alchemy_Bll_PlatformFeed_Kaixin::buildingLevelup($uid, $id);
		}
		else if ( 'sinaweibo' == PLATFORM ) {
			//platform feed
			Hapyfish2_Alchemy_Bll_PlatformFeed_Sinawb::buildingLevelup($uid, $id);
		}
	}

	public static function occupyHelp($uid, $id)
	{
		if ( 'kaixin' == PLATFORM ) {
			//platform feed
			$feed = Hapyfish2_Alchemy_Bll_PlatformFeed_Kaixin::occupyHelp($uid, $id);
		}
		else if ( 'sinaweibo' == PLATFORM ) {
			//platform feed
			$feed = Hapyfish2_Alchemy_Bll_PlatformFeed_Sinawb::occupyHelp($uid);
		}
		return $feed;
	}
	
	public static function userRolePromotion($uid, $id)
	{
		if ( 'kaixin' == PLATFORM ) {
			//platform feed
			Hapyfish2_Alchemy_Bll_PlatformFeed_Kaixin::userRolePromotion($uid, $id);
		}
		else if ( 'sinaweibo' == PLATFORM ) {
			//platform feed
			Hapyfish2_Alchemy_Bll_PlatformFeed_Sinawb::userRolePromotion($uid, $id);
		}
	}

	
}