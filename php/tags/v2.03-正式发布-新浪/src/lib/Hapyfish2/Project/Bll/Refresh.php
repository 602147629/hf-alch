<?php

class Hapyfish2_Project_Bll_Refresh
{
	public static $LIST = array(
		'notice' => 'manageapi/refreshnotice',
		'appinfo' => 'manageapi/refreshappinfo'
	);
	
	public static function all($name)
	{
		$result = array();
		if (isset(self::$LIST[$name])) {
			$list = Hapyfish2_Project_Bll_AppServer::getWebList();
			if (!empty($list)) {
				$host = str_replace('http://', '', HOST);
				foreach ($list as $server) {
					$url = 'http://' . $server['local_ip'] . '/' . self::$LIST[$name];
					$data = Hapyfish2_Project_Bll_AppServer::requestWeb($host, $url);
					$result[] = array('server_id' => $server['id'], 'result' => $data);
				}
			}
		}
		
		return $result;
	}
}