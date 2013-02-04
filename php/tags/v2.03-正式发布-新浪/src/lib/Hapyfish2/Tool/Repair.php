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
					$zhujue = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
					$zhujue['weapon'] = array();
					Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $zhujue, true); //重置主角状态
					Hapyfish2_Alchemy_Bll_Recoup::resetMercenary($uid,0);
					$yongbing = Hapyfish2_Alchemy_HFC_FightMercenary::getAll($uid);
					if($yongbing){
						foreach($yongbing as $yid => $yonginfo){
							$yonginfo['weapon'] = array();
							Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $yid, $yonginfo, true);//重置佣兵状态
							Hapyfish2_Alchemy_Bll_Recoup::resetMercenary($uid,$yid);
						}
					}
					$weapon = Hapyfish2_Alchemy_HFC_Weapon::getAll($uid);
					if($weapon){
				    	foreach($weapon as $k => $v){
				    		$wnum += 1;
			    			Hapyfish2_Alchemy_HFC_Weapon::delWeapon($uid, $v['wid']);
			    			Hapyfish2_Alchemy_Bll_EventGift::addUserWeapon($uid, $v['cid'], $v['type']);
				    	}
	    			}
	    			info_log($uid, 'repairBug');
				}
			}
		}
		echo '人数：'.$num.'---装备数：'.$wnum;
	}
}