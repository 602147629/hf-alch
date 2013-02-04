<?php

class Hapyfish2_Alchemy_Bll_Act_Sinawb {

    public static function get($uid) {
        $actState = array();

        //illustrate
        $illustrateAct = array(
            'actName' => 'IllustratedHandbook',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012110801/IllustratedHandbook.swf?v=2012080801'
        );
        $actState[] = $illustrateAct;

        //storage
        $storageAct = array(
            'actName' => 'storage',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121001/storage.swf?v=2012080801'
        );
        $actState[] = $storageAct;

        //WorldMap
        $hasNewWorld = false;
        $aryWorldMap = Hapyfish2_Alchemy_Bll_WorldMap::getOpenWorldMap($uid, $hasNewWorld);
        $worldMapAct = array(
            'actName' => 'WorldMap',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121401/WorldMap.swf?v=2012080801',
            'moduleData' => array('curOpenScene' => $aryWorldMap, 'bg' => STATIC_HOST . '/alchemy/image/worldmap/bg04.jpg?v=1.00')
        );
        $actState[] = $worldMapAct;

        //Battle
        $battleAct = array(
            'actName' => 'battle',
            //act,initswf 都有，版本号需要同步更新
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121801/battle.swf?v=2012081301'
        );
        $actState[] = $battleAct;

        //hire
        $hireAct = array(
            'actName' => 'hire',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121401/hire.swf?v=2012080801'
        );
        $actState[] = $hireAct;

        //roleInfo
        $roleInfoAct = array(
            'actName' => 'roleInfo',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012122001/roleInfo.swf?v=2012080801'
        );
        $actState[] = $roleInfoAct;

        //Gift
        $mkey = 'a:u:gift:newrececnt:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $newReceCnt = (int) $cache->get($mkey);
        $giftAct = array(
            'actName' => 'giftact',
            'backModuleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012120501/GiftGetAct.swf?v=2012080801',
            'moduleData' => array('giftNum' => $newReceCnt)
        );
        $actState[] = $giftAct;
        $dm = array(
            'actName' => 'DM',
            'backModuleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012120501/DM.swf',
        );
        $actState[] = $dm;
        $eventgiftAct = array(
            'actName' => 'GuideGift',
            'backModuleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121401/GuideGift.swf?v=2012080801',
        );
        $actState[] = $eventgiftAct;
        $shopAct = array(
            'actName' => 'shop',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121401/shop.swf?v=2012080801'
        );
        $actState[] = $shopAct;

        $tigerMachineAct = array(
            'actName' => 'TigerMachine',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121001/TigerMachine.swf?v=2012080801'
        );
        $actState[] = $tigerMachineAct;

        $specialUpgradeAct = array(
            'actName' => 'specialUpgrade',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121401/specialUpgrade.swf?v=2012080801'
        );
        $actState[] = $specialUpgradeAct;

        $recoverHpMpAct = array(
            'actName' => 'recoverHpMp',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121401/recoverHpMp.swf?v=2012080801'
        );
        $actState[] = $recoverHpMpAct;

        $fixEquipAct = array(
            'actName' => 'fixEquip',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012120501/fixEquip.swf?v=2012080801'
        );
        $actState[] = $fixEquipAct;

        $strengThenAct = array(
            'actName' => 'StrengThen',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/StrengThen.swf?v=2012080801'
        );
        $actState[] = $strengThenAct;

        $roleWorkAct = array(
            'actName' => 'RoleWork',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012112801/RoleWork.swf?v=2012080801'
        );
        $actState[] = $roleWorkAct;

        $strengthenEquipment = array(
            'actName' => 'StrengthenEquipment',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121901/StrengthenEquipment.swf'
        );
        $actState[] = $strengthenEquipment;

        $actbtn = array(
            'actName' => 'ActBtn',
            'backModuleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012120501/ActBtn.swf',
        );
        $actState[] = $actbtn;
        $livenessTask = array(
            'actName' => 'LivenessTask',
            'backModuleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121401/LivenessTask.swf',
        );
        $actState[] = $livenessTask;
        $arena = array(
            'actName' => 'arena',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121401/alchemyArena.swf'
        );
        $actState[] = $arena;

        $vipDayAward = array(
            'actName' => 'VIPEveryDay',
            'backModuleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121401/VIPEveryDay.swf',
        );
        $actState[] = $vipDayAward;

        if (PLATFORM != 'sinaweibo') {
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
                'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/UserFeedback.swf',
                'menuUrl' => STATIC_HOST . '/' . SWF_VER . '/UserFeedback.swf',
                'menuIndex' => 9998,
                'menuClass' => 'UserFeedbackBtn',
                'menuType' => 3
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
//        $dailyAct = array(
//            'actName' => 'Daily',
//            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012110801/Daily.swf?v=2012080801',
//            'menuUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012120501/diaryBtn.swf',
//            'menuClass' => 'diaryBtn',
//            'menuIndex' => 50,
//        );
//        $actState[] = $dailyAct;

        $trainingCamp = array(
            'actName' => 'TrainingCamp',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012112801/TrainingCamp.swf',
        );
        $actState[] = $trainingCamp;
        $newOrder = array(
            'actName' => 'newOrder',
            'backModuleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121401/NewOrder.swf',
        );
        $actState[] = $newOrder;
//         $Helper = array (
//        	'actName' => 'Helper',
//        	'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/helper.swf',
//        );
//		$actState[] = $Helper;
        $EveryDaySignIn = array(
            'actName' => 'EveryDaySignIn',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121401/EveryDaySignIn.swf',
            'menuUrl' => STATIC_HOST . '/' . SWF_VER . '/EveryDaySignBtn.swf',
            'menuClass' => 'EveryDaySignBtn',
            'menuIndex' => 0,
            'menuType' => 1
        );
        $actState[] = $EveryDaySignIn;
        $treasureBox = array(
            'actName' => 'TreasureBox',
            'backModuleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121301/TreasureBox.swf?v=2012080801',
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
        $frontRoleInfo = array(
            'actName' => 'FrontRoleInfo',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121001/FrontRoleInfo.swf',
        );
        $actState[] = $frontRoleInfo;
        //cdkey
        $cdkeyArray = array(
            'actName' => 'cdkey',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/cdkey.swf',
            'menuUrl' => STATIC_HOST . '/' . SWF_VER . '/cdkey.swf',
            'menuClass' => 'CdkeyAwardIcon',
        );
 		$actState[] = $cdkeyArray;
       
        $hellTower = array(
        	'actName'=>'HellTower',
        	'moduleUrl'=>STATIC_HOST . '/'.SWF_VER.'/v2012121401/HellTower.swf',
        );
        $actState[] = $hellTower;
        
         $hellTowerScene = array(
        	'actName'=>'HellTowerScene',
        	'moduleUrl'=>STATIC_HOST . '/'.SWF_VER.'/v2012121401/HellTowerScene.swf',
        );
        $actState[] = $hellTowerScene;
        
        $hellTowershop = array(
        	'actName'=>'HellTowerShop',
        	'moduleUrl'=>STATIC_HOST . '/'.SWF_VER.'/HellTowerShop.swf',
        );
        $actState[] = $hellTowershop; 
        $userDemond = Hapyfish2_Alchemy_Cache_EventGift::getUserDemoed($uid);  //获取次数
        if ($userDemond['award_count'] < 3) {
            $everyDaySendGemAct = array(
                'actName' => 'EveryDaySendGemAct',
                'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/EveryDaySendGemAct.swf',
                'menuUrl' => STATIC_HOST . '/' . SWF_VER . '/EveryDaySendGemActBtn.swf',
                'menuClass' => 'EveryDaySendGemActBtn',
                'menuType' => 3
            );
            $actState[] = $everyDaySendGemAct;
        }
        $userLoading = Hapyfish2_Alchemy_Cache_EventGift::getUserLoading($uid);
        if ($userLoading == 0) {
            $loadingGift = array(
                'actName' => 'LoadingGift',
                'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/loadingGift.swf',
                'menuUrl' => STATIC_HOST . '/' . SWF_VER . '/loadingGiftBtn.swf',
                'menuClass' => 'loadingGiftBtn',
                'menuType' => 1
            );
            $actState[] = $loadingGift;
        }
        $cashcowdata = array(
            'actName' => 'CashCowData',
            'backModuleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121401/CashCowData.swf',
        );

        $actState[] = $cashcowdata;
        $cashcow = array(
            'actName' => 'CashCow',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121401/CashCow.swf',
        );
        $actState[] = $cashcow;
        $specialShop = array(
        	'actName'=>'DailyDiscountGift',
        	'backModuleUrl'=>STATIC_HOST . '/'.SWF_VER.'/v2012121801/DailyDiscountGift.swf',
        );
        $actState[] = $specialShop; 
        
		//建筑升级TIPS
        $buildUpgradSelecter = array(
        		'actName'=>'buildUpgradSelecter',
        		'moduleUrl'=>STATIC_HOST . '/'.SWF_VER.'/v2012121401/buildUpgradSelecter.swf',
        );
        $actState[] = $buildUpgradSelecter;
        $Christmas = array(
            'actName' => 'Christmas',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012122001/Christmas.swf',
        );
        $actState[] = $Christmas;
        return $actState;
    }

}