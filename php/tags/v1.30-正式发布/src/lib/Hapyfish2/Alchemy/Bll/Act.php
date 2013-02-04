<?php

class Hapyfish2_Alchemy_Bll_Act
{

    public static function get($uid)
	{
		$actState = array();

		//illustrate
        $illustrateAct = array (
            'actName' => 'IllustratedHandbook',
            'moduleUrl' => STATIC_HOST . '/swf03/IllustratedHandbook.swf?v=2012080801'
        );
        $actState[] = $illustrateAct;

		//storage
        $storageAct = array (
            'actName' => 'storage',
            'moduleUrl' => STATIC_HOST . '/swf03/storage.swf?v=2012080801'
        );
        $actState[] = $storageAct;

		//WorldMap
		$hasNewWorld = false;
		$aryWorldMap = Hapyfish2_Alchemy_Bll_WorldMap::getOpenWorldMap($uid, $hasNewWorld);
        $worldMapAct = array (
            'actName' => 'WorldMap',
            'moduleUrl' => STATIC_HOST . '/swf03/WorldMap.swf?v=2012080801',
            'moduleData' => array('curOpenScene' => $aryWorldMap, 'bg' => STATIC_HOST . '/alchemy/image/worldmap/bg.jpg?v=1.00')
        );
        $actState[] = $worldMapAct;

		//Battle
        $battleAct = array (
            'actName' => 'battle',
            'moduleUrl' => STATIC_HOST . '/swf03/battle.swf?v=2012080801'
        );
        $actState[] = $battleAct;

        //hire
        $hireAct = array (
            'actName' => 'hire',
            'moduleUrl' => STATIC_HOST . '/swf03/hire.swf?v=2012080801'
        );
        $actState[] = $hireAct;

        //roleInfo
        $roleInfoAct = array (
            'actName' => 'roleInfo',
            'moduleUrl' => STATIC_HOST . '/swf03/roleInfo.swf?v=2012080801'
        );
        $actState[] = $roleInfoAct;

        //Gift
		$mkey = 'a:u:gift:newrececnt:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $newReceCnt = (int)$cache->get($mkey);
		$giftAct = array(
			'actName' => 'giftact',
			'backModuleUrl' => STATIC_HOST . '/swf03/GiftGetAct.swf?v=2012080801',
		    'moduleData' => array('giftNum' => $newReceCnt)
		);
		$actState[] = $giftAct;
		
		$eventgiftAct = array(
			'actName' => 'GuideGift',
			'backModuleUrl' => STATIC_HOST . '/swf03/GuideGift.swf?v=2012080801',
		);
		$actState[] = $eventgiftAct;
		
		$userFeed = array(
			'actName' => 'UserFeedback',
			'backModuleUrl' => STATIC_HOST . '/swf03/UserFeedback.swf?v=2012080801',
		);
		$actState[] = $userFeed;

        $shopAct = array (
        	'actName' => 'shop',
        	'moduleUrl' => STATIC_HOST . '/swf03/shop.swf?v=2012080801'
        );
        $actState[] = $shopAct;

        $tigerMachineAct = array (
        	'actName' => 'TigerMachine',
        	'moduleUrl' => STATIC_HOST . '/swf03/TigerMachine.swf?v=2012080801'
        );
        $actState[] = $tigerMachineAct;

        $specialUpgradeAct = array (
        	'actName' => 'specialUpgrade',
        	'moduleUrl' => STATIC_HOST . '/swf03/specialUpgrade.swf?v=2012080801'
        );
        $actState[] = $specialUpgradeAct;

        $recoverHpMpAct = array (
        	'actName' => 'recoverHpMp',
        	'moduleUrl' => STATIC_HOST . '/swf03/recoverHpMp.swf?v=2012080801'
        );
        $actState[] = $recoverHpMpAct;

        $dailyAct = array (
        	'actName' => 'Daily',
        	'moduleUrl' => STATIC_HOST . '/swf03/Daily.swf?v=2012080801'
        );
        $actState[] = $dailyAct;
        
        $fixEquipAct = array (
        	'actName' => 'fixEquip',
        	'moduleUrl' => STATIC_HOST . '/swf03/fixEquip.swf?v=2012080801'
        );
        $actState[] = $fixEquipAct;
        
        $strengThenAct = array (
        	'actName' => 'StrengThen',
        	'moduleUrl' => STATIC_HOST . '/swf03/StrengThen.swf?v=2012080801'
        );
        $actState[] = $strengThenAct;
        
        $roleWorkAct = array (
        	'actName' => 'RoleWork',
        	'moduleUrl' => STATIC_HOST . '/swf03/RoleWork.swf?v=2012080801'
        );
        $actState[] = $roleWorkAct;
        
         $strengthenEquipment = array (
        	'actName' => 'StrengthenEquipment',
        	'moduleUrl' => STATIC_HOST . '/swf03/StrengthenEquipment.swf'
        );
        $actState[] = $strengthenEquipment;
        
        $livenessTask = array (
        	'actName' => 'LivenessTask',
        	'moduleUrl' => STATIC_HOST . '/swf03/LivenessTask.swf',
        	'menuUrl'	=> STATIC_HOST . '/swf03/LivenessTask.swf',
        	'menuIndex'	=> 9999,
        	'menuClass'=>'LivenessTaskBtn'
        );
        $actState[] = $livenessTask;
        $tgiftStatus = Hapyfish2_Alchemy_Bll_EventGift::getTestGiftStatus($uid);
        if($tgiftStatus){
        	 $testGift = array (
	        	'actName' => 'TestGift',
        	 	'moduleUrl' => STATIC_HOST . '/swf03/TestGift.swf',
	        	'menuUrl'	=> STATIC_HOST . '/swf03/TestGift.swf',
	        	'menuIndex'	=> 9999,
	        	'menuClass'=>'TestGiftBtn'
       		 );
        	$actState[] = $testGift;
        }
		$awardOfInvited = array (
        	'actName' => 'awardOfInvited',
        	'moduleUrl' => STATIC_HOST . '/swf03/awardOfInvited.swf',
        	'menuUrl'	=> STATIC_HOST . '/swf03/awardOfInvited.swf',
			'menuClass'	=>'happymagic.awardOfInvited.view.InviteAwardIcon'
        );
		$actState[] = $awardOfInvited;
        
		return $actState;
	}


}