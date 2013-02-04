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
	
	public static function startTimeGift($job,$uid)
	{
		$userGift = Hapyfish2_Alchemy_Cache_EventGift::getUserTGift($uid);
		$gift = Hapyfish2_Alchemy_Cache_EventGift::getTGDetail($job, 1);
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
						self::addUserWeapon($uid, $v[0],$v[3]);
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
		return array('day'=>$id, 'type'=>$type, 'time'=>$time);
//		return array('day'=>$id, 'type'=>-1, 'time'=>$time);
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
		if($type == 4){
			$rate = 100;
		}else if($type == 3){
			$rate = rand(66, 99);
		}else if($type == 2){
			$rate = rand(33,66);
		}else{
			$rate = rand(0,33);
		}
		$detail = array_values($detail);
    	$detail = array_slice($detail, 4, 11);
    	foreach($detail as $k=>$v){
    		$dnum = json_decode($v, true);
    			$info[] = $dnum[0] + floor(($dnum[1]-$dnum[0])*$rate/100);
    	}
		$info[] = $type;
		$info[] = 0;
		return $info;
	}
	
	public static function useCard($uid, $cid)
	{
		$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$job = $userMercenary['job'];
		$detail = Hapyfish2_Alchemy_Cache_Basic::getGoodsInfo($cid);
		$userInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$userLevel = $userInfo['level'];
		if($userLevel < $detail['useLevel']){
			return -701;
		}
		$cinfo = Hapyfish2_Alchemy_Cache_EventGift::getPackageDetail($cid, $job);
		if(!$cinfo){
			return -200;
		}
		$award = $cinfo['awards'];
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
		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, 1);
		return 1;
	}
	
	public static function getTestGiftStatus($uid)
	{
		$status = false;
		$uTestG = Hapyfish2_Alchemy_Cache_EventGift::getUTGift($uid);
		if(!$uTestG){
			return $status;
		}
		$finish = json_decode($uTestG['finish'], true);
		if(count($finish) >= 3){
			return $status;
		}
		$status = true;
		return $status;
	}
	
	public static function initTestGift($uid)
	{
		$list = self::getStatusList($uid);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'testGiftVo', array('awardList'=>$list));
	}
	
	public static function getStatusList($uid)
	{
		$uTestG = Hapyfish2_Alchemy_Cache_EventGift::getUTGift($uid);
		$list = array(0,0,0);
		if(!$uTestG){
			return -200;
		}
		$finish = json_decode($uTestG['finish'], true);
		if($uTestG['test1'] == 1){
			$list[0] = 1;
			if(in_array(1, $finish)){
				$list[0] = 2;
			}
		}
		if($uTestG['test2'] == 1){
			$list[1] = 1;
			if(in_array(2, $finish)){
				$list[1] = 2;
			}
		}
		if($uTestG['test3'] == 1){
			$list[2] = 1;
			if(in_array(3, $finish)){
				$list[2] = 2;
			}
			$funcLocks = Hapyfish2_Alchemy_HFC_Help::getUnlockFunc($uid);
			if(in_array('hire', $funcLocks)){
				$list[2] = 3;
			}
		}
		return $list;
	}
	
	public static function getTestAward($uid, $type)
	{
		$uTestG = Hapyfish2_Alchemy_Cache_EventGift::getUTGift($uid);
		$finish = json_decode($uTestG['finish'], true);
		if(!in_array($type, array(1,2,3))){
			return -200;
		}
		if(!$uTestG){
			return -200;
		}
		if(in_array($type, $finish)){
			return -200;
		}
		if($type == 1){
			if($uTestG['test1'] != 1){
				return -200;
			}
			$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
			$job = $userMercenary['job'];
			if($job == 1){
				$cid = 11561;
			}else if($job == 2){
				$cid = 11661;
			}else if($job == 3){
				$cid = 11761;
			}
			self::addUserWeapon($uid, $cid,2);
		}else if ($type == 2){
			if($uTestG['test2'] != 1){
				return -200;
			}
			self::addUserWeapon($uid, 11862,2);
		}else if($type == 3){
			$funcLocks = Hapyfish2_Alchemy_HFC_Help::getUnlockFunc($uid);
			if($uTestG['test3'] != 1){
				return -200;
			}
			if(in_array('hire', $funcLocks)){
				return -200;
			}
			Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, 117, 1);
		}
		$finish[] = $type;
		$uTestG['finish'] = json_encode($finish);
		Hapyfish2_Alchemy_Cache_EventGift::updateTestGift($uid, $uTestG);
		$list = self::getStatusList($uid);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'list', $list);
		return 1;
	}
	
	public static function initInviteGift($uid)
	{
		$count = Hapyfish2_Alchemy_HFC_User::getTotalInvite($uid);
		$step = array();
		$uInviteAward = Hapyfish2_Alchemy_Cache_EventGift::getUserIgift($uid);
		if($uInviteAward){
			$step = json_decode($uInviteAward['step'], true);
		}
		$status = array(
			'1'=>1,
			'2'=>1,
			'3'=>1,
			'4'=>1,
			'5'=>1,
			'6'=>1,
			'7'=>1,
			'8'=>1,
			'9'=>1,
			'10'=>1
		);
			
		for($i=1;$i<=10;$i++){
			if($count >= $i){
				$status[$i] = 2;
			}
			if(in_array($i, $step)){
				$status[$i] = 3;
			}
		}
		
		$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$job = $userMercenary['job'];
		$award = array();
		$award[] = array(
				'count'=>1,
				'list'=>array(),
				'isFriendSkill'=>1
			);
		$award[] = array(
			'count'=>2,
			'list'=>array(6615,1),
			'isFriendSkill'=>0
		);
		$award[] = array(
			'count'=>3,
			'list'=>array(5015,10),
			'isFriendSkill'=>0
		);
		$award[] = array(
			'count'=>4,
			'list'=>array(11362,1),
			'isFriendSkill'=>0
		);
		$award[] = array(
			'count'=>5,
			'list'=>array(1515,5),
			'isFriendSkill'=>0
		);
		$award[] = array(
			'count'=>6,
			'list'=>array(11464,1),
			'isFriendSkill'=>0
		);
		$award[] = array(
			'count'=>7,
			'list'=>array(2715,5),
			'isFriendSkill'=>0
		);
		$award[] = array(
			'count'=>8,
			'list'=>array(1015,1),
			'isFriendSkill'=>0
		);
		$award[] = array(
			'count'=>9,
			'list'=>array(6115,1),
			'isFriendSkill'=>0
		);
		$award[] = array(
			'count'=>10,
			'list'=>array(117,1),
			'isFriendSkill'=>0
		);
		return array('count'=> $count, 'inviteStates'=>$status, 'inviteAward'=>$award);
	}
	
	public static function initUserInvite($uid)
	{
		$vo = self::initInviteGift($uid);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'count', $vo['count']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'inviteStates', $vo['inviteStates']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'inviteAward', $vo['inviteAward']);
		return 1;
	}
	
	public static function getIniteAward($uid,$type)
	{
		$init = self::initInviteGift($uid);
		if($init['inviteStates'][$type] != 2){
			return -200;
		}
		
		foreach($init['inviteAward'] as $k => $v){
			if($v['count'] == $type){
				$award = $v['list'];
				break;
			}
		}
		$award = array_chunk($award,2);
		foreach($award as $k1 => $v1){
			$itemType =	substr($v1[0], -2, 1);
			if($itemType == 6){
				self::addUserWeapon($uid, $v1[0], 2);
			}else{
				Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $v1[0], $v1[1]);
			}
		}
		$uInviteAward = Hapyfish2_Alchemy_Cache_EventGift::getUserIgift($uid);
		if($uInviteAward){
			$step = json_decode($uInviteAward['step'], true);
		}else{
			$step = array();
		}
		$step[] = $type;
		$uInviteAward['uid'] = $uid;
		$uInviteAward['step'] = json_encode($step);
		$uInviteAward['type'] = 1;
		Hapyfish2_Alchemy_Cache_EventGift::updateInviteGift($uInviteAward);
		return 1;
	}
	
	public static function getPayStatus($uid)
	{
		$pay = Hapyfish2_Alchemy_Cache_EventGift::getFirshPay($uid);
		if($pay){
			$step = json_decode($pay['step'], true);
			if(count($step) >= 4){
				return false;
			}
		}
		return true;
	}
	
	public static function openFriendBox($uid)
	{
		$keyNum = Hapyfish2_Alchemy_HFC_User::getUserFriendKey($uid);
		if($keyNum < 1){
			return -711;
		}
		$dayBox = Hapyfish2_Alchemy_Cache_EventGift::getDayFriendBox($uid);
		if($dayBox['open'] == 1){
			return -712;
		}
		if($dayBox['get'] == 0){
			return -713;
		}
		Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, 1515, 2);
		Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, 3815, 2);
		Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, 5015, 2);
		$dayBox['open'] = 1;
		Hapyfish2_Alchemy_Cache_EventGift::updateFriendBox($uid, $dayBox);
		$keyNum -= 1;
		Hapyfish2_Alchemy_HFC_User::updateFriendKey($uid, $keyNum);
		return 1;
	}
	public static function InitBox($uid)
	{
		$data = array();
		$keyNum = Hapyfish2_Alchemy_HFC_User::getUserFriendKey($uid);
		$data['num'] = $keyNum;
		$dayBox = Hapyfish2_Alchemy_Cache_EventGift::getDayFriendBox($uid);
		if($dayBox['open'] == 1){
			$data['isgetaward'] = 0;
		}else{
			$data['isgetaward'] = 1;
		}
		$data['isget'] = $dayBox['get'];
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'treasureBoxVo', $data);
		return 1;
	}
	
	public static function checkGift($uid,$payNum)
	{
		$endtime = 1354117179;
		$now = time();
		if($now > $endtime){
			return;
		}
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$pay = $dal->getPayEvent($uid);
		if(!$pay){
			$pay['uid'] = $uid;
			$pay['totalPay'] = 0;
			Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, 217, 1);
		}
		$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$type = $userMercenary['job'];
		$pay['totalPay'] += $payNum;
		$payArr = array(50,100,500,1000);
		$award = array(
			'1'=>array('50'=>array(22064),'100'=>array(21563),'500'=>array(21362),'1000'=>array(11561)),
			'2'=>array('50'=>array(22164),'100'=>array(21763),'500'=>array(11862),'1000'=>array(11661)),
			'3'=>array('50'=>array(22264),'100'=>array(21863),'500'=>array(21462),'1000'=>array(11761))
		);
		$eventPay = $dal->getEventPay($uid);
		$finish = json_decode($eventPay['step'],true);
		foreach($payArr as $num){
			if($pay['totalPay'] >= $num){
				if(!in_array($num,$finish)){
					$awarddetail = $award[$type][$num];
					foreach($awarddetail as $id){
						self::addUserWeapon($uid, $id, 4);
					}
					$finish[] = $num;
					$userPay['uid'] = $uid;
					$userPay['step'] = json_encode($finish);
					$userPay['type'] = 6;
					$dal->insertInviteGift($userPay);
				}
			}
		}
		$dal->insertEventPay($pay);
	}
}