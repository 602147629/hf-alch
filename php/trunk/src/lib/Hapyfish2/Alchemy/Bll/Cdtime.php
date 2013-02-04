<?php

class Hapyfish2_Alchemy_Bll_Cdtime
{
	/**
	 * CD时间列表
	 * 
	 * @param int $uid
	 * @return number
	 */
	public static function getCdList($uid)
	{
		$nowTm = time();
		
		$strengthenRemainTm = self::strengthenCd($uid);

		$trainingRemainTm = self::trainingCd($uid);
		
		$buildingRemainTm = self::buildingCd($uid);
		
		$furnacesRemainTm = self::mixCd($uid);
		
		$hireRemainTm = self::hireCd($uid);
		
		$arenaRemainTm = self::arenaCd($uid);
		
		$occTm = self::occCd($uid);

		$cdList = array('strengthenCd' => $strengthenRemainTm,
				'trainingCd' => $trainingRemainTm,
				'buildingCd' => $buildingRemainTm,
				'mixCd' => $furnacesRemainTm,
				'hireCd' => $hireRemainTm,
				'arenaCd' => $arenaRemainTm,
				'occCd' => $occTm);
		
		return $cdList;
	}
	
	public static function resetCdTmByField($uid, $field)
	{
		if ( !in_array($field, array('strengthenCd', 'trainingCd', 'buildingCd', 'mixCd', 'hireCd', 'arenaCd', 'occCd')) ) {
			return;
		}
		
		$cdList = array();
		$cdList[$field] = self::$field($uid);
		
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'cdList', $cdList);
		return;
	}
	
	public static function strengthenCd($uid)
	{
		//强化时间
		$strengthenInfo = Hapyfish2_Alchemy_Bll_Weapons::getStrCoolTime($uid);
		if ( $strengthenInfo['canStr'] == 1 ) {
			$strengthenRemainTm = 0;
		}
		else {
			$strengthenRemainTm = $strengthenInfo['coolTime'];
		}
		return $strengthenRemainTm;
	}

	public static function trainingCd($uid)
	{
		$nowTm = time();
		
		//训练营时间
    	$userTraining = Hapyfish2_Alchemy_HFC_Training::getAll($uid);
    	
    	$curTraNum = $userTraining['curTraNum'];
    	$trainingList = $userTraining['list'];
		
		$userTraPosNum = Hapyfish2_Alchemy_HFC_User::getUserTrainingPosNum($uid);
	
		if ( $userTraPosNum > $curTraNum ) {
			$trainingRemainTm = 0;
		}
		else {
	    	$trainingTemp = 1000000000;
	    	foreach ( $trainingList as $v ) {
	    		if ( $v['complete_time'] > 0 ) {
		    		$remainTm = $v['complete_time'] - $nowTm;
		    		if ( $remainTm < $trainingTemp ) {
		    			$trainingTemp = $remainTm;
		    		}
	    		}
	    		else {
	    			$trainingTemp = 10000000001;
	    		}

	    		if ( $remainTm < $trainingTemp ) {
	    			$trainingTemp = $remainTm;
	    		}
	    	}
			if ( $trainingTemp == 1000000000 ) {
				$trainingRemainTm = 0;
			}
			else {
				$trainingRemainTm = $trainingTemp < 0 ? 0 : $trainingTemp;
			}
		}
		
		return $trainingRemainTm;
	}
	
	public static function buildingCd($uid)
	{
		$nowTm = time();

		//建筑升级时间
		$buildingCdTm = Hapyfish2_Alchemy_Cache_User::getBuildingCdTm($uid);
		$buildingRemainTm = $buildingCdTm - $nowTm;
		$buildingRemainTm = $buildingRemainTm < 0 ? 0 : $buildingRemainTm;

		return $buildingRemainTm;
	}
	
	public static function mixCd($uid)
	{
		$nowTm = time();
		
		//合成时间
		$furnace = Hapyfish2_Alchemy_HFC_Furnace::getOnRoom($uid);
		$furnacesList = $furnace['furnaces'];
		$furnacesTemp = 1000000000;
		if ( !empty($furnacesList) ) {
			foreach ( $furnacesList as $v ) {
				if ( $v['cid'] > 0 && $v['status'] == 1 ) {
					$remainTime = ($v['start_time'] + $v['need_time']) - $nowTm;
				}
				else {
					$furnacesTemp = 0;
				}
				if ( $remainTime < $furnacesTemp ) {
					$furnacesTemp = $remainTime;
				}
			}
		}
		
		if ( $furnacesTemp == 1000000000 ) {
			$furnacesRemainTm = 0;
		}
		else {
			$furnacesRemainTm = $furnacesTemp < 0 ? 0 : $furnacesTemp;
		}

		return $furnacesRemainTm;
	}
	
	public static function hireCd($uid)
	{
		$nowTm = time();
		
		//酒馆时间
		$userHireData = Hapyfish2_Alchemy_HFC_Hire::getHireData($uid);
		$userHireInfo = $userHireData['data'];
		$hireTemp = 1000000000;
		if ( !empty($userHireInfo) ) {
			foreach ( $userHireInfo as $hire ) {
				$remainTm = $hire['endTime'] - $nowTm;
				if ( $remainTm < $hireTemp ) {
					$hireTemp = $remainTm;
				}
			}
		}
		
		if ( $hireTemp == 1000000000 ) {
			$hireRemainTm = 0;
		}
		else {
			$hireRemainTm = $hireTemp < 0 ? 0 : $hireTemp;
		}
		
		return $hireRemainTm;
	}
	
	public static function arenaCd($uid)
	{
		$nowTm = time();

		//竞技场时间
		$userArena = Hapyfish2_Alchemy_Cache_Arena::getUserArena($uid);

		//没有剩余次数则返回 "-1"
		if ( $userArena['challengeTimes'] < 1 ) {
			return -1;
		}
		else {
			$arenaRemainTm = $userArena['cd'] - $nowTm;
			$arenaRemainTm = $arenaRemainTm < 0 ? 0 : $arenaRemainTm;
			
			return $arenaRemainTm;
		}
	}

	/**
	 * 侵略冷却时间
	 * @param int $uid
	 * @return int
	 */
	public static function occCd($uid)
	{
		$nowTm = time();
		
		$occTemp = 1000000000;
		$occupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($uid);
		if (!$occupyInfo) {
			$occRemainTm = 0;
		}
		else {
	        $usedMerc = $occupyInfo['corps_used'];
	        //homeside
	        $homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
	        foreach ($homeSide as $data) {
	        	if ($usedMerc && isset($usedMerc[$data['id']])) {
	        		if ($nowTm - (int)$usedMerc[$data['id']] < Hapyfish2_Alchemy_Bll_FightOccupy::CORPS_COOLDOWN_INTERVAL) {  
	        			$remainTime = $usedMerc[$data['id']] + Hapyfish2_Alchemy_Bll_FightOccupy::CORPS_COOLDOWN_INTERVAL - $nowTm;  
	        		}
	        		else {
	        			$occTemp = 0;
	        		}
	        	}
	        	else {
	        		$occTemp = 0;
	        	}
	        	if ( $remainTime < $occTemp ) {
	        		$occTemp = $remainTime;
	        	}
	        }
	        if ( $occTemp == 1000000000 ) {
	        	$occRemainTm = 0;
	        }
	        else {
	        	$occRemainTm = $occTemp < 0 ? 0 : $occTemp;
	        }
		}
		
		return $occRemainTm;
	}
	
}