<?php

$aryInitSwf = array(
		'decorfb1.swf',
		'floorfb1.swf',
		'mine1.swf',
		'portalfb1.swf',
		'dungeon.swf',
        'magicUi.swf',
        'decor1.swf',
        'door1.swf',
        'floor1.swf',
        'walldecor1.swf',
        'wall1.swf',
		'mixitem.swf',
        'music1.swf',
        'npc1.swf',
        'item1.swf',
        'iconclass1.swf',
        'bgicon1.swf',
        'levelplayerUp.swf',
		'DailyRecordViewUI.swf',
		'mainMenuUi.swf',
        'alchemyMainUi.swf',
        'rightMenuUi.swf',
        'storyUi.swf',
		'sceneSwitchMv.swf',
        'friendUi.swf',
		'specialBuildUi.swf',
        'HappyMagic.swf',
		'mix.swf',
        'diy.swf',
        'task.swf',
        'guide.swf',
        'promptFrame.swf',
        'SpecialBuildingTip.swf',
		'SceneSuggests.swf',
		'order.swf',
		'WorldMapIconSwc.swf',
		'fl.swf'
);


$otherSwf = array();

$aryInterface = array(
        'loadstatic' 		=> 'api/initstatic',
		'loadinit' 			=> 'api/inituserinfo',
        'loadFriend' 		=> 'api/getfriends',

       'enteredit' => 'bag/decorlist',
        'leaveedit' => 'building/saveedit',
        'transfer' => 'student/opendoor',
        'studymagic' => 'student/study',
        'interrupt' => 'student/interrupt',
        'pickup' => 'student/pickup',
        'saveGuides' => 'init/newbie',
        'changeMagicType' => 'magic/changetype',
        'finishTask' => 'event/award',
        'friendsHome' => 'friends/scene',
        'getSwitch' => 'deal/getdeal',
        'killEnemy' => 'scene/killmonster',
        'learnMagicClass' => 'magic/studyteach',
        'learnTrans' => 'magic/studytrans',
        'loadSwitchVo' => 'deal/readdeal',
        'mix' => 'magic/mixmagic',
        'moveScene' => 'scene/change',
        'putSwitch' => 'deal/adddeal',
        'roomUp' => 'scene/resize',
        'feedAward' => 'event/award',
        'switchCrystal' => 'deal/dodeal',
        'unlockScene' => 'scene/unlock',
        'upSwitchBag' => 'deal/upgrade',
        'useTrans' => 'magic/transmagic',
        'studentAward' => 'student/award',
		'GiftGetActInitStatic' => 'data/GiftGetActInitStaticCommand.txt',
		'GiftGetActInit' => 'data/GiftGetActInit.txt',
		'FriendRequest' => 'data/FriendRequestCommand.txt',
		'IgnoreGift' => 'data/IgnoreGiftCommand.txt',
		'ReceiveGift' => 'data/ReceiveGiftCommand.txt',
		'ReleaseMyWish' => 'data/ReleaseMyWishCommand.txt',
		'SendGift' => 'data/SendGiftCommand.txt',
		'SignAwardInitStatic' => 'SignAwardInitStaticCommand.txt',
		'SignAward' => 'SignAwardCommand.txt',
		'guideData' => 'data/guidedata.txt',

		//商城购买
        'buyItem' 			=> 'api/buyitem',

		//合成
        'mixItemStart' 		=> 'mix/startmix',
        'mixItemComplete' 	=> 'mix/completemix',
        'buyItems' 			=> 'api/buyitems',

        //战斗
        'BattleInit'	    => 'api/beginfight',
        'BattleEnd'	        => 'api/endfight',

        //探险
        'GoHome'	        => 'api/gohome',
        'GoScene'	        => 'api/entermap',
        'GatherMine'	    => 'api/gather',

        //任务
        'acceptTask'	    => 'task/accept',
        'completeTask'	    => 'task/complete',
        'getDaysTasks'	    => 'task/daily',
        'viewTask'	        => 'task/read',

        //礼物
    	'GiftGetActInitStatic'	=> 'gift/list',
    	'GiftGetActInit'	    => 'gift/user',
    	'FriendRequest'	        => 'gift/friendrequest',
    	'IgnoreGift'	        => 'gift/ignoregift',
    	'ReceiveGift'	        => 'gift/receivegift',
    	'SendGift'	            => 'gift/send',
    	'ReleaseMyWish'	        => 'gift/mywish',
    	'GiftGetKnowNew'	    => 'gift/hadread',

        //diy
        'diyAdd' => 'diy/add',
        'diyRemove' => 'diy/remove',
		'diyEdit' => 'diy/edit',

        //订单
        'requestOrder' => 'order/requestorder',
		'acceptOrder' => 'order/acceptorder',
        'completeOrder' => 'order/completeorder',
		'rejectOrder' => 'order/rejectorder',
		'refreshOrder' => 'order/refreshorder',

        //佣兵
        'initHire' => 'mercenary/gethire',
		'refreshHire' => 'mercenary/refreshhire',
        'completeHire' => 'mercenary/completehire',
		'hireRole' => 'mercenary/hirerole',
		'dismissRole' => 'mercenary/dismissrole',
        'replaceEquip' => 'mercenary/replaceequip',
		'stripAllEquip' => 'mercenary/stripallequip',
		'replaceSkill' => 'mercenary/replaceskill',
		'unlockSkill' => 'mercenary/unlockskill',
		'expandRoleLimit' => 'mercenary/expandrolelimit',
        'roleFormationSet' => 'mercenary/updatematrix',
		
		//派驻打工
		'loadRoleWorkStatic' 	=> 'mercenary/initmercenarywork',
		'loadRoleWorkInit' 		=> 'mercenary/getmercenarywork',
		'roleWroldStartWork' 	=> 'mercenary/startwork',
		'roleWorkFastComplete' 	=> 'mercenary/completework',
		'roleWorkGetAward' 		=> 'mercenary/getworkaward',
		'roleWorkEsc' 			=> 'mercenary/cancelwork',

		//剧情，对白
        'completeChat' => 'api/readdialog',

		//使用道具
        'useitem' => 'api/usecard',

		//临时显示剧情
        'showStory' 		=> 'api/showstory',

		//阅读新图鉴
        'readillustration' 		=> 'api/readnewillustration',

		//酒馆，自宅升级，主角晋级
        'initUpgrades' 		=> 'api/initspecialupgrade',
        'initRecover' 		=> 'api/initroleupgrade',
        'specialUpgrade' 		=> 'api/specialupgrade',
        'roleUpgradeQuality' 		=> 'mercenary/roleupgrade',
        'recoverHpMp' 		=> 'mercenary/resumehp',

		//交互，好友家加酒
        'addWine' 		=> 'api/addwine',

		//日志消息
        'initDaily' 		=> 'api/readfeed',

		//维修
		'fixEquip'				=> 'api/repairweapon',

		//强化
		'strengthenRequest'		=> 'mercenary/strthenstart',
		'saveAttribute'			=> 'mercenary/strthencomplete',
		
        //
        'TigerMachineInit' 		=> 'api/invadeready',
        'TigerMachineReady' 	=> 'api/invade',
        'openProtect' 		    => 'api/openprotect',
        'collectTax' 		    => 'api/collecttax',

		//
		'updateGuide'			=> 'api/updatehelp',
		'finishGuide'			=> 'api/completehelp',
		'funcUnlock'			=> 'api/unlockfunc',

		'GuideGiftInitStatic'	=>'gift/initeventgift',
		'GetAwardGuideGift'		=>'gift/receiveaward',
		'GuideGiftInit'			=>'gift/eventgiftinfo',
);

$aryModule = array(
    array(
            'name' => 'mainInfo',
            'className' => 'happymagic.display.view.maininfo.MainInfoView',
            'algin' => 'top_center',
            'x' => 0,
            'y' => 9,
            'fx' => 0,
            'fy' => -200,
            'mvType' => 'fromTop',
            'mvTime' => 0.5,
			'layer' => 2
    ),
    array(
            'name' => 'menu',
            'className' => 'happymagic.display.view.mainMenu.MainMenuView',
            'algin' => 'bottom_center',
            'x' => -376,
            'y' => 0,
            'fx' => 0,
            'fy' => 100,
            'mvType' => 'fromBottom',
            'mvTime' => 0.5,
			'layer' => 2,
			'useBounds' => false
    ),
    array(
        'name' => 'actMenu',
        'className' => 'happyfish.manager.actModule.display.ActMenuView',
        'algin' => 'top_center',
        'x' => 328,
        'y' => 200,
        'fx' => 400,
        'fy' => 0,
        'mvType' => 'fromRight',
        'mvTime' => 0.5,
		'layer' => 2
    ),
    array(
        'name' => 'rightMenu',
        'className' => 'happymagic.display.view.RightMenuView',
        'algin' => 'bottom_center',
        'x' => 300,
        'y' => 0,
        'fx' => 0,
        'fy' => -100,
        'mvType' => 'fromBottom',
        'mvTime' => 0.5,
		'layer' => 2
    ),

    array(
        'name' => 'rightCenterMenu',
        'className' => 'happymagic.display.view.RightCenterMenuView',
        'algin' => 'top_right',
        'x' => 42,
        'y' => 57,
        'fx' => 600,
        'fy' => 0,
        'mvType' => 'fromRight',
        'mvTime' => 0.5,
		'layer' => 2,
		'useBounds' => false
    ),

    array(
            "name"=>"dungeonInfo",
            "className"=>"happymagic.display.view.dungeon.DungeonInfoView",
            "algin"=>"bottom_center",
            "x"=>307,
            "y"=>86,
            "fx"=>0,
            "fy"=>-40,
            "mvType"=>"fromBottom",
            "mvTime"=>0.5,
			"layer"=>2
    ),
    
     array(
            "name"=>"protectUi",
            "className"=>"happymagic.display.ui.ProtectUI",
     		"algin"=>"top_center",
            "x"=>-300,
            "y"=>70,
            "fx"=>0,
            "fy"=>-40,
            "mvType"=>"fromTop",
            "mvTime"=>0.5,
			"layer"=>2
    ),
);

$otherModules = array(
	array(
		'name' => 'battle',
		'className' => 'happymagic.battle.BattleUISprite'
	),
    array(
        'name' => 'friend',
        'className' => 'happymagic.display.view.friends.FriendsView',
        'algin' => 'bottom_center',
        'x' => 0,
        'y' => -10,
        'fx' => 0,
        'fy' => -200,
        'mvType' => 'fromBottom',
        'mvTime' => 0.5
    ),
    array(
        'name' => 'buyItemPopUp',
        'className' => 'happymagic.display.view.ui.BuyItemPopUp'
    ),
    array(
    	'name' => 'BusinessLevelNoEnoughView',
    	'className' => 'happymagic.display.view.promptFrame.BusinessLevelNoEnoughView'
    ),
    array(
    	'name' => 'BusinessLevelUpView',
    	'className' => 'happymagic.display.view.promptFrame.BusinessLevelUpView'
    ),
    array(
    	'name' => 'CoinNoEnoughView',
    	'className' => 'happymagic.display.view.promptFrame.CoinNoEnoughView'
    ),
    array(
    	'name' => 'GemNoEnoughView',
    	'className' => 'happymagic.display.view.promptFrame.GemNoEnoughView'
    ),
    array(
    	'name' => 'NeedMoreItemView',
    	'className' => 'happymagic.display.view.promptFrame.NeedMoreItemView'
    ),
    array(
    	'name' => 'NeedMorePhysicalStrengthView',
    	'className' => 'happymagic.display.view.promptFrame.NeedMorePhysicalStrengthView'
    ),
    array(
    	'name' => 'happymagic.order.view::OrderDescriptionUISprite',
    	'className' => 'happymagic.order.view::OrderDescriptionUISprite'
    ),
    array(
    	'name' => 'TitleSprite',
        'x' => 150,
        'y' => 30,
    	'className' => 'happyfish.utils.display.TitleSprite'
    ),
    array(
    	'name' => 'GetNewItemsView',
    	'className' => 'happymagic.display.view.promptFrame.GetNewItemsView'
    ),
    array(
    	'name' => 'spBuildOpeate',
    	'algin' => 'alginNone',
    	'className' => 'happymagic.display.view.specialBuild.SpBuildOpeateView'
    )
);

$swfConfig = array(
	'initSwf' 		    => $aryInitSwf,
	'mainClass' 		=> 'HappyMagicMain',
    'otherSwf'			=> $otherSwf,
	'interfaces'	    => $aryInterface,
	'bgMusic'		    => 'bgm01.mp3?v=20111221v1',
    'modules' 		    => $aryModule,
    'otherModules' 		=> $otherModules
);