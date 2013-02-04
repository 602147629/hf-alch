<?php

class Hapyfish2_Alchemy_Bll_Act_Qq
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
            'moduleData' => array('curOpenScene' => $aryWorldMap, 'bg' => STATIC_HOST . '/alchemy/image/worldmap/bg04.jpg?v=1.00')
        );
        $actState[] = $worldMapAct;

		//Battle
        $battleAct = array (
            'actName' => 'battle',
            'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012121801/battle.swf?v=2012081301'
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
		
//		 $dm = array (
//        	'actName' => 'DM',
//        	'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/DM.swf',
//        );
//		$actState[] = $dm; 
//		
		$eventgiftAct = array(
			'actName' => 'GuideGift',
			'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/GuideGift.swf?v=2012080801',
		);
		$actState[] = $eventgiftAct;
        $shopAct = array (
        	'actName' => 'shop',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012122001/shop.swf?v=2012080801'
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
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012122001/recoverHpMp.swf?v=2012080801'
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
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012122001/StrengthenEquipment.swf'
        );
        $actState[] = $strengthenEquipment;
        
        $actbtn = array (
	        	'actName' => 'ActBtn',
        	 	'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/ActBtn.swf',
       		 );
        $actState[] = $actbtn;
        
        $livenessTask = array (
        	'actName' => 'LivenessTask',
        	'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/LivenessTask.swf',
        );
        $actState[] = $livenessTask;

         $arena = array (
        	'actName' => 'arena',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/alchemyArena.swf'
         );
         $actState[] = $arena;
         
          $vipDayAward = array(
            'actName' => 'VIPEveryDay',
            'backModuleUrl' => STATIC_HOST . '/' . SWF_VER . '/VIPEveryDay.swf',
        );
        $actState[] = $vipDayAward;

	
        $dailyAct = array (
		        'actName' => 'Daily',
		        'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/Daily.swf?v=2012080801'
        );
        $actState[] = $dailyAct;
         
         $trainingCamp = array (
        	'actName' => 'TrainingCamp',
        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012121801/TrainingCamp.swf',
         );
         $actState[] = $trainingCamp;
         $newOrder = array(
				'actName'=>'newOrder',
         		'backModuleUrl'=> STATIC_HOST . '/'.SWF_VER.'/NewOrder.swf',
		);
		 $actState[] = $newOrder;
		 
		$treasureBox = array(
			'actName' => 'TreasureBox',
			'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/TreasureBox.swf?v=2012080801',
		);
		$actState[] = $treasureBox;
		
        $yellowAct = array (
		        'actName' => 'QQLevelGift',
		        'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/QQLevelGift.swf',
        );
        $actState[] = $yellowAct;
        
		$qqYellowVipDailyGiftBox = array(
				'actName' => 'dailyGiftHuangzhuan',
				'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012121801/dailyGiftHuangzhuan.swf',
		);
		$actState[] = $qqYellowVipDailyGiftBox;

		$qqYellowVipGuideGiftBox = array (
        	'actName' => 'QQGuideGift',
        	'backModuleUrl' => STATIC_HOST . '/'.SWF_VER.'/v2012121801/QQGuideGift.swf',
        );
		$actState[] = $qqYellowVipGuideGiftBox;
		 
//		$awardOfInvited = array (
//        		'actName' => 'awardOfInvited',
//	        	'moduleUrl' => STATIC_HOST . '/'.SWF_VER.'/awardOfInvited.swf',
//	        	'menuUrl'	=> STATIC_HOST . '/'.SWF_VER.'/awardOfInvited.swf',
//				'menuClass'	=>'happymagic.awardOfInvited.view.InviteAwardIcon',
//				'menuIndex'	=> 9996,
//	        	'menuType' =>3
//        );
//        $actState[] = $awardOfInvited;
        $EveryDaySignIn = array(
            'actName' => 'EveryDaySignIn',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/v2012121801/EveryDaySignIn.swf',
            'menuUrl' => STATIC_HOST . '/' . SWF_VER . '/EveryDaySignBtn.swf',
            'menuClass' => 'EveryDaySignBtn',
            'menuIndex' => 0,
            'menuType' => 1
        );
        $actState[] = $EveryDaySignIn;

        $frontRoleInfo = array(
            'actName' => 'FrontRoleInfo',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/FrontRoleInfo.swf',
        );
        $actState[] = $frontRoleInfo;
        $hellTower = array(
        	'actName'=>'HellTower',
        	'moduleUrl'=>STATIC_HOST . '/'.SWF_VER.'/HellTower.swf',
        );
        $actState[] = $hellTower;
        
         $hellTowerScene = array(
        	'actName'=>'HellTowerScene',
        	'moduleUrl'=>STATIC_HOST . '/'.SWF_VER.'/HellTowerScene.swf',
        );
        $actState[] = $hellTowerScene;
        
        $hellTowershop = array(
        	'actName'=>'HellTowerShop',
        	'moduleUrl'=>STATIC_HOST . '/'.SWF_VER.'/HellTowerShop.swf',
        );
        $actState[] = $hellTowershop; 
//        $userDemond = Hapyfish2_Alchemy_Cache_EventGift::getUserDemoed($uid);  //获取次数
//        if ($userDemond['award_count'] < 3) {
//            $everyDaySendGemAct = array(
//                'actName' => 'EveryDaySendGemAct',
//                'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/EveryDaySendGemAct.swf',
//                'menuUrl' => STATIC_HOST . '/' . SWF_VER . '/EveryDaySendGemActBtn.swf',
//                'menuClass' => 'EveryDaySendGemActBtn',
//                'menuType' => 3
//            );
//            $actState[] = $everyDaySendGemAct;
//        }
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
            'backModuleUrl' => STATIC_HOST . '/' . SWF_VER . '/CashCowData.swf',
        );

        $actState[] = $cashcowdata;
        $cashcow = array(
            'actName' => 'CashCow',
            'moduleUrl' => STATIC_HOST . '/' . SWF_VER . '/CashCow.swf',
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
        		'moduleUrl'=>STATIC_HOST . '/'.SWF_VER.'/buildUpgradSelecter.swf',
        );
        $actState[] = $buildUpgradSelecter;
		 return $actState;
		
		return $actState;
	}
}