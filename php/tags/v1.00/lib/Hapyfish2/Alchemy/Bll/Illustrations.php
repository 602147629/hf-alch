<?php

class Hapyfish2_Alchemy_Bll_Illustrations
{
	/**
	 * 获取用户图鉴信息
	 *
	 * @param int $uid
	 */
	public static function getUserIllustrations($uid)
	{
		$userIllustrations = Hapyfish2_Alchemy_Cache_Illustrations::getUserIllustrations($uid);
		
		$result = array();
		foreach ( $userIllustrations as $v ) {
			$result[] = array('cid' => $v['id'], 'isNew' => $v['new']);
		}
		
		return $result;
	}
	
	public static function addIllusByCid($uid, $itemCid)
	{
		$resultVo =	array('result' => array('status' =>	-1));
		
		$illustration = Hapyfish2_Alchemy_Cache_Basic::getIllustInfoByCid($itemCid);
		if ( !$illustration ) {
			return $resultVo;
		}
		self::addUserIllustrations($uid, $illustration['id']);
		
		$resultVo['result']['status']= 1;
		return $resultVo;
	}
	
	
	public static function addUserIllustrations($uid, $id)
	{
		$resultVo =	array('result' => array('status' =>	-1));
		
		$illustration = Hapyfish2_Alchemy_Cache_Basic::getIllustrationsInfo($id);
		if (!$illustration) {
			return $resultVo;
		}
		
		$ok = Hapyfish2_Alchemy_Cache_Illustrations::addUserIllustrations($uid, $id);
		if ( $ok ) {
			$addillustrated = array('cid' => $id, 'isNew' => 1);
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'addillustrated', array($addillustrated));
			$resultVo['result']['status']= 1;
			
			if ( $illustration['mix_cid'] > 0 ) {
        		Hapyfish2_Alchemy_HFC_Mix::addUserMix($uid, $illustration['mix_cid']);
			}
			if ( $id == 14571 ) {
				$newHelpId = 8;
				$userHelp = Hapyfish2_Alchemy_HFC_Help::get($uid);
				$userHelp['id'] = $newHelpId;
				$userHelp['idx'] = 1;
				$userHelp['status'] = 1;
				$ok = Hapyfish2_Alchemy_HFC_Help::update($uid, $userHelp);
				if ( $ok ) {
					Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'showGuide', $newHelpId);
				}
			}
		}
		
		return $resultVo;
	}
	
	public static function readUserIllustration($uid, $id)
	{
		$resultVo =	array('result' => array('status' =>	-1));
		
		$userIllustrations = Hapyfish2_Alchemy_Cache_Illustrations::getUserIllustrations($uid);
		if ( !isset($userIllustrations[$id]) ) {
			return;
		}
		$userIllustrations[$id]['new'] = 0;
		Hapyfish2_Alchemy_Cache_Illustrations::updateUserIllustrations($uid, $userIllustrations);
		
		$resultVo['result']['status']= 1;
		return $resultVo;
	}


}