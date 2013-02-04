<?php

class Hapyfish2_Tool_Repair
{
	public static function repair()
	{
		$num = 0;
		$wnum = 0;
		$db = array();
		for($i=0;$i<DATABASE_NODE_NUM;$i++){
			for($j=0;$j<=9;$j++){
				$db[$i][]= DATABASE_NODE_NUM*$j + $i;
			}
		}
		$dal = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
		foreach($db as $k => $v){
			foreach($v as $id){
				$userList = $dal->getUid($id);
				foreach($userList as $uid){
					$num += 1;
					Hapyfish2_Alchemy_Bll_Recoup::resetMercenary($uid,0);
					$yongbing = Hapyfish2_Alchemy_HFC_FightMercenary::getAll($uid);
					if($yongbing){
						foreach($yongbing as $yid => $yonginfo){
							$wnum += 1;
							Hapyfish2_Alchemy_Bll_Recoup::resetMercenary($uid,$yid);
						}
					}
					
	    			info_log($uid, 'repairBug');
				}
			}
		}
		echo '人数：'.$num.'---佣兵数：'.$wnum;
	}
	
	public static function sendCoin()
	{
		$num = 1;
		$coin = 300000;
		$db = Hapyfish2_Db_Factory::getEventDB('db_0');
    	$rdb = $db['r'];
    	$sql = "select uid from test_user";
    	$uidlist = $rdb->fetchCol($sql);
    	foreach($uidlist as $uid){
    		$num += 1;
    		Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $coin);
    		info_log($uid.'--'.$coin,'sendCoin');
    	}
    	return $num;
	}
	
	public static function sendPackage()
	{
		$num = 0;
		$db = array();
		$cid = 1001115;
		for($i=0;$i<DATABASE_NODE_NUM;$i++){
			for($j=0;$j<=9;$j++){
				$db[$i][]= DATABASE_NODE_NUM*$j + $i;
			}
		}
		$dal = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
		foreach($db as $k => $v){
			foreach($v as $id){
				$userList = $dal->getUid($id);
				foreach($userList as $uid){
					Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, 1001115, 1);
					$num += 1;
				}
					
	    			info_log($uid, 'sendPackage');
				}
			}
		echo '人数：'.$num;
	}
}