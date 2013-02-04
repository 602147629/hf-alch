<?php

class Hapyfish2_Alchemy_Bll_ConsumeLog
{	
	public static function getCoin($uid, $year, $month, $limit = 50)
	{
		try {
			if ($month < 10) {
				$month = '0' . $month;
			}
			$yearmonth = $year . $month;
			$dalLog = Hapyfish2_Alchemy_Dal_ConsumeLog::getDefaultInstance();
			return $dalLog->getCoin($uid, $yearmonth, $limit);
		} catch (Exception $e) {
			info_log('[Hapyfish2_Alchemy_Bll_ConsumeLog::getCoin]' . $uid . ':' . $e->getMessage(), 'db.err');
		}
		
		return null;
	}
	
	public static function getGem($uid, $year, $month, $limit = 50)
	{
		try {
			if ($month < 10) {
				$month = '0' . $month;
			}
			$yearmonth = $year . $month;
			$dalLog = Hapyfish2_Alchemy_Dal_ConsumeLog::getDefaultInstance();
			return $dalLog->getGem($uid, $yearmonth, $limit);
		} catch (Exception $e) {
			info_log('[Hapyfish2_Alchemy_Bll_ConsumeLog::getGem]' . $uid . ':' . $e->getMessage(), 'db.err');
		}
		
		return null;
	}
}