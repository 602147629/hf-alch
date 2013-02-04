<?php

class Hapyfish2_Alchemy_Bll_Story
{
	/**
	 * 触发剧情，脚本模块
	 * @param int $uid
	 * @param int $id，脚本id
	 */
	public static function startStory($uid, $id, $isTest = false)
	{
		//玩家已浏览剧情列表
		$userStoryList = Hapyfish2_Alchemy_HFC_Story::getStory($uid);
		if ( !$isTest ) {
			if ( isset($userStoryList[$id]) ) {
				return -200;
			}
		}
	
		//剧情信息
		$storyInfo = Hapyfish2_Alchemy_Cache_Basic::getStory($id);
		if (!$storyInfo) {
			return -200;
		}
		$npcIds = split(',', $storyInfo['npc_ids']);
		$actionIds = split(',', $storyInfo['action_ids']);
		
		$actorList = array();
		if ( $storyInfo['npc_ids'] != null ) {
			foreach ( $npcIds as $npc ) {
				if ( $npc == 0 ) {
					//主角信息
		        	$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		        	$actorList[] = array('id' => 0,
										 'className' => $userMercenary['class_name'],
										 'name' => $userMercenary['name'],
										 'head' => $userMercenary['face_class_name']);
				}
				else {
					$npcInfo = Hapyfish2_Alchemy_Cache_Basic::getStoryNpc($npc);
					$actorList[] = array('id' => $npcInfo['nid'],
										 'className' => $npcInfo['class_name'],
										 'name' => $npcInfo['name'],
										 'head' => $npcInfo['head']);
				}
			}
		}
		
		$actions = array();
		if ( $storyInfo['action_ids'] != null ) {
			foreach ( $actionIds as $action ) {
				$actionInfo = Hapyfish2_Alchemy_Cache_Basic::getStoryAction($action);
				$actions[] = array('npcId' => $actionInfo['nid'],
								   'x' => $actionInfo['x'],
								   'y' => $actionInfo['y'],
								   'faceX' => $actionInfo['faceX'],
								   'faceY' => $actionInfo['faceY'],
								   'content' => $actionInfo['content'],
								   'camera' => $actionInfo['camera'],
								   'wait' => $actionInfo['wait'],
								   'immediately' => $actionInfo['immediately'],
								   'hide' => $actionInfo['hide'],
								   'className' => $actionInfo['class_name'],
								   'shockScreenTime' => $actionInfo['shock_screen_time'],
								   'actionLabel' => $actionInfo['action_label'],
								   'labelTimes' => $actionInfo['label_times'],
								   'toStop' => $actionInfo['to_stop'],
								   'chatTime' => $actionInfo['chat_time']);
			}
		}
		
		$addItems = json_decode($storyInfo['items']);
		$storyVo = array('id' => $storyInfo['sid'],
						 'sceneId' => $storyInfo['scene_id'],
						 'endAt' => $storyInfo['end_at'],
						 'coin' => $storyInfo['coin'],
						 'gem' => $storyInfo['gem'],
						 'items' => $addItems,
						 'actorList' => $actorList,
						 'actions' => $actions);

		if ( $id == 21 ) {
			//剧情触发战斗
			$battle = Hapyfish2_Alchemy_Bll_Help::guideFight($uid);
			$storyVo['battleData'] = array('BattleVo' => $battle['BattleVo'], 'RndNums' => $battle['RndNums']);
		}
		
		if ( $storyInfo['next_sid'] > 0 ) {
			$storyVo['nextStoryId'] = $storyInfo['next_sid'];
		}
		
		$ok = Hapyfish2_Alchemy_HFC_Story::gainStory($uid, $id);
		if ($ok) {
			if ( $storyVo['coin'] > 0 ) {
				Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $storyVo['coin']);
			}
			if ( $storyVo['gem'] > 0 ) {
				$gemInfo = array('gem' => $storyVo['gem']);
				Hapyfish2_Alchemy_Bll_Gem::add($uid, $gemInfo);
			}
			//$items = array(cid,num,status):道具cid，个数，状态：默认不填或0、1-耐久度为0的武器
			foreach ( $addItems as $items) {
				if ( !isset($items[2]) ) {
					Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid,	$items[0], $items[1]);
				}
				else {
					for ( $i=0;$i<$items[1];$i++ ) {
						Hapyfish2_Alchemy_HFC_Weapon::addUserDamagedWeapon($uid, $items[0]);
					}
				}
			}
		}
		
		//剧情触发任务
		if ( $storyInfo['task_id'] > 0 ) {
			Hapyfish2_Alchemy_Bll_Task::acceptTask($uid, $storyInfo['task_id']);
		}
		
		$newHelpId = 0;
		//剧情触发新场景
		if ( $storyInfo['open_scene'] > 0 ) {
			Hapyfish2_Alchemy_Bll_WorldMap::setWorldMapOpened($uid, $storyInfo['open_scene']);
		}
		
		//剧情触发新手引导：设置角色，装备，布阵
		if ( $id == 111 ) {
			$newHelpId = 9;
		}
		else if ( $id == 221 ) {
			$newHelpId = 14;
		}
		if ( $newHelpId > 0 ) {
            Hapyfish2_Alchemy_Bll_Help::startHelp($uid, $newHelpId);
		}
		
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'story', $storyVo);
	
		$npcListChange = false;
		//剧情触发NPC消失
		$removePerson = explode(',', $storyInfo['remove_person']);
		if ( !empty($removePerson) && $removePerson != array(0) ) {
			foreach ( $removePerson as $r ) {
				Hapyfish2_Alchemy_Bll_Person::removePerson($uid, $r);
				$npcListChange = true;
			}
		}
		//剧情触发新NPC出现
		$addPerson = explode(',', $storyInfo['add_person']);
		if ( !empty($addPerson) && $addPerson != array(0) ) {
			foreach ( $addPerson as $a ) {
				Hapyfish2_Alchemy_Bll_Person::addPerson($uid, $a);
				$npcListChange = true;
			}
		}
		
		if ( $npcListChange ) {
			//对白NPC
			$userVo = Hapyfish2_Alchemy_HFC_User::getUserScene($uid);
			$npcVo = Hapyfish2_Alchemy_Bll_Story::getNpcVo($uid, $userVo['cur_scene_id']);
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'npcListChange', $npcVo);
		}
		
		//剧情触发英雄佣兵退出
		$removeHero = explode(',', $storyInfo['remove_hero']);
		if ( !empty($removeHero) && $removeHero != array(0) ) {
			foreach ( $removeHero as $rh ) {
				Hapyfish2_Alchemy_Bll_Hero::removeHeroMercenary($uid, $rh);
			}
		}
		//剧情触发英雄佣兵加入
		$addHero = explode(',', $storyInfo['add_hero']);
		if ( !empty($addHero) && $addHero != array(0) ) {
			foreach ( $addHero as $ah ) {
				Hapyfish2_Alchemy_Bll_Hero::addHeroMercenary($uid, $ah);
			}
		}
		
		return $storyVo;
	}

	/**
	 * 读取场景中对白信息，npcvo
	 * @param int $uid
	 * @param int $sceneId,场景id
	 */
	public static function getNpcVo($uid, $sceneId)
	{
		//玩家当前对白列表
		$userDialogList = Hapyfish2_Alchemy_HFC_Story::getDialog($uid);
	
        $npcTemp = array();
		if (isset($userDialogList[$sceneId])) {
	        $list = $userDialogList[$sceneId];
	        
	        foreach ( $list as $k => $v ) {
	            $dialogInfo = Hapyfish2_Alchemy_Cache_Basic::getStoryDialog($k);
	            if ( $dialogInfo ) {
	                $nid = $dialogInfo['nid'];
	                $npcTemp[$nid] = array('id' => $nid,
	                                 'chatId' => $dialogInfo['id'],
	                                 'chats' => $dialogInfo['dialog'],
	                                 'chatState' => $v);    //是否已阅读,1:未阅读,2:已阅读
	            }
	        }
		}
	
		//玩家动态NPC显示列表
		//指定地图NPC列表
		$basicPersonList = Hapyfish2_Alchemy_Cache_Basic::getPersonListByMap($sceneId);
		//玩家动态NPC信息
		$userPerson = Hapyfish2_Alchemy_HFC_Person::getPerson($uid);
	
		$userPersonIds = array();
		if ( !empty($userPerson) ) {
			$userPersonIds = array_keys($userPerson);
		}
        
		foreach ( $basicPersonList as $person ) {
			if ( !empty($userPersonIds) && in_array($person['id'], $userPersonIds) ) {
				$nid = $person['id'];
				if ( isset($npcTemp[$nid]) ) {
					$npcTemp[$nid]['exist'] = (string)$userPerson[$nid];
				}
				else {
					$npcTemp[$nid] = array('id' => $nid,
									 'exist' => (string)$userPerson[$nid]);
				}
			}
		}
		
		$npcVo = array();
		foreach ( $npcTemp as $t ) {
			$npcVo[] = $t;
		}
		return $npcVo;
	}
	
	/**
	 * 阅读对白
	 * @param int $uid
	 * @param int $id
	 */
	public static function readDialog($uid, $id)
	{	
		$dialogInfo = Hapyfish2_Alchemy_Cache_Basic::getStoryDialog($id);
		if (!$dialogInfo) {
			return -200;
		}
		
		//玩家当前对白列表
		$userDialogList = Hapyfish2_Alchemy_HFC_Story::getDialog($uid);
		$sceneId = $dialogInfo['scene_id'];
		if (!isset($userDialogList[$sceneId])) {
			return -200;
		}
		$list = $userDialogList[$sceneId];
		$listKeys = array_keys($list);
		if (!in_array($id, $listKeys)) {
			return -200;
		}
		
		if ( $list[$id] == 2 ) {
			return;
		}
		
		//战斗胜利后才更新对白信息的部分
    	if ( $dialogInfo['fight_id'] > 0 && $dialogInfo['fight_type'] == 1 ) {
    		
    	}
    	else {
			$list[$id] = 2;
			$userDialogList[$sceneId] = $list;
			$ok = Hapyfish2_Alchemy_HFC_Story::updateDialog($uid, $userDialogList);
			if (!$ok) {
				return -200;
			}
			
			//触发新任务
			if ( $dialogInfo['task_id'] > 0 ) {
				Hapyfish2_Alchemy_Bll_Task::acceptTask($uid, $dialogInfo['task_id']);
			}
			
			//触发任务记录
			//$event = array('uid' => $uid, 'data' => array(10=>1)) storyId=> num
	        $event = array('uid' => $uid, 'data' => array($id=>1));
	    	Hapyfish2_Alchemy_Bll_TaskMonitor::storyNpcTalked($event);
    	}
    	
    	//对白触发战斗
    	if ( $dialogInfo['fight_id'] > 0 ) {
			$battle = Hapyfish2_Alchemy_Bll_Fight::dialogFight($uid, $dialogInfo['fight_id'], $id, $dialogInfo['fight_type']);
			$battleData = array('BattleVo' => $battle['BattleVo'], 'RndNums' => $battle['RndNums']);
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'battleData', $battleData);
    	}
    	
	    //对白NPC
        $npcVo = Hapyfish2_Alchemy_Bll_Story::getNpcVo($uid, $sceneId);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'npcListChange', $npcVo);
		
		return 1;
	}
	
	/**
	 * 完成对白触发战斗
	 * @param int $uid
	 * @param int $id
	 */
	public static function completeFightDialog($uid, $info, $rst)
	{
		$enemyId = $info['enemy_id'];
		$fightType = substr($enemyId, 0, 1);
		$dialogId = substr($enemyId, 2);
		if ( $fightType == 1 ) {
			$dialogInfo = Hapyfish2_Alchemy_Cache_Basic::getStoryDialog($dialogId);
			if (!$dialogInfo) {
				return;
			}
			//玩家当前对白列表
			$userDialogList = Hapyfish2_Alchemy_HFC_Story::getDialog($uid);
			$sceneId = $dialogInfo['scene_id'];
			if (!isset($userDialogList[$sceneId])) {
				return;
			}
			$list = $userDialogList[$sceneId];
			$list[$dialogId] = 2;
			$userDialogList[$sceneId] = $list;
			$ok = Hapyfish2_Alchemy_HFC_Story::updateDialog($uid, $userDialogList);
			if (!$ok) {
				return;
			}
			
		    //对白NPC
	        $npcVo = Hapyfish2_Alchemy_Bll_Story::getNpcVo($uid, $sceneId);
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'npcList', $npcVo);
		}
		return;
	}
	
	/**
	 * 触发对白,指定id
	 * @param int $uid
	 * @param int $id,对白id
	 */
	public static function triggerDialogById($uid, $id)
	{
		$dialogInfo = Hapyfish2_Alchemy_Cache_Basic::getStoryDialog($id);
		if (!$dialogInfo) {
			return -200;
		}
		
		$newDialog = array();
		$newDialog[$id] = $dialogInfo;
		self::triggerDialog($uid, $newDialog);
	}
	
	//用户等级
	public static function triggerDialogByUserLevel($uid, $level)
	{
		$dialogList = Hapyfish2_Alchemy_Cache_Basic::getStoryDialogByUserLevel($level);
		$newDialog = array();
		foreach ( $dialogList as $dialog ) {
			$newDialog[$dialog['id']] = $dialog;
		}
		if (empty($newDialog)) {
			return;
		}
		self::triggerDialog($uid, $newDialog);
	}
	//冒险等级
	public static function triggerDialogByFightLevel($uid, $level)
	{
		$dialogList = Hapyfish2_Alchemy_Cache_Basic::getStoryDialogByFightLevel($level);
		$newDialog = array();
		foreach ( $dialogList as $dialog ) {
			$newDialog[$dialog['id']] = $dialog;
		}
		if (empty($newDialog)) {
			return;
		}
		self::triggerDialog($uid, $newDialog);
	}
	
	public static function triggerDialog($uid, $list)
	{
		//玩家当前对白列表
		$userDialogList = Hapyfish2_Alchemy_HFC_Story::getDialog($uid);
		
		foreach ( $list as $k => $newDialog ) {
			$dialogList = array();
			if (isset($userDialogList[$newDialog['scene_id']])) {
				$dialogList = $userDialogList[$newDialog['scene_id']];
				foreach ( $dialogList as $i => $v ) {
					$oldDialog = Hapyfish2_Alchemy_Cache_Basic::getStoryDialog($i);
					if ( $oldDialog['nid'] == $newDialog['nid'] ) {
						unset($dialogList[$i]);
					}
				}
			}
			$newId = (int)$newDialog['id'];
			$dialogList[$newId] = 1;
			$sceneId = (int)$newDialog['scene_id'];
			$userDialogList[$sceneId] = $dialogList;
		}
		Hapyfish2_Alchemy_HFC_Story::updateDialog($uid, $userDialogList);
		
		$userVo = Hapyfish2_Alchemy_HFC_User::getUserScene($uid);
		$npcVo = Hapyfish2_Alchemy_Bll_Story::getNpcVo($uid, $userVo['cur_scene_id']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'npcListChange', $npcVo);
	}
	
	/**
	 * 进入不同场景触发剧情
	 * @param int $uid
	 * @param int $mapId,场景id
	 */
	public static function startStoryByMapId($uid, $mapId)
	{
		$storyId = 0;
        if ( $mapId == 101 ) {
        	$storyId = 81;
        }
        else if ( $mapId == 105 ) {
        	$storyId = 91;
        }
        else if ( $mapId == 107 ) {
        	$storyId = 111;
        }
        else if ( $mapId == 109 ) {
        	$storyId = 121;
        }
        else if ( $mapId == 108 ) {
        	$storyId = 131;
        }
        else if ( $mapId == 111 ) {
        	$storyId = 161;
        }
        else if ( $mapId == 113 ) {
        	$storyId = 171;
        }
        /*else if ( $mapId == 115 ) {
        	$storyId = 181;
        }*/
        
        if ( $storyId > 0 ) {
        	self::startStory($uid, $storyId);
        }
        return;
	}
	
	/**
	 * 击杀怪物触发剧情
	 * @param int $uid
	 * @param int $monsterId,怪物id
	 * @param int $isWin,本场战斗是否胜利:1-胜利;0-失败
	 */
	public static function startStoryByMonsterId($uid, $monsterId, $isWin)
	{
		$storyId = 0;
		if ( $monsterId == 1 ) {//强盗
			
		}
		else if ( $monsterId == 171 ) {
			$storyId = 101;
		}
		/*else if ( $monsterId == 1671 ) {
			$storyId = 141;
		}
		else if ( $monsterId == 2771 ) {
			$storyId = 191;
		}*/
		
        if ( $storyId > 0 ) {
        	self::startStory($uid, $storyId);
        }
        return;
	}
}