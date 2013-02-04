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
	 * @param int $uid,
	 * @param int $id,佣兵实例id
	 * @param int $idx,佣兵技能位置id
	 */
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

		/**start - start upgrade skill**/
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
	}
	
	
	
	
	
}