<?php

class Hapyfish2_Platform_Bll_QqpayByToken
{
	private static $_refreshScore = array(
			'0' => 30,
			'1' => 60,
			'2' => 180,
			'3' => 500,
			'4' => 40,
			'5' => 80,
			'6' => 225,	
			'7' => 1000
	);
	
   //取得购买token
    public static function getToken($uid,$goodsInfo)
    {
    	//return true;
        try {
            $context = Hapyfish2_Util_Context::getDefaultInstance();
            $openkey = $context->get('session_key');
            $openid = $context->get('puid');
            $pfkey = $context->get('pfkey');
            $rest = OpenApi_QQ_Client::getInstance();
            $rest->setUser($openid,$openkey);
            $platform = PLATFORM;
            $rest->setPlatform($platform);
            $rest->setPfkey($pfkey);
//            $goodsInfo['goodsurl'] = self::checkPic($goodsInfo['goodsurl']);
            $data = $rest->getToken($goodsInfo);
            if($data['ret'] !== 0){
    			info_log('errCode:'.$data['msg'],'getTokenErr');
	    		return null;
    		}
	    	$token = $data['token'];
	    	$logData = array();
	    	$logData['uid'] = $uid;
	    	$logData['token'] = $token;
	    	$logData['payitem'] = $goodsInfo['payitem'];
			$logData['pl'] = PLATFORM;
        	try {
                //register qpoint buy token
                $dal = Hapyfish2_Platform_Dal_QqpayByToken::getDefaultInstance();
                $dal->insertToken($uid, $logData);
            } catch (Exception $e) {
    			info_log('getToken:'.$e->getMessage(), 'Platform_Bll_QqpayByToken');
                return null;
		    }
        }
        catch (Exception $e) {
        	info_log('getTokenErr:'.$e->getMessage(), 'getTokenErr');
            return null;
        }

        return $data;
    }
    
    public static function createItemInfo($cid,$num,$itemInfo=array())
    {
    	$data = array();
    	$itemType =	substr($cid, -2, 1);
    	if(empty($itemInfo)){
	    	if ( $itemType == 1	) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getGoodsInfo($cid);
			}
			else if	( $itemType	== 2 ) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getScrollInfo($cid);
			}
			else if	( $itemType	== 3 ) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getStuffInfo($cid);
			}
			else if	( $itemType	== 5 ) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getDecorInfo($cid);
			}
			else if	( $itemType	== 6 ) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getWeaponInfo($cid);
			}
    	}
    	$Suffix = '.jpg';
    	$className = $itemInfo['class_name'];
    	$prefixArr = explode('.', $className);
    	$prefix = $prefixArr[0].$prefixArr[1];
    	$goodsmeta = $itemInfo['name'].'*'.$itemInfo['content'];
    	$goodsurl = STATIC_HOST.'/alchemy/image/item/'.$prefix.$Suffix;
    	$payitem = $itemInfo['cid'].'*'.$itemInfo['buy_gem'].'*'.$num;
    	$data['goodsmeta'] = $goodsmeta;
    	$data['goodsurl'] = $goodsurl;
    	$data['payitem'] = $payitem;
    	return $data;
    }
    
    //0: 成功 1: 系统繁忙 2: token已过期 3: token不存在
    public static function completeBuy($uid, $params)
    {
    	try {
    	    $dal = Hapyfish2_Platform_Dal_QqpayByToken::getDefaultInstance();
            $row = $dal->getToken($uid, $params['token']);
    	    if (!$row) {
                return 3;
            }
        } catch (Exception $e) {
			info_log('completeBuy:'.$e->getMessage(), 'Platform_Bll_QqpayByToken');
			return 1;
		}
    	
    	try {
            $dalLog = Hapyfish2_Platform_Dal_QqpayByToken::getDefaultInstance();
            $rowLog = $dalLog->getPayDone($uid, $params['billno']);
        } catch (Exception $e) {
			info_log('completeBuy:'.$e->getMessage(), 'Platform_Bll_QqpayByToken');
			return 1;
		}
        //had already done
        if ($rowLog) {
            return 0;
        }
        $old = Hapyfish2_Alchemy_Cache_payUserResult::getPayResult($uid);
        Hapyfish2_Alchemy_Bll_UserResult::setUser($uid);
		$aryItem = explode(';', $params['payitem']);
		$payNeedData = isset($old['payNeedData'])?$old['payNeedData']:array();
		$totalPayNum = 0;
		if($params['amt']){
			$totalPayNum += $params['amt']/10;
		}
		if($params['pubacct_payamt_coins']){
			$totalPayNum += $params['pubacct_payamt_coins'];
		}
		self::completeShipments($uid, $aryItem,$old['payNeedData'],$totalPayNum);
    	$logData = array();
        $logData['billno'] = $params['billno'];
        $logData['payitem'] = $params['payitem'];
        $logData['token_id'] = $params['token'];
        $logData['amt'] = $params['amt'];
        $logData['payamt_coins'] = $params['payamt_coins'];
        $logData['pubacct_payamt_coins'] = $params['pubacct_payamt_coins'];
        $logData['uid'] = $uid;
        $totalPay = Hapyfish2_Alchemy_HFC_User::getTotalPay($uid);
		$totalPay += $totalPayNum;
		Hapyfish2_Alchemy_HFC_User::updateTotalPay($uid, $totalPay);
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		$vip->addGrowUp($uid, $totalPayNum);
//		Hapyfish2_Alchemy_Bll_EventGift::checkGift($uid,$totalPayNum);
    	try {
           $dalLog->insertPayDone($uid, $logData);
        } catch (Exception $e) {
			info_log('completeBuy:'.$e->getMessage(), 'Platform_Bll_QqpayByToken');
			return 1;
		}
		self::proUserResult($uid,$old);
        return 0;
    }
    
    public static function verifySig($method, $script_name, $params, $secret,$sig)
    {
    	require_once 'OpenApi/QQ/V3/lib/SnsSigCheck.php';
    	return SnsSigCheck::verifySig($method, $script_name, $params, $secret,$sig);
    }
    
	public static function proUserResult($uid,$old = array())
	{
		$data = array();
		if(empty($old)){
			$old = Hapyfish2_Alchemy_Cache_payUserResult::getPayResult($uid);
		}
		if(is_array($old)){
			$data = array_merge($data, $old);
		}
		$new = Hapyfish2_Alchemy_Bll_UserResult::flush();
		$data = array_merge($data, $new);
		unset($data['payNeedData']);
		Hapyfish2_Alchemy_Cache_payUserResult::addPayResult($uid, $data);
	}
	
	public static function shipped($uid,$token)
	{
		$try = 3;
		$dal = Hapyfish2_Platform_Dal_QqpayByToken::getDefaultInstance();
		$detail = $dal->getPayDoneInfo($uid,$token);
		if(!$detail){
			return;
		}
		while($try > 0) {
	        try {
	            $context = Hapyfish2_Util_Context::getDefaultInstance();
	            $openkey = $context->get('session_key');
	            $openid = $context->get('puid');
	            $pfkey = $context->get('pfkey');
	            $rest = OpenApi_QQ_Client::getInstance();
	            $rest->setUser($openid,$openkey);
	            $platform = PLATFORM;
	            $rest->setPlatform($platform);
	            $rest->setPfkey($pfkey);
	            $data = $rest->callConfirm($detail);
	            if($data['ret'] == 0){
	    			break;
	    		}else{
	    			info_log(json_encode($data),'callConfim');
	    		}
		    	$try --;
			}catch (Exception $e) {
	        	info_log('getTokenErr:'.$e->getMessage(), 'callConfim');
	        }
		}
		$data = Hapyfish2_Alchemy_Cache_payUserResult::getPayResult($uid);
		return $data;
	}
	
	public static function completeShipments($uid, $aryItem, $payNeedData,$payNum)
	{
		$userFight = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$fightLevel = $userFight['level'];
    	$log = Hapyfish2_Util_Log::getInstance();
		$time = time();
		foreach($aryItem as $k=>$itemList){
         	$itemInfo = explode('*', $itemList);
//         	info_log(json_encode($itemInfo),'callConfim');
			//订单
         	if($itemInfo[0] == 'p001'){
         		$orderStatus = Hapyfish2_Alchemy_HFC_Order::getOrderStatus($uid);
         		Hapyfish2_Alchemy_Bll_Order::getNeworder($uid, $orderStatus);
         		$newOrders = array();
				$userOrder = Hapyfish2_Alchemy_HFC_Order::getOrderList($uid);
				$orderStatus = Hapyfish2_Alchemy_HFC_Order::getOrderStatus($uid);
				if($userOrder){
					foreach($userOrder as $orders){
						$newOrders[] = $orders;
					}
				}
         		$reTime = $orderStatus['lastTime']+1200-$time>0?$orderStatus['lastTime']+1200-$time:0;
				$last = 2-$orderStatus['refreshCount']>0?2-$orderStatus['refreshCount']:0;
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'NewOrders', $newOrders);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'maxDeliverTimes', $orderStatus['limit']);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'deliverTimes', $orderStatus['num']);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'refreshTime', $reTime);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'freeRefreshTimes', $last);
				$log->report('GemLevel', array($uid, $fightLevel, $payNum, 1));
         	}
         	//强化冷却
			else if($itemInfo[0] == 'p002'){
         		self::completeClearCoolTime($uid);
         		$log->report('GemLevel', array($uid, $fightLevel, $payNum, 2));
         	}
         	//佣兵培养
         	else if($itemInfo[0] == 'p003'){
         		$id = $payNeedData['id'];
         		$type = $payNeedData['type'];
	         	if ( $id == 0 ) {
				//主角信息
	        	$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
				}
				else {
					//用户佣兵信息
					$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
				}
				$roleStrthen = array('s_str' => $userMercenary['s_str'],
							 's_dex' => $userMercenary['s_dex'],
							 's_mag' => $userMercenary['s_mag'],
							 's_phy' => $userMercenary['s_phy']);
				$strengthen = Hapyfish2_Alchemy_Bll_Mercenary::getStrengthen($userMercenary['job'], $userMercenary['level'], $type, $roleStrthen);
				$userStrthen = array();
				$userStrthen[$id] = $strengthen;
				$ok = Hapyfish2_Alchemy_HFC_Strengthen::update($uid, $userStrthen);
				$event = array('uid' => $uid, 'data' => 1);
				Hapyfish2_Alchemy_Bll_TaskMonitor::strengthenStart($event);
				$log->report('GemLevel', array($uid, $fightLevel, $payNum, 3));
			//佣兵位刷新	
         	}else if($itemInfo[0] == 'p004'){
         		self::completeRefreshHire($uid,$payNeedData['type']);
         		$log->report('GemLevel', array($uid, $fightLevel, $payNum, 4));
         	}
         	//技能解锁
			else if($itemInfo[0] == 'p005'){
         		self::completeUnlockSkill($uid,$payNeedData['id'],$payNeedData['idx']);
         		$log->report('GemLevel', array($uid, $fightLevel, $payNum, 5));
         	}
         	//扩展佣兵位
			else if($itemInfo[0] == 'p006'){
         		self::completeExpandRoleLimit($uid);
         		$log->report('GemLevel', array($uid, $fightLevel, $payNum, 6));
         	}
         	//重置主角属性
			else if($itemInfo[0] == 'p007'){
         		self::completeResetRoleQuality($uid,$payNeedData['locks']);
         		$log->report('GemLevel', array($uid, $fightLevel, $payNum, 7));
         	}
         	//增加训练位
			else if($itemInfo[0] == 'p008'){
         		self::completeAddPos($uid);
         		$log->report('GemLevel', array($uid, $fightLevel, $payNum, 8));
         	}
         	//消除建筑升级cd
			else if($itemInfo[0] == 'p009'){
         		self::completeRemoveBuildingCd($uid);
         		$log->report('GemLevel', array($uid, $fightLevel, $payNum, 9));
         	}
         	//增加体力
			else if($itemInfo[0] == 'p010'){
         		self::completeAddSp($uid,$payNeedData['type']);
         		$log->report('GemLevel', array($uid, $fightLevel, $payNum, 10));
         	}
         	//训练佣兵
			else if($itemInfo[0] == 'p011'){
         		self::completeStartTraining($uid,$payNeedData['roleId'], $payNeedData['tid'], $payNeedData['gemType']);
         		$log->report('GemLevel', array($uid, $fightLevel, $payNum, 11));
         	}
         	//水牢复活
			else if($itemInfo[0] == 'p012'){
         		self::completeRevivalQqWaterDungeon($uid);
         		$log->report('GemLevel', array($uid, $fightLevel, $payNum, 12));
         	}
         	//深渊复活
			else if($itemInfo[0] == 'p013'){
         		self::completeRevivalQQAbyssDungeon($uid);
         		$log->report('GemLevel', array($uid, $fightLevel, $payNum, 13));
         	}
         	//完成水牢扫荡
			else if($itemInfo[0] == 'p014'){
         		self::completeFinishQQWaterSweep($uid);
         		$log->report('GemLevel', array($uid, $fightLevel, $payNum, 14));
         	}
         	//签到补签
			else if($itemInfo[0] == 'p015'){
         		self::completeSignQQRetroactive($uid,$payNeedData['day']);
         		$log->report('GemLevel', array($uid, $fightLevel, $payNum, 15));
         	}
         	//聚神点金
			else if($itemInfo[0] == 'p016'){
         		self::completeCashQQCowCommand($uid,$payNeedData['type']);
         		$log->report('GemLevel', array($uid, $fightLevel, $payNum, 16));
         	}
         	//每日优惠礼包
			else if($itemInfo[0] == 'p017'){
         		self::completebuySpecialShop($uid,$payNeedData['type']);
         		$log->report('GemLevel', array($uid, $fightLevel, $payNum, 17));
         	}
         	//商店购买
         	else{
         	
         		Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $itemInfo[0],$itemInfo[2]);
         		$event = array('uid' => $uid, 'data' => array($cid=>$num));
				Hapyfish2_Alchemy_Bll_TaskMonitor::exgStuffGain($event);
				$userFight = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
				$fightLevel = $userFight['level'];
				//report log,统计玩家商店购买物品信息
				$logger = Hapyfish2_Util_Log::getInstance();
		        $logger->report('buyLevel', array($uid, $fightLevel, $itemInfo[0], $itemInfo[2]));
		        $log->report('GemLevel', array($uid, $fightLevel, $payNum, 99));
			}
         }
	}
	
	public static function completeRefreshHire($uid, $type)
	{
		if ( !in_array($type, array(0,1,2,3))) {
			return -200;
		}
		
		$needGem  = $hireType = $addScore = 0;

		//用户酒馆佣兵信息
		$userHireData = Hapyfish2_Alchemy_HFC_Hire::getHireData($uid);
		$userHireInfo = $userHireData['data'];
		
		$hireInfo = $userHireInfo[$type];
		
		//是否加速刷新
		switch ( $type ) {
			case 0 :
				//刷新需要宝石数
				$needGem = 10;
				//刷新类型，影响佣兵品质
				$hireType = 4;
				break;
			case 1 :
				$needGem = 20;
				$hireType = 5;
				break;
			case 2 :
				$needGem = 50;
				$hireType = 6;
				break;
			case 3 :
				$needGem = 200;
				$hireType = 7;
				break;
		}
		
		//根据当前酒馆积分 计算当前可刷新的 佣兵星级
		$curScoreRp = Hapyfish2_Alchemy_Cache_BasicExt::getHireScoreRpByScore($userHireData['score']);
		
		//佣兵星级 对应的酒馆刷新信息,根据当前星级和刷新类型判断
		$curRpInfo = Hapyfish2_Alchemy_Cache_BasicExt::getHireRp($curScoreRp, $hireType);
		
		//刷新增加积分数,需要金币数
		$addScore = $curRpInfo['add_score'];

		//VIP积分翻倍功能，4级以上VIP
		$bllVip = new Hapyfish2_Alchemy_Bll_Vip();
		$userVipInfo = $bllVip->getVipInfo($uid);
		if ( $userVipInfo['level'] >= 4 && $userVipInfo['vipStatus'] == 1 ) {
			$addScore += $addScore;
		}
		$firstRefresh = 0;
		//判断 钻石、至尊 首次加速刷新
		if ( $type == 2) {
			if ( $userHireData['first_refresh_2'] != 1 ) {
				$firstRefresh = 2;
				$userHireData['first_refresh_2'] = 1;
			}
		}
		else if ( $type == 3) {
			if ( $userHireData['first_refresh_3'] != 1 ) {
				$firstRefresh = 3;
				$userHireData['first_refresh_3'] = 1;
			}
		}
		
		/**start - refresh hire **/
		$hireVo = array();
		//所有雇佣位信息
		$hireList = Hapyfish2_Alchemy_HFC_Hire::getAll($uid);
		for ( $i=1;$i<4;$i++ ) {
			//获取一个新的雇佣佣兵信息
			$newHire = Hapyfish2_Alchemy_Bll_Mercenary::getNewHire($uid, $i, $hireList[$i], $hireType, $curScoreRp);
			$ok = Hapyfish2_Alchemy_HFC_Hire::updateOne($uid, $i, $newHire);
			if (!$ok) {
				return -200;
			}
			$hireVo[] = Hapyfish2_Alchemy_Bll_Mercenary::getHireVo($newHire);
		}
		
		switch ( $type ) {
			case 0 :
				$needTm = 10*60;
				break;
			case 1 :
				$needTm = 24*60*60;
				break;
			case 2 :
				$needTm = 2*24*60*60;
				break;
			case 3 :
				$needTm = 5*24*60*60;
				break;
		}
		//计算完成时间
		$hireInfo['endTime'] = $nowTm + $needTm;
		$userHireInfo[$type] = $hireInfo;

		//刷新获得对应积分
		$userHireData['score'] += $addScore;
		
		//根据当前酒馆积分 计算当前可刷新的 佣兵星级
		$nextScoreRp = Hapyfish2_Alchemy_Cache_BasicExt::getHireScoreRpByScore($userHireData['score']);
		for ( $i=0;$i<4;$i++ ) {
			//根据佣兵星级和刷新类型 来查询 刷新需要的金币数
			$nextRpInfo = Hapyfish2_Alchemy_Cache_BasicExt::getHireRp($nextScoreRp, $i);
			$userHireInfo[$i]['price'] = $nextRpInfo['need_coin'];
		}
		$userHireData['data'] = $userHireInfo;
		
	    //更新用户酒馆佣兵信息
	    $okUpdate = Hapyfish2_Alchemy_HFC_Hire::updateHireData($uid, $userHireData);
	    if ( !$okUpdate ) {
	    	return -200;
	    }
		$refreshList = array();
		foreach ( $userHireInfo as $k => $hire ) {
			$hire['remainingTime'] = $hire['endTime'] - $nowTm;
			$hire['remainingTime'] = $hire['remainingTime'] < 0 ? 0 : $hire['remainingTime'];
			
			if ( $k != 0 ) {
				$hire['times'] = -1;
			}
			unset($hire['endTime']);
			
			//更具刷新类型不同，刷新档次不同，获得积分数
			$coinHireType = $k;
			$gemHireType = $k + 4;
			$hire['count'] = self::$_refreshScore[$coinHireType];
			$hire['gemCount'] = self::$_refreshScore[$gemHireType];
			$refreshList[] = $hire;
		}
		
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'list', $hireVo);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'refreshList', $refreshList);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'curCount', $userHireData['score']);
		//酒馆积分 对应 佣兵星级的 积分公式
		$hireRpLevel = Hapyfish2_Alchemy_Cache_BasicExt::getHireRpLevel();
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'countLevel', $hireRpLevel);
		Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'hireCd');
		//report log,统计玩家雇佣佣兵信息-刷新佣兵次数
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('302', array($uid));
		
		return 1;
	}
	
	public static function completeUnlockSkill($uid,$id,$idx)
	{
		//用户佣兵信息
		if ( $id == 0 ) {
			//主角信息
        	$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		}
		else {
			//佣兵雇佣信息
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
		}
		
		if (!$userMercenary) {
			return -222;
		}

		$skill = $userMercenary['skill'];
		if (empty($skill)) {
			$skill = array(0,0,0,-1,-1);
		}
		$field = $idx - 1;
		if ( $skill[$field] != -1 ) {
			return -200;
		}
		$skill[$field] = 0;
		$userMercenary['skill'] = $skill;
		//更新技能信息
		if ( $id == 0 ) {
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
		}
		else {
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $id, $userMercenary);
		}
		return 1;
	}
	
	public static function completeExpandRoleLimit($uid)
	{
		$userMaxCount = Hapyfish2_Alchemy_HFC_User::getUserMaxMercenaryCount($uid);

		$maxCount = 30;

		if ( $userMaxCount >= $maxCount ) {
			return -231;
		}
		$newCount = $userMaxCount + 1;
		
		//扩展位置信息
		$positionInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryPosition($newCount);
		$needHomeLevel = $positionInfo['level'];
		
		//用户自宅等级
		$userHomeLevel = Hapyfish2_Alchemy_HFC_User::getUserHomeLevel($uid);
		if ( $userHomeLevel < $needHomeLevel ) {
			return -250;
		}
		$ok = Hapyfish2_Alchemy_HFC_User::updateUserMaxMercenaryCount($uid, $newCount);
		if (!$ok) {
			return -200;
		}
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'max', $newCount);

		return 1;
	}
	
	public static function completeResetRoleQuality($uid,$locks)
	{
		$fieldAry = array('1' => 'hp',
						'2' => 'mp',
						'3' => 'phy_att',
						'4' => 'phy_def',
						'5' => 'mag_att',
						'6' => 'mag_def',
						'7' => 'agility',
						'8' => 'crit',
						'9' => 'dodge',
						'10' => 'hit',
						'11' => 'tou');
		
		foreach ( $locks as $k ) {
			if ( !isset($fieldAry[$k]) ) {
				return -200;
			}
		}
		
		//主角信息
		$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		if ( !$userMercenary ) {
			return -200;
		}
		
		$fieldAry[] = 'str';
		$fieldAry[] = 'dex';
		$fieldAry[] = 'mag';
		$fieldAry[] = 'phy';
		
		//佣兵模型信息
		$modelInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryInfoByJob($userMercenary['job']);
		//成长信息
		$growInfo = Hapyfish2_Alchemy_Cache_Basic::getMercenaryGrowInfoByJob($userMercenary['job']);
		
		//随机新佣兵属性品质，及获得初始属性
		$qualityList = Hapyfish2_Alchemy_Bll_Mercenary::getRandQuality($userMercenary['rp'], $userMercenary['job'], 0, $locks, $userMercenary);
		
		//已锁定的属性，则保持不变
		foreach ( $locks as $key ) {
			$qField = 'q_'.$fieldAry[$key];
			$qualityList[$qField] = $userMercenary[$qField];
		}
		
		//刷新后各属性变化
		$growChange = array();
		foreach ( $fieldAry as $field ) {
			$qField = 'q_'.$field;
			
			//刷新后的数值
			$growChange[$field] = Hapyfish2_Alchemy_Bll_Mercenary::_getProperty($modelInfo[$field], $growInfo[$field], $userMercenary['rp'], $qualityList[$qField], $userMercenary['level']);
			
			//刷新前数值，差值
			$growChange[$field] -= Hapyfish2_Alchemy_Bll_Mercenary::_getProperty($modelInfo[$field], $growInfo[$field], $userMercenary['rp'], $userMercenary[$qField], $userMercenary['level']);
			
			//主角新的各属性资质信息
			$userMercenary[$qField] = $qualityList[$qField];
		}
		
		//主角晋级后新属性信息
		$userMercenary['hp_max'] += $growChange['hp'];
		$userMercenary['hp'] = $userMercenary['hp_max'];
		$userMercenary['mp_max'] += $growChange['mp'];
		$userMercenary['mp'] = $userMercenary['mp_max'];
		$userMercenary['phy_att'] += $growChange['phy_att'];
		$userMercenary['phy_def'] += $growChange['phy_def'];
		$userMercenary['mag_att'] += $growChange['mag_att'];
		$userMercenary['mag_def'] += $growChange['mag_def'];
		$userMercenary['agility'] += $growChange['agility'];
		$userMercenary['crit'] += $growChange['crit'];
		$userMercenary['dodge'] += $growChange['dodge'];
		$userMercenary['hit'] += $growChange['hit'];
		$userMercenary['tou'] += $growChange['tou'];
		$userMercenary['str'] += $growChange['str'];
		$userMercenary['dex'] += $growChange['dex'];
		$userMercenary['mag'] += $growChange['mag'];
		$userMercenary['phy'] += $growChange['phy'];
		$userMercenary['q_role'] = $qualityList['q_role'];

		try {
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
			if (!$ok) {
				return -200;
			}
			$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
		}
		catch ( Exception $e ) {
			info_log('resetRoleQuality:failed'.$e->getMessage(), 'Bll_Mercenary');
    	}
		
		$RoleResetVo = array('hps' => $growChange['hp'],
							 'mps' => $growChange['mp'],
							 'pas' => $growChange['phy_att'],
							 'pds' => $growChange['phy_def'],
							 'mas' => $growChange['mag_att'],
							 'mds' => $growChange['mag_def'],
							 'speeds' => $growChange['agility'],
							 'crits' => $growChange['crit'],
							 'dodges' => $growChange['dodge'],
							 'hitRates' => $growChange['hit'],
							 'luckys' => $growChange['tou'],
							 'ishpMax' => $qualityList['q_hp'] == 5 ? 1 : 0,
							 'ismpMax' => $qualityList['q_mp'] == 5 ? 1 : 0,
							 'ispaMax' => $qualityList['q_phy_att'] == 5 ? 1 : 0,
							 'ispdMax' => $qualityList['q_phy_def'] == 5 ? 1 : 0,
							 'ismaMax' => $qualityList['q_mag_att'] == 5 ? 1 : 0,
							 'ismdMax' => $qualityList['q_mag_def'] == 5 ? 1 : 0,
							 'isspeedMax' => $qualityList['q_agility'] == 5 ? 1 : 0,
							 'iscritMax' => $qualityList['q_crit'] == 5 ? 1 : 0,
							 'isdodgeMax' => $qualityList['q_dodge'] == 5 ? 1 : 0,
							 'ishitRateMax' => $qualityList['q_hit'] == 5 ? 1 : 0,
							 'isluckyMax' => $qualityList['q_tou'] == 5 ? 1 : 0);

		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'roleResetVo', $RoleResetVo);

		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
		
		return 1;
	}
	
	public static function completeClearCoolTime($uid)
	{
		$coolStatus = Hapyfish2_Alchemy_Bll_Weapons::getStrCoolTime($uid);
		if($coolStatus['coolTime'] <= 0){
			return -200;
		}
		$userStr = Hapyfish2_Alchemy_HFC_User::getStrCoolTime($uid);
		$userStr['endtime'] = 0;
		$userStr['canStr'] = 1;
		Hapyfish2_Alchemy_HFC_User::updateStrCoolTime($uid,$userStr);
		Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'strengthenCd');
		return 1;
	}
	
	public static function completeAddPos($uid)
	{
		$userTraPosNum = Hapyfish2_Alchemy_HFC_User::getUserTrainingPosNum($uid);
    	$userTraPosNum ++;
    	try {
	    	$ok = Hapyfish2_Alchemy_HFC_User::updateUserTrainingPosNum($uid, $userTraPosNum, true);
	    	if ( !$ok ) {
	    		info_log('addPos:failed:'.$uid.'-type:'.$type.'-num:'.$userTraPosNum, 'Bll_Training');
	    		return -200;
	    	}
    	}
    	catch ( Exception $e ) {
	        info_log('addPos:failed'.$e->getMessage(), 'Bll_Training');
    	}
    	Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'trainingCd');
    	return 1;
	}
	
	public static function completeRemoveBuildingCd($uid)
	{
		$nowTm = time();
		//用户最后一次建筑升级时间
		$buildingCdTm = Hapyfish2_Alchemy_Cache_User::getBuildingCdTm($uid);
		$remainTm = $buildingCdTm - $nowTm;
		
		if ( $remainTm < 1 ) {
			return -200;
		}
		Hapyfish2_Alchemy_Cache_User::updateBuildingCdTm($uid, 0);

		Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'buildingCd');
		
		return 1;
	}
	
	public static function completeAddSp($uid,$type)
	{
		$userSpAdd = Hapyfish2_Alchemy_Bll_VipWelfare::getSpArr($uid);
		$userSp = Hapyfish2_Alchemy_HFC_User::getUserSp($uid);
		if($userSp['sp'] >= 50)
		{
			return -236;
		}
		$addSp = 0;
		if($type == 1){
			$addSp = 1;
		}
		if($type == 2 ){
			$addSp = 5;
			}
		if($type == 3 ){
			$addSp = 10;	
		}
		$ok = Hapyfish2_Alchemy_HFC_User::incUserSp($uid, $addSp);
		$userSp = Hapyfish2_Alchemy_Cache_User::getUserSpAdd($uid);
		$userSp['list'][$type-1] += 1;
		Hapyfish2_Alchemy_Cache_User::updateUserSpAdd($uid,$userSp);
		return 1;
	}
	
	public static function completeStartTraining($uid, $roleId, $tid, $gemType)
    {
    	//用户训练营等级
    	$userTraLev = Hapyfish2_Alchemy_HFC_User::getUserTrainingLevel($uid);
    	
    	//训练项目信息
    	$basicTra = Hapyfish2_Alchemy_Cache_BasicExt::getTrainingInfo($tid);

    	//判断该训练项目是否已解锁
    	if ( $userTraLev < $basicTra['need_training_level'] ) {
    		return -270;
    	}
    	
    	//所有训练信息列表
    	$userTra = Hapyfish2_Alchemy_HFC_Training::getAll($uid);
    	$traList = $userTra['list'];
    	$curTraNum = $userTra['curTraNum'];
    	
		//用户当前训练位置数
    	$userTraPosNum = Hapyfish2_Alchemy_HFC_User::getUserTrainingPosNum($uid);
    	if ( $userTraPosNum <= $curTraNum ) {
    		return -271;
    	}
    	
    	//判断训练类型
    	if ( $gemType == 1 ) {
    		$basicTra['needs'] = $basicTra['needs_s'];
    		$basicTra['add_num'] = $basicTra['add_num_s'];
    	}
    	
    	//检查需消耗物品是否满足
    	$needs = json_decode($basicTra['needs'], true);
    	$rstCheckNeeds = Hapyfish2_Alchemy_Bll_Item::checkNeeds($uid, $needs);
    	if ( $rstCheckNeeds != 1) {
    		return $rstCheckNeeds;
    	}
    	
    	if ( !empty($traList) && isset($traList[$roleId]) ) {
    		$isNewRole = false;
    		$roleTra = $traList[$roleId];
    		if ( $roleTra['id'] != 0 ) {
    			return -272;
    		}
    	}
    	else {
    		$isNewRole = true;
    		$roleTra = array('uid' => $uid,
							'mid' => $roleId,
							'hp_lev' => 0,
							'mp_lev' => 0,
							'pa_lev' => 0,
							'pd_lev' => 0,
							'ma_lev' => 0,
							'md_lev' => 0,
							'agility_lev' => 0,
							'crit_lev' => 0,
							'dodge_lev' => 0,
							'hit_lev' => 0,
							'tou_lev' => 0,
							'complete_time' => 0,
							'has_get' => 0,
							'type' => 0,
							'id' => 0,
    						'gem_type' => 0);
    	}
    	
    	//根据训练项目类型，区分内容
    	$fieldInfo = Hapyfish2_Alchemy_Bll_Training::_getFieldByType($basicTra['type']);
    	$fieldLev = $fieldInfo['fieldLev'];
    	
    	if ( ($roleTra[$fieldLev] + 1) != $basicTra['level'] ) {
    		return -273;
    	}
    	
    	//佣兵信息
    	if ( $roleId == 0 ) {
        	$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
    	}
    	else {
    		$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $roleId);
    	}
    	//匹配佣兵职业
    	if ( $userMercenary['job'] != $basicTra['job'] ) {
    		return -284;
    	}

    	$needTime = $basicTra['need_time'];
    	$vip = new Hapyfish2_Alchemy_Bll_Vip();
    	$vipInfo = $vip->getVipInfo($uid);
    	if($vipInfo['level'] >= 3 && $vipInfo['vipStatus'] == 1){
    		$needTime = 0;
    	}
    	/**start - start training**/
    	$completeTm = time() + $needTime;
    	$roleTra['complete_time'] = $completeTm;
    	$roleTra['id'] = $basicTra['id'];
    	$roleTra['type'] = $basicTra['type'];
    	$roleTra['gem_type'] = $gemType;
    	
		try {
	    	//消耗物品
	    	$rstConsumeNeeds = Hapyfish2_Alchemy_Bll_Item::consumeNeeds($uid, $needs);
	    	if ( $rstConsumeNeeds != 1 ) {
	    		return $rstConsumeNeeds;
	    	}
	    	
	    	//新佣兵与否
	    	if ( $isNewRole ) {
	    		$ok = Hapyfish2_Alchemy_HFC_Training::addOne($uid, $roleTra);
	    	}
	    	else {
	    		$ok = Hapyfish2_Alchemy_HFC_Training::updateOne($uid, $roleId, $roleTra);
	    	}
	    	
	    	if ( !$ok ) {
	    		return -200;
	    	}
    	}
    	catch ( Exception $e ) {
	        info_log('startTraining:failed'.$e->getMessage(), 'Bll_Training');
    	}

    	$TrainingCampInitVo = Hapyfish2_Alchemy_Bll_Training::_getTraingVo($uid);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'trainingCampInitVo', $TrainingCampInitVo);
    	
    	Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'trainingCd');
    	
    	return 1;
    }
    
    public static function completeRevivalQqWaterDungeon($uid)
    {
    	$userRefresh = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterRefiesh($uid);
       	Hapyfish2_Alchemy_Cache_Helltower::updateUserWaterStatus($uid, $userRefresh['data']);
    	$posMatrix = Hapyfish2_Alchemy_Cache_FightCorps::getFightCorpsInfo($uid);
    	foreach($posMatrix as $k => $v){
    		if($v == 0){
    			$selfInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
    			$selfInfo['hp'] = $selfInfo['hp_max'];
    			$selfInfo['mp'] = $selfInfo['mp_max'];
    			Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $selfInfo);
    		}else{
    			$mercInfo = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $v);
    			$mercInfo['hp'] = $mercInfo['hp_max'];
    			$mercInfo['mp'] = $mercInfo['mp_max'];
    			Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $v, $mercInfo);
    		}
    	}
    	$userRefresh['status'] = 1;
    	Hapyfish2_Alchemy_Cache_Helltower::updateUserWaterRefiesh($uid, $userRefresh);
    	$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
    	return 1;
    }
    
    public static function completeRevivalQQAbyssDungeon($uid)
    {
    	$userRefresh = Hapyfish2_Alchemy_Cache_Helltower::getUserAbyssRefiesh($uid);
       	Hapyfish2_Alchemy_Cache_Helltower::updateUserAbyssStatus($uid, $userRefresh['data']);
    	$posMatrix = Hapyfish2_Alchemy_Cache_FightCorps::getFightCorpsInfo($uid);
    	foreach($posMatrix as $k => $v){
    		if($v == 0){
    			$selfInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
    			$selfInfo['hp'] = $selfInfo['hp_max'];
    			$selfInfo['mp'] = $selfInfo['mp_max'];
    			Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $selfInfo);
    		}else{
    			$mercInfo = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $v);
    			$mercInfo['hp'] = $mercInfo['hp_max'];
    			$mercInfo['mp'] = $mercInfo['mp_max'];
    			Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $v, $mercInfo);
    		}
    	}
    	$userRefresh['status'] = 1;
    	Hapyfish2_Alchemy_Cache_Helltower::updateUserAbyssRefiesh($uid, $userRefresh);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'abyssDungeonOpen', 1);
    	$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
    	return 1;
    }
    
    public static function completeFinishQQWaterSweep($uid)
    {
    	$userwater = Hapyfish2_Alchemy_Cache_Helltower::getUserWaterStatus($uid);
		for($i = $userwater['current'];$i <= $userwater['max'];$i++){
			$curInfo = Hapyfish2_Alchemy_Cache_Basic::getHelltowerInfo(1, $i);
			$awards = $curInfo['awards'];
			$awardItem = Hapyfish2_Alchemy_Bll_VipWelfare::getFightVipAward($uid, $awards);
    		$changeResult = array();
            Hapyfish2_Alchemy_Bll_Helltower::awardCondition($uid, $awardItem);
			foreach($awardItem as $k=>$v){
            	if($v['type'] == 2){
            		if ($v['id'] == 'coin') {
						$userwater['totalcoin'] += $v['num'];
            		}
            		if ($v['id'] == 'battleExp') {
            			$userwater['totalexp'] += $v['num'];
            		}
            	}
            }
		}
		
    	$posMatrix = Hapyfish2_Alchemy_Cache_FightCorps::getFightCorpsInfo($uid);
		foreach($posMatrix as $pos=>$id){
			if($v == 0){
	    		$userMerc = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
	    	}else{
	    		$userMerc = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
	    	}
			$levelUp = Hapyfish2_Alchemy_Bll_Mercenary::checkMercenaryLevelUp($uid, $id, $userMerc);
		}
		$userwater['current'] = $userwater['max'] + 1;
		Hapyfish2_Alchemy_Cache_Helltower::updateUserWaterStatus($uid, $userwater);
    	$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
    	return 1;
    }
		
    public static function completeSignQQRetroactive($uid, $day)
    {
        $userEverySign = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryInfo($uid);
        if ($userEverySign['update_time'] > 0) {
            $time = $userEverySign['update_time'];
        } else {
            $time = mktime(0, 0, 0);
        }
        $userEverySign = Hapyfish2_Alchemy_HFC_EverySign::getUserEveryInfo($uid);
        if ($userEverySign) {
            $array_sign = $userEverySign['every_day'];
            foreach ($array_sign as $k => $v) {
                if ($k == $day) {
                    $tempSignArray[$k] = 1;
                } else {
                    $tempSignArray[$k] = $v;
                }
            }
            $info['every_day'] = $tempSignArray;
            $info['award'] = $userEverySign['award'];
            $info['sign_time'] = $time;
            $info['award_five_sign'] = $userEverySign['award_five_sign'];
            $info['award_seven_sign'] = $userEverySign['award_seven_sign'];
            $info['update_time'] = $userEverySign['update_time'];
            Hapyfish2_Alchemy_HFC_EverySign::updateUserEvery($uid, $info);
            $log = Hapyfish2_Util_Log::getInstance();
            $log->report('signRetroactive', array($uid, 1));
            return 1;
        }
    }
    
    public static function completeCashQQCowCommand($uid, $type)
    {
     	$selfInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
        $level = $selfInfo['level'];
        // 获取 主角 战斗等级 的   相关 金币 和 神勇点 兑换 
        $diamondExchange = Hapyfish2_Alchemy_Cache_DiamondExchange::getDiamodExchange($level);
        // 获取 主角 兑换 金币 和 神勇点的次数
        $userDiamondExchange = Hapyfish2_Alchemy_Cache_DiamondExchange::getUserDiamodExchange($uid);
        if ($type == 1) {
            $exchangeData = $diamondExchange[0]['gold_change'];
            $diamondCost = $diamondExchange[0]['gold_diamondcost'];
            $count = $userDiamondExchange['goldchanger'] + 1;
        } else {
            $exchangeData = $diamondExchange[0]['brova_change'];
            $diamondCost = $diamondExchange[0]['brova_diamondcost'];
            $count = $userDiamondExchange['bravechanger'] + 1;
        }
        $awardData = $exchangeData[$count - 1];
        if ($count <= 10) {
            $diamondCost = $diamondCost[$count - 1];
            //按照类型 增加金币 和 神勇点  扣减宝石
            if ($type == 1) {
                Hapyfish2_Alchemy_HFC_User::incUserCoin($uid,$awardData);
                $userDiamondExchange['goldchanger'] += 1;
            } else {
                Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, 3315, $awardData);
                $userDiamondExchange['bravechanger'] += 1;
            }
            $userDiamondExchange['change_data'] = date('Ymd', mktime(0, 0, 0));
            Hapyfish2_Alchemy_Cache_DiamondExchange::updateUserDiamodExchange($uid, $userDiamondExchange);
            $log = Hapyfish2_Util_Log::getInstance();
            $log->report('DiamondExchange', array($uid, $type));
            $log->report('DiamondExchangeCost', array($uid, $type, $diamondCost));
        } else {

            //return -1200;
        }
    }
    
	public static function completebuySpecialShop($uid,$type)
	{
		$userFight = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$fightLevel = $userFight['level'];
		$userSpecialShop = Hapyfish2_Alchemy_Cache_SpecialShop::getUserSpecialShop($uid);
		$shopDetail = Hapyfish2_Alchemy_Cache_SpecialShop::getSpecialShopDetail($type);
		$total = Hapyfish2_Alchemy_Cache_SpecialShop::getTotalSpecialShop();
		$detail = Hapyfish2_Alchemy_Cache_SpecialShop::getDetail($type, $fightLevel);
		$itemShopTotal = $shopDetail['total'] - $total['list'][$type-1] - $total['auto'][$type-1];
		$itemShopTotal = $itemShopTotal>0?$itemShopTotal:0;
		$totalHave = $total['list'][$type-1] + $total['auto'][$type-1];
		$totalHave = $totalHave>$shopDetail['total']?$shopDetail['total']:$totalHave;
		
		Hapyfish2_Alchemy_Bll_MapCopy::awardCondition($uid, $detail['awards']);
		$userFight = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$fightLevelNew = $userFight['level'];
    	if($fightLevelNew > $fightLevel){
    		$details = Hapyfish2_Alchemy_Cache_SpecialShop::getDetail($type, $fightLevelNew);
    		$awardsNew = $details['awards'];
    	}else{
    		$awardsNew = $detail['awards'];
    	}
    	$userSpecialShop['list'][$type-1] += 1;
    	$total['list'][$type-1] += 1;
    	Hapyfish2_Alchemy_Cache_SpecialShop::updateUserSpecialShop($uid, $userSpecialShop);
    	Hapyfish2_Alchemy_Cache_SpecialShop::updateTotalSpecialShop($total);
    	$priceArr = array($detail['price'],$detail['newPrice'],0,0);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'cur', $itemShopTotal);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'total', $shopDetail['total']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'has', $has-1);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'price', $priceArr);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'items', $awardsNew);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'giftAward', $detail['awards']);
    	return 1;
		
	}
	
	public static function checkPic($url)
	{
		$ch = curl_init();
		$timeout = 3;
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		 
		$contents = curl_exec($ch);
		if($contents){
			curl_close($ch);
		}
		if (preg_match("/200\s+OK/", $contents)){
			return $url;
		}else{
			return STATIC_HOST.'/alchemy/image/item/qpoint.jpg';
		}
	}
}