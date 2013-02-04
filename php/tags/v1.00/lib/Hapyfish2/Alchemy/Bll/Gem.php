<?php

class Hapyfish2_Alchemy_Bll_Gem
{
	public static function get($uid, $cache = false)
	{
		$gem = 0;
		if ($cache) {
			$gem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
		} else {
			try {
		    	$dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
            	$gem = $dalUser->getGem($uid);
			} catch (Exception $e) {
				info_log('[Hapyfish2_Alchemy_Bll_Gem::get]' . $uid . ':' . $e->getMessage(), 'db.err');
			}
		}
		
		return $gem;
	}

	public static function addConsumeGemLog($uid, $info)
	{
		try {
			$dalLog = Hapyfish2_Alchemy_Dal_ConsumeLog::getDefaultInstance();
			$dalLog->insertGem($uid, $info);
			return true;
		} catch (Exception $e) {
			info_log('[Hapyfish2_Alchemy_Bll_Gem::addConsumeGemLog]' . $uid . ':' . $e->getMessage(), 'db.err');
		}
		
		return false;
	}

	public static function consume($uid, $gemInfo)
	{
        $gem = $gemInfo['cost'];

		try {
			$dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
			$dalUser->decGem($uid, $gem);

			Hapyfish2_Alchemy_HFC_User::reloadUserGem($uid);
			Hapyfish2_Alchemy_Bll_UserResult::mergeGem($uid, -$gem);
		} catch (Exception $e) {
			info_log('[Hapyfish2_Alchemy_Bll_Gem::consume]' . $uid . ':' . $e->getMessage(), 'db.err');
			return false;
		}

		$gemInfo['create_time'] = time();
        self::addConsumeGemLog($uid, $gemInfo);

        return true;
	}

	public static function add($uid, $gemInfo)
	{
		$gem = $gemInfo['gem'];
		if ($gem <=0) {
			return false;
		}

		$ok = false;
		try {
			$dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
			$dalUser->incGem($uid, $gem);
			Hapyfish2_Alchemy_HFC_User::reloadUserGem($uid);
			$ok = true;
		} catch (Exception $e) {
			info_log('[Hapyfish2_Alchemy_Bll_Gem::add]' . $uid . ':' . $e->getMessage(), 'db.err');
		}
		if ($ok) {
			if (isset($gemInfo['type'])) {
				$type = $gemInfo['type'];
			} else {
				$type = 0;
			}

			if (isset($gemInfo['time'])) {
				$time = $gemInfo['time'];
			} else {
				$time = time();
			}

			$info = array('uid' => $uid, 'gem' => $gem, 'type' => $type, 'create_time' => $time);
			self::addGemLog($uid, $info);
		}
		return $ok;
	}

	public static function addGemLog($uid, $info)
	{
		try {
			$dalLog = Hapyfish2_Alchemy_Dal_AddGemLog::getDefaultInstance();
			$dalLog->insert($uid, $info);
			return true;
		} catch (Exception $e) {
			info_log('[Hapyfish2_Alchemy_Bll_Gem::addGemLog]' . $uid . ':' . $e->getMessage(), 'db.err');
		}
		
		return false;
	}
}