<?php

class Hapyfish2_Alchemy_Bll_Card
{
	/**
	 * 使用道具
	 * @param int $uid
	 * @param int $cid，道具cid
	 */
	public static function useCard($uid, $cid, $roleId)
	{
		$type = substr($cid, -2, 1);
		if ( $type == 1 ) {
			$cardInfo = Hapyfish2_Alchemy_Cache_Basic::getGoodsInfo($cid);
			if (!$cardInfo) {
				return -200;
			}

			//需要用户等级
	        $userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
	        if ($userLevel < $cardInfo['need_level']) {
	            return -235;
	        }
			//剩余道具数
			$userCard = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
			if (!isset($userCard[$cid]) || $userCard[$cid]['count'] < 1) {
				return -239;
			}

			$result = 1;
			if ( in_array($cid, array(111, 212, 2112, 2212, 2811, 2911)) ) {
				$result = self::hpCard($uid, $cid, $cardInfo, $roleId);
			}
			else if ( $cid == 1611 ) {
				$result = self::mpCard($uid, $cid, $cardInfo, $roleId);
			}
			else if ( in_array($cid, array(515,615,715,815)) ) {
				$result = self::spCard($uid, $cid, $cardInfo);
			}
			else if ( in_array($cid, array(1415,1515)) ) {
				$result = self::expCard($uid, $cid, $cardInfo, $roleId);
			}
			//急救箱
			else if ($cid == 4415) {
				$result = self::addBankCard($uid, $cid, 1);
			}
			//魔法药粉
			else if ($cid == 4515) {
				$result = self::addBankCard($uid, $cid, 2);
			}
			//援助卷轴
			else if ($cid == 3115) {
                $result = self::assistCard($uid, $cid, $cardInfo);
			}
		}
		else if ( $type == 2 ) {
			$result = self::mixScroll($uid, $cid);
		}

		return $result;
	}

	/**
	 * 回血药剂
	 */
	public static function hpCard($uid, $cid, $cardInfo, $roleId)
	{
		if ( $roleId == 0 ) {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		}
		else {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $roleId);
		}
		if (!$userMercenary) {
			return -200;
		}

		if ( $userMercenary['hp'] >= $userMercenary['hp_max'] ) {
			return -238;
		}

		//不同道具，加血量不同
		if ( $cid == 111 ) {
			$addHp = 10;
		}
		else if ( $cid == 212 ) {
			$addHp = 15;
		}
		else if ( $cid == 2112 ) {
			$addHp = 50;
		}
		else if ( $cid == 2212 ) {
			$addHp = 11;
		}
		else if ( $cid == 2811 ) {
			$addHp = 40;
		}
		else if ( $cid == 2911 ) {
			$addHp = 100;
		}

		//不能超过最大值
		if ( ($userMercenary['hp'] + $addHp) > $userMercenary['hp_max'] ) {
			$addHp = $userMercenary['hp_max'] - $userMercenary['hp'];
		}

		$userMercenary['hp'] += $addHp;
		if ( $roleId == 0 ) {
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary);
		}
		else {
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $roleId, $userMercenary);
		}

		if (!$ok) {
			return -200;
		}

		$roleChange = array('id' => $roleId,
							'hp' => $userMercenary['hp']);
		$rolesChange = array($roleChange);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);

		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, 1);

		return 1;
	}

	/**
	 * 回蓝药剂
	 */
	public static function mpCard($uid, $cid, $cardInfo, $roleId)
	{
		if ( $roleId == 0 ) {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		}
		else {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $roleId);
		}
		if (!$userMercenary) {
			return -200;
		}

		if ( $userMercenary['mp'] >= $userMercenary['mp_max'] ) {
			return -238;
		}

		if ( $cid == 1611 ) {
			$addMp = 10;
		}
		else if ( $cid == 1712 ) {
			$addMp = 11;
		}
		else if ( $cid == 1812 ) {
			$addMp = 12;
		}
		else if ( $cid == 1912 ) {
			$addMp = 13;
		}

		if ( ($userMercenary['mp'] + $addMp) > $userMercenary['mp_max'] ) {
			$addMp = $userMercenary['mp_max'] - $userMercenary['mp'];
		}

		$userMercenary['mp'] += $addMp;
		if ( $roleId == 0 ) {
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary);
		}
		else {
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $roleId, $userMercenary);
		}

		if (!$ok) {
			return -200;
		}

		$roleChange = array('id' => $roleId,
							'mp' => $userMercenary['mp']);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $roleChange);

		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, 1);

		return 1;
	}

	/**
	 * 行动力药剂
	 */
	public static function spCard($uid, $cid, $cardInfo)
	{
		$userSp = Hapyfish2_Alchemy_HFC_User::getUserSp($uid);
		if ( $userSp['sp'] >= $userSp['max_sp'] ) {
			return -236;
		}

		$addSp = 0;
		if ( $cid == 515 ) {
			$addSp = 1;
		}
		else if ( $cid == 615 ) {
			$addSp = 5;
		}
		else if ( $cid == 715 ) {
			$addSp = 20;
		}
		else if ( $cid == 815 ) {
			$addSp = $userSp['max_sp'] - $userSp['sp'];
		}

		$ok = Hapyfish2_Alchemy_HFC_User::incUserSp($uid, $addSp);
		if (!$ok) {
			return -200;
		}

		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, 1);

		return 1;
	}

	/**
	 * 经验卷-战斗经验
	 *
	 */
	public static function expCard($uid, $cid, $cardInfo, $roleId)
	{
		if ( $roleId == 0 ) {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		}
		else {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $roleId);
		}
		if (!$userMercenary) {
			return -200;
		}

		//不同道具，加经验不同
		if ( $cid == 1415 ) {
			$addExp = 10;
		}
		else if ( $cid == 1515 ) {
			$addExp = 50;
		}

		$userMercenary['exp'] += $addExp;
		if ( $roleId == 0 ) {
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary);
		}
		else {
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $roleId, $userMercenary, true);
		}
		if (!$ok) {
			return -200;
		}

		//判断是否升级
		$levelUp = Hapyfish2_Alchemy_Bll_Mercenary::checkMercenaryLevelUp($uid, $roleId, $userMercenary);
		if ( $levelUp ) {

		}

		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, 1);

		//佣兵与主角数据
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		$rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
		
		return 1;
	}

	/**
	 * 援助卷轴
	 */
	public static function assistCard($uid, $cid, $cardInfo)
	{
        $assistInfo = Hapyfish2_Alchemy_HFC_User::getUserFightAssistInfo($uid);
        $assistInfo['assist_ext_count'] += 1;
        Hapyfish2_Alchemy_HFC_User::updateUserFightAssistInfo($uid, $assistInfo);
		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, 1);

		return 1;
	}

	/**
	 * 合成术卷轴
	 * @param int $uid
	 * @param int $cid
	 */
	public static function mixScroll($uid, $cid)
	{
		$scrollInfo = Hapyfish2_Alchemy_Cache_Basic::getScrollInfo($cid);
		if (!$scrollInfo) {
			return -200;
		}
		if ($scrollInfo['type']!= 21) {
			return -241;
		}

		/*$userScroll = Hapyfish2_Alchemy_HFC_Scroll::getUserScroll($uid);
		if ( !isset($userScroll[$cid]) || $userScroll[$cid]['count'] < 1 ) {
			return -200;
		}*/

		$mixCid = $scrollInfo['mix_cid'];
		$userMix = Hapyfish2_Alchemy_HFC_Mix::getUserMix($uid);
		if (in_array($mixCid, $userMix)) {
			return -237;
		}

        $ok = Hapyfish2_Alchemy_HFC_Mix::addUserMix($uid, $mixCid);
		if (!$ok) {
			return -200;
		}
		//Hapyfish2_Alchemy_HFC_Scroll::useUserScroll($uid, $cid, 1);

		$newmixs = array($mixCid);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'newmixs', $newmixs);
		
		return 1;
	}
	
	public static function addBankCard($uid, $cid, $type)
	{
		$max = 10000;
		$add = 1000;
		$bank = Hapyfish2_Alchemy_HFC_Goods::getUserPond($uid);
		if($type == 1){
			if($bank['hp'] > 9000){
				return -700;
			}
			$bank['hp'] += 1000;
		}else{
			if($bank['mp'] > 9000){
				return -700;
			}
			$bank['mp'] += 1000;
		}
		Hapyfish2_Alchemy_HFC_Goods::updateUserPond($uid, $bank);
		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, 1);
		$selfInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$selfInfo['hp'] = Hapyfish2_Alchemy_Bll_Fight::autoSupply($uid, $selfInfo, $selfInfo['hp'], 1, 0);
		$selfInfo['mp'] = Hapyfish2_Alchemy_Bll_Fight::autoSupply($uid, $selfInfo, $selfInfo['mp'], 2, 0);
		Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $selfInfo);
		$ids = Hapyfish2_Alchemy_Cache_FightMercenary::getMercenaryIds($uid);
		foreach($ids as $k => $v){
			$mercInfo = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $v);
			if($mercInfo){
				$mercInfo['hp'] = Hapyfish2_Alchemy_Bll_Fight::autoSupply($uid, $mercInfo, $mercInfo['hp'], 1, $v);
        		$mercInfo['mp'] = Hapyfish2_Alchemy_Bll_Fight::autoSupply($uid, $mercInfo, $mercInfo['mp'], 2, $v);
        		Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $mercInfo['mid'], $mercInfo);
			}
		}
        $blood = Hapyfish2_Alchemy_HFC_Goods::getBloodVo($uid);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'bloodBank', $blood);
		return 1;
	}

}