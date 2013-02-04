<?php

/**
 * training
 *
 * @copyright  Copyright (c) 2012 HapyFish
 * @create      2012/08   Nick
 */
class Hapyfish2_Alchemy_Bll_Training
{
    const MAX_POS_NUM = 11;			//最大训练位置数

	/**
	 * 初始化训练营信息
	 * 
	 */
	public static function initTrainingStatic()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_BasicExt::getTrainingList();
		foreach ($data as $item) {
			$info[] = array(
					'id' => $item['id'],
					'type' => $item['type'],
					'job' => $item['job'],
					'currentLevel' => $item['level'],
					'currentLevelAddNum' => $item['add_num'],
					'startCondition' => json_decode($item['needs'], true),
					'currentLevelAddNumS' => $item['add_num_s'],
					'startConditionS' => json_decode($item['needs_s'], true),
					'coolTime' => $item['need_time'],
					'name' => $item['name'],
					'content' => $item['content']
			);
		}

		return array('trainingCampStaticVo' => $info);
	}
	
	/**
	 * 初始化训练营信息
	 * @param int $uid
	 * 
	 */
	public static function initTraining($uid)
	{
		//训练信息列表
		$trainingCampInitVo = self::_getTraingVo($uid);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'trainingCampInitVo', $trainingCampInitVo);

		//训练位置信息
		$userTrainingPosNum = Hapyfish2_Alchemy_HFC_User::getUserTrainingPosNum($uid);
		$commonInfo = array('currentPositionNum' => $userTrainingPosNum, 'maxPositionNum' => self::MAX_POS_NUM);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'trainingCampCommonVo', $commonInfo);
		
        return 1;
    }
    
    /**
     * 格式化训练营信息
     * @param int $uid
     * @return array
     */
    private static function _getTraingVo($uid)
    {
    	//所有训练信息列表
    	$userTraining = Hapyfish2_Alchemy_HFC_Training::getAll($uid);
    	$trainingList = $userTraining['list'];
    	
    	$list = array();
    	$nowTm = time();
    	//佣兵与主角数据
    	$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);

    	foreach ( $homeSide as $v ) {
    		//佣兵id
    		$mid = $v['id'];
    		if ( isset($trainingList[$mid]) ) {
				$traInfo = $trainingList[$mid];
    			//是否在升级中
    			if ( $traInfo['id'] != 0) {
    				//剩余时间，是否可领
    				$remainTm = $traInfo['complete_time'] - $nowTm;
    				$remainTm = $remainTm < 0 ? 0 : $remainTm;
    				if ( $remainTm == 0 && $traInfo['has_get'] == 0 ) {
    					$isOk = 1;
    				}
    				else {
    					$isOk = 0;
    				}
    			}
    			else {
    				$remainTm = 0;
    				$isOk = 0;
    			}
    			
    			$list[] = array('roleId' => $mid,
    					'addHpLevel' => $traInfo['hp_lev'],
    					'addMpLevel' => $traInfo['mp_lev'],
    					'addPaLevel' => $traInfo['pa_lev'],
    					'addPdLevel' => $traInfo['pd_lev'],
    					'addMaLevel' => $traInfo['ma_lev'],
    					'addMdLevel' => $traInfo['md_lev'],
    					'addSpeedLevel' => $traInfo['agility_lev'],
    					'addCritLevel' => $traInfo['crit_lev'],
    					'addDodgeLevel' => $traInfo['dodge_lev'],
    					'addHitRateLevel' => $traInfo['hit_lev'],
    					'addLuckyLevel' => $traInfo['tou_lev'],
    					'coolTime' => $remainTm,
    					'isOk' => $isOk,
    					'type' => $traInfo['type'],
    					'id' => $traInfo['id']);
    		}
    		else {
    			$list[] = array('roleId' => $mid,
    					'addHpLevel' => 0,
    					'addMpLevel' => 0,
    					'addPaLevel' => 0,
    					'addPdLevel' => 0,
    					'addMaLevel' => 0,
    					'addMdLevel' => 0,
    					'addSpeedLevel' => 0,
    					'addCritLevel' => 0,
    					'addDodgeLevel' => 0,
    					'addHitRateLevel' => 0,
    					'addLuckyLevel' => 0,
    					'coolTime' => 0,
    					'isOk' => 0,
    					'type' => 0,
    					'id' => 0);
    		}
    	}
    	return $list;
    }
    
    /**
     * 开始训练
     * @param int $uid
     * @param int $roleId,佣兵Id
     * @param int $tid,训练项目Id
     * @param int $gemType,训练类型:0:普通训练，1:高等训练
     */
    public static function startTraining($uid, $roleId, $tid, $gemType)
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
    	$fieldInfo = self::_getFieldByType($basicTra['type']);
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

    	$TrainingCampInitVo = self::_getTraingVo($uid);
    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'trainingCampInitVo', $TrainingCampInitVo);
    	
    	Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'trainingCd');
    	
    	return 1;
    }
    
    public static function startQqTraining($uid, $roleId, $tid, $gemType)
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
    	$needGem = 0;
    	foreach($needs as $k => $v){
    		if($v['type'] == 2 && $v['id'] == 'gem')
    		{
    			$needGem = $v['num'];
    			break;
    		}
    	}
    	if($needGem > 0){
    		$goodsInfo = array();
	    	$goodsInfo['goodsmeta'] = '训练佣兵*训练佣兵';
	    	$goodsInfo['goodsurl'] = STATIC_HOST.'/alchemy/image/item/qpoint.jpg';
	    	$goodsInfo['payitem'] = 'p011*'.$needGem.'*1';
			$token = Hapyfish2_Platform_Bll_QqpayByToken::getToken($uid,$goodsInfo);
			if($token){
				Hapyfish2_Alchemy_Bll_UserResult::setYellow($uid);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'payJs', $token['url_params']);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'token', $token['token']);
				$payNeedData = array('roleId'=>$roleId,'tid'=>$tid,'gemType'=>$gemType);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'payNeedData', $payNeedData);
			}    	
    	}
    	return 1;
    }

    /**
     * 完成训练
     * @param int $uid
     * @param int $roleId,佣兵Id
     */
    public static function completeTraining($uid, $roleId)
    {
    	//该佣兵训练信息
    	$roleTra = Hapyfish2_Alchemy_HFC_Training::getOne($uid, $roleId);
    	if ( !$roleTra ) {
    		return -275;
    	}
    	
    	if ( $roleTra['id'] == 0  ) {
    		return -275;
    	}
    	
    	$nowTm = time();
    	//剩余时间，是否可领
    	$remainTm = $roleTra['complete_time'] - $nowTm;
    	$remainTm = $remainTm < 0 ? 0 : $remainTm;
    	
    	if ( $remainTm > 0 ) {
    		return -276;
    	}
    	
    	if ( $roleTra['has_get'] == 1 ) {
    		return -277;
    	}
    	
    	//佣兵信息
    	if ( $roleId == 0 ) {
    		$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
    	}
    	else {
    		$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $roleId);
    	}
    	if ( !$userMercenary ) {
    		return -275;
    	}

    	$basicTra = Hapyfish2_Alchemy_Cache_BasicExt::getTrainingInfo($roleTra['id']);
    	
    	$fieldInfo = self::_getFieldByType($basicTra['type']);
    	$fieldLev = $fieldInfo['fieldLev'];
    	$fieldAdd = $fieldInfo['fieldAdd'];
    	$fieldMer = $fieldInfo['fieldMer'];
    	
    	//判断训练类型，0：普通训练,1：高等训练
    	if ( $roleTra['gem_type'] == 1 ) {
    		$basicTra['add_num'] = $basicTra['add_num_s'];
    	}
    	
    	/**start - complete training**/
    	$roleTra['id'] = 0;
    	$roleTra['type'] = 0;
    	$roleTra['complete_time'] = 0;
    	$roleTra['has_get'] = 0;
    	$roleTra['gem_type'] = 0;
    	$roleTra[$fieldLev] = $basicTra['level'];
    	$roleTra[$fieldAdd] += $basicTra['add_num'];
    	
    	try {
	    	$ok = Hapyfish2_Alchemy_HFC_Training::updateOne($uid, $roleId, $roleTra);
	    	if ( !$ok ) {
	    		return -200;
	    	}
	    	
	    	$userMercenary[$fieldMer] += $basicTra['add_num'];
	    	//佣兵信息
	    	if ( $roleId == 0 ) {
	    		$ok2 = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary);
	    	}
	    	else {
	    		$ok2 = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $roleId, $userMercenary);
	    	}
	    	if ( !$ok2 ) {
	    		return -200;
	    	}
    	}
	    catch (Exception $e) {
	        info_log('completeTraining:failed'.$e->getMessage(), 'Bll_Training');
	    }

	    //佣兵与主角数据
	    $homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
	    $rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
	    Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);

	    //训练信息列表
	    $trainingCampInitVo = self::_getTraingVo($uid);
	    Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'trainingCampInitVo', $trainingCampInitVo);
	    
	    Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'trainingCd');
	    
    	return 1;
    }
    
    /**
     * 添加训练位置
     * @param int $uid
     * @param int $type,1:招募好友开通位置 2：花宝石开通位置
     */
    public static function addPos($uid, $type)
    {
    	//训练位置信息
    	$userTraPosNum = Hapyfish2_Alchemy_HFC_User::getUserTrainingPosNum($uid);
    	$maxPosNum = self::MAX_POS_NUM;
    	
    	if ( $userTraPosNum >= $maxPosNum ) {
    		return -278;
    	}
    	
    	$canAdd = false;
    	if ( $type == 1 ) {
    		if ( $userTraPosNum == 1 ) {
    			$needNum = 5;
    			$needRoleLev = 10;
    			$needUserLev = 0;
    		}
    		else if ( in_array($userTraPosNum, array(2,3,4)) ) {
    			$needNum = 5;
    			$needRoleLev = 0;
    			$needUserLev = 10;
    		}
    		else {
    			return -200;
    		}
    		
    		$curNum = 0;
    		$inviteData = Hapyfish2_Alchemy_Bll_InviteLog::get($uid);
    		$list = $inviteData['list'];
    		foreach ( $list as $v ) {
    			$friendRole = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($v['fid']);
    			$friendLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($v['fid']);

    			if ( $friendRole['level'] >= $needRoleLev && $friendLevel >= $needUserLev ) {
    				$curNum ++;
    			}
    		}
    		
    		if ( $curNum < $needNum ) {
    			return -279;
    		}
    		$canAdd = true;
    	}
    	else if ( $type == 2 ) {
    		if ( $userTraPosNum == 1 ) {
    			$needGem = 100;
    		}
    		else if ( $userTraPosNum == 2 ) {
    			$needGem = 200;
    		}
    		else if ( $userTraPosNum == 3 ) {
    			$needGem = 300;
    		}
    		else if ( $userTraPosNum == 4 ) {
    			$needGem = 400;
    		}
    		else if ( $userTraPosNum == 5 ) {
    			$needGem = 500;
    		}
    		else if ( $userTraPosNum == 6 ) {
    			$needGem = 500;
    		}
    		else if ( $userTraPosNum == 7 ) {
    			$needGem = 500;
    		}
    		else if ( $userTraPosNum == 8 ) {
    			$needGem = 500;
    		}
    		else if ( $userTraPosNum == 9 ) {
    			$needGem = 500;
    		}
    		else if ( $userTraPosNum == 10 ) {
    			$needGem = 500;
    		}
    		else {
    			return -200;
    		}

    		$userGem = Hapyfish2_Alchemy_HFC_User::getUserGem($uid);
    		if ($userGem < $needGem) {
    			return -206;
    		}
    		$canAdd = true;
    	}
    	
    	if ( !$canAdd ) {
    		return -279;
    	}
    	
    	/** start - add position num **/
    	$userTraPosNum ++;
    	
    	try {
	    	$ok = Hapyfish2_Alchemy_HFC_User::updateUserTrainingPosNum($uid, $userTraPosNum, true);
	    	if ( !$ok ) {
	    		info_log('addPos:failed:'.$uid.'-type:'.$type.'-num:'.$userTraPosNum, 'Bll_Training');
	    		return -200;
	    	}
	    	if ( $type == 2 ) {
	    		//购买需要经营等级
	    		$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
	    		$gemInfo = array(
	    				'uid' => $uid,
	    				'cost' => $needGem,
	    				'summary' => LANG_PLATFORM_BASE_TXT_8,
	    				'user_level' => $userLevel,
	    				'cid' => $userTraPosNum,
	    				'num' => 1
	    		);
	    		$ok2 = Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo,14);
	    		if ( !$ok2 ) {
	    			info_log('addPos-consume:failed:'.$uid.'-type:'.$type.'-num:'.$userTraPosNum, 'Bll_Training');
	    			return -200;
	    		}
	    	}
    	}
    	catch ( Exception $e ) {
	        info_log('addPos:failed'.$e->getMessage(), 'Bll_Training');
    	}

    	Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'trainingCd');
    	
    	return 1;
    }

    
    public static function addQqPos($uid, $type)
    {
    	//训练位置信息
    	$userTraPosNum = Hapyfish2_Alchemy_HFC_User::getUserTrainingPosNum($uid);
    	$maxPosNum = self::MAX_POS_NUM;
    	
    	if ( $userTraPosNum >= $maxPosNum ) {
    		return -278;
    	}
    	
    	if ( $userTraPosNum == 1 ) {
    		$needGem = 100;
    	}
    	else if ( $userTraPosNum == 2 ) {
    		$needGem = 200;
    	}
    	else if ( $userTraPosNum == 3 ) {
    		$needGem = 300;
    	}
    	else if ( $userTraPosNum == 4 ) {
    		$needGem = 400;
    	}
    	else if ( $userTraPosNum == 5 ) {
    		$needGem = 500;
    	}
    	else if ( $userTraPosNum == 6 ) {
    		$needGem = 500;
    	}
    	else if ( $userTraPosNum == 7 ) {
    		$needGem = 500;
    	}
    	else if ( $userTraPosNum == 8 ) {
    		$needGem = 500;
    	}
    	else if ( $userTraPosNum == 9 ) {
    		$needGem = 500;
    	}
    	else if ( $userTraPosNum == 10 ) {
    		$needGem = 500;
    	}
    	else {
    		return -200;
    	}
    	$goodsInfo = array();
    	$goodsInfo['goodsmeta'] = '增加训练位*增加训练位';
    	$goodsInfo['goodsurl'] = STATIC_HOST.'/alchemy/image/item/qpoint.jpg';
    	$goodsInfo['payitem'] = 'p008*'.$needGem.'*1';
		$token = Hapyfish2_Platform_Bll_QqpayByToken::getToken($uid,$goodsInfo);
		if($token){
			Hapyfish2_Alchemy_Bll_UserResult::setYellow($uid);
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'payJs', $token['url_params']);
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'token', $token['token']);
		}    	
    	return 1;
    }
    /**
     * 完成训练
     * @param int $uid
     * @param int $roleId,佣兵Id
     */
    public static function completeCd($uid, $roleId)
    {
    	//该佣兵训练信息
    	$roleTra = Hapyfish2_Alchemy_HFC_Training::getOne($uid, $roleId);
    	if ( !$roleTra ) {
    		return -275;
    	}
    	 
    	if ( $roleTra['id'] == 0  ) {
    		return -275;
    	}

    	$nowTm = time();
    	//剩余时间，是否可领
    	$remainTm = $roleTra['complete_time'] - $nowTm;
    	$remainTm = $remainTm < 0 ? 0 : $remainTm;
    	
    	//半小时一个沙漏,cid = 2715;
    	$gameData = Hapyfish2_Alchemy_Bll_BasicInfo::getGameData();
    	$shalouTime = $gameData['shalouTime'];
    	$needGoodsCid =	$gameData['shalouCid'];
    	$needCardCount = ceil( $remainTm/$shalouTime );
    	$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
    	if ( !isset($userGoods[$needGoodsCid]) || $userGoods[$needGoodsCid]['count'] < $needCardCount )	{
    		return -212;
    	}
    	
    	if ( $remainTm <= 0 ) {
    		return -281;
    	}
    	
    	/**start - complete cd**/
    	$roleTra['complete_time'] = 0;
    	
    	try {
    		$ok = Hapyfish2_Alchemy_HFC_Training::updateOne($uid, $roleId, $roleTra);
    		if ( !$ok ) {
    			return -200;
    		}
    		
    		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid,	$needGoodsCid, $needCardCount);
    	}
    	catch (Exception $e) {
    		info_log('completeCd:failed'.$e->getMessage(), 'Bll_Training');
    	}

    	Hapyfish2_Alchemy_Bll_Cdtime::resetCdTmByField($uid, 'trainingCd');
    	
    	return 1;
    }
    
    public static function _getFieldByType($type)
    {
    	switch ( $type ) {
    		case 1 :
    			$fieldLev = 'hp_lev';
    			$fieldAdd = 'add_hp';
    			$fieldMer = 'hp_max';
    			break;
    		case 2 :
    			$fieldLev = 'mp_lev';
    			$fieldAdd = 'add_mp';
    			$fieldMer = 'mp_max';
    			break;
    		case 3 :
    			$fieldLev = 'pa_lev';
    			$fieldAdd = 'add_pa';
    			$fieldMer = 'phy_att';
    			break;
    		case 4 :
    			$fieldLev = 'ma_lev';
    			$fieldAdd = 'add_ma';
    			$fieldMer = 'mag_att';
    			break;
    		case 5 :
    			$fieldLev = 'agility_lev';
    			$fieldAdd = 'add_agility';
    			$fieldMer = 'agility';
    			break;
    		case 6 :
    			$fieldLev = 'pd_lev';
    			$fieldAdd = 'add_pd';
    			$fieldMer = 'phy_def';
    			break;
    		case 7 :
    			$fieldLev = 'md_lev';
    			$fieldAdd = 'add_md';
    			$fieldMer = 'mag_def';
    			break;
    		case 8 :
    			$fieldLev = 'crit_lev';
    			$fieldAdd = 'add_crit';
    			$fieldMer = 'crit';
    			break;
    		case 9 :
    			$fieldLev = 'dodge_lev';
    			$fieldAdd = 'add_dodge';
    			$fieldMer = 'dodge';
    			break;
    		case 10 :
    			$fieldLev = 'hit_lev';
    			$fieldAdd = 'add_hit';
    			$fieldMer = 'hit';
    			break;
    		case 11 :
    			$fieldLev = 'tou_lev';
    			$fieldAdd = 'add_tou';
    			$fieldMer = 'tou';
    			break;
    	}
    	return array('fieldLev' => $fieldLev, 'fieldAdd' => $fieldAdd, 'fieldMer' => $fieldMer);
    }
}