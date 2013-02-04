<?php

class Hapyfish2_Alchemy_Bll_MercenaryExt
{
	/**
	 * 传授技能图鉴
	 * 
	 * @param int $uid,
	 * @param int $id,佣兵实例id
	 */
	/* public static function studySkill($uid, $id)
	{
		$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
		$skillList = $userMercenary['skill'];
		$skill = $skillList[0];
		
		$scrollInfo = Hapyfish2_Alchemy_Cache_Basic::getScrollInfo($skill);
		if ( !$scrollInfo ) {
			return -200;
		}
		
		if ( $userMercenary['level'] < $scrollInfo['study_level'] ) {
			return -259;
		}
		
		//添加图鉴
		Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $skill);
		
		return true;
	} */
	
	/**
	 * 升级佣兵技能
	 * 
	 * @param int $uid,
	 * @param int $id,佣兵实例id
	 * @param int $cid,要升级到的技能cid,学习时为1级的技能cid,升级时为下一等级的技能cid
	 */
	public static function upgradeSkill($uid, $id, $curCid, $nextCid)
	{
		if ( $id == 0 ) {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		}
		else {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
		}
		//该佣兵已拥有技能列表，
		$hasSkillList = $userMercenary['skill_list'];
		
		//技能卷轴静态信息
		$nextSkillBasic = Hapyfish2_Alchemy_Cache_Basic::getScrollInfo($nextCid);
		if ( !$nextSkillBasic ) {
			return -337;
		}
		
		//检查需消耗物品是否满足
		$needs = json_decode($nextSkillBasic['levelup_needs'], true);
		$rstCheckNeeds = Hapyfish2_Alchemy_Bll_Item::checkNeeds($uid, $needs);
		if ( $rstCheckNeeds != 1) {
			return $rstCheckNeeds;
		}
		
		//检查佣兵等级是否满足
		if ( $userMercenary['level'] < $nextSkillBasic['need_level'] ) {
			return -803;
		}

		$newStudySkill = false;
		if ( $nextCid == $curCid ) {
			//已经学习的，无需重复学习
			$isExist = array_search($nextCid, $hasSkillList);
			if ( $isExist !== false ) {
				return -805;
			}
			$newStudySkill = true;
		}
		else {
			$curSkillBasic = Hapyfish2_Alchemy_Cache_Basic::getScrollInfo($curCid);
			if ( !$curSkillBasic ) {
				return -337;
			}
			if ( $curSkillBasic['next_level_cid'] != $nextCid ) {
				return -804;
			}

			//已经升级的，无需重复学习
			$isCurExist = array_search($curCid, $hasSkillList);
			if ( $isCurExist === false ) {
				return -806;
			}
			
			//已经升级的，无需重复学习
			$isNextExist = array_search($nextCid, $hasSkillList);
			if ( $isNextExist !== false ) {
				return -805;
			}
			
			//升级技能时，去除低级技能
			unset($hasSkillList[$isCurExist]);
			$hasSkillList = array_values($hasSkillList);
		}
		
		$hasSkillList[] = (int)$nextCid;
		$userMercenary['skill_list'] = $hasSkillList;
		
		//如果升级身上已装备技能升级，则该技能同步替换
		//佣兵当前技能列表
		$skill = $userMercenary['skill'];
		$skillChange = false;
		if ( !empty($skill) ) {
			foreach ( $skill as $k => $v ) {
				if ( $v == $curCid ) {
					$skill[$k] = $nextCid;
					$skillChange = true;
				}
			}
			if ( $skillChange ) {
				$userMercenary['skill'] = $skill;
			}
		}
		
		try {
			//被动技能数据重新计算
			if ( !$newStudySkill ) {
				if($nextSkillBasic['skill_type'] == 97){
					$userMercenary = Hapyfish2_Alchemy_Bll_Mercenary::reloadSgain($uid, $userMercenary, $curCid, 1);
				}
			}
			if($nextSkillBasic['skill_type'] == 97){
				$userMercenary = Hapyfish2_Alchemy_Bll_Mercenary::reloadSgain($uid, $userMercenary, $nextCid, 2);
			}
			
			//更新技能信息
			if ( $id == 0 ) {
				$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
			}
			else {
				$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $id, $userMercenary);
			}
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
			info_log('startTraining:failed'.$e->getMessage(), 'Bll_Training');
		}
	
		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
		
		return 1;
	}
	
	/* 
	public static function upgradeSkill($uid, $id, $idx)
	{
		if ( $id == 0 ) {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		}
		else {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
		}
		$skill = $userMercenary['skill'];
		
		$field = $idx - 1;
		$nowSkillCid = $skill[$field];
		
		//未学习技能
		if ( $nowSkillCid == -1 || $nowSkillCid == 0 ) {
			return -336;
		}
		//技能卷轴信息,当前技能信息
		$nowScroll = Hapyfish2_Alchemy_Cache_Basic::getScrollInfo($nowSkillCid);
		if ( $nowScroll['next_level_cid'] == 0 ) {
			return -337;
		}

		//技能卷轴信息,下一等级技能信息
		$nextScroll = Hapyfish2_Alchemy_Cache_Basic::getScrollInfo($nowScroll['next_level_cid']);
		if ( !$nextScroll ) {
			return -337;
		}
		
		//检查需消耗物品是否满足
		$needs = json_decode($nowScroll['levelup_needs'], true);
		$rstCheckNeeds = Hapyfish2_Alchemy_Bll_Item::checkNeeds($uid, $needs);
		if ( $rstCheckNeeds != 1) {
			return $rstCheckNeeds;
		}

		$skill[$field] = $nextScroll['cid'];
		$userMercenary['skill'] = $skill;

		try {
			//更新技能信息
			if ( $id == 0 ) {
				$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
			}
			else {
				$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $id, $userMercenary);
			}
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
	        info_log('startTraining:failed'.$e->getMessage(), 'Bll_Training');
    	}
		
		return 1;
	} */
	
	
	
	
	
}