<?php

class Hapyfish2_Alchemy_Bll_Mix
{
	/**
	 * 开始合成
	 *
	 * @param int $uid
	 * @param int $cid,合成术cid
	 * @param int $furnaceCid,工作台cid
	 * @param int $num,本次合成数量
	 * @param int $preNum,合成成功几率加成量
	 */
	public static function startMix($uid, $cid,	$furnaceCid, $num, $preNum)
	{
		if ( $num < 1 && !is_int($num)) {
			return -203;
		}
		
		//判断合成成功率
		if ( $preNum > 100 || $preNum <	0  ) {
			return -205;
		}

		//判断合成术是否存在
		$mixInfo = Hapyfish2_Alchemy_Cache_Basic::getMixInfo($cid);
		if ( !$mixInfo ) {
			return -203;
		}
		
        $userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
        if ( $userLevel < $mixInfo['need_level'] ) {
        	return -258;
        }
        
		//判断概率添加量是否正常
		if ( $preNum < $mixInfo['probability'] ) {
			return -205;
		}

		//计算提高成功率需要的道具
		$needCardCount = 0;
		if ( $preNum > $mixInfo['probability'] ) {
			$addPro	= $preNum -	$mixInfo['probability'];
			$addProNum = $addPro/$mixInfo['probability_change_value'];
			$addProNum = $addProNum < 1 ? 1 : $addProNum;
			if ( !is_int($addProNum) ) {
				return -205;
			}
			$needCardCount = $addProNum	* $mixInfo['per_probability_gem'] * $num;
			$gameData = Hapyfish2_Alchemy_Bll_BasicInfo::getGameData();
			$needCardId = $gameData['crystalCid'];
			
			$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
			if ( !isset($userGoods[$needCardId]) || $userGoods[$needCardId]['count'] < $needCardCount ) {
				return -204;
			}
		}

		//判断合成时间是否超出上限
		$needTime =	$num * $mixInfo['time'];
		if ( $needTime > $mixInfo['max_time'] )	{
			return -209;
		}
       
		//判断金币宝石是否足够
		$userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
		$needGem = $mixInfo['gem'] * $num;
		if ( $userGem <	$needGem ) {
			return -206;
		}

		$needCoin =	$mixInfo['coin'] * $num;
		if ( $needCoin > 0 ) {
			$userCoin =	Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
			if ( $userCoin < $mixInfo['coin'] )	{
				return -207;
			}
		}

		//判断用户是否已学习此合成术，已学习合成术列表
		$userMix = Hapyfish2_Alchemy_HFC_Mix::getUserMix($uid);
		if ( !in_array($cid, $userMix) ) {
			return -201;
		}

		//判断用户是否有空闲工作台
		//是否手动指定工作台（由图鉴快捷则没有指定）
		if ( $furnaceCid > 0 ) {
			$userFurnace = Hapyfish2_Alchemy_HFC_Furnace::getOneCanUseByCid($uid, $furnaceCid);
			if ( empty($userFurnace)	) {
				return -202;
			}
			//工作台正在工作
			if ( $userFurnace['cid'] != 0 ) {
				return -202;
			}
		}
		else {
			$userFurnace = Hapyfish2_Alchemy_HFC_Furnace::getOneCanUseByCid($uid, $mixInfo['furnace_cid']);
			if ( empty($userFurnace)	) {
				return -202;
			}
			//工作台正在工作
			if ( $userFurnace['cid'] != 0 ) {
				return -202;
			}
			$furnaceCid = $userFurnace['furnace_id'];
		}

		if ( $furnaceCid == 0 ) {
			return -202;
		}

		//判断材料是否足够
		$needs = json_decode($mixInfo['needs']);
		$needGoods = array();
		$needScroll	= array();
		$needStuff = array();
		$needDecorInBag	= array();
		$needWeapon	= array();
		foreach	( $needs as	$need )	{
			$needCid = $need[0];
			$needCount = $need[1] * $num;
			$needType =	substr($needCid, -2, 1);
			//1->物品,2->卷轴,3->材料,4->工作台,5->装修,6->装备
			if ( $needType == 1	) {
				$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
				if ( !isset($userGoods[$needCid]) || $userGoods[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needGoods[] = array('cid' => $needCid,	'count'	=> $needCount);
			}
			else if	( $needType	== 2 ) {
				$userScroll	= Hapyfish2_Alchemy_HFC_Scroll::getUserScroll($uid);
				if ( !isset($userScroll[$needCid]) || $userScroll[$needCid]['count']	< $needCount ) {
					return -204;
				}
				$needScroll[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
			else if	( $needType	== 3 ) {
				$userStuff = Hapyfish2_Alchemy_HFC_Stuff::getUserStuff($uid);
				if ( !isset($userStuff[$needCid]) || $userStuff[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needStuff[] = array('cid' => $needCid,	'count'	=> $needCount);
			}
			else if	( $needType	== 5 ) {
				$userDecorInBag	= Hapyfish2_Alchemy_HFC_Decor::getBag($uid);
				if ( !isset($userDecorInBag[$needCid]) || $userDecorInBag[$needCid]['count']	< $needCount ) {
					return -204;
				}
				$needDecorInBag[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
			else if	( $needType	== 6 ) {
				$userNewWeapon = Hapyfish2_Alchemy_HFC_Weapon::getNewPerfectWeapon($uid);
				if ( !isset($userNewWeapon[$needCid]) || $userNewWeapon[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needWeapon[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
		}
		
		$vip =new Hapyfish2_Alchemy_Bll_Vip();
		$vipInfo = $vip->getVipInfo($uid);
		if($vipInfo['level'] >=5 && $vipInfo['vipStatus'] == 1){
			$needTime = 0;
		}
		
		//添加合成
		$userFurnace['cid']	= $cid;
		$userFurnace['start_time'] = time();
		$userFurnace['need_time'] = $needTime;
		$userFurnace['cur_probability']	= $preNum;
		$userFurnace['num']	= $num;
		$updateResult =	Hapyfish2_Alchemy_HFC_Furnace::updateOne($uid, $userFurnace['id'], $userFurnace);
		if ( !$updateResult	) {
			return -200;
		}
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'mixId', $userFurnace['id']);

		$removeItems = array();
		//扣除需求物品
		if ( !empty($needGoods)	) {
			foreach	( $needGoods as	$goods ) {
				$userGoods[$goods['cid']]['count'] -= $goods['count'];
				$userGoods[$goods['cid']]['update'] = 1;
				$removeItems[] = array($goods['cid'], $goods['count'], 0);
			}
			Hapyfish2_Alchemy_HFC_Goods::updateUserGoods($uid, $userGoods);
		}
		if ( !empty($needScroll) ) {
			foreach	( $needScroll as $scroll ) {
				$userScroll[$scroll['cid']]['count'] -= $scroll['count'];
				$userScroll[$scroll['cid']]['update'] = 1;
				$removeItems[] = array($scroll['cid'], $scroll['count'], 0);
			}
			Hapyfish2_Alchemy_HFC_Scroll::updateUserScroll($uid, $userScroll);
		}
		if ( !empty($needStuff)	) {
			foreach	( $needStuff as	$stuff ) {
				$userStuff[$stuff['cid']]['count'] -= $stuff['count'];
				$userStuff[$stuff['cid']]['update'] = 1;
				$removeItems[] = array($stuff['cid'], $stuff['count'], 0);
			}
			Hapyfish2_Alchemy_HFC_Stuff::updateUserStuff($uid, $userStuff);
		}
		if ( !empty($needDecorInBag) ) {
			foreach	( $needDecorInBag as $decor	) {
				$userDecorInBag[$decor['cid']]['count'] -= $decor['count'];
				$userDecorInBag[$decor['cid']]['update'] = 1;
				$removeItems[] = array($decor['cid'], $decor['count'], 0);
			}
			Hapyfish2_Alchemy_HFC_Decor::updateBag($uid, $userDecorInBag);
		}
		if ( !empty($needWeapon) ) {
			foreach	( $needWeapon as $weapon ) {
				for	( $i=0;$i<$weapon['count'];$i++	) {
					Hapyfish2_Alchemy_HFC_Weapon::delPerfectWeaponByCid($uid, $weapon['cid']);
				}
			}
		}
		if ( $needCoin > 0 ) {
			//扣除用户金币
			Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $needCoin,10);
		}
		if ( $needGem >	0 )	{
			//扣除用户宝石
			$gemInfo = array(
	        		'uid' => $uid,
	        		'cost' => $needGem,
	        		'summary' => LANG_PLATFORM_BASE_TXT_1,
	        		'user_level' => $userLevel,
	        		'cid' => $mixInfo['item_cid'],
	        		'num' => $num
	        	);
			Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo,8);
		}
		if ( $needCardCount > 0 ) {
			Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $needCardId, $needCardCount);
		}

		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeItems', $removeItems);

		Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'mixCd');
		
		//report log,统计玩家合成术分布
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('201', array($uid, $cid));

		return 1;
	}

	/**
	 * 完成合成术，收获奖励
	 *
	 * @param int $uid
	 * @param int $fid,工作台实例id
	 * @param int $furnaceCid,工作台cid
	 */
	public static function completeMix($uid, $fid, $furnaceCid)
	{
		$completeIds = array();
		if ( $fid > 0 ) {
			//立即完成此合成术
			$ok = self::gainMix($uid, $fid, 0);
			if ($ok == 1) {
				$completeIds[] = $fid;
			}
			else {
				return -200;
			}
		}
		else {
			//收获此工作台所有已完成的合成术
			$data = Hapyfish2_Alchemy_HFC_Furnace::getAll($uid);
			foreach ( $data as $item ) {
				if ( $item[1] == $furnaceCid ) {
					$fid = $item[0];
					$ok = self::gainMix($uid, $fid, 1);
					if ($ok == 1) {
						$completeIds[] = $fid;
					}
				}
			}
			if ( empty($completeIds) ) {
				return -200;
			}
		}

		//返回本次操作 成功收获的实例id
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'mixIds', $completeIds);
		
		Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'mixCd');
		
		return 1;
	}

	/**
	 * 收获合成术奖励
	 * @param int $uid
	 * @param int $fid
	 * @param int $isFinish,合成术是否完成,1->已完成,0->未完成、立刻完成
	 */
	public static function gainMix($uid, $fid, $isFinish = 1)
	{
		//查询对应工作台信息
		$userFurnace = Hapyfish2_Alchemy_HFC_Furnace::getOne($uid, $fid);
		
		if ( !$userFurnace ) {
			return -210;
		}
		if ( $userFurnace['cid'] ==	0 )	{
			return -210;
		}

		$finishTime	= $userFurnace['start_time'] + $userFurnace['need_time'];
		$nowTime = time();
		$remainTime	= $finishTime - $nowTime;
		$needCardCount = 0;
		if ( $remainTime > 0 ) {
			if ( $isFinish == 1	) {
				return -211;
			}

			//半小时一个沙漏,cid = 2715;
			$gameData = Hapyfish2_Alchemy_Bll_BasicInfo::getGameData();
			$shalouTime = $gameData['shalouTime'];
			$needGoodsCid =	$gameData['shalouCid'];
			$needCardCount = ceil( $remainTime/$shalouTime );
			$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
			if ( !isset($userGoods[$needGoodsCid]) || $userGoods[$needGoodsCid]['count'] < $needCardCount )	{
				return -212;
			}
		}

		//查询对应合成术信息
		$mixInfo = Hapyfish2_Alchemy_Cache_Basic::getMixInfo($userFurnace['cid']);
		//查询用户剩余行动力sp
		$userSp = Hapyfish2_Alchemy_HFC_User::getUserSp($uid);
		$needSp = $mixInfo['sp'];
		if ( $userSp['sp'] < $needSp )	{
			return -208;
		}

		$curProbability	= $userFurnace['cur_probability'];
		
		$completeCount = 0;
		$failCount = 0;
		for ($i=1;$i<=$userFurnace['num'];$i++) {
			$rand =	rand(1,	100);
			if ( $rand <= $curProbability )	{
				$completeCount++;
			}
			else {
				$failCount++;
			}
		}

		//开始收获
		//合成成功
		if ( $completeCount > 0 )	{
			$itemCid = $mixInfo['item_cid'];
			$count = $completeCount;
			self::addNewItem($uid, $itemCid, $count);

			//触发任务处理
            $event = array('uid' => $uid, 'data' => array($itemCid=>$count));
    		Hapyfish2_Alchemy_Bll_TaskMonitor::mixGain($event);
            Hapyfish2_Alchemy_Bll_TaskMonitor::exgStuffGain($event);
            
            $exp = round(pow($count, 1) * $mixInfo['exp']);
            Hapyfish2_Alchemy_HFC_User::incUserExp($uid, $exp);		//加经验
		}
		//合成失败
		if ( $failCount > 0 ) {
			$failItem =	json_decode($mixInfo['fail_item']);
			foreach	( $failItem	as $item ) {
				$itemCid = $item[0];
				$count = $item[1] * $failCount;
				self::addNewItem($uid, $item[0], $count);
			}
		}

		if ( $needSp > 0 ) {
			//更新用户行动力
			Hapyfish2_Alchemy_HFC_User::decUserSp($uid, $needSp);
			$resultVo['result']['sp'] = -$needSp;
		}

		//使用沙漏
		if ( $needCardCount	> 0	) {
			Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid,	$needGoodsCid, $needCardCount);
		}
		$mixCid = $userFurnace['cid'];

		//重置工作台合成术信息
		$userFurnace['cid']	= 0;
		$userFurnace['start_time'] = 0;
		$userFurnace['need_time'] = 0;
		$userFurnace['cur_probability']	= 0;
		$userFurnace['num']	= 0;
		Hapyfish2_Alchemy_HFC_Furnace::updateOne($uid, $fid, $userFurnace);

		//触发任务处理
        $event = array('uid' => $uid, 'data' => 1);
		Hapyfish2_Alchemy_Bll_TaskMonitor::completeMix($event);
		
		//合成成功
		if ( $completeCount > 0 )	{
			$itemType =	substr($itemCid, -2, 1);
			//匹配物品信息
			if ( $itemType == 1	) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getGoodsInfo($itemCid);
			}
			else if	( $itemType	== 2 ) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getScrollInfo($itemCid);
			}
			else if	( $itemType	== 3 ) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getStuffInfo($itemCid);
			}
			else if	( $itemType	== 5 ) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getDecorInfo($itemCid);
			}
			else if	( $itemType	== 6 ) {
				$itemInfo =	Hapyfish2_Alchemy_Cache_Basic::getWeaponInfo($itemCid);
			}
			
			//合成术
			$mixInfo = Hapyfish2_Alchemy_Cache_Basic::getMixInfo($mixCid);
			$needCoin =	$mixInfo['coin'] * $count;
			$needGem =	$mixInfo['gem'] * $count;
			
			//report log,统计玩家合成物品信息
			$logger = Hapyfish2_Util_Log::getInstance();
			$logger->report('341', array($uid, $itemCid, $itemInfo['name'], $count, $needCoin, $needGem));
		}

		Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'mixCd');
		
		return 1;
	}
	
	/**
	 * 扩展炼金炉格子数
	 * @param int $uid
	 * @param int $furnaceCid,炼金炉cid
	 * @param int $num,要扩展到的格子数（第N个格子）
	 */
	public static function expandFurnace($uid, $furnaceCid, $num)
	{
		//炼金炉当前格子数
		$curNum = Hapyfish2_Alchemy_HFC_Furnace::getFurnaceCurNum($uid, $furnaceCid);
		if ( $curNum + 1 != $num ) {
			return -200;
		}
		
		//炼金炉信息
		$furnaceBasic = Hapyfish2_Alchemy_Cache_Basic::getFurnaceInfo($furnaceCid);
		
		//扩展格子需要条件
		$field = 'expand_needs_'.$num;
		if ( !isset($furnaceBasic[$field]) ) {
			return -200;
		}

		//检查需消耗物品是否满足
		$needs = json_decode($furnaceBasic[$field], true);
		if ( empty($needs) ) {
			return -200;
		}
		$rstCheckNeeds = Hapyfish2_Alchemy_Bll_Item::checkNeeds($uid, $needs);
		if ( $rstCheckNeeds != 1) {
			return $rstCheckNeeds;
		}

		/**start - expand furnace**/
		try {
			$furnace = array('uid' => $uid,
							'furnace_id' => $furnaceCid,
							'idx' => $num,
							'status' => 1);
			$ok = Hapyfish2_Alchemy_HFC_Furnace::addOne($uid, $furnace);
			if (!$ok) {
				return -200;
			}
	
			//消耗物品
			$rstConsumeNeeds = Hapyfish2_Alchemy_Bll_Item::consumeNeeds($uid, $needs);
			if ( $rstConsumeNeeds != 1 ) {
				return $rstConsumeNeeds;
			}
		}
		catch ( Exception $e ) {
			info_log('expandFurnace:failed'.$e->getMessage(), 'Bll_Mix');
		}

		Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'mixCd');
		
		return 1;		
	}

	/**
	 * 当前所有炼金炉的格子数信息
	 * @param int $uid
	 * @return array
	 */
	public static function getFurnaceNumList($uid)
	{
		$data = Hapyfish2_Alchemy_HFC_Furnace::getAll($uid);
		if (empty($data)) {
			return array();
		}
		
		$info = array();
		foreach ($data as $item) {
			$cid = $item[1];
			if ( isset($info[$cid]) ) {
				$info[$cid]++;
			}
			else {
				$info[$cid] = 1;
			}
		}
		return $info;
	}
	
	/**
	 * 获得物品
	 *
	 * @param int $uid
	 * @param int $itemCid
	 */
	public static function addNewItem($uid,	$itemCid, $count = 1)
	{
		$addResult = false;
		$itemType =	substr($itemCid, -2, 1);
		//itemType,1->物品,2->卷轴,3->材料,4->工作台,5->装修,6->装备
		if ( $itemType == 1	) {
			$addResult = Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, $itemCid, $count);
		}
		else if	( $itemType	== 2 ) {
			$addResult = Hapyfish2_Alchemy_HFC_Scroll::addUserScroll($uid, $itemCid, $count);
		}
		else if	( $itemType	== 3 ) {
			$addResult = Hapyfish2_Alchemy_HFC_Stuff::addUserStuff($uid, $itemCid, $count);
		}
		else if ( $itemType	== 4 ) {
            for ( $n = 0; $n < $count; $n++ ) {
	        	$furnace = array('uid' => $uid,
	        					 'furnace_id' => $itemCid,
	        					 'status' => 0,
	        					 'idx' => 1);
	        	$addResult = Hapyfish2_Alchemy_HFC_Furnace::addOne($uid, $furnace);
            }
		}
		else if	( $itemType	== 5 ) {
			$addResult = Hapyfish2_Alchemy_HFC_Decor::addBag($uid, $itemCid, $count);
		}
		else if	( $itemType	== 6 ) {
			$addResult = Hapyfish2_Alchemy_HFC_Weapon::addUserWeapon($uid, $itemCid, $count);
		}

		return $addResult;
	}


}