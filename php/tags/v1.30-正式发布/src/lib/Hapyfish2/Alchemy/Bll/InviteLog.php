<?php

class Hapyfish2_Alchemy_Bll_InviteLog
{
	public static function add($uid, $fid, $t = null)
	{
		$ok = false;
		if (!$t) {
			$t = time();
		}
		$info = array(
			'uid' => $uid,
			'fid' => $fid,
			'time' => $t
		);
		
		try {
			$dalLog = Hapyfish2_Alchemy_Dal_InviteLog::getDefaultInstance();
			$dalLog->insert($uid, $info);
			$ok = true;
		} catch (Exception $e) {
			info_log('Hapyfish2_Alchemy_Bll_InviteLog::add' . $e->getMessage(), 'db.err');
		}
		return $ok;
	}
	
	public static function get($uid)
	{
		$list = self::getAll($uid);
		if ($list) {
			$count = count($list);
		} else {
			$count = 0;
		}
		return array('list' => $list, 'count' => $count);
	}
	
	public static function getAll($uid)
	{
		try {
			$dalLog = Hapyfish2_Alchemy_Dal_InviteLog::getDefaultInstance();
			return $dalLog->getAll($uid);
		} catch (Exception $e) {
			info_log('Hapyfish2_Alchemy_Bll_InviteLog::getAll' . $e->getMessage(), 'db.err');
		}
		
		return null;
	}
	
}