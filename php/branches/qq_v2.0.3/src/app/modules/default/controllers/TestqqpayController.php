<?php

/**
 * Alchemy tools controller
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/02    Nick
 */
class TestqqpayController extends Hapyfish2_Controller_Action_Api
{
	

	protected function getClientIP()
	{
		$ip = false;
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) {
				array_unshift($ips, $ip);
				$ip = false;
			}
			for ($i = 0, $n = count($ips); $i < $n; $i++) {
				if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])) {
					$ip = $ips[$i];
					break;
				}
			}
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
	
		return $ip;
	}
	
//	public function __construct()
//	{
//		$stop = true;
//		$ip = $this->getClientIP();
//		if ( $ip == '180.168.126.10' ) {
//			$stop = false;
//		}
//	
//		if ($stop) {
//			echo '-100';
//			exit;
//		}
//	}
	
    protected function echoResult($data)
    {
        header("Cache-Control: no-store, no-cache, must-revalidate");
        echo json_encode($data);
        exit;
    }
    
    public function testpay1Action()
    {
    	$uid = $this->uid;
    	Hapyfish2_Alchemy_Bll_Shop::buyOneItemQq($uid,7815,1);
    	$result = Hapyfish2_Alchemy_Bll_UserResult::all();
    	$data = array('url'=>$result['payJs'],'token'=>$result['token']);
    	echo json_encode($data);
    	exit;
    }
    
    public function testorderAction()
    {
    	$uid = $this->uid;
    	Hapyfish2_Alchemy_Bll_Order::initorder($uid, 1);
    	$result = Hapyfish2_Alchemy_Bll_UserResult::all();
    	$data = array('url'=>$result['payJs'],'token'=>$result['token']);
    	echo json_encode($data);
    	exit;
    }
}