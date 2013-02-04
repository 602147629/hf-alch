<?php
class Hapyfish2_Alchemy_Event_Bll_VipGuess
{
	public static function initVipGuess($uid)
	{
		$data = array('isGetAward'=>1,'isGuess'=>1,'price'=>0);
		$userVipGuess = self::getUserVipGuess($uid);
		if($userVipGuess && $userVipGuess['price'] > 0){
			$data['isGuess'] = 0;
		}
		if($userVipGuess['get'] == 1){
			$data['isGetAward'] = 0;
		}
		if($userVipGuess['price'] > 0){
			$data['price'] = (int)$userVipGuess['price'];
		}
		return $data;
		
	}
	
	public static function getUserVipGuess($uid)
	{
		$key = 'a:u:vip:guess:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			$dal = Hapyfish2_Alchemy_Event_Dal_VipGuess::getDefaultInstance();
			$info = $dal->getUserGuess($uid);
			if($info){
				$cache->set($key, $data);
			}else{
				return null;
			}
			
		}
		return $data;
	}
	
	public static function updateUserGuess($uid,$data)
	{
		$key = 'a:u:vip:guess:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key,$data);
		try {
        	$dal = Hapyfish2_Alchemy_Event_Dal_VipGuess::getDefaultInstance();
            $dal->update($uid,$data);
                
            } catch (Exception $e) {
            	info_log('[Hapyfish2_Alchemy_Cache_Activity::get]' . $e->getMessage(), 'db.err');
                return null;
            }
	}
	
	public static function guessPrice($uid,$price)
	{
		$userVipGuess = self::getUserVipGuess($uid);
		if($userVipGuess && $userVipGuess['price'] > 0){
			return -200;
		}
		if($price >= 999 || $price <= 0){
			return -200;
		}
		$userVipGuess['uid'] = $uid;
		$userVipGuess['price'] = $price;
		$userVipGuess['get'] = isset($userVipGuess['get'])?$userVipGuess['get']:0;
		self::updateUserGuess($uid, $userVipGuess);
		return 1;
		
	}
	
	public static function getGuessAward($uid)
	{
		$userVipGuess = self::getUserVipGuess($uid);
		if($userVipGuess && $userVipGuess['get'] == 1){
			return -200;
		}
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		$vip->userVipDayCard($uid);
		if($userVipGuess){
			$userVipGuess['get'] = 1;
		}else{
			$userVipGuess['uid'] = $uid;
			$userVipGuess['price'] = 0;
			$userVipGuess['get'] = 1;
		}
		self::updateUserGuess($uid, $userVipGuess);
		return 1;
	}
}