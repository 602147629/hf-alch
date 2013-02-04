<?php

class Hapyfish2_Alchemy_Bll_Invite
{
	public static function add($inviteUid, $newUid, $time = null)
	{
		if (!$time) {
			$time = time();
		}
		
		$ok = Hapyfish2_Alchemy_Bll_InviteLog::add($inviteUid, $newUid, $time);
		if ($ok) {
			$log = Hapyfish2_Util_Log::getInstance();
			$report = array($inviteUid);
			$log->report('411', $report);
			//500金币
			Hapyfish2_Alchemy_HFC_User::incUserCoin($inviteUid, 500);
			//1瓶大行动力药剂
			Hapyfish2_Alchemy_HFC_Goods::addUserGoods($inviteUid, 715, 1);
		} else {
			info_log('[' . $inviteUid . ':' . $newUid, 'invite_failure');
		}
	}
	
	public static function refresh($uid, $list)
	{
		if (empty($list)) {
			return 0;
		}
		
		$num1 = count($list);
		$log = Hapyfish2_Alchemy_Bll_InviteLog::getAll($uid);
		$all = true;
		if ($log) {
			$num2 = count($log);
			if ($num1 == $num2) {
				return 0;
			}
			$all = false;
			$tmp = array();
			foreach ($log as $f) {
				$tmp[$f['fid']] = $f['time'];
			}
		}
		
		$data = array();
		$time = time();
		foreach ($list as $puid) {
			$user = Hapyfish2_Platform_Cache_UidMap::getUser($puid);
			if ($user) {
				$fid = $user['uid'];
				if ($all) {
					$data[$fid] = array('fid' => $fid, 'time' => $time);
				} else {
					if (!isset($tmp[$fid])) {
						$data[$fid] = array('fid' => $fid, 'time' => $time);
					}
				}
			}
		}

		$count = count($data);
		if ($count > 0) {
			foreach ($data as $user) {
				self::add($uid, $user['fid'], $user['time']);
			}
		}
		
		return $count;
	}
	
}