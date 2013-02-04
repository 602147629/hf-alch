<?php

class Hapyfish2_Alchemy_Bll_Stat
{
	
	/**
	 * 新手用户操作统计
	 * 
	 * @param int $uid
	 * @param array $params,array('type'=>1,'cid'=>1)
	 */
	public static function addActionLog($uid, $params, $setNew = null)
	{
		$type = $params['type'];
		$cid = $params['cid'];
		
		$logList = Hapyfish2_Alchemy_Cache_BasicExt::getStatUserAction();
		
		$stepInfo = false;
		foreach ( $logList as $v ) {
			if ( $v['type'] == $type && $v['cid'] == $cid ) {
				$stepInfo = $v;
			}
		}
		
		if ( $stepInfo ) {
			$isNew = 0;
			$todayTm = strtotime(date('Ymd'));
			
			$createTm = Hapyfish2_Alchemy_Cache_User::getCreateTime($uid);
			if ( $setNew == 1 ) {
				$isNew = 1;
			}
			else {
				if ( $createTm >= $todayTm ) {
					$isNew = 1;
				}
			}
			
			$logger = Hapyfish2_Util_Log::getInstance();
			$logger->report('useraction', array($uid, $isNew, $stepInfo['id'], $stepInfo['order'], $stepInfo['name']));
			
			if ( $isNew == 1 ) {
				try {
					
					$dalStat = Hapyfish2_Alchemy_Dal_Stat::getDefaultInstance();
					$statList = $dalStat->getStepList($uid);
					if ( $statList ) {
						$statList = json_decode($statList, true);
					}
					else {
						$statList = array();
					}
					
					$loginInfo = Hapyfish2_Alchemy_HFC_User::getUserLoginInfo($uid);
					$lastLoginTm = $loginInfo['last_login_time'];
					
					if (!in_array($stepInfo['id'], $statList)) {
						$statList[] = $stepInfo['id'];
						$dalStat->insUpd($uid, $stepInfo['id'], json_encode($statList), $createTm, $lastLoginTm);
					}
				
				}
			    catch (Exception $e) {
			        info_log('addActionLog:failed:'.$e->getMessage(), 'Hapyfish2_Alchemy_Bll_Stat');
			    }
			}
		}

		return true;
	}

	/**
	 * 新手用户操作统计-注册用户信息之前
	 * 
	 * @param int $uid
	 * @param array $params,array('type'=>1,'cid'=>1)
	 */
	public static function addActionLogNoUser($uid, $params)
	{
		$type = $params['type'];
		$cid = $params['cid'];
		
		$logList = Hapyfish2_Alchemy_Cache_BasicExt::getStatUserAction();
		
		$stepInfo = false;
		foreach ( $logList as $v ) {
			if ( $v['type'] == $type && $v['cid'] == $cid ) {
				$stepInfo = $v;
			}
		}
		
		if ( $stepInfo ) {
			$isNew = 0;
			$todayTm = strtotime(date('Ymd'));
			
			//$createTm = Hapyfish2_Alchemy_Cache_User::getCreateTime($uid);
			
			//if ( $createTm >= $todayTm ) {
			$isNew = 1;
			//}
			
			$logger = Hapyfish2_Util_Log::getInstance();
			$logger->report('useraction', array($uid, $isNew, $stepInfo['id'], $stepInfo['order'], $stepInfo['name']));
		}

		return true;
	}
}