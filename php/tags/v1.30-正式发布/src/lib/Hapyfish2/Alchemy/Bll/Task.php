<?php

/**
 * task
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/04    zx
 */
class Hapyfish2_Alchemy_Bll_Task
{

    private static $_taskTypeLogicMap = array(
                11 => 11, 22 => 11,
                12 => 12, 24 => 12,
                13 => 13, 25 => 13,
                14 => 14, 26 => 14,
                15 => 15,
                16 => 16,
                17 => 17, 27 => 17,
                18 => 18, 28 => 18, 29 => 18, 30 => 18, 31 => 18, 32 => 18, 33 => 18, 34 => 18,35 => 18,36 => 18,37 => 18,38 => 18,39 => 18,40 => 18,41 => 18,
                19 => 19,
                20 => 20,
                21 => 21,
                23 => 23
    );

    /**
     *
     * generate task vo
     * @param integer $uid
     * @param integer $tid
     * @param array $basTaskInfo
     * @param array $taskProcAll
     * @param array $readStatus
     * @return array taskVo
     */
    public static function genTaskVo($uid, $tid, $basTaskInfo=null, $taskProcAll=null, $readStatus=null)
    {
        $taskVo = array();
        if (!$basTaskInfo) {
            $basTaskInfo = Hapyfish2_Alchemy_Cache_Basic::getTaskInfo($tid);
        }
        if ($basTaskInfo) {
            if ($taskProcAll) {
                //current task complete process data
                $curTaskProc = null;
                if (isset($taskProcAll[$tid])) {
                    $curTaskProc = $taskProcAll[$tid];
                }
            }
            
            $status = 1;//1 未领 2 已领未查看 3 已查看
            if ($readStatus) {
                $status = 2;
                if (isset($readStatus[$tid]) && $readStatus[$tid]) {
                    $status = 3;
                }
            }
            if ($basTaskInfo['condition_ids']) {
                $aryCondition = json_decode($basTaskInfo['condition_ids'], true);
                $conditionVo = array();
                foreach ($aryCondition as $condi) {
                    $cInfo = Hapyfish2_Alchemy_Cache_Basic::getTaskConditionInfo($condi);
                    if ($cInfo) {
                        //current task condition complete process data
                        $curTaskCondiProc = 0;
                        if ($curTaskProc) {
                        	if (isset($curTaskProc[$condi])) {
                        		$curTaskCondiProc = (int)$curTaskProc[$condi];
                        		//special type deal
							    if ($cInfo['condition_type'] == 21) {
									$curTaskCondiProc = count($curTaskProc[$condi]);
								}
                        	}
                        }

                        //消耗收集物类 特殊处理
                        if ($cInfo['condition_type'] == 20) {
                            $curTaskCondiProc = Hapyfish2_Alchemy_Bll_BagItemDict::getItemCntByCid($uid, $cInfo['cid']);
						}
                        $conditionVo[] = array(
                            'type' => (int)$cInfo['classname_type'],
                            'content' => $cInfo['desp'],
                            'id' => $cInfo['icon_cid'],
                            'num' => (int)$cInfo['num'],
                            'curNum' => $curTaskCondiProc
                        );
                    }
                }
            }
            $taskVo = array(
                'id' => (int)$basTaskInfo['id'],
                'index' => (int)$basTaskInfo['priority'],
                //'type' => (int)$basTaskInfo['label'], //1主线 2支线 3日常
            	'name' => $basTaskInfo['title'],
            	'content' => $basTaskInfo['help_desp'],
            	'npcId' => (int)$basTaskInfo['npc_id'],
            	'npcName' => $basTaskInfo['npc_name'],
            	'npcChat' => $basTaskInfo['foreword'].'|'.$basTaskInfo['done_desp'],
            	'npcFaceClass' => $basTaskInfo['npc_classname'],
            	'sceneId' => (int)$basTaskInfo['worldmap_id'],
            	'conditions' => $conditionVo,
            	'awards' => json_decode($basTaskInfo['awards'], true),
            	'state' => $status, //1 未领 2 已领未查看 3 已查看
            	'nowFinishPrice' => (int)$basTaskInfo['complete_cost']
            );
        }
        return $taskVo;
    }

    public static function triggerStory($uid, $ids)
    {
    	$ids = explode(',', $ids);
    	
    	foreach ( $ids as $id ) {
            //准备npc剧情/对白
            if ($id) {
                //info_log($uid.'|'.$id, 'ccc');
                if (substr($id, -1, 1) == '1') {
                    Hapyfish2_Alchemy_Bll_Story::startStory($uid, $id);
                }
                else {
                    Hapyfish2_Alchemy_Bll_Story::triggerDialogById($uid, $id);
                }
            }
    	}
    	
        return;
    }

    /**
     *
     * init open task
     * @param integer $uid
     * @param array $openTask
     * @return boolean
     */
    public static function initOpenTask($uid, &$openTask)
    {
        $basTaskList = Hapyfish2_Alchemy_Cache_Basic::getTaskList();
        $selfInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$taskOpen = array();
		$taskPrepare = array();
		
		foreach ($basTaskList as $data) {
		    if ($data['label'] < 3 && $data['need_user_level'] == 1 && $data['need_fight_level'] == 1 && ($data['front_task_id'] == '[]' || !$data['front_task_id'])) {
		        if ($data['from_type'] == 4 ) {
		            $taskOpen[] = (int)$data['id'];
                    //准备npc剧情/对白
                    self::triggerStory($uid, $data['accept_story']);
		        }
		        else {
                    $taskPrepare[] = (int)$data['id'];
		        }
			}
		}

		$openTask['list'] = $taskOpen;
		$openTask['list2'] = $taskPrepare;
		return Hapyfish2_Alchemy_HFC_TaskOpen::update($uid, $openTask);
    }

    /**
     *
     * get current open task list
     * @param integer $uid
     * @return array <taskVoList>
     */
    public static function getCurTaskList($uid)
	{
		$aryTaskVo = array();
        $openTask = Hapyfish2_Alchemy_HFC_TaskOpen::getInfo($uid);

        if (!$openTask['list'] && !$openTask['list2']) {
            self::initOpenTask($uid, $openTask);
        }

        //current doing task
        if ($openTask['list']) {
            //task is read status
            $readStatus = Hapyfish2_Alchemy_Cache_TaskStatus::get($uid, $openTask['list']);
            //task current complete status
            $taskProcAll = $openTask['data'];
            foreach ($openTask['list'] as $tid) {
                $aryTaskVo[] = self::genTaskVo($uid, $tid, null, $taskProcAll, $readStatus);
            }
        }

        //can accept task
	    if ($openTask['list2']) {
            foreach ($openTask['list2'] as $tid) {
                $aryTaskVo[] = self::genTaskVo($uid, $tid);
            }
        }

        //daily task
        $aryDailyTask = self::getDailyTask($uid);

        return array_merge($aryTaskVo, $aryDailyTask);
	}

	/**
     *
     * get Daily task
     * @param integer $uid
     * @param integer $tid
     */
	public static function getDailyTask($uid)
	{
        $nowTm = time();
        $taskDaily = Hapyfish2_Alchemy_HFC_TaskDaily::getInfo($uid);

        //if need refresh task
        if ( !$taskDaily['refresh_tm'] || date('Ymd',$taskDaily['refresh_tm'])!=date('Ymd',$nowTm) || empty($taskDaily['list']) ) {
            $ids = Hapyfish2_Alchemy_Cache_Basic::getDailyTaskIds();
            $taskDaily['list'] = $ids;
            $taskDaily['data'] = array();
            $taskDaily['finish'] = array();
            $taskDaily['refresh_tm'] = $nowTm;
            Hapyfish2_Alchemy_HFC_TaskDaily::save($uid, $taskDaily, true);
        }

        $aryTaskVo = array();
        $taskProcAll = $taskDaily['data'];
        foreach ($taskDaily['list'] as $id) {
            $taskVo = self::genTaskVo($uid, $id, null, $taskProcAll);
            $taskVo['state'] = 3;
            $aryTaskVo[] = $taskVo;
        }
        return $aryTaskVo;
	}

	/**
     *
     * accept task in prepare list
     * @param integer $uid
     * @param integer $tid
     * @return int
     */
	public static function acceptTask($uid, $tid)
	{
	    $taskInfo = Hapyfish2_Alchemy_Cache_Basic::getTaskInfo($tid);
	    if (!$taskInfo) {
	        return -200;
	    }
	    //can not accept daily task or auto come task
	    if ($taskInfo['label'] == 3 || $taskInfo['from_type'] == 4) {
            return -200;
	    }

        $openTask = Hapyfish2_Alchemy_HFC_TaskOpen::getInfo($uid);
        if ($openTask['list2']) {
            $srhKey = array_search($tid, $openTask['list2']);
            if ($srhKey !== false) {
                //add tid to list
                $openTask['list'][] = (int)$tid;

                //remove tid from list2
                $list2 = array();
        		foreach ($openTask['list2'] as $taskId) {
        			if ($tid != $taskId) {
        				$list2[] = (int)$taskId;
        			}
        		}
        		$openTask['list2'] = $list2;

                //check if is_auto_complete task
                /*
                if ($taskInfo['is_auto_complete']) {
                    Hapyfish2_Alchemy_Bll_Task_Base::check($uid, $openTask, $taskInfo);
                }*/

                //save
                $ok = Hapyfish2_Alchemy_HFC_TaskOpen::update($uid, $openTask);
                if ($ok) {
                    $taskVo = self::genTaskVo($uid, $tid);
                    $taskVo['state'] = 2;
                    Hapyfish2_Alchemy_Bll_UserResult::addTaskChanges($uid, $taskVo);

                    //准备npc剧情/对白
                    self::triggerStory($uid, $taskInfo['accept_story']);

                    return 1;
                }
            }
        }
        return -200;
	}

	/**
     *
     * complete task
     * @param integer $uid
     * @param integer $tid
     * @param integer $isComplete 0-use rmb to complete task / 1-complete task
     * @return int
     */
	public static function completeTask($uid, $tid, $isComplete=1)
	{
        $taskInfo = Hapyfish2_Alchemy_Cache_Basic::getTaskInfo($tid);
		if (!$taskInfo || !$taskInfo['condition_ids']) {
			return -401;
		}

		$conditionIds = json_decode($taskInfo['condition_ids'], true);
		if (empty($conditionIds)) {
			return -401;
		}

		//the task can not cheat (use rmb to pass)
		if (!$isComplete && !$taskInfo['complete_cost']) {
		    return -403;
		}

		//check rmb enough
		if (!$isComplete && $taskInfo['complete_cost']) {
		    $userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
		    $needGem = $taskInfo['complete_cost'];
		    if ( $userGem < $needGem ) {
		    	return -206;
		    }
			
			/*$needCost = (int)$taskInfo['complete_cost'];
		    $cid = 2715;//沙漏
		    $goods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
		    $ownCost = 0;
		    if (isset($goods[$cid])) {
		        $ownCost = $goods[$cid]['count'];
		    }
            if ($needCost>$ownCost) {
                return -404;
            }*/
		}

		//daily task
		if ($taskInfo['label'] == 3) {
		    $dlyTask = Hapyfish2_Alchemy_HFC_TaskDaily::getInfo($uid);
		    if (!in_array($tid, $dlyTask['list'])) {
    			return -401;
    		}
    		$nowTm = time();
		    if (!$dlyTask['refresh_tm'] || date('Ymd',$dlyTask['refresh_tm'])!=date('Ymd',$nowTm)) {
                return -402;
    		}
            $ok = Hapyfish2_Alchemy_Bll_Task_Base::checkDailyTask($uid, $dlyTask, $taskInfo, $isComplete);
		}
		//other task
		else {
    		$openTask = Hapyfish2_Alchemy_HFC_TaskOpen::getInfo($uid);
    		if (!in_array($tid, $openTask['list'])) {
    			return -401;
    		}
		    $ok = Hapyfish2_Alchemy_Bll_Task_Base::check($uid, $openTask, $taskInfo, $isComplete);
		}
		if (!$ok) {
		    return -400;
		}

		//cheat for rmb to complete task
		if (!$isComplete) {
            //Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, $needCost);
			$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
			//扣除用户宝石
			$gemInfo = array(
	        		'uid' => $uid,
	        		'cost' => $needGem,
	        		'summary' => LANG_PLATFORM_BASE_TXT_7,
	        		'user_level' => $userLevel,
	        		'cid' => $tid,
	        		'num' => 1
	        	);
			Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo);
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
		if ( isset($taskTriHelp[$tid]) ) {
			$newHelpId = $taskTriHelp[$tid];
		}
		if ( $newHelpId > 0 ) {
            Hapyfish2_Alchemy_Bll_Help::startHelp($uid, $newHelpId);
		}
		
		return 1;
	}

    /**
     *
     * set current open task read
     * @param integer $uid
     * @param integer $tid
     * @return int
     */
	public static function setRead($uid, $tid)
	{
        $openTask = Hapyfish2_Alchemy_HFC_TaskOpen::getInfo($uid);
        $rst = Hapyfish2_Alchemy_Cache_TaskStatus::update($uid, $openTask['list'], $tid);
        return 1;
	}

	/**
     *
     * listen task condition changes
     * @param integer $uid
     * @param integer $type
     * @param array $data
     */
    public static function listen($uid, $type, $data = null)
    {
    	$openTask = Hapyfish2_Alchemy_HFC_TaskOpen::getInfo($uid);
    	if ($openTask) {
    		if (!empty($openTask['list'])) {
    			$counter = 0;
    			foreach ($openTask['list'] as $tid) {
	    			$taskInfo = Hapyfish2_Alchemy_Cache_Basic::getTaskInfo($tid);
	    			if ($taskInfo) {
	    				$conditionIds = json_decode($taskInfo['condition_ids'], true);
	    				foreach ($conditionIds as $cid) {
	    					$taskConditionInfo = Hapyfish2_Alchemy_Cache_Basic::getTaskConditionInfo($cid);
	    					//info_log($type . ':' . json_encode($taskConditionInfo), 'listen4');
	    					if ($taskConditionInfo && $taskConditionInfo['condition_type'] == $type) {
	    						if ($counter == 0) {
	    							$newOpenTask = $openTask;
	    						} else {
	    							$newOpenTask = Hapyfish2_Alchemy_HFC_TaskOpen::getInfo($uid);
	    						}
	    						self::handleTask($uid, $type, $newOpenTask, $taskInfo, $taskConditionInfo, $data);
	    						$counter++;
	    					}
	    				}
	    			}
    			}
    		}
    	}

    	//daily task
        $dailyTask = Hapyfish2_Alchemy_HFC_TaskDaily::getInfo($uid);
    	if ($dailyTask) {
    		$finish = $dailyTask['finish']?$dailyTask['finish']:array();
    		if (!empty($dailyTask['list'])) {
    			$counter = 0;
    			foreach ($dailyTask['list'] as $tid) {
	    			$taskInfo = Hapyfish2_Alchemy_Cache_Basic::getTaskInfo($tid);
	    			if ($taskInfo) {
	    				$conditionIds = json_decode($taskInfo['condition_ids'], true);
	    				foreach ($conditionIds as $cid) {
	    					$taskConditionInfo = Hapyfish2_Alchemy_Cache_Basic::getTaskConditionInfo($cid);
	    					//info_log($type . ':' . json_encode($taskConditionInfo), 'listen4');
	    					if ($taskConditionInfo && $taskConditionInfo['condition_type'] == $type) {
	    						if ($counter == 0) {
	    							$newOpenTask = $dailyTask;
	    						} else {
	    							$newOpenTask = Hapyfish2_Alchemy_HFC_TaskDaily::getInfo($uid);
	    						}
	    						if(!in_array($tid,$finish)){
	    							self::handleTask($uid, $type, $newOpenTask, $taskInfo, $taskConditionInfo, $data);
	    						}
	    						$counter++;
	    					}
	    				}
	    			}
    			}
    		}
    	}
    }

    private static function handleTask($uid, $type, $openTask, $taskInfo, $taskConditionInfo, $data = null)
    {
        //$name = 'T' . $type;
        if (isset(self::$_taskTypeLogicMap[$type])) {
            $name = 'T' . self::$_taskTypeLogicMap[$type];
        	$implFile = 'Hapyfish2/Alchemy/Bll/Task/' . $name . '.php';
            if (is_file(LIB_DIR . '/' . $implFile)) {
                require_once $implFile;
                $implClassName = 'Hapyfish2_Alchemy_Bll_Task_' . $name;
                $impl = new $implClassName();
                $impl->trigger($uid, $openTask, $taskInfo, $taskConditionInfo, $data);
            }
        }
    }







    public static function getConditionInfo($taskId)
	{
		$info = array();

		$taskInfo = Hapyfish2_Alchemy_Cache_Basic::getTaskInfo($taskId);
		if (!$taskInfo) {
			return $info;
		}

		$conditionIds = json_decode($taskInfo['condition_ids'], true);
		if (empty($conditionIds)) {
			return $info;
		}

		foreach ($conditionIds as $id) {
			$info[] = 0;
		}

		return $info;
	}

	public static function getDoneInfo($uid, $taskId, &$status)
	{
		$info = array();

		$taskInfo = Hapyfish2_Alchemy_Cache_Basic::getTaskInfo($taskId);
		if (!$taskInfo) {
			return $info;
		}

		$conditionIds = json_decode($taskInfo['condition_ids'], true);
		if (empty($conditionIds)) {
			return $info;
		}

		$openTask = Hapyfish2_Alchemy_HFC_TaskOpen::getInfo($uid);

		if (!in_array($taskId, $openTask['list'])) {
			return $info;
		}

		$status = Hapyfish2_Alchemy_Cache_TaskStatus::update($uid, $openTask['list'], $taskId);

		if (isset($openTask['data'][$taskId])) {
			$taskData = $openTask['data'][$taskId];
		} else {
			$taskData = array();
		}

		foreach ($conditionIds as $id) {
			$taskConditionInfo = Hapyfish2_Alchemy_Cache_Basic::getTaskConditionInfo($id);
			$type = $taskConditionInfo['condition_type'];
			$num = 0;
			if (isset($taskData[$id])) {
				if ($type == 29 || $type == 31 || $type == 43) {
					$num = count($taskData[$id]);
				} else {
					$num = $taskData[$id];
				}
			} else {
				if ($type == 9) {
					$num = Hapyfish2_Alchemy_Bll_Task_Base::getUnlockAnimalNum($uid);
				} else if ($type == 10) {
					$num = Hapyfish2_Alchemy_Bll_Task_Base::getOneUnlockAnimalNum($uid, $taskConditionInfo['cid']);
				} else if ($type == 11) {
					$num = Hapyfish2_Alchemy_Bll_Task_Base::getPhytotronNum($uid);
				} else if ($type == 12) {
					$num = Hapyfish2_Alchemy_Bll_Task_Base::getBuildingNum($uid);
				} else if ($type == 18) {
					$num = Hapyfish2_Alchemy_Bll_Task_Base::getActiveLoginNum($uid);
				} else if ($type == 32) {
					$num = Hapyfish2_Alchemy_Bll_Task_Base::getBuildingNumByCid($uid, $taskConditionInfo['cid']);
				} else if ($type == 33) {
					$num = Hapyfish2_Alchemy_Bll_Task_Base::getUserLevel($uid);
				}
			}

			$info[] = $num;
		}

		return $info;
	}
	
}