<?php

class Hapyfish2_Alchemy_Bll_Task_Base
{

    /**
     *
     * complete daily task
     * @param integer $uid
     * @param array $dailyTask
     * @param array $taskInfo
     * @param integer $chkComplete 0-use rmb to complete task / 1-complete task
     * @return boolean
     */
	public static function checkDailyTask($uid, $dailyTask, $taskInfo, $chkComplete=1)
	{
	    $taskId = (int)$taskInfo['id'];
		if ($chkComplete == 1) {
    		if (empty($dailyTask['data']) || !isset($dailyTask['data'][$taskId])) {
    			return false;
    		}
    		$finish = $dailyTask['finish'];
    		if(in_array($taskId, $finish)){
    			return false;
    		}
		    $taskData = $dailyTask['data'][$taskId];
		    $conditionIds = json_decode($taskInfo['condition_ids'], true);
    	    //check each condition
    		foreach ($conditionIds as $condiId) {
    			$taskConditionInfo = Hapyfish2_Alchemy_Cache_Basic::getTaskConditionInfo($condiId);
    			$type = $taskConditionInfo['condition_type'];
    			$num = 0;
    			//condition complete number like 9/10
    			if (isset($taskData[$condiId])) {
    			    $num = $taskData[$condiId];
    			    //special type deal
    			    if ($type == 21) {
    					$num = count($taskData[$condiId]);
    				}
    			}

    			//condition not match
    			if ($taskConditionInfo['num'] && $num < $taskConditionInfo['num']) {
    			    return false;
    			}
    		}
		}

		//complete this task
	    $list = array();
		//complete open task
		$finish[] = $taskId;
		$dailyTask['finish'] = $finish;
	    $saveOK = Hapyfish2_Alchemy_HFC_TaskDaily::save($uid, $dailyTask, true);
	    if ($saveOK) {
    	    //set changes to auto output deal
    		Hapyfish2_Alchemy_Bll_UserResult::addTaskCompletedId($uid, $taskId);
    		//send awards
    		$awardRst = Hapyfish2_Alchemy_Bll_MapCopy::awardCondition($uid, json_decode($taskInfo['awards'], true), $changeResult);
	    }

	    return $saveOK;
	}

	/**
     *
     * check task can completed and complete it if need
     * @param integer $uid
     * @param array $openTask
     * @param array $taskInfo
     * @param integer $chkComplete 0-use rmb to complete task / 1-complete task
     * @return boolean
     */
	public static function check($uid, $openTask, $taskInfo, $chkComplete=1)
	{
		$taskId = (int)$taskInfo['id'];
		if (empty($openTask['data']) || !isset($openTask['data'][$taskId])) {
			$taskData = array();
		}
		else {
			$taskData = $openTask['data'][$taskId];
		}

		$consumeItem = array();
		if ($chkComplete == 1) {
    		$conditionIds = json_decode($taskInfo['condition_ids'], true);
    		//check each condition
    		foreach ($conditionIds as $condiId) {
    			$taskConditionInfo = Hapyfish2_Alchemy_Cache_Basic::getTaskConditionInfo($condiId);
    			$type = $taskConditionInfo['condition_type'];
    			$num = 0;
    			//condition complete number like 9/10
    			if (isset($taskData[$condiId])) {
    			    $num = $taskData[$condiId];
    				//special type deal
    			    if ($type == 21) {
    					$num = count($taskData[$condiId]);
    				}
    			}
    			//condition complete number special
    			else {
    				/*if ($type == 33) {
    					$num = self::getUserLevel($uid);
    				} else {
    					return false;
    				}
    				*/
    			}
    			//special type deal 消耗收集物类 特殊处理
    			if ($type == 20) {
    			    $consumeCid = $taskConditionInfo['cid'];
    			    $num = Hapyfish2_Alchemy_Bll_BagItemDict::getItemCntByCid($uid, $consumeCid);
    			    $consumeItem[$consumeCid] = (int)$taskConditionInfo['num'];
    			}

    			//condition not match
    			if ($taskConditionInfo['num'] && $num < $taskConditionInfo['num']) {
    			    return false;
    			}
    		}
		}
		//special type deal 消耗收集物类 特殊处理
	    if ($consumeItem) {
	        foreach ($consumeItem as $cid=>$cnt) {
	            Hapyfish2_Alchemy_Bll_BagItemDict::consumeItemByCid($uid, $cid, $cnt);
	        }
		}

		//complete this task
	    $list = array();
		foreach ($openTask['list'] as $tid) {
			if ($tid != $taskId) {
				$list[] = $tid;
			}
		}
		$openTask['list'] = $list;
		unset($openTask['data'][$taskId]);

		//if had already completed, delete fake data
		$isCompleted = Hapyfish2_Alchemy_Cache_Task::isCompletedTask($uid, $taskId);
		if ($isCompleted) {
			Hapyfish2_Alchemy_HFC_TaskOpen::update($uid, $openTask);
			//set changes to auto output deal
			Hapyfish2_Alchemy_Bll_UserResult::addTaskDeletedId($uid, $taskId);
			return false;
		}

		//complete task list log update
		$ok = Hapyfish2_Alchemy_Cache_Task::completeTask($uid, $taskId);
		if ($ok) {
		    //complete open task
		    $saveOK = Hapyfish2_Alchemy_HFC_TaskOpen::save($uid, $openTask, true);
		    //set changes to auto output deal
			Hapyfish2_Alchemy_Bll_UserResult::addTaskCompletedId($uid, $taskId);

			//send awards
			$userLevelBefore = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
			Hapyfish2_Alchemy_Bll_MapCopy::awardCondition($uid, json_decode($taskInfo['awards'], true), $changeResult);
			$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
			if ($changeResult['exp']) {
			    //level up ed (refresh to read task open)
                if ($userLevelBefore != $userLevel) {
                    $openTask = Hapyfish2_Alchemy_HFC_TaskOpen::getInfo($uid);
                }
			}

			//trigger story or talk
            Hapyfish2_Alchemy_Bll_Task::triggerStory($uid, $taskInfo['story']);

            $needTrigLev = false;
			//find and get next task to prepare task
			$nextTaskList = json_decode($taskInfo['next_task_id'], true);
			if (!empty($nextTaskList)) {
				//$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
				$userHomeLevel = Hapyfish2_Alchemy_HFC_User::getUserHomeLevel($uid);
				$userFight = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
				foreach ($nextTaskList as $ntid) {
					$taskInfo1 = Hapyfish2_Alchemy_Cache_Basic::getTaskInfo($ntid);
					if ($taskInfo1 && $taskInfo1['label']!=3) {
	    				$frontTaskList = json_decode($taskInfo1['front_task_id'], true);
	    				$newTask = true;
	    				//check if front task had all completed
	    				foreach ($frontTaskList as $id) {
							$ok0 = Hapyfish2_Alchemy_Cache_Task::isCompletedTask($uid, $id);
							if (!$ok0) {
								$newTask = false;
								break;
							}
	    				}

	    				if ($newTask) {
	    					//is level enough
	    					if ($taskInfo1['need_user_level'] && $userLevel < $taskInfo1['need_user_level']) {
	    						//等级没到要求则放入buffer_list
	    						//$openTask['buffer_list'][$ntid] = $taskInfo1['need_user_level'];
	    						$openTask['buffer_list'][] = $ntid;
	    					}
	    					else if ($taskInfo1['need_fight_level'] && $userFight['level'] < $taskInfo1['need_fight_level']) {
                                $openTask['buffer_list'][] = $ntid;
	    					}
	    					else {
	    					    $status = 1;//1 未领 2 已领未查看 3 已查看
	    					    //check prepare task need auto accept( if need list2->list auto accept)
	    					    if (4 == $taskInfo1['from_type']) {
	    					        //new atuo accept task
	    					        $openTask['list'][] = $ntid;
	    					        $status = 2;

	    					        //trigger story or talk
                                    Hapyfish2_Alchemy_Bll_Task::triggerStory($uid, $taskInfo1['accept_story']);
	    					    }
	    					    else {
	    					        //new prepare task
	    						    $openTask['list2'][] = $ntid;
	    					    }
	    					    //set changes to auto output deal
	    					    $newTaskVo = Hapyfish2_Alchemy_Bll_Task::genTaskVo($uid, $ntid, $taskInfo1);
	    					    $newTaskVo['state'] = $status;
	    						Hapyfish2_Alchemy_Bll_UserResult::addTaskNew($uid, $newTaskVo);

	    						//check is level up condition( continuously lev up )
	    						if (4 == $taskInfo1['from_type']) {
	    						    $conditionIds1 = json_decode($taskInfo1['condition_ids'], true);
                            		//check each condition
                            		foreach ($conditionIds1 as $condiId) {
                            			$taskConditionInfo1 = Hapyfish2_Alchemy_Cache_Basic::getTaskConditionInfo($condiId);
                            			$type1 = $taskConditionInfo1['condition_type'];
                            			if ($type1 == 23) {
                            			    $needTrigLev = $userHomeLevel;
                                            break;
                            			}
                            		}

	    						}
	    					}
	    				}
	    			}
				}//end for next task list
			}//end if has next task list

			//save task info
			$saveOK = Hapyfish2_Alchemy_HFC_TaskOpen::save($uid, $openTask);

			//continuously lev up
			if ($needTrigLev) {
			    Hapyfish2_Alchemy_Bll_Task::listen($uid, 23, $needTrigLev);
			}

			//任务触发新手引导
			$newHelpId = 0;
			$taskTriHelp = array('3511' => 6,
								 '3591' => 7,
								 '3661' => 10,
								 '3611' => 11,
								 '3691' => 12,
								 '5831' => 15,
                                 '3821' => 17);
			if ( isset($taskTriHelp[$taskId]) ) {
				$newHelpId = $taskTriHelp[$taskId];
			}
			if ( $newHelpId > 0 ) {
                Hapyfish2_Alchemy_Bll_Help::startHelp($uid, $newHelpId);
			}
		}

		return $ok;
	}

	/**
     *
     * add buffer/limited task when level up or etc
     * @param integer $uid
     * @param array $openTask
     * @param array $idsBuffer
     * @param array $idsNew
     * @return boolean
     */
	public static function addTask($uid, &$openTask, $idsBuffer, $idsNew)
	{
		$addIds = array();
		if (!empty($idsBuffer)) {
			foreach ($idsBuffer as $tid) {
			    if (substr($tid, -1) != 3) {
			        $addIds[$tid] = 1;
			    }
			}
		}

		if (!empty($idsNew)) {
			//已完成任务列表
			$finishTasks = Hapyfish2_Alchemy_Cache_Task::getIds($uid);
			foreach ($idsNew as $tid) {
		    	if (is_array($finishTasks) && in_array($tid, $finishTasks)) {
		    		
		    	}
		    	else if ( in_array($tid, $openTask['list']) ) {
		    		
		    	}
		    	else if ( in_array($tid, $openTask['list2']) ) {
		    		
		    	}
		    	else if ( in_array($tid, $openTask['buffer_list']) ) {
		    		
		    	}
		    	else {
				    if (substr($tid, -1) != 3 && !array_key_exists($tid, $addIds)) {
					    $addIds[$tid] = 1;
				    }
		    	}
			}
		}

		if ($addIds) {
    		foreach ($addIds as $tid=>$val) {
    	    	$taskInfo = Hapyfish2_Alchemy_Cache_Basic::getTaskInfo($tid);
    			$status = 1;//1 未领 2 已领未查看 3 已查看
    		    //check prepare task need auto accept( if need list2->list auto accept)
    		    if (4 == $taskInfo['from_type']) {
    		        //new atuo accept task
    		        $openTask['list'][] = $tid;
    		        $status = 2;

    		        //trigger story or talk
                    Hapyfish2_Alchemy_Bll_Task::triggerStory($uid, $taskInfo['accept_story']);
    		    }
    		    else {
    		        //new prepare task
    			    $openTask['list2'][] = $tid;
    		    }
    		    //set changes to auto output deal
    		    $newTaskVo = Hapyfish2_Alchemy_Bll_Task::genTaskVo($uid, $tid, $taskInfo);
    		    $newTaskVo['state'] = $status;
    			Hapyfish2_Alchemy_Bll_UserResult::addTaskNew($uid, $newTaskVo);
    		}
		    return Hapyfish2_Alchemy_HFC_TaskOpen::save($uid, $openTask, true);
		}

		return true;
	}


	public static function getActiveLoginNum($uid)
	{
		$loginInfo = Hapyfish2_Alchemy_HFC_User::getUserLoginInfo($uid);
		if (!$loginInfo || !isset($loginInfo['active_login_count'])) {
			return 0;
		}

		return $loginInfo['active_login_count'];
	}

	public static function getUserLevel($uid)
	{
		$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
		if (!$userLevel) {
			return 0;
		}

		return $userLevel;
	}
}