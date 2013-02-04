<?php

class Hapyfish2_Alchemy_Bll_UserDump
{
	
    public static function dumpUser($uid, $filename)
    {
        $dumpData = self::_getAllUserData($uid);
        if ( !$dumpData ) {
            return false;
        }
        
        $nowTime = time();
        $file = TEMP_DIR . '/userdump/'.$filename.'.dump';
        $data = json_encode($dumpData);
        $data = gzcompress($data, 9);
        //$file .= '.zip';
        //file_put_contents($file, $data);
        
        $newDump = array('uid' => $uid, 'file_name' => $filename, 'content' => $data, 'create_time' => time());
        $dal = Hapyfish2_Alchemy_Dal_UserDump::getDefaultInstance();
        $dal->insert($newDump);
        
        return true;
    }
    
    public static function loadUserData($uid, $fileId)
    {
        $dal = Hapyfish2_Alchemy_Dal_UserDump::getDefaultInstance();
        $fileInfo = $dal->getOne($fileId);
        if ( !$fileInfo ) {
            return;
        }
        
        if (!$fileInfo['content']) {
	        $filename = $fileInfo['file_name'];
	        $file = TEMP_DIR . '/userdump/'.$filename.'.dump.zip';
	        if (is_file($file)) {
	            $dumpData = file_get_contents($file);
	        } else {
	            return false;
	        }
	        $fileInfo['content'] = $dumpData;
	        $dal->update($uid, $fileInfo);
            $dumpData = gzuncompress($fileInfo['content']);
        }
        else {
            $dumpData = gzuncompress($fileInfo['content']);
        }
        
        $dumpData = json_decode($dumpData, true);
        
        $ok = self::_recoverAllUserData($uid, $dumpData);
        if ( !$ok ) {
        	return false;
        }
        
        return true;
    }
    
    public static function removeUserData($uid, $fileId)
    {        
        $dal = Hapyfish2_Alchemy_Dal_UserDump::getDefaultInstance();
        $fileInfo = $dal->getOne($fileId);
        if ( !$fileInfo ) {
        	return;
        }
        $dal->del($fileId);
        
        return true;
    }
    
    private static function _recoverAllUserData($uid, $dumpData)
    {
        $step = 0;
        
        //echo json_encode($decor);
        //var_dump($dumpData);
        //var_dump('<br/>');
        try {
	        $decor = $dumpData['decor'];
	        $dalDecor = Hapyfish2_Alchemy_Dal_Decor::getDefaultInstance();
	        foreach ( $decor as $dec ) {
	        	$dec['uid'] = $uid;
	        	$dalDecor->insertInScene($uid, $dec);
	        }
	        $step++;    //1
            $step++;    //2
	        
	        $fightAttribute = $dumpData['fightAttribute'];
	        $dalFightAttribute = Hapyfish2_Alchemy_Dal_FightAttribute::getDefaultInstance();
	        $fightAttribute['uid'] = $uid;
	        $dalFightAttribute->insert($uid, $fightAttribute);
	        $step++;    //3
	        
	        $fightCorps = $dumpData['fightCorps'];
	        $dalFightCorps = Hapyfish2_Alchemy_Dal_FightCorps::getDefaultInstance();
	        $newFightCorps = array('uid'=>$uid, 'matrix'=>$fightCorps);
	        $dalFightCorps->insert($uid, $newFightCorps);
	        $step++;    //4
	        
	        $fightMercenary = $dumpData['fightMercenary'];
	        $dalFightMer = Hapyfish2_Alchemy_Dal_FightMercenary::getDefaultInstance();

	        foreach ( $fightMercenary as $figMer ) {
	        	$figMer['uid'] = $uid;
	        	$dalFightMer->insert($uid, $figMer);
	        }
	        $step++;    //5
	        
	        $floor = $dumpData['floor'];

	        $dalFloor = Hapyfish2_Alchemy_Dal_FloorWall::getDefaultInstance();
	        $dalFloor->insert($uid, $floor[0], $floor[1]);
	        $step++;    //6
	        
	        $furnace = $dumpData['furnace'];
	        $dalFurnace = Hapyfish2_Alchemy_Dal_Furnace::getDefaultInstance();
	        foreach ( $furnace as $furna ) {
	            $furna['uid'] = $uid;
	            $dalFurnace->insert($uid, $furna);
	        }
	        $step++;    //7
	        
	        $goods = $dumpData['goods'];
	        $dalGoods = Hapyfish2_Alchemy_Dal_Goods::getDefaultInstance();
	        foreach ( $goods as $good ) {
	            $good['uid'] = $uid;
	            $dalGoods->insert($uid, $good);
	        }
	        $step++;    //8
	        
	        $help = $dumpData['help'];
	        $dalHelp = Hapyfish2_Alchemy_Dal_Help::getDefaultInstance();
	        $newHelp = array('id'=>$help[0],'idx'=>$help[1],'status'=>$help[2],'finish_ids'=>$help[3]);
            $newHelp['uid'] = $uid;
	        $dalHelp->insert($uid, $newHelp);
	        $step++;    //9
	        
	        $illustra = $dumpData['illustra'];
	        $dal = Hapyfish2_Alchemy_Dal_Illustrations::getDefaultInstance();
	        $newIllustra = array('uid'=>$uid, 'id'=>$illustra);
	        $dal->insert($uid, $newIllustra);
	        $step++;    //10
	        
	        $userinfo = $dumpData['userinfo'];
	        $dal = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
	        $userinfo['uid'] = $uid;
	        $dal->insert($uid, $userinfo);
	        $step++;    //11
	        
	        $mapcopy = $dumpData['mapcopy'];   
	        $dal = Hapyfish2_Alchemy_Dal_MapCopy::getDefaultInstance();
	        foreach ( $mapcopy as $mapcopyv ) {
	            $mapcopyv['uid'] = $uid;        
	            $dal->insert($uid, $mapcopyv);
	        }
	        $step++;    //12
	        
	        $person = $dumpData['person'];
	        $dal = Hapyfish2_Alchemy_Dal_Person::getDefaultInstance();
	        $newPerson = array('uid'=>$uid, 'list'=>$person);
	        $dal->insert($uid, $newPerson);
	        $step++;    //13
	        
	        $transport = $dumpData['transport'];   
	        $dal = Hapyfish2_Alchemy_Dal_OpenTransport::getDefaultInstance();
	        $newtransport = array('uid'=>$uid, 'list'=>$transport);
	        $dal->insert($uid, $newtransport);
	        $step++;    //14
	        
	        $mix = $dumpData['mix'];
	        $dal = Hapyfish2_Alchemy_Dal_Mix::getDefaultInstance();
	        $mix['uid'] = $uid;
	        $dal->insert($uid, $mix);
	        $step++;    //15
	        
	        $monster = $dumpData['monster'];
	        $dal = Hapyfish2_Alchemy_Dal_Monster::getDefaultInstance();
	        $newmonster = array('uid'=>$uid, 'monster'=>$person);
	        $dal->insert($uid, $newmonster);
	        $step++;    //16
	        
	        $fightOccupy = $dumpData['fightOccupy'];
	        $dal = Hapyfish2_Alchemy_Dal_FightOccupy::getDefaultInstance();
	        $fightOccupy['uid'] = $uid;
	        $dal->insert($uid, $fightOccupy);
	        $step++;    //17
	        
	        $mine = $dumpData['mine'];
	        $dal = Hapyfish2_Alchemy_Dal_OpenMine::getDefaultInstance();
	        $newmine = array('uid'=>$uid, 'open_mine'=>$mine);
	        $dal->insert($uid, $newmine);
	        $step++;    //18
	        
	        $portal = $dumpData['portal'];
	        $dal = Hapyfish2_Alchemy_Dal_OpenPortal::getDefaultInstance();
	        $newportal = array('uid'=>$uid, 'open_portal'=>$portal);
	        $dal->insert($uid, $newportal);
	        $step++;    //19
	        
	        $userseq = $dumpData['userseq'];
	        $dal = Hapyfish2_Alchemy_Dal_UserSequence::getDefaultInstance();
	        foreach ( $userseq as $seq ) {
	            $seq['uid'] = $uid;
	            $dal->insert($uid, $seq);
	        }
	        $step++;    //20
	        
	        $story = $dumpData['story'];
	        $dal = Hapyfish2_Alchemy_Dal_Story::getDefaultInstance();
	        $story['uid'] = $uid;
	        $dal->insert($uid, $story);
	        $step++;    //21
	        
	        $storyDialog = $dumpData['storyDialog'];
	        $dal = Hapyfish2_Alchemy_Dal_Story::getDefaultInstance();
	        $newstorydialog = array('uid'=>$uid, 'list'=>$storyDialog);
	        $dal->insertDialog($uid, $newstorydialog);
	        $step++;    //22
	        
	        $stuff = $dumpData['stuff'];
	        $dal = Hapyfish2_Alchemy_Dal_Stuff::getDefaultInstance();
	        foreach ( $stuff as $kStuff => $vStuff ) {
	            $dal->update($uid, $kStuff, $vStuff);
	        }
	        $step++;    //23
	        
	        $task = $dumpData['task'];
	        $dal = Hapyfish2_Alchemy_Dal_Task::getDefaultInstance();
	        foreach ( $task as $taskv ) {
	            $taskv['uid'] = $uid;
	            $dal->insert($uid, $taskv['tid'], $taskv['finish_time']);
	        }
	        $step++;    //24
	        
	        $taskDaily = $dumpData['taskDaily'];
	        $dal = Hapyfish2_Alchemy_Dal_TaskDaily::getDefaultInstance();
	        $newTaskDaily = array('uid' => $uid, 'list' => $taskDaily[0], 'data' => $taskDaily[1], 'refresh_tm' => $taskDaily[2]);
	        
            $dal->insert($uid, $newTaskDaily);
	        $step++;    //25
	        
	        $taskOpen = $dumpData['taskOpen'];
	        $dal = Hapyfish2_Alchemy_Dal_TaskOpen::getDefaultInstance();
	        $taskOpen['uid'] = $uid;
	        $newTaskOpen = array('uid' => $uid, 
	                              'list' => $taskOpen[0], 
	                              'list2' => $taskOpen[1], 
	                              'data' => $taskOpen[2], 
                                  'buffer_list' => $taskOpen[3]);
	        
            $dal->insert($uid, $newTaskOpen);
	        $step++;    //26
	        
	        $uniqueItem = $dumpData['uniqueItem'];
	        $dal = Hapyfish2_Alchemy_Dal_UniqueItem::getDefaultInstance();
	        $newUniqueItem = array('uid'=>$uid, 'item_ids'=>$uniqueItem);
	        $dal->insert($uid, $newUniqueItem);
	        $step++;    //27
	        
	        $unlockFunc = $dumpData['unlockFunc'];
	        $dal = Hapyfish2_Alchemy_Dal_UnlockFunc::getDefaultInstance();
	        $newunlockFunc = array('uid'=>$uid, 'func_list'=>$unlockFunc);
	        $dal->insert($uid, $newunlockFunc);
	        $step++;    //28
	        
	        $weapon = $dumpData['weapon'];
	        $dal = Hapyfish2_Alchemy_Dal_Weapon::getDefaultInstance();
	        foreach ( $weapon as $weaponv ) {
	            $weaponv['uid'] = $uid;
	            $newWeapon = array('uid' => $uid,
	                               'cid' => $weaponv[0],
                                   'count' => $weaponv[1],
                                   'data' => $weaponv[2]);
	            $dal->insert($uid, $newWeapon);
	        }
	        $step++;    //29
	        
	        $worldmap = $dumpData['worldmap'];
	        $dal = Hapyfish2_Alchemy_Dal_WorldMap::getDefaultInstance();
	        $newworldmap = array('uid'=>$uid, 'map_ids'=>$worldmap);
	        $dal->insert($uid, $newworldmap);
	        $step++;    //30
        
            //初始化佣兵,酒馆位置信息
            $dalHire = Hapyfish2_Alchemy_Dal_Hire::getDefaultInstance();
            $dalHire->init($uid);
            $step++;    //31
	        
        } catch (Exception $e) {
        	var_dump('error:'.$step);
	        var_dump('<br/>');
	        var_dump($e->getMessage());
	        var_dump('<br/>');
            info_log('[' . $step . ']' . $e->getMessage(), 'alchemy.user.loaddump');
            return false;
        }
        
        return $step;
    }
    
    private static function _getAllUserData($uid)
    {
        $dumpData = array();
        $step = 0;
        
        try {
	        $dalDecor = Hapyfish2_Alchemy_Dal_Decor::getDefaultInstance();
	        $decor = $dalDecor->getInScene($uid);
	        $dumpData['decor'] = $decor;
	        $step++;    //1
	        
	        $decorInBag = $dalDecor->getInBag($uid);
	        $dumpData['decorInBag'] = $decorInBag;
	        $step++;    //2
	        
	        $dalFightAttribute = Hapyfish2_Alchemy_Dal_FightAttribute::getDefaultInstance();
	        $fightAttribute = $dalFightAttribute->getInfo($uid);
	        $dumpData['fightAttribute'] = $fightAttribute;
	        $step++;    //3
	        
	        $dalFightCorps = Hapyfish2_Alchemy_Dal_FightCorps::getDefaultInstance();
	        $fightCorps = $dalFightCorps->getCorpsInfo($uid);
	        $dumpData['fightCorps'] = $fightCorps;
	        $step++;    //4
	        
	        $dalFightMercenary = Hapyfish2_Alchemy_Dal_FightMercenary::getDefaultInstance();
	        $fightMercenary = $dalFightMercenary->getAll($uid);
	        $dumpData['fightMercenary'] = $fightMercenary;
	        $step++;    //5
	        
	        $dalFloor = Hapyfish2_Alchemy_Dal_FloorWall::getDefaultInstance();
	        $floor = $dalFloor->get($uid);
	        $dumpData['floor'] = $floor;
	        $step++;    //6
	        
	        $dalFurnace = Hapyfish2_Alchemy_Dal_Furnace::getDefaultInstance();
	        $furnace = $dalFurnace->get($uid);
	        $dumpData['furnace'] = $furnace;
	        $step++;    //7
	        
	        $dalGoods = Hapyfish2_Alchemy_Dal_Goods::getDefaultInstance();
	        $goods = $dalGoods->getAll($uid);
	        $dumpData['goods'] = $goods;
	        $step++;    //8
	        
	        $dalHelp = Hapyfish2_Alchemy_Dal_Help::getDefaultInstance();
	        $help = $dalHelp->get($uid);
	        $dumpData['help'] = $help;
	        $step++;    //9
	        
	        $dal = Hapyfish2_Alchemy_Dal_Illustrations::getDefaultInstance();
	        $illustra = $dal->get($uid);
	        $dumpData['illustra'] = $illustra;
	        $step++;    //10
	        
	        $dal = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
	        $userinfo = $dal->get($uid);
	        $dumpData['userinfo'] = $userinfo;
	        $step++;    //11
	        
	        $dal = Hapyfish2_Alchemy_Dal_MapCopy::getDefaultInstance();
	        $mapcopy = $dal->getAllList($uid);
	        $dumpData['mapcopy'] = $mapcopy;
	        $step++;    //12
	        
	        $dal = Hapyfish2_Alchemy_Dal_Person::getDefaultInstance();
	        $person = $dal->get($uid);
	        $dumpData['person'] = $person;
	        $step++;    //13
	        
	        $dal = Hapyfish2_Alchemy_Dal_OpenTransport::getDefaultInstance();
	        $transport = $dal->get($uid);
	        $dumpData['transport'] = $transport;
	        $step++;    //14
            
	        $dal = Hapyfish2_Alchemy_Dal_Mix::getDefaultInstance();
	        $mix = $dal->get($uid);
	        $dumpData['mix'] = $mix;
	        $step++;    //15
	        
	        $dal = Hapyfish2_Alchemy_Dal_Monster::getDefaultInstance();
	        $monster = $dal->get($uid);
	        $dumpData['monster'] = $monster;
	        $step++;    //16
	        
	        $dal = Hapyfish2_Alchemy_Dal_FightOccupy::getDefaultInstance();
	        $fightOccupy = $dal->get($uid);
	        $dumpData['fightOccupy'] = $fightOccupy;
	        $step++;    //17
	        
	        $dal = Hapyfish2_Alchemy_Dal_OpenMine::getDefaultInstance();
	        $mine = $dal->get($uid);
	        $dumpData['mine'] = $mine;
	        $step++;    //18
	        
	        $dal = Hapyfish2_Alchemy_Dal_OpenPortal::getDefaultInstance();
	        $portal = $dal->get($uid);
	        $dumpData['portal'] = $portal;
	        $step++;    //19
	        
	        $dal = Hapyfish2_Alchemy_Dal_UserSequence::getDefaultInstance();
	        $userseq = $dal->getAll($uid);
	        $dumpData['userseq'] = $userseq;
	        $step++;    //20
	        
	        $dal = Hapyfish2_Alchemy_Dal_Story::getDefaultInstance();
	        $story = $dal->getAll($uid);
	        $dumpData['story'] = $story;
	        $step++;    //21
	        
	        $dal = Hapyfish2_Alchemy_Dal_Story::getDefaultInstance();
	        $storyDialog = $dal->getDialog($uid);
	        $dumpData['storyDialog'] = $storyDialog;
	        $step++;    //22
	        
	        $dal = Hapyfish2_Alchemy_Dal_Stuff::getDefaultInstance();
	        $stuff = $dal->get($uid);
	        $dumpData['stuff'] = $stuff;
	        $step++;    //23
	        
	        $dal = Hapyfish2_Alchemy_Dal_Task::getDefaultInstance();
	        $task = $dal->get($uid);
	        $dumpData['task'] = $task;
	        $step++;    //24
	        
	        $dal = Hapyfish2_Alchemy_Dal_TaskDaily::getDefaultInstance();
	        $taskDaily = $dal->get($uid);
	        $dumpData['taskDaily'] = $taskDaily;
	        $step++;    //25
	        
	        $dal = Hapyfish2_Alchemy_Dal_TaskOpen::getDefaultInstance();
	        $taskOpen = $dal->get($uid);
	        $dumpData['taskOpen'] = $taskOpen;
	        $step++;    //26
	        
	        $dal = Hapyfish2_Alchemy_Dal_UniqueItem::getDefaultInstance();
	        $uniqueItem = $dal->getInfo($uid);
	        $dumpData['uniqueItem'] = $uniqueItem;
	        $step++;    //27
	        
	        $dal = Hapyfish2_Alchemy_Dal_UnlockFunc::getDefaultInstance();
	        $unlockFunc = $dal->get($uid);
	        $dumpData['unlockFunc'] = $unlockFunc;
	        $step++;    //28
	        
	        $dal = Hapyfish2_Alchemy_Dal_Weapon::getDefaultInstance();
	        $weapon = $dal->get($uid);
	        $dumpData['weapon'] = $weapon;
	        $step++;    //29
	        
	        $dal = Hapyfish2_Alchemy_Dal_WorldMap::getDefaultInstance();
	        $worldmap = $dal->getInfo($uid);
	        $dumpData['worldmap'] = $worldmap;
	        $step++;    //30
        
        } catch (Exception $e) {
            var_dump('error:'.$step);
            var_dump('<br/>');
            var_dump('<br/>');
            var_dump($e->getMessage());
            info_log('[' . $step . ']' . $e->getMessage(), 'alchemy.user.loaddump');
            return false;
        }
        
        return $dumpData;
    }
	

}