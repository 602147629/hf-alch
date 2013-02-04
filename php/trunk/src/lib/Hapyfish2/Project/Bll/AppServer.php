<?php

class Hapyfish2_Project_Bll_AppServer
{
	public static function getWebList()
	{
		return Hapyfish2_Project_Cache_AppServer::getWebList();
	}
	
	public static function requestWeb($host, $url)
	{
        $ch = curl_init();
        $header = array();
        $header[] = 'Host: ' . $host;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //max curl execute time
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $cURLVersion = curl_version();
        $ua = 'PHP-cURL/' . $cURLVersion['version'] . ' HapyFish-TOPRest/1.0';
        curl_setopt($ch, CURLOPT_USERAGENT, $ua);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$result = @curl_exec($ch);

        //$errno = @curl_errno($ch);
        //$error = @curl_error($ch);
        curl_close($ch);

        return $result;
	}
}