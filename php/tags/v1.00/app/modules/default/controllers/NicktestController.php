<?php

/**
 * Alchemy nicktest controller
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/02    Nick
 */
class NicktestController extends Hapyfish2_Controller_Action_Api
{
	
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
			$dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
			for ( $i=0;$i<4;$i++ ) {
				$allList = $dalUser->getAll($i);
				foreach ( $allList as $v ) {
					$uid = $v['uid'];
					$ok = Hapyfish2_Alchemy_Bll_User::resetUser($uid);
				}
			}
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
	
	public function beginfightAction()
	{
		$uid = $this->_request->getParam('uid', 1);
		$id = $this->_request->getParam('id', 1);
		
	    //$info = Hapyfish2_Alchemy_Cache_Fight::getFightInfo($uid);
	    //if (!$info || $info['status']) {
        $info = array();
		$info['uid'] = $uid;
		$info['fid'] = self::getNewId($uid);
		$info['type'] = 0;
		$info['status'] = 0;

		$aryRnd = array();
		for ($i=0; $i<20; $i++) {
            $aryRnd[] = mt_rand(1,1000);
		}

		$homeSide = Hapyfish2_Alchemy_Bll_FightCorps::getHomeSideUnitList($uid);
		if (!$homeSide) {
		    return -321;
		}

	    $enemySide = Hapyfish2_Alchemy_Bll_MapCopy::getEnemySideUnitList($uid, $id);
		if (!$enemySide) {
		    return -322;
		}

		//体力不足时不能战斗
		$userSpInfo = Hapyfish2_Alchemy_HFC_User::getUserSp($uid);
		if ( $userSpInfo['sp'] < 1 ) {
			return -323;
		}
		
		$info['rnd_element'] = $aryRnd;
		$info['home_side'] = $homeSide;
		$info['enemy_side'] = $enemySide;
		$info['content'] = array();
		$info['create_time'] = time();

		$usrScene = Hapyfish2_Alchemy_HFC_User::getUserScene($uid);
		$info['enemy_id'] = $usrScene['cur_scene_id'] . '-' . $id;
		$saveDb = (defined('ENABLE_FIGHT_DB_LOG') && ENABLE_FIGHT_DB_LOG);
        Hapyfish2_Alchemy_Cache_Fight::saveFightInfo($uid, $info, $saveDb);
	    //}

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

	    foreach ($enemySide as $key => $data) {
	        if ($data['is_boss']) {
    	        if ($data['talk']) {
                    $aryTalk[] = array((int)$data['matrix_pos'], $data['talk']);
                }
	        }

	        $data['award_conditions'] = array_merge($data['award_conditions'], $data['first_award_conditions']);
	        $enemySide[$key] = $data;
            //添加图鉴
            $illResult = Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $data['tid']);
            if ( $illResult['result']['status'] == 1 ) {
            	var_dump();
            	$data['award_conditions'] = 1;
            }
        }
		
        echo json_encode($enemySide);
	        
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
		$temp = 'order,itembox,diy,goVillage,roleInfo,worldMap,order,itembox,diy,goVillage,roleInfo';
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
	
	public function completehelpAction()
	{
		$uid = $this->_request->getParam('uid', 1);
		
		$userHelp = array('id'=>10,'idx'=>1,'status'=>0,'finish_ids'=>'1,2,3,4,5,6,7,8,9,10');
		Hapyfish2_Alchemy_HFC_Help::update($uid, $userHelp);
		
		$data = array();
		Hapyfish2_Alchemy_HFC_Help::updateUnlockFunc($uid, $data);
		
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
        
        $removeResult =	Hapyfish2_Alchemy_HFC_Weapon::delWeaponByCid($uid, $cid);
        
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
        	$ok = Hapyfish2_Alchemy_Bll_Gem::consume($uid, $gemInfo);
        }
        else {
        	$gemInfo = array('gem' => $count);
    		$ok = Hapyfish2_Alchemy_Bll_Gem::add($uid, $gemInfo);
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
    		$ok = Hapyfish2_Alchemy_HFC_User::decUserCoin($uid, $count);
        }
        else 
        {
    		$ok = Hapyfish2_Alchemy_HFC_User::incUserCoin($uid, $count);
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
		echo $date."-$prefix-" . ($rst ? 'OK' : 'NG');
		exit;
    }
    
}