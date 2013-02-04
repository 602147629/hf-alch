<?php

class Hapyfish2_Stat_Bll_Payment
{
	public static function cal($day)
	{
		$begin = strtotime($day);
		$end = $begin + 86400;
		$amount = 0;
		$gold = 0;
		$count = 0;
		
		$yearmonth = date('Ym', $begin);
		$sevenDayTm = $begin - 7*86400;
		$userCount = 0;
		$uidTemp = array();
		$costGold = 0;
		$newPayUserCnt = 0;
		$allPayUserCnt = 0;
		$sevenDayUserCnt = 0;
		
		try {
			$dalPay = Hapyfish2_Stat_Dal_PaymentLog::getDefaultInstance();
			for ($i = 0; $i < DATABASE_NODE_NUM; $i++) {
				for ($j = 0; $j < 10; $j++) {
					//充值信息
					$data = $dalPay->getPaymentLogData($i, $j, $begin, $end);
					if ($data) {
						$uidList = array();
						foreach ($data as $row) {
							$amount += $row['amount'];
							$gold += $row['gold'];
							$count++;
							if ( !isset($uidTemp[$row['uid']]) ) {
								$userCount++;
								$uidTemp[$row['uid']] = 1;
							}
							//首充人数
							if ($row['is_first_pay'] == 1) {
								$newPayUserCnt ++;
							}
						}
					}
				
					//所有充值玩家uid列表
					$allPayUidList = $dalPay->getAllPayUidList($i, $j);
					foreach ( $allPayUidList as $v ) {
						$uid = $v['uid'];
						$allPayUserCnt ++;
						
						//玩家最后一次登录时间
	    				$loginInfo = Hapyfish2_Alchemy_HFC_User::getUserLoginInfo($uid);
						if ( $loginInfo['last_login_time'] >= $sevenDayTm ) {
							$sevenDayUserCnt ++;
						}
					}
						
				}
				
				for ($n = 0; $n < 50; $n++) {
					//岛钻消费信息
					$goldData = $dalPay->getGold($i, $n, $begin, $end);
					if ( $goldData > 0 ) {
						$costGold += $goldData;
					}
				}
			}
			$result =  array('amount' => $amount, 
							 'gold' => $gold, 
							 'costGold' => $costGold, 
							 'count' => $count, 
							 'userCount' => $userCount,
							 'newPayUserCnt' => $newPayUserCnt,
							 'allPayUserCnt' => $allPayUserCnt,
							 'sevenDayUserCnt' => $sevenDayUserCnt);
			return $result;
			
		} catch (Exception $e) {
			
			$result = array('amount' => $amount, 
							'gold' => $gold, 
							'costGold' => $costGold, 
							'count' => $count, 
							'userCount' => $userCount,
							'newPayUserCnt' => $newPayUserCnt,
							'allPayUserCnt' => $allPayUserCnt,
							'sevenDayUserCnt' => $sevenDayUserCnt);
			return $result;
			
		}
	}

}