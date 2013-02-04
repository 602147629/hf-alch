<?php

class Hapyfish2_Alchemy_Bll_Vip
{
	const DURATION = 1; //vip过期后续费有效时间
	const VIP_CONINUE = 120; //vip特权延续时间
	const DAY_DEBUFF = 0;
	const LIMIT = 10;
	private static $_skipFight = array(50,80,99999,99999,99999);
	protected $_levelArr = array(0,0,0,0,0,0); //卡片成长
	protected $_vipLevelAward = array(
		'1' => array(1000815,1),
		'2' => array(1000815,1),
		'3' => array(1000915,1),
		'4' => array(1000915,1),
		'5' => array(1001015,1)
	); //vip升级奖励
	
	protected $_vipArena = array(
		'1' => array(0,0),
		'2' => array(0,0),
		'3' => array(5,5),
		'4' => array(5,5),
		'5' => array(5,5)
	); //vip竞技场
	
	protected $_vipSp = array(
		'1' => array(6,60),
		'2' => array(6,70),
		'3' => array(7,80),
		'4' => array(7,90),
		'5' => array(8,100)
	); //vip竞技场
	
	protected $_vipCard = array(
		'6115'=>array(1, 1), //[vip类型，有效期]
		'6215'=>array(2, 7),
		'6315'=>array(3, 30)
	); //卡片对应类型
	protected $_levelExp = array(100,500,2000,10000); //vip经验 
	protected $_dayGrowUp;//当前成长
	protected $_level; //vip等级
	protected $_vipInfo; //用户vip信息
	protected $_levelUp;	//是否需要更新
	protected $_change;	//是否需要更新
	protected $_vipStatus = false;	//vip是否过期
	protected $_vipAddition = array();
	
	
	protected function _initVip($uid)
	{	
		$this->_vipInfo = Hapyfish2_Alchemy_Cache_Vip::getUserInfo($uid);
		if(!$this->_vipInfo){
			$this->_vipInfo =  array(
				'uid'=>$uid,
				'starttime'=>0,
				'endtime'=>0,
				'growup'=>0,
				'level'=>0,
				'type'=>0,
				'settlementtime'=>0,
				'vipStatus'=>0
			);
			return;
		}
		$this->_getGrowUp();
		$level = $this->_getLevel();
		if($level > $this->_vipInfo['level']){
			$this->_levelUp = true;
			$this->_vipInfo['level'] = $level;
		}
		
	}
	//取得当前vip等级
	protected function _getLevel()
	{
		foreach($this->_levelExp as $k => $v){
			if($this->_vipInfo['growup'] < $v){
				return $k+1;
			}
		}
		return count($this->_levelExp) + 1;
	}
	//取得当前成长值
	protected function _getGrowUp()
	{
		if(!$this->_vipInfo){
			$this->_dayGrowUp = 0;
			return ;
		}
		$growUp = $this->_getGrowDay();
		$this->_dayGrowUp = $this->_levelArr[$this->_vipInfo['type']];
		$growChange = $this->_dayGrowUp * $growUp['add'] - self::DAY_DEBUFF * $growUp['del'];
		$this->_vipInfo['growup'] = $this->_vipInfo['growup'] + $growChange >= 0 ? $this->_vipInfo['growup'] + $growChange : 0;
	}
	//自动结算成长
	protected function _getGrowDay()
	{
		$info = $this->_vipInfo;
		$now = time();
		$validity = intval(($info['endtime'] - $info['starttime'])/86400);//vip天数
		$nowDay = floor(($now - $info['starttime'])/86400);//开通vip到现在的天数
		$settlementDay = $info['settlementtime'];//结算的天数
		//vip是否过期
		if($now < ($info['endtime'] + self::VIP_CONINUE)){ 
			$this->_vipStatus = true;
		}
		//当日计算完
		if($settlementDay == $nowDay){ 
			return array('add'=>0,'del'=>0);
		}
		$this->_vipInfo['settlementtime'] = $nowDay;
		$addEnd = $nowDay >= $validity ? $validity : $nowDay;
		$add = $addEnd - $settlementDay;
		$del = $nowDay - $validity - self::DURATION > 0 ? $nowDay - $validity - self::DURATION : 0;
		$this->_change = true;
		return array('add'=>$add,'del'=>$del);
	}
	
	public function getInfo($uid)
	{
		if ( $uid == 10027 ) {
			info_log('$uid-0-2-1-1:'.$uid, 'test_session');
		}
		$this->_initVip($uid);
		$totalPay = Hapyfish2_Alchemy_HFC_User::getTotalPay($uid);
		if ( $uid == 10027 ) {
			info_log('$uid-0-2-1-2:'.$uid, 'test_session');
		}
		self::checkChange();
		if ( $uid == 10027 ) {
			info_log('$uid-0-2-1-3:'.$uid, 'test_session');
		}
		$data = array();
		if($this->_vipInfo['level'] == 0){
			$firstVip = 1;
			$max = 10;
		}else{
			$firstVip = 0;
			$max = $this->_levelExp[$this->_vipInfo['level']-1];
		}
		
		$nextPay = self::getNextPay($totalPay);
		return array(
			'vipLevel' 			=> isset($this->_vipInfo['level'])?$this->_vipInfo['level']:0,
			'vipGrowUp'			=> isset($this->_vipInfo['growup'])?$this->_vipInfo['growup']:0,
			'vipEndTime'		=> isset($this->_vipInfo['endtime'])?$this->_vipInfo['endtime']:0,
			'vipGrowUpForDay'	=> $this->_dayGrowUp?$this->_dayGrowUp:0,
			'vipGemNum'			=>$totalPay,
			'firstVip'			=>$firstVip,
			'nextPay'			=>$nextPay,
			'maxPay'			=>$max
		);
	}
	
	public function checkChange()
	{
		if($this->_change == true){
			if($this->_vipInfo['level'] == 0){
				$firstVip = 1;
				$max = 10;
			}else{
				$firstVip = 0;
				$max = $this->_levelExp[$this->_vipInfo['level']-1];
			}
			$totalPay = Hapyfish2_Alchemy_HFC_User::getTotalPay($this->_vipInfo['uid']);
			$nextPay = self::getNextPay($totalPay);
			Hapyfish2_Alchemy_Cache_Vip::updateVip($this->_vipInfo);
			$change['vipLevel'] = $this->_vipInfo['level'];
			$change['vipGrowUp'] = $this->_vipInfo['growup'];
			$change['vipEndTime'] = $this->_vipInfo['endtime'];
			$change['vipGrowUpForDay'] = $this->_dayGrowUp;
			$change['firstVip'] = $firstVip;
			$change['nextPay'] = $nextPay;
			$change['maxPay'] = $max;
			Hapyfish2_Alchemy_Bll_UserResult::addField($this->_vipInfo['uid'], 'vipChange', $change);
			$spchange = Hapyfish2_Alchemy_Bll_VipWelfare::getSpArr($this->_vipInfo['uid']);
			Hapyfish2_Alchemy_Bll_UserResult::addField($this->_vipInfo['uid'], 'linePowerClass', $spchange);
			
		}
		if($this->_levelUp == true){
			$userAward = Hapyfish2_Alchemy_Cache_Vip::getVipLevelAward($this->_vipInfo['uid']);
			$this->_reloadSkip();
			$this->_reloadArena();
			$this->_reloadOrder();
			$finish = array();
			if($userAward){
				$finish = json_decode($userAward['step']);
			}
			if(in_array($this->_vipInfo['level'],$finish)){
				return;
			}
		}
	}
	
	public  function sendUpAward($uid, $userlevel)
	{
		$this->_initVip($uid);
		if($this->_vipStatus !=true){
			return;
		}
		$finish = array();
		$userLevelUpa = Hapyfish2_Alchemy_Cache_EventGift::getVipLevelUp($uid);
		$finish = json_decode($userLevelUpa['step'],true);
		$level = $this->_vipInfo['level'];
		if(in_array($userlevel, $finish)){
			return;
		}
		$award = $this->_vipLevelAward;
		$detail = $award[$level];
		Hapyfish2_Alchemy_Bll_Mix::addNewItem($this->_vipInfo['uid'], $detail[0],$detail[1]);
		$finish[] = $userlevel;
		$userLevelUpa['uid'] = $this->_vipInfo['uid'];
		$userLevelUpa['step'] = json_encode($finish);
		$userLevelUpa['type'] = 3;
		Hapyfish2_Alchemy_Cache_EventGift::updateVipLevelUp($userLevelUpa);
		
	}
	
	public function userVipCard($uid,$cid,$count)
	{
		$time = time();
		$this->_initVip($uid);
		$info = $this->_vipInfo;
		if($info['type'] == 0){
			$this->_vipInfo['uid'] = $uid;
			$this->_vipInfo['starttime'] = $time;
			$this->_vipInfo['endtime'] = $time + 86400 * $this->_vipCard[$cid][1] * $count;
			$this->_vipInfo['level'] = 1;
			$this->_vipInfo['growup'] = 0;
			$this->_vipInfo['type'] = $this->_vipCard[$cid][0];
			$this->_vipInfo['settlementtime'] = 0;
			$this->_reloadArena();
		} else {
			if($time > $this->_vipInfo['endtime'] + self::DURATION * 86400){
				$this->_vipInfo['starttime'] = $time;
				$this->_vipInfo['endtime'] = $time + 86400 * $this->_vipCard[$cid][1]*$count;
				$this->_vipInfo['type'] = $this->_vipCard[$cid][0];
				$this->_vipInfo['settlementtime'] = 0;
				$this->_reloadArena();
			} else {
				$changeType = 1;
				if($time > $this->_vipInfo['endtime']){
					$this->_vipInfo['endtime'] = $time + 86400 * $this->_vipCard[$cid][1]*$count;
				}else{
					$this->_vipInfo['endtime'] += 86400 * $this->_vipCard[$cid][1]*$count;
				}
				
				$vaildDay = ceil(($this->_vipInfo['endtime'] - $time)/86400);
				if($vaildDay >= 7){
					$changeType = 2;
				}
				if($vaildDay >= 30){
					$changeType = 3;
				}
				$this->_vipInfo['type'] = $changeType > $this->_vipInfo['type'] ? $changeType : $this->_vipInfo['type'];
			}
		}
		$this->_clearStrcool($uid);
		$this->_reloadSkip();
		$this->_dayGrowUp = $this->_levelArr[$this->_vipInfo['type']];
		$this->_change = true;
		self::checkChange();
	}
	
	public function addGrowUp($uid,$num)
	{
		$this->_initVip($uid);
//		if($this->_vipStatus != true){
//			return;
//		}
		
		$this->_vipInfo['growup'] += $num;
		$this->_getGrowUp();
		$level = $this->_getLevel();
		if($level > $this->_vipInfo['level']){
			$this->_vipInfo['level'] = $level;
			$this->_levelUp = true;
		}
		$this->_change = true;
		self::checkChange();
	}
	//取得vip信息
	public function getVipInfo($uid)
	{
		$this->_initVip($uid);
		$info = $this->_vipInfo;
		if($this->_vipStatus == true){
			$info['vipStatus'] = 1;
		}else{
			$info['vipStatus'] = 0;
		}
		if(!$info){
			return array('level'=>0,'vipStatus'=>0);
		}
//		self::checkChange();
		return $info;
	}
	
	public function getGain($uid,$type)
	{
		$this->_initVip($uid);
		$info = $this->_vipInfo;
		if(!$info || $this->_vipStatus != true){
			return 0;
		}
		$level = $info['level'];
		$vipAddtion = Hapyfish2_Alchemy_Cache_Vip::getVipAddition();
		$addtion = $vipAddtion[$level]['addition'];
		$add = $addtion[$type];
		self::checkChange();
		return $add;
	}
	
	/******************************************************/
	
	/**
	 * 重置VIP每日奖励
	 * @param int $uid
	 */
	public static function resetVipDailyAward($uid)
	{
		$bllVip = new Hapyfish2_Alchemy_Bll_Vip();
		$userVipInfo = $bllVip->getVipInfo($uid);
		
		Hapyfish2_Alchemy_Cache_Vip::setVipDailyAward($uid, 'Y');
		
		return;
	}
	
	/**
	 * 领取VIP每日奖励
	 * @param int $uid
	 */
	public static function getVipDialyAward($uid)
	{
		$bllVip = new Hapyfish2_Alchemy_Bll_Vip();
		$userVipInfo = $bllVip->getVipInfo($uid);
		$userVipLev = $userVipInfo['level'];
		if ( $userVipLev == 0 || $userVipInfo['vipStatus'] == 0) {
			return -267;
		}
		
		$canGetAward = Hapyfish2_Alchemy_Cache_Vip::getVipDailyAward($uid);
		if ( $canGetAward['get'] == 1 ) {
			return -268;
		}
		
		$awardAry = self::_getDailyAwardAry();
		
		$vipAwards = $awardAry[$userVipLev];
		$vipAwards = json_decode($vipAwards);
		
		foreach ( $vipAwards as $award ) {
			$cid = $award[0];
			$count = $award[1];
			$type = $award[2];
			
			//type:1 2 3 代表是金币 宝石 道具
			if ( $type == 1 ) {
				Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $count,20);
			}
			else if ( $type == 2 ) {
				//VIP每日奖励，暂时不会发送宝石
			}
			else if ( $type == 3 ) {
				Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $cid, $count);
			}
		}
		$canGetAward['get'] = 1;
		Hapyfish2_Alchemy_Cache_Vip::setVipDailyAward($uid, $canGetAward);
		
		return 1;
	}
	
	public static function checkVipDailyAward($uid)
	{
		$canGetAward = Hapyfish2_Alchemy_Cache_Vip::getVipDailyAward($uid);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'isGetAward', $canGetAward['get']);
		return 1;
	}
	
	/**
	 * 读取VIP每日礼包奖励列表
	 */
	public static function initVipDailyAward($uid)
	{
		$awardAry = self::_getDailyAwardAry();
		$info = array();
		foreach ( $awardAry as $key => $award ) {
			$info[] = array('VIPLevel' => $key, 'awards' => json_decode($award));
		}
		
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'vipEveryDayVo', $info);
		return 1;
	}
	
	/**
	 * VIP每日奖励内容
	 */
	private static function _getDailyAwardAry()
	{
		//type:1 2 3 代表是金币 宝石 道具
		$awardAry = array('1' => '[[0,10000,1],[4415,2,3],[2715,2,3]]',
						  '2' => '[[0,30000,1],[9515,2,3],[2715,4,3]]',
						  '3' => '[[0,50000,1],[6615,2,3],[2715,6,3]]',
						  '4' => '[[0,70000,1],[9215,2,3],[2715,8,3]]',
						  '5' => '[[0,100000,1],[6815,2,3],[2715,10,3]]');
		return $awardAry;
	}
	
	protected function _reloadSkip()
	{
		$level = $this->_vipInfo['level'];
		$skipArr = self::$_skipFight;
		$max = $skipArr[$level-1];
		$userSkip = Hapyfish2_Alchemy_Cache_Vip::getUserSkip($this->_vipInfo['uid']);
		$userSkip['max'] = $max;
		Hapyfish2_Alchemy_Cache_Vip::updateUserSkip($this->_vipInfo['uid'], $userSkip);
	}
	
	protected function _reloadOrder()
	{
		$orderStatus = Hapyfish2_Alchemy_HFC_Order::getOrderStatus($this->_vipInfo['uid']);
		if($this->_vipInfo['level'] >= 4 && $this->_vipStatus == true){
			$limit= 15;
		}else{
			$limit = 10;
		}
		$orderStatus['limit'] = $limit;
		Hapyfish2_Alchemy_HFC_Order::updateOrderStatus($this->_vipInfo['uid'],$orderStatus);
	}
	
	protected function _reloadArena()
	{
		$userArena = Hapyfish2_Alchemy_Cache_Arena::getUserArena($this->_vipInfo['uid']);
		$level = $this->_vipInfo['level'];
		if($level == 1){
			$change = $this->_vipArena[1];
		}else{
			$change = array($this->_vipArena[$level][0] - $this->_vipArena[$level-1][0], $this->_vipArena[$level][1] - $this->_vipArena[$level-1][1]);
		}
		$userArena['challengeTimes'] += $change[0];
		$userArena['refreshTimes'] += $change[1];
		Hapyfish2_Alchemy_Cache_Arena::updateUserArena($this->_vipInfo['uid'], $userArena);
	}
	
	public function getVipSkip($uid)
	{
		$this->_initVip($uid);
		$info = $this->_vipInfo;
		$userSkip = Hapyfish2_Alchemy_Cache_Vip::getUserSkip($uid);
		if($info && $this->_vipStatus != true && $userSkip['max'] > 30){
			$userSkip['max'] = 30;
			Hapyfish2_Alchemy_Cache_Vip::updateUserSkip($uid, $userSkip);
		}
//		self::checkChange();
		return $userSkip;
	}
	
	public  function getVipStatic()
	{
		$skip = self::$_skipFight;
		$repair = array();
		$addition = Hapyfish2_Alchemy_Cache_Vip::getVipAddition();
		foreach($addition as $level => $add){
			$addstatus = $add['addition'];
			$repair[] = $addstatus['repair_coin'];
		}
		$levelArr = $this->_levelExp;
		array_unshift($levelArr, 0);
		$vipdismissExp = array(25,50,75,100,100);
		return array('vipTable'=>array('skipBattle'=>$skip, 'fixEquip'=>$repair, 'dismissExp'=>$vipdismissExp),'vipGrowUp'=>$levelArr);
	}
	
	public function vipSkip($uid)
	{
		$this->_initVip($uid);
		$skip = self::getVipSkip($uid);
		if($skip['max'] - $skip['num'] <= 0){
			return -200;
		}
		$skip['num'] += 1;
		Hapyfish2_Alchemy_Cache_Vip::updateUserSkip($uid, $skip);
		return 1;
	}
	
	public function getVipArena($uid)
	{
		$this->_initVip($uid);
		if(!$this->_vipInfo ||  $this->_vipStatus != true){
			return array(0,0);
		}
	    	if($uid == 10027){
	    		self::checkChange();
	    	}
		return $this->_vipArena[$this->_vipInfo['level']];
		
	}
	
	public function setAddition($uid, $data)
	{
		foreach ($data as $k => $v) {
			$addNum = 0;
			if ($v['type'] == 1) {
    			$type =	substr($v['id'], -2, 1);
    			if($type == 3){
    				$addNum = $this->getGain($uid, 'fright_material');
    			}
			}
			//玩家属性
			else if ($v['type'] == 2) {
				if ($v['id'] == 'coin') {
				    $addNum = $this->getGain($uid, 'fright_coin');
				}
			}
			
				
			$num = ceil($v['num']*($addNum/100));
			if($addNum > 0){
				$v['num'] = $num;
				$this->_vipAddition[] = $v;
			}
		}	
	}
	
	public function getAddition()
	{
		$data = array();
		if(!empty($this->_vipAddition)){
			foreach($this->_vipAddition as $k => $v){
				if(!isset($data[$v['id']])){
					$data[$v['id']] = $v;
				}else{
					$data[$v['id']]['num'] += $v['num'];
				}
			}
		}
		return array_values($data);
	}
	
	public function getMaxSp($uid)
	{
		$this->_initVip($uid);
		if(!$this->_vipInfo ||  $this->_vipStatus != true){
			return 50;
		}
		
//		self::checkChange();
		return $this->_vipSp[$this->_vipInfo['level']][1];
	}
	
	public function getMaxSpNum($uid)
	{
		$this->_initVip($uid);
		if(!$this->_vipInfo ||  $this->_vipStatus != true){
			return 5;
		}
		
//		self::checkChange();
		return $this->_vipSp[$this->_vipInfo['level']][0];
	}
	public function getMaxOrder($uid)
	{
		$this->_initVip($uid);
//		self::checkChange();
		if($this->_vipInfo['level'] >= 4 && $this->_vipStatus == true){
			return 15;
		}
		return 10;
	}
	
	public function getRefreshTime($uid)
	{
		$this->_initVip($uid);
//		self::checkChange();
		if($this->_vipInfo['level'] >= 4 && $this->_vipStatus == true){
			return 14400;
		}else if($this->_vipInfo['level'] >= 2 && $this->_vipStatus == true){
			return 21600;
		}else{
			return 28800;
		}
	}
	
	public function addEndTime($uid, $day)
	{
		$this->_initVip($uid);
		$now = time();
		if($this->_vipStatus != true){
			$this->_vipInfo['starttime'] = $now;
			$this->_vipInfo['endtime'] = $now+$day*86400;
		}else{
			$this->_vipInfo['endtime'] += $day*86400;
		}
		$this->_change = true;
		self::checkChange();
	}
	
	public function userVipDayCard($uid)
	{
		$time = time();
		$this->_initVip($uid);
		$info = $this->_vipInfo;
		if($info['type'] == 0){
			$this->_vipInfo['uid'] = $uid;
			$this->_vipInfo['starttime'] = $time;
			$this->_vipInfo['endtime'] = $time + 1800;
			$this->_vipInfo['level'] = 1;
			$this->_vipInfo['growup'] = 0;
			$this->_vipInfo['type'] = 1;
			$this->_vipInfo['settlementtime'] = 0;
		} else {
			if($time > $this->_vipInfo['endtime'] + self::DURATION * 86400){
				$this->_vipInfo['starttime'] = $time;
				$this->_vipInfo['endtime'] = $time + 3600 * 1;
				$this->_vipInfo['type'] = 1;
				$this->_vipInfo['settlementtime'] = 0;
				$this->_reloadArena();
			} else {
				$changeType = 1;
				$this->_vipInfo['endtime'] += 86400 * 1;
				$vaildDay = ceil(($this->_vipInfo['endtime'] - $time)/86400);
				if($vaildDay >= 7){
					$changeType = 2;
				}
				if($vaildDay >= 30){
					$changeType = 3;
				}
				$this->_vipInfo['type'] = $changeType > $this->_vipInfo['type'] ? $changeType : $this->_vipInfo['type'];
			}
		}
		$this->_reloadSkip();
		$this->_reloadArena();
		$this->_reloadOrder();
		$this->_clearStrcool($uid);
		$this->_dayGrowUp = $this->_levelArr[$this->_vipInfo['type']];
		$this->_change = true;
		self::checkChange();
	}
	
	public function getNextPay($totalPay)
	{
		$level = $this->_vipInfo['level'];
		if($level == 0){
			return 10;
		}else{
			$num = $this->_levelExp[$level-1]-$this->_vipInfo['growup']>0?$this->_levelExp[$level-1]-$this->_vipInfo['growup']:0;
			return $num;
		}
	}
	
	public function _clearStrcool($uid)
	{
		$userStr = Hapyfish2_Alchemy_HFC_User::getStrCoolTime($uid);
		$userStr['endtime'] = 0;
		$userStr['canStr'] = 1;
		Hapyfish2_Alchemy_HFC_User::updateStrCoolTime($uid,$userStr);
		Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'strengthenCd');
	}
}