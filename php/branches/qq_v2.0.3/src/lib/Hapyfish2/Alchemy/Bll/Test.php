<?php

/**
 * task
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/04    zx
 */
class Hapyfish2_Alchemy_Bll_Test
{

	public static function addTask($uid, $tid)
	{
	    $taskInfo = Hapyfish2_Alchemy_Cache_Basic::getTaskInfo($tid);
	    
	    if (!$taskInfo) {
	        return -200;
	    }
        $openTask = Hapyfish2_Alchemy_HFC_TaskOpen::getInfo($uid);

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
                var_dump($ok);
                if ($ok) {
                    $taskVo = Hapyfish2_Alchemy_Bll_Task::genTaskVo($uid, $tid);
                    $taskVo['state'] = 2;
                    Hapyfish2_Alchemy_Bll_UserResult::addTaskChanges($uid, $taskVo);

                    //准备npc剧情/对白
                    Hapyfish2_Alchemy_Bll_Task::triggerStory($uid, $taskInfo['accept_story']);

                    return 1;
                }
	}
	
	public static function delTask($uid, $tid)
	{

        $openTask = Hapyfish2_Alchemy_HFC_TaskOpen::getInfo($uid);

                //add tid to list
                $openTask['list'][] = (int)$tid;
                foreach ( $openTask['list'] as $k => $v ) {
                	if ( $tid == $v ) {
                		unset($openTask['list'][$k]);
                	}
                }

                //remove tid from list2
                $list2 = array();
        		foreach ($openTask['list2'] as $m => $n ) {
        			if ($tid == $n) {
        				unset($openTask['list2'][$m]);
        			}
        		}

                //save
                $ok = Hapyfish2_Alchemy_HFC_TaskOpen::update($uid, $openTask);
                var_dump($ok);
                return 1;
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
            //return -403;
        }

        //check rmb enough
        if (!$isComplete && $taskInfo['complete_cost']) {
            $userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
            $needGem = $taskInfo['complete_cost'];
            $needGem = 0;
            if ( $userGem < $needGem ) {
                //return -206;
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
            $ok = Hapyfish2_Alchemy_Bll_Test::check($uid, $openTask, $taskInfo, $isComplete);
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
    		//return false;
    	}
    
    	//complete task list log update
    	$ok = Hapyfish2_Alchemy_Cache_Task::completeTask($uid, $taskId);
    	if ($ok) {
    		//complete open task
    		$saveOK = Hapyfish2_Alchemy_HFC_TaskOpen::save($uid, $openTask, true);
    		if ( !$isCompleted ) {
	    		//set changes to auto output deal
	    		Hapyfish2_Alchemy_Bll_UserResult::addTaskCompletedId($uid, $taskId);
    		}
    
    		//send awards
    		$userLevelBefore = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
    		Hapyfish2_Alchemy_Bll_MapCopy::awardCondition($uid, json_decode($taskInfo['awards'], true), $changeResult, 3);
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
    		$taskTriHelp = array('21' => 6,
    				'81' => 10,
    				'41' => 7,
    				'251' => 14,
    				'261' => 15,
    				'391' => 12,
    				'521' => 19,
    				'531' => 21,
    				'671' => 11,
    				'51' => 23,
    				'211' => 24,
    				'1241' => 25);
    		if ( isset($taskTriHelp[$taskId]) ) {
    			$newHelpId = $taskTriHelp[$taskId];
    		}
    		if ( $newHelpId > 0 ) {
    			Hapyfish2_Alchemy_Bll_Help::startHelp($uid, $newHelpId);
    		}
    	}
    
    	return $ok;
    }
    
    public static function addNewTask($uid, $ntid)
    {

    	$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
    	$userHomeLevel = Hapyfish2_Alchemy_HFC_User::getUserHomeLevel($uid);
    	$userFight = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);

    	$openTask = Hapyfish2_Alchemy_HFC_TaskOpen::getInfo($uid);
    	
    	$taskInfo1 = Hapyfish2_Alchemy_Cache_Basic::getTaskInfo($ntid);
    	var_dump($ntid);
    	var_dump('<br/>');
    	if ($taskInfo1 && $taskInfo1['label']!=3) {
    		$frontTaskList = json_decode($taskInfo1['front_task_id'], true);
    		$newTask = true;
    		//check if front task had all completed
    		/* foreach ($frontTaskList as $id) {
    			$ok0 = Hapyfish2_Alchemy_Cache_Task::isCompletedTask($uid, $id);
    			if (!$ok0) {
    				$newTask = false;
    				break;
    			}
    		} */

    		var_dump($newTask);
    		var_dump('<br/>');
    		if ($newTask) {
    			//is level enough
    			/* if ($taskInfo1['need_user_level'] && $userLevel < $taskInfo1['need_user_level']) {
    				//等级没到要求则放入buffer_list
    				//$openTask['buffer_list'][$ntid] = $taskInfo1['need_user_level'];
    				$openTask['buffer_list'][] = $ntid;
    			}
    			else if ($taskInfo1['need_fight_level'] && $userFight['level'] < $taskInfo1['need_fight_level']) {
    				$openTask['buffer_list'][] = $ntid;
    			}
    			else { */
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
    						/* if ($type1 == 23) {
    							$needTrigLev = $userHomeLevel;
    							break;
    						} */
    					}
    	
    				}
    			//}
    		}
    	}

    	//save task info
    	$saveOK = Hapyfish2_Alchemy_HFC_TaskOpen::save($uid, $openTask);
    	
    	return $saveOK;
    }

    public static function testDb($uid, $id)
    {
    	$info = array();
    	$info['uid'] = $uid;
    	$info['fid'] = Hapyfish2_Alchemy_Bll_Fight::getNewId($uid);
    	$info['type'] = 0;
    	$info['status'] = 0;
    
    	return 1;
    }

    public static function testCache($uid, $id)
    {
    	$homeSide = Hapyfish2_Alchemy_Bll_FightCorps::getHomeSideUnitList($uid);
    	if (!$homeSide) {
    		return -321;
    	}
    
    	$enemySide = Hapyfish2_Alchemy_Bll_MapCopy::getEnemySideUnitList($uid, $id);
    	if (!$enemySide) {
    		return -322;
    	}
    
    	//体力不足时不能战斗
    	$userSpInfo = Hapyfish2_Alchemy_HFC_User::getUserSp($uid);

    	$usrScene = Hapyfish2_Alchemy_HFC_User::getUserScene($uid);
    	$info['enemy_id'] = $usrScene['cur_scene_id'] . '-' . $id;
    	$saveDb = (defined('ENABLE_FIGHT_DB_LOG') && ENABLE_FIGHT_DB_LOG);

    	//拼接Vo数据返回前端
    	//我方
    	$roleList1 = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
    	//敌方
    	$roleList2 = Hapyfish2_Alchemy_Bll_MapCopy::genEnemyRolesVo($enemySide);
    
    	//可援助攻击
    	$aryAssist = array();
    	$assCnt = 0;
    	$extCnt = 0;
    	$assistInfo = Hapyfish2_Alchemy_HFC_User::getUserFightAssistInfo($uid);
    	$assCnt = $assistInfo['assist_bas_count'];
    	$extCnt = $assistInfo['assist_ext_count'];
    	$aryAssist = Hapyfish2_Alchemy_Cache_Fight::getFightFriendAssistInfo($uid);

    	//$resultVo = array('BattleVo'=>$battle, 'RndNums'=>$info['rnd_element']);
    	//Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'BattleVo', $battle);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'RndNums', $info['rnd_element']);
    	return 1;
    }

    public static function regFight($uid, $id)
    {
    	//$info = Hapyfish2_Alchemy_Cache_Fight::getFightInfo($uid);
    	//if (!$info || $info['status']) {
    	$info = array();
    	$info['uid'] = $uid;
    	$info['fid'] = Hapyfish2_Alchemy_Bll_Fight::getNewId($uid);
    	$info['type'] = 0;
    	$info['status'] = 0;
    
    	$aryRnd = array();
    	for ($i=0; $i<20; $i++) {
    		$aryRnd[] = mt_rand(1,1000);
    	}
    
    	$homeSide = Hapyfish2_Alchemy_Bll_FightCorps::getHomeSideUnitList($uid);
    	if (!$homeSide) {
    		return -321;
    	}
    
    	$enemySide = Hapyfish2_Alchemy_Bll_MapCopy::getEnemySideUnitList($uid, $id);
    	if (!$enemySide) {
    		return -322;
    	}
    
    	//体力不足时不能战斗
    	$userSpInfo = Hapyfish2_Alchemy_HFC_User::getUserSp($uid);
/*     	if ( $userSpInfo['sp'] < 1 ) {
    		return -323;
    	} */
    
    	$info['rnd_element'] = $aryRnd;
    	$info['home_side'] = $homeSide;
    	$info['content'] = array();
    	$info['create_time'] = time();
    
    	$usrScene = Hapyfish2_Alchemy_HFC_User::getUserScene($uid);
    	$info['enemy_id'] = $usrScene['cur_scene_id'] . '-' . $id;
    	$saveDb = (defined('ENABLE_FIGHT_DB_LOG') && ENABLE_FIGHT_DB_LOG);
    	//}
    
    	//战斗宣言
    	$aryTalk = array();
    	$cntHomeSide = count($homeSide);
    	$rndTalkRole = mt_rand(1, $cntHomeSide);
    	$idx = 0;
    	foreach ($homeSide as $data) {
    		$idx ++;
    		//if ($data['id'] == 0) {
    		if ($idx == $rndTalkRole) {
    			$talks = Hapyfish2_Alchemy_Cache_Basic::getFightDeclareByJob($data['job']);
    			if ($talks) {
    				$rndKey = mt_rand(1, count($talks));
    				$aryTalk[] = array((int)$data['matrix_pos'], $talks[$rndKey-1]);
    			}
    			break;
    		}
    	}
    
    	$newMonsterAry = array();
    	$vip = new Hapyfish2_Alchemy_Bll_Vip();
    	$vipAddition = array();
    	foreach ($enemySide as $key => $data) {
    		if ($data['is_boss']) {
    			if ($data['talk']) {
    				$aryTalk[] = array((int)$data['matrix_pos'], $data['talk']);
    			}
    		}
    		if ( !in_array($data['cid'], $newMonsterAry) ) {
    			//添加遇到怪物记录，并判断是否有首杀奖励
    			$isNewMonster = Hapyfish2_Alchemy_HFC_Monster::isNewMonster($uid, $data['cid']);
    			if ( $isNewMonster ) {
    				$newMonsterAry[] = $data['cid'];
    				$data['award_conditions'] = array_merge($data['award_conditions'], $data['first_award_conditions']);
    				$enemySide[$key] = $data;
    				 
    				$newHelpId = 0;
    				if ( $data['cid'] == 14571 ) {
    					$newHelpId = 8;
    				}
    				else if ( $data['cid'] == 15271 ) {
    					$newHelpId = 16;
    				}
    				if ( $newHelpId > 0 ) {
    					Hapyfish2_Alchemy_Bll_Help::startHelp($uid, $newHelpId);
    				}
    			}
    		}
    		$vip->setAddition($uid, $data['award_conditions']);
    		//添加图鉴
    		$illResult = Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $data['tid']);
    
    		/*if ( $illResult['result']['status'] == 1 ) {
    		 $data['award_conditions'] = array_merge($data['award_conditions'], $data['first_award_conditions']);
    		$enemySide[$key] = $data;
    		}*/
    	}
    	$vipAddition = $vip->getAddition();
    	//首次遇到怪物记录
    	$info['new_monster'] = implode(',', $newMonsterAry);
    
    	//保存初始战斗信息
    	$info['enemy_side'] = $enemySide;
    	Hapyfish2_Alchemy_Cache_Fight::saveFightInfo($uid, $info, $saveDb);
    
    	//拼接Vo数据返回前端
    	//我方
    	$roleList1 = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
    	//敌方
    	$roleList2 = Hapyfish2_Alchemy_Bll_MapCopy::genEnemyRolesVo($enemySide);
    
    	//可援助攻击
    	$aryAssist = array();
    	$assCnt = 0;
    	$extCnt = 0;
    	$assistInfo = Hapyfish2_Alchemy_HFC_User::getUserFightAssistInfo($uid);
    	$assCnt = $assistInfo['assist_bas_count'];
    	$extCnt = $assistInfo['assist_ext_count'];
    	$aryAssist = Hapyfish2_Alchemy_Cache_Fight::getFightFriendAssistInfo($uid);
    	if (!$aryAssist) {
    		$aryAssist = Hapyfish2_Alchemy_Bll_Fight::getFriendAssistVo($uid);
    		Hapyfish2_Alchemy_Cache_Fight::setFightFriendAssistInfo($uid, $aryAssist);
    	}
    	$skip = $vip->getVipSkip($uid);
    	$jumpTime = $skip['max'] - $skip['num'] >0 ? $skip['max'] - $skip['num']:0;
    	$battle = array(
    			'id' => $info['fid'],
    			'bgClassName' => 'battlebg.1.Background',
    			'roleList' => array_merge($roleList1, $roleList2),
    			'talk' => $aryTalk,
    			'friendSkill' => $aryAssist,
    			'assCnt' => $assCnt,
    			'extCnt' => $extCnt,
    			'jumpTimes'=>$jumpTime,
    			'vipPrize'=>$vipAddition
    	);
    	
    	//战斗完成消耗体力
    	//Hapyfish2_Alchemy_HFC_User::decUserSp($uid, 1);
    
    	//$resultVo = array('BattleVo'=>$battle, 'RndNums'=>$info['rnd_element']);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'BattleVo', $battle);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'RndNums', $info['rnd_element']);
    	return 1;
    }

    /**
     * 重置竞技场排行信息，每周日24:00重置
     *
     */
    public static function resetArenaRank()
    {
    	$curWeekDay = date("w");
    	 
    	//周一 以外时间不做检查
    	if ( $curWeekDay != 1 ) {
    		//return;
    	}
    
    	//上周最后时间, 周日 24:00
    	$lastWeekEndTm = strtotime("-1 week Monday");
    
    	//竞技场信息
    	$arenaInfo = Hapyfish2_Alchemy_Cache_Arena::getArena();
    	if ( $arenaInfo['last_time'] >= $lastWeekEndTm ) {
    		//return;
    	}
    	 
    	$lastWeek = date("Ymd", $lastWeekEndTm);
    	 
    	$dalArena = Hapyfish2_Alchemy_Event_Dal_Arena::getDefaultInstance();
    	 
    	try {
    		//备份上周排行数据
    		$bakTableName = 'alchemy_arena_rank_'.$lastWeek;
    		$sqlBakLast = "DROP TABLE IF EXISTS `$bakTableName`;CREATE TABLE `$bakTableName` SELECT * FROM `alchemy_arena_rank_lastweek`;";
    		$dalArena->updateArenaTable($sqlBakLast);
    	}
    	catch (Exception $e) {
    		info_log('[Hapyfish2_Alchemy_Bll_Arena::resetArenaRank:del]' . $e->getMessage(), 'db.err.arena');
    	}
    	 
    	try {
    		//删除上周数据
    		$sqlTruncate = "TRUNCATE TABLE `alchemy_arena_rank_lastweek`;";
    		$dalArena->updateArenaTable($sqlTruncate);
    
    		//记录本周数据
    		$sqlBakCur = "INSERT INTO `alchemy_arena_rank_lastweek` SELECT * FROM `alchemy_arena_rank`;";
    		$okBak = $dalArena->updateArenaTable($sqlBakCur);
    
    		if ( !$okBak ) {
    			return -100;
    		}
    		//重置本周数据
    		$sqlReset = "UPDATE `alchemy_arena_rank` SET `score`=0;";
    		$okUpdate = $dalArena->updateArenaTable($sqlReset);
    	}
    	catch (Exception $e) {
    		info_log('[Hapyfish2_Alchemy_Bll_Arena::resetArenaRank:update]' . $e->getMessage(), 'db.err.arena');
    	}
    	 
    	if ( !$okUpdate ) {
    		return -100;
    	}
    	 
    	//更新重置时间
    	$arenaInfo['last_time'] = time();
    	Hapyfish2_Alchemy_Cache_Arena::updateArena($arenaInfo);
    
    	return true;
    }
    
    
    
	
}