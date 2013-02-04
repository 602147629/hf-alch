<?php
class Hapyfish2_Alchemy_Bll_EventGift
{
	public static function init($uid)
	{
		$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$type = $userMercenary['job'];
		$levellist = array();
		$sevenlist = array();
		$seven = Hapyfish2_Alchemy_Cache_EventGift::getSevenGift($type);
		foreach($seven as $day=>$s){
			$data = array();
			$data['day'] = $s['day'];
			foreach($s['awards'] as &$a){
				$a = array_slice($a, 0 ,3);
			}
			$data['awards'] = $s['awards'];
			$sevenlist[] = $data;
		}
		$level = Hapyfish2_Alchemy_Cache_EventGift::getLevelGift($type);
		if($level){
			foreach($level as $k=>$v){
				$data= array();
				$data['level'] = $v['level'];
				$data['nextLevel'] = $v['nextLevel'];
				$levellist[] = $data;
			}
		}
		return array('sevenGiftStaticVo'=>$sevenlist, 'levelPhasedGiftStaticVo'=>$levellist);
	}
	
	public static function eventGiftInfo($uid)
	{
		$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$type = $userMercenary['job'];
		$sevenGift = self::getUserSGInfo($uid);
		$tgift = self::getUserTInfo($uid,$type);
		$lgift = self::getUserLInfo($uid,$type);
		$guideGiftVo['sevenGiftVo'] = $sevenGift;
		$guideGiftVo['phasedGiftVo'] = $tgift;
		$guideGiftVo['levelPhasedGiftVo'] = $lgift;
		return array('guideGiftVo'=>$guideGiftVo);
	}
	
	public static function startTimeGift($uid)
	{
		$userGift = Hapyfish2_Alchemy_Cache_EventGift::getUserTGift($uid);
		$gift = Hapyfish2_Alchemy_Cache_EventGift::getTGDetail(1);
		$time = time();
		$endTime = $time + $gift['time'];
		$userGift['id'] = 0;
		$userGift['end'] = $endTime;
		Hapyfish2_Alchemy_Cache_EventGift::updateUserTGift($uid, $userGift);
	}
	
	public static function receiveGift($uid, $type)
	{
		$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$job = $userMercenary['job'];
		$data = array();
		if($type == 1){
			$result = self::receiveTimeGift($job,$uid);
			if(is_array($result)){
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'phasedGiftVo', $result);
			}else{
				$data['error'] = $result;
			}
		}else if ($type == 2)
		{
			$result = self::receiveSevenGift($job,$uid);
			if(is_array($result)){
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'sevenGiftVo', $result);
			}else{
				$data['error'] = $result;
			}
		}else if ($type == 3)
		{
			$result = self::receiveLevelGift($job,$uid);
			if(is_array($result)){
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'levelPhasedGiftVo', $result);
			}else{
				$data['error'] = $result;
			}
		}
		
	}
	
	public static function receiveTimeGift($job,$uid)
	{
		$userGift = Hapyfish2_Alchemy_Cache_EventGift::getUserTGift($uid);
		$time = time();
		$nextime = 0;
		$left = 0;
		$finish = 0;
		if(!$userGift || $time < $userGift['end']){
			return -701;
		}
		if($userGift['id'] == 0){
			$nextId = 1;
		}else{
			$gift = Hapyfish2_Alchemy_Cache_EventGift::getTGDetail($job,$userGift['id']);
			$nextId = $gift['next_id'];
		}
		
		if($nextId <= 0){
			return -702;
		}
		$nextGift = Hapyfish2_Alchemy_Cache_EventGift::getTGDetail($job,$nextId);
		if(!$nextGift){
			return -200;
		}
		$award = $nextGift['list'];
		foreach($award as $k=>$v){
			if($v[1] == 1){
				if( $v[2]> 0){
					Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $v[2]);
				}
			}
			if($v[1] == 2){
				if( $v[2]> 0){
					$gemInfo = array('gem' => $v[2]);
					Hapyfish2_Alchemy_Bll_Gem::add($uid, $gemInfo);
					Hapyfish2_Alchemy_Bll_UserResult::mergeGem($uid, $v[2]);
				}
			}
			if($v[1] == 3){
				if( $v[2]> 0){
					$itemType =	substr($v[0], -2, 1);
					if($itemType == 6){
						self::addUserWeapon($uid, $v[0],$v[2]);
					}else{
						Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $v[0],$v[2]);
					}
				}
			}
		}
		if($nextGift['next_id'] > 0){
			$nnextGift = Hapyfish2_Alchemy_Cache_EventGift::getTGDetail($job,$nextGift['next_id']);
			$userGift['end'] = $time + $nnextGift['time'];
			$awards = $nnextGift['list'];
			$nextime = $nnextGift['time'];
			$type = 1;
		}else{
			$userGift['end'] = 0;
			$type = -1;
			$finish = 1;
			$awards = array();
		}
		$userGift['id'] = $nextId;
		$log = Hapyfish2_Util_Log::getInstance();
		$log->report('401', array($uid, $userGift['id'], $finish));
		Hapyfish2_Alchemy_Cache_EventGift::updateUserTGift($uid, $userGift);
		
		return array('type'=>$type, 'time'=>$nextime,'awards'=>$awards);
	}
	
	public static function receiveSevenGift($job,$uid)
	{
		$status = 0;
		$finish = 0;
		$userGift = Hapyfish2_Alchemy_Cache_EventGift::getUserSGift($uid);
		$day = strtotime(date('Y-m-d'));
		$date = date('Ymd');
		if($userGift['id'] > 0 && $date <= $userGift['date']){
			return -701;
		}
		if($userGift['id'] >= 7){
			return -702;
		}
		$gift = Hapyfish2_Alchemy_Cache_EventGift::getSGDetail($job,$userGift['id'] + 1);
		if(!$gift){
			return -200;
		}
		foreach($gift['awards'] as $k=>$v){
			if($v[1] == 1){
				if( $v[2]> 0){
					Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $v[2]);
				}
			}
			if($v[1] == 2){
				if( $v[2]> 0){
					$gemInfo = array('gem' => $v[2]);
					Hapyfish2_Alchemy_Bll_Gem::add($uid, $gemInfo);
					Hapyfish2_Alchemy_Bll_UserResult::mergeGem($uid, $v[2]);
				}
			}
			if($v[1] == 3){
				$itemType =	substr($v[0], -2, 1);
				if($itemType == 6){
					self::addUserWeapon($uid, $v[0],$v[3]);
				}else{
					Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $v[0],$v[2]);
				}
			}
		}
		$userGift['id'] = $userGift['id']+1;
		$userGift['date'] = $date;
		Hapyfish2_Alchemy_Cache_EventGift::updateUserSGift($uid, $userGift);
		if($userGift['id'] >= 7){
			$finish = 1;
			$status = -1;
		}else{
			$status = 1;
		}
		$log = Hapyfish2_Util_Log::getInstance();
		$log->report('402', array($uid, $userGift['id'], $finish));
		$time = $day + 86400 - time();
		$userGift['id']  = $userGift['id'] + 1 > 7?7:$userGift['id'] + 1;
		return array('day'=>$userGift['id'],'type'=>$status,'time'=>$time);
	}
	
	public static function receiveLevelGift($job,$uid)
	{
		$awards = array();
		$nextlevel = 0;
		$finish = 0;
		$userGift = Hapyfish2_Alchemy_Cache_EventGift::getUserLGift($uid);
		if($userGift == 0){
			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
			$level = $dal->getMinLevel();
		}else{
			$info = Hapyfish2_Alchemy_Cache_EventGift::getLGDetail($job,$userGift);
			$level = $info['nextLevel'];
		}
		$userInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$userLevel = $userInfo['level'];
		if($level <= 0){
			return -702;
		}
		if($userLevel < $level){
			return -701;
		}
		$data = Hapyfish2_Alchemy_Cache_EventGift::getLGDetail($job,$level);
		if(!$data){
			return -200;
		}
		$award = $data['awards'];
		if($award){
			foreach($award as $k => $v){
				if($v[1] == 1){
					if( $v[2]> 0){
						Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $v[2]);
					}
				}
				if($v[1] == 2){
					if( $v[2]> 0){
						$gemInfo = array('gem' => $v[2]);
						Hapyfish2_Alchemy_Bll_Gem::add($uid, $gemInfo);
						Hapyfish2_Alchemy_Bll_UserResult::mergeGem($uid, $v[2]);
					}
				}
				if($v[1] == 3){
					$itemType =	substr($v[0], -2, 1);
					if($itemType == 6){
						self::addUserWeapon($uid, $v[0],$v[3]);
					}else{
						Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $v[0],$v[2]);
					}
				}
			}
		}
		Hapyfish2_Alchemy_Cache_EventGift::updateUserLGift($uid, $level);
		$info = Hapyfish2_Alchemy_Cache_EventGift::getLGDetail($job,$level);
		if($info['nextLevel'] > 0){
			$nextlevel = $info['nextLevel'];
			$type = 1;
			$nextGift = Hapyfish2_Alchemy_Cache_EventGift::getLGDetail($job,$info['nextLevel']);
			
			$awards = $nextGift['awards'];
		}else{
			$finish = 1;
			$type = -1;
			$awards = array();
		}
		$log = Hapyfish2_Util_Log::getInstance();
		$log->report('403', array($uid, $level, $finish));
		return array('level'=>$nextlevel,'type'=>$type, 'awards'=>$awards);
	}
	
	public static function getUserSGInfo($uid)
	{
		$time = 0;
		$type = 1;
		$day = strtotime(date('Y-m-d'));
		$userGift = Hapyfish2_Alchemy_Cache_EventGift::getUserSGift($uid);
		$id = $userGift['id'] + 1;
		$date = date('Ymd');
		if($date > $userGift['date'] ){
			$type = 0;
		}else{
			$time = $day + 86400 - time();
		}
		if($userGift['id'] >= 7){
			$type = -1;
		}
//		return array('day'=>$id, 'type'=>$type, 'time'=>$time);
		return array('day'=>$id, 'type'=>-1, 'time'=>$time);
	}
	
	public static function getUserTInfo($uid,$job)
	{
		$type = 1;
		$nextime = 0;
		$award = array();
		$nextAward = array();
		$left = 0;
		$userGift = Hapyfish2_Alchemy_Cache_EventGift::getUserTGift($uid);
		if($userGift['id'] == 0){
			$nextId = 1;
		}else{
			$gift = Hapyfish2_Alchemy_Cache_EventGift::getTGDetail($job,$userGift['id']);
			$nextId = $gift['next_id'];
		}
		
		if($nextId <= 0){
			$type = -1;
		}else{
			$nextGift = Hapyfish2_Alchemy_Cache_EventGift::getTGDetail($job,$nextId);
			$award = $nextGift['list'];
			if($nextGift['next_id'] > 0){
				$nnGift = Hapyfish2_Alchemy_Cache_EventGift::getTGDetail($job,$nextGift['next_id']);
				$nextAward = $nnGift['list'];
				$nextime = $nnGift['time'];
			}
			$time = time();
			if($time >= $userGift['end']){
				$type = 0;
			}else{
				$left = $userGift['end'] - $time;
			}
		}
		return array('type'=>$type, 'time'=>$left, 'awards'=>$award, 'nextAwards'=>$nextAward,'nextTime'=>$nextime);
	}
	
	public static function getUserLInfo($uid,$job)
	{
		$type = 1;
		$id = 0;
		$award = array();
		$nextAward = array();
		$nextLevel = 0;		
		$userGift = Hapyfish2_Alchemy_Cache_EventGift::getUserLGift($uid);
		if($userGift == 0){
			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
			$level = $dal->getMinLevel();
		}else{
			$info = Hapyfish2_Alchemy_Cache_EventGift::getLGDetail($job,$userGift);
			$level = $info['nextLevel'];
		}
		if($level <= 0){
			$type = -1;
		}else{
			$userInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
			$userLevel = $userInfo['level'];
			$nextLevel = $level;
			if($userLevel >= $level){
				$type = 0;
			}
			$gift = Hapyfish2_Alchemy_Cache_EventGift::getLGDetail($job,$level);
			$award = $gift['awards'];
			if($gift['nextLevel']>0){
				$nextGift = Hapyfish2_Alchemy_Cache_EventGift::getLGDetail($job,$gift['nextLevel']);
				$nextAward = $nextGift['awards'];
			}		
		}
		return array('type'=>$type, 'awards'=>$award,'level'=>$nextLevel);
	}
	
	public static function addUserWeapon($uid,$cid,$type)
	{
		$ok = false;
		$widTemp = Hapyfish2_Alchemy_HFC_Weapon::getNewWeaponId($uid);
		$cidTemp = str_pad($cid, 7, 0, STR_PAD_LEFT);
		$wid = $widTemp . $cidTemp;
		$detail = Hapyfish2_Alchemy_Cache_Basic::getWeaponInfo($cid);
		if($detail == null){
			return $ok;
		}
		$newInfo = self::getQuality($cid, $detail, $type);
		$newDurability = 1000;
		$binfo = array((int)$wid, 0, $newDurability);
		$binfo = array_merge($binfo, $newInfo);
        try {
            $dalWeapon = Hapyfish2_Alchemy_Dal_Weapon::getDefaultInstance();
        	$oldWeapon = $dalWeapon->getWeaponByCid($uid, $cid);
        	if (!$oldWeapon) {
        		$newData = array();
        		$newData[] = $binfo;
        		$newWeapon = array('uid'=>$uid, 'cid'=>$cid, 'count'=>1,'data'=>json_encode($newData));
                $dalWeapon->insert($uid, $newWeapon);
        	}
        	else {
        		$oldData = json_decode($oldWeapon['data']);
        		$oldData[] = $binfo;
        		$newCount = $oldWeapon['count'] + 1;
        		$newWeapon = array('count' => $newCount, 'data' => json_encode($oldData));
        		$dalWeapon->update($uid, $cid, $newWeapon);
        	}
        	Hapyfish2_Alchemy_HFC_Weapon::loadAll($uid);
        	$ok = true;
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_Weapon::addOne:'. $uid. ']' . $e->getMessage(), 'db.err');
        }
        
        if ($ok) {
    		$addItem = array($cid, 1, $wid, $newDurability);
    		$addItem = array_merge($addItem, $newInfo);
    		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'addItems', array($addItem));
			//添加图鉴
			Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $cid);
        }
        return $ok;
	}
	
	public static function getQuality($cid,$detail,$type)
	{
		$info = array();
		$detail = array_values($detail);
    	$detail = array_slice($detail, 4, 9);
    	foreach($detail as $k=>$v){
    		$dnum = json_decode($v, true);
    		if($type == 4){
    			$info[] = $v[1];
    		}else{
    			$start = $dnum[0] + floor(($dnum[1]-$dnum[0])*$type/4);
    			$end = $dnum[0] + floor(($dnum[1]-$dnum[0])*($type+1)/4);
    			$info[] = rand($start, $end);
    		}
    	}
		$info[] = $type;
		return $info;
	}
}