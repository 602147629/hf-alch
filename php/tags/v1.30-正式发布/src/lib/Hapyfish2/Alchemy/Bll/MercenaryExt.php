<?php

class Hapyfish2_Alchemy_Bll_MercenaryExt
{
	/**
	 * 传授技能图鉴
	 * 
	 * @param int $uid,
	 * @param int $id,佣兵实例id
	 */
	public static function studySkill($uid, $id)
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
	}
}