<?php

class Hapyfish2_Alchemy_Bll_MercenaryWork
{
	
	/**
	 * 查询当前打工信息
	 * @param int $uid
	 */
	public static function getWork($uid)
	{
		$allWork = Hapyfish2_Alchemy_HFC_MercenaryWork::getAll($uid);
		$list = array();
		foreach ( $allWork as $v ) {
			$awards = json_decode($v['awards'], true);
			$awardList = array();
			foreach ( $awards as $k => $a ) {
				if ( isset($a[2]) && $a[2] == 4 ) {
					$awardList[] = array($a[0], 4);
				}
				else if ( $a[0] == 'coin' ) {
					$awardList[] = array("coin", $a[1]);
				}
				else if ( $a[0] == 'exp' ) {
					
				}
				else {
					$awardList[] = array($a[0], 1);
				}
			}
			$work = array('id' => $v['id'],
						  'awards' => $awardList,
						  'state' => $v['state']);
			if ( $v['state'] == 2 ) {
				$work['time'] = $v['finish_time'];
				$work['roleIds'] = $v['role_ids'];
			}
			$list[] = $work;
		}
		
		//for test
		if ( empty($list) ) {
			$awards = array(array(131,1), array(431,1));
			$list[] = array('id' => 100, 'state' => 1, 'awards' => $awards);
			$list[] = array('id' => 2, 'state' => 1, 'awards' => $awards);
		}
				
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'pointVos', $list);
		return 1;
	}
	
	/**
	 * 开始打工
	 * @param int $uid
	 * @param int $id,打工地点id
	 * @param varchar $roleIds,参加佣兵列表
	 */
	public static function startWork($uid, $id, $roleIds)
	{
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
    	$vipInfo = $vip->getVipInfo($uid);
    	if($vipInfo['level'] == 0 || $vipInfo['vipStatus'] == 0){
    		return -100;
    	} 
		//打工地点静态信息
		$basicWork = Hapyfish2_Alchemy_Cache_Basic::getMercenaryWork($id);
		if ( !$basicWork ) {
			return -200;
		}
		
		//行动力
		$userSpInfo = Hapyfish2_Alchemy_HFC_User::getUserSp($uid);
		$userSp = $userSpInfo['sp'];
		if ( $userSp < $basicWork['sp'] ) {
			return -208;
		}
		
		//佣兵个数，佣兵等级
		$roleIds = explode(',', $roleIds);
		$roleIds = array_unique($roleIds);
		if ( empty($roleIds) || count($roleIds) < $basicWork['role_num'] ) {
			return -200;
		}
		foreach ( $roleIds as $roleId ) {
			if ( $roleId == 0 ) {
        		$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
			}
			else {
				$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $roleId);
			}
			if ( !$userMercenary || $userMercenary['work'] == 1 || $userMercenary['level'] < $basicWork['role_level'] ) {
				return -200;
			}
		}
		
		//用户打工地点信息
		$userWorkInfo = Hapyfish2_Alchemy_HFC_MercenaryWork::getOne($uid, $id);
		if ( !isset($userWorkInfo['state']) || $userWorkInfo['state'] != 1 ) {
			return -252;
		}
		
		//开始打工
		
		//随机奖励信息
		//$awards = self::_getRandomAward($uid, $basicWork);
		$awards = $userWorkInfo['awards'];
		$finishTime = time() + $basicWork['need_time'];
		$newWork = array('uid' => $uid,
						 'id' => $id,
						 'finish_time' => $finishTime,
						 'role_ids' => implode(',', $roleIds),
						 'awards' => $awards,
						 'state' => 2);
		$ok = Hapyfish2_Alchemy_HFC_MercenaryWork::updateOne($uid, $id, $newWork);
		if ( !$ok ) {
			return -200;
		}
		
		//扣除行动力
		Hapyfish2_Alchemy_HFC_User::decUserSp($uid, $basicWork['sp']);
	
		//更新佣兵打工状态,work=1 打工中
		foreach ( $roleIds as $roleId ) {
			if ( $roleId == 0 ) {
        		$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
        		$userMercenary['work_time'] = $finishTime;
        		$userMercenary['work_max_time'] = $basicWork['need_time'];
        		Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary);
			}
			else {
				$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $roleId);
        		$userMercenary['work_time'] = $finishTime;
        		$userMercenary['work_max_time'] = $basicWork['need_time'];
				Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $userMercenary['mid'], $userMercenary);
			}
		}
		
		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
	
		$awards = json_decode($awards);
		$awardList = array();
		foreach ( $awards as $k => $a ) {
			if ( isset($a[2]) && $a[2] == 4 ) {
				$awardList[] = array($a[0], 4);
			}
			else if ( $a[0] == 'coin' ) {
				$awardList[] = array("coin", $a[1]);
			}
			else if ( $a[0] == 'exp' ) {
				
			}
			else {
				$awardList[] = array($a[0], 1);
			}
		}
		$pointVo = array('id' => $newWork['id'],
						 'time' => $newWork['finish_time'],
						 'roleIds' => $roleIds,
						 'awards' => $awardList,
						 'state' => 2);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'pointVo', $pointVo);
		
		return 1;
	}
	
	/**
	 * 立即完成打工
	 * @param int $uid
	 * @param int $id
	 */
	public static function completeWork($uid, $id)
	{
		//用户打工地点信息
		$userWorkInfo = Hapyfish2_Alchemy_HFC_MercenaryWork::getOne($uid, $id);
		if ( !isset($userWorkInfo['state']) || $userWorkInfo['state'] != 2 ) {
			return -200;
		}
		
		//打工剩余时间
		$nowTime = time();
		if ( $userWorkInfo['finish_time'] < $nowTime ) {
			return -200;
		}
		$remainTime = $userWorkInfo['finish_time'] - $nowTime;
		
		//半小时一个沙漏,cid = 2715;
		$gameData = Hapyfish2_Alchemy_Bll_BasicInfo::getGameData();
		$shalouTime = $gameData['shalouTime'];
		$needGoodsCid =	$gameData['shalouCid'];
		$needCardCount = ceil( $remainTime/$shalouTime );
		$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
		if ( $userGoods[$needGoodsCid]['count'] < $needCardCount )	{
			return -212;
		}
		
		//立刻完成
		$userWorkInfo['finish_time'] = $nowTime;
		$ok = Hapyfish2_Alchemy_HFC_MercenaryWork::updateOne($uid, $id, $userWorkInfo);
		if (!$ok) {
			return -200;
		}
		
		//使用沙漏
		if ( $needCardCount	> 0	) {
			Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid,	$needGoodsCid, $needCardCount);
		}
		return 1;
	}
	
	/**
	 * 收获打工奖励
	 * @param int $uid
	 * @param int $id
	 */
	public static function getAward($uid, $id)
	{
		//用户打工地点信息
		$userWorkInfo = Hapyfish2_Alchemy_HFC_MercenaryWork::getOne($uid, $id);
		if ( !isset($userWorkInfo['state']) || $userWorkInfo['state'] != 2 ) {
			return -200;
		}
		
		$nowTime = time();
		if ( $userWorkInfo['finish_time'] > $nowTime ) {
			return -200;
		}
		
		$roleIds = explode(',', $userWorkInfo['role_ids']);
		
		//打工地点静态信息
		$basicWork = Hapyfish2_Alchemy_Cache_Basic::getMercenaryWork($id);
		
		//随机奖励信息
		$awards = self::_getRandomAward($uid, $basicWork);
		
		//更新打工点信息
		$userWorkInfo['state'] = 1;
		$userWorkInfo['finish_time'] = 0;
		$userWorkInfo['role_ids'] = 0;
		$userWorkInfo['awards'] = $awards;
		$ok = Hapyfish2_Alchemy_HFC_MercenaryWork::updateOne($uid, $id, $userWorkInfo);
		if (!$ok) {
			return -200;
		}
		
		$awards = json_decode($awards);
		$conditionVo = array();
		//发放奖励
		foreach ( $awards as $award ) {
			//奖励金币
			if ( $award[0] == 'coin' ) {
				$addCoin = $award[1];
				Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $addCoin);
				
				$conditionVo[] = array('type' => 2, 'id' => 'coin', 'num' => $addCoin);
			}//奖励佣兵经验
			else if ( $award[0] == 'exp' ) {
				$addExp = $award[1];
				foreach ( $roleIds as $roleId ) {
					if ( $roleId == 0 ) {
						$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
					}
					else {
						$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $roleId);
					}
					$userMercenary['exp'] += $addExp;
	        		$userMercenary['work_time'] = 0;
	        		$userMercenary['work_max_time'] = 0;
					if ( $roleId == 0 ) {
						$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary);
					}
					else {
						$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $roleId, $userMercenary, true);
					}
					Hapyfish2_Alchemy_Bll_Mercenary::checkMercenaryLevelUp($uid, $roleId, $userMercenary);
				}
				
				$conditionVo[] = array('type' => 2, 'id' => 'battleExp', 'num' => $addExp);
			}//奖励物品
			else {
				$addCid = $award[0];
				$addCount = $award[1];
				Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $addCid, $addCount);
				
				$conditionVo[] = array('type' => 1, 'id' => $addCid, 'num' => $addCount);
			}
		}
		
		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
		
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'awards', $conditionVo);
		
		return 1;
	}
	
	/**
	 * 取消打工
	 * @param int $uid
	 * @param int $id
	 */
	public static function cancelWork($uid, $id)
	{
		//用户打工地点信息
		$userWorkInfo = Hapyfish2_Alchemy_HFC_MercenaryWork::getOne($uid, $id);
		if ( !isset($userWorkInfo['state']) || $userWorkInfo['state'] != 2 ) {
			return -200;
		}
		
		//打工地点静态信息
		$basicWork = Hapyfish2_Alchemy_Cache_Basic::getMercenaryWork($id);
		
		$roleIds = explode(',', $userWorkInfo['role_ids']);
		//更新打工点信息
		$userWorkInfo['state'] = 1;
		$userWorkInfo['finish_time'] = 0;
		$userWorkInfo['role_ids'] = 0;
		$userWorkInfo['awards'] = $basicWork['awards'];
		$ok = Hapyfish2_Alchemy_HFC_MercenaryWork::updateOne($uid, $id, $userWorkInfo);
		if (!$ok) {
			return -200;
		}
		
		//更新佣兵工作状态
		foreach ( $roleIds as $roleId ) {
			if ( $roleId == 0 ) {
				$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
			}
			else {
				$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $roleId);
			}
        	$userMercenary['work_time'] = 0;
        	$userMercenary['work_max_time'] = 0;
			if ( $roleId == 0 ) {
				$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary);
			}
			else {
				$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $roleId, $userMercenary, true);
			}
		}
		
		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
		
		return 1;
	}
	
	/**
	 * 解锁工作地点
	 * @param int $uid
	 * @param int $id
	 */
	public static function setWorkOpened($uid, $id)
	{
		//打工地点静态信息
		$basicWork = Hapyfish2_Alchemy_Cache_Basic::getMercenaryWork($id);
		//随机奖励信息
		$awards = self::_getRandomAward($uid, $basicWork);
		
		$work = array('uid' => $uid,
					  'id' => $id,
					  'finish_time' => 0,
					  'role_ids' => 0,
					  'awards' => $awards,
					  'state' => 1);
		return Hapyfish2_Alchemy_HFC_MercenaryWork::addOne($uid, $work);
	}
	
	/**
	 * 随机奖励
	 * @param int $uid
	 * @param array $basicWork
	 */
	public static function _getRandomAward($uid, $basicWork)
	{
		$rand = rand(1, 10);
		if ( $rand > $basicWork['random_award_pro'] ) {
			return $basicWork['awards'];
		}
		
		//获取随机奖励信息
		$randArray = array();
		$randomAwardData = json_decode($basicWork['random_award_data']);
		foreach ( $randomAwardData as $m ) {
			for ( $i=0,$iCount=$m[2]; $i<$iCount; $i++ ) {
				$randArray[] = array($m[0], $m[1]);
			}
		}
		$randTemp = array_rand($randArray);
		$randomAward = $randArray[$randTemp];
		//$randomAward = array(3434, 2);
		$randomAward[] = 4;
		
		$awards = json_decode($basicWork['awards']);
		$awards[] = $randomAward;
		
		return json_encode($awards);
	}
		
}