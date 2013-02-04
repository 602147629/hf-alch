<?php

class Hapyfish2_Log_Feed
{
    public static function checkCode($code)
    {
    	if (!empty($code)) {
    	    $tmp = explode('.', $code);
	    	if (empty($tmp)) {
	    		return false;
	    	}
	    	$count = count($tmp);
	    	if ($count != 5) {
	    		return false;
	    	}
	        $uid = $tmp[0];
	        $id = $tmp[1];
	        $dt = $tmp[2];
	        $t = $tmp[3];
			$sig = $tmp[4];
	        $vsig = md5($uid . $id . $dt . $t . APP_SECRET);
	        if ($sig != $vsig) {
	        	return false;
	        }
	        return array('uid' => $uid, 'id' => $id, 'dt' => $dt, 't' => $t);
    	}
    	
    	return false;
    }
	
	public static function handle($code)
	{
		$info = self::checkCode($code);
		if ($info) {
			$log = Hapyfish2_Util_Log::getInstance();
			$log->report('feed', array($info['uid'], $info['id'], $info['t']));
			
			return true;
		}
		
		return false;
	}

}