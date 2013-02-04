<?php

class Hapyfish2_Alchemy_Bll_Act
{

    public static function get($uid)
	{
		$actState = array();

		//illustrate
        $illustrateAct = array (
            'actName' => 'IllustratedHandbook',
            'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/IllustratedHandbook.swf?v=2012080801'
        );
        $actState[] = $illustrateAct;

		//storage
        $storageAct = array (
            'actName' => 'storage',
            'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/storage.swf?v=2012080801'
        );
        $actState[] = $storageAct;

		//WorldMap
		$hasNewWorld = false;
		$aryWorldMap = Hapyfish2_Alchemy_Bll_WorldMap::getOpenWorldMap($uid, $hasNewWorld);
        $worldMapAct = array (
            'actName' => 'WorldMap',
            'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/WorldMap.swf?v=2012080801',
            'moduleData' => array('curOpenScene' => $aryWorldMap, 'bg' => STATIC_HOST . '/alchemy/image/worldmap/bg02.jpg?v=1.00')
        );
        $actState[] = $worldMapAct;

		//Battle
        $battleAct = array (
            'actName' => 'battle',
            'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/battle.swf?v=2012081301'
        );
        $actState[] = $battleAct;

        //hire
        $hireAct = array (
            'actName' => 'hire',
            'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/hire.swf?v=2012080801'
        );
        $actState[] = $hireAct;

        //roleInfo
        $roleInfoAct = array (
            'actName' => 'roleInfo',
            'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/roleInfo.swf?v=2012080801'
        );
        $actState[] = $roleInfoAct;

        //Gift
		$mkey = 'a:u:gift:newrececnt:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $newReceCnt = (int)$cache->get($mkey);
		$giftAct = array(
			'actName' => 'giftact',
			'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/GiftGetAct.swf?v=2012080801',
		    'moduleData' => array('giftNum' => $newReceCnt)
		);
		$actState[] = $giftAct;
		
		$eventgiftAct = array(
			'actName' => 'GuideGift',
			'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/GuideGift.swf?v=2012080801',
		);
		$actState[] = $eventgiftAct;
		
        $shopAct = array (
        	'actName' => 'shop',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/shop.swf?v=2012080801'
        );
        $actState[] = $shopAct;

        $tigerMachineAct = array (
        	'actName' => 'TigerMachine',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/TigerMachine.swf?v=2012080801'
        );
        $actState[] = $tigerMachineAct;

        $specialUpgradeAct = array (
        	'actName' => 'specialUpgrade',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/specialUpgrade.swf?v=2012080801'
        );
        $actState[] = $specialUpgradeAct;

        $recoverHpMpAct = array (
        	'actName' => 'recoverHpMp',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/recoverHpMp.swf?v=2012080801'
        );
        $actState[] = $recoverHpMpAct;

        $fixEquipAct = array (
        	'actName' => 'fixEquip',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/fixEquip.swf?v=2012080801'
        );
        $actState[] = $fixEquipAct;
        
        $strengThenAct = array (
        	'actName' => 'StrengThen',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/StrengThen.swf?v=2012080801'
        );
        $actState[] = $strengThenAct;
        
        $roleWorkAct = array (
        	'actName' => 'RoleWork',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/RoleWork.swf?v=2012080801'
        );
        $actState[] = $roleWorkAct;
        
         $strengthenEquipment = array (
        	'actName' => 'StrengthenEquipment',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/StrengthenEquipment.swf'
        );
        $actState[] = $strengthenEquipment;
        
        $actbtn = array (
	        	'actName' => 'ActBtn',
        	 	'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/ActBtn.swf',
       		 );
        $actState[] = $actbtn;
        
         $arena = array (
        	'actName' => 'arena',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/alchemyArena.swf'
         );
         $actState[] = $arena;
	         if(PLATFORM != 'sinaweibo'){
//	         	 $vipDayAward = array (
//	        	'actName' => 'VIPEveryDay',
//	        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/VIPEveryDay.swf',
//	        	'menuUrl'	=> STATIC_HOST . '/'.SWF_VER.'/VIPEveryDay.swf',
//				'menuClass'	=>'VIPEveryDayBtn',
//				'menuIndex'	=> 9990,
//	        	'menuType' =>3
//	         );
//	         $actState[] = $vipDayAward;
//	         $pay = Hapyfish2_Alchemy_Bll_EventGift::getPayStatus($uid);
//        	 if($pay){
//	        	 $firstPay = array (
//		        	'actName' => 'FirstPay',
//		        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/FirstPay.swf',
//		        	'menuUrl'	=> STATIC_HOST . '/'.SWF_VER.'/FirstPay.swf',
//					'menuClass'	=>'FirstPayBtn',
//					'menuIndex'	=> 9995,
//		        	'menuType' =>3
//	        	 );
//				 $actState[] = $firstPay;
//       		 }

//			$actState[] = $awardOfInvited;
			$userFeed = array(
	        	'actName' => 'UserFeedback',
        	 	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/UserFeedback.swf',
	        	'menuUrl'	=> STATIC_HOST . '/'.SWF_VER.'/UserFeedback.swf',
	        	'menuIndex'	=> 9998,
	        	'menuClass'=>'UserFeedbackBtn',
        	 	'menuType' =>3
			);
			$actState[] = $userFeed;
//	        $tgiftStatus = Hapyfish2_Alchemy_Bll_EventGift::getTestGiftStatus($uid);
//       		if($tgiftStatus){
//	        	$testGift = array (
//		        	'actName' => 'TestGift',
//	        	 	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/TestGift.swf',
//		        	'menuUrl'	=> STATIC_HOST . '/'.SWF_VER.'/TestGift.swf',
//		        	'menuIndex'	=> 9999,
//		        	'menuClass'=>'TestGiftBtn',
//	        	 	'menuType' =>3
//	       		 );
//	        	$actState[] = $testGift;
//        	}
        	
         }
        $dailyAct = array (
		        'actName' => 'Daily',
		        'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/Daily.swf?v=2012080801'
        );
        $actState[] = $dailyAct;
         
         $trainingCamp = array (
        	'actName' => 'TrainingCamp',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/TrainingCamp.swf',
         );
         $actState[] = $trainingCamp;
         $newOrder = array(
				'actName'=>'newOrder',
         		'backModuleUrl'=> STATIC_HOST . '/'.SWF_VER.'/NewOrder.swf',
		);
		 $actState[] = $newOrder;
//         $Helper = array (
//        	'actName' => 'Helper',
//        	'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/helper.swf',
//        );
//		$actState[] = $Helper;
		/* $EveryDaySignIn = array (
        	'actName' => 'EveryDaySignIn',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/EveryDaySignIn.swf',
        	'menuUrl'	=> STATIC_HOST . '/'.SWF_VER.'/EveryDaySignIn.swf',
			'menuClass'	=>'EveryDaySignInBtn',
			'menuIndex'	=> 9989,
        	'menuType' =>3
         );
         $actState[] = $EveryDaySignIn; */
		$treasureBox = array(
			'actName' => 'TreasureBox',
			'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/TreasureBox.swf?v=2012080801',
		);
		$actState[] = $treasureBox;
		$vipGuess = array(
			'actName' => 'VIPGuess',
			'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/VIPGuess.swf',
			'menuUrl'	=> STATIC_HOST . '/'.SWF_VER.'/VIPGuess.swf',
			'menuClass'	=>'VIPGuessBtn',
			'menuIndex'	=> 9988,
			'menuType' =>3
		);
		$actState[] = $vipGuess;
//		$awardOfInvited = array (
//        		'actName' => 'awardOfInvited',
//	        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/awardOfInvited.swf',
//	        	'menuUrl'	=> STATIC_HOST . '/'.SWF_VER.'/awardOfInvited.swf',
//				'menuClass'	=>'happymagic.awardOfInvited.view.InviteAwardIcon',
//				'menuIndex'	=> 9996,
//	        	'menuType' =>3
//        );
//        $actState[] = $awardOfInvited;
//       $livenessTask = array (
//	        	'actName' => 'LivenessTask',
//	        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/LivenessTask.swf',
//	        	'menuUrl'	=> STATIC_HOST . '/'.SWF_VER.'/LivenessTask.swf',
//	        	'menuIndex'	=> 9999,
//	        	'menuClass'=>'LivenessTaskBtn',
//	        	'menuType' =>3
//        
//        	);
//        $actState[] = $livenessTask;
       $Legend = array (
	        	'actName' => 'Legend',
	        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/Legend.swf',
	        	'menuUrl'	=> STATIC_HOST . '/'.SWF_VER.'/Legend.swf',
	        	'menuIndex'	=> 10000,
	        	'menuClass'=>'LegendBtn',
	        	'menuType' =>3
        
        	);
        $actState[] = $Legend;
		 return $actState;
	}


}