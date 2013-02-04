<?php

class Hapyfish2_Alchemy_Bll_User
{
	public static function getUserInit($uid)
	{
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		$userVO = Hapyfish2_Alchemy_HFC_User::getUserVO($uid);

        $userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		$t = time();

		//酒馆，自宅等级
		$userHomeLevel = Hapyfish2_Alchemy_HFC_User::getUserHomeLevel($uid);
		$userTavernLevel = Hapyfish2_Alchemy_HFC_User::getUserTavernLevelList($uid);
		$userSmithyLevel = Hapyfish2_Alchemy_HFC_User::getUserSmithyLevel($uid);
		
		$viliageInfo = array(HOME_ID => $userHomeLevel, 
							 TAVERN_ID => $userTavernLevel['tavern_level'], 
							 TAVERN_CITY_ID => $userTavernLevel['tavern_city_level'], 
							 SMITHY_ID => $userSmithyLevel, 
							 SMITHY_CITY_ID => $userSmithyLevel);

		//酒馆加酒信息：已加酒次数；是否使用过：1-使用过，0：未使用
		$userWine = Hapyfish2_Alchemy_HFC_Hire::getWine($uid);
		$wineCount = count($userWine['list']);
		$used = $userWine['used'];

		//用户当前满意度
		$userCurStatisfaction = Hapyfish2_Alchemy_HFC_Order::getSatisfaction($uid);

		//占领信息
		$context = Hapyfish2_Util_Context::getDefaultInstance();
        $viewUid = $context->get('uid');
		$occupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($uid);
		$safeTm = 0;
		$ownerUid = 0;
		$ownerName = '';
		$ownerFace = '';
		$ownerEndTime = 0;
		$ownerAwardTime = 0;
		$atkSafeTime = 0;
		$ownerBuildId = 0;
		//$feats = Hapyfish2_Alchemy_HFC_User::getUserFeats($uid);
		if ($occupyInfo) {
		    //15分钟没有战斗完成则自动释放beingattack状态
		    if ($occupyInfo['passive']['status'] == Hapyfish2_Alchemy_Bll_FightOccupy::STATUS_BEING_ATTACKED) {
		        if ($t - (int)$occupyInfo['passive']['beingTm'] >= Hapyfish2_Alchemy_Bll_FightOccupy::AUTO_RELEASE_LOCK_INTERVAL) {
                    $occupyInfo['passive']['status'] = Hapyfish2_Alchemy_Bll_FightOccupy::STATUS_FREE;
                    unset($occupyInfo['passive']['beingUid']);
                    unset($occupyInfo['passive']['beingTm']);
                    Hapyfish2_Alchemy_HFC_FightOccupy::save($uid, $occupyInfo);
		        }
		    }

		    if ($t - (int)$occupyInfo['last_protect_open_tm'] < Hapyfish2_Alchemy_Bll_FightOccupy::PROTECT_INTERVAL) {
		        $safeTm = (int)$occupyInfo['last_protect_open_tm'] + Hapyfish2_Alchemy_Bll_FightOccupy::PROTECT_INTERVAL;
		    }

		    if ($occupyInfo['passive'] && $occupyInfo['passive']['status'] == Hapyfish2_Alchemy_Bll_FightOccupy::STATUS_BEING_OCCUPIED) {//0-free 1-being attack 2-being occupied
		        $ownerEndTime = (int)$occupyInfo['passive']['tm'] + Hapyfish2_Alchemy_Bll_FightOccupy::MAX_OCCUPY_INTERVAL;
		        //占领中
		        if ($ownerEndTime > $t) {
    		        $ownerUid = $occupyInfo['passive']['uid'];
                    if ($ownerUid != $uid) {
                        $ownerData = Hapyfish2_Platform_Bll_User::getUser($ownerUid);
                        $ownerName = $ownerData['name'];
                        $ownerFace = $ownerData['figureurl'];
                    }

                    $ownerBuildId = (int)$occupyInfo['passive']['house'];

                    if ($viewUid == $ownerUid) {
                        if ($occupyInfo['passive']['taxTm']) {
                            $cnt = count($occupyInfo['passive']['taxTm']);
                            $ownerAwardTime = (int)$occupyInfo['passive']['taxTm'][$cnt-1] + Hapyfish2_Alchemy_Bll_FightOccupy::COLLECT_TAX_INTERVAL;
                        }
                        else {
                            $ownerAwardTime = (int)$occupyInfo['passive']['tm'] + Hapyfish2_Alchemy_Bll_FightOccupy::COLLECT_TAX_INTERVAL;
                        }
                    }
		        }
		        //自动释放 已被占领满最长占领时间的 玩家状态
		        else {
                    $occupyInfo['passive']['status'] = Hapyfish2_Alchemy_Bll_FightOccupy::STATUS_FREE;
                    Hapyfish2_Alchemy_HFC_FightOccupy::save($uid, $occupyInfo);

                    $ownerUid = $occupyInfo['passive']['uid'];
                    $tarOccupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($ownerUid);
                    unset($tarOccupyInfo['initiative'][$uid]);
                    Hapyfish2_Alchemy_HFC_FightOccupy::save($ownerUid, $tarOccupyInfo);
		        }
		    }

		    if ($occupyInfo['passive'] && $occupyInfo['passive']['status'] == Hapyfish2_Alchemy_Bll_FightOccupy::STATUS_FREE) {
		        if ($occupyInfo['passive']['uid'] == $viewUid) {
                    $atkSafeTime = (int)$occupyInfo['passive']['tm'] + Hapyfish2_Alchemy_Bll_FightOccupy::ATT_PROTECT_INTERVAL;
		        }
		    }
		}

		//新手引导信息
		$userHelp = Hapyfish2_Alchemy_HFC_Help::get($uid);
		$guideInfo = array('id' => $userHelp['id'],
						   'idx' => $userHelp['idx']);
		if ( $userHelp['status'] == 0 ) {
			$guideInfo['id'] = 0;
		}
		//未开放功能信息
		$funcLocks = Hapyfish2_Alchemy_HFC_Help::getUnlockFunc($uid);
		
		return array(
			'uid' => $userVO['uid'],
			'name' => $user['name'],
			'face' => $user['figureurl'],
			'avatar' => $userVO['avatar'],
			'level' => $userVO['level'],
			'roleLevel' => $userMercenary['level'],
			'exp' => $userVO['exp'],
		    'maxExp' => $userVO['next_level_exp'],
			'coin' => $userVO['coin'],
			'gem' => $userVO['gem'],
		    'sp' => $userVO['sp'],
		    'maxSp' => $userVO['max_sp'],
			'replySpTime' => $userVO['sp_set_time'] + SP_RECOVERY_TIME - $t,
		    'tileX' => $userVO['tile_x_length'],
            'tileZ' => $userVO['tile_z_length'],
			'currentSceneId' => $userVO['cur_scene_id'],
			'satisfaction' => $userCurStatisfaction,
			'viliageInfo' => $viliageInfo,
			'maxRoleNum' => $userVO['maxRoleNum'],
			'maxOrder' => $userVO['maxOrder'],
			'hireHelp' => $wineCount,     //酒馆当前已被帮助次数
			'hireHelpUsed' => $used,  //1使用过   0没用过
			//'feats' => $feats,
			'ownerUid' => $ownerUid,
			'ownerName' => $ownerName,
			'ownerFace' => $ownerFace,
			'safeTime' => $safeTm,
			'ownerEndTime' => $ownerEndTime,
			'ownerAwardTime' => $ownerAwardTime,
			'ownerBuildId' => $ownerBuildId,
			'atkSafeTime' => $atkSafeTime,
			'guideInfo' => $guideInfo,
			'funcLocks' => $funcLocks
		);
	}

	public static function joinUser($uid)
	{
        //report tutorial log,新手引导log
		/*$logger = Hapyfish2_Util_Log::getInstance();
		$userInfo = Hapyfish2_Platform_Cache_User::getUser($uid);
		$joinTime = $userInfo['create_time'];
		$gender = $userInfo['gender'];
		$logger->report('tutorial', array($uid, $help, $joinTime, $gender));*/


		/*$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		if (empty($user)) {
			return false;
		}*/

		$step = 0;

		$initInfo = Hapyfish2_Alchemy_Cache_Basic::getInitUserInfo();
		try {
			//用户基本信息
			$dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
			//uid,coin,gem,sp,max_sp,cur_scene_id
			$userData = array(
				'uid' => $uid,
				'coin' => $initInfo['coin'],
				'gem' => $initInfo['gem'],
				'sp' => $initInfo['sp'],
				'max_sp' => $initInfo['sp'],
				'cur_scene_id' => 1,
				'tile_x_length' => $initInfo['home_size'],
				'tile_z_length' => $initInfo['home_size'],
				'create_time' => time(),
				'last_login_time' => time(),
				'today_login_count' => 1,
				'all_login_count' => 1,
				'login_day_count' => 1
			);
			$dalUser->insert($uid, $userData);
			$step++;

			$step++;
			//图鉴
			$dalIllustrations = Hapyfish2_Alchemy_Dal_Illustrations::getDefaultInstance();
			$dalIllustrations->init($uid, $initInfo['illustration']);
			$step++;
			

			//地板，墙
			$dalFloorWall = Hapyfish2_Alchemy_Dal_FloorWall::getDefaultInstance();
			$dalFloorWall->insert($uid, $initInfo['floor'], $initInfo['wall']);
			$step++;

			//物品
			if (!empty($initInfo['goods'])) {
				$dalGoods = Hapyfish2_Alchemy_Dal_Goods::getDefaultInstance();
				foreach ($initInfo['goods'] as $item) {
					$dalGoods->update($uid, $item[0], $item[1]);
				}
				$step++;
			}

			//材料
			if (!empty($initInfo['stuff'])) {
				$dalStuff = Hapyfish2_Alchemy_Dal_Stuff::getDefaultInstance();
				foreach ($initInfo['stuff'] as $item) {
					$dalStuff->update($uid, $item[0], $item[1]);
				}
				$step++;
			}

			//工作台
			if (!empty($initInfo['furnace'])) {
				$dalFurnace = Hapyfish2_Alchemy_Dal_Furnace::getDefaultInstance();
				$id = 1;
				foreach ($initInfo['furnace'] as $item) {
					if ($item[1] > 0) {
						for($n = 0; $n < $item[1]; $n++) {
							$dalFurnace->init($uid, $id, $item[0]);
							$id++;
						}
					}
				}
				$step++;
			}

			//背包装饰物
			if (!empty($initInfo['decor'])) {
				$dalDecor = Hapyfish2_Alchemy_Dal_Decor::getDefaultInstance();
				foreach ($initInfo['decor'] as $item) {
					$dalDecor->updateInBag($uid, $item[0], $item[1]);
				}
				$step++;
			}

			//合成术
			$dalMix = Hapyfish2_Alchemy_Dal_Mix::getDefaultInstance();
			$mixData = array(
				'uid' => $uid,
				'mix_cids' => $initInfo['mix']
			);
			$dalMix->insert($uid, $mixData);
			$step++;

			//已打开场景
			$dalWorldMap = Hapyfish2_Alchemy_Dal_WorldMap::getDefaultInstance();
			$map_ids = array();
			foreach ($initInfo['open_scene'] as $id) {
				$map_ids[] = array($id, 1, ($id<100?1:0));
			}
			$mapData = array(
				'uid' => $uid,
				'map_ids' => json_encode($map_ids)
			);
			$dalWorldMap->insert($uid, $mapData);
			$step++;

			//佣兵打工，与已打开场景一致
			$dalWork = Hapyfish2_Alchemy_Dal_MercenaryWork::getDefaultInstance();
			foreach ($initInfo['open_scene'] as $id) {
				Hapyfish2_Alchemy_Bll_MercenaryWork::setWorkOpened($uid, $id);
			}
			$step++;
			
			//卷轴
			if (!empty($initInfo['scroll'])) {
				$dalScroll = Hapyfish2_Alchemy_Dal_Scroll::getDefaultInstance();
				foreach ($initInfo['scroll'] as $item) {
					$dalScroll->update($uid, $item[0], $item[1]);
				}
				$step++;
			}
			//装备
			if (!empty($initInfo['equipment'])) {
				foreach ($initInfo['equipment'] as $item) {
					Hapyfish2_Alchemy_HFC_Weapon::addUserWeapon($uid, $item[0], $item[1]);
				}
				$step++;
			}
			try {
				//家里装饰
				if (!empty($initInfo['home_decor'])) {
					$dalDecor = Hapyfish2_Alchemy_Dal_Decor::getDefaultInstance();
					foreach ($initInfo['home_decor'] as $item) {
						$item['uid'] = $uid;
						$dalDecor->insertInScene($uid, $item);
					}
					$step++;
				}
			} catch (Exception $e) {
			}
			//主角站位
			$dalFightCorps = Hapyfish2_Alchemy_Dal_FightCorps::getDefaultInstance();
			$dalFightCorps->insert($uid, array('uid' => $uid, 'matrix' => $initInfo['fight_matrix']));
			$step++;

			//任务
			$dalTaskD = Hapyfish2_Alchemy_Dal_TaskDaily::getDefaultInstance();
			$dalTaskD->init($uid);
			$taskOpen = array();
			$taskPrepare = array();
			$dalTask = Hapyfish2_Alchemy_Dal_TaskOpen::getDefaultInstance();
			$dalTask->init($uid, $taskOpen, $taskPrepare);
			$step++;

			//侵略交互
			$dalOccupy = Hapyfish2_Alchemy_Dal_FightOccupy::getDefaultInstance();
			$dalOccupy->init($uid);
			$step++;

			//游戏中唯一物品
			$dalUnique = Hapyfish2_Alchemy_Dal_UniqueItem::getDefaultInstance();
			$dalUnique->init($uid);
			$step++;

			//初始化佣兵,酒馆位置信息
			$dalHire = Hapyfish2_Alchemy_Dal_Hire::getDefaultInstance();
			$dalHire->init($uid);
			$step++;

			//初始化订单
			$dalOrder = Hapyfish2_Alchemy_Dal_Order::getDefaultInstance();
			$dalOrder->init($uid);
			$step++;

			//初始化剧情，脚本
			$dalStory = Hapyfish2_Alchemy_Dal_Story::getDefaultInstance();
			$dalStory->initStory($uid);
			$step++;

			//初始化剧情，对白
			$dalStory = Hapyfish2_Alchemy_Dal_Story::getDefaultInstance();
			$dalStory->initDialog($uid);
			$step++;

			//未解锁功能
		    $dal= Hapyfish2_Alchemy_Dal_UnlockFunc::getDefaultInstance();
            $dal->init($uid);
			$step++;
            
			//新手引导
		    $dal= Hapyfish2_Alchemy_Dal_Help::getDefaultInstance();
            $dal->init($uid);
			$step++;
			
			//时间礼包
			Hapyfish2_Alchemy_Bll_EventGift::startTimeGift($uid);

		} catch (Exception $e) {
			info_log('[' . $step . ']' . $e->getMessage(), 'alchemy.user.init');
            return false;
		}

		Hapyfish2_Alchemy_Cache_User::setAppUser($uid);

		return true;
	}

    public static function initAvatar($uid, $avatarId)
	{

		$avatar = Hapyfish2_Alchemy_HFC_User::getUserAvatar($uid);
		if ($avatar) {
			return -103;
		}

		$roleList = Hapyfish2_Alchemy_Cache_Basic::getInitRoleList();
		$id = 1;
		foreach ($roleList as $key => $data) {
		    if ($data['avatar'] == $avatarId) {
                $id = $key;
                break;
		    }
		}

		$ok = self::initRole($uid, $id);
		if (!$ok) {
			return -104;
		}

		return 1;
	}

	public static function initRole($uid, $id = 1)
	{
		$info = Hapyfish2_Alchemy_Cache_Basic::getInitRole($id);
		if ($info == null) {
			return false;
		}

		$platInfo = Hapyfish2_Platform_Bll_User::getUser($uid);

		$data = array(
			'uid' => $uid,
			'cid' => $id,
			'gid' => $info['gid'],
			'rp' => $info['rp'],
			'job' => $info['job'],
			'name' => $platInfo['name'],
			'class_name' => $info['class_name'],
			'face_class_name' => $info['face_class_name'],
			's_face_class_name' => $info['s_face_class_name'],
			'scene_player_class' => $info['scene_player_class'],
			'sex' => $info['sex'],
			'element' => mt_rand(1,3),
			'exp' => 0,
			'level' => 1,
			'hp' => $info['hp'],
			'hp_max' => $info['hp'],
			'mp' => $info['mp'],
			'mp_max' => $info['mp'],
			'phy_att' => $info['phy_att'],
			'phy_def' => $info['phy_def'],
			'mag_att' => $info['mag_att'],
			'mag_def' => $info['mag_def'],
			'agility' => $info['agility'],
			'crit' => $info['crit'],
			'dodge' => $info['dodge'],
			'weapon' => '[]',
			'skill' => $info['skill']
		);

		try {
			$dalFightAttribute = Hapyfish2_Alchemy_Dal_FightAttribute::getDefaultInstance();
			$dalFightAttribute->insert($uid, $data);
			Hapyfish2_Alchemy_HFC_User::updateUserAvatar($uid, $info['avatar'], true);
			return true;
		} catch (Exception $e) {
			info_log('[Hapyfish2_Alchemy_Bll_User::initRole]' . $uid . ':' . $e->getMessage(), 'db.err');
		}
		return false;
	}

	public static function checkLevelUp($uid)
	{
		$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
		$userExp = Hapyfish2_Alchemy_HFC_User::getUserExp($uid);

		$nextLevel = $userLevel + 1;

		//test
		if ( $nextLevel > 20 ) {
			return false;
		}

		$nextLevelInfo = Hapyfish2_Alchemy_Cache_Basic::getUserLevelInfo($nextLevel);
		if (!$nextLevelInfo) {
			return false;
		}

		if ( $userExp < $nextLevelInfo['exp'] ) {
			return false;
		}
		$ok = Hapyfish2_Alchemy_HFC_User::updateUserLevel($uid, $nextLevel);
		if ($ok) {
			//升级奖励
			$nowTime = time();
			//最大行动力
			$userSp = Hapyfish2_Alchemy_HFC_User::getUserSp($uid);
			$userSp['max_sp'] = $nextLevelInfo['max_sp'];
			$userSp['sp'] = $userSp['max_sp'];
			$userSp['sp_set_time'] = $nowTime;
			Hapyfish2_Alchemy_HFC_User::updateUserSp($uid, $userSp);
			//coin
			if ($nextLevelInfo['coin'] > 0) {
				Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $nextLevelInfo['coin']);
			}
			//gem
			if ($nextLevelInfo['gem'] > 0) {
				$gemInfo = array('gem' => $nextLevelInfo['gem']);
				Hapyfish2_Alchemy_Bll_Gem::add($uid, $gemInfo);
			}
			//items
			$items = json_decode($nextLevelInfo['levelup_item'], true);
			if (!empty($items)) {
				foreach ( $items as $item ) {
					$itemCid = $item[0];
					$count = $item[1] * $userFurnace['num'];
					Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $item[0], $count);
				}
			}

			//触发任务处理
            $event = array('uid' => $uid, 'data' => $nextLevel);
            Hapyfish2_Alchemy_Bll_TaskMonitor::levelUp($event);

			Hapyfish2_Alchemy_Bll_UserResult::setLevelUp($uid, true);

			return true;
		}

		return false;
	}


	public static function updateLoginTime($uid)
	{
	    $loginInfo = Hapyfish2_Alchemy_HFC_User::getUserLoginInfo($uid);
		if (!$loginInfo) {
			return null;
		}

		$isSaveDb = true;
		$now = time();
		$todayTm = strtotime(date('Ymd'));
		$newLoginInfo = array();
		$newLoginInfo['last_login_time'] = $loginInfo['last_login_time'];
		if ($loginInfo['last_login_time'] < $now) {
		    $newLoginInfo['last_login_time'] = $now;
		}
		$newLoginInfo['today_login_count'] = $loginInfo['today_login_count'] + 1;
		$newLoginInfo['all_login_count'] = $loginInfo['all_login_count'] + 1;
		$newLoginInfo['active_login_count'] = $loginInfo['active_login_count'];
		$newLoginInfo['max_active_login_count'] = $loginInfo['max_active_login_count'];
		$newLoginInfo['login_day_count'] = $loginInfo['login_day_count'];

        //info_log(json_encode($loginInfo), 'aa');

		//new day come
		if ($todayTm > $loginInfo['last_login_time']) {
		    $isSaveDb = true;
		    $newLoginInfo['today_login_count'] = 1;
		    if ($todayTm - $loginInfo['last_login_time'] > 86460) {
		        $newLoginInfo['active_login_count'] = 0;
		    }
		    else {
		        $newLoginInfo['active_login_count'] = $loginInfo['active_login_count'] + 1;
		        if ($newLoginInfo['active_login_count'] > $loginInfo['max_active_login_count']) {
                    $newLoginInfo['max_active_login_count'] = $newLoginInfo['active_login_count'];
		        }
		    }
		    $newLoginInfo['login_day_count'] = $loginInfo['login_day_count'] + 1;
		    //add log
			$logger = Hapyfish2_Util_Log::getInstance();
			$userInfo = Hapyfish2_Platform_Bll_User::getUser($uid);
			$joinTime = $userInfo['create_time'];
			$gender = $userInfo['gender'];
			$userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
			$logger->report('101', array($uid, $joinTime, $gender, $userLevel, $loginInfo['active_login_count'], $loginInfo['login_day_count'], $loginInfo['max_active_login_count']));

			//重置好友家订单信息
			$orderFids = array();
			Hapyfish2_Alchemy_HFC_Order::updateOrderFids($uid, $orderFids);
			$requestList = Hapyfish2_Alchemy_HFC_Order::getRequestList($uid);
			$requestListChg = false;
			foreach ( $requestList as $v => $order ) {
				//好友家订单
				$friendOrder = substr($order['id'], -1, 1);
				if ( $friendOrder == 'f' ) {
					$requestListChg = true;
					unset($requestList[$v]);
				}
			}
			if ( $requestListChg ) {
				Hapyfish2_Alchemy_HFC_Order::updateRequestList($uid, $requestList);
			}

			//重置好友家加酒信息
			$userWine = array('list' => array(), 'used' => 0);
			Hapyfish2_Alchemy_HFC_Hire::updateWine($uid, $userWine);

		    //援助攻击次数重置
            $usrShopLev = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
            $shopLevInfo = Hapyfish2_Alchemy_Cache_Basic::getUserLevelInfo($usrShopLev);
            if ($shopLevInfo) {
                $assistInfo = Hapyfish2_Alchemy_HFC_User::getUserFightAssistInfo($uid);
                $assistInfo['assist_bas_count'] = (int)$shopLevInfo['assistance'] + 10;
                Hapyfish2_Alchemy_HFC_User::updateUserFightAssistInfo($uid, $assistInfo);
            }
		}

		foreach ($newLoginInfo as $key=>$value) {
		    $newLoginInfo[$key] = (int)$value;
		}

        Hapyfish2_Alchemy_HFC_User::updateUserLoginInfo($uid, $newLoginInfo, $isSaveDb);
        return $newLoginInfo;
	}

	public static function clearUser($uid)
	{
		$keys = array();
		
		$mapUserMcKey = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey();
		foreach ( $mapUserMcKey as $mk ) {
			$keys[] = $mk . $uid;
		}
		
		$allMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getAll($uid);
		foreach ( $allMercenary as $v ) {
			$keys[] = 'a:u:mercenary:' . $uid . ':' . $v['mid'];
		}
		
		$allFurnace = Hapyfish2_Alchemy_HFC_Furnace::getAll($uid);
		foreach ( $allFurnace as $m ) {
            $keys[] = 'a:u:furnace:' . $uid . ':' . $m[0];
		}
		
    	$allWordk = Hapyfish2_Alchemy_HFC_MercenaryWork::getAll($uid);
		foreach ( $allWordk as $n ) {
            $keys[] = 'a:u:merwork:' . $uid . ':' . $n['id'];
		}
        
		$allMap = Hapyfish2_Alchemy_Cache_Basic::getMapCopyVerList();
		foreach ( $allMap as $k ) {
    		$keys[] = 'a:u:mapcopy:' . $uid . ':' . $k['map_id'];
		}
    	
		$keys[] = 'a:u:decor:bag:' . $uid;
		$keys[] = 'a:u:decor:scene:' . $uid;
		$keys[] = 'a:u:fightattrib:' . $uid;
		$keys[] = 'a:u:fightoccupy:' . $uid;
		$keys[] = 'a:u:block:' . $uid;
        $keys[] = 'a:u:goods:' . $uid;
    	$keys[] = 'a:u:goods:pond:'.$uid;
    	$keys[] = 'a:u:help:' . $uid;
    	$keys[] = 'a:u:unlockfunc:' . $uid;
    	$keys[] = 'a:u:hirelist:' . $uid;
    	$keys[] = 'a:u:wine:' . $uid;
        $keys[] = 'a:u:mix:' . $uid;
    	$keys[] = 'a:u:orderlist:' . $uid;
    	$keys[] = 'a:u:orderreqlist:' . $uid;
        $keys[] = 'a:u:satisfaction:' . $uid;
        $keys[] = 'a:u:lastrequtime:' . $uid;
    	$keys[] = 'a:u:orderfids:' . $uid;
        $keys[] = 'a:u:scroll:' . $uid;
    	$keys[] = 'a:u:storylist:' . $uid;
    	$keys[] = 'a:u:dialoglist:' . $uid;
    	$keys[] = 'a:u:strthen:' . $uid;
        $keys[] = 'a:u:stuff:' . $uid;
	    $keys[] = 'a:u:taskdly:' . $uid;
	    $keys[] = 'a:u:taskopen:' . $uid;
        $keys[] = 'a:u:scene:' . $uid;
        $keys[] = 'a:u:avatar:' . $uid;
        $keys[] = 'a:u:exp:' . $uid;
        $keys[] = 'a:u:coin:' . $uid;
        $keys[] = 'a:u:gem:' . $uid;
        $keys[] = 'a:u:level:' . $uid;
        $keys[] = 'a:u:sp:' . $uid;
    	$keys[] = 'a:u:scene:' . $uid;
		$keys[] = 'a:u:avatar:' . $uid;
        $keys[] = 'a:u:login:' . $uid;
        $keys[] = 'a:u:fightassist:' . $uid;
        $keys[] = 'a:u:feats:' . $uid;
        $keys[] = 'a:u:maxodrcut:' . $uid;
        $keys[] = 'a:u:maxmercyct:' . $uid;
        $keys[] = 'a:u:tavernlevel:' . $uid;
        $keys[] = 'a:u:smithylevel:' . $uid;
		$keys[] = 'a:u:hometitle:' . $uid;
        $keys[] = 'a:u:homelevel:' . $uid;
        $keys[] = 'a:u:weapon:' . $uid;
    	$keys[] = 'a:u:fight:' . $uid;
    	$keys[] = 'a:u:fight:friendass:' . $uid;
    	$keys[] = 'a:u:fightcorps:' . $uid;
		$keys[] = 'a:u:fightmercenaryids:' . $uid;
        $keys[] = 'a:u:fun:ids:' . $uid;
    	$keys[] = 'a:u:illusts:' . $uid;
        $keys[] = 'a:u:mapcopy:series:' . $uid;
        $keys[] = 'a:u:merwork:ids:' . $uid;  
    	$keys[] = 'a:u:alltask:' . $uid;
    	$keys[] = 'a:u:taskstatus:' . $uid;
    	$keys[] = 'a:u:uniqueitem:' . $uid;
    	$keys[] = 'a:u:worldmap:' . $uid;
        $keys[] = 'a:u:fun:onroom:' . $uid;
        $keys[] = 'a:u:personlist:' . $uid;
        $keys[] = 'a:u:openportal:' . $uid;
        $keys[] = 'a:u:openmine:' . $uid;
        $keys[] = 'a:u:person:' . $uid;
        
	    $localcache = Hapyfish2_Cache_LocalCache::getInstance();
	    $cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
        $hc = Hapyfish2_Cache_HighCache::getInstance();
	    foreach ($keys as $key) {
	        $cache->delete($key);
	        $localcache->delete($key);
	        $hc->delete($key);
	        echo $key;
	        echo '<br/>';
	    }
	    
		try {
			//用户基本信息
			$dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
			//清楚所有相关用户表信息
			$dalUser->clearUser($uid);
		} catch (Exception $e) {
			info_log('[' . $step . ']' . $e->getMessage(), 'alchemy.user.reset');
            return false;
		}
		
	    return true;
	}
	
	public static function resetUser($uid)
	{
		$step = 0;

		$initInfo = Hapyfish2_Alchemy_Cache_Basic::getInitUserInfo();
		
		echo json_encode($initInfo);
		var_dump('<br/>');
		var_dump('<br/>');
		try {
			//用户基本信息
			$dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
			
			//uid,coin,gem,sp,max_sp,cur_scene_id
			$userData = array(
				'uid' => $uid,
				'coin' => $initInfo['coin'],
				'gem' => $initInfo['gem'],
				'sp' => $initInfo['sp'],
				'max_sp' => $initInfo['sp'],
				'cur_scene_id' => 1,
				'tile_x_length' => $initInfo['home_size'],
				'tile_z_length' => $initInfo['home_size'],
				'create_time' => time(),
				'last_login_time' => time(),
				'today_login_count' => 1,
				'all_login_count' => 1,
				'login_day_count' => 1
			);
			$dalUser->insert($uid, $userData);
			$step++;

			//图鉴
			$dalIllustrations = Hapyfish2_Alchemy_Dal_Illustrations::getDefaultInstance();
			$dalIllustrations->init($uid, $initInfo['illustration']);
			$step++;

			//地板，墙
			$dalFloorWall = Hapyfish2_Alchemy_Dal_FloorWall::getDefaultInstance();
			$dalFloorWall->insert($uid, $initInfo['floor'], $initInfo['wall']);
			$step++;

			//物品
			if (!empty($initInfo['goods'])) {
				$dalGoods = Hapyfish2_Alchemy_Dal_Goods::getDefaultInstance();
				foreach ($initInfo['goods'] as $item) {
					$dalGoods->update($uid, $item[0], $item[1]);
				}
				$step++;
			}

			//材料
			if (!empty($initInfo['stuff'])) {
				$dalStuff = Hapyfish2_Alchemy_Dal_Stuff::getDefaultInstance();
				foreach ($initInfo['stuff'] as $item) {
					$dalStuff->update($uid, $item[0], $item[1]);
				}
				$step++;
			}

			//工作台
			if (!empty($initInfo['furnace'])) {
				$dalFurnace = Hapyfish2_Alchemy_Dal_Furnace::getDefaultInstance();
				$id = 1;
				foreach ($initInfo['furnace'] as $item) {
					if ($item[1] > 0) {
						for($n = 0; $n < $item[1]; $n++) {
							$dalFurnace->init($uid, $id, $item[0]);
							$id++;
						}
					}
				}
				$step++;
			}

			//背包装饰物
			if (!empty($initInfo['decor'])) {
				$dalDecor = Hapyfish2_Alchemy_Dal_Decor::getDefaultInstance();
				foreach ($initInfo['decor'] as $item) {
					$dalDecor->updateInBag($uid, $item[0], $item[1]);
				}
				$step++;
			}

			//合成术
			$dalMix = Hapyfish2_Alchemy_Dal_Mix::getDefaultInstance();
			$mixData = array(
				'uid' => $uid,
				'mix_cids' => $initInfo['mix']
			);
			$dalMix->insert($uid, $mixData);
			$step++;

			//已打开场景
			$dalWorldMap = Hapyfish2_Alchemy_Dal_WorldMap::getDefaultInstance();
			$map_ids = array();
			foreach ($initInfo['open_scene'] as $id) {
				$map_ids[] = array($id, 1, ($id<100?1:0));
			}
			$mapData = array(
				'uid' => $uid,
				'map_ids' => json_encode($map_ids)
			);
			$dalWorldMap->insert($uid, $mapData);
			$step++;

			//佣兵打工，与已打开场景一致
			$dalWork = Hapyfish2_Alchemy_Dal_MercenaryWork::getDefaultInstance();
			foreach ($initInfo['open_scene'] as $id) {
				Hapyfish2_Alchemy_Bll_MercenaryWork::setWorkOpened($uid, $id);
			}
			$step++;
			
			//卷轴
			if (!empty($initInfo['scroll'])) {
				$dalScroll = Hapyfish2_Alchemy_Dal_Scroll::getDefaultInstance();
				foreach ($initInfo['scroll'] as $item) {
					$dalScroll->update($uid, $item[0], $item[1]);
				}
				$step++;
			}
			//装备
			if (!empty($initInfo['equipment'])) {
				foreach ($initInfo['equipment'] as $item) {
					Hapyfish2_Alchemy_HFC_Weapon::addUserWeapon($uid, $item[0], $item[1]);
				}
				$step++;
			}
			try {
				//家里装饰
				if (!empty($initInfo['home_decor'])) {
					$dalDecor = Hapyfish2_Alchemy_Dal_Decor::getDefaultInstance();
					foreach ($initInfo['home_decor'] as $item) {
						$item['uid'] = $uid;
						$dalDecor->insertInScene($uid, $item);
					}
					$step++;
				}
			} catch (Exception $e) {
			}
			//主角站位
			$dalFightCorps = Hapyfish2_Alchemy_Dal_FightCorps::getDefaultInstance();
			$dalFightCorps->insert($uid, array('uid' => $uid, 'matrix' => $initInfo['fight_matrix']));
			$step++;

			//任务
			$dalTaskD = Hapyfish2_Alchemy_Dal_TaskDaily::getDefaultInstance();
			$dalTaskD->init($uid);
			$taskOpen = array();
			$taskPrepare = array();
			$dalTask = Hapyfish2_Alchemy_Dal_TaskOpen::getDefaultInstance();
			$dalTask->init($uid, $taskOpen, $taskPrepare);
			$step++;

			//侵略交互
			$dalOccupy = Hapyfish2_Alchemy_Dal_FightOccupy::getDefaultInstance();
			$dalOccupy->init($uid);
			$step++;

			//游戏中唯一物品
			$dalUnique = Hapyfish2_Alchemy_Dal_UniqueItem::getDefaultInstance();
			$dalUnique->init($uid);
			$step++;

			//初始化佣兵,酒馆位置信息
			$dalHire = Hapyfish2_Alchemy_Dal_Hire::getDefaultInstance();
			$dalHire->init($uid);
			$step++;

			//初始化订单
			$dalOrder = Hapyfish2_Alchemy_Dal_Order::getDefaultInstance();
			$dalOrder->init($uid);
			$step++;

			//初始化剧情，脚本
			$dalStory = Hapyfish2_Alchemy_Dal_Story::getDefaultInstance();
			$dalStory->initStory($uid);
			$step++;

			//初始化剧情，对白
			$dalStory = Hapyfish2_Alchemy_Dal_Story::getDefaultInstance();
			$dalStory->initDialog($uid);
			$step++;

			//未解锁功能
		    $dal= Hapyfish2_Alchemy_Dal_UnlockFunc::getDefaultInstance();
            $dal->init($uid);
			$step++;
            
			//新手引导
		    $dal= Hapyfish2_Alchemy_Dal_Help::getDefaultInstance();
            $dal->init($uid);
			$step++;
			
		} catch (Exception $e) {
				var_dump('error');
				var_dump('<br/>');
				var_dump('<br/>');
				var_dump($step);
				var_dump('<br/>');
				var_dump('<br/>');
			info_log('[' . $step . ']' . $e->getMessage(), 'alchemy.user.reset');
			var_dump($e->getMessage());
				var_dump('<br/>');
				var_dump('<br/>');
            return false;
		}
		
		Hapyfish2_Alchemy_Cache_User::setAppUser($uid);

		return true;
	}
	
}