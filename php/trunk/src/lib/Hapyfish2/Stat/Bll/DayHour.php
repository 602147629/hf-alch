<?php

class Hapyfish2_Stat_Bll_DayHour
{
	public static function getMain($day)
	{
		$data = null;
		try {
			$dal = Hapyfish2_Stat_Dal_MainHour::getDefaultInstance();
			$data = $dal->getDay($day); 
		} catch (Exception $e) {
			
		}
		
		return $data;
	}
}