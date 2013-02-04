<?php

class Hapyfish2_Alchemy_Bll_ConsumeLog
{	
	public static function getCoin($uid, $limit = 50)
	{
		try {
			$dalLog = Hapyfish2_Alchemy_Dal_ConsumeLog::getDefaultInstance();
			return $dalLog->getCoin($uid, $limit);
		} catch (Exception $e) {
			info_log('[Hapyfish2_Alchemy_Bll_ConsumeLog::getCoin]' . $uid . ':' . $e->getMessage(), 'db.err');
		}
		
		return null;
	}
	
	public static function getGem($uid, $limit = 50)
	{
		try {
			$dalLog = Hapyfish2_Alchemy_Dal_ConsumeLog::getDefaultInstance();
			return $dalLog->getGem($uid, $limit);
		} catch (Exception $e) {
			info_log('[Hapyfish2_Alchemy_Bll_ConsumeLog::getGem]' . $uid . ':' . $e->getMessage(), 'db.err');
		}
		
		return null;
	}
}