<?php

class Hapyfish2_Platform_Bll_WeiboAchieve
{
    protected static $_mcKeyPrex = 'i:u:sinawb:achi';

    //protected static $_gameAchieveId = array(3007,3015,3018,3038,3044,3059,3001,3004,3009,3012,3021,3024,3027,3030,3041,3047,3050,3053,3062,3071,3074,3077,3080,3002,3005,3008,3010,3013,3016,3019,3022,3025,3028,3031,3039,3042,3045,3048,3051,3054,3060,3063,3072,3075,3078,3081);

    protected static $_weiboAchieveId = array(
    									'level'=>array(
    											10=>1,25=>2,35=>3,50=>4,80=>5		
    )	
    );


    public static function checkAchieveId($uid, $type, $achieveId)
    {
    	//return true;
        try {
            $context = Hapyfish2_Util_Context::getDefaultInstance();
            $sessionKey = $context->get('session_key');
            $rest = OpenApi_SinaWeibo_Client::getInstance();
            $rest->setUser($sessionKey);

            $mckey = self::$_mcKeyPrex . $uid;
            $cache = Hapyfish2_Cache_Factory::getMC($uid);
            $lstGained = $cache->get($mckey);
            if (!$lstGained) {
                //get user current achieve from weibo platform
                $lstGained = $rest->listAchieve();
//info_log($uid.'|'.json_encode($lstGained), 'fromsina-checkAchieveId');
                if (null === $lstGained) {
                    return false;
                }
                $cache->set($mckey, $lstGained);
            }

//info_log($achieveId, 'fromsina-checkAchieveId');
            //if is complete
            if (array_key_exists($achieveId, self::$_weiboAchieveId[$type])) {
                $weiboAid = self::$_weiboAchieveId[$type][$achieveId];
                 if (!in_array($weiboAid, $lstGained)) {
//info_log($weiboAid.' call api', 'fromsina-checkAchieveId');
                    $rst = $rest->setAchieve($weiboAid);
                    if ($rst) {
//info_log('delcache', 'fromsina-checkAchieveId');
                        $cache->delete($mckey);
                    }
                }
            }
        }
        catch (Exception $e) {
            //info_log('checkAchieveId_Err:'.$e->getMessage(), 'Hapyfish2_Platform_Bll_WeiboAchieve');
            return false;
        }

        return true;
    }
    
    public static function checkLevelAchieveId($uid, $level)
    {
    	//return true;
        try {
            $context = Hapyfish2_Util_Context::getDefaultInstance();
            $sessionKey = $context->get('session_key');
            $rest = OpenApi_SinaWeibo_Client::getInstance();
            $rest->setUser($sessionKey);

            $mckey = self::$_mcKeyPrex . $uid;
            $cache = Hapyfish2_Cache_Factory::getMC($uid);
            $lstGained = $cache->get($mckey);
            if (!$lstGained) {
                //get user current achieve from weibo platform
                $lstGained = $rest->listAchieve();
//info_log($uid.'|'.json_encode($lstGained), 'fromsina-checkAchieveId');
                if (null === $lstGained) {
                    return false;
                }
                $cache->set($mckey, $lstGained);
            }
			$achieveList = self::$_weiboAchieveId['level'];
            //if is complete
            if (array_key_exists($level, $achieveList)) {
                $weiboAid = $achieveList[$level];
                 if (!in_array($weiboAid, $lstGained)) {
//info_log($weiboAid.' call api', 'fromsina-checkAchieveId');
                    $rst = $rest->setAchieve($weiboAid);
                    if ($rst) {
//info_log('delcache', 'fromsina-checkAchieveId');
                        $cache->delete($mckey);
                        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'sinaAchieve', $weiboAid);
                    }
                }
            }
        }
        catch (Exception $e) {
          //info_log('checkAchieveId_Err:'.$e->getMessage(), 'Hapyfish2_Platform_Bll_WeiboAchieve');
            return false;
        }

        return true;
    }

    public static function checkAchieveAll($uid, $aryAchieve)
    {
		return true;
        try {
            $context = Hapyfish2_Util_Context::getDefaultInstance();
            $sessionKey = $context->get('session_key');
            $rest = OpenApi_SinaWeibo_Client::getInstance();
            $rest->setUser($sessionKey);

            $mckey = self::$_mcKeyPrex . $uid;
            $cache = Hapyfish2_Cache_Factory::getMC($uid);
            $lstGained = $cache->get($mckey);
            if (!$lstGained) {
                //get user current achieve from weibo platform
                $lstGained = $rest->listAchieve();
//info_log($uid.'|old|'.json_encode($lstGained), 'fromsina-checkAchieveAll');
                if (null === $lstGained) {
                    return false;
                }
                $cache->set($mckey, $lstGained);
            }

//info_log($uid.'|'.json_encode($aryAchieve), 'fromsina-checkAchieveAll');
            $newComplete = array();
            foreach ($aryAchieve as $data) {
                $gameAchieveId = $data['taskClassId'];
                //achieve complete && achieve in sina achieve && is new complete achieve
                if ($data['state'] != 0 && array_key_exists($gameAchieveId, self::$_weiboAchieveId)) {
                    $weiboAid = self::$_weiboAchieveId[$gameAchieveId];
                    if (!in_array($weiboAid, $lstGained)) {
                        $newComplete[] = $weiboAid;
                    }
                }
            }

//info_log($uid.'|new|'.json_encode($newComplete), 'fromsina-checkAchieveAll');
            //update sina achieve
            if ($newComplete && count($newComplete) > 0) {
                foreach ($newComplete as $data) {
                    $rest->setAchieve($data);
                }
                $cache->delete($mckey);
            }
        }
        catch (Exception $e) {
            //info_log('checkAchieveAll_Err:'.$e->getMessage(), 'Hapyfish2_Platform_Bll_WeiboAchieve');
            return false;
        }

        return true;
    }

}