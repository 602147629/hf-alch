<?php

class Hapyfish2_Alchemy_Bll_Act
{

    public static function get($uid)
	{
		$actState = array();

		//illustrate
        $illustrateAct = array (
            'actName' => 'IllustratedHandbook',
            'moduleUrl' => STATIC_HOST . '/swf/IllustratedHandbook.swf'
        );
        $actState[] = $illustrateAct;

		//storage
        $storageAct = array (
            'actName' => 'storage',
            'moduleUrl' => STATIC_HOST . '/swf/storage.swf'
        );
        $actState[] = $storageAct;

		//WorldMap
		$hasNewWorld = false;
		$aryWorldMap = Hapyfish2_Alchemy_Bll_WorldMap::getOpenWorldMap($uid, $hasNewWorld);
        $worldMapAct = array (
            'actName' => 'WorldMap',
            'moduleUrl' => STATIC_HOST . '/swf/WorldMap.swf',
            'moduleData' => array('curOpenScene' => $aryWorldMap, 'bg' => STATIC_HOST . '/alchemy/image/worldmap/bg.jpg?v=1.00')
        );
        $actState[] = $worldMapAct;

		//Battle
        $battleAct = array (
            'actName' => 'battle',
            'moduleUrl' => STATIC_HOST . '/swf/battle.swf'
        );
        $actState[] = $battleAct;

        //hire
        $hireAct = array (
            'actName' => 'hire',
            'moduleUrl' => STATIC_HOST . '/swf/hire.swf'
        );
        $actState[] = $hireAct;

        //roleInfo
        $roleInfoAct = array (
            'actName' => 'roleInfo',
            'moduleUrl' => STATIC_HOST . '/swf/roleInfo.swf'
        );
        $actState[] = $roleInfoAct;

        //Gift
		$mkey = 'a:u:gift:newrececnt:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $newReceCnt = (int)$cache->get($mkey);
		$giftAct = array(
			'actName' => 'giftact',
			'backModuleUrl' => STATIC_HOST . '/swf/GiftGetAct.swf',
		    'moduleData' => array('giftNum' => $newReceCnt)
		);
		$actState[] = $giftAct;
		
		$eventgiftAct = array(
			'actName' => 'GuideGift',
			'backModuleUrl' => STATIC_HOST . '/swf/GuideGift.swf',
		);
		$actState[] = $eventgiftAct;
		
		$userFeed = array(
			'actName' => 'UserFeedback',
			'backModuleUrl' => STATIC_HOST . '/swf/UserFeedback.swf',
		);
		$actState[] = $userFeed;

        $shopAct = array (
        	'actName' => 'shop',
        	'moduleUrl' => STATIC_HOST . '/swf/shop.swf'
        );
        $actState[] = $shopAct;

        $tigerMachineAct = array (
        	'actName' => 'TigerMachine',
        	'moduleUrl' => STATIC_HOST . '/swf/TigerMachine.swf'
        );
        $actState[] = $tigerMachineAct;

        $specialUpgradeAct = array (
        	'actName' => 'specialUpgrade',
        	'moduleUrl' => STATIC_HOST . '/swf/specialUpgrade.swf'
        );
        $actState[] = $specialUpgradeAct;

        $recoverHpMpAct = array (
        	'actName' => 'recoverHpMp',
        	'moduleUrl' => STATIC_HOST . '/swf/recoverHpMp.swf'
        );
        $actState[] = $recoverHpMpAct;

        $dailyAct = array (
        	'actName' => 'Daily',
        	'moduleUrl' => STATIC_HOST . '/swf/Daily.swf'
        );
        $actState[] = $dailyAct;
        
        $fixEquipAct = array (
        	'actName' => 'fixEquip',
        	'moduleUrl' => STATIC_HOST . '/swf/fixEquip.swf'
        );
        $actState[] = $fixEquipAct;
        
        $strengThenAct = array (
        	'actName' => 'StrengThen',
        	'moduleUrl' => STATIC_HOST . '/swf/StrengThen.swf'
        );
        $actState[] = $strengThenAct;
        
        $roleWorkAct = array (
        	'actName' => 'RoleWork',
        	'moduleUrl' => STATIC_HOST . '/swf/RoleWork.swf'
        );
        $actState[] = $roleWorkAct;
        
		return $actState;
	}


}