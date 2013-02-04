<?php

class Hapyfish2_Alchemy_Bll_Act_Sinawb
{

    public static function get($uid)
	{
		$actState = array();

		//illustrate
        $illustrateAct = array (
            'actName' => 'IllustratedHandbook',
            'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012110801/IllustratedHandbook.swf?v=2012080801'
        );
        $actState[] = $illustrateAct;

		//storage
        $storageAct = array (
            'actName' => 'storage',
            'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012111901/storage.swf?v=2012080801'
        );
        $actState[] = $storageAct;

		//WorldMap
		$hasNewWorld = false;
		$aryWorldMap = Hapyfish2_Alchemy_Bll_WorldMap::getOpenWorldMap($uid, $hasNewWorld);
        $worldMapAct = array (
            'actName' => 'WorldMap',
            'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012110901/WorldMap.swf?v=2012080801',
            'moduleData' => array('curOpenScene' => $aryWorldMap, 'bg' => STATIC_HOST . '/alchemy/image/worldmap/bg02.jpg?v=1.00')
        );
        $actState[] = $worldMapAct;

		//Battle
        $battleAct = array (
            'actName' => 'battle',
            'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012110901/battle.swf?v=2012081301'
        );
        $actState[] = $battleAct;

        //hire
        $hireAct = array (
            'actName' => 'hire',
            'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012110701/hire.swf?v=2012080801'
        );
        $actState[] = $hireAct;

        //roleInfo
        $roleInfoAct = array (
            'actName' => 'roleInfo',
            'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012111901/roleInfo.swf?v=2012080801'
        );
        $actState[] = $roleInfoAct;

        //Gift
		$mkey = 'a:u:gift:newrececnt:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $newReceCnt = (int)$cache->get($mkey);
		$giftAct = array(
			'actName' => 'giftact',
			'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012111201/GiftGetAct.swf?v=2012080801',
		    'moduleData' => array('giftNum' => $newReceCnt)
		);
		$actState[] = $giftAct;
		$dm = array (
        	'actName' => 'DM',
        	'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012111901/DM.swf',
        );
		$actState[] = $dm;
		$eventgiftAct = array(
			'actName' => 'GuideGift',
			'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012111401/GuideGift.swf?v=2012080801',
		);
		$actState[] = $eventgiftAct;
        $shopAct = array (
        	'actName' => 'shop',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012111901/shop.swf?v=2012080801'
        );
        $actState[] = $shopAct;

        $tigerMachineAct = array (
        	'actName' => 'TigerMachine',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012110801/TigerMachine.swf?v=2012080801'
        );
        $actState[] = $tigerMachineAct;

        $specialUpgradeAct = array (
        	'actName' => 'specialUpgrade',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012111901/specialUpgrade.swf?v=2012080801'
        );
        $actState[] = $specialUpgradeAct;

        $recoverHpMpAct = array (
        	'actName' => 'recoverHpMp',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/recoverHpMp.swf?v=2012080801'
        );
        $actState[] = $recoverHpMpAct;

        $fixEquipAct = array (
        	'actName' => 'fixEquip',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012110701/fixEquip.swf?v=2012080801'
        );
        $actState[] = $fixEquipAct;
        
        $strengThenAct = array (
        	'actName' => 'StrengThen',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/StrengThen.swf?v=2012080801'
        );
        $actState[] = $strengThenAct;
        
        $roleWorkAct = array (
        	'actName' => 'RoleWork',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012110801/RoleWork.swf?v=2012080801'
        );
        $actState[] = $roleWorkAct;
        
         $strengthenEquipment = array (
        	'actName' => 'StrengthenEquipment',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012111201/StrengthenEquipment.swf'
        );
        $actState[] = $strengthenEquipment;
        
        $actbtn = array (
	        	'actName' => 'ActBtn',
        	 	'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/2012110601/ActBtn.swf',
       		 );
        $actState[] = $actbtn;
        $livenessTask = array (
        	'actName' => 'LivenessTask',
        	'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012111901/LivenessTask.swf',
        );
        $actState[] = $livenessTask;
         $arena = array (
        	'actName' => 'arena',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012111201/alchemyArena.swf'
         );
         $actState[] = $arena;
         
	         $vipDayAward = array (
	        	'actName' => 'VIPEveryDay',
	        	'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012111901/VIPEveryDay.swf',
	         );
	         $actState[] = $vipDayAward;

		 if(PLATFORM != 'sinaweibo'){
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
		        'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012110801/Daily.swf?v=2012080801'
        );
        $actState[] = $dailyAct;
         
         $trainingCamp = array (
        	'actName' => 'TrainingCamp',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012111901/TrainingCamp.swf',
         );
         $actState[] = $trainingCamp;
         $newOrder = array(
				'actName'=>'newOrder',
         		'backModuleUrl'=> STATIC_HOST . '/'.SWF_VER.'/v2012111901/NewOrder.swf',
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
			'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012111901/TreasureBox.swf?v=2012080801',
		);
		$actState[] = $treasureBox;
//		$vipGuess = array(
//			'actName' => 'VIPGuess',
//			'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012110901/VIPGuess.swf',
//			'menuUrl'	=> STATIC_HOST . '/'.SWF_VER.'/VIPGuessBtn.swf',
//			'menuClass'	=>'VIPGuessBtn',
//			'menuIndex'	=> 9988,
//			'menuType' =>3
//		);                      
//		$actState[] = $vipGuess;
//		$awardOfInvited = array (
//        		'actName' => 'awardOfInvited',
//	        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/awardOfInvited.swf',
//	        	'menuUrl'	=> STATIC_HOST . '/'.SWF_VER.'/awardOfInvited.swf',
//				'menuClass'	=>'happymagic.awardOfInvited.view.InviteAwardIcon',
//				'menuIndex'	=> 9996,
//	        	'menuType' =>3
//        );
//        $actState[] = $awardOfInvited;
		 return $actState;
	}
}