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
    
    
    
	
}