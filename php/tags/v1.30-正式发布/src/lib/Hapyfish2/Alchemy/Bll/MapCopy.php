<?php

class Hapyfish2_Alchemy_Bll_MapCopy
{

    private static $_refreshInterval = 3600;//3600;

    //回自己家 or 村庄
    public static function enterHomeOrVila($uid, $mapId)
    {
        //家 or 村庄
        if ($mapId == Hapyfish2_Alchemy_Bll_Scene::$vilaSceneId) {
            $userVo = Hapyfish2_Alchemy_Bll_User::getUserInit($uid);

            //update user currrent scene
            if ($userVo['currentSceneId'] != $mapId) {
                $usrScene = Hapyfish2_Alchemy_HFC_User::getUserScene($uid);
        		$usrScene['cur_scene_id'] = $mapId;
        		Hapyfish2_Alchemy_HFC_User::updateUserScene($uid, $usrScene, true);
            }

            if ($userVo['currentSceneId'] > 100) {
                $mapSeries = substr($userVo['currentSceneId'], 0, -2);
	            $seriesInfo = Hapyfish2_Alchemy_Cache_MapCopy::getMapCopySeriesById($uid, $mapSeries);
	            //重置地图boss 刷新
                if (isset($seriesInfo['bossTm']) && $seriesInfo['bossTm']) {
                    $seriesInfo['bossTm'] = 0;
                    Hapyfish2_Alchemy_Cache_MapCopy::setMapCopySeriesById($uid, $mapSeries, $seriesInfo);
                }
            }

            $sceneVo = array(
    	        'sceneId' => $mapId,
    	        'tm' => 9999999999,
    	        'monsterList' => array(),
    	        'mineList' => array(),
    	        'user' => $userVo
	        );

		    //对白NPC
	        $npcVo = Hapyfish2_Alchemy_Bll_Story::getNpcVo($uid, $mapId);
	        if (!empty($npcVo)) {
	        	$sceneVo['npcList'] = $npcVo;
	        }
        
	        /*//额外NPC数据
	        $userPerson = Hapyfish2_Alchemy_HFC_Person::getPerson($uid);
	        if ( isset($userPerson['add_person'][$mapId]) ) {
	        	$sceneVo['addPerson'] = Hapyfish2_Alchemy_Bll_Person::genPersonVo($userPerson['add_person'][$mapId]);
	        }
	        else {
	        	$sceneVo['addPerson'] = array();
	        }
	        $sceneVo['removePerson'] = $userPerson['remove_person'];*/
	        
			//好友家佣兵信息
			$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
			$roles = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
			$sceneRoles = array();
			foreach ( $roles as $role ) {
				$sceneRoles[] = array('id' => $role['id'],
									  'name' => $role['name'],
									  'className' => $role['scenePlayerClass'],
									  'faceClass' => $role['faceClass'],
									  'sex' => $role['sex'],
									  'profession' => $role['profession']);
			}
			//name,classname,faceclass,sex,job
			$sceneVo['sceneRoles'] = $sceneRoles;
			
	        //第一次进入村子的剧情
	        Hapyfish2_Alchemy_Bll_Story::startStory($uid, 21);

	        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'scene', $sceneVo);
            return 1;
        }
        else {
            $usrScene = Hapyfish2_Alchemy_HFC_User::getUserScene($uid);
            if ($usrScene['cur_scene_id'] > 100) {
                $mapSeries = substr($usrScene['cur_scene_id'], 0, -2);
	            $seriesInfo = Hapyfish2_Alchemy_Cache_MapCopy::getMapCopySeriesById($uid, $mapSeries);
	            //重置地图boss 刷新
                if (isset($seriesInfo['bossTm']) && $seriesInfo['bossTm']) {
                    $seriesInfo['bossTm'] = 0;
                    Hapyfish2_Alchemy_Cache_MapCopy::setMapCopySeriesById($uid, $mapSeries, $seriesInfo);
                }
            }

            return Hapyfish2_Alchemy_Bll_Scene::goHomeScene($uid);
        }
    }

    //进入好友副本（村子）
    public static function enterFriendMap($uid, $fid, $mapId = 2)
	{
        if (!Hapyfish2_Platform_Bll_Friend::isFriend($uid, $fid)) {
            return -105;
        }

	    if (empty($mapId) || $mapId != Hapyfish2_Alchemy_Bll_Scene::$vilaSceneId) {
            $mapId = Hapyfish2_Alchemy_Bll_Scene::$vilaSceneId;
        }
        $userVo = Hapyfish2_Alchemy_Bll_User::getUserInit($fid);

		//酒馆加酒信息：是否已经对该好友加过酒：1-是，0：否
		$friendWine = Hapyfish2_Alchemy_HFC_Hire::getWine($fid);
		if ( in_array($uid, $friendWine['list']) ) {
			$userVo['hireHelpUsed'] = 1;
		}

        $sceneVo = array(
	        'sceneId' => $mapId,
	        'tm' => 9999999999,
	        'monsterList' => array(),
	        'mineList' => array(),
	        'user' => $userVo
	    );

		//好友家佣兵信息
		$homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($fid);
		if (!isset($homeSide[0]['uid'])) {
			$sceneVo['sceneRoles'] = array();
		}
		else {
			$roles = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($fid, $homeSide);
			$sceneRoles = array();
			foreach ( $roles as $role ) {
				$sceneRoles[] = array('id' => $role['id'],
									  'name' => $role['name'],
									  'className' => $role['scenePlayerClass'],
									  'faceClass' => $role['faceClass'],
									  'sex' => $role['sex'],
									  'profession' => $role['profession']);
			}
			//name,classname,faceclass,sex,job
			$sceneVo['sceneRoles'] = $sceneRoles;
		}

	    Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'scene', $sceneVo);

	    //好友家订单
	    $friendOrder = Hapyfish2_Alchemy_Bll_Order::getFriendOrder($uid, $fid);
	    if ( $friendOrder ) {
	    	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'friendOrder', $friendOrder);
	    }
	    
	    //访问好友次数任务
	    $event = array('uid' => $uid, 'data' => $fid);
	    Hapyfish2_Alchemy_Bll_TaskMonitor::visitFriend($event);

	    return 1;
	}

    //进入副本地图
    public static function enterMap($uid, $mapId, $portalId=null, $storyId=null, $transport=null)
	{
	    if (strlen($mapId)<3) {
	        return -301;
	    }

	    //basic map copy info
    	$basMapData = Hapyfish2_Alchemy_Cache_Basic::getMapCopyTranscriptList($mapId);
    	if (!$basMapData) {
            return -301;
    	}

	    //get current map copy info
	    $curMapCopy = Hapyfish2_Alchemy_HFC_MapCopy::getInfo($uid, $mapId);
	    //已解锁门列表
	    $openPortal = Hapyfish2_Alchemy_HFC_MapCopy::getOpenPortal($uid);
	    
    	//剧情直接进入场景，不做portal check和sp扣除
	    if ( $storyId ) {
    	    //当前剧情check TODO::
			$userStory = Hapyfish2_Alchemy_HFC_Story::getStory($uid);
			if ( !isset($userStory[$storyId]) ) {
				return -200;
			}
			if ( $userStory[$storyId] == 1 ) {
				return -200;
			}
    	    $userStory[$storyId] = 1;
    	    $storyArray = array('list' => json_encode($userStory));
			Hapyfish2_Alchemy_HFC_Story::updateStory($uid, $storyArray);

    	    //get current map id
    	    $userVo = Hapyfish2_Alchemy_Bll_User::getUserInit($uid);
    	    $curMapId = $userVo['currentSceneId'];
    	    $mapSeries = substr($mapId, 0, -2);
    	    $seriesInfo = Hapyfish2_Alchemy_Cache_MapCopy::getMapCopySeriesById($uid, $mapSeries);
	    }//通过传送门进入场景
	    else if ( $transport ) {
    	    $mapSeries = substr($mapId, 0, -2);
    	    $seriesInfo = Hapyfish2_Alchemy_Cache_MapCopy::getMapCopySeriesById($uid, $mapSeries);
    	    
		    //已记录副本列表
		    $openTransport = Hapyfish2_Alchemy_HFC_MapCopy::getOpenTransport($uid);
		    if (!in_array($mapId, $openTransport)) {
		    	return -333;
		    }
		    
			$gameData = Hapyfish2_Alchemy_Bll_BasicInfo::getGameData();
			$needTransportCid = $gameData['transportCid'];
			
			//传送门静态信息
			$transportBasic = Hapyfish2_Alchemy_Cache_Basic::getTransport($mapId);
			if (!in_array($mapId, $openTransport)) {
				return -334;
			}
			$needTransportCount = $transportBasic['cost_num'];
			if ( $needTransportCount > 0 ) {
	            $userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
	            if ( !isset($userGoods[$needTransportCid]) || $userGoods[$needTransportCid]['count'] < $needTransportCount ) {
	            	return -335;
	            }
			    Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $needTransportCid, $needTransportCount);
			}
	    }
	    else {//正常进入场景
    	    //boss被消灭 强制回村
            /*$mapSeries = substr($mapId, 0, -2);
    	    $seriesInfo = Hapyfish2_Alchemy_Cache_MapCopy::getMapCopySeriesById($uid, $mapSeries);
        	if ($seriesInfo && isset($seriesInfo['bossTm']) && $seriesInfo['bossTm']) {
        	    $usrScene = Hapyfish2_Alchemy_HFC_User::getUserScene($uid);
        		$usrScene['cur_scene_id'] = $mapId;
        		Hapyfish2_Alchemy_HFC_User::updateUserScene($uid, $usrScene);
                return self::enterHomeOrVila($uid, Hapyfish2_Alchemy_Bll_Scene::$vilaSceneId);
        	}*/
    	    $mapSeries = substr($mapId, 0, -2);
    	    $seriesInfo = Hapyfish2_Alchemy_Cache_MapCopy::getMapCopySeriesById($uid, $mapSeries);

    	    //check enter conditions  [ story id / level ]
            if ($basMapData['condition']) {
                foreach ($basMapData['condition'] as $condi) {
                    if (isset($condi['story'])) {
                        $lstStory = Hapyfish2_Alchemy_HFC_Story::getStory($uid);
                        if ($lstStory) {
                            $aryStory = explode(',', $lstStory);
                            if (!in_array($condi['story'], $aryStory)) {
                                return -309;
                            }
                        }
                        else {
                            return -309;
                        }
                    }
                    /*//第一次进入副本需要消耗物品判断
                    if (isset($condi['item'])) {
                    	if (!$curMapCopy) {
                    		$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
                    		$needCid = $condi['item'];
                    		if ( !isset($userGoods[$needCid]) || $userGoods[$needCid]['count'] < 1 ) {
                    			return -309;
                    		}
                    		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $needCid, 1);
                    	}
                    }*/
                    //进入副本需要出征佣兵等级
                    if (isset($condi['roleLevel'])) {
                    	$homeSide = Hapyfish2_Alchemy_Bll_FightCorps::getHomeSideUnitList($uid);
                    	foreach ( $homeSide as $data ) {
                    		if ( $data['level'] < $condi['roleLevel'] ) {
                    			return -309;
                    		}
                    	}
                    }
                }
            }

    	    //get current map id
    	    $userVo = Hapyfish2_Alchemy_Bll_User::getUserInit($uid);
    	    $curMapId = $userVo['currentSceneId'];
	    
        	//can not direct jump check validation
        	if (!$basMapData['jump']) {
        	    //current map id check
            	if ($curMapId != $mapId) {
            	    //check can enter next map
            	    $validEnter = false;
            	    $basCurMap = Hapyfish2_Alchemy_Cache_Basic::getMapCopyTranscriptList($curMapId);
            	    $portalList = $basCurMap['portalList'];
            	    foreach ($portalList as $pid=>$data) {
                        if ($data['tar'] == $mapId) {
                            $validEnter = true;
                            break;
                        }
            	    }
            	    if (!$validEnter) {
                        return -302;
            	    }
            	    if ( !$portalId ) {
            	    	return -307;
            	    }
        	    }

        	    //portal id check
            	if ($portalId) {
                    //id,cid,x,z,mirror,tar
                    if (!isset($portalList[$portalId])) {
                        return -307;
                        //return Hapyfish2_Alchemy_Bll_UserResult::Error('mapcopy_invalid_jump_map');
                    }
                    if ($portalList[$portalId]['tar'] != $mapId) {
                        return -308;
                        //return Hapyfish2_Alchemy_Bll_UserResult::Error('mapcopy_invalid_jump_map2');
                    }
            	
                    //第一次打开门需要消耗物品
                    if ( $portalList[$portalId]['lock'] == 1 ) {
                    	$needCid = $portalList[$portalId]['needItem'];
                    	//是否已解锁
                    	if (!in_array($portalId, $openPortal)) {
                    		$userGoods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
                    		if ( !isset($userGoods[$needCid]) || $userGoods[$needCid]['count'] < 1 ) {
                    			return -309;
                    		}
                    		Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $needCid, 1);
                    		
                    		//添加解锁门信息
                    		Hapyfish2_Alchemy_HFC_MapCopy::addOpenPortal($uid, $portalId);
                    		$openPortal[] = $portalId;
                    	}
                    }
                }
        	}
        	//can directly jump entance
        	else {
        	    //check sp enough
                if ($curMapId != $mapId) {
                    if (substr($mapId, 0, -2) != substr($curMapId, 0, -2)) {
                        if (!Hapyfish2_Alchemy_Bll_WorldMap::costWorldMapEnterSp($uid, $mapId)) {
                            return -208;//sp not enough
                        }
                    }
                }
        	}
        	
            //添加进入副本记录
            Hapyfish2_Alchemy_HFC_MapCopy::addOpenTransport($uid, $mapId);
    	}


	    $nowTm = time();
	    //update current user scene
        if ($curMapId != $mapId) {
            $usrScene = Hapyfish2_Alchemy_HFC_User::getUserScene($uid);
    		$usrScene['cur_scene_id'] = $mapId;
    		Hapyfish2_Alchemy_HFC_User::updateUserScene($uid, $usrScene, true);
        }

	    $needRefresh = false;
	    //first time init seriesinfo
	    if (!$seriesInfo) {
	        $seriesInfo = array('refreshTm'=>$nowTm, 'bossTm'=>0);
	        Hapyfish2_Alchemy_Cache_MapCopy::setMapCopySeriesById($uid, $mapSeries, $seriesInfo);
	        $needRefresh = true;
	        Hapyfish2_Alchemy_Bll_WorldMap::setWorldMapEntered($uid, $mapId);
	    }
	    else {
	        //if ($seriesInfo['refreshTm'] + self::$_refreshInterval < $nowTm || $seriesInfo['bossTm']) {
	        if ($seriesInfo['refreshTm'] + self::$_refreshInterval < $nowTm) {
	            $seriesInfo['refreshTm'] = $nowTm;
	            $seriesInfo['bossTm'] = 0;
	            Hapyfish2_Alchemy_Cache_MapCopy::setMapCopySeriesById($uid, $mapSeries, $seriesInfo);
                $needRefresh = true;
	        }
	    }

	    //basic map copy ver info
    	$basMapVer = Hapyfish2_Alchemy_Cache_Basic::getMapCopyVerInfo($mapId);

	    //get current map copy info
	    $isFirst = false;
	    if (!$curMapCopy || $curMapCopy['map_ver'] != $basMapVer['ver']) {
	        if (!$curMapCopy) {
	            $isFirst = true;
	        }
	        $needRefresh = true;
	    }
        else {
    	    //current map copy need refresh
    	    if ($curMapCopy['enter_time'] < $seriesInfo['refreshTm']) {
    	        $needRefresh = true;
    	    }
        }

	    //refresh this map
	    //if ($needRefresh && $seriesInfo['bossTm'] == 0) {
	    if ($needRefresh) {
    	    $basMine = Hapyfish2_Alchemy_Cache_Basic::getMineList();

    	    $monster = array();
	        $mine = array();

	        //id,cid,x,z,fr_x,fr_z,per,detail - monster
            foreach ($basMapData['monsterList'] as $data) {
                $dispRate = (((int)$data['per'] > 100) ? 100 : (int)$data['per']);
                $randNum = mt_rand(1, 100);
                if ($randNum<=$dispRate) {
                    //$monsterMatrix = array();//$data['matrix']
                    $matrixDetail = (int)$data['detail'];
                    $matrixDetail = empty($matrixDetail) ? 1 : $matrixDetail;
                    $monster[(int)$data['id']] = array((int)$data['cid'], 1, $matrixDetail);
                }
            }
            //id,cid,x,z,per - mine
            foreach ($basMapData['mineList'] as $data) {
                $mine[(int)$data['id']] = array((int)$data['cid'], (int)$basMine[$data['cid']]['hp']);
            }
            //save
            $curMapCopy = array(
                'uid' => $uid,
                'map_id' => $mapId,
                'map_ver' => $basMapVer['ver'],
                'enter_time' => $nowTm,
                'data' => array('monster' => $monster, 'mine' => $mine)
            );
            Hapyfish2_Alchemy_HFC_MapCopy::updateInfo($uid, $mapId, $curMapCopy);
            info_log("$uid - map:$mapId refreshed:$nowTm", 'Bll_MapCopy');
	    }
	    else {
            $monster = $curMapCopy['data']['monster'];
            $mine = $curMapCopy['data']['mine'];
            //info_log("$uid - map:$mapId enter:$nowTm", 'Bll_MapCopy');
	    }

	    $monsterVo = array();
	    $mineVo = array();
	    $basMatrix = Hapyfish2_Alchemy_Cache_Basic::getFightMonsterMatixList();
	    foreach ($monster as $key=>$data) {
	        $lev = 1;
	        if (isset($basMatrix[$data[2]])) {
                $lev = $basMatrix[$data[2]]['lev'];
	        }
            $monsterVo[] = array('id' => $key, 'currentHp' => $data[1], 'level' =>$lev);
	    }
	    foreach ($mine as $key=>$data) {
            $mineVo[] = array('id' => $key, 'currentHp' => $data[1]);
	    }

	    $refreshRemainTime = $seriesInfo['refreshTm']+self::$_refreshInterval;

	    $sceneVo = array(
	        'sceneId' => $mapId,
	        'tm' => $refreshRemainTime,
	        'monsterList' => $monsterVo,
	        'mineList' => $mineVo,
	        'user' => $userVo
	    );
	    
	    //已解锁门数据列表
	    $sceneVo['openPortalList'] = $openPortal;

        //触发任务处理
        //if ($isFirst) {
        $event = array('uid' => $uid, 'data' => array($mapId=>1));
        Hapyfish2_Alchemy_Bll_TaskMonitor::firstEnterMap($event);
        //}
	    
        //触发剧情处理
        Hapyfish2_Alchemy_Bll_Story::startStoryByMapId($uid, $mapId);

	    //对白NPC
        $npcVo = Hapyfish2_Alchemy_Bll_Story::getNpcVo($uid, $mapId);
        if (!empty($npcVo)) {
        	//进场景时候刷新npclist，则去除原有自动化部分的 npclistchange信息
			Hapyfish2_Alchemy_Bll_UserResult::removeField($uid, 'npcListChange');
        	$sceneVo['npcList'] = $npcVo;
        }

        /*//额外NPC数据
        $userPerson = Hapyfish2_Alchemy_HFC_Person::getPerson($uid);
        if ( isset($userPerson['add_person'][$mapId]) ) {
        	$sceneVo['addPerson'] = Hapyfish2_Alchemy_Bll_Person::genPersonVo($userPerson['add_person'][$mapId]);
        }
        else {
        	$sceneVo['addPerson'] = array();
        }
        $sceneVo['removePerson'] = $userPerson['remove_person'];*/
        
	    //$resultVo = array('scene' => $sceneVo);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'scene', $sceneVo);

        //统计分析log
        $userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
        $selfInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
        $log = Hapyfish2_Util_Log::getInstance();
        $log->report('220', array($uid, $userLevel, $selfInfo['level'], $mapId));
        return 1;
	}

    //击打矿
    public static function hitMine($uid, $id)
    {
        $userSceneInfo = Hapyfish2_Alchemy_HFC_User::getUserScene($uid);
        $mapId = $userSceneInfo['cur_scene_id'];
        if (!$mapId || $mapId == Hapyfish2_Alchemy_Bll_Scene::$homeSceneId) {
            return -303;
            //return Hapyfish2_Alchemy_Bll_UserResult::Error('mapcopy_not_in_map');
        }

        //get current map copy info
        $curMapCopy = Hapyfish2_Alchemy_HFC_MapCopy::getInfo($uid, $mapId);
        if (!$curMapCopy) {
            return -301;
            //return Hapyfish2_Alchemy_Bll_UserResult::Error('mapcopy_not_found');
        }

        //mine not fouond
        $mine = $curMapCopy['data']['mine'];
        if (!isset($mine[$id])) {
            return -304;
            //return Hapyfish2_Alchemy_Bll_UserResult::Error('mapcopy_mine_not_found');
        }

        //mine had dead
        if ($mine[$id][1] <= 0) {
            return -304;
            //return Hapyfish2_Alchemy_Bll_UserResult::Error('mapcopy_mine_had_dead');
        }

        $cid = $mine[$id][0];
        //basic mine
        $basMine = Hapyfish2_Alchemy_Cache_Basic::getMineList();

        //need condition
        $condition = json_decode($basMine[$cid]['need_conditions'], true);
        $rst = self::_costCondition($uid, $condition, $removeResult);
        if ($rst) {
            return $rst;
            //return Hapyfish2_Alchemy_Bll_UserResult::Error($rst);
        }

        //deal mine hp
        $newHp = ($mine[$id][1] - 1) > 0 ? ($mine[$id][1] - 1) : 0;
        $curMapCopy['data']['mine'][$id][1] = $newHp;
        $ok = Hapyfish2_Alchemy_HFC_MapCopy::updateInfo($uid, $mapId, $curMapCopy);
        if (!$ok) {
            return -305;
            //return Hapyfish2_Alchemy_Bll_UserResult::Error('mapcopy_hit_mine_failed');
        }
        
        //award condition
        $awdCondition = json_decode($basMine[$cid]['award_conditions'], true);
        
        $userOpenMine = Hapyfish2_Alchemy_HFC_MapCopy::getOpenMine($uid);
        if ( !in_array($cid, $userOpenMine) ) {
        	if ( $basMine[$cid]['first_award_conditions'] != '[]' ) {
        		$firstAwdCondition = json_decode($basMine[$cid]['first_award_conditions'], true);
        		$awdCondition = array_merge($awdCondition, $firstAwdCondition);
        		Hapyfish2_Alchemy_HFC_MapCopy::addOpenMine($uid, $cid);
        	}
        }
        
        $rst = self::awardCondition($uid, $awdCondition, $addResult, 2);
        if ($rst) {
            return -306;
            //return Hapyfish2_Alchemy_Bll_UserResult::Error($rst);
        }

        //trigger event
        if ($newHp == 0) {

        }

        //触发任务处理
        $event = array('uid' => $uid, 'data' => $addResult['gain']);
        $event1 = array('uid' => $uid, 'data' => 1);
        Hapyfish2_Alchemy_Bll_TaskMonitor::hitMineGain($event);
        Hapyfish2_Alchemy_Bll_TaskMonitor::exgStuffGain($event);
        Hapyfish2_Alchemy_Bll_TaskMonitor::hitRandomThing($event1);
        return 1;
    }

    //击打怪
    public static function beatMonster($uid, $mapId, $id)
    {
        $curMapCopy = Hapyfish2_Alchemy_HFC_MapCopy::getInfo($uid, $mapId);
    	$monster = $curMapCopy['data']['monster'];
    	if (!isset($monster[$id])) {
    	    info_log('beatMonster:monster id not found', 'Bll-MapCopy');
    	    return 0;
    	}

    	//monster cid
        $cid = $monster[$id][0];

        //beat monster
        $curMapCopy['data']['monster'][$id][1] = 0;
        $ok = Hapyfish2_Alchemy_HFC_MapCopy::updateInfo($uid, $mapId, $curMapCopy);
        if (!$ok) {
            info_log('beatMonster:map monster beat update failed', 'Bll-MapCopy');
    	    return 0;
        }

        $basMapData = Hapyfish2_Alchemy_Cache_Basic::getMapCopyTranscriptList($mapId);
        if (isset($basMapData['monsterList'][$id]['end']) && $basMapData['monsterList'][$id]['end']) {
            //set map series cleared
            Hapyfish2_Alchemy_Bll_WorldMap::setWorldMapCleared($uid, $mapId);

            //refresh map
            $mapSeries = substr($mapId, 0, -2);
            $seriesInfo = Hapyfish2_Alchemy_Cache_MapCopy::getMapCopySeriesById($uid, $mapSeries);
            $seriesInfo['bossTm'] = time();
            Hapyfish2_Alchemy_Cache_MapCopy::setMapCopySeriesById($uid, $mapSeries, $seriesInfo);
            //return to the map entrance / vila
            //$mapEntrance = $mapSeries.'01';
            $mapEntrance = Hapyfish2_Alchemy_Bll_Scene::$vilaSceneId;
            Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'nextScene', $mapEntrance);
        }

        Hapyfish2_Alchemy_Bll_Story::startStoryByMonsterId($uid, $cid, 1);
    	return 1;
    }

    //取敌方战斗单位列表
    public static function getEnemySideUnitList($uid, $id)
	{
	    //get current mapcopy id
        $usrScene = Hapyfish2_Alchemy_HFC_User::getUserScene($uid);
    	$mapId = $usrScene['cur_scene_id'];

    	$curMapCopy = Hapyfish2_Alchemy_HFC_MapCopy::getInfo($uid, $mapId);
    	$monster = $curMapCopy['data']['monster'];
    	if (!isset($monster[$id])) {
    	    info_log('getEnemySideUnitList:monster id not found:'.$id, 'Bll-MapCopy');
    	    return null;
    	}

    	//basic map copy info
    	$basMapData = Hapyfish2_Alchemy_Cache_Basic::getMapCopyTranscriptList($mapId);
    	$basMonsterMatrix = Hapyfish2_Alchemy_Cache_Basic::getFightMonsterMatixList();
    	$detailId = (int)$basMapData['monsterList'][$id]['detail'];
    	$detailId = empty($detailId) ? 1 : $detailId;
    	//$detail = json_decode('{"num":4,"gp":[[171,70],[271,30]]}', true);
    	//$detail = $basMapData['monsterList'][$id]['detail'];
	    if (!isset($basMonsterMatrix[$detailId])) {
    	    info_log('getEnemySideUnitList:base monster id fight detail info not found:'.$id, 'Bll-MapCopy');
    	    return null;
    	}
    	$detail = json_decode($basMonsterMatrix[$detailId]['matrix'], true);
    	if (!$detail) {
    	    info_log('getEnemySideUnitList:base monster id fight detail not found:'.$id, 'Bll-MapCopy');
    	    return null;
    	}

    	//basic monster info
        $basMonster = Hapyfish2_Alchemy_Cache_Basic::getMonsterList();

    	$posMonster = array();
    	/*$posMonster = array(
	        '4'=>array('id' => 104, 'cid'=>701),
	    	'5'=>array('id' => 105, 'cid'=>702),
	    	'6'=>array('id' => 101, 'cid'=>701),
	    	'7'=>array('id' => 102, 'cid'=>702),
	    	'8'=>array('id' => 103, 'cid'=>701)
	    );*/
    	//{"6":1171,"7":1171,"8":1171,"4":171}    boss固定时：6，7，8号位置有怪1171。4号位置有怪171
        //{"num":5,"gp":[[171,70],[1171,30]]}     随机时：最大怪数 5 ， 怪id 171出现概率70%，怪id 1171出现概率30%
        //random matrix pos
    	if (isset($detail['num']) && isset($detail['gp'])) {
    	    if ((int)$detail['num'] > 9) {
    	        info_log('getEnemySideUnitList:monster num overflow, max monster num can be 9', 'Bll-MapCopy');
    	        return null;
    	    }
    	    $min = $max = 1;
    	    if (isset($detail['num'])) {
    	        $max = (int)$detail['num'];
    	    }
    	    if (isset($detail['min'])) {
    	        $min = (int)$detail['min'];
    	    }
    	    //random monster count
            $maxNum = mt_rand($min, $max);
            $selMonster = array();
            $idx = 1;
            if ($maxNum > 0) {
                //random monster cid
                $aryRnd = array();
                foreach ($detail['gp'] as $data) {
                    for ($i=0; $i<(int)$data[1]; $i++) {
                        $aryRnd[] = (int)$data[0];
                    }
                }
                $rndKey = array_rand($aryRnd, $maxNum);
                if (!is_array($rndKey)) {
                    $tmp = $rndKey;
                    $rndKey = array($tmp);
                }

                foreach ($rndKey as $key) {
                    $cid = $aryRnd[$key];
                    $selMonster[$idx] = $basMonster[$cid];
                    $idx ++;
                }
            }

            //map meet monster
            //$cid = $basMapData['monsterList'][$id]['cid'];
            //$selMonster[$idx] = $basMonster[$cid];

            //generate fight matrix position
            $aryPos = self::_arrangeEnemyFightPosAi($selMonster);

            if (count($aryPos) != count($selMonster)) {
                info_log('getEnemySideUnitList:monster matrix gen failed', 'Bll-MapCopy');
    	        return null;
            }
            foreach ($aryPos as $pos=>$id) {
                $posMonster[$pos] = array('id'=>$id, 'cid'=>$selMonster[$id]['cid']);
            }

    	}
    	//fix matrix pos
    	else {
    	    $id = 1;
            foreach ($detail as $pos=>$cid) {
                $posMonster[$pos] = array('id'=>$id, 'cid'=>$cid);
                $id ++;
            }
    	}

        $enemySideInfo = array();

        //array $data(id,matrix_pos,job,level,level,hp,hp_max,mp,mp_max,phy_att,phy_def,mag_att,mag_def,agility,crit,dodge,size_x,size_y,size_z,is_boss,weapon,skill,award_conditions
        foreach ($posMonster as $pos=>$data) {
            $enemyInfo = array();
            $monsterInfo = $basMonster[$data['cid']];
            if ($monsterInfo) {
                $monsterInfo['id'] = (int)$data['id'];
                $monsterInfo['matrix_pos'] = (int)$pos;
                $monsterInfo['hp_max'] = (int)$monsterInfo['hp'];
                $monsterInfo['mp_max'] = (int)$monsterInfo['mp'];
                $monsterInfo['skill'] = json_decode($monsterInfo['skill'], true);
                $monsterInfo['weapon'] = json_decode($monsterInfo['weapon'], true);
                $monsterInfo['award_conditions'] = self::_preCalcAwardCondition($monsterInfo['award_conditions']);
                $monsterInfo['first_award_conditions'] = self::_preCalcAwardCondition($monsterInfo['first_award_conditions']);
                unset($monsterInfo['content']);
                unset($monsterInfo['avatar_class_name']);

                //add weapon prop to attribute prop
                Hapyfish2_Alchemy_Bll_Fight::addWeaponProp($monsterInfo);
                $enemySideInfo[(int)$pos] = $monsterInfo;
            }
        }

	    return $enemySideInfo;
	}

	public static function genEnemyRolesVo($enemySide)
	{
	    $roleList = array();
        foreach ($enemySide as $data) {
            $role = array(
                'id' => $data['id'],
                'name' => $data['name'],
                'sex' => 1,
                'label' => '',
                'className' => $data['class_name'],
                'faceClass' => $data['face_class_name'],
                'sFaceClass' => $data['s_face_class_name'],
                'pos' => (int)$data['matrix_pos'],
                'sizeX' => (int)$data['size_x'],
                'sizeZ' => (int)$data['size_z'],
                'profession' => (int)$data['job'],
                'prop' => (int)$data['element'],
                'hp' => (int)$data['hp'],
                'maxHp' => (int)$data['hp_max'],
                'mp' => (int)$data['mp'],
                'maxMp' => (int)$data['mp_max'],
                'speed' => (int)$data['agility'],
                'phyAtk' => (int)$data['phy_att'],
                'phyDef' => (int)$data['phy_def'],
                'magAtk' => (int)$data['mag_att'],
                'magDef' => (int)$data['mag_def'],
                'dodge' => (int)$data['dodge'],
                'baseDodge' => (int)$data['dodge'],
                'crit' => (int)$data['crit'],
                'baseCrit' => (int)$data['crit'],
                'skills' => $data['skill'],
                'items' => $data['award_conditions'],
                'aiScriptId' => array(),
                'statusList' => array(),
                'level' => (int)$data['level']
            );
            if ($data['is_boss'] == 1) {
                $role['label'] = 'BOSS';
            }
            $roleList[] = $role;
        }//end for

        return $roleList;
	}


    //消耗物品
    private static function _costCondition($uid, $condition, &$changeResult)
    {
        $basGoods = Hapyfish2_Alchemy_Cache_Basic::getGoodsList();
        $coinChange = $spChange = 0;
		foreach ($condition as $k => $v) {
		    //道具
			if ($v['type'] == 1) {
			    $cid = $v['id'];
				$userItemList = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
				if (empty($userItemList) || !isset($userItemList[$cid]) || $userItemList[$cid]['count'] <= 0) {
					//return 'item_not_enough';
					return -204;
				}

				if ($basGoods[$cid]['lose']) {
				    if ($userItemList[$cid]['count']<$v['num']) {
				        //return 'item_not_enough';
				        return -204;
				    }
				    Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $v['id'], $v['num'], $userItemList);
				    //$removeItems[] = array($v['id'], $v['num'], $v['id']);
				}
			}
			//玩家属性
			else if ($v['type'] == 2) {
				if ($v['id'] == 'coin') {
					$userCoin = Hapyfish2_Alchemy_HFC_User::getUserCoin($uid);
					if ($userCoin < $v['num']) {
						//return 'coin_not_enough';
						return -207;
					}
					else {
						Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $v['num']);
						$coinChange -= $v['num'];
					}
				}
				else if ($v['id'] == 'sp') {
                    $userSp = Hapyfish2_Alchemy_HFC_User::getUserSp($uid);
				    if ($userSp['sp'] < $v['num']) {
						//return 'sp_not_enough';
						return -208;
					}
					else {
                        Hapyfish2_Alchemy_HFC_User::decUserSp($uid, $v['num']);
                        $spChange -= $v['num'];
					}
				}
			}
		}
		$changeResult = array('coin'=>$coinChange, 'sp'=>$spChange);
		return '';
    }

    //获得奖励 $fromType=1 打怪奖励   $fromType=2 打矿奖励
    public static function awardCondition($uid, $condition, &$changeResult, $fromType=0)
    {    	
        $coinChange = $spChange = $expChange = 0;
        $gainItem = array();
        $numPar = 1000000;
        //统计分析log
        $userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
        $log = Hapyfish2_Util_Log::getInstance();
		foreach ($condition as $k => $v) {
		    //check if in random percent
		    $bingo = true;
		    if (isset($v['per'])) {
                /*if ($v['per'] < 1) {
                    $aryKeys['hit'] = $v['per']*$numPar;
                    $aryKeys['nohit'] = 100*$numPar - $v['per']*$numPar;
                }
                else if ($v['per'] <= 100) {
                    $aryKeys['hit'] = $v['per'];
                    $aryKeys['nohit'] = 100 - $v['per'];
                }
                else {
                    $aryKeys['hit'] = 100;
                }*/
		        $aryKeys['hit'] = (int)$v['per'];
		        $aryKeys['nohit'] = $numPar - (int)$v['per'];
                $hit = self::_randomKeyForOdds($aryKeys);
                if ($hit == 'nohit') {
                    $bingo = false;
                }
		    }
		    if (!$bingo) {
		        continue;
		    }

		    //道具
			if ($v['type'] == 1) {
			    $cid = $v['id'];
			    $num = $v['num'];
    			$type =	substr($cid, -2, 1);

    			//1->物品,2->卷轴,3->材料,4->工作台,5->装修,6->装备
    			switch ($type) {
                    case 1:
                        Hapyfish2_Alchemy_HFC_Goods::addUserGoods($uid, $cid, $num);
                        break;
                    case 2:
                        Hapyfish2_Alchemy_HFC_Scroll::addUserScroll($uid, $cid, $num);
                        break;
                    case 3:
                        Hapyfish2_Alchemy_HFC_Stuff::addUserStuff($uid, $cid, $num);
                        break;
                    case 4:
                    	for ( $n = 0; $n < $num; $n++ ) {
				        	$furnace = array('uid' => $uid,
				        					 'furnace_id' => $cid,
				        					 'status' => 0);
				        	Hapyfish2_Alchemy_HFC_Furnace::addOne($uid, $furnace);
                    	}
                        break;
                    case 5:
                        Hapyfish2_Alchemy_HFC_Decor::addBag($uid, $cid, $num);
                        break;
                    case 6:
                        Hapyfish2_Alchemy_HFC_Weapon::addUserWeapon($uid, $cid, $num);
                        break;
                    default:
    			}
    			$gainItem[$cid] = $num;
    			$userVo = Hapyfish2_Alchemy_Bll_User::getUserInit($uid);
    	    	$map = $userVo['currentSceneId'];
                //统计log
                $log->report('223', array($uid, $map, $cid, $num, $fromType));
			}
			//玩家属性
			else if ($v['type'] == 2) {
				if ($v['id'] == 'coin') {
				    $ok = Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $v['num']);
				    if ($ok) {
					    $coinChange += $v['num'];
				    }
				}
				else if ($v['id'] == 'sp') {
                    $ok = Hapyfish2_Alchemy_HFC_User::incUserSp($uid, $v['num']);
                    if ($ok) {
                        $spChange += $v['num'];
                    }
				}
			    else if ($v['id'] == 'exp') {
                    $ok = Hapyfish2_Alchemy_HFC_User::incUserExp($uid, $v['num']);
                    if ($ok) {
                        $expChange += $v['num'];
                    }
				}
				else if ($v['id'] == 'activity') {
                    $ok = Hapyfish2_Alchemy_Bll_Activity::addUserActivity($uid, $v['num']);
				}
				//神勇点
				/*else if ($v['id'] == 'feats') {
                    $ok = Hapyfish2_Alchemy_HFC_User::incUserFeats($uid, $v['num']);
                    if ($ok) {
                        $featsChange += $v['num'];
                    }
				}*/
			}//奖励图鉴
			else if ($v['type'] == 3) {
				$illCid = $v['id'];
				//添加图鉴
				Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $illCid);
			}
		}//end for

        /*if ($decors) {
            $awardRot = new Hapyfish2_Magic_Bll_Award();
			$awardRot->setDecorList($decors);
			$awardRot->sendOne($uid);
		}*/

		$changeResult = array('coin'=>$coinChange, 'sp'=>$spChange, 'exp'=>$expChange, 'gain'=>$gainItem);
		return '';
    }

    //打击怪后获得奖励计算
    public static function _preCalcAwardCondition($condition)
    {

        $newCondition = array();
        if ($condition) {
            $condition = json_decode($condition, true);
        }

        $numPar = 1000000;
		foreach ($condition as $k => $v) {
		    //check if in random percent
		    $bingo = true;
		    if (isset($v['per'])) {
                /*if ($v['per'] < 1) {
                    $aryKeys['hit'] = $v['per']*$numPar;
                    $aryKeys['nohit'] = 100*$numPar - $v['per']*$numPar;
                }
                else if ($v['per'] <= 100) {
                    $aryKeys['hit'] = $v['per'];
                    $aryKeys['nohit'] = 100 - $v['per'];
                }
                else {
                    $aryKeys['hit'] = 100;
                }*/
		        $aryKeys['hit'] = (int)$v['per'];
		        $aryKeys['nohit'] = $numPar - (int)$v['per'];
                $hit = self::_randomKeyForOdds($aryKeys);
                if ($hit == 'nohit') {
                    $bingo = false;
                }
		    }
		    if (!$bingo) {
		        continue;
		    }

		    unset($v['per']);
		    $newCondition[] = $v;
		}//end for

		//return json_encode($newCondition);
		return $newCondition;
    }

    public static function _arrangeEnemyFightPosAi($selMonster)
    {
        $aryPos = array();
        //$posBegin = Hapyfish2_Alchemy_Bll_Fight_Simulator::MATRIX_ENEMY_MAX;

        $rndPos = mt_rand(1, 100);
        //1-Warrior 2-Rogue 3-Magus
        $priorPosWarrior = array(7,6,8,4,3,5,1,0,2);
        if ($rndPos < 34) {
            $priorPosMagus = array(4,3,5,7,6,8,1,0,2);
        }
        else if ($rndPos < 67){
            $priorPosMagus = array(4,3,5,1,0,2,7,6,8);
        }
        else {
            $priorPosMagus = array(7,6,8,4,3,5,1,0,2);
        }

        $priorPosRogue = array(1,0,2,4,3,5,7,6,8);
        $frontPos = array(6,7,8);
        $frontMidPos = array(6,7,8,3,4,5);
        $midBackPos = array(0,1,2,3,4,5);
        $antePosMap = array(0=>3, 3=>6, 1=>4, 4=>7, 2=>5, 5=>8);

        //priority front line Warrior
        foreach ($selMonster as $id=>$data) {
            if ($data['job'] == 1) {
                foreach ($priorPosWarrior as $pos) {
                    if (!in_array($id, $aryPos) && !array_key_exists($pos, $aryPos)) {
                        $aryPos[$pos] = $id;
                    }
                }
            }
        }
        //priority middle line Magus
	    foreach ($selMonster as $id=>$data) {
            if ($data['job'] == 3) {
                foreach ($priorPosMagus as $pos) {
                    $canStand = false;
                    //mid or back line:check this position can stand people (need ante position has people stand)
                    if (isset($antePosMap[$pos]) && isset($aryPos[$antePosMap[$pos]])) {
                        $canStand = true;
                    }
                    //front line
                    if (in_array($pos, $frontPos)) {
                        //if (in_array($pos, $frontMidPos) && mt_rand(1, 100)<=50) {
                            $canStand = true;
                        //}
                    }
                    if ($canStand && !in_array($id, $aryPos) && !array_key_exists($pos, $aryPos)) {
                        $aryPos[$pos] = $id;
                    }
                }
            }
        }
        //priority back line Rogue
	    foreach ($selMonster as $id=>$data) {
            if ($data['job'] == 2) {
                foreach ($priorPosRogue as $pos) {
                $canStand = false;
                    //mid or back line:check this position can stand people (need ante position has people stand)
                    if (isset($antePosMap[$pos]) && isset($aryPos[$antePosMap[$pos]])) {
                        $canStand = true;
                    }
                    //front line
                    if (in_array($pos, $frontPos)) {
                        //if (in_array($pos, $frontMidPos) && mt_rand(1, 100)<=50) {
                            $canStand = true;
                        //}
                    }
                    if ($canStand && !in_array($id, $aryPos) && !array_key_exists($pos, $aryPos)) {
                        $aryPos[$pos] = $id;
                    }
                }
            }
        }
        //check if Rogue in front line
        foreach ($frontPos as $pos) {
            $id = $aryPos[$pos];
            if ($selMonster[$id]['job'] == 2) {
                foreach ($midBackPos as $pos2) {
                    $id2 = $aryPos[$pos2];
                    //change pos with Rogue and Magus
                    if ($selMonster[$id2]['job'] == 3) {
                        $aryPos[$pos2] = $id;
                        $aryPos[$pos] = $id2;
                        break;
                    }
                }
            }
        }

        return $aryPos;
    }

	/**
	 * generate random by key=>odds
	 *
	 * @param array $aryKeys
	 * @return integer
	 */
	private static function _randomKeyForOdds($aryKeys)
	{
		$tot = 0;
		$aryTmp = array();
		foreach ($aryKeys as $key => $odd) {
			$tot += $odd;
			$aryTmp[$key] = $tot;
		}
		$rnd = mt_rand(1,$tot);

		foreach ($aryTmp as $key=>$value) {
			if ($rnd <= $value) {
				return $key;
			}
		}
	}
	
	/**
	 * 获取用户传送门信息
	 * @param int $uid
	 */
	public static function getTransportList($uid)
	{
		$basicList = Hapyfish2_Alchemy_Cache_Basic::getTransportList();
	    $openTransport = Hapyfish2_Alchemy_HFC_MapCopy::getOpenTransport($uid);
	    $info = array();
		foreach ( $basicList as $v ) {
			if ( in_array($v['map_id'], $openTransport) ) {
				$lock = 0;
			}
			else {
				$lock = 1;
			}
			
			$info[] = array('sceneId' => $v['map_id'],
							'name' => $v['name'],
							'costNum' => $v['cost_num'],
							'order' => $v['order'],
							'group' => $v['group'],
							'entranceId' => $v['entrance_id'],
							'lock' => $lock,
                            'reachable' => (int)$v['reachable']
							);
		}
		return $info;
	}
	
}