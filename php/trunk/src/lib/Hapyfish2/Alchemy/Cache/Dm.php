<?php

class Hapyfish2_Alchemy_Cache_Dm
{
	
	public static function getBasicMC()
	{
		$key = 'mc_0';
		return Hapyfish2_Cache_Factory::getBasicMC($key);
	}
	
    public static function getDmConfig()
    {
        $key = 'a:u:config:dm';
        $cache = self::getBasicMC();
        $data =  $cache->get($key);
       	if($data == false){
			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
			$data = $dal->getDm();
			if($data){
				$cache->set($key, $data);
			}else{
				return null;
			}
		}
		return $data;
    }
    
    public static function updateDmConfig($data)
    {
    	$key = 'a:u:config:dm';
        $cache = self::getBasicMC();
        $cache->set($key, $data);
    	try{
			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
			$dal->updateDm($data);
		} catch (Exception $e) {
            return "fatal error:".$e->getMessage();
	    }
        
    }
}