<?php

class Hapyfish2_Alchemy_Bll_BasicInfo
{
	public static function getInitVoData($v = '1.0', $compress = false)
	{
		if (!$compress) {
			return self::restore($v);
		} else {
			return self::restoreCompress($v);
		}
	}
	public static function removeDumpFile($v = '1.0', $compress = false)
	{
	    $file = TEMP_DIR . '/initvo.' . $v . '.cache';
	    if ($compress) {
	        $file .= '.zip';
	    }
	    if (is_file($file)) {
            $rst = @unlink($file);
	    }
	    return $rst;
	}

	public static function dump($v = '1.0', $compress = false)
	{
		$resultInitVo = self::getInitVo();
		$file = TEMP_DIR . '/initvo.' . $v . '.cache';
		$data = json_encode($resultInitVo);
		if ($compress) {
		    file_put_contents($file, $data);
			$data = gzcompress($data, 9);
			$file .= '.zip';
		}

		file_put_contents($file, $data);
		return $data;
	}

	public static function restore($v = '1.0')
	{
		$file = TEMP_DIR . '/initvo.' . $v . '.cache';
		if (is_file($file)) {
			return file_get_contents($file);
		} else {
			return self::dump($v);
		}
	}

	public static function restoreCompress($v = '1.0')
	{
		$file = TEMP_DIR . '/initvo.' . $v . '.cache.zip';
		if (is_file($file)) {
			return file_get_contents($file);
		} else {
			return self::dump($v, true);
		}
	}

	public static function getInitVo()
	{
        $resultInitVo = array();

        $mixList = self::getMixList();
        $goodsList = self::getGoodsList();
        $scrollList = self::getScrollList();
        $stuffList = self::getStuffList();
        $furnaceList = self::getFurnaceList();
        $decorList = self::getDecorList();
        $weaponList = self::getWeaponList();
        $mercenaryCardList = self::getMercenaryCardList();
        $goodsList = array_merge($goodsList, $mercenaryCardList);

        $illustrationsList = self::getIllustrationsList();

        $resultInitVo['itemClass'] = array_merge($goodsList, $scrollList, $stuffList, $furnaceList, $decorList, $weaponList);
        $resultInitVo['mixClass'] = $mixList;
        $resultInitVo['illustrationsClass'] = $illustrationsList;

        $resultInitVo['levelInfos'] = self::getUserLevelList();
        $resultInitVo['roomLevelClass'] = self::getRoomLevelList();
        $resultInitVo['avatarClass'] = self::getAvatarList();
        $resultInitVo['sceneClass'] = self::getSceneList();
        $resultInitVo['npcClass'] = array();

        $resultInitVo['gameData'] = self::getGameData();

        $resultInitVo['worldMapClass'] = self::getWorldMapList();
        $resultInitVo['skillAndItems'] = self::getEffectList();
		//$resultInitVo['monsterClass'] = self::getMonsterList();
		//$resultInitVo['mineClass'] = self::getMineList();
		$resultInitVo['mapcopyClass'] = self::getMapCopyVerList();
		$resultInitVo['propAdj'] = self::getElementRestrict();
		$resultInitVo['jobAdj'] = self::getJobRestrict();

		$resultInitVo['shopItemList'] = self::getShopItemList();
		$resultInitVo['rolePosPrices'] = self::getRolePosPrices();
		$resultInitVo['roleExps'] = self::getRoleExps();
		$resultInitVo['roleUpClass'] = self::getRoleUpClass();
		$resultInitVo['hireRoleClass'] = self::getHireRoleClass();

		$resultInitVo['fixEquip'] = self::getFixEquip();
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		$resultInitVo['vip'] = $vip->getVipStatic();
		$resultInitVo['linePowerClass'] = Hapyfish2_Alchemy_Bll_VipWelfare::initUserSp();
		
        //$resultInitVo = array_merge($resultInitVo, $tempData);
//		$resultInitVo['autoUseList'] = self::getAutoList();
        return $resultInitVo;
	}

	public static function getGameData()
	{
		$info = array();
		$robots = explode(',',BOTFRIEND);
		$info['replySpTime'] = SP_RECOVERY_TIME;
		$info['replySp'] = SP_RECOVERY_SP;
		$info['worldMapBg'] = '';
		$info['shalouCid'] = 2715;
		$info['shalouTime'] = 1800;
		$info['crystalCid'] = 915;
		$info['customerGiveupSp'] = 1;
		$info['customerInTime'] = 60;
		$info['tipsRateBySatisfaction'] = 2;
		//$info['tipsRateBySatisfaction'] = 50;
		$info['homeSceneId'] = 1;
		$info['viliageSceneId'] = 2;
		$info['homeBuildId'] = HOME_ID;  //自宅id
		$info['barBuildIds'] = array(TAVERN_ID, TAVERN_CITY_ID);  //酒馆id
		$info['wineCid'] = 1115;
		$info['needWineNum'] = 1;
		$info['skillUnlock'] = json_decode('[[0,1,0],[15,1,0],[30,1,0],[0,2,10],[0,2,50]]');
		$info['maxBattleRoles'] = 3;
		$info['hpPrice'] = 1;
		$info['mpPrice'] = 1;
		$info['maxHireHelp'] = 3;
		$info['spaddItemIdArray'] = array(515,615,715,815);
		$info['orderRefreshGem'] = 1;
		$info['smithyBuildId'] = array(SMITHY_ID, SMITHY_CITY_ID);     //铁匠铺id
		$info['arenaBuildId'] = array(ARENA_ID);     //竞技场id
		$info['expScrollCid'] = 1515;
		$info['occTime'] = 21600;	//入侵时间
		$info['transportCid'] = 5015;	//传送门道具
		$info['vipCardCids'] = array(6115, 6215, 6315);
		$info['skillMaxLevel'] = 5;		//技能最高等级
		$info['activeSkillTypes'] = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 99);		//主动技能类型
		$info['passiveSkillTypes'] = array(11, 97);	
		$info['robots']	= $robots;//被动技能类型
		$info['helpEnabled'] = 0;
		return $info;
	}

	public static function getFixEquip()
	{
        $resultVo = array('npcFace' => 'face.2.Fm2',
        				  'chat' => '需要帮忙吗？我能让你的装备更强~');
        return $resultVo;
	}

	public static function getInitSpecialUpgrade()
	{
        $resultVo = array();

        $homeLevel = self::getHomeLevelList();
        $tavernLevel = self::getTavernLevelList();
        $smithyLevel = self::getSmithyLevelList();
        $arenaLevel = self::getArenaLevelList();
		$resultVo['SpecialUpgradeVo'] = array_merge($homeLevel, $tavernLevel, $smithyLevel, $arenaLevel);
        $trainingLevel = self::getTrainingLevelList();
		$resultVo['SpecialUpgradeVo'] = array_merge($homeLevel, $tavernLevel, $smithyLevel, $arenaLevel, $trainingLevel);

        return $resultVo;
	}

	public static function getInitRoleUpgrade()
	{
        $resultVo = array();

		$resultVo['RoleUpgradeStarVo'] = self::getRoleLevelList();

        return $resultVo;
	}

	public static function getRolePosPrices()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getMercenaryPositionList();
		foreach ($data as $item) {
			$info[$item['position']] = array($item['level'], $item['coin'], $item['gem']);
		}
		return $info;
	}

	public static function getRoleExps()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getMercenaryLevelList();
		foreach ($data as $item) {
			$info[] = $item['exp'];
		}
		return $info;
	}

	public static function getHireRoleClass()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getTavernLevelList();
		foreach ($data as $item) {
			$id = $item['id'];
			$level = $item['level'];
			$info[$id][$level] = array('refreshPrice' => $item['refresh_price']);
		}
		return $info;
	}

	public static function getRoleUpClass()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getMercenaryGrowClassList();
		foreach ($data as $item) {
			$info[$item['rp']] = array(
							'hp' => $item['hp'],
							'mp' => $item['mp'],
							'sPhyAtk' => $item['phy_att'],
							'sPhyDef' => $item['phy_def'],
							'sMagAtk' => $item['mag_att'],
							'sMagDef' => $item['mag_def'],
							'sSpeed'  => $item['agility']);
		}
		return $info;
	}
	
	public static function getInitMercenaryWork()
	{
        $resultVo = array();

		$resultVo['roleWorkMapVo'] = self::getMercenaryWork();

        return $resultVo;
	}
	
	public static function getHomeLevelList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getHomeLevelList();
		foreach ($data as $item) {
			$info[] = array(
				'id' => HOME_ID,
				'level' => $item['level'],
				'needLevel' => $item['need_level'],
				'needItems' => $item['need_items'],
				'needCoin' => $item['need_coin'],
				'content' => $item['content'],
				'npcClass' => $item['npc_class'],
				'npcChat' => $item['npc_chat']
			);
		}
		return $info;
	}

	public static function getTavernLevelList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getTavernLevelList();
		foreach ($data as $item) {
			$info[] = array(
				'id' => (int)$item['id'],
				'level' => $item['level'],
				'needLevel' => $item['need_level'],
				'needItems' => $item['need_items'],
				'needCoin' => $item['need_coin'],
				'content' => $item['content'],
				'npcClass' => $item['npc_class'],
				'npcChat' => $item['npc_chat']
			);
		}
		return $info;
	}

	public static function getSmithyLevelList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getSmithyLevelList();
		foreach ($data as $item) {
			$info[] = array(
				'id' => SMITHY_ID,
				'level' => $item['level'],
				'needLevel' => $item['need_level'],
				'needItems' => $item['need_items'],
				'needCoin' => $item['need_coin'],
				'content' => $item['content'],
				'npcClass' => $item['npc_class'],
				'npcChat' => $item['npc_chat']
			);
		}
		return $info;
	}

	public static function getArenaLevelList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getArenaLevelList();
		foreach ($data as $item) {
			$info[] = array(
				'id' => ARENA_ID,
				'level' => $item['level'],
				'needLevel' => $item['need_level'],
				'needItems' => $item['need_items'],
				'needCoin' => $item['need_coin'],
				'content' => $item['content'],
				'npcClass' => $item['npc_class'],
				'npcChat' => $item['npc_chat']
			);
		}
		return $info;
	}

	public static function getTrainingLevelList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getTrainingLevelList();
		foreach ($data as $item) {
			$info[] = array(
					'id' => TRAINING_ID,
					'level' => $item['level'],
					'needLevel' => $item['need_level'],
					'needItems' => $item['need_items'],
					'needCoin' => $item['need_coin'],
					'content' => $item['content'],
					'npcClass' => $item['npc_class'],
					'npcChat' => $item['npc_chat']
			);
		}
		return $info;
	}
	public static function getRoleLevelList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getRoleLevelList();
		foreach ($data as $item) {
			$info[] = array(
				'quality' => $item['level'],
				'needLevel' => $item['need_level'],
				'needRoleLevel' => $item['need_role_level'],
				'needCoin' => $item['need_coin'],
				'needItems' => $item['need_items'],
				'npcClass' => $item['npc_class'],
				'npcChat' => $item['npc_chat']
			);
		}
		return $info;
	}

	public static function getMixList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getMixList();
		foreach ($data as $item) {
			$info[] = array(
				'cid' => $item['cid'],
				'furnaceCid' => $item['furnace_cid'],
				'itemCid' => $item['item_cid'],
				'name' => $item['name'],
				'coin' => $item['coin'],
				'gem' => $item['gem'],
				'needs' => $item['needs'],
				'needLevel' => $item['need_level'],
				'sp' => $item['sp'],
				'exp' => $item['exp'],
				'time' => $item['time'],
				'maxTime' => $item['max_time'],
				'probability' => $item['probability'],
				'probabilityInterval' => $item['probability_change_value'],
				'perProbabilityGem' => $item['per_probability_gem']
			);
		}
		return $info;
	}

	public static function getGoodsList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getGoodsList();
		foreach ($data as $item) {
			$temp = array(
				'cid' => $item['cid'],
				'funcId' => $item['func_id'],
				'name' => $item['name'],
				'content' => $item['content'],
				'className' => $item['class_name'],
				'isNew' => $item['is_new'],
				'canBuy' => $item['can_buy'],
				'coin' => $item['buy_coin'],
				'gem' => $item['buy_gem'],
				'discountCoin' => $item['buy_coin_vip'],
				'discountGem' => $item['buy_gem_vip'],
				'sale' => $item['sale_coin'],
				'worth' => $item['worth'],
				'buyLevel' => $item['buy_level'],
				'sort' => (int)$item['sort'],
				'useLevel' => (int)$item['useLevel'],
				'useType' => $item['useType'],
			);
			if ( $item['label'] ) {
				$temp['label'] = $item['label'];
			}
			$info[] = $temp;
		}
		return $info;
	}

	public static function getMercenaryCardList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getMercenaryCardList();
		foreach ($data as $item) {
			$temp = array(
				'cid' => $item['cid'],
				'funcId' => $item['func_id'],
				'name' => $item['name'],
				'content' => $item['content'],
				'className' => $item['class_name'],
				'isNew' => $item['is_new'],
				'canBuy' => $item['can_buy'],
				'coin' => $item['buy_coin'],
				'gem' => $item['buy_gem'],
				'discountCoin' => $item['buy_coin_vip'],
				'discountGem' => $item['buy_gem_vip'],
				'sale' => $item['sale_coin'],
				'worth' => $item['worth'],
				'buyLevel' => $item['buy_level'],
				'sort' => (int)$item['sort'],
				'useLevel' => (int)$item['useLevel'],
				'useType' => '',
			);
			if ( $item['label'] ) {
				$temp['label'] = $item['label'];
			}
			$info[] = $temp;
		}
		return $info;
	}
	
	public static function getScrollList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getScrollList();
		foreach ($data as $item) {
			$jobsAry = explode(',', $item['jobs']);
			$newJobs = array();
			foreach ( $jobsAry as $v ) {
				$newJobs[] = (int)$v;
			}
			$jobs = json_encode($newJobs);

			$propsAry = explode(',', $item['element']);
			$newProps = array();
			foreach ( $propsAry as $k ) {
				$newProps[] = (int)$k;
			}
			$props = json_encode($newProps);

			$array = array(
				'cid' => $item['cid'],
				'mixCid' => $item['mix_cid'],
				'jobs' => $jobs,
				'props' => $props,
				'name' => $item['name'],
				'content' => $item['content'],
				'className' => $item['class_name'],
				'isNew' => $item['is_new'],
				'canBuy' => $item['can_buy'],
				'coin' => $item['buy_coin'],
				'gem' => $item['buy_gem'],
				'sale' => $item['sale_coin'],
				'worth' => $item['worth'],
				'buyLevel' => $item['buy_level'],
				'sort' => (int)$item['sort'],
				'level' => $item['need_level'],
				'lockLevel' => $item['study_level'],
			);

			if ( $item['type'] == 22 ) {
				$array['skillType'] = $item['skill_type'];
				$array['skillLev'] = $item['skill_level'];
				$array['nextLevCid'] = $item['next_level_cid'];
				$array['needs'] = $item['levelup_needs'];
			}
			$info[] = $array;
		}
		return $info;
	}

	public static function getStuffList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getStuffList();
		foreach ($data as $item) {
			$info[] = array(
				'cid' => $item['cid'],
				'name' => $item['name'],
				'content' => $item['content'],
				'className' => $item['class_name'],
				'isNew' => $item['is_new'],
				'canBuy' => $item['can_buy'],
				'coin' => $item['buy_coin'],
				'discountCoin' => $item['buy_coin_vip'],
				'discountGem' => $item['buy_gem_vip'],
				'gem' => $item['buy_gem'],
				'sale' => $item['sale_coin'],
				'worth' => $item['worth'],
				'buyLevel' => $item['buy_level'],
				'sort' => (int)$item['sort']
			);
		}
		return $info;
	}

	public static function getFurnaceList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getFurnaceList();
		foreach ($data as $item) {
			
			$expandNeeds = array('2' => json_decode($item['expand_needs_2'], true),
								 '3' => json_decode($item['expand_needs_3'], true),
								 '4' => json_decode($item['expand_needs_4'], true),
								 '5' => json_decode($item['expand_needs_5'], true),
								 '6' => json_decode($item['expand_needs_6'], true));
			
			$info[] = array(
				'cid' => $item['cid'],
				'sizeX' => $item['size_x'],
				'sizeZ' => $item['size_z'],
				'mixcids' => $item['mix_cids'],
				'types' => $item['mix_type'],
				'name' => $item['name'],
				'content' => $item['content'],
				'className' => $item['class_name'],
				'isNew' => $item['is_new'],
				'canBuy' => $item['can_buy'],
				'coin' => $item['buy_coin'],
				'gem' => $item['buy_gem'],
				'sale' => $item['sale_coin'],
				'worth' => $item['worth'],
				'buyLevel' => $item['buy_level'],
				'sort' => (int)$item['sort'],
				'funrnaceGridInfo' => $expandNeeds
			);
		}
		return $info;
	}

	public static function getDecorList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getDecorList();
		foreach ($data as $item) {
			$info[] = array(
				'cid' => $item['cid'],
				'sizeX' => $item['size_x'],
				'sizeZ' => $item['size_z'],
				'name' => $item['name'],
				'content' => $item['content'],
				'className' => $item['class_name'],
				'isNew' => $item['is_new'],
				'canBuy' => $item['can_buy'],
				'coin' => $item['buy_coin'],
				'gem' => $item['buy_gem'],
				'sale' => $item['sale_coin'],
				'worth' => $item['worth'],
				'buyLevel' => $item['buy_level'],
				'sort' => (int)$item['sort']
			);
		}
		return $info;
	}

	public static function getWeaponList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getWeaponList();
		foreach ($data as $item) {
			$jobsAry = explode(',', $item['jobs']);
			$newJobs = array();
			foreach ( $jobsAry as $v ) {
				$newJobs[] = (int)$v;
			}
			$jobs = json_encode($newJobs);
			$add = json_decode($item['strGrow'], true);
			$strGrow = array(
				'pa' => $add[0],
				'pd' =>$add[1],
				'ma' =>$add[2],
				'md' => $add[3],
				'speed' => $add[4],
				'hp' => $add[5],
				'mp' => $add[6],
				'cri' => $add[7],
				'dod' => $add[8],
				'hitRate' => $add[9],
				'lucky' => $add[10]
			);
			$info[] = array(
				'cid' => $item['cid'],
				'level' => $item['level'],
				'maxWear' => $item['durability'],
				'jobs' => $jobs,
				'pa' => $item['pa'],
				'pd' => $item['pd'],
				'ma' => $item['ma'],
				'md' => $item['md'],
				'speed' => $item['speed'],
				'hp' => $item['hp'],
				'mp' => $item['mp'],
				'cri' => $item['cri'],
				'dod' => $item['dod'],
				'hitRate' => $item['hit'],
				'lucky' => $item['tou'],	
				'name' => $item['name'],
				'content' => $item['content'],
				'className' => $item['class_name'],
				'isNew' => $item['is_new'],
				'canBuy' => $item['can_buy'],
				'coin' => $item['buy_coin'],
				'gem' => $item['buy_gem'],
				'sale' => $item['sale_coin'],
				'worth' => $item['worth'],
				'canFix' => $item['can_repair'],
				'fixLevel' => $item['repair_level'],
				'buyLevel' => $item['buy_level'],
				'sort' => (int)$item['sort'],
				'itemLevel' => $item['itemLevel'],
				'strCost'	=>$item['costCoin'],
				'addAttributeArray'	=> $strGrow
			);
		}
		return $info;
	}

	public static function getIllustrationsList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getIllustrationsList();
		foreach ($data as $item) {
			$type = substr($item['id'], -2, 1);
			$type2 = substr($item['id'], -2);
			$info[] = array(
				'cid' => $item['id'],
				'name' => $item['name'],
				'className' => $item['class_name'],
				'content' => $item['content'],
				'source' => $item['source'],
				'mixCid' => $item['mix_cid'],
				'itemCid' => $item['id'],
				'type' => $type,
				'type2' => $type2
			);
		}
		return $info;
	}

	public static function getUserLevelList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getUserLevelList();
		$lastLevelExp = 0;
		foreach ($data as $item) {
			$info[] = array(
				'level' => $item['level'],
				'maxExp' => $item['exp'] - $lastLevelExp,
				'maxSp' => $item['max_sp'],
				'items' => json_decode($item['items'], true),
				'gem' => $item['gem'],
				'coin' => $item['coin'],
				'title' => $item['title'],
				'titleClassName' => $item['title_class_name'],
				'assistCnt' => (int)$item['assistance']
			);
			$lastLevelExp = $item['exp'];
		}
		return $info;
	}

	public static function getRoomLevelList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getRoomLevelList();
		foreach ($data as $item) {
			$info[] = array(
				'level' => $item['level'],
				'needLevel' => $item['need_level'],
				'tile_x_length' => $item['tile_x_length'],
				'tile_z_length' => $item['tile_z_length'],
				'coin' => $item['coin'],
				'gem' => $item['gem'],
				'items' => $item['items']
			);
		}
		return $info;
	}

	public static function getAvatarList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getAvatarList();
		foreach ($data as $item) {
			$info[] = array(
				'avatarId' => $item['id'],
				'name' => $item['name'],
				'face' => $item['face'],
				'className' => $item['class_name'],
				'type' => $item['type']
			);
		}
		return $info;
	}

	public static function getSceneList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getSceneList();
		foreach ($data as $item) {
			$info[] = array(
				'sceneId' => $item['id'],
				'type' => $item['type'],
				'name' => $item['name'],
				'content' => $item['content'],
				'bg' => $item['bg'],
				'bgSound' => $item['bg_sound'],
				'nodeStr' => $item['node_str'],
				'entrances' => $item['entrances'],
				'numCols' => $item['num_cols'],
				'numRows' => $item['num_rows'],
				'isoStartX' => $item['iso_star_x'],
				'isoStartY' => $item['iso_star_y'],
				'parentSceneId' => $item['parent_sceneId']
			);
		}
		return $info;
	}

	public static function getEffectList()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getEffectList();
		foreach ($data as $item) {
		    $cid = $item['cid'];
			$info[] = array(
				'cid' => $cid,
				'name' => $item['name'],
				'content' => $item['content'],
				'className' => $item['class_name'],
				'target' => (int)$item['target'],
				'range' => (int)$item['range'],
				'area' => (int)$item['area'],
				'dodgeAccept' => (int)$item['dodge_accept'],
				'critAccept' => (int)$item['crit_accept'],
				'critAccept' => (int)$item['crit_accept'],
				'resist' => (int)$item['resist'],
				'needMp' => (int)$item['mp'],
				'effectList' => json_decode($item['effect'], true),
				'displayScript' => json_decode($item['disp_script'], true),
				'aiScript' => json_decode($item['ai_script'], true)

			);
		}
		return $info;
	}

    public static function getMonsterList()
	{
        $info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getMonsterList();
		foreach ($data as $item) {
			$info[] = array(
				'cid' => $item['cid'],
				'name' => $item['name'],
				'className' => $item['class_name'],
				'maxHp' => $item['hp'],
				'size_x' => $item['size_x'],
				'size_z' => $item['size_z'],
				'collisionRange' => $item['collision_range'],
				'conditions' => json_decode($item['award_conditions'], true)
			);
		}
		return $info;
	}

    public static function getMineList()
	{
        $info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getMineList();
		foreach ($data as $item) {
			$info[] = array(
				'cid' => $item['cid'],
				'name' => $item['name'],
				'className' => $item['class_name'],
				'maxHp' => $item['hp'],
				'size_x' => $item['size_x'],
				'size_z' => $item['size_z'],
				'conditions' => json_decode($item['need_conditions'], true)
			);
		}
		return $info;
	}

    public static function getWorldMapList()
	{
        $info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getWorldMapList();
		foreach ($data as $item) {
			$info[] = array(
				'cid' => $item['cid'],
				'sceneId' => $item['enter_scene'],
				'name' => $item['name'],
				'iconClass' => $item['class_name'],
				'sp' => $item['need_sp'],
				'x' => $item['x'],
				'y' => $item['y'],
				'links' => json_decode($item['links'], true),
				'roleConditionLevel' => (int)$item['role_level'],
				'isLock' => $item['is_lock']
			);
		}
		return $info;
	}

	public static function getMapCopyVerList()
	{
	    $info = array();
        $data = Hapyfish2_Alchemy_Cache_Basic::getMapCopyVerList();
        foreach ($data as $key=>$item) {
			$info[(int)$key] = HOST . '/api/mapstatic?id='.$item['fname'].'&ver='.$item['ver'];
		}
		return $info;
	}

	public static function getElementRestrict()
	{
	    $info = array();
        $data = Hapyfish2_Alchemy_Cache_Basic::getFightRestrict();
        $info = json_decode($data['element_pair'], true);
		return $info;
	}

	public static function getJobRestrict()
	{
	    $info = array();
        $data = Hapyfish2_Alchemy_Cache_Basic::getFightRestrict();
        $info = json_decode($data['job_pair'], true);
		return $info;
	}

    public static function getMapStaticData($mapId, $compress = false)
	{
	    $file = TEMP_DIR . '/mapcopy/'. $mapId . '.cache';
		if ($compress) {
			$file .= '.zip';
		}
		if (is_file($file)) {
			return file_get_contents($file);
		}
		else {
			return '';
		}
	}

    public static function getMapCopyData($mapId)
	{
        $info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getMapCopyTranscriptList($mapId);
		$item = Hapyfish2_Alchemy_Cache_Basic::getSceneInfo($mapId);
		$info = array(
				'sceneId' => $item['id'],
				'type' => $item['type'],
				'name' => $item['name'],
				'content' => $item['content'],
				'needLevel' => $item['need_level'],
				'bg' => $item['bg'],
				'bgSound' => $item['bg_sound'],
				'nodeStr' => $item['node_str'],
				'entrances' => json_decode($item['entrances']),
				'numCols' => $item['num_cols'],
				'numRows' => $item['num_rows'],
				'isoStartX' => $item['iso_star_x'],
				'isoStartY' => $item['iso_star_y'],
				'parentSceneId' => $item['parent_sceneId'],
				'floorList' => $data['floorList'],
				'portalList' => $data['portalList'],
				'monsterList' => $data['monsterList'],
				'mineList' => $data['mineList'],
				'decorList' => $data['decorList']
		);
		return $info;
	}

    //任务相关
	public static function getTaskTypeList()
	{
		$taskTypeList = Hapyfish2_Alchemy_Cache_Basic::getTaskTypeList();
		$taskTypeData = array();

		foreach ($taskTypeList as $v) {
			$taskTypeData[] = array(
				'id' => (int)$v['id'],
				'desp' => $v['desp'],
				'is_client_action' => $v['is_client_action']
			);
		}

		return $taskTypeData;
	}

	public static function getTaskConditionList()
	{
		$taskConditionList = Hapyfish2_Alchemy_Cache_Basic::getTaskConditionList();
		$taskConditionData = array();

		foreach ($taskConditionList as $v) {
			$taskConditionData[] = array(
				'id' => (int)$v['id'],
				'desp' => $v['desp'],
				'condition_type' => (int)$v['condition_type'],
				'cid' => (int)$v['cid'],
				'num' => (int)$v['num']
			);
		}

		return $taskConditionData;
	}

	public static function getTaskList()
	{
		$taskList = Hapyfish2_Alchemy_Cache_Basic::getTaskList();
		$taskData = array();

		foreach ($taskList as $v) {
			$taskData[] = array(
				'id' => (int)$v['id'],
				'priority' => (int)$v['priority'],
				'condition_ids' => json_decode($v['condition_ids'], true),
			    'complete_cost' => (int)$v['complete_cost'],
				'title' => $v['title'],
				'foreword' => $v['foreword'],
				'help_desp' => $v['help_desp'],
				'done_desp' => $v['done_desp'],
				'guide' => (int)$v['guide'],
				'story' => (int)$v['story'],
				'awards'	=> json_decode($v['awards'], true)
			);
		}

		return $taskData;
	}

	public static function getShopItemList()
	{
		$itemList = array();

		$goodsList = Hapyfish2_Alchemy_Cache_Basic::getGoodsList();
		foreach ( $goodsList as $goods ) {
			if ( $goods['can_buy'] == 1 ) {
				$itemList[] = $goods['cid'];
			}
		}

		$scrollList = Hapyfish2_Alchemy_Cache_Basic::getScrollList();
		foreach ( $scrollList as $scroll ) {
			if ( $scroll['can_buy'] == 1 ) {
				$itemList[] = $scroll['cid'];
			}
		}

		$stuffList = Hapyfish2_Alchemy_Cache_Basic::getStuffList();
		foreach ( $stuffList as $stuff ) {
			if ( $stuff['can_buy'] == 1 ) {
				$itemList[] = $stuff['cid'];
			}
		}

		$furnaceList = Hapyfish2_Alchemy_Cache_Basic::getFurnaceList();
		foreach ( $furnaceList as $furnace ) {
			if ( $furnace['can_buy'] == 1 ) {
				$itemList[] = $furnace['cid'];
			}
		}

		$decorList = Hapyfish2_Alchemy_Cache_Basic::getDecorList();
		foreach ( $decorList as $decor ) {
			if ( $decor['can_buy'] == 1 ) {
				$itemList[] = $decor['cid'];
			}
		}

		$weaponList = Hapyfish2_Alchemy_Cache_Basic::getWeaponList();
		foreach ( $weaponList as $weapon ) {
			if ( $weapon['can_buy'] == 1 ) {
				$itemList[] = $weapon['cid'];
			}
		}

		return $itemList;
	}

	public static function getMercenaryWork()
	{
		$info = array();
		$data = Hapyfish2_Alchemy_Cache_Basic::getMercenaryWorkList();

		$bgArray = array();
		foreach ($data as $item) {
			if ( !isset($bgArray[$item['bg_id']]) ) {
				$bgArray[$item['bg_id']] = $item['bg_id'];
			}
		}
		
		foreach ( $bgArray as $bgKey => $bg ) {
			$pointArray = array();
			foreach ($data as $m) {
				if ( $m['bg_id'] == $bg ) {
					$pointArray[] = array(
							'id' => $m['id'],
							'name' => $m['name'],
							'iconClass' => $m['icon_class'],
							'x' => $m['x'],
							'y' => $m['y'],
							'sp' => $m['sp'],
							'roleLevel' => $m['role_level'],
							'roleNum' => $m['role_num'],
							'needTime' => $m['need_time']
					);
				}
			}
			$info = array('bg' => $bg,'pointClass' => $pointArray);
		}
		
		return $info;
	}
	
	/*
	
	public static function getAutoList()
	{
		$list = array();
		$goods = Hapyfish2_Alchemy_Cache_Basic::getGoodsList();
		if($goods){
			foreach($goods as $id => $v){
				if($v['auto'] == 1){
					$list[] = $id;
				}
			}
		}
		return $list;
	}
	 */
	
}