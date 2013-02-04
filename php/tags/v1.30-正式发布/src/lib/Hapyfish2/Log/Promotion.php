<?php

class Hapyfish2_Log_Promotion
{
    public static function checkCode($code)
    {
    	if (!empty($code)) {
    	    $tmp = explode('.', $code);
	    	if (empty($tmp)) {
	    		return false;
	    	}
	    	$count = count($tmp);
	    	if ($count != 3) {
	    		return false;
	    	}
	        $id = $tmp[0];
	        $t = $tmp[1];
			$sig = $tmp[2];
	        $vsig = md5($id . $t . APP_SECRET);
	        if ($sig != $vsig) {
	        	return false;
	        }
	        return array('id' => $id, 't' => $t);
    	}
    	
    	return false;
    }
	
	public static function handle($code)
	{
		$info = self::checkCode($code);
		if ($info) {
			$log = Hapyfish2_Util_Log::getInstance();
			$log->report('promotion', array($info['id'], $info['t']));
			
			return true;
		}
		
		return false;
	}

}