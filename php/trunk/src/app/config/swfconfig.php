<?php

$aryInitSwf = array(
        'dungeon.swf?v=2012080801',
        'magicUi.swf?v=2012080801',
        'mixitem.swf?v=2012080801',
        'music1.swf?v=2012080801',
        'levelplayerUp.swf?v=2012080801',
        'DailyRecordViewUI.swf?v=2012080801',
        'mainMenuUi.swf?v=2012080801',
        'alchemyMainUi.swf?v=2012080801',
        'rightMenuUi.swf?v=2012080801',
        'storyUi.swf?v=2012080801',
        'sceneSwitchMv.swf?v=2012080801',
        'friendUi.swf?v=2012080801',
        'specialBuildUi.swf?v=2012080801',
        'diy8.swf?v=2012080801',
        'sface1.swf?v=2012080801',
        'face1.swf?v=2012080801',
        'diy21.swf?v=2012080801',
        'diy35.swf?v=2012080801',
        'diy33.swf?v=2012080801',
        'diy34.swf?v=2012080801',
        'diy49.swf?v=2012080801',
        'diy58.swf?v=2012080801',
        'diy45.swf?v=2012080801',
        'diy47.swf?v=2012080801',
        'diy43.swf?v=2012080801',
        'diy59.swf?v=2012080801',
        'diy50.swf?v=2012080801',
        'diy51.swf?v=2012080801',
        'diy6.swf?v=2012080801',
        'diy53.swf?v=2012080801',
        'diy39.swf?v=2012080801',
        'diy46.swf?v=2012080801',
        'diy5.swf?v=2012080801',
        'play2.swf?v=2012080801',
        'villagefloor.swf?v=2012080801',
		'LinePower.swf?v=2012080801',

		'floor32.swf',
		'sface10.swf',
		'sface7.swf',
		'face6.swf',
		'decorfb12.swf',
		'decorfb9.swf',
		'decorfb13.swf',
		'decorfb15.swf',
		'decorfb14.swf',
		'decorfb11.swf',
		'decorfb16.swf',
		'decorfb2.swf',
		'decorfb3.swf',
		'decorfb10.swf',
		'shangdian1.swf',
		'zhujuewu1.swf',
		'jiuguan10.swf',
		'npc85.swf',
		'npc5.swf',
		'npc47.swf',
		'npc39.swf',
		'npc46.swf',
		'npc15.swf',
		'npc2.swf',
		'npc1.swf',
		'npc6.swf',
		'npc13.swf',
		'play10.swf',
		'play7.swf',
		'play6.swf',
		'npc22.swf',
		'facenpc22.swf',

        'HappyMagic.swf?v=20120080301',
		'TaskGuideArrow.swf',
        'StatisticsPlugin.swf?v=2012080801',
        'mix.swf?v=2012080801',
        'diy.swf?v=2012080801',
        'task.swf?v=2012080801',
        'guide.swf?v=2012081701',
        'promptFrame.swf?v=2012080801',
        'SpecialBuildingTip.swf?v=2012080801',
        'SceneSuggests.swf?v=2012080801',
//        'order.swf?v=2012080801',
        'WorldMapIconSwc.swf?v=2012080801',
		'TrainingCampData.swf?v=2012080801',
        'sound.swf?v=2012080801',
		'vip.swf?v=2012080801',
		'CDList.swf?v=2012080801',
);

$otherSwf = array(
        //STATIC_HOST. '/'. SWF_VER .'/fuhuodiejia1.swf?v=2012080801',
);

$aryInterface = array(
        'loadstatic'        => 'api/initstatic',
        'loadinit'          => 'api/inituserinfo',
        'loadFriend'        => 'api/getfriends',

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
        'buyItem'           => 'api/buyitem',

        //合成
        'mixItemStart'      => 'mix/startmix',
        'mixItemComplete'   => 'mix/completemix',
        'buyItems'          => 'api/buyitems',

        //战斗
        'BattleInit'        => 'api/beginfight',
        'BattleEnd'         => 'api/endfight',

        //探险
        'GoHome'            => 'api/gohome',
        'GoScene'           => 'api/entermap',
        'GatherMine'        => 'api/gather',

        //任务
        'acceptTask'        => 'task/accept',
        'completeTask'      => 'task/complete',
        'getDaysTasks'      => 'task/daily',
        'viewTask'          => 'task/read',

        //礼物
        'GiftGetActInitStatic'  => 'gift/list',
        'GiftGetActInit'        => 'gift/user',
        'FriendRequest'         => 'gift/friendrequest',
        'IgnoreGift'            => 'gift/ignoregift',
        'ReceiveGift'           => 'gift/receivegift',
        'SendGift'              => 'gift/send',
        'ReleaseMyWish'         => 'gift/mywish',
        'GiftGetKnowNew'        => 'gift/hadread',

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
        'getSkill' => 'mercenary/studyskill',
        
        //派驻打工
        'loadRoleWorkStatic'    => 'mercenary/initmercenarywork',
        'loadRoleWorkInit'      => 'mercenary/getmercenarywork',
        'roleWroldStartWork'    => 'mercenary/startwork',
        'roleWorkFastComplete'  => 'mercenary/completework',
        'roleWorkGetAward'      => 'mercenary/getworkaward',
        'roleWorkEsc'           => 'mercenary/cancelwork',

        //剧情，对白
        'completeChat' => 'api/readdialog',

        //使用道具
        'useitem' => 'api/usecard',

        //临时显示剧情
        'showStory'         => 'api/showstory',

        //阅读新图鉴
        'readillustration'      => 'api/readnewillustration',

        //酒馆，自宅升级，主角晋级
        'initUpgrades'      => 'api/initspecialupgrade',
        'initRecover'       => 'api/initroleupgrade',
        'specialUpgrade'        => 'api/specialupgrade',
        'roleUpgradeQuality'        => 'mercenary/roleupgrade',
        'recoverHpMp'       => 'mercenary/resumehp',

        //交互，好友家加酒
        //'addWine'       => 'api/addwine',

        //日志消息
        'initDaily'         => 'api/readfeed',

        //维修
        'fixEquip'              => 'api/repairweapon',

        //强化
        'strengthenRequest'     => 'mercenary/strthenstart',
        'saveAttribute'         => 'mercenary/strthencomplete',
        
        //
        'TigerMachineInit'      => 'api/invadeready',
        'TigerMachineReady'     => 'api/invade',
        'openProtect'           => 'api/openprotect',
        'collectTax'            => 'api/collecttax',

        //
        'updateGuide'           => 'api/updatehelp',
        'finishGuide'           => 'api/completehelp',
        'funcUnlock'            => 'api/unlockfunc',

        'GuideGiftInitStatic'   =>'gift/initeventgift',
        'GetAwardGuideGift'     =>'gift/receiveaward',
        'GuideGiftInit'         =>'gift/eventgiftinfo',

        'StrengthenEquipment'  =>'api/strengthenequipment',
        'StrengthenEquipmentInit'=>'api/initstrengthenequipment',
		'LivenessTaskInit' 		=>'task/getactivity',
		'LivenessTaskStatic'	=>'task/initactivity',
		'LivenessTaskGetAward'	=>'task/getactivityaward',
		'refreshInviteAward'	=>'gift/initinvite',
		'getInviteAward'		=>'gift/getinvitegift',
		'TestGift'				=>'gift/inittestgift',
		'TestGiftGetAward'		=>'gift/gettestgift',
        
        //竞技场1V1
        'initArena'     		=> 'arena/initarena',
        'scanOpponent'         	=> 'arena/getopponent',
        'challenge'         	=> 'arena/challenge',
        'initRankPanel'         => 'arena/initrank',
        'refreshArenaOpponents' => 'arena/refreshopponents',
        'getArenaBattleRecord' 	=> 'arena/getbattlerecord',
        'getArenaBattle' 		=> 'arena/readfight',
        'getArenaPrize' 		=> 'arena/getprize',
        'removeChallengeCD'		=> 'arena/completecd',

        //VIP每日礼包
        'VIPEveryDayGetAward'   => 'api/getvipdailyaward',
        'VIPEveryDayStatic'     => 'api/initvipdailyaward',
        'VIPEveryDayInit'     	=> 'api/checkvipdailyaward',
        
        //训练营
        'TrainingCampInit'   	=> 'training/inittraining',
        'TrainingCampInitStatic'=> 'training/inittrainingstatic',
        'TrainingCampStart'   	=> 'training/start',
        'TrainingCampAddPost'   => 'training/addpos',
        'TrainingCampEnd'   	=> 'training/complete',
        'TrainingCampImmediatelyEnd' => 'training/completecd',
		'Identity'				=>'api/getidentity',
		'AddLinePower'			=>'api/addsp',

		//黑心商人
		'sellItemsToBlackHeart'	=>'api/saleitems',
		
		//重置主角各属性资质
		'RoleReset'				=>'mercenary/resetrolequality',
		'RoleResetInit'			=>'mercenary/getrolequality',
		
		'extFunrnaceGrid' 		=> 'mix/expandfurnace',
		
        'upgradeSkill' 			=> 'mercenary/upgradeskill',
		'initNewOrderPanel'		=>'order/initorder',
		'completeNewOrder'	    =>'order/completeneworder',
		'refreshDungeon'		=>'api/refreshmap',
		//邀请宝箱
		'TreasureBox'			=>'api/openfriendbox',
		'TreasureBoxInit'		=>'api/initfriendbox',
		//vip猜价
		'VIPGuessInit'			=>'api/initvipguess',
		'VIPGuessGetAward'			=>'api/getguessaward',
		'VIPGuess'				=>'api/guessprice',
		'StrengthenResCoolTime'	=>'api/clearstrcooltime',
		
		'removeBuildingCd'		=>'api/removebuildingcd',
                  //loading礼包
                'LoadingGiftGetAward' =>'gift/loadingaward',
                //每日签到
                'EveryDaySignInInit'=>'gift/everysign',
                'EveryDaySignInGetAward'=>'gift/everysignvip',
                'EveryDaySignInRepair'=>'gift/everysignretroactive',
                'EveryDaySignIn'=>'gift/everysignday',
                'EveryDaySignInInitStatic'=>'gift/everysignlaba',
                'EveryDaySignInLaBaGetAward'=>'gift/everylabaaward',
		//建筑升级tips ，静态信息
		'buildUpgradeData' => 'api/buildupgradebasic',
                //圣诞树
                'ChristmasInit'=>'christmasevents/christmasinit',
                'ChristmasGetAward'=>'christmasevents/christmasgetaward',
                'ChristmasChange'=>'christmasevents/christmaschange',
                'ChristmasBlessing'=>'christmasevents/christmasblessing',
		
);

$aryModule = array(
    array(
            'name' => 'mainInfo',
            'className' => 'happymagic.display.view.maininfo.MainInfoView',
            'algin' => 'top_center',
            'x' => -366,
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
            'x' => -400,
            'y' => 0,
            'fx' => 0,
            'fy' => 100,
            'mvType' => 'fromBottom',
            'mvTime' => 0.5,
            'layer' => 2,
            'useBounds' => false
    ),
    array(
        'name' => 'rightMenu',
        'className' => 'happymagic.display.view.RightMenuView',
        'algin' => 'bottom_center',
        'x' => 407,
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
            "x"=>327,
            "y"=>126,
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
            "y"=>90,
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
        'x' => -365,
        'y' => -10,
        'fx' => 0,
        'fy' => -200,
        'mvType' => 'fromBottom',
        'mvTime' => 0.5
    ),
    array(
        'name' => 'buyItemPopUp',
        'startSoundClass'=>'OpenUiSpriteMp3',
        'endSoundClass'=>'CloseUiSpriteMp3',
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
        'x'=>0,
        'y'=>-5,
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
    ),
     array(
        'name' => 'transportUi',
        'algin' => 'center',
        'className' => 'happymagic.display.view.dungeon.TransportUI'
    ),
    array(
        'name' => 'openVipUISprite',
        'className' => 'happymagic.vip.view.OpenVipUISprite'
    ),
    array(
        'name' => 'openVipByOneCardUISprite',
        'className' => 'happymagic.vip.view.OpenVipByOneCardUISprite'
    ),
    array(
        'name' => 'vipDescriptionUISprite',
        'className' => 'happymagic.vip.view.VipDescriptionUISrpite'
    )
    
);

$swfConfig = array(
    'initSwf'           => $aryInitSwf,
    'mainClass'         => 'HappyMagicMain',
    'otherSwf'          => $otherSwf,
    'interfaces'        => $aryInterface,
    'bgMusic'           => 'bgm01.mp3?v=20111221v1',
    'modules'           => $aryModule,
    'otherModules'      => $otherModules
);