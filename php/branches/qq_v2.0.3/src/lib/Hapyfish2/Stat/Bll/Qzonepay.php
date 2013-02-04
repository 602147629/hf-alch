<?php

class Hapyfish2_Stat_Bll_Qzonepay
{
	public static function getDay($day)
	{
		$data = null;
		try {
			$dalMain = Hapyfish2_Stat_Dal_Main::getDefaultInstance();
			$data = $dalMain->getDay($day); 
		} catch (Exception $e) {
			
		}
		
		return $data;
	}
	
	public static function getAll()
	{
		$amt = $payamtCoins = $pubacctPayamtCoins = $userCount = $payCount = 0;
		$uidTemp = array();
		
		$dal = Hapyfish2_Stat_Dal_Qzonepay::getDefaultInstance();
		
		try {
			for ( $i = 0; $i < 4; $i++ ) {
				for ( $j = 0; $j < 10; $j++ ) {
					$data = $dal->getAll($i, $j);
					

					if ($data) {
						$uidList = array();
						foreach ($data as $row) {
							$amt += $row['amt'];
							$payamtCoins += $row['payamt_coins'];
							$pubacctPayamtCoins += $row['pubacct_payamt_coins'];
							if ( !isset($uidTemp[$row['uid']]) ) {
								$userCount++;
								$uidTemp[$row['uid']] = 1;
							}
							$payCount++;
						}
					}
				}
			}
			

			$result =  array(
							'pay' => $amt/10 + $payamtCoins,
							'amt' => $amt,
							'payamt_coins' => $payamtCoins,
							'pubacct_payamt_coins' => $pubacctPayamtCoins,
							'payCount' => $payCount,
							'userCount' => $userCount);
			
			
		} catch (Exception $e) {
			$result = array('status' => -1,
							'msg' => $e->getMessage());
		}
		
		return $result;
	}
	
}