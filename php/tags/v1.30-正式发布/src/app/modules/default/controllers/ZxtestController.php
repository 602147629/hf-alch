<?php

class ZxtestController extends Hapyfish2_Controller_Action_External
{

    //清静态数据
	public function clearbascacheAction()
	{
	    $list = Hapyfish2_Alchemy_Cache_MemkeyList::mapBasicMcKey();
	    $localcache = Hapyfish2_Cache_LocalCache::getInstance();
	    $cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
	    foreach ($list as $key) {
	        $cache->delete($key);
	        $localcache->delete($key);
	        echo $key;
	        echo '<br/>';
	    }

	    //task relate
	    Hapyfish2_Alchemy_Cache_Basic::loadAllTaskConditionInfo();
	    Hapyfish2_Alchemy_Cache_Basic::loadAllTaskInfo();
		Hapyfish2_Alchemy_Cache_Basic::loadWeaponList();
	    $v = '1.0';
	    @unlink(TEMP_DIR . '/initvo.' . $v . '.cache');
	    @unlink(TEMP_DIR . '/initvo.' . $v . '.cache.zip');

	    @unlink(TEMP_DIR . '/giftvo.' . $v . '.cache');
	    @unlink(TEMP_DIR . '/giftvo.' . $v . '.cache.zip');
	    exit;
	}

	//清静态副本地图数据
    public function clearbasmapcopyAction()
	{
	    $ids = $this->_request->getParam('ids');
	    $aryMapId = explode(',', $ids);
	    foreach ($aryMapId as $mapId) {
	        $data = Hapyfish2_Alchemy_Cache_Basic::loadMapCopyTranscriptList($mapId);
	        $localcache = Hapyfish2_Cache_LocalCache::getInstance();
	        $delKey = Hapyfish2_Alchemy_Cache_MemkeyList::mapBasicMcKey('alchemy:bas:mapcopydetail:') . $mapId;
	        $localcache->delete($delKey);
	        echo '<br/>';
	        echo $mapId.' CLEAR OK! ';
	        echo '<br/>';
	    }

	    Hapyfish2_Alchemy_Cache_Basic::loadMapCopyVerList();
	    exit;
	}

    public function getbasmapcopyAction()
	{
	    $ids = $this->_request->getParam('ids');
	    $aryMapId = explode(',', $ids);
	    foreach ($aryMapId as $mapId) {
	        $data = Hapyfish2_Alchemy_Cache_Basic::getMapCopyTranscriptList($mapId);
	        echo json_encode($data);
	        echo '<br/><br/>';
	    }

	    exit;
	}

	public function refreshmapcopyAction()
	{
        $uid = $this->_request->getParam('uid');
        $mapId = $this->_request->getParam('mapid');
        $upd = (int)$this->_request->getParam('upd');
        $bef = Hapyfish2_Alchemy_Cache_MapCopy::getMapCopySeries($uid);
        echo json_encode($bef);
        echo '<br />';
        if ($upd) {
            $mapSeries = substr($mapId, 0, -2);
    	    $seriesInfo = Hapyfish2_Alchemy_Cache_MapCopy::getMapCopySeriesById($uid, $mapSeries);
    	    $seriesInfo['refreshTm'] = time();
            Hapyfish2_Alchemy_Cache_MapCopy::setMapCopySeriesById($uid, $mapSeries, $seriesInfo);
        }
        $aft = Hapyfish2_Alchemy_Cache_MapCopy::getMapCopySeries($uid);
        echo json_encode($aft);
        echo '<br />';
        exit;
	}

	public function overfightAction()
	{
	    $uid = $this->_request->getParam('uid');
	    $info = Hapyfish2_Alchemy_Cache_Fight::getFightInfo($uid);
	    $info['status'] = 4;
        echo Hapyfish2_Alchemy_Cache_Fight::saveFightInfo($uid, $info, true);
        exit;
	}

	/**
     * test战斗结束
     */
    public function testendfightAction()
    {
        $uid = $this->_request->getParam('uid');
        $rst = $this->_request->getParam('rst');
        $log = $this->_request->getParam('log');
        Hapyfish2_Alchemy_Bll_UserResult::setUser($uid);
        $status = -200;
        if ($rst) {
            $info = json_decode($rst, true);
            $id = $info['id'];
            $ver = $info['v'];
            $ftRst = $info['type'];
            $aryAct = $info['data'];
info_log_fight('rst='.$rst, 'test-proc-'.$uid.'-'.$id.'-act');
            if ($log) {
                info_log_fight($log, 'test-proc-'.$uid.'-'.$id.'-as');
                $strAct = '';
                foreach ($aryAct as $act) {
                    $strAct .= json_encode($act) . "\n";
                }
                info_log_fight($strAct, 'test-proc-'.$uid.'-'.$id.'-act');
            }

            $status = Hapyfish2_Alchemy_Bll_Fight::completeFight($uid, $id, $aryAct, $ftRst, true);
        }

        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();

    }

    /**
     * test模拟战斗
     */
    public function testinitfightAction()
    {
        $uid = $this->_request->getParam('uid');
        $fid = $this->_request->getParam('fid');

        $info = Hapyfish2_Alchemy_Cache_Fight::loadFightInfo($uid, $fid);

	    if (!$info) {
	        echo 'uid or fid not found!';
	        exit;
	    }

	    $homeSide = $info['home_side'];
	    $enemySide = $info['enemy_side'];
        //拼接Vo数据返回前端
        //我方
        $roleList1 = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
        //敌方
        $roleList2 = Hapyfish2_Alchemy_Bll_MapCopy::genEnemyRolesVo($enemySide);

        //战斗宣言
        $aryTalk = array();
        $cntHomeSide = count($homeSide);
        $rndTalkRole = mt_rand(1, $cntHomeSide);
        $idx = 0;
        foreach ($homeSide as $data) {
        	$idx ++;
            //if ($data['id'] == 0) {
            if ($idx == $rndTalkRole) {
                $talks = Hapyfish2_Alchemy_Cache_Basic::getFightDeclareByJob($data['job']);
                if ($talks) {
                    $rndKey = mt_rand(1, count($talks));
                    $aryTalk[] = array((int)$data['matrix_pos'], $talks[$rndKey-1]);
                }
                break;
            }
        }

	    foreach ($enemySide as $data) {
	        if ($data['is_boss']) {
    	        if ($data['talk']) {
                    $aryTalk[] = array((int)$data['matrix_pos'], $data['talk']);
                }
	        }
        }

        //可援助攻击
        $aryAssist = array();
        $assCnt = 0;
        $extCnt = 0;
        $aryAssist = Hapyfish2_Alchemy_Bll_Fight::getFriendAssistVo($uid);
        $assistInfo = Hapyfish2_Alchemy_HFC_User::getUserFightAssistInfo($uid);
        $assCnt = $assistInfo['assist_bas_count'];
        $extCnt = $assistInfo['assist_ext_count'];


        $battle = array(
            'id' => $info['fid'],
            'bgClassName' => 'battlebg.1.Background',
            'roleList' => array_merge($roleList1, $roleList2),
        	'talk' => $aryTalk,
            'friendSkill' => $aryAssist,
            'assCnt' => $assCnt,
            'extCnt' => $extCnt
        );


        $resultVo = array('BattleVo' => $battle, 'RndNums' => $info['rnd_element']);
        echo json_encode($resultVo);
    	exit;
    }

	public function updmercAction()
	{
	    $uid = $this->_request->getParam('uid');
	    $list = Hapyfish2_Alchemy_HFC_FightMercenary::getAll($uid);
	    foreach ($list as $key=>$data) {
	        $mercInfo = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $key);
	        print_r($mercInfo);
            $mercInfo['hp'] = $mercInfo['hp_max'];
            $mercInfo['mp'] = $mercInfo['mp_max'];
            echo '<br/>After:<br/>';
            print_r($mercInfo);
            echo Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $key, $mercInfo);
	    }
        exit;
	}

    public function updmaxhpmpAction()
	{
	    $uid = $this->_request->getParam('uid');
	    $id = (int)$this->_request->getParam('id');
	    $hp = (int)$this->_request->getParam('hp');
	    $mp = (int)$this->_request->getParam('mp');

	    if ($id) {
            $mercInfo = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
            print_r($mercInfo);
            if ($hp) {
                $mercInfo['hp_max'] = $hp;
            }
            if ($mp) {
                $mercInfo['mp_max'] = $mp;
            }
            echo '<br/>After:<br/>';
            print_r($mercInfo);
            echo Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $id, $mercInfo);
	    }
	    else {
            $info = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
	        if ($hp) {
                $info['hp_max'] = $hp;
            }
            if ($mp) {
                $info['mp_max'] = $mp;
            }
            print_r($info);
            echo Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $info);
	    }
        exit;
	}

    public function updselfAction()
	{
	    $uid = $this->_request->getParam('uid');
	    $info = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
        //$mercInfo['exp'] += 5;
        $info['hp'] = $info['hp_max'];
        $info['mp'] = $info['mp_max'];
        print_r($info);
        echo Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $info);
        exit;
	}

    public function addskillAction()
    {
        $uid = $this->_request->getParam('uid');
        $id = $this->_request->getParam('id');
        $skill = $this->_request->getParam('skill');
        if (!$skill) {
            exit;
        }
        if ($id) {
            $row = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
            $row['skill'] = array((int)$skill, 0, 0);
            Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $id, $row);
        }
        else {
            $row = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
            $row['skill'] = array((int)$skill, 0, 0);
            Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $row);
        }

        Hapyfish2_Alchemy_HFC_FightAttribute::loadInfo($uid);
	    Hapyfish2_Alchemy_HFC_FightMercenary::reloadAll($uid);
	    Hapyfish2_Alchemy_Cache_FightCorps::loadFightCorpsInfo($uid);
	    echo 'ok';
	    exit;
    }

    public function gotomapAction()
	{
	    $uid = $this->_request->getParam('uid');
	    $id = $this->_request->getParam('id');
	    if ($id) {
            $usrScene = Hapyfish2_Alchemy_HFC_User::getUserScene($uid);
    		$usrScene['cur_scene_id'] = $id;
    		Hapyfish2_Alchemy_HFC_User::updateUserScene($uid, $usrScene, true);
            echo 'ok';
	    }

        exit;
	}

    public function addtaskAction()
	{
	    $uid = $this->_request->getParam('uid');
	    $tid = (int)$this->_request->getParam('tid');
	    if ($tid) {
	        $bas = Hapyfish2_Alchemy_Cache_Basic::getTaskInfo($tid);
	        if ($bas) {
	            $task = Hapyfish2_Alchemy_HFC_TaskOpen::getInfo($uid);
                if (!in_array($tid, $task['list2'])) {
                    $task['list2'][] = $tid;
                    Hapyfish2_Alchemy_HFC_TaskOpen::save($uid, $task);
                }
                echo "task: $tid added";
	        }
	    }

        exit;

	}

	public function resettaskAction()
	{
        $uid = $this->_request->getParam('uid');
        if ($uid) {
            $task = Hapyfish2_Alchemy_HFC_TaskOpen::getInfo($uid);
            if ($task) {
                $basTaskList = Hapyfish2_Alchemy_Cache_Basic::getTaskList();
    			$taskOpen = array();
    			$taskPrepare = array();
    			/*foreach ($basTaskList as $data) {
    			    if ($data['label'] < 3 && $data['need_user_level'] == 1 && ($data['front_task_id'] == '[]' || !$data['front_task_id'])) {
    			        if ($data['from_type'] == 4) {
    			            $taskOpen[] = (int)$data['id'];
    			        }
    			        else {
                            $taskPrepare[] = (int)$data['id'];
    			        }
    				}
    			}*/
    			//$task['list'] = '[31,451,1792,2061,3931,3941,3951,3961,3971]';
                //$task['list2'] = '[221,572,3511]';
                $task['list'] = $taskOpen;
                $task['list2'] = $taskPrepare;
                $task['data'] = array();
                $task['buffer_list'] = array();
                Hapyfish2_Alchemy_HFC_TaskOpen::save($uid, $task, true);
            }

            $taskDaily = Hapyfish2_Alchemy_HFC_TaskDaily::getInfo($uid);
            if ($taskDaily) {
                $taskDaily['list'] = '[]';
                $taskDaily['data'] = '[]';
                $taskDaily['refresh_tm'] = 0;
                Hapyfish2_Alchemy_HFC_TaskDaily::save($uid, $taskDaily);
            }

            $dal = Hapyfish2_Alchemy_Dal_Task::getDefaultInstance();
            $dal->clear($uid);
            Hapyfish2_Alchemy_Cache_Task::loadIds($uid);

            echo $uid.': tasks reseted!';
        }
        exit;
	}

	public function resetavatarAction()
	{
        $uid = $this->_request->getParam('uid');
        if ($uid) {
            Hapyfish2_Alchemy_HFC_User::updateUserAvatar($uid, 0);

            $dalFightAttribute = Hapyfish2_Alchemy_Dal_FightAttribute::getDefaultInstance();
			$dalFightAttribute->clear($uid);
            $cache = Hapyfish2_Cache_Factory::getHFC($uid);
            $key = 'a:u:fightattrib:'.$uid;
            $cache->delete($key);

            echo $uid. ': avatar info cleared!';
        }
        exit;
	}

	public function resetoccupyforbiddenAction()
	{
        $uids = $this->_request->getParam('uids');
        $ary = explode(',', $uids);
        foreach ($ary as $uid) {
            $occupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($uid);
            $occupyInfo['corps_used'] = array();
            $occupyInfo['passive'] = array();
            $occupyInfo['initiative'] = array();
            $occupyInfo['last_protect_open_tm'] = 0;
            Hapyfish2_Alchemy_HFC_FightOccupy::save($uid, $occupyInfo);
            echo $uid.':ok';
        }

        exit;
	}

    public function resetmerccdAction()
	{
        $uid = $this->_request->getParam('uid');

        $occupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($uid);
        $occupyInfo['corps_used'] = array();
        Hapyfish2_Alchemy_HFC_FightOccupy::save($uid, $occupyInfo);
        echo $uid.':ok';
        exit;
	}

    public function createuserdbsqlAction()
	{
	    $aryTables = array();



	    $aryTables[] = array('name'=>'alchemy_user_decor', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_decor` (`uid` int(10) unsigned NOT NULL,`id` int(10) unsigned NOT NULL COMMENT '装饰物实例id',`cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',`x` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:x',`z` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '坐标:z',`m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',`s` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '删除标记位(0删除,1有效)',PRIMARY KEY (`uid`,`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_decor_inbag', 'num'=>50, 'val'=>"CREATE TABLE `alchemy_user_decor_inbag` (`uid` int(10) unsigned NOT NULL,`cid` int(10) unsigned NOT NULL COMMENT '装饰物cid,对应 alchemy_decor表',`count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拥有数量',PRIMARY KEY (`uid`,`cid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_fight', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_fight` (`uid` int(10) unsigned NOT NULL,`fid` int(10) unsigned NOT NULL,`type` tinyint(4) DEFAULT '0' COMMENT '0-普通打怪 1-侵略 2-反抗 3-救援',`enemy_id` varchar(50) NOT NULL,`status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '战斗状态 0-开始 1-胜利 2-失败 3-逃跑 4-其他',`rnd_element` varchar(1000) NOT NULL DEFAULT '[]',`home_side` text NOT NULL,`enemy_side` text NOT NULL,`content` text NOT NULL,`create_time` int(10) unsigned NOT NULL,PRIMARY KEY (`uid`,`fid`),KEY `fid` (`fid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_fight_attribute', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_fight_attribute` (`uid` int(10) unsigned NOT NULL,`cid` int(10) unsigned NOT NULL,`gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',`rp` tinyint(3) unsigned NOT NULL,`job` tinyint(3) unsigned NOT NULL,`name` varchar(30) DEFAULT NULL,`class_name` varchar(200) NOT NULL,`face_class_name` varchar(200) NOT NULL,`s_face_class_name` varchar(200) NOT NULL,`scene_player_class` varchar(200) NOT NULL,`sex` tinyint(3) unsigned NOT NULL DEFAULT '1',`element` tinyint(3) unsigned NOT NULL DEFAULT '1',`exp` int(10) unsigned NOT NULL DEFAULT '0',`level` int(10) unsigned NOT NULL DEFAULT '1',`hp` int(10) unsigned NOT NULL DEFAULT '1',`hp_max` int(10) unsigned NOT NULL DEFAULT '1',`mp` int(10) unsigned NOT NULL DEFAULT '1',`mp_max` int(10) unsigned NOT NULL DEFAULT '1',`phy_att` int(10) unsigned NOT NULL DEFAULT '1',`phy_def` int(10) unsigned NOT NULL DEFAULT '1',`mag_att` int(10) unsigned NOT NULL DEFAULT '1',`mag_def` int(10) unsigned NOT NULL DEFAULT '1',`agility` int(10) unsigned NOT NULL DEFAULT '1',`crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',`dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',`weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',`skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',`s_phy_att` int(10) DEFAULT '0' COMMENT '强化属性',`s_phy_def` int(10) DEFAULT '0',`s_mag_att` int(10) DEFAULT '0',`s_mag_def` int(10) DEFAULT '0',`s_agility` int(10) DEFAULT '0',PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_fight_corps', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_fight_corps` (`uid` int(10) unsigned NOT NULL,`matrix` varchar(200) NOT NULL DEFAULT '[]',PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_fight_mercenary', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_fight_mercenary` (`uid` int(10) unsigned NOT NULL,`mid` int(10) unsigned NOT NULL COMMENT '实例id',`cid` int(10) unsigned NOT NULL COMMENT '模型cid，mercenary_model 表cid',`gid` int(10) unsigned NOT NULL COMMENT '成长曲线id，mercenary_grow',`rp` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '资质级别',`job` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-战士，2-弓手，3-法师',`name` varchar(30) NOT NULL COMMENT '佣兵名字',`class_name` varchar(200) NOT NULL COMMENT '素材',`face_class_name` varchar(200) NOT NULL COMMENT '头像素材类名',`s_face_class_name` varchar(200) DEFAULT NULL COMMENT '战斗时的小头像素材类名',`scene_player_class` varchar(200) DEFAULT NULL COMMENT '佣兵在场景中走动的素材',`sex` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-男，0-女',`element` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1-风,2-火,3-水',`exp` int(10) unsigned NOT NULL DEFAULT '0',`level` tinyint(3) unsigned NOT NULL DEFAULT '1',`hp` int(10) unsigned NOT NULL DEFAULT '1',`hp_max` int(10) unsigned NOT NULL DEFAULT '1',`mp` int(10) unsigned NOT NULL DEFAULT '1',`mp_max` int(10) unsigned NOT NULL DEFAULT '1',`phy_att` int(10) unsigned NOT NULL DEFAULT '1',`phy_def` int(10) unsigned NOT NULL DEFAULT '1',`mag_att` int(10) unsigned NOT NULL DEFAULT '1',`mag_def` int(10) unsigned NOT NULL DEFAULT '1',`agility` int(10) unsigned NOT NULL DEFAULT '1',`crit` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '暴击率 0-1000 (percent)',`dodge` smallint(5) unsigned NOT NULL DEFAULT '1' COMMENT '闪避率 0-1000 (percent)',`weapon` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3,4]',`skill` varchar(200) NOT NULL DEFAULT '[]' COMMENT '[1,2,3]',`s_phy_att` int(10) DEFAULT '0' COMMENT '强化附加属性',`s_phy_def` int(10) DEFAULT '0' COMMENT '强化附加属性',`s_mag_att` int(10) DEFAULT '0' COMMENT '强化附加属性',`s_mag_def` int(10) DEFAULT '0' COMMENT '强化附加属性',`s_agility` int(10) DEFAULT '0' COMMENT '强化附加属性',PRIMARY KEY (`uid`,`mid`),KEY `mid` (`mid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_floorwall', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_floorwall` (`uid` int(10) unsigned NOT NULL,`floor` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '地板cid',`wall` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '墙cid',PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_furnace', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_furnace` (`id` int(10) unsigned NOT NULL COMMENT '工作台实例id',`uid` int(10) unsigned NOT NULL,`furnace_id` int(10) unsigned NOT NULL COMMENT '工作台cid',`x` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：x坐标',`z` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '位置：z坐标',`m` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '镜像(方向)',`cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术cid',`start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成术开始时间',`need_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '合成需要时间',`cur_probability` int(10) unsigned DEFAULT '0' COMMENT '当前成功率',`num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '本次合成个数',`status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否在房间内,1:在房间，0:在背包',PRIMARY KEY (`uid`,`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_gift_bag', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_gift_bag` (`uid` int(10) unsigned NOT NULL,`from_uid` int(10) unsigned NOT NULL,`date` int(10) unsigned NOT NULL COMMENT '日期',`method` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '类型 0-送礼 1-2-3-愿望',`gid` int(10) unsigned NOT NULL,`status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0-未处理 1-接受 2-忽略',`create_time` int(10) unsigned NOT NULL,PRIMARY KEY (`uid`,`from_uid`,`date`,`method`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_gift_friend_wish', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_gift_friend_wish` (`uid` int(10) unsigned NOT NULL,`from_uid` int(10) unsigned NOT NULL,`gid_1` int(10) unsigned NOT NULL,`gid_2` int(10) unsigned NOT NULL,`gid_3` int(10) unsigned NOT NULL,`dealt` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1,2,3',`create_time` int(10) unsigned NOT NULL,PRIMARY KEY (`uid`,`from_uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_gift_wish', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_gift_wish` (`uid` int(10) unsigned NOT NULL,`fids` varchar(1000) NOT NULL DEFAULT '',`gid_1` int(10) unsigned NOT NULL,`gid_2` int(10) unsigned NOT NULL,`gid_3` int(10) unsigned NOT NULL,`gained_1` varchar(32) NOT NULL DEFAULT '' COMMENT 'fromuid|time',`gained_2` varchar(32) NOT NULL DEFAULT '',`gained_3` varchar(32) NOT NULL DEFAULT '',`create_time` int(10) unsigned NOT NULL,PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_goods', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_goods` (`uid` int(10) unsigned NOT NULL,`cid` int(10) unsigned NOT NULL COMMENT '物品cid,对应 alchemy_goods表',  `count` int(10) unsigned NOT NULL COMMENT '拥有数量',PRIMARY KEY (`uid`,`cid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_hire', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_hire` (`uid` int(10) unsigned NOT NULL COMMENT '酒馆信息表',`hire_1` varchar(200) NOT NULL DEFAULT '[1,1,1]' COMMENT '佣兵位置信息-1',  `hire_2` varchar(200) NOT NULL DEFAULT '[2,1,1]' COMMENT '佣兵位置信息-2',  `hire_3` varchar(200) NOT NULL DEFAULT '[3,2,1]' COMMENT '佣兵位置信息-3',  `hire_4` varchar(200) NOT NULL DEFAULT '[4,2,1]' COMMENT '佣兵位置信息-4',  `hire_5` varchar(200) NOT NULL DEFAULT '[5,3,1]' COMMENT '佣兵位置信息-5',  `hire_6` varchar(200) NOT NULL DEFAULT '[6,3,1]' COMMENT '佣兵位置信息-6',  PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_illustrations', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_illustrations` (`uid` int(10) unsigned NOT NULL,`id` text NOT NULL COMMENT '用户拥有图鉴列表,[[id,isNew],[1,1]],isNew:是否新获得,1:新，0:非新',  PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_info', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_info` (`uid` int(10) unsigned NOT NULL COMMENT '用户id',`avatar` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '头像id',`coin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金币数',`gem` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '宝石数',`feats` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '神勇点',`sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前行动力',`max_sp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '允许最大行动力',`sp_set_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后一次恢复sp时间',`exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '经验值',`max_exp` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最大经验值',`level` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '经营等级',`home_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '自宅等级',`tavern_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '酒馆等级',`smithy_level` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '铁匠铺等级',`order_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '接受订单数量上限',`mercenary_count` int(10) unsigned NOT NULL DEFAULT '3' COMMENT '佣兵位置数',`satisfaction` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '满意度',`tile_x_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',`tile_z_length` tinyint(3) unsigned NOT NULL DEFAULT '8' COMMENT '房间大小',`open_scene_list` varchar(200) NOT NULL DEFAULT '0' COMMENT '已开启场景id列表',`cur_scene_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当前场景id',`isfans` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否粉丝,1:是,0:否',`create_time` int(10) unsigned NOT NULL DEFAULT '0',`last_login_time` int(10) unsigned NOT NULL DEFAULT '0',`today_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',`active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',`max_active_login_count` smallint(5) unsigned NOT NULL DEFAULT '0',`all_login_count` int(10) unsigned NOT NULL DEFAULT '0',`login_day_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '累计登录天数',`assist_bas_count` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '援助基本次数',`assist_ext_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '援助扩展次数',PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_log_add_gem', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_log_add_gem` (`uid` int(10) unsigned NOT NULL,`gold` int(10) unsigned NOT NULL,`type` tinyint(3) unsigned NOT NULL DEFAULT '0',`summary` varchar(255) NOT NULL DEFAULT '',`create_time` int(10) unsigned NOT NULL,KEY `idx_uid` (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_log_consume_coin', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_log_consume_coin` (`uid` int(10) unsigned NOT NULL,`cost` int(10) unsigned NOT NULL,`summary` varchar(255) NOT NULL,`create_time` int(10) unsigned NOT NULL,KEY `uid` (`uid`,`create_time`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_log_consume_gem', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_log_consume_gem` (`uid` int(10) unsigned NOT NULL,`cost` int(10) unsigned NOT NULL,`summary` varchar(255) NOT NULL,`create_time` int(10) unsigned NOT NULL,`user_level` tinyint(3) unsigned NOT NULL,`cid` int(10) unsigned NOT NULL,`num` smallint(5) unsigned NOT NULL,KEY `uid` (`uid`,`create_time`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_map_copy', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_map_copy` (`uid` int(10) unsigned NOT NULL,`map_id` int(10) unsigned NOT NULL,`map_ver` int(10) unsigned NOT NULL DEFAULT '1',`enter_time` int(10) unsigned NOT NULL,`data` text NOT NULL,PRIMARY KEY (`uid`,`map_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_mix', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_mix` (`uid` int(10) NOT NULL,`mix_cids` varchar(10000) DEFAULT '[]' COMMENT '已学习合成术列表,[121,221]',PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_occupy', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_occupy` (`uid` int(10) unsigned NOT NULL,`corps_used` varchar(500) NOT NULL DEFAULT '[]',`passive` varchar(500) NOT NULL DEFAULT '[]',`initiative` varchar(3000) NOT NULL DEFAULT '[]',`last_protect_open_tm` int(10) unsigned NOT NULL DEFAULT '0',PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_order', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_order` (`uid` int(10) NOT NULL,`order` text COMMENT '订单信息',PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_paylog', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_paylog` (`uid` int(10) unsigned NOT NULL,`orderid` varchar(32) NOT NULL,`pid` varchar(32) NOT NULL,`amount` int(10) unsigned NOT NULL DEFAULT '0',`gold` int(10) unsigned NOT NULL DEFAULT '0',`extra_gold` int(10) unsigned NOT NULL DEFAULT '0',`summary` varchar(100) NOT NULL DEFAULT '',`create_time` int(10) unsigned NOT NULL DEFAULT '0',`user_level` tinyint(3) unsigned NOT NULL DEFAULT '0',`pay_before_gold` int(10) unsigned NOT NULL DEFAULT '0',`is_first_pay` tinyint(3) unsigned NOT NULL DEFAULT '0',PRIMARY KEY (`orderid`),KEY `idx_uid` (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_scroll', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_scroll` (`uid` int(10) unsigned NOT NULL,`cid` int(10) unsigned NOT NULL COMMENT '卷轴cid,对应 alchemy_scroll表',`count` int(10) unsigned NOT NULL COMMENT '拥有数量',PRIMARY KEY (`uid`,`cid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_seq', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_seq` (`uid` int(10) unsigned NOT NULL,`name` char(1) NOT NULL,`id` int(10) unsigned NOT NULL DEFAULT '100',PRIMARY KEY (`uid`,`name`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_story', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_story` (`uid` int(11) NOT NULL,`list` varchar(2000) DEFAULT NULL,PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_story_dialog', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_story_dialog` (`uid` int(10) NOT NULL,`list` varchar(2000) DEFAULT '{\"101\":{\"1\":1},\"103\":{\"6\":1}}',PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_stuff', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_stuff` (`uid` int(10) unsigned NOT NULL,`cid` int(10) unsigned NOT NULL COMMENT '材料cid,对应 alchemy_stuff表',`count` int(10) unsigned NOT NULL COMMENT '拥有数量',PRIMARY KEY (`uid`,`cid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_task', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_task` (`uid` int(10) unsigned NOT NULL,`tid` smallint(5) unsigned NOT NULL,`finish_time` int(10) unsigned NOT NULL,UNIQUE KEY `idx_uid_tid` (`uid`,`tid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_task_daily', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_task_daily` (`uid` int(10) NOT NULL,`list` varchar(255) NOT NULL DEFAULT '[]',`data` varchar(3000) NOT NULL DEFAULT '[]',`refresh_tm` int(10) unsigned NOT NULL DEFAULT '0',PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_task_open', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_task_open` (`uid` int(10) unsigned NOT NULL,`list` varchar(2000) NOT NULL DEFAULT '[]',`list2` varchar(2000) NOT NULL DEFAULT '[]',`data` varchar(6000) NOT NULL DEFAULT '[]',`buffer_list` varchar(1000) NOT NULL DEFAULT '[]',PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_unique_item', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_unique_item` (`uid` int(10) unsigned NOT NULL,`item_ids` varchar(3000) NOT NULL default '[]' COMMENT '已获得唯一物品列表',PRIMARY KEY  (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_weapon', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_weapon` (`uid` int(10) unsigned NOT NULL,`cid` int(10) unsigned NOT NULL COMMENT '装备cid',`count` int(10) unsigned NOT NULL COMMENT '拥有个数',`data` varchar(10000) NOT NULL COMMENT '装备信息[[id,status,durability],[实例id。状态，0:未装备,-1:主角，XX:佣兵id。耐久度]]',PRIMARY KEY (`uid`,`cid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'alchemy_user_world_map', 'num'=>10, 'val'=>"CREATE TABLE `alchemy_user_world_map` (`uid` int(10) unsigned NOT NULL,`map_ids` varchar(2000) NOT NULL DEFAULT '[]',PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'platform_user_friend', 'num'=>10, 'val'=>"CREATE TABLE `platform_user_friend` (`uid` int(10) unsigned NOT NULL,`fids` varchar(12000) NOT NULL DEFAULT '',`count` smallint(5) unsigned NOT NULL DEFAULT '0',PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'platform_user_info', 'num'=>10, 'val'=>"CREATE TABLE `platform_user_info` (`uid` int(10) unsigned NOT NULL,`puid` varchar(64) NOT NULL,`name` varchar(32) NOT NULL DEFAULT '',`figureurl` varchar(255) NOT NULL DEFAULT '',`gender` tinyint(4) NOT NULL DEFAULT '-1',`create_time` int(10) unsigned NOT NULL DEFAULT '0',`vuid` varchar(16) NOT NULL DEFAULT '',`promote_code` int(10) unsigned NOT NULL DEFAULT '0',`status` tinyint(4) NOT NULL DEFAULT '0',`status_update_time` int(10) unsigned NOT NULL DEFAULT '0',PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	    $aryTables[] = array('name'=>'platform_user_info_more', 'num'=>10, 'val'=>"CREATE TABLE `platform_user_info_more` (`uid` int(10) unsigned NOT NULL,`session_key` varchar(100) NOT NULL DEFAULT '',`info` varchar(1000) NOT NULL DEFAULT '',PRIMARY KEY (`uid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

	    $out = '';
	    foreach ($aryTables as $data) {
	        $out .= '/* TABLE ' . $data['name'] . '*/<br/>';
	        for ($i=0;$i<$data['num'];$i++) {
	            $sql = str_replace('`'.$data['name'].'`', '`'.$data['name'].'_'.$i.'`', $data['val']);
	            $out .= $sql . '<br/>';
	        }
	    }
	    echo $out;
	    exit;
	}




    public function testvalAction()
	{
	    $aList = array('1'=>array(111), '2'=>array(22), '3'=>array('cc'));
	    echo 'alist:';
	    print_r($aList);
	    echo '<br />';

	    $bList = $aList;
	    //$bList['2'] = array('bb');

	    $bList = null;
	    echo 'alist:';
	    print_r($aList);
	    echo '<br />';

	    echo 'blist:';
	    print_r($bList);
	    echo '<br />';

	    echo ceil(-1.256);
        echo '<br />';
echo $this->_request->getParam('a');
echo urlencode('a+b');exit;
        echo base64_encode(md5('12029234ZXhwaXJlc19pbj0xNTU1MjAwMCZpZnJhbWU9MSZyZV9leHBpcmVzX2luPTAmcmVmcmVzaF90b2tlbj02MTAyMjAxZjUxMTcwNzc3MzhlMTI4YzI1NjFlMjI5NjRjNWNlMGI2ZjY5NmFjMzc4OTk2MzM1JnRzPTEzMzMwNzg3NzkzNzEmdmlzaXRvcl9pZD03ODk5NjMzNSZ2aXNpdG9yX25pY2s9t+fWrtfm6100401821fd298cf6124cad211a7e53a2af08cef9fb8e47899633596ad573ff3fef48a84b3fcf7e7da605c',true));
exit;

	    $detail = '{"num":5,"gp":[[171,70],[271,20],[371,10]]}';
	    $detail = json_decode($detail, true);
        //random monster count
        $maxNum = 10;
echo $maxNum;echo '<br/>';
        //random monster cid
        $aryRnd = array();
        foreach ($detail['gp'] as $data) {
            for ($i=0; $i<(int)$data[1]; $i++) {
                $aryRnd[] = (int)$data[0];
            }
        }
echo json_encode($aryRnd);echo '<br/>';

        $testAry = array(4,10,100,1000,10000);
        foreach ($testAry as $testcnt) {
        //次数
        $rateAll = array();
        for ($j=0;$j<$testcnt;$j++) {
            $rndKey = array_rand($aryRnd, $maxNum);
            $cids = array();
            foreach ($rndKey as $key) {
                $cids[] = $aryRnd[$key];
            }

            $rate = array();
            $all = count($cids);
            foreach ($cids as $cid) {
                if (!array_key_exists($cid, $rate)) {
                    $rate[$cid] = 1;
                }
                else {
                    $rate[$cid] += 1;
                }
            }
            foreach ($rate as $cid=>$val) {
                $rateAll[$cid] += $val/$all*100;
            }
            //echo json_encode($rate);
            //echo '<br/>';
        }

        foreach ($rateAll as $cid=>$val) {
            $rateAll[$cid] = $val/$testcnt;
        }
        echo $testcnt.' times:'.json_encode($rateAll);
        echo '<br/>';
        }
        exit;
	}

    public function testtestAction()
    {

        echo json_encode(Hapyfish2_Alchemy_Bll_Help::guideFight(10245));exit;
        echo mt_rand(2,2);exit;
        $a = 'sssfdsfs';
        $a= array(10);
        print_r($a);exit;


        $uid = $this->_request->getParam('uid');
        $aa = Hapyfish2_Alchemy_Cache_Fight::getFightFriendAssistInfo($uid);
        echo json_encode($aa);exit;
        $name = 'zx';
        $text = <<<aaaa
My name is "$name". I am printing some $name
Now, I am printing some .
This should print a capital 'A':
aaaa;
echo $text;exit;


        $num = 4;
        $ary = array('1'=>'1','3'=>'3','5'=>'5','7'=>'7');
        if ($num == 1) {
            $randKeys1 = array(array_rand($ary, $num));
        }
        else {
            $randKeys1 = array_rand($ary, $num);
        }
        print_r($randKeys1);

	    echo $data;
        exit;
    }

    protected function flush()
	{
    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	$data = Hapyfish2_Alchemy_Bll_UserResult::flush();
    	echo json_encode($data);
    	exit;
	}
	
	public function addpondAction()
	{
		 $uid = $this->_request->getParam('uid');
		 $hp =  $this->_request->getParam('hp', 0);
		 $mp = $this->_request->getParam('mp', 0);
		 $pond = Hapyfish2_Alchemy_HFC_Goods::getUserPond($uid);
		 $pond['hp'] += $hp;
		 $pond['mp'] += $mp;
		 Hapyfish2_Alchemy_HFC_Goods::updateUserPond($uid, $pond);
		 echo "hp: ".$pond['hp'];
		 echo "<br />";
		 echo "mp: ".$pond['mp'];
		 $list = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
		 print_r($list);
		 exit;
	}
	
	public function addweaponAction()
	{
		$uid = $this->_request->getParam('uid');
		$cid = $this->_request->getParam('cid');
		Hapyfish2_Alchemy_HFC_Weapon::addOne($uid, $cid);
		echo "ok";
		exit;
	}
	
	public function clearuserillustrationsAction()
	{
		$uid = $this->_request->getParam('uid');
		$data = array();
		Hapyfish2_Alchemy_Cache_Illustrations::updateUserIllustrations($uid, $data);
		echo "ok";
		exit;
	}
	
	public function cleareventgiftAction()
	{
		$cache = Hapyfish2_Alchemy_Cache_EventGift::getBasicMC();
		$k = 'alchemy:bas:timeGift';
		$k1 = 'alchemy:bas:sevenGift';
		$k2 = 'alchemy:bas:levelGift';
		$k3 = 'alchemy:bas:package';
		$cache->delete($k);
		$cache->delete($k1);
		$cache->delete($k2);
		$cache->delete($k3);
		echo "ok";
		exit;
	}
	
	public function clearusereventgiftAction()
	{
		$uid = $this->_request->getParam('uid');
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$key = 'a:u:levelGift:'.$uid;
		$key1 = 'a:u:timeGift:'.$uid;
		$key2 = 'a:u:sevenGift:'.$uid;
		$cache->delete($key);
		$cache->delete($key1);
		$cache->delete($key2);
		$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
		$dal->clear($uid);
		echo "ok";
		exit;
	}
	
	public function updateusersevenAction()
	{
		$uid = $this->_request->getParam('uid');
		$data = Hapyfish2_Alchemy_Cache_EventGift::getUserSGift($uid);
		$data['date'] -= 1;
		Hapyfish2_Alchemy_Cache_EventGift::updateUserSGift($uid,$data);
		echo "ok";
		exit;
		
	}
	
	public function clearqlAction()
	{
		$uid = $this->_request->getParam('uid');
		$tarOccupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($uid);
		$tarOccupyInfo['passive']['status'] = 0;
		$tarOccupyInfo['passive']['tm'] =time()-7200;
		Hapyfish2_Alchemy_HFC_FightOccupy::save($uid, $tarOccupyInfo);
		echo "ok";
		exit;
	}
	
	public function clearopentaskAction()
	{
		$uid = $this->_request->getParam('uid'); 
		$key = 'a:u:taskopen:' . $uid;
	    $cache = Hapyfish2_Cache_Factory::getHFC($uid);
	    $cache->delete($key);
	    echo "Ok";
	}
	
	public function clearactivityAction()
	{
		$uid = $this->_request->getParam('uid'); 
        $ids = Hapyfish2_Alchemy_Cache_Basic::getDailyTaskIds();
        $taskDaily['list'] = $ids;
        $taskDaily['data'] = array();
        $taskDaily['finish'] = array();
        $taskDaily['refresh_tm'] = time();
        Hapyfish2_Alchemy_HFC_TaskDaily::save($uid, $taskDaily, true);
        $userac['uid'] = $uid;
        $userac['step'] = '[]';
        $userac['activity'] = 0;
        $userac['update_time'] = date('Ymd');
        Hapyfish2_Alchemy_Cache_Activity::update($uid, $userac);
        echo "ok";
        exit;
		
	}
	
	public function clearinvitelogAction()
	{
		$uid = $this->_request->getParam('uid'); 
		$key = 'a:u:invite:' . $uid;
	    $cache = Hapyfish2_Cache_Factory::getHFC($uid);
	    $cache->delete($key);
	    echo "Ok";
	}
	
}