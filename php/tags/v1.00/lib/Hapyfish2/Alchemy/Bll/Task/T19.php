<?php

/**
 * 雇佣佣兵（分职业和属性）（带引导）  注：有职业和属性要求时，需设定condition cid为{职业|属性|星级}（中间用|做分隔符），比如四星半火属性的战士，设置cid为1|2|9。 备注：1-战士 2-弓手 3-法师  1-风 2-火 3-水   星级1-10
 * type = 19
 */
class Hapyfish2_Alchemy_Bll_Task_T19
{
	public function trigger($uid, $openTask, $taskInfo, $taskConditionInfo, $data)
	{

		if (!$data || count($data) != 3) {
			return;
		}

		$job = $data[0];
		$element = $data[1];
		$star = $data[2];

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

		if (!$taskConditionInfo['cid']) {
		    return;
		}

		$canComplete = false;
		$aryTmp = explode('|', $taskConditionInfo['cid']);
		if (!$aryTmp || count($aryTmp) != 3) {
		    return;
		}
		$needJob = $aryTmp[0];
		$needElement = $aryTmp[1];
		$needStar = $aryTmp[2];
		//job match && elements match && star match
		if ( (!$needJob || ($needJob && $needJob == $job))
		        && (!$needElement || ($needElement && $needElement == $element))
		        && (!$needStar || ($needStar && $needStar == $star)) ) {

            $openTask['data'][$taskId][$conditionId] = 1;
            $canComplete = true;
		}

	    $saved = false;
	    //日常任务
	    if ($taskInfo['label'] == 3) {
            $saved = Hapyfish2_Alchemy_HFC_TaskDaily::save($uid, $openTask);
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