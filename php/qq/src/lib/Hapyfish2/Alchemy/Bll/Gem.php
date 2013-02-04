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

	public static function consume($uid, $gemInfo, $type=99)
	{
        $gem = $gemInfo['cost'];

		try {
			$dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
			$dalUser->decGem($uid, $gem);
//			$vip = new Hapyfish2_Alchemy_Bll_Vip();
//			$vip->addGrowUp($uid, $gem/10);
			Hapyfish2_Alchemy_HFC_User::reloadUserGem($uid);
			Hapyfish2_Alchemy_Bll_UserResult::mergeGem($uid, -$gem);
			$userFight = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
			$fightLevel = $userFight['level'];
    		$log = Hapyfish2_Util_Log::getInstance();
        	$log->report('GemLevel', array($uid, $fightLevel, $gem, $type));
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

			$info = array('uid' => $uid, 'gold' => $gem, 'type' => $type, 'create_time' => $time);
			if (isset($gemInfo['summary'])) {
				$info['summary'] = $gemInfo['summary'];
			}
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
	
	public static function getGemType($cid)
	{
		$itemType =	substr($cid, -2, 1);
		$type = 99;
		if($cid == 1214){
			$type = 3;
		}else if (in_array($cid, array(4415,6615,7715,9215,9515))){
			$type = 1;
		}else if (in_array($cid, array(4515,6715,7815,9315,9415))){
			$type = 2;
		}else if ($cid == 2715){
			$type = 6;
		}else if (in_array($cid, array(6115,6215,6315))){
			$type = 10;
		}else if ($cid == 1015){
			$type = 9;
		}
		if($itemType == 3){
			$type = 5;
		}
		return $type;
	}
}