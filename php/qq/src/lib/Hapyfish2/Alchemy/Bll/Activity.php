<?php

/**
 * task
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/04    zx
 */
class Hapyfish2_Alchemy_Bll_Activity
{

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
                    }
                }
            }
            $status = 0;
            if($curTaskCondiProc >= $cInfo['num']){
            	$status = 1;
            }
            $taskVo = array(
            	'name' => $basTaskInfo['title'],
            	'currentNum' => $curTaskCondiProc,
            	'maxNum' => (int)$cInfo['num'],
            	'status'=>(int)$status
            );
        }
        return $taskVo;
    }

	public static function init($uid)
	{
		$list = array();
		$activityAward = Hapyfish2_Alchemy_Cache_Activity::getActivityAward();
		foreach($activityAward as $k=>$v){
			$list[] = $v['activity'];
		}
		$data['livenessArray'] = $list;
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'livenessTaskStaticVo', $data);
		return 1;
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
	public static function getTaskVo($uid)
	{
		$nowTm = time();
        $taskDaily = Hapyfish2_Alchemy_HFC_TaskDaily::getInfo($uid);
		$userActivity = Hapyfish2_Alchemy_Cache_Activity::get($uid);
		$activityNum = self::getUserActivity($uid,$taskDaily);
        $aryTaskVo = array();
        $taskProcAll = $taskDaily['data'];
        $i = 1;
        foreach ($taskDaily['list'] as $id) {
        	$info = Hapyfish2_Alchemy_Cache_Activity::getInfo($id);
            $taskVo = self::genTaskVo($uid, $id, null, $taskProcAll);
            $taskVo['sort'] = $i;
            $taskVo['livenessNum'] = $info['activity'];
            $i++;
            $aryTaskVo[] = $taskVo;
        }
        $activityVo['currentLiveness'] = $activityNum;
        $activityVo['maxLiveness'] = self::getMaxActivity();
        $activityVo['list'] = self::getComplete($activityNum,json_decode($userActivity['step']));
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'livenessTaskInitVo', $aryTaskVo);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'livenessTaskCommonVo', $activityVo);
        return 1;
	}
	
	public static function getActivityAward($uid, $type)
	{
		$userAc = Hapyfish2_Alchemy_Cache_Activity::get($uid);
		$activityNum = self::getUserActivity($uid);
		$step = json_decode($userAc['step'], true);
		if(in_array($type,  $step)){
			return -703;
		}
		$info = Hapyfish2_Alchemy_Cache_Activity::getAwardInfo($type);
		if($activityNum < $info['activity']){
			return -704;
		}
		$awards = json_decode($info['awards'],true);
		foreach($awards as $k=>$v){
			if($v[1] == 1){
				if( $v[2]> 0){
					Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $v[2]);
				}
			}
			if($v[1] == 2){
				if( $v[2]> 0){
					$gemInfo = array('gem' => $v[2]);
					Hapyfish2_Alchemy_Bll_Gem::add($uid, $gemInfo);
					Hapyfish2_Alchemy_Bll_UserResult::mergeGem($uid, $v[2]);
				}
			}
			if($v[1] == 3){
				$itemType =	substr($v[0], -2, 1);
				if($itemType == 6){
					Hapyfish2_Alchemy_Bll_EventGift::addUserWeapon($uid, $v[0],$v[3]);
				}else{
					Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $v[0],$v[2]);
				}
			}
		}
		$step[] = $type;
		$userAc['step'] = json_encode($step);
		Hapyfish2_Alchemy_Cache_Activity::update($uid, $userAc);
		$userAc = Hapyfish2_Alchemy_Cache_Activity::get($uid);
		$list = self::getComplete($activityNum,json_decode($userAc['step']));
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'list', $list);
		
		return 1;
	}
	
	public static function getMaxActivity()
	{
		$num = 0;
		$activityList = Hapyfish2_Alchemy_Cache_Activity::getList();
		foreach($activityList as $k => $v){
			$num += $v['activity'];
		}
		return $num;
	}
	
	public static function getComplete($userActivity,$list)
	{
		$data = array();
		$max = 1;
		$award = Hapyfish2_Alchemy_Cache_Activity::getActivityAward();
		foreach($award as $k => $v){
			$max = $k>$max?$k:$max;
		}
		for($i = 1; $i <= $max; $i ++){
			$status = 0;
			if($userActivity >= $award[$i]['activity']){
				$status = 1;
			}
			if(in_array($i, $list)){
				$status = 2;
			}
			$data[] = $status;
		}
		return $data;
	}
	
	public static function addUserActivity($uid, $num)
	{
		$userAc = Hapyfish2_Alchemy_Cache_Activity::get($uid);
		$userAc['activity'] += $num;
		Hapyfish2_Alchemy_Cache_Activity::update($uid, $userAc);
		return 1;
	}
	
	public static function getUserActivity($uid,$data=array())
	{
		if(empty($data)){
			$data = Hapyfish2_Alchemy_HFC_TaskDaily::getInfo($uid);
		}
		$finish = $data['finish'];
		if(!empty($finish)){
			$num = 0;
			foreach($finish as $k => $fid){
				$info = Hapyfish2_Alchemy_Cache_Activity::getInfo($fid);
				$num += $info['activity'];
			}
			return $num;
		}else{
			return 0;
		}
	}
	
}