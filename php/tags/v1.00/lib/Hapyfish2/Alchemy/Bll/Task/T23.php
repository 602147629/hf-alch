<?php

/**
 * 23-扩店等级
 * type = 23
 */
class Hapyfish2_Alchemy_Bll_Task_T23
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
		if ($num >= $taskConditionInfo['num']) {
            $openTask['data'][$taskId][$conditionId] = (int)$taskConditionInfo['num'];
            $canComplete = true;
		}
		else {
            $openTask['data'][$taskId][$conditionId] = $num;
		}

		$saved = false;
	    //日常任务
	    if ($taskInfo['label'] == 3) {
            $saved = Hapyfish2_Alchemy_HFC_TaskDaily::save($uid, $openTask);
	    }
	    //主线or支线任务
	    else {
    	    $complete = false;
    	    //check is auto complete task
    		if ($canComplete && $taskInfo['is_auto_complete']) {
                $complete = Hapyfish2_Alchemy_Bll_Task_Base::check($uid, $openTask, $taskInfo);
    		}
            //if not auto complete, task data must be updated yet
    		if (!$complete) {
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