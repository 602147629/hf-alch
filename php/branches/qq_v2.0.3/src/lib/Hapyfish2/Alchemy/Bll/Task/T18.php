<?php

/**
 * 18-更换一次技能	28-换一次装备	29-接一次订单	30-完成订单数量	31-合成收获道具次数	32-发布一次礼物愿望	33-赠送好友一次礼物	34-移动一次家具( 任何装饰 )
 * type = 18
 */
class Hapyfish2_Alchemy_Bll_Task_T18
{
	public function trigger($uid, $openTask, $taskInfo, $taskConditionInfo, $data)
	{
	    $num = (int)$data;
		if (!$num) {
			return;
		}
	    $taskId = (int)$taskInfo['id'];
		$conditionId = (int)$taskConditionInfo['id'];

	    if (!isset($openTask['data'][$taskId])) {
			$oldNum = 0;
			$openTask['data'][$taskId] = array($conditionId => 0);
		}
		else if (!isset($openTask['data'][$taskId][$conditionId])) {
			$oldNum = 0;
			$openTask['data'][$taskId][$conditionId] = 0;
		}
		else {
			$oldNum = (int)$openTask['data'][$taskId][$conditionId];
		}

        //condition meet matched
		$canComplete = false;
		if (($oldNum + $num) >= $taskConditionInfo['num']) {
            $openTask['data'][$taskId][$conditionId] = (int)$taskConditionInfo['num'];
            $canComplete = true;
		}
		else {
            $openTask['data'][$taskId][$conditionId] = $oldNum + $num;
		}

	    $saved = false;
	    //日常任务
	    if ($taskInfo['label'] == 3) {
            $saved = Hapyfish2_Alchemy_HFC_TaskDaily::save($uid, $openTask);
            $ok = Hapyfish2_Alchemy_Bll_Task_Base::checkDailyTask($uid, $openTask, $taskInfo, 1);
	    }
	    //主线or支线任务
	    else {
    	    $ok = false;
    	    //check is auto complete task
    		if ($canComplete && $taskInfo['is_auto_complete']) {
                $ok = Hapyfish2_Alchemy_Bll_Task_Base::check($uid, $openTask, $taskInfo);
    		}
            //if not auto complete, task data must be updated yet
    		if (!$ok) {
    		    $saved = Hapyfish2_Alchemy_HFC_TaskOpen::save($uid, $openTask);
    		}
	    }

	    if ($saved) {
			//set changes to auto output deal
			$chgTaskVo = Hapyfish2_Alchemy_Bll_Task::genTaskVo($uid, $taskId, $taskInfo, $openTask['data']);
			$chgTaskVo['state'] = 3;
			Hapyfish2_Alchemy_Bll_UserResult::addTaskChanges($uid, $chgTaskVo);
		}
	}

}