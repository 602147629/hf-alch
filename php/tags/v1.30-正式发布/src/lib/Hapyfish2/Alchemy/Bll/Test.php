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
            Hapyfish2_Alchemy_HFC_Bll::startHelp($uid, $newHelpId);
        }
        
        return 1;
    }
	
	
}