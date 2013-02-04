<?php

class Hapyfish2_Alchemy_Bll_Help
{

    private static $_helpUnlock = array('1' => array('goVillage'),
                            '2' => array('itembox','diy'),
                            '3' => array('order'),
                            '4' => array('taskBar'),
                            '5' => array('worldMap'),
                            '6' => array('roleInfo'),
                            '7' => array('roleInfoSkill'),
                            '8' => array(),
                            '9' => array(),
                            '10' => array(),
                            '11' => array('roleInfoTrain'),
                            '12' => array('hire'),
                            '13' => array(),
                            '14' => array('fix'),
                            '15' => array('heal'),
                            '16' => array(),
                            '17' => array('strengthen')
    );
	
	/**
	 * 获取新手引导信息
	 * @param $uid
	 */
	public static function getHelp($uid)
	{
		$userHelp = Hapyfish2_Alchemy_HFC_Help::get($uid);
		return $userHelp;
	}
	
	/**
	 * 更新当前进行的引导索引
	 * @param int $uid
	 * @param int $idx,小步骤idx
	 */
	public static function updateHelp($uid, $idx)
	{
		$userHelp = Hapyfish2_Alchemy_HFC_Help::get($uid);
		if ( $userHelp['id'] == 0 || $userHelp['status'] == 0 ) {
			return -200;
		}
		
		$basicHelp = Hapyfish2_Alchemy_Cache_Basic::getHelp($userHelp['id']);
		$basicIdx = explode(',',$basicHelp['idx']);

		if ( !in_array($idx, $basicIdx) ) {
			return -200;
		}
		if ( $idx <= $userHelp['idx'] ) {
			return -200;
		}
		
		$userHelp['idx'] = $idx;
		$ok = Hapyfish2_Alchemy_HFC_Help::update($uid, $userHelp);
		if (!$ok) {
			return -200;
		}
		return 1;
	}
	
	/**
	 * 完成新手引导步骤
	 * @param int $uid
	 * @param int $id,大步骤id
	 */
	public static function completeHelp($uid)
	{
		$userHelp = Hapyfish2_Alchemy_HFC_Help::get($uid);
		if ( $userHelp['id'] == 0 || $userHelp['status'] != 1 ) {
			return -200;
		}
		$finishId = $userHelp['id'];
		$log = Hapyfish2_Util_Log::getInstance();
		if($finishId == 2){
			$aryLog = array($uid, 12);
		}else if($finishId == 3){
			$aryLog = array($uid, 13);
		}else if($finishId == 6){
			$aryLog = array($uid, 16);
		}else if($finishId == 8){
			$aryLog = array($uid, 19);
		}else if($finishId == 9){
			$aryLog = array($uid, 22);
		}else if($finishId == 11){
			$aryLog = array($uid, 25);
		}
		if(isset($aryLog)){
			$log->report('newuser', $aryLog);
		}
		$basicHelp = Hapyfish2_Alchemy_Cache_Basic::getHelp($userHelp['id']);
		
		//判断是否自动进入下一步新手引导
		$nextHelp = self::_getNextHelp($uid, $userHelp);
		if ( $nextHelp > 0 ) {
			$userHelp['id'] = $nextHelp;
			$userHelp['status'] = 1;
			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'showGuide', $nextHelp);
			
	        $unlockAry = self::$_helpUnlock[$nextHelp];
	        foreach ( $unlockAry as $func ) {
	            self::unlockFunc($uid, $func);
	        }
		}
		else {
			$userHelp['status'] = 0;
		}
		
		//更新已完成列表
		$finishIds = explode(',', $userHelp['finish_ids']);
		$finishIds[] = $finishId;
		$userHelp['finish_ids'] = implode(',', $finishIds);
		
		$userHelp['idx'] = 1;
		
		$ok = Hapyfish2_Alchemy_HFC_Help::update($uid, $userHelp);
		if (!$ok) {
			return -200;
		}
		
		//发送完成奖励
		$awards = json_decode($basicHelp['awards']);
		foreach ( $awards as $v ) {
			$cid = $v[0];
			$count = $v[1];
			Hapyfish2_Alchemy_Bll_Mix::addNewItem($uid, $cid, $count);
		}
		
		if ( $finishId == 1 ) {
			Hapyfish2_Alchemy_Bll_Story::startStory($uid, 41);
		}
		else if ( $finishId == 2 ) {
			Hapyfish2_Alchemy_Bll_Story::startStory($uid, 51);
		}
		else if ( $finishId == 3 ) {
			Hapyfish2_Alchemy_Bll_Help::unlockFunc($uid, 'taskBar');
			Hapyfish2_Alchemy_Bll_Story::triggerDialogById($uid, 2322);
			Hapyfish2_Alchemy_Bll_Story::startStory($uid, 61);
		}
		else if ( $finishId == 4 ) {
			//Hapyfish2_Alchemy_Bll_Story::startStory($uid, 71);
		}
		
		return 1;
	}
	
	public static function startHelp($uid, $newHelpId)
	{
        $userHelp = Hapyfish2_Alchemy_HFC_Help::get($uid);
        $userHelp['id'] = $newHelpId;
        $userHelp['idx'] = 1;
        $userHelp['status'] = 1;
        $ok = Hapyfish2_Alchemy_HFC_Help::update($uid, $userHelp);
        if ( $ok ) {
            Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'showGuide', $newHelpId);
        }
        
        $unlockAry = self::$_helpUnlock[$newHelpId];
        foreach ( $unlockAry as $func ) {
        	self::unlockFunc($uid, $func);
        }
        return true;
	}
	
	public static function getFunc($uid)
	{
		$userFunc = Hapyfish2_Alchemy_HFC_Help::getUnlockFunc($uid);
		return $userFunc;
	}
	
	/**
	 * 解锁功能按钮
	 * @param int $uid
	 * @param string $func,功能名
	 */
	public static function unlockFunc($uid, $func)
	{
		$userFunc = Hapyfish2_Alchemy_HFC_Help::getUnlockFunc($uid);
		if ( !in_array($func, $userFunc) ) {
			return -200;
		}
		
		foreach ( $userFunc as $k=>$v ) {
			if ( $v == $func ) {
				unset($userFunc[$k]);
			}
		}
		
		//unset($userFunc[$func]);
		$ok = Hapyfish2_Alchemy_HFC_Help::updateUnlockFunc($uid, $userFunc);
		if (!$ok) {
			return -200;
		}
		return 1;
	}
	
	public static function _getNextHelp($uid, $userHelp = null)
	{
		if (!$userHelp) {
			$userHelp = Hapyfish2_Alchemy_HFC_Help::get($uid);
		}
		
		$autoHelpAry = array(1,2,3,4,12);
		//$stopHelpAry = array(5,6,7,8,9,10,11,12,13,14,15,16,17);
		if ( in_array($userHelp['id'], $autoHelpAry) ) {
            $nextId = $userHelp['id'] + 1;
		}
		else {
            $nextId = 0;
		}
		return $nextId;
	}
	
    public static function guideFight($uid)
	{
        $info = array();
		$info['uid'] = $uid;
		$info['fid'] = Hapyfish2_Alchemy_Bll_Fight::getNewId($uid);
		$info['type'] = 9;
		$info['status'] = 0;

		$aryRnd = array();
		for ($i=0; $i<20; $i++) {
            $aryRnd[] = mt_rand(1,1000);
		}

		//本方
		$homeSide = Hapyfish2_Alchemy_Bll_FightCorps::getHomeSideUnitList($uid);
		if (!$homeSide) {
		    return -321;
		}

		//敌方
		$basMonsterMatrix = Hapyfish2_Alchemy_Cache_Basic::getFightMonsterMatixList();
    	$detailId = 20;
    	//$detail = json_decode('{"num":4,"gp":[[171,70],[271,30]]}', true);
    	//$detail = $basMapData['monsterList'][$id]['detail'];
	    if (!isset($basMonsterMatrix[$detailId])) {
    	    return -322;
    	}
    	$detail = json_decode($basMonsterMatrix[$detailId]['matrix'], true);
    	if (!$detail) {
    	    return -322;
    	}

    	//basic monster info
        $basMonster = Hapyfish2_Alchemy_Cache_Basic::getMonsterList();
	    $id = 1;
        foreach ($detail as $pos=>$cid) {
            $posMonster[$pos] = array('id'=>$id, 'cid'=>$cid);
            $id ++;
        }
	    $enemySide = array();
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
                $monsterInfo['award_conditions'] = Hapyfish2_Alchemy_Bll_MapCopy::_preCalcAwardCondition($monsterInfo['award_conditions']);
                unset($monsterInfo['content']);
                unset($monsterInfo['avatar_class_name']);

                //add weapon prop to attribute prop
                Hapyfish2_Alchemy_Bll_Fight::addWeaponProp($monsterInfo);
                $enemySide[$pos] = $monsterInfo;
            }
        }

		if (!$enemySide) {
		    return -322;
		}

		$info['rnd_element'] = $aryRnd;
		$info['home_side'] = $homeSide;
		$info['enemy_side'] = $enemySide;
		$info['content'] = array();
		$info['create_time'] = time();

		$info['enemy_id'] = '2-2';
		$saveDb = (defined('ENABLE_FIGHT_DB_LOG') && ENABLE_FIGHT_DB_LOG);
        Hapyfish2_Alchemy_Cache_Fight::saveFightInfo($uid, $info, $saveDb);

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

            //添加图鉴
            Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $data['tid']);
        }

        $battle = array(
            'id' => $info['fid'],
            'bgClassName' => 'battlebg.1.Background',
            'roleList' => array_merge($roleList1, $roleList2),
        	'talk' => $aryTalk,
            'friendSkill' => array(),
            'assCnt' => 0,
            'extCnt' => 0
        );

        $result = array('BattleVo' => $battle,
        				'RndNums' => $info['rnd_element']);
        
        return $result;
	}


}