<?php

/*
 * 任务事件派发工具类
 */
class Hapyfish2_Alchemy_Bll_TaskMonitor
{

    //战斗胜利次数
	//$event = array('uid' => $uid, 'data' => 1)   number
	public static function winFight($event)
	{
info_log('triggle-type:winFight'.' data:'.json_encode($event), 'triggerTask');
		$type = 11;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //获得金币数量（只算增加部分）
	//$event = array('uid' => $uid, 'data' => 500)   number
	public static function coinGain($event)
	{
info_log('triggle-type:coinGain'.' data:'.json_encode($event), 'triggerTask');
		//$type = 11;
		$type = 22;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //扩店等级
	//$event = array('uid' => $uid, 'data' => 3)   number
	public static function enlargeHouse($event)
	{
info_log('triggle-type:enlargeHouse'.' data:'.json_encode($event), 'triggerTask');
		//$type = 11;
		$type = 23;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}


    //消灭目标怪数量
	//$event = array('uid' => $uid, 'data' => array(171=>2, 671=>1))  tid=> num
	public static function killMonster($event)
	{
info_log('triggle-type:killMonster'.' data:'.json_encode($event), 'triggerTask');
		$type = 12;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //消灭目标boss数量
	//$event = array('uid' => $uid, 'data' => array(1671=>1))     cid=> num
	public static function killBoss($event)
	{
info_log('triggle-type:killBoss'.' data:'.json_encode($event), 'triggerTask');
		//$type = 12;
        $type = 24;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}


    //采集得到目标道具数量（按个数）
	//$event = array('uid' => $uid, 'data' => array(212=>2, 313=>1)) cid=> num
	public static function hitMineGain($event)
	{
info_log('triggle-type:hitMineGain'.' data:'.json_encode($event), 'triggerTask');
		$type = 13;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //合成收获目标道具数量（按个数）
	//$event = array('uid' => $uid, 'data' => array(531=>2, 731=>1)) cid=> num
	public static function mixGain($event)
	{
info_log('triggle-type:mixGain'.' data:'.json_encode($event), 'triggerTask');
		//$type = 13;
		$type = 25;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //使用目标物品次数（带引导）
	//$event = array('uid' => $uid, 'data' => array(515=>1)) cid=> num
	public static function useItem($event)
	{
info_log('triggle-type:useItem'.' data:'.json_encode($event), 'triggerTask');
		$type = 14;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //学习一次目标合成术（带引导）
	//$event = array('uid' => $uid, 'data' => array(831=>1)) cid=> num
	public static function studyMix($event)
	{
info_log('triggle-type:studyMix'.' data:'.json_encode($event), 'triggerTask');
		//$type = 14;
		$type = 26;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //首次进入某个副本（带引导）>根据场景id
	//$event = array('uid' => $uid, 'data' => array(101=>1)) mapId=> num
	public static function firstEnterMap($event)
	{
info_log('triggle-type:firstEnterMap'.' data:'.json_encode($event), 'triggerTask');
		$type = 15;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //和某人对话（需要增加npc名字，任务语泡）
	//$event = array('uid' => $uid, 'data' => array(10=>1)) storyId=> num
	public static function storyNpcTalked($event)
	{
info_log('triggle-type:storyNpcTalked'.' data:'.json_encode($event), 'triggerTask');
		$type = 16;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //放置一次工作台，装饰（带引导）
	//$event = array('uid' => $uid, 'data' => array(141=>1)) cid=> num
	public static function diyFurnace($event)
	{
info_log('triggle-type:diyFurnace'.' data:'.json_encode($event), 'triggerTask');
		$type = 17;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //移动一次工作台，装饰（带引导）
	//$event = array('uid' => $uid, 'data' => array(141=>1)) cid=> num
	public static function moveFurnace($event)
	{
info_log('triggle-type:moveFurnace'.' data:'.json_encode($event), 'triggerTask');
		//$type = 17;
		$type = 27;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //更换一次技能（带引导）
	//$event = array('uid' => $uid, 'data' => 1)   number
	public static function changeSkill($event)
	{
info_log('triggle-type:changeSkill'.' data:'.json_encode($event), 'triggerTask');
		$type = 18;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //更换一次装备（带引导）
	//$event = array('uid' => $uid, 'data' => 1)   number
	public static function changeWeapon($event)
	{
info_log('triggle-type:changeWeapon'.' data:'.json_encode($event), 'triggerTask');
		//$type = 18;
		$type = 28;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //接一次订单（带引导）
    //$event = array('uid' => $uid, 'data' => 1)   number
	public static function acceptOrder($event)
	{
info_log('triggle-type:acceptOrder'.' data:'.json_encode($event), 'triggerTask');
		//$type = 18;
		$type = 29;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
		$type = 37;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //完成订单数量
	//$event = array('uid' => $uid, 'data' => 500)   number
	public static function completeOrder($event)
	{
info_log('triggle-type:completeOrder'.' data:'.json_encode($event), 'triggerTask');
		//$type = 18;
		$type = 30;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //合成收获道具次数（带引导）
	//$event = array('uid' => $uid, 'data' => 500)   number
	public static function completeMix($event)
	{
info_log('triggle-type:completeMix'.' data:'.json_encode($event), 'triggerTask');
		//$type = 18;
		$type = 31;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //发布一次礼物愿望
    //$event = array('uid' => $uid, 'data' => 1)   number
	public static function giftWish($event)
	{
info_log('triggle-type:giftWish'.' data:'.json_encode($event), 'triggerTask');
		//$type = 18;
		$type = 32;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //赠送好友一次礼物
    //$event = array('uid' => $uid, 'data' => 1)   number
	public static function giftSend($event)
	{
info_log('triggle-type:giftSend'.' data:'.json_encode($event), 'triggerTask');
		//$type = 18;
		$type = 33;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //移动一次傢具，装饰
	//$event = array('uid' => $uid, 'data' => 1)   number
	public static function moveDecor($event)
	{
info_log('triggle-type:moveDecor'.' data:'.json_encode($event), 'triggerTask');
		//$type = 18;
		$type = 34;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //雇佣佣兵（分职业和属性）（带引导）
    //$event = array('uid' => $uid, 'data' => array(2,1,9))   job,element,star
	public static function hireMercenary($event)
	{
info_log('triggle-type:hireMercenary'.' data:'.json_encode($event), 'triggerTask');
		$type = 19;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}


    //道具数量兑换（扣除）
	//$event = array('uid' => $uid, 'data' => array(212=>2, 313=>1)) cid=> num
	public static function exgStuffGain($event)
	{
info_log('triggle-type:exgStuffGain'.' data:'.json_encode($event), 'triggerTask');
		$type = 20;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}

    //访问好友次数
	//$event = array('uid' => $uid, 'data' => 10019)   friend uid
	public static function visitFriend($event)
	{
info_log('triggle-type:visitFriend'.' data:'.json_encode($event), 'triggerTask');
		$type = 21;
		$fid = (int)$event['data'];
		if (Hapyfish2_Alchemy_Cache_User::isAppUser($fid)) {
			Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
		}
	}

    //用户升级(经营)
	public static function levelUp($event)
	{
/*info_log('triggle-type:levelUp'.' data:'.json_encode($event), 'triggerTask');
		$type = 23;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);*/

		$uid = $event['uid'];
		$level = $event['data'];
		$userFight = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$fightLevel = $userFight['level'];

		/*$type = 33;
		$data = array('level' => $level);
		Hapyfish2_Alchemy_Bll_Task::listen($uid, $type, $data);*/

		$idsBuffer = array();
		$openTask = Hapyfish2_Alchemy_HFC_TaskOpen::getInfo($uid);
		//检查是否有以前遗留的等级未到任务
		if (!empty($openTask['buffer_list'])) {
			foreach ($openTask['buffer_list'] as $id) {
			    $basTaskInfo = Hapyfish2_Alchemy_Cache_Basic::getTaskInfo($id);
				if ($basTaskInfo['need_user_level']<=$level && $basTaskInfo['need_fight_level']<=$fightLevel) {
					$idsBuffer[] = (int)$id;
				}
			}
		}

		//检查是否有新任务触发，经营等级和战斗等级均满足的情况
		$idsNew = Hapyfish2_Alchemy_Cache_Basic::getTaskIdsByLevel($level, $fightLevel);
		if (empty($idsBuffer) && empty($idsNew)) {
			return;
		}

		Hapyfish2_Alchemy_Bll_Task_Base::addTask($uid, $openTask, $idsBuffer, $idsNew);
	}

    //用户升级(战斗)
	public static function fightLevelUp($event)
	{
		$uid = $event['uid'];
		$fightLevel = $event['data'];
		$level = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);

		$idsBuffer = array();
		$openTask = Hapyfish2_Alchemy_HFC_TaskOpen::getInfo($uid);
	    //检查是否有以前遗留的等级未到任务
		if (!empty($openTask['buffer_list'])) {
			foreach ($openTask['buffer_list'] as $id) {
			    $basTaskInfo = Hapyfish2_Alchemy_Cache_Basic::getTaskInfo($id);
				if ($basTaskInfo['need_user_level']<=$level && $basTaskInfo['need_fight_level']<=$fightLevel) {
					$idsBuffer[] = (int)$id;
				}
			}
		}

		//检查是否有新任务触发，经营等级和战斗等级均满足的情况
		$idsNew = Hapyfish2_Alchemy_Cache_Basic::getTaskIdsByLevel($level, $fightLevel);
		if (empty($idsBuffer) && empty($idsNew)) {
			return;
		}

		Hapyfish2_Alchemy_Bll_Task_Base::addTask($uid, $openTask, $idsBuffer, $idsNew);
	}
	
	public static function killRandomBoss($event)
	{
		info_log('triggle-type:giftSend'.' data:'.json_encode($event), 'triggerTask');
		//$type = 18;
		$type = 35;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}
	
	public static function hitRandomThing($event)
	{
		info_log('triggle-hitRandomThing'.' data:'.json_encode($event), 'triggerTask');
		//$type = 18;
		$type = 36;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}
	
	public static function inviteFriend($event)
	{
		info_log('triggle-hitRandomThing'.' data:'.json_encode($event), 'triggerTask');
		//$type = 18;
		$type = 38;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}
	
	public static function strengthenStart($event)
	{
		info_log('triggle-hitRandomThing'.' data:'.json_encode($event), 'triggerTask');
		//$type = 18;
		$type = 39;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}
	
	public static function strengthenequipment($event)
	{
		info_log('triggle-hitRandomThing'.' data:'.json_encode($event), 'triggerTask');
		//$type = 18;
		$type = 40;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}
	
	public static function refreshHire($event)
	{
		info_log('triggle-hitRandomThing'.' data:'.json_encode($event), 'triggerTask');
		//$type = 18;
		$type = 41;
		Hapyfish2_Alchemy_Bll_Task::listen($event['uid'], $type, $event['data']);
	}
}