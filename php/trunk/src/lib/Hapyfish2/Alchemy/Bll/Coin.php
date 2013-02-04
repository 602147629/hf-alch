<?php

class Hapyfish2_Alchemy_Bll_Coin
{
	public static function get($uid, $cache = false)
	{
		$gem = 0;
		if ($cache) {
			$gem = Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
		} else {
			try {
		    	$dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
            	$gem = $dalUser->getCoin($uid);
			} catch (Exception $e) {
				info_log('[Hapyfish2_Alchemy_Bll_Coin::get]' . $uid . ':' . $e->getMessage(), 'db.err');
			}
		}
		return $gem;
	}
	
	public static function consume($uid, $coin, $summary)
	{
		$ok = Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $coin,99, true);
		if ($ok) {
			$info = array(
				'uid' => $uid,
				'cost' => $coin,
				'summary' => $summary,
				'create_time' => time()
			);
	        self::addConsumeCoinLog($uid, $info);
		}
        return $ok;
	}

	public static function addConsumeCoinLog($uid, $info)
	{
		try {
			$dalLog = Hapyfish2_Alchemy_Dal_ConsumeLog::getDefaultInstance();
			$dalLog->insertCoin($uid, $info);
			return true;
		} catch (Exception $e) {
			info_log('[Hapyfish2_Alchemy_Bll_Coin::addConsumeCoinLog]' . $uid . ':' . $e->getMessage(), 'db.err');
		}
		return false;
	}
}