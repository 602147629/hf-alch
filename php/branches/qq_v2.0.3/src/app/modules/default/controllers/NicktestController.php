<?php

/**
 * Alchemy nicktest controller
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/02    Nick
 */
class NicktestController extends Hapyfish2_Controller_Action_Api
{

	protected function getClientIP()
	{
		$ip = false;
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) {
				array_unshift($ips, $ip);
				$ip = false;
			}
			for ($i = 0, $n = count($ips); $i < $n; $i++) {
				if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])) {
					$ip = $ips[$i];
					break;
				}
			}
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
	
		return $ip;
	}
	
//	public function init()
//	{ 
//		$stop = true;
//		$ip = $this->getClientIP();
//		if ( $ip == '180.168.126.10' ) {
//			$stop = false;
//		}
//		
//		if ($stop) {
//			echo '-111';
//			exit;
//		}
//	}
	
	public function clearcacheAction()
	{
		$isTest = $this->_request->getParam('istest', 0);
		
		if ( $isTest == 1 ) {
			$memcache_host = '127.0.0.1';
			$port = 11212;
		}
		else {
			$memcache_host = '192.168.1.249';
			$port = 11611;
		}
		
		$memcache_obj = memcache_connect($memcache_host, $port);

		memcache_flush($memcache_obj);
		
		$memcache_obj = new Memcache;
		$memcache_obj->connect($memcache_host, $port);
		
		$memcache_obj->flush();

		echo 'OK';
	}

	/**
	 * 重置帐号
	 */
	public function clearuserAction()
	{
		$uid = $this->_request->getParam('uid', 0);
		$redirect = $this->_request->getParam('redirect', 0);
		
		if ( $uid != 0 ) {
			$ok = Hapyfish2_Alchemy_Bll_User::clearUser($uid);
			if ($ok) {
				$ok = Hapyfish2_Alchemy_Bll_User::resetUser($uid);
			}
		}
		else {
			/*$dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
			for ( $i=0;$i<10;$i++ ) {
				$allList = $dalUser->getAll($i);
				foreach ( $allList as $v ) {
					$uid = $v['uid'];
					$ok = Hapyfish2_Alchemy_Bll_User::resetUser($uid);
				}
			}*/
		}
		
		if ($ok) {
			if ( $redirect == 0 ) {
				$this->_redirect(HOST."/watch?uid=".$uid);
			}
			echo 'OK';
		}
		else {
			echo 'False';
		}
	}
	
    public function dumpuserdataAction()
    {
        $uid = $this->_request->getParam('uid');
        $filename = $this->_request->getParam('filename');
        
        if ( !$filename ) {
        	echo '文件名不能为空。';
        	exit;
        }
        
        $ok = Hapyfish2_Alchemy_Bll_UserDump::dumpUser($uid, $filename);
        
        if ($ok) {
            echo 'OK';
        }
        else {
            echo 'False';
        }
        exit;
    }
    
    public function loaduserdataAction()
    {
        $uid = $this->_request->getParam('uid', null);
        $loadId = $this->_request->getParam('loadId', null);
        
        if ( !$loadId ) {
            echo '文件名不能为空。';
            exit;
        }
            $okClear = true;
            $okClear = Hapyfish2_Alchemy_Bll_User::clearUser($uid);
            if ( $okClear ) {
                $ok = Hapyfish2_Alchemy_Bll_UserDump::loadUserData($uid, $loadId);
            }
        
        if ($ok) {
            $this->_redirect(HOST."/watch?uid=".$uid);
            echo 'OK';
        }
        else {
            echo 'False';
        }
    }
    
    public function removeuserdataAction()
    {
        $uid = $this->_request->getParam('uidRev', null);
        $revId = $this->_request->getParam('revId', null);
        
        if ( !$revId ) {
            echo '文件名不能为空。';
            exit;
        }
        
        $ok = Hapyfish2_Alchemy_Bll_UserDump::removeUserData($uid, $revId);
        
        if ($ok) {
            echo 'OK';
            $this->_redirect(HOST."/watch?uid=".$uid);
        }
        else {
            echo 'False';
        }
    }

    public function resethiredayAction()
    {
    	$uid = $this->_request->getParam('uid');
    	
    	//重置用户酒馆 普通刷新次数
    	$userHireData = Hapyfish2_Alchemy_HFC_Hire::getHireData($uid);
    	$userHireInfo = $userHireData['data'];
    	$userHireInfo[0]['times'] = 20;
		$userHireData['data'] = $userHireInfo;
    	
    	$ok = Hapyfish2_Alchemy_HFC_Hire::updateHireData($uid, $userHireData);
    	 
    	if ($ok) {
    		echo 'OK';
    	}
    	else {
    		echo 'False';
    	}
    }
    
    public function resethireAction()
    {
    	$uid = $this->_request->getParam('uid');

    	
    	//用户酒馆佣兵信息
    	$userHireInfo = Hapyfish2_Alchemy_HFC_Hire::getHireData($uid);
    	
    	$nowTm = time();
    	$data0 = array('times' => 10,
    			'endTime' => 0,
    			'price' => 200,
    			'fastGemPrice' => 10);
    	$data1 = array('times' => 0,
    			'endTime' => 24*60*60 + $nowTm,
    			'price' => 200,
    			'fastGemPrice' => 50);
    	$data2 = array('times' => 0,
    			'endTime' => 2*24*60*60 + $nowTm,
    			'price' => 100,
    			'fastGemPrice' => 200);
    	$data3 = array('times' => 0,
    			'endTime' => 5*24*60*60 + $nowTm,
    			'price' => 50,
    			'fastGemPrice' => 500);
    	$data = array('0' => $data0,
	    			'1' => $data1,
	    			'2' => $data2,
	    			'3' => $data3);
    	$userHireInfo['data'] = $data;
    	$userHireInfo['first_refresh_2'] = 0;
    	$userHireInfo['first_refresh_3'] = 0;
    	
    	//更新用户酒馆佣兵信息
    	$ok = Hapyfish2_Alchemy_HFC_Hire::updateHireData($uid, $userHireInfo);
    	
    	if ($ok) {
    		echo 'OK';
    	}
    	else {
    		echo 'False';
    	}
    }
    
    public function dismerAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$id = $this->_request->getParam('id');
    	
    	//更新站位信息
    	/* $fightCorps = Hapyfish2_Alchemy_Cache_FightCorps::getFightCorpsInfo($uid);
    	var_dump($fightCorps);
    	foreach ( $fightCorps as $pos => $v ) {
    		if ( $v == $id ) {
    			unset($fightCorps[$pos]);
    		}
    	}
    	Hapyfish2_Alchemy_Cache_FightCorps::saveFightCorpsInfo($uid, $fightCorps);
    	 */

    	//原有站位信息
    	$fightCorps = Hapyfish2_Alchemy_Cache_FightCorps::getFightCorpsInfo($uid);
    	$oldFightCorps = array();
    	foreach ( $fightCorps as $kC => $vC ) {
    		$oldFightCorps[$kC] = $vC;
    	}
    	foreach ( $oldFightCorps as $pos => $v ) {
    		if ( $v == $id ) {
    			unset($oldFightCorps[$pos]);
    		}
    	}
    	Hapyfish2_Alchemy_Cache_FightCorps::saveFightCorpsInfo($uid, $oldFightCorps);
    	
    	return 1;
    }
    
    public function resettrainingAction()
    {
        $uid = $this->_request->getParam('uid');
        
        //所有训练信息列表
        $userTra = Hapyfish2_Alchemy_HFC_Training::getAll($uid);
        $traList = $userTra['list'];
        
        foreach ( $traList as $v ) {
        	Hapyfish2_Alchemy_HFC_Training::delOne($uid, $v['mid']);
        }
        echo 'OK';
        exit;
    }
    
    public function addinviteAction()
    {
        $uid = $this->_request->getParam('uid');
        $fid = $this->_request->getParam('fid');
        
        $ok = Hapyfish2_Alchemy_Bll_InviteLog::add($uid, $fid);
        
        if ($ok) {
            echo 'OK';
        }
        else {
            echo 'False';
        }
        exit;
    }
    
    public function resetinviteAction()
    {
        $uid = $this->_request->getParam('uid');
        
		$dalLog = Hapyfish2_Alchemy_Dal_InviteLog::getDefaultInstance();
		$dalLog->clear($uid);
		$ok = Hapyfish2_Alchemy_Bll_InviteLog::reloadAll($uid);
        if ($ok) {
            echo 'OK';
        }
        else {
            echo 'False';
        }
        exit;
    }
    
    public function getinviteAction()
    {
        $uid = $this->_request->getParam('uid');
        
        $rst = Hapyfish2_Alchemy_Bll_InviteLog::get($uid);
        echo json_encode($rst);
        exit;
    }
    
    public function resetarenaAction()
    {
        $uid = $this->_request->getParam('uid');
        
		$userScore = Hapyfish2_Alchemy_Cache_Arena::getUserScore($uid);
        
		$ok = Hapyfish2_Alchemy_Bll_Arena::resetUserArena($uid, $userScore);
		
        if ($ok) {
            echo 'OK';
        }
        else {
            echo 'False';
        }
        exit;
    }
    
    public function resetarenauserprizeAction()
    {
        $uid = $this->_request->getParam('uid');

        $userArena = Hapyfish2_Alchemy_Cache_Arena::getUserArena($uid);
        $userArena['prizeGetted'] = 0;
        
        $ok = Hapyfish2_Alchemy_Cache_Arena::updateUserArena($uid, $userArena);
        
        if ($ok) {
            echo 'OK';
        }
        else {
            echo 'False';
        }
        exit;
    }

    public function updatearenascoreAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$score = $this->_request->getParam('score');
    
    	$ok = Hapyfish2_Alchemy_Cache_Arena::updateUserScore($uid, $score);
    	
    	if ($ok) {
    		echo 'OK';
    	}
    	else {
    		echo 'False';
    	}
    	exit;
    }
    
    public function getarenaprizeAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$rank = $this->_request->getParam('rank');

    	$prizeBasic = Hapyfish2_Alchemy_Cache_BasicExt::getArenaPrizeByRank($rank);
    	 
    	$addItems = array('coin' => $prizeBasic['coin'],
    			'feats' => $prizeBasic['feats']);
    	
    	if ($addItems) {
    		echo json_encode($addItems);

    		echo '<br/>';
    		echo 'OK';
    	}
    	else {
    		echo 'False';
    	}
    	exit;
    }
    
    public function resetvipdailyawardAction()
    {
        $uid = $this->_request->getParam('uid');
        
		$ok = Hapyfish2_Alchemy_Cache_Vip::setVipDailyAward($uid, 'Y');
		
        if ($ok) {
            echo 'OK';
        }
        else {
            echo 'False';
        }
        exit;
    }
    
    public function addarenacountAction()
    {
        $uid = $this->_request->getParam('uid');
        $num = $this->_request->getParam('num', 100);
        
		$userArena = Hapyfish2_Alchemy_Cache_Arena::getUserArena($uid);
		$userArena['challengeTimes'] = $num;
		$ok = Hapyfish2_Alchemy_Cache_Arena::updateUserArena($uid, $userArena);
		
        if ($ok) {
            echo 'OK';
        }
        else {
            echo 'False';
        }
        exit;
    }
    
    public function addtaskAction()
    {
        $uid = $this->_request->getParam('uid');
        $taskId = $this->_request->getParam('taskId', null);
        
        $ok = Hapyfish2_Alchemy_Bll_Test::addTask($uid, $taskId);
        if ($ok) {
            echo 'OK';
        }
        else {
            echo 'False';
        }
        exit;
    }

    public function delcomtaskAction()
    {
    	$uid = $this->_request->getParam('uid');
    	$taskId = $this->_request->getParam('taskId', null);
    
    	$ok = Hapyfish2_Alchemy_Cache_Task::delCompleteTask($uid, $taskId);
    	if ($ok) {
    		echo 'OK';
    	}
    	else {
    		echo 'False';
    	}
    	exit;
    }
    
    public function resetmercenayAction()
    {
        $uid = $this->_request->getParam('uid');
        $mid = $this->_request->getParam('mid');
        
        $ok = Hapyfish2_Alchemy_Bll_Recoup::resetMercenary($uid, $mid);
        if ($ok) {
            echo 'OK';
        }
        else {
            echo 'False';
        }
        exit;
    }
    
    public function deltaskAction()
    {
        $uid = $this->_request->getParam('uid');
        $taskId = $this->_request->getParam('taskId', null);
        
        $ok = Hapyfish2_Alchemy_Bll_Test::delTask($uid, $taskId);
        if ($ok) {
            echo 'OK';
        }
        else {
            echo 'False';
        }
        exit;
    }
    
    public function getfinishtaskAction()
    {
        $uid = $this->_request->getParam('uid');
        
        $list = Hapyfish2_Alchemy_Cache_Task::getIds($uid);
        echo json_encode($list);
        echo '<br/>';
        if ($ok) {
            echo 'OK';
        }
        else {
            echo 'False';
        }
        exit;
    }
    
    public function addtasktestAction()
    {
        $uid = $this->_request->getParam('uid', null);
        $taskId = $this->_request->getParam('taskId', null);
        
        $ok = Hapyfish2_Alchemy_Bll_Test::addNewTask($uid, $taskId);
        
        if ($ok) {
        	echo $ok;
        	echo 'OK';
        	//$this->_redirect(HOST."/watch?uid=".$uid);
        }
        else {
        	echo $ok;
        	echo 'False';
        }
        exit;
    }
    
    public function completetaskAction()
    {
        $uid = $this->_request->getParam('uidTask', null);
        $taskId = $this->_request->getParam('taskId', null);
        
        $ok = Hapyfish2_Alchemy_Bll_Test::completeTask($uid, $taskId, 0);
        if ($ok) {
            echo $ok;
            echo 'OK';
            //$this->_redirect(HOST."/watch?uid=".$uid);
        }
        else {
            echo $ok;
            echo 'False';
        }
        exit;
    }
    
	public function joinuserAction()
	{
		$uid = $this->_request->getParam('uid', 0);
		
		$ok = Hapyfish2_Alchemy_Bll_User::resetUser($uid);
		
		if ($ok) {
			echo 'OK';
		}
		else {
			echo 'False';
		}
	}
		
	public function addtaskawardAction()
	{
		$uid = $this->_request->getParam('uid', 1);
		$tid = $this->_request->getParam('tid', 1);
		
		$ok = Hapyfish2_Alchemy_Bll_Test::completeTask($uid, $tid);
	
		if ($ok) {
			echo 'OK';
		}
		else {
			echo 'False';
		}
	}
	
	
	public function updateheroAction()
	{
		$uid = $this->_request->getParam('uid', 1);
		$id = $this->_request->getParam('id', 1);
		$type = $this->_request->getParam('type', 1);

		if ( $type == 1 ) {
			$ok = Hapyfish2_Alchemy_Bll_Hero::addHeroMercenary($uid, $id);
		}
		else {
			$ok = Hapyfish2_Alchemy_Bll_Hero::removeHeroMercenary($uid, $id);
		}
		if ( $ok ) {
			echo 'OK';
		}
		else {
			echo 'False';
		}
		exit;
	}
	
	public function addmonsterAction()
	{
		$uid = $this->_request->getParam('uid', 1);
		$cid = $this->_request->getParam('cid', 1);
		
	
        //添加遇到怪物记录，并判断是否有首杀奖励
            $addMonster = Hapyfish2_Alchemy_HFC_Monster::addMonster($uid, $cid);
            if ( $addMonster ) {
            	echo 1;
		        $data['award_conditions'] = array_merge($data['award_conditions'], $data['first_award_conditions']);
		        $enemySide[$key] = $data;
            }
		echo 2;
		exit;
	}
	
	public function updateprotalAction()
	{
		$uid = $this->_request->getParam('uid', 1);
		$pid = $this->_request->getParam('pid', 1);
		$type = $this->_request->getParam('type', 1);

		if ( $type == 1 ) {
			$item = Hapyfish2_Alchemy_HFC_MapCopy::getOpenPortal($uid);
		}
		else {
			$item = Hapyfish2_Alchemy_HFC_MapCopy::addOpenPortal($uid, $pid);
		}
		echo json_encode($item);
		exit;
	}

	
	public function clearopenportalAction()
	{
		$uid = $this->_request->getParam('uid', 1);
		
		Hapyfish2_Alchemy_HFC_MapCopy::updateOpenPortal($uid, array());
		echo 'OK';
		exit;
	}
	
	public function updatepersonAction()
	{
		$uid = $this->_request->getParam('uid', 1);
		$type = $this->_request->getParam('type', 1);
		$id = $this->_request->getParam('id', 1);
		
		if ( $type == 1 ) {
			$ok = Hapyfish2_Alchemy_Bll_Person::addPerson($uid, $id);
		}
		else if ( $type == 2 ) {
			$ok = Hapyfish2_Alchemy_Bll_Person::removePerson($uid, $id);
		}
		else if ( $type == 3 ) {
			$ok = Hapyfish2_Alchemy_Bll_Person::resetPerson($uid, $id, 1);
		}
		else if ( $type == 4 ) {
			$ok = Hapyfish2_Alchemy_Bll_Person::resetPerson($uid, $id, 2);
		}
		else if ( $type == 5 ) {
			$info = array('add_person' => array(), 'remove_person' => array());
			$ok = Hapyfish2_Alchemy_HFC_Person::updatePerson($uid, $info);
		}
		echo $ok;
		exit;
	}
	
	public function unlockworldmapAction()
	{
		$uid = $this->_request->getParam('uid', 1);
		$mapId = $this->_request->getParam('mapId', 1);
		
		$ok = Hapyfish2_Alchemy_Bll_WorldMap::setWorldMapOpened($uid, $mapId);
		if ($ok) {
			echo 'OK';
		}
		else {
			echo 'False';
		}
		exit;
	}

	/**
	 * 解锁一个功能按钮
	 */
	public function unlockfuncAction()
	{
		$uid = $this->uid;
		$func = $this->_request->getParam('func');
	
		$status = Hapyfish2_Alchemy_Bll_Help::unlockFunc($uid, $func);
		if ($status) {
			echo 'OK';
		}
		else {
			echo 'False';
		}
		exit;
	}
	
	public function getfuncAction()
	{
		$uid = $this->_request->getParam('uid', 1);
		
		$result = Hapyfish2_Alchemy_Bll_Help::getFunc($uid);
		
		echo json_encode($result);
		exit;	
	}
	
	public function clearfuncAction()
	{
		$uid = $this->_request->getParam('uid', 1);
		$temp = 'order,itembox,diy,goVillage,roleInfo,worldMap,roleWork,roleInfoSkill,roleInfoTrain,hire,fix,heal,strengthen';
		$data = explode(',', $temp);
		Hapyfish2_Alchemy_HFC_Help::updateUnlockFunc($uid, $data);
		
		echo 'ok';	
	}
	
	public function finishfuncAction()
	{
		$uid = $this->_request->getParam('uid', 1);
		$data = array();
		Hapyfish2_Alchemy_HFC_Help::updateUnlockFunc($uid, $data);
		
		echo 'ok';	
	}
	
	public function gethelpAction()
	{
		$uid = $this->_request->getParam('uid', 1);
		
		$result = Hapyfish2_Alchemy_Bll_Help::getHelp($uid);
		
		echo json_encode($result);
		exit;	
	}
	
	public function clearhelpAction()
	{
		$uid = $this->_request->getParam('uid', 1);
		
		$userHelp = array('id'=>1,'idx'=>1,'status'=>1,'finish_ids'=>null);
		Hapyfish2_Alchemy_HFC_Help::update($uid, $userHelp);
		
		echo 'ok';		
	}
	
    /**
     * 完成所有引导步骤
     */
	public function completehelpAction()
	{
		$uid = $this->_request->getParam('uid', 1);
		
		$userHelp = array('id'=>10,'idx'=>1,'status'=>0,'finish_ids'=>'1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34');
		Hapyfish2_Alchemy_HFC_Help::update($uid, $userHelp);
		
		$data = array();
		Hapyfish2_Alchemy_HFC_Help::updateUnlockFunc($uid, $data);
		
		$this->_redirect(HOST."/watch?uid=".$uid);
	}
	
    /**
     * 完成当前引导步骤
     */
    public function completecurhelpAction()
    {
        $uid = $this->uid;

        Hapyfish2_Alchemy_Bll_Help::completeHelp($uid);
        
        $this->_redirect(HOST."/watch?uid=".$uid);
    }
    
	public function finishhelpAction()
	{
		$uid = $this->_request->getParam('uid', 1);
		
		$userHelp = array('id'=>10,'idx'=>1,'status'=>0,'finish_ids'=>'1,2,3,4,5,6,7,8,9,10');
		Hapyfish2_Alchemy_HFC_Help::update($uid, $userHelp);
				
		echo 'ok';		
	}
	
	public function unlockworkAction()
	{
		$uid = $this->_request->getParam('uid', 1);
		$id = $this->_request->getParam('id', 1);
		
		Hapyfish2_Alchemy_Bll_MercenaryWork::setWorkOpened($uid, $id);
		echo 'ok';
	}
	
	public function getworkawardAction()
	{
		$uid = $this->_request->getParam('uid', 1);
		$id = $this->_request->getParam('id', 1);
		
		$basicWork = Hapyfish2_Alchemy_Cache_Basic::getMercenaryWork($id);
		$awrads = Hapyfish2_Alchemy_Bll_MercenaryWork::_getRandomAward($uid, $basicWork);
		
		$awards = json_encode($awrads);
		
		echo $awards;
		var_dump('<br/>');
		
		
		
		exit;
	}
	
	public function initfriendAction()
	{
		$orderLevel = $this->_request->getParam('level', 1);
		$orderListByLevel = Hapyfish2_Alchemy_Cache_Basic::getOrderListByLevel($orderLevel);
	
			$tempAry = array_slice($orderListByLevel, 0, 1);
			$order = $tempAry[0];
			
			echo json_encode($order);
			exit;
	}
	
    /**
     * 初始化静态信息
     */
    public function initstaticAction()
    {
        header('Cache-Control: max-age=31104000');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31104000). ' GMT');
		$gz = $this->_request->getParam('gz', 0);
		if ($gz == 1) {
			header('Content-Type: application/octet-stream');
			echo Hapyfish2_Alchemy_Bll_BasicInfo::getInitVoData('1.0.1', true);
		}
		else {
			echo Hapyfish2_Alchemy_Bll_BasicInfo::getInitVoData('1.0.1');
		}
		exit;
    }
	
	public function adddialogAction()
	{
        $uid = $this->_request->getParam('uid');
        $id = $this->_request->getParam('id');
        
        /*$npcVo = array();
			$npcVo[] = array('id' => 1,
							 'chatId' => 1,
							 'chats' => 1,
							 'chatState' => 1);		//是否已阅读,1:未阅读,2:已阅读
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'npcListChange', $npcVo);*/
		
		Hapyfish2_Alchemy_Bll_Story::triggerDialogById($uid, $id);
		Hapyfish2_Alchemy_Bll_UserResult::removeField($uid, 'npcListChange');
		
		$sceneVo = array();
	    //对白NPC
        $npcVo = Hapyfish2_Alchemy_Bll_Story::getNpcVo($uid, 101);
        if (!empty($npcVo)) {
        	$sceneVo['npcList'] = $npcVo;
        }
        
	    //$resultVo = array('scene' => $sceneVo);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'scene', $sceneVo);
        
		$status = 1;
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
	}
	
	public function delweaponbycidAction()
	{
        $uid = $this->_request->getParam('uid');
        $cid = $this->_request->getParam('cid');
        $num = $this->_request->getParam('num', 1);
        
        $removeResult =	Hapyfish2_Alchemy_HFC_Weapon::delWeaponByCid($uid, $cid, $num);
        
        echo 'OK';
	}
	
	public function gettipAction()
	{
        $coin = $this->_request->getParam('coin');
        $satisfaction = $this->_request->getParam('s');
		$tip = Hapyfish2_Alchemy_Bll_Order::getTip($coin, $satisfaction);
		echo $tip;
	}
	
	public function addallscrollAction()
	{
        $uid = $this->_request->getParam('uid');
        
        $list = Hapyfish2_Alchemy_Cache_Basic::getScrollList();
        foreach ( $list as $v ) {
        	$cid = $v['cid'];
        	$count = 50;
        	$result = Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $cid, $count);
        }
        echo 'OK';
	}
	
	public function getinitdecoAction()
	{
        $uid = $this->_request->getParam('uid');
        
        $list = Hapyfish2_Alchemy_HFC_Decor::getScene($uid);
        $newList = array();
        $id = 1;
        foreach ( $list as $v ) {
        	$newList[] = array('id' => $id,
        					   'cid' => $v['cid'],
        					   'x' => $v['x'],
        					   'z' => $v['z'],
        					   'm' => $v['m']);
        	$id++;
        }
		$this->echoResult($newList);
		
		echo '<br/><br/>wall-floor:<br/>';
	}
	
	public function updateweaponAction()
	{
        $uid = $this->_request->getParam('uid');
        $wid = $this->_request->getParam('wid');
        $durability = $this->_request->getParam('d');
        
        $weapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $wid);

        $weapon['durability'] = $durability;
        $ok = Hapyfish2_Alchemy_HFC_Weapon::updateOne($uid, $wid, $weapon);

        echo 'OK';
	}
	
	public function getsatisfactionAction()
	{
        $uid = $this->_request->getParam('uid');
        $r = Hapyfish2_Alchemy_HFC_Order::getSatisfaction($uid);
        $result['satisfaction'] = $r;
		$this->echoResult($result);
	}
	
	public function friendorderAction()
	{
        $uid = $this->_request->getParam('uid');
        $fid = $this->_request->getParam('fid');
        
        $order = Hapyfish2_Alchemy_Bll_Order::getFriendOrder($uid, $fid);
        $result['order'] = $order;
		$this->echoResult($result);
	}
	
	public function clearfriendorderAction()
	{
        $uid = $this->_request->getParam('uid');
	
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
		
		echo 'ok';
	}
	
	public function addfeedAction()
	{
        $uid = $this->_request->getParam('uid');
		try {
			//insert minifeed
			$minifeed = array('uid' => $uid,
	                          'template_id' => 1,
	                          'actor' => $uid,
	                          'target' => $uid,
	                          'title' => array('name' => $uid),
	                          'type' => 1,
	                          'create_time' => time());
			Hapyfish2_Alchemy_Bll_Feed::insertMiniFeed($minifeed);
		} catch (Exception $e) {
		}
		echo 'ok';
	}

	public function updaterolelevelAction()
	{
		$uid = $this->_request->getParam('uid');
		$roleId = $this->_request->getParam('id');
		$level = $this->_request->getParam('level');
	
		if ( $roleId == 0 ) {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
			$userMercenary['level'] = $level;
			$exp = Hapyfish2_Alchemy_Cache_Basic::getMercenaryLevelExp($level);
			$userMercenary['exp'] = $exp + 1;
			
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
		}
		else {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $roleId);
			$userMercenary['level'] = $level;
			
			$exp = Hapyfish2_Alchemy_Cache_Basic::getMercenaryLevelExp($level);
			$userMercenary['exp'] = $exp + 1;
			
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $roleId, $userMercenary, true);
		}
		if ($ok) {
			echo 'ok';
		}
		else {
			echo 'false';
		}
	}
	public function updateroleexpAction()
	{
        $uid = $this->_request->getParam('uid');
        $roleId = $this->_request->getParam('id');
        $exp = $this->_request->getParam('exp');
        
		if ( $roleId == 0 ) {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
			$userMercenary['exp'] += $exp;
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
		}
		else {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $roleId);
			$userMercenary['exp'] += $exp;
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $roleId, $userMercenary, true);
		}
		if ($ok) {
			echo 'ok';
		}
		else {
			echo 'false';
		}
	}
	
	public function updateexpAction()
	{
        $uid = $this->_request->getParam('uid');
        $exp = $this->_request->getParam('exp', 1);
        
        $ok = Hapyfish2_Alchemy_HFC_User::incUserExp($uid, $exp);
        
		if ($ok) {
			echo 'ok';
		}
	}
	
	public function updatelevelAction()
	{
        $uid = $this->_request->getParam('uid');
        $id = $this->_request->getParam('id', -1);
        $level = $this->_request->getParam('level');
        $exp = $this->_request->getParam('exp', 1);
        
        if ( $id == HOME_ID ) {
			$ok = Hapyfish2_Alchemy_HFC_User::updateUserHomeLevel($uid, $level);
        }
        else if ( $id == TAVERN_ID ) {
        	$userTavernLevel = Hapyfish2_Alchemy_HFC_User::getUserTavernLevelList($uid);
        	$userTavernLevel['tavern_level'] = $level;
			$ok = Hapyfish2_Alchemy_HFC_User::updateUserTavernLevel($uid, $userTavernLevel);
        }
        else if ( $id == TAVERN_CITY_ID ) {

        	$userTavernLevel = Hapyfish2_Alchemy_HFC_User::getUserTavernLevelList($uid);
        	$userTavernLevel['tavern_city_level'] = $level;
			$ok = Hapyfish2_Alchemy_HFC_User::updateUserTavernLevel($uid, $userTavernLevel);

        }
        else if ( $id == 0 ) {
        	Hapyfish2_Alchemy_HFC_User::updateUserExp($uid, $exp);
        	$ok = Hapyfish2_Alchemy_HFC_User::updateUserLevel($uid, $level);
        }
        else if ( $id == SMITHY_ID ) {
			$ok = Hapyfish2_Alchemy_HFC_User::updateUserSmithyLevel($uid, $level);
        }
		if ($ok) {
			echo 'ok';
		}
		exit;
	}
	
	public function updaterolerpAction()
	{
        $uid = $this->_request->getParam('uid');
        $rp = $this->_request->getParam('rp');
        
        $userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
        $userMercenary['rp'] = $rp;
        
        $ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
        
		if ($ok) {
			echo 'ok';
		}
	}
	
	public function hireAction()
	{
        $uid = $this->_request->getParam('uid');
        $id = $this->_request->getParam('pos');
        
		//所有雇佣位信息
		$hireList = Hapyfish2_Alchemy_HFC_Hire::getAll($uid);
		$hireInfo = $hireList[$id];
		
		$newHire = Hapyfish2_Alchemy_Bll_Mercenary::getNewHire($uid, $id, $hireInfo);
		//更新雇佣位
		$ok = Hapyfish2_Alchemy_HFC_Hire::updateOne($uid, $id, $newHire);
	}
	
	public function getrandorderAction()
	{
        $uid = $this->_request->getParam('uid');
        $userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
        
		$result = Hapyfish2_Alchemy_Bll_Order::getRandOrderInfo($uid, -1);
		
		$this->echoResult($result);
	}
	
	public function addillustartionAction()
	{
        $uid = $this->_request->getParam('uid');
        $id = $this->_request->getParam('id');
		$result = Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $id);
		
        $this->echoResult($result);
	}
	
	public function clearillustartionAction()
	{
        $uid = $this->_request->getParam('uid');
		$result = Hapyfish2_Alchemy_Cache_Illustrations::updateUserIllustrations($uid, array());
		
        $this->echoResult($result);
	}
	
	
	
	public function getweaponAction()
	{
		
        $uid = $this->_request->getParam('uid');
        $id = $this->_request->getParam('id');
		$result = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $id);
		
        $this->echoResult($result);
	}
		
    //用户添加合成术
    public function addusermixAction()
    {
        $uid = $this->_request->getParam('uid');
        $mixCid = $this->_request->getParam('mixCid');
    	
        $result = Hapyfish2_Alchemy_HFC_Mix::addUserMix($uid, $mixCid);
        
        if ( $result ) {
        	echo 'OK';
        	exit;
        }
        echo 'False';
        exit;
    }
    
    //清空用户合成术
    public function clearusermixAction()
    {
        $uid = $this->_request->getParam('uid');

        $result = Hapyfish2_Alchemy_HFC_Mix::updateUserMix($uid, array());

        if ( $result ) {
        	echo 'OK';
        	exit;
        }
        echo 'False';
        exit;
    }
    
    public function addalldecoAction()
    {
        $uid = $this->_request->getParam('uid');
        //10631  10562  10533
        $list = Hapyfish2_Alchemy_Cache_Basic::getDecorList();
        foreach ( $list as $v ) {
        	Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $v['cid'], 50);
        }
        echo 'OK';
        exit;
    }
    
    
    
    //添加用户物品
    public function adduseritemAction()
    {
        $uid = $this->_request->getParam('uid');
        $cid = $this->_request->getParam('cid');
        $count = $this->_request->getParam('count', 1);
    	
        $itemType = substr($cid, -2, 1);
        if ( $itemType == 4 ) {
        	$furnace = array('uid' => $uid,
        					 'furnace_id' => $cid,
        					 'x' => 1,
        					 'z' => 1,
        					 'cid' => 0,
        					 'start_time' => 0,
        					 'remaining_time' => 0,
        					 'cur_probability' => 0,
        					 'num' => 0,
        					 'status' => 1);
        	
        	$result = Hapyfish2_Alchemy_HFC_Furnace::addOne($uid, $furnace);
        }
        else {
        	$result = Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $cid, $count);
        }
        
        if ( $result ) {
        	echo 'OK';
        	exit;
        }
        echo 'False';
        exit;
    }
    
    public function delweaponAction()
    {
        $uid = $this->_request->getParam('uid');
        $id = $this->_request->getParam('id');
        $result = Hapyfish2_Alchemy_HFC_Weapon::delWeapon($uid, $id);
        
        if ( $result ) {
        	echo 'OK';
        	exit;
        }
        echo 'False';
        exit;
    }
    
    public function clearweaponAction()
    {
        $uid = $this->_request->getParam('uid');
        $cid = $this->_request->getParam('cid');
        $result = Hapyfish2_Alchemy_HFC_Weapon::clearWeaponByCid($uid, $cid);
        
        if ( $result ) {
        	echo 'OK';
        	exit;
        }
        echo 'False';
        exit;
    }
    
    //注册用户
	public function registerAction()
	{
	    $puid = $this->_request->getParam('puid');
		$uidInfo = Hapyfish2_Platform_Cache_UidMap::getUser($puid);
		if (!$uidInfo) {
    		$uidInfo = Hapyfish2_Platform_Cache_UidMap::newUser($puid);
    		if (!$uidInfo) {
    			echo 'inituser error: 1';
    			exit;
    		}
		}
		$uid = $uidInfo['uid'];
		$name = $this->_request->getParam('name');
		if (empty($name)) {
			$name = '测试' . $uid;
		}
		$id = $this->_request->getParam('id');
		if (empty($id)) {
			$id = 1;
		} else {
			$id = (int)$id;
			if ($id <= 0 || $id > 6) {
				$id = 1;
			}
		}
		$figureurl = $this->_request->getParam('figureurl');
		if (empty($figureurl)) {
			$figureurl = 'http://hdn.xnimg.cn/photos/hdn521/20091210/1355/tiny_E7Io_11729b019116.jpg';
		}

        $user = array();
        $user['uid'] = $uid;
        $user['puid'] = $puid;
        $user['name'] = $name;
        $user['figureurl'] = $figureurl;
        $user['gender'] = rand(0,1);
		Hapyfish2_Platform_Bll_User::addUser($user);

		$ok = Hapyfish2_Alchemy_Bll_User::joinUser($uid);
		if ($ok) {
			Hapyfish2_Alchemy_Bll_User::initRole($uid, $id);
			echo 'Success: ' . $uid;
		} else {
			echo 'Failure';
		}
		exit;
	}

	public function  updatefriendAction()
	{
		$uid = $this->_request->getParam('uid');
		$fids = $this->_request->getParam('fids');
		$fids = explode(',', $fids);

		Hapyfish2_Platform_Bll_Friend::updateFriend($uid, $fids);
		echo '好友列表更新';
		exit;
	}
    
    public function getfurnaceAction()
    {
    	$uid = $this->_request->getParam('uid', 1011);
        $fid = $this->_request->getParam('fid', 0);
        if ($fid>0) {
	    	$result = Hapyfish2_Alchemy_HFC_Furnace::getOne($uid, $fid);
        }
        else if ($fid == -1) {
	    	$result = Hapyfish2_Alchemy_HFC_Furnace::getCurMixs($uid);
        }
        else {
	    	$result = Hapyfish2_Alchemy_HFC_Furnace::getOnRoom($uid);
        }
    	$this->echoResult(array('result' => $result));
    }
    
    //添加宝石
    public function addgemAction()
    {
        $uid = $this->_request->getParam('uid');
        $count = $this->_request->getParam('count', 1);
        $type = $this->_request->getParam('type', 1);
        
        if ( $type==2 ) {
        	$gemInfo = array('cost' => $count);
        	$ok = Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo,1);
        }
        else {
        	$gemInfo = array('gem' => $count);
    		$ok = Hapyfish2_Alchemy_Bll_Gem::add($uid, $gemInfo,3);
        }
    	if ( $ok ) {
	    	echo 'OK';
	    	exit;
    	}
    	echo 'False';
    	exit;
    }

    //添加金币
    public function addcoinAction()
    {
        $uid = $this->_request->getParam('uid');
        $count = $this->_request->getParam('count', 1);
        $type = $this->_request->getParam('type', 1);
                
        if ($type == 2) {
    		$ok = Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $count,1);
        }
        else 
        {
    		$ok = Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $count,1);
        }
    	if ( $ok ) {
	    	echo 'OK';
	    	exit;
    	}
    	echo 'False';
    	exit;
    }

    //行动力
    public function addspAction()
    {
        $uid = $this->_request->getParam('uid');
        $count = $this->_request->getParam('count', 1);
        $maxcount = $this->_request->getParam('maxcount', $count);
                
        $spInfo = array('sp' => $count, 'max_sp'=>$maxcount, 'sp_set_time'=>time());
    	$ok = Hapyfish2_Alchemy_HFC_User::updateUserSp($uid, $spInfo);
    	if ( $ok ) {
	    	echo 'OK';
	    	exit;
    	}
    	echo 'False';
    	exit;
    }

    //重置剧情
    public function clearstoryAction()
    {
        $uid = $this->_request->getParam('uid');
        $id = $this->_request->getParam('id', -1);
                
        if ($id == -1) {
        	$ok = Hapyfish2_Alchemy_HFC_Story::updateStory($uid, array(), true);
        }
        else {
        	$ok = Hapyfish2_Alchemy_HFC_Story::delStory($uid, $id);
        }
        
    	if ( $ok ) {
	    	echo 'OK';
	    	exit;
    	}
    	echo 'False';
    	exit;
    }
    
    public function startstoryAction()
    {
        $uid = $this->uid;
        $id = (int)$this->_request->getParam('id');

        $status = Hapyfish2_Alchemy_Bll_Story::startStory($uid, $id);
		if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    public function readstoryAction()
    {
        $uid = (int)$this->_request->getParam('uid');
        $storyId = (int)$this->_request->getParam('sid');
        
    	    //当前剧情check TODO::
			$userStory = Hapyfish2_Alchemy_HFC_Story::getStory($uid);
			if ( !isset($userStory[$storyId]) ) {
				echo '-200';
				return -200;
			}
			if ( $userStory[$storyId] == 1 ) {
				echo '-200';
				return -200;
			}
    	    $userStory[$storyId] = 1;
    	    $storyArray = array('list' => json_encode($userStory));
			Hapyfish2_Alchemy_HFC_Story::updateStory($uid, $storyArray);
			
		echo 'OK';
    }
    
    public function triggerdialogAction()
    {
        $uid = $this->_request->getParam('uid');
        $id = (int)$this->_request->getParam('id');
        $userLevel = (int)$this->_request->getParam('userLevel', 0);
        $fightLevel = (int)$this->_request->getParam('fightLevel', 0);

        if ( $userLevel > 0 ) {
        	$status = Hapyfish2_Alchemy_Bll_Story::triggerDialogByUserLevel($uid, $userLevel);
        }
        else if ( $fightLevel > 0 ) {
        	$status = Hapyfish2_Alchemy_Bll_Story::triggerDialogByFightLevel($uid, $fightLevel);
        }
        else {
        	$status = Hapyfish2_Alchemy_Bll_Story::triggerDialogById($uid, $id);
        }
		if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    	
    }
    
    public function readdialogAction()
    {
        $uid = $this->_request->getParam('uid');
        $id = (int)$this->_request->getParam('id');

        $status = Hapyfish2_Alchemy_Bll_Story::readDialog($uid, $id);
        
		if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    public function resetdialogAction()
    {
        $uid = $this->_request->getParam('uid');
        $id = $this->_request->getParam('id');

		$dialogInfo = Hapyfish2_Alchemy_Cache_Basic::getStoryDialog($id);
		if (!$dialogInfo) {
			return;
		}
		//玩家当前对白列表
		$userDialogList = Hapyfish2_Alchemy_HFC_Story::getDialog($uid);
		$sceneId = $dialogInfo['scene_id'];
		if (!isset($userDialogList[$sceneId])) {
			return;
		}
		$list = $userDialogList[$sceneId];
		$list[$id] = 1;
		$userDialogList[$sceneId] = $list;
		$ok = Hapyfish2_Alchemy_HFC_Story::updateDialog($uid, $userDialogList);
		echo $ok;
    }
    
    public function clearuniqueitemAction()
    {
        $uid = $this->_request->getParam('uid');
        
        $ok = Hapyfish2_Alchemy_Cache_UniqueItem::saveInfo($uid, array());
        echo $ok;
        exit;
    }
    
    public function cleardialogAction()
    {
        $uid = $this->_request->getParam('uid');

        $status = Hapyfish2_Alchemy_HFC_Story::updateDialog($uid, array());
        
		if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    public function getnpcvoAction()
    {
        $uid = $this->_request->getParam('uid');
        $sceneId = (int)$this->_request->getParam('id');

        $data = Hapyfish2_Alchemy_Bll_Story::getNpcVo($uid, $sceneId);
        $data = array('npcVo' => $data);
    	header("Cache-Control: no-store, no-cache, must-revalidate");
    	echo json_encode($data);
    	exit;
    }
    
    public function statmainAction()
    {
        $stat = $this->_request->getParam('stat', 301);
        $date = $this->_request->getParam('date', null);
    	$dir = '/home/admin/website/alchemy/renren/logs/';
    	
    	if (!$date) {
			$dtYesterday = strtotime("-1 day");
			$date = date('Ymd', $dtYesterday);
    	}
		
    	if ( $stat == 301 ) {
    		$rst = Hapyfish2_Stat_Bll_Mercenary::calHireMercenary($date, $dir);
    	}
    	else if ( $stat == 311 ) {
    		$rst = Hapyfish2_Stat_Bll_Order::calOrder($date, $dir);
    	}
    	else if ( $stat == 321 ) {
    		$rst = Hapyfish2_Stat_Bll_Item::calItem($date, $dir);
    	}
    	else if ( $stat == 331 ) {
    		$rst = Hapyfish2_Stat_Bll_Shop::calShop($date, $dir);
    	}
    	else if ( $stat == 341 ) {
    		$rst = Hapyfish2_Stat_Bll_Mix::calMix($date, $dir);
    	}
    	else if ( $stat == 361 ) {
			$prefix1 = 361;
			$dtYesterday = strtotime("-0 day");
			$logDate = date('Ymd', $dtYesterday);
		    //$logDate = '2011071600';
		    $dir = '/home/admin/stat/data/alchemy/kaixin/';
		    $nowTime = time();
		    $dayDate = date('Y-m-d 00:00:00', $nowTime);
		    $dayStatTime = strtotime($dayDate);
    	
		    $nowTime = time();
		    $nowHour = date('Y-m-d H:00:00', $nowTime);
		    $nowHourTime = strtotime($nowHour);
		    $day = date('Y-m-d 00:00:00', $nowTime);
		    $dayTime = strtotime($day);
		    $statTime = $dayTime;
		    for ( $i=0;$i<24;$i++ ) {
		    	if ( $statTime <= $nowHourTime ) {
					$rst = Hapyfish2_Stat_Bll_Statmainhour::calStatMainhour($logDate, $dir, $statTime);
					echo $logDate."-$prefix1-" . ($rst ? 'OK' : 'NG');
					$statTime += 3600;
		    	}
		    }
    		//$rst = Hapyfish2_Stat_Bll_Statmainhour::calStatMainhour($date, $dir);
    	}
    	else if ( $stat == 'flashload' ) {
			$dtYesterday = strtotime("-0 day");
			$logDate = date('Ymd', $dtYesterday);
		    //$logDate = '20110716';
		    $dir = '/home/admin/website/alchemy/renren/logs/';///home/admin/logs/weibo/stat-data/cLoadTm/
		
			$rst = Hapyfish2_Stat_Bll_CloadTm::calcDayData($logDate, $dir);
    	}
    	else if ( $stat == 'useraction' ) {
			$dtYesterday = strtotime("-0 day");
			$logDate = date('Ymd', $dtYesterday);
		    //$logDate = '20110716';
		    $dir = '/home/admin/website/alchemy/renren/logs/';
		
			$rst = Hapyfish2_Stat_Bll_UserAction::calcDayData($logDate, $dir);
    	}
		echo $date."-$prefix-" . ($rst ? 'OK' : 'NG');
		exit;
    }
    
    public function difflevelAction()
    { 
        $baseExp = $this->_request->getParam('exp');
        $levDiff = $this->_request->getParam('diff');
        
        if (!$baseExp) {
            return 0;
        }

        $retExp = $baseExp;
        if ($levDiff == 0) {
            return $retExp;
        }
        
        $expLevDiff = Hapyfish2_Alchemy_Cache_Basic::getFightExpLevDiff();
        $kl1 = $expLevDiff['kl1']/100;		//加成值1
        $kl2 = $expLevDiff['kl2']/100;		//加成值2
        $kd1 = $expLevDiff['kd1']/100;		//衰减值1
        $kd2 = $expLevDiff['kd2']/100;		//衰减值2
        $max = $expLevDiff['max']/100;		//上限值
        $min = $expLevDiff['min']/100;		//下限值
        
        //100	35	100	10	250	5
        
        if ($levDiff > 0) {
			$retExp = $baseExp*(1+pow($levDiff, $kl1)*$kl2);
        }
        else {
        	$retExp = $baseExp/(1+pow(abs($levDiff), $kl1)*$kd2);
        }
        
        $minExp = $baseExp*$min;
        $maxExp = $baseExp*$max;
	
        if ($retExp > $maxExp) {
            $retExp = $maxExp;
        }
        if ($retExp < $minExp) {
            $retExp = $minExp;
        }
        
        echo '公式计算结果：'.$retExp;
        echo '<br/>';
        echo '最终结果（四舍五入）：'.round($retExp);
        echo '<br/>';
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
		$k4 = 'alchemy:bas:yellowGift';
		$cache->delete($k);
		$cache->delete($k1);
		$cache->delete($k2);
		$cache->delete($k3);
		$cache->delete($k4);
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
	
	public function clearoaylogAction()
	{
		$uid = $this->_request->getParam('uid'); 
		$data['uid'] = $uid;
		$data['step'] = '[]';
		$data['type'] = 2;
		Hapyfish2_Alchemy_Cache_EventGift::updateFrishPay($data);
	    echo "Ok";
	    exit;
	}
	
	public function resettraposAction()
	{
		$uid = $this->_request->getParam('uid');
		$num = $this->_request->getParam('num');
	
		$ok = Hapyfish2_Alchemy_HFC_User::updateUserTrainingPosNum($uid, $num, true);
		echo $ok;
		exit;
	}
	
	public function regpayorderAction()
	{
		$uid = $this->_request->getParam('uid');
		$type = $this->_request->getParam('type');
	}
	
	public function completepayorderAction()
	{
		$uid = $this->_request->getParam('uid');
		$amount = $this->_request->getParam('amount');
		
		//充值送
		$content = Hapyfish2_Alchemy_Bll_Payment::chargeGift($uid, $amount);
		echo $content;
	}

	public function completehireAction()
	{
		$uid = $this->_request->getParam('uid');
		$type = $this->_request->getParam('type');

		//用户酒馆佣兵信息
		$userHireData = Hapyfish2_Alchemy_HFC_Hire::getHireData($uid);
		$userHireInfo = $userHireData['data'];
		
		if ( !isset($userHireInfo[$type]) ) {
			echo 'false';
			exit;
		}
		
		$hireInfo = $userHireInfo[$type];

		//计算完成时间
		$hireInfo['endTime'] = 0;
		$userHireInfo[$type] = $hireInfo;
		
		$userHireData['data'] = $userHireInfo;
		
	    //更新用户酒馆佣兵信息
	    $okUpdate = Hapyfish2_Alchemy_HFC_Hire::updateHireData($uid, $userHireData);
		if ($okUpdate) {
			echo 'ok';
			exit;
		}
		else {
			echo 'false';
			exit;
		}
	}
	
	public function starthelpAction()
	{
		$uid = $this->_request->getParam('uid');
		$help = $this->_request->getParam('help');

		$ok = Hapyfish2_Alchemy_Bll_Help::startHelp($uid, $help);
		
		if ($ok) {
			echo 'ok';
			exit;
		}
		else {
			echo 'false';
			exit;
		}
	}
	
	public function resetrecouptaskAction()
	{
		$uid = $this->_request->getParam('uid');

		$ok = Hapyfish2_Alchemy_Cache_User::setFirstRecoupTaskForNewHelp($uid, 'Y');

		echo 'ok';
		exit;
	}
	
	public function delstoryAction()
	{
		$uid = $this->_request->getParam('uid');
		$story = $this->_request->getParam('story');

		$ok = Hapyfish2_Alchemy_HFC_Story::delStory($uid, $story);

		if ($ok) {
			echo 'ok';
			exit;
		}
		else {
			echo 'false';
			exit;
		}
	}
	
	public function updatehirescoreAction()
	{
		$uid = $this->_request->getParam('uid');
		$score = $this->_request->getParam('score');

		//用户酒馆佣兵信息
		$userHireData = Hapyfish2_Alchemy_HFC_Hire::getHireData($uid);
		$userHireInfo = $userHireData['data'];
		$userHireData['score'] = $score;

		$ok = Hapyfish2_Alchemy_HFC_Hire::updateHireData($uid, $userHireData);
		
		if ($ok) {
			echo 'ok';
			exit;
		}
		else {
			echo 'false';
			exit;
		}
	}
	
	public function resetskilllistAction()
	{
		$uid = $this->_request->getParam('uid');
		$id = $this->_request->getParam('id', 0);

		if ( $id == 0 ) {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
		}
		else {
			$userMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $id);
		}
		
		$userMercenary['skill_list'] = array();
		

		//更新技能信息
		if ( $id == 0 ) {
			$ok = Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $userMercenary, true);
		}
		else {
			$ok = Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $id, $userMercenary);
		}
		
		if ($ok) {
			echo 'ok';
			exit;
		}
		else {
			echo 'false';
			exit;
		}
		
	}
	
	public function resetarenarankAction()
	{
		$ok = Hapyfish2_Alchemy_Bll_Test::resetArenaRank();

		if ($ok) {
			echo 'ok';
			exit;
		}
		else {
			echo 'false';
			exit;
		}
	}
	
	public function getscorechangeAction()
	{
		$type = $this->_request->getParam('type');
		$score = $this->_request->getParam('score');
		$rivalScore = $this->_request->getParam('rscore');
		
		$result = Hapyfish2_Alchemy_Bll_Arena::_getScoreChange($type, $score, $rivalScore);
		
		echo $result;
		exit;
	}
	
}