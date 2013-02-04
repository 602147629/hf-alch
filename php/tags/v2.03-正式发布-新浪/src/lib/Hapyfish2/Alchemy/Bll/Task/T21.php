<?php

/**
 * 访问不同好友次数
 * type = 21
 */
class Hapyfish2_Alchemy_Bll_Task_T21
{
	public function trigger($uid, $openTask, $taskInfo, $taskConditionInfo, $data)
	{
		if (!$data) {
			return;
		}

		$newFid = (int)$data;
		$taskId = (int)$taskInfo['id'];
		$conditionId = (int)$taskConditionInfo['id'];

	    if (!isset($openTask['data'][$taskId])) {
			$oldFids = array();
			$openTask['data'][$taskId] = array($conditionId => $oldFids);
		}
		else if (!isset($openTask['data'][$taskId][$conditionId])) {
			$oldFids = array();
			$openTask['data'][$taskId][$conditionId] = $oldFids;
		}
		else {
			$oldFids = $openTask['data'][$taskId][$conditionId];
		}

		//add a new visit
		if (!in_array($newFid, $oldFids)) {
		    $oldFids[] = $newFid;
		    $openTask['data'][$taskId][$conditionId] = $oldFids;
		}

        //condition meet matched
		$canComplete = false;
		if (count($oldFids) >= $taskConditionInfo['num']) {
            $canComplete = true;
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