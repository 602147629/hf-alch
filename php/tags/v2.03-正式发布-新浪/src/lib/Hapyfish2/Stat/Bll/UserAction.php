<?php

class Hapyfish2_Stat_Bll_UserAction
{

    public static function calcDayData($dt, $dir)
    {
        $strDate = $dt;
        $fileName = $dir . $strDate . '/all-useraction-' . $strDate . '.log';
        //$fileName = '/home/admin/website/alchemy/renren/logs/useraction-20120825.log';
        try {
            //file not exists
            if (!file_exists($fileName)) {
                info_log($fileName . ' not exists!', 'stat_Dayuseraction');
                return false;
            }
            $content = file_get_contents($fileName);
            if (!$content) {
                info_log($fileName . ' has no content!', 'stat_Dayuseraction');
                return false;
            }
            $lines = explode("\n", $content);

            $retData = array();
            $userAction = array();
            $userActionNew = array();
            $allCount = 0;
            $newUidStepList = array();
            
            foreach ($lines as $line) {
                if (empty($line) || $line == '-100') {
    				continue;
    			}

                $aryLine = explode("\t", $line);
                $uid = $aryLine[2];
                $isNew = $aryLine[3];
                $id = $aryLine[4];
                $order = $aryLine[5];
                $name = $aryLine[6];
                
                $step = 'step'.$id;
                
                $allCount++;
                if ( $isNew == 1 ) {
                	if ( isset($newUidStepList[$uid][$step]) ) {
                		
                	}
                	else {
		                if ( isset($userActionNew[$step]) ) {
		                	$userActionNew[$step]['count']++;
		                }
		                else {
		                	$userActionNew[$step] = array('id'=>$id,'count'=>1,'order'=>$order,'name'=>$name);
		                }
		                $newUidStepList[$uid][$step] = 1;
                	}
                }
                else {
	                if ( isset($userAction[$step]) ) {
	                	$userAction[$step]['count']++;
	                }
	                else {
	                	$userAction[$step] = array('id'=>$id,'count'=>1,'order'=>$order,'name'=>$name);
	                }
                }
            }
            
            $retData['user_action'] = $userAction;
            $retData['user_action_new'] = $userActionNew;

            //用户离开时数据
            $userLeaveList = self::getUserLeaveData($dt);
            $retData['user_leave'] = $userLeaveList;
            
            $dal = Hapyfish2_Stat_Dal_UserAction::getDefaultInstance();
            $row = $dal->getRow($strDate);
            if (!empty($row)) {
                $dal->delete($strDate);
            }
            $info = array();
            $info['log_time'] = (int)$strDate;
            $info['all_count'] = (int)$allCount;
            if ($userAction || $userActionNew) {
                $info['user_action'] = json_encode($retData['user_action']);
                $info['user_action_new'] = json_encode($retData['user_action_new']);
            }
            $info['user_leave'] = json_encode($retData['user_leave']);

            $dal->insert($info);

        }
        catch (Exception $e) {
            info_log($e->getMessage(), 'stat_UserAction');
            return false;
        }
        return true;
    }
    
    //30号 统计 28号注册而29号未登录的用户信息
    public static function getUserLeaveData($dt)
    {
    	$logTm = strtotime($dt);
    	$logTm = $logTm + 1 * 86400;
    	$beginCreateTm = $logTm - 2 * 86400;
    	$endCreateTm = $logTm - 1 * 86400;
    	$beginLoginTm = $logTm - 1 * 86400;
    	$endLoginTm = $logTm;

    	$leaveList = array();
		$dal = Hapyfish2_Stat_Dal_UserAction::getDefaultInstance();
    	$list = $dal->getUserLeaveList($beginCreateTm, $endCreateTm, $beginLoginTm);
    	
    	return $list;
    }
    
    public static function getDay($day)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Stat_Dal_UserAction::getDefaultInstance();
			$data = $dal->getRow($day);
		} catch (Exception $e) {

		}

		return $data;
	}

    public static function listData($day1, $day2)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Stat_Dal_UserAction::getDefaultInstance();
			$data = $dal->listData($day1, $day2);
		} catch (Exception $e) {

		}

		return $data;
	}

    public static function userLeaveListData($day1, $day2)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Stat_Dal_UserAction::getDefaultInstance();
			$data = $dal->userLeaveListData($day1, $day2);
		} catch (Exception $e) {

		}

		return $data;
	}
	
}