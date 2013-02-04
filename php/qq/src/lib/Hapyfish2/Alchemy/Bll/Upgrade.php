<?php

class Hapyfish2_Alchemy_Bll_Upgrade
{
	/**
	 * 特殊建筑升级（酒馆，自宅，铁匠铺）
	 * @param int $uid
	 * @param int $id
	 */
	public static function specialUpgrade($uid, $id)
	{
		$result = -1;
		
		$nowTm = time();
		//用户最后一次建筑升级时间
		$buildingCdTm = Hapyfish2_Alchemy_Cache_User::getBuildingCdTm($uid);
		if ( $nowTm < $buildingCdTm ) {
			return -350;
		}
		
		if ( $id == HOME_ID ) {
			$result = self::homeUpgrade($uid);
		}
		else if ( $id == TAVERN_ID || $id == TAVERN_CITY_ID ) {
			$result = self::tavernUpgrade($uid, $id);
		}
		else if ( $id == SMITHY_ID ) {
			$result = self::smithyUpgrade($uid);
		}
		else if ( $id == ARENA_ID ) {
			$result = self::arenaUpgrade($uid);
		}
		else if ( $id == TRAINING_ID ) {
			$result = self::trainingUpgrade($uid);
		}
		
		if ( $result > -1 ) {
			//VIP用户没有冷却时间
			$bllVip = new Hapyfish2_Alchemy_Bll_Vip();
			$userVipInfo = $bllVip->getVipInfo($uid);
			if ( $userVipInfo['level'] < 1 || $userVipInfo['vipStatus'] == 0) {
				$cdTm = $nowTm + $result;
				Hapyfish2_Alchemy_Cache_User::updateBuildingCdTm($uid, $cdTm);
				
				//建筑升级时间-结束时间点
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'spBuildUpgradeCd', $cdTm);
			}

			
			Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'buildingCd');
		}

		return $result;
	}
	
	/**
	 * 快速结束建筑升级CD时间
	 * @param int $uid
	 */
	public static function removeBuildingCd($uid)
	{
		$nowTm = time();
		//用户最后一次建筑升级时间
		$buildingCdTm = Hapyfish2_Alchemy_Cache_User::getBuildingCdTm($uid);
		
		$remainTm = $buildingCdTm - $nowTm;
		
		if ( $remainTm < 1 ) {
			return -200;
		}
		
		//需要宝石
		$needCount = ceil( $remainTm/1800 );
		$needGem = $needCount * 2;
		
		$userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
		if ($userGem < $needGem) {
			return -206;
		}
		$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
		$gemInfo = array(
				'uid' => $uid,
				'cost' => $needGem,
				'summary' => LANG_PLATFORM_BASE_TXT_11,
				'user_level' => $userLevel,
				'cid' => 1,
				'num' => 1
		);
		Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo);
		
		Hapyfish2_Alchemy_Cache_User::updateBuildingCdTm($uid, 0);

		Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'buildingCd');
		
		return 1;
	}
	

	/**
	 *自宅升级
	 *@param int $uid
	 */
	public static function homeUpgrade($uid)
	{
		//用户当前自宅等级
		$userHomeLevel = Hapyfish2_Alchemy_HFC_User::getUserHomeLevel($uid);
		$nextLevel = $userHomeLevel + 1;

		//自宅下一等级信息
		$levelInfo = Hapyfish2_Alchemy_Cache_Basic::getHomeLevel($nextLevel);
		if ( !$levelInfo ) {
			return -200;
		}

		//用户当前经营等级
		$userHomeLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
		if ( $userHomeLevel < $levelInfo['need_level'] ) {
			return -243;
		}

		$userCoin = Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
		if ( $userCoin < $levelInfo['need_coin'] ) {
			return -207;
		}

		//判断材料是否足够
		$needs = json_decode($levelInfo['need_items']);
		$needGoods = array();
		$needScroll	= array();
		$needStuff = array();
		$needDecorInBag	= array();
		$needWeapon	= array();
		foreach	( $needs as	$need )	{
			$needCid = $need[0];
			$needCount = $need[1];
			$needType =	substr($needCid, -2, 1);
			//1->物品,2->卷轴,3->材料,4->工作台,5->装修,6->装备
			if ( $needType == 1	) {
				$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
				if ( $userGoods[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needGoods[] = array('cid' => $needCid,	'count'	=> $needCount);
			}
			else if	( $needType	== 2 ) {
				$userScroll	= Hapyfish2_Alchemy_HFC_Scroll::getUserScroll($uid);
				if ( $userScroll[$needCid]['count']	< $needCount ) {
					return -204;
				}
				$needScroll[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
			else if	( $needType	== 3 ) {
				$userStuff = Hapyfish2_Alchemy_HFC_Stuff::getUserStuff($uid);
				if ( $userStuff[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needStuff[] = array('cid' => $needCid,	'count'	=> $needCount);
			}
			else if	( $needType	== 5 ) {
				$userDecorInBag	= Hapyfish2_Alchemy_HFC_Decor::getBag($uid);
				if ( $userDecorInBag[$needCid]['count']	< $needCount ) {
					return -204;
				}
				$needDecorInBag[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
			else if	( $needType	== 6 ) {
				$userNewWeapon = Hapyfish2_Alchemy_HFC_Weapon::getNewWeapon($uid);
				if ( $userNewWeapon[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needWeapon[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
		}

		//完成升级
		$ok = Hapyfish2_Alchemy_HFC_User::updateUserHomeLevel($uid, $nextLevel);
		if (!$ok) {
			return -200;
		}

		//更新最大订单数,房间大小
		if ( $levelInfo['order_count'] > 0 ) {
			Hapyfish2_Alchemy_HFC_User::updateUserMaxOrderCount($uid, $levelInfo['order_count']);
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'maxOrder', $levelInfo['order_count']);
		}
		
		if ( $levelInfo['tile_x_length'] > 0 && $levelInfo['tile_z_length'] > 0 ) {
			$userScene = Hapyfish2_Alchemy_HFC_User::getUserScene($uid);
			$userScene['tile_x_length'] = $levelInfo['tile_x_length'];
			$userScene['tile_z_length'] = $levelInfo['tile_z_length'];
			Hapyfish2_Alchemy_HFC_User::updateUserScene($uid, $userScene);
		}

		//升级自动处理
		$villageBuildUpgrade = array('id' => HOME_ID, 'level' => $nextLevel);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'villageBuildUpgrade', $villageBuildUpgrade);

		//扣除金币
		Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $levelInfo['need_coin']);

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
					Hapyfish2_Alchemy_HFC_Weapon::delWeaponByCid($uid, $weapon['cid']);
				}
			}
		}

		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeItems', $removeItems);

		//触发任务处理
        $event = array('uid' => $uid, 'data' => $nextLevel);
        Hapyfish2_Alchemy_Bll_TaskMonitor::enlargeHouse($event);
				
		//platform feed
		Hapyfish2_Alchemy_Bll_PlatformFeed_Feed::buildingLevelup($uid, 1);

		//report log,统计玩家自宅升级信息
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('351', array($uid, $nextLevel, $levelInfo['need_coin'], $levelInfo['need_prestige']));
		
		return $levelInfo['cd_time'];
	}

	/**
	 * 酒馆升级
	 * @param int $uid
	 */
	public static function tavernUpgrade($uid, $id)
	{
		//用户当前酒馆等级
		$userTavernLevelList = Hapyfish2_Alchemy_HFC_User::getUserTavernLevelList($uid);
		if ( $id == TAVERN_ID ) {
			$field = 'tavern_level';
		}
		else if ( $id == TAVERN_CITY_ID ) {
			$field = 'tavern_city_level';
		}
		$userTavernLevel = $userTavernLevelList[$field];
		
		if ( $userTavernLevel >= 10 ) {
			return -200;
		}

		$nextLevel = $userTavernLevel + 1;
		
		//升级酒馆，多个酒馆同时升级
		$userTavernLevelList['tavern_level'] = $nextLevel;
		$userTavernLevelList['tavern_city_level'] = $nextLevel;

		//酒馆下一等级信息
		$levelInfo = Hapyfish2_Alchemy_Cache_Basic::getTavernLevel($nextLevel, $id);
		if ( !$levelInfo ) {
			return -200;
		}
		//用户当前经营等级
		$userHomeLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
		if ( $userHomeLevel < $levelInfo['need_level'] ) {
			return -243;
		}

		$userCoin = Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
		if ( $userCoin < $levelInfo['need_coin'] ) {
			return -207;
		}

		//$userPrestige = Hapyfish2_Alchemy_HFC_User::getUserPrestige($uid);
		$userPrestige = 1000000;
		if ( $userPrestige < $levelInfo['need_prestige'] ) {
			return -244;
		}

		//判断材料是否足够
		$needs = json_decode($levelInfo['need_items']);
		$needGoods = array();
		$needScroll	= array();
		$needStuff = array();
		$needDecorInBag	= array();
		$needWeapon	= array();
		foreach	( $needs as	$need )	{
			$needCid = $need[0];
			$needCount = $need[1];
			$needType =	substr($needCid, -2, 1);
			//1->物品,2->卷轴,3->材料,4->工作台,5->装修,6->装备
			if ( $needType == 1	) {
				$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
				if ( $userGoods[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needGoods[] = array('cid' => $needCid,	'count'	=> $needCount);
			}
			else if	( $needType	== 2 ) {
				$userScroll	= Hapyfish2_Alchemy_HFC_Scroll::getUserScroll($uid);
				if ( $userScroll[$needCid]['count']	< $needCount ) {
					return -204;
				}
				$needScroll[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
			else if	( $needType	== 3 ) {
				$userStuff = Hapyfish2_Alchemy_HFC_Stuff::getUserStuff($uid);
				if ( $userStuff[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needStuff[] = array('cid' => $needCid,	'count'	=> $needCount);
			}
			else if	( $needType	== 5 ) {
				$userDecorInBag	= Hapyfish2_Alchemy_HFC_Decor::getBag($uid);
				if ( $userDecorInBag[$needCid]['count']	< $needCount ) {
					return -204;
				}
				$needDecorInBag[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
			else if	( $needType	== 6 ) {
				$userNewWeapon = Hapyfish2_Alchemy_HFC_Weapon::getNewWeapon($uid);
				if ( $userNewWeapon[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needWeapon[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
		}
		
		//完成升级
		$ok = Hapyfish2_Alchemy_HFC_User::updateUserTavernLevel($uid, $userTavernLevelList);
		if (!$ok) {
			return -200;
		}

		//升级自动处理
		$villageBuildUpgrade = array('id' => $id, 'level' => $nextLevel);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'villageBuildUpgrade', $villageBuildUpgrade);

		//更新雇佣信息
		$hireList = Hapyfish2_Alchemy_HFC_Hire::getAll($uid);
		foreach ( $hireList as $hire ) {
			$hire['level'] = $nextLevel;
			Hapyfish2_Alchemy_HFC_Hire::updateOne($uid, $hire['id'], $hire);
		}

		//扣除金币，声望
		Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $levelInfo['need_coin']);

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
					Hapyfish2_Alchemy_HFC_Weapon::delWeaponByCid($uid, $weapon['cid']);
				}
			}
		}

		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeItems', $removeItems);

		//report log,统计玩家酒馆升级信息-村庄酒馆
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('353', array($uid, $nextLevel, $levelInfo['need_coin'], $levelInfo['need_prestige']));
		
		//platform feed
		if ($id == TAVERN_ID) {
			Hapyfish2_Alchemy_Bll_PlatformFeed_Feed::buildingLevelup($uid, 3);
		} else {
			Hapyfish2_Alchemy_Bll_PlatformFeed_Feed::buildingLevelup($uid, 4);
		}
		
		return $levelInfo['cd_time'];
	}

	/**
	 * 铁匠铺升级
	 * @param int $uid
	 */
	public static function smithyUpgrade($uid)
	{
		//用户当前铁匠铺等级
		$userSmithyLevel = Hapyfish2_Alchemy_HFC_User::getUserSmithyLevel($uid);
		if ( $userSmithyLevel >= 50 ) {
			return -200;
		}

		$nextLevel = $userSmithyLevel + 1;

		//铁匠铺下一等级信息
		$levelInfo = Hapyfish2_Alchemy_Cache_Basic::getSmithyLevel($nextLevel);
		if ( !$levelInfo ) {
			return -200;
		}
		//用户当前经营等级
		$userHomeLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
		if ( $userHomeLevel < $levelInfo['need_level'] ) {
			return -243;
		}

		$userCoin = Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
		if ( $userCoin < $levelInfo['need_coin'] ) {
			return -207;
		}

		//$userPrestige = Hapyfish2_Alchemy_HFC_User::getUserPrestige($uid);
		$userPrestige = 1000000;
		if ( $userPrestige < $levelInfo['need_prestige'] ) {
			return -244;
		}

		//判断材料是否足够
		$needs = json_decode($levelInfo['need_items']);
		$needGoods = array();
		$needScroll	= array();
		$needStuff = array();
		$needDecorInBag	= array();
		$needWeapon	= array();
		foreach	( $needs as	$need )	{
			$needCid = $need[0];
			$needCount = $need[1];
			$needType =	substr($needCid, -2, 1);
			//1->物品,2->卷轴,3->材料,4->工作台,5->装修,6->装备
			if ( $needType == 1	) {
				$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
				if ( $userGoods[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needGoods[] = array('cid' => $needCid,	'count'	=> $needCount);
			}
			else if	( $needType	== 2 ) {
				$userScroll	= Hapyfish2_Alchemy_HFC_Scroll::getUserScroll($uid);
				if ( $userScroll[$needCid]['count']	< $needCount ) {
					return -204;
				}
				$needScroll[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
			else if	( $needType	== 3 ) {
				$userStuff = Hapyfish2_Alchemy_HFC_Stuff::getUserStuff($uid);
				if ( $userStuff[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needStuff[] = array('cid' => $needCid,	'count'	=> $needCount);
			}
			else if	( $needType	== 5 ) {
				$userDecorInBag	= Hapyfish2_Alchemy_HFC_Decor::getBag($uid);
				if ( $userDecorInBag[$needCid]['count']	< $needCount ) {
					return -204;
				}
				$needDecorInBag[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
			else if	( $needType	== 6 ) {
				$userNewWeapon = Hapyfish2_Alchemy_HFC_Weapon::getNewWeapon($uid);
				if ( $userNewWeapon[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needWeapon[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
		}

		//完成升级
		$ok = Hapyfish2_Alchemy_HFC_User::updateUserSmithyLevel($uid, $nextLevel);
		if (!$ok) {
			return -200;
		}

		//升级自动处理
		$villageBuildUpgrade = array('id' => SMITHY_ID, 'level' => $nextLevel);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'villageBuildUpgrade', $villageBuildUpgrade);

		//扣除金币，声望
		Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $levelInfo['need_coin']);
		//Hapyfish2_Alchemy_HFC_User::decUserPrestige($uid, $levelInfo['need_prestige']);

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
					Hapyfish2_Alchemy_HFC_Weapon::delWeaponByCid($uid, $weapon['cid']);
				}
			}
		}

		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeItems', $removeItems);

		//report log,统计玩家铁匠铺升级信息
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('352', array($uid, $nextLevel, $levelInfo['need_coin'], $levelInfo['need_prestige']));
		
		//platform feed
		Hapyfish2_Alchemy_Bll_PlatformFeed_Feed::buildingLevelup($uid, 2);
		
		return $levelInfo['cd_time'];
	}

	/**
	 * 竞技场升级
	 * @param int $uid
	 */
	public static function arenaUpgrade($uid)
	{
		//用户当前竞技场等级
		$userArenaLevel = Hapyfish2_Alchemy_HFC_User::getUserArenaLevel($uid);

		$nextLevel = $userArenaLevel + 1;

		//竞技场下一等级信息
		$levelInfo = Hapyfish2_Alchemy_Cache_Basic::getArenaLevel($nextLevel);
		if ( !$levelInfo ) {
			return -200;
		}
		//用户当前经营等级
		$userHomeLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
		if ( $userHomeLevel < $levelInfo['need_level'] ) {
			return -243;
		}

		$userCoin = Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
		if ( $userCoin < $levelInfo['need_coin'] ) {
			return -207;
		}
		
		//判断材料是否足够
		$needs = json_decode($levelInfo['need_items']);
		$needGoods = array();
		$needScroll	= array();
		$needStuff = array();
		$needDecorInBag	= array();
		$needWeapon	= array();
		foreach	( $needs as	$need )	{
			$needCid = $need[0];
			$needCount = $need[1];
			$needType =	substr($needCid, -2, 1);
			//1->物品,2->卷轴,3->材料,4->工作台,5->装修,6->装备
			if ( $needType == 1	) {
				$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
				if ( $userGoods[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needGoods[] = array('cid' => $needCid,	'count'	=> $needCount);
			}
			else if	( $needType	== 2 ) {
				$userScroll	= Hapyfish2_Alchemy_HFC_Scroll::getUserScroll($uid);
				if ( $userScroll[$needCid]['count']	< $needCount ) {
					return -204;
				}
				$needScroll[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
			else if	( $needType	== 3 ) {
				$userStuff = Hapyfish2_Alchemy_HFC_Stuff::getUserStuff($uid);
				if ( $userStuff[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needStuff[] = array('cid' => $needCid,	'count'	=> $needCount);
			}
			else if	( $needType	== 5 ) {
				$userDecorInBag	= Hapyfish2_Alchemy_HFC_Decor::getBag($uid);
				if ( $userDecorInBag[$needCid]['count']	< $needCount ) {
					return -204;
				}
				$needDecorInBag[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
			else if	( $needType	== 6 ) {
				$userNewWeapon = Hapyfish2_Alchemy_HFC_Weapon::getNewWeapon($uid);
				if ( $userNewWeapon[$needCid]['count'] < $needCount	) {
					return -204;
				}
				$needWeapon[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
			}
		}

		//完成升级
		$ok = Hapyfish2_Alchemy_HFC_User::updateUserArenaLevel($uid, $nextLevel);
		if (!$ok) {
			return -200;
		}
		
		//用户竞技场信息
		$userArena = Hapyfish2_Alchemy_Cache_Arena::getUserArena($uid);
		$userArena['challengeTimes'] += 1;
		Hapyfish2_Alchemy_Cache_Arena::updateUserArena($uid, $userArena);
		
		//升级自动处理
		$villageBuildUpgrade = array('id' => ARENA_ID, 'level' => $nextLevel);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'villageBuildUpgrade', $villageBuildUpgrade);

		//扣除金币
		Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $levelInfo['need_coin']);
		
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
					Hapyfish2_Alchemy_HFC_Weapon::delWeaponByCid($uid, $weapon['cid']);
				}
			}
		}

		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeItems', $removeItems);

		//report log,统计玩家竞技场升级信息
		$logger = Hapyfish2_Util_Log::getInstance();
		$logger->report('354', array($uid, $nextLevel, $levelInfo['need_coin'], $levelInfo['need_prestige']));
		
		//platform feed
		//Hapyfish2_Alchemy_Bll_PlatformFeed_Feed::buildingLevelup($uid, 2);
		
		return $levelInfo['cd_time'];
	}

	/**
	 * 训练场升级
	 * @param int $uid
	 */
	public static function trainingUpgrade($uid)
	{
		//用户当前训练营等级
		$userTrainingLevel = Hapyfish2_Alchemy_HFC_User::getUserTrainingLevel($uid);
		$nextLevel = $userTrainingLevel + 1;
	
		//训练营下一等级信息
		$levelInfo = Hapyfish2_Alchemy_Cache_Basic::getTrainingLevel($nextLevel);
		if ( !$levelInfo ) {
			return -200;
		}
		//用户当前经营等级
		$userHomeLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
		if ( $userHomeLevel < $levelInfo['need_level'] ) {
			return -243;
		}
	
		$userCoin = Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
		if ( $userCoin < $levelInfo['need_coin'] ) {
			return -207;
		}
	
		//判断材料是否足够
		$needs = json_decode($levelInfo['need_items']);
				$needGoods = array();
				$needScroll	= array();
				$needStuff = array();
				$needDecorInBag	= array();
				$needWeapon	= array();
				foreach	( $needs as	$need )	{
					$needCid = $need[0];
					$needCount = $need[1];
					$needType =	substr($needCid, -2, 1);
					//1->物品,2->卷轴,3->材料,4->工作台,5->装修,6->装备
					if ( $needType == 1	) {
						$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
						if ( $userGoods[$needCid]['count'] < $needCount	) {
							return -204;
						}
						$needGoods[] = array('cid' => $needCid,	'count'	=> $needCount);
					}
					else if	( $needType	== 2 ) {
						$userScroll	= Hapyfish2_Alchemy_HFC_Scroll::getUserScroll($uid);
						if ( $userScroll[$needCid]['count']	< $needCount ) {
							return -204;
						}
						$needScroll[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
					}
					else if	( $needType	== 3 ) {
						$userStuff = Hapyfish2_Alchemy_HFC_Stuff::getUserStuff($uid);
						if ( $userStuff[$needCid]['count'] < $needCount	) {
							return -204;
						}
						$needStuff[] = array('cid' => $needCid,	'count'	=> $needCount);
					}
					else if	( $needType	== 5 ) {
						$userDecorInBag	= Hapyfish2_Alchemy_HFC_Decor::getBag($uid);
						if ( $userDecorInBag[$needCid]['count']	< $needCount ) {
							return -204;
						}
						$needDecorInBag[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
					}
					else if	( $needType	== 6 ) {
						$userNewWeapon = Hapyfish2_Alchemy_HFC_Weapon::getNewWeapon($uid);
						if ( $userNewWeapon[$needCid]['count'] < $needCount	) {
							return -204;
						}
						$needWeapon[] =	array('cid'	=> $needCid, 'count' =>	$needCount);
					}
				}
	
				//完成升级
				$ok = Hapyfish2_Alchemy_HFC_User::updateUserTrainingLevel($uid, $nextLevel);
				if (!$ok) {
					return -200;
				}
	
				//用户训练营信息
								
	
				//升级自动处理
				$villageBuildUpgrade = array('id' => TRAINING_ID, 'level' => $nextLevel);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'villageBuildUpgrade', $villageBuildUpgrade);
	
				//扣除金币
				Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $levelInfo['need_coin']);
	
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
							Hapyfish2_Alchemy_HFC_Weapon::delWeaponByCid($uid, $weapon['cid']);
						}
					}
				}

				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeItems', $removeItems);
	
				//report log,统计玩家训练营升级信息
				$logger = Hapyfish2_Util_Log::getInstance();
				$logger->report('355', array($uid, $nextLevel, $levelInfo['need_coin'], $levelInfo['need_prestige']));
	
				//platform feed
				//Hapyfish2_Alchemy_Bll_PlatformFeed_Feed::buildingLevelup($uid, 2);
	
				return $levelInfo['cd_time'];
	}
	
}