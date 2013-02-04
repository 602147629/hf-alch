<?php

class Hapyfish2_Alchemy_Bll_Person
{
	public static function genPersonVo($list)
	{
		$personVo = array();
		foreach ( $list as $v ) {
			$personInfo = Hapyfish2_Alchemy_Cache_Basic::getPerson($v);
			
			$addPerson = array('id' => $personInfo['id'],
							   'cid' => $personInfo['cid'],
							   'name' => $personInfo['name'],
							   'className' => $personInfo['class_name'],
							   'faceClass' => $personInfo['face_class'],
							   'tips' => $personInfo['tips'],
							   'clickValue' => $personInfo['click_value'],
							   'clickType' => $personInfo['click_type'],
							   'faceX' => $personInfo['face_x'],
							   'faceZ' => $personInfo['face_z'],
							   'x' => $personInfo['x'],
							   'z' => $personInfo['z'],
							   'fiddleRangeX' => $personInfo['fiddle_range_x'],
							   'fiddleRangeZ' => $personInfo['fiddle_range_z']);
			$personVo[] = $addPerson;
		}
		return $personVo;
	}
	
	/**
	 * 显示NPC
	 * @param int $uid
	 * @param int $pid
	 */
	public static function addPerson($uid, $pid)
	{
		$personInfo = Hapyfish2_Alchemy_Cache_Basic::getPerson($pid);
		if (!$personInfo) {
			return -200;
		}
		
		$userPerson = Hapyfish2_Alchemy_HFC_Person::getPerson($uid);
		
		$userPerson[$pid] = 1;
		Hapyfish2_Alchemy_HFC_Person::updatePerson($uid, $userPerson);
		
		return 1;
	}

	/**
	 * 隐藏NPC
	 * @param int $uid
	 * @param int $pid
	 */
	public static function removePerson($uid, $pid)
	{
		$personInfo = Hapyfish2_Alchemy_Cache_Basic::getPerson($pid);
		if (!$personInfo) {
			return -200;
		}
		
		$userPerson = Hapyfish2_Alchemy_HFC_Person::getPerson($uid);
		
		$userPerson[$pid] = 0;
		Hapyfish2_Alchemy_HFC_Person::updatePerson($uid, $userPerson);
		
		return 1;
	}
	
	/**
	 * 重置NPC状态
	 * @param int $uid
	 * @param int $id
	 * @param int $type
	 */
	public static function resetPerson($uid, $pid)
	{
		$personInfo = Hapyfish2_Alchemy_Cache_Basic::getPerson($pid);
		if (!$personInfo) {
			return -200;
		}
		
		$userPerson = Hapyfish2_Alchemy_HFC_Person::getPerson($uid);
		if ( isset($userPerson[$pid]) ) {
			unset($userPerson[$pid]);
		}
		Hapyfish2_Alchemy_HFC_Person::updatePerson($uid, $userPerson);
		
		return 1;
	}
	
}