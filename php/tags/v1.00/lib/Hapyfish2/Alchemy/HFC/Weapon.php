<?php

class Hapyfish2_Alchemy_HFC_Weapon
{
    //装备,type:6
    public static function getAll($uid)
    {
        $key = 'a:u:weapon:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data === false) {
            try {
                $dalWeapon = Hapyfish2_Alchemy_Dal_Weapon::getDefaultInstance();
                $data = $dalWeapon->get($uid);
                if ($data) {
                    $cache->add($key, $data);
                } else {
                    return null;
                }
            } catch (Exception $e) {
            	info_log('[Hapyfish2_Alchemy_HFC_Weapon::getAll:'. $uid. ']' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        
        //weapon:cid,count,data
        $weapons = array();
        foreach ($data as $weapon) {
        	//if ( $weapon[1] > 0 ) {
        		$weaponData = json_decode($weapon[2]);
        		foreach ( $weaponData as $v ) {
        			if(!isset($v[12])){
        				$detailInfo = Hapyfish2_Alchemy_Cache_Basic::getWeaponInfo($weapon[0]);
        				$v[12] = self::getWeaponQuality($detailInfo, array_slice($v, 3, 9));
        			}
        			$wid = $v[0];
        			$weapons[$wid] = array('wid' => (int)$wid,
        								   'cid' => (int)$weapon[0],    //装备cid
        								   'status' => (int)$v[1],		//是否已装备，1:装备中;0:未装备
        								   'durability' => (int)$v[2],  //剩余耐久度
        								   'pa'	=> (int)$v[3],
        									'pd' => (int)$v[4],
        									'ma' => (int)$v[5],
        									'md' => (int)$v[6],
        									'speed' => (int)$v[7],
											'hp' => (int)$v[8],
        									'mp' => (int)$v[9],
        									'cri' => (int)$v[10],
        									'dod' => (int)$v[11],
        									'type'=> (int)$v[12],       				
        			); 
        		}
        	//}
        }
        
        return $weapons;
    }
    
    public static function loadAll($uid)
    {
        try {
            $dalWeapon = Hapyfish2_Alchemy_Dal_Weapon::getDefaultInstance();
            $data = $dalWeapon->get($uid);
            if ($data) {
                $key = 'a:u:weapon:' . $uid;
                $cache = Hapyfish2_Cache_Factory::getMC($uid);
                $cache->set($key, $data);
            } else {
                return null;
            }
            return $data;
        } catch (Exception $e) {
        	info_log('[Hapyfish2_Alchemy_HFC_Weapon::loadAll:'. $uid. ']' . $e->getMessage(), 'db.err');
        }
        return null;
    }

    /**
     * 查询满耐久度的装备
     * 
     * @param int $uid
     */
    public static function getNewWeapon($uid)
    {
    	$userWeapon = self::getAll($uid);
    	
    	$newWeapon = array();
    	foreach ( $userWeapon as $waepon ) {
    		$cid = $waepon['cid'];
    		if ( $waepon['durability'] == 1000 ) {
    			if ( !isset($newWeapon[$cid]) ) {
    				$newWeapon[$cid] = array('count' => 1);
    			}
    			else {
    				$newWeapon[$cid]['count'] = $newWeapon[$cid]['count'] + 1;
    			}
    		}
    	}
    	
    	return $newWeapon;
    }
    
    public static function delWeaponByCid($uid, $cid, $count = 1)
    {
    	$result = false;
    	
    	$delCount = 0;
        $allWeapon = self::getAll($uid);
        foreach ( $allWeapon as $weapon ) {
        	if ( $delCount == $count ) {
        		return array('status' => $result, 'id' => $wid);
        	}
        	if ( $weapon['cid'] == $cid && $weapon['durability'] == 1000 ) {
        		$result = self::delWeapon($uid, $weapon['wid']);
        		$delCount++;
        		$wid = $weapon['wid'];
        	}
        }
        return array('status' => $result, 'id' => $wid);
    }
    
    public static function delWeapon($uid, $wid)
    {
    	$ok = false;
    	
        $cid = substr($wid, -4, 4);
        try {
	        $dalWeapon = Hapyfish2_Alchemy_Dal_Weapon::getDefaultInstance();
	        $oldWeapon = $dalWeapon->getWeaponByCid($uid, $cid);
	    	$oldData = json_decode($oldWeapon['data']);
	    	
	    	foreach ( $oldData as $key => $value ) {
	    		if ( $value[0] == $wid ) {
	    			unset($oldData[$key]);
	    		}
	    	}
	    	
	    	$newData = array();
	    	foreach ( $oldData as $v ) {
	    		$newData[] = $v;
	    	}
	    	$newCount = $oldWeapon['count'] - 1;
	        $newWeapon = array('count' => $newCount, 'data' => json_encode($newData));

	        $dalWeapon->update($uid, $cid, $newWeapon);
        	self::loadAll($uid);
        	$ok = true;
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_Weapon::delWeapon:'. $uid. ']' . $e->getMessage(), 'db.err');
        }
    	
        return $ok;
    }

    public static function getOne($uid, $wid)
    {
        $allWeapon = self::getAll($uid);
        if (!isset($allWeapon[$wid])) {
        	return null;
        }
        return $allWeapon[$wid];
    }
    
    public static function addUserWeapon($uid, $cid, $count = 1)
    {
    	for ($i=0; $i< $count; $i++) {
    		$ok = self::addOne($uid, $cid);
    		if (!$ok) {
    			return false;
    		}
    	}
    	return true;
    }
    
    public static function addOne($uid, $cid)
    {
    	$ok = false;
    	
		$widTemp = self::getNewWeaponId($uid);
		$cidTemp = str_pad($cid, 7, 0, STR_PAD_LEFT);
		$wid = $widTemp . $cidTemp;
		$detail = Hapyfish2_Alchemy_Cache_Basic::getWeaponInfo($cid);
		if($detail == null){
			return $ok;
		}
		$newInfo = array();
		$i = 0;
		foreach($detail as $k => $v){
			if($i >=4 && $i<=12){
				$dnum = json_decode($v, true);
				$newInfo[] = rand($dnum[0], $dnum[1]);
			}
			$i ++;
		}
		$type = self::getWeaponQuality($detail, $newInfo);
		$newInfo[] = $type;
		$newDurability = 1000;
		$binfo = array((int)$wid, 0, $newDurability);
		$binfo = array_merge($binfo, $newInfo);
        try {
            $dalWeapon = Hapyfish2_Alchemy_Dal_Weapon::getDefaultInstance();
        	$oldWeapon = $dalWeapon->getWeaponByCid($uid, $cid);
        	if (!$oldWeapon) {
        		$newData = array();
        		$newData[] = $binfo;
        		$newWeapon = array('uid'=>$uid, 'cid'=>$cid, 'count'=>1,'data'=>json_encode($newData));
                $dalWeapon->insert($uid, $newWeapon);
        	}
        	else {
        		$oldData = json_decode($oldWeapon['data']);
        		$oldData[] = $binfo;
        		$newCount = $oldWeapon['count'] + 1;
        		$newWeapon = array('count' => $newCount, 'data' => json_encode($oldData));
        		$dalWeapon->update($uid, $cid, $newWeapon);
        	}
        	self::loadAll($uid);
        	$ok = true;
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_Weapon::addOne:'. $uid. ']' . $e->getMessage(), 'db.err');
        }
        
        if ($ok) {
    		$addItem = array($cid, 1, $wid, $newDurability);
    		$addItem = array_merge($addItem, $newInfo);
    		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'addItems', array($addItem));
			//添加图鉴
			Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $cid);
        }
        return $ok;
    }

    public static function updateOne($uid, $wid, $info, $savedb = true)
    {
    	$ok = false;
    	
        $cid = substr($wid, -7, 7);
         
        try {
        	$i = 0;
        	$ins = array();
        	foreach($info as $k => $v){
        		if($i >= 4){
        			$ins[] = $v; 
        		}
        		$i++;
        	}
	        $dalWeapon = Hapyfish2_Alchemy_Dal_Weapon::getDefaultInstance();
	        $oldWeapon = $dalWeapon->getWeaponByCid($uid, $cid);
	    	$oldData = json_decode($oldWeapon['data']);
	    		    	
	    	$countChange = false;
	    	foreach ( $oldData as $key => $value ) {
	    		if ( $value[0] == $wid ) {
	    			if ( $value[1] != 0 && $info['status'] == 0 ) {
	    				$newCount = $oldWeapon['count'] + 1;
	    				$countChange = true;
	    			}
	    			else if ( $value[1] == 0 && $info['status'] != 0 ) {
	    				$newCount = $oldWeapon['count'] - 1;
	    				$countChange = true;
	    			}
	    			$durability = $value[2];
	    			$change = array((int)$wid, (int)$info['status'], (int)$info['durability']);
	    			$oldData[$key] = array_merge($change, $ins);
	    		}
	    	}
	    	
	        $newWeapon = array('data' => json_encode($oldData));
	        if ($countChange) {
	        	$newWeapon['count'] = $newCount;
	        }
	        $dalWeapon->update($uid, $cid, $newWeapon);
	        
        	self::loadAll($uid);
        	$ok = true;
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_Weapon::updateOne:'. $uid. ']' . $e->getMessage(), 'db.err');
        }
    
        if ($ok && $countChange) {
        	if ( $newCount > $oldWeapon['count'] ) {
    			$addItem = array($cid, 1, $wid, $durability);
    			$addItem = array_merge($addItem, $ins);
    			Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'addItems', array($addItem));
        	}
        	else if ( $newCount < $oldWeapon['count'] ) {
    			$removeItems = array($cid, 1, $wid, $durability);
    			$removeItems = array_merge($removeItems, $ins);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeItems', array($removeItems));
        	}
        }
        return $ok;
    }
    
    public static function clearWeaponByCid($uid, $cid)
    {
    	$ok = false;
    	
        try {
	        $dalWeapon = Hapyfish2_Alchemy_Dal_Weapon::getDefaultInstance();
	        $dalWeapon->delete($uid, $cid);
        	self::loadAll($uid);
        	$ok = true;
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_Weapon::delWeapon:'. $uid. ']' . $e->getMessage(), 'db.err');
        }
    	
        return $ok;
    }
    
    public static function getNewWeaponId($uid)
    {
        try {
            $dalUserSequence = Hapyfish2_Alchemy_Dal_UserSequence::getDefaultInstance();
            return $dalUserSequence->get($uid, 'g', 1);
        } catch (Exception $e) {
        	info_log('[Hapyfish2_Alchemy_HFC_Weapon::getNewWeaponId:'. $uid. ']' . $e->getMessage(), 'db.err');
        }
        return 0;
    }
    
    public static function getWeaponQuality($detail, $info)
    {
    	$type = 1;
    	$limitMin = 0;
    	$limitMax = 0;
    	$limit = 0;
    	$mnum = 0;
    	$limit = array_sum($info);
    	$detail = array_values($detail);
    	$detail = array_slice($detail, 4, 9);
    	foreach($detail as $k=>$v){
    		$dnum = json_decode($v, true);
    		$limitMin += $dnum[0];
    		$limitMax += $dnum[1];
    	}
    	$mnum = $limitMax - $limitMin;
    	$dnum = $limit - $limitMin;
    	$step1 = floor($mnum/3);
    	$step2 = floor((2*$mnum)/3);
    	if($dnum <= $step1){
    		$type = 1;
    	}else if ($dnum > $step1 && $dnum <= $step2){
    		$type = 2;
    	}else if ($dnum > $step2 && $dnum <$limitMax){
    		$type = 3;
    	}else if($dnum == $limitMax){
    		$type = 4;
    	}
    	return $type;
    }
	
	public static function addMercenaryWeapon($uid, $cid, $mid)
	{
		$ok = false;
    	
		$widTemp = self::getNewWeaponId($uid);
		$cidTemp = str_pad($cid, 7, 0, STR_PAD_LEFT);
		$wid = $widTemp . $cidTemp;
		$detail = Hapyfish2_Alchemy_Cache_Basic::getWeaponInfo($cid);
		if($detail == null){
			return $ok;
		}
		$newInfo = array();
		$i = 0;
		foreach($detail as $k => $v){
			if($i >=4 && $i<=12){
				$dnum = json_decode($v, true);
				$newInfo[] = rand($dnum[0], $dnum[1]);
			}
			$i ++;
		}
		$type = self::getWeaponQuality($detail, $newInfo);
		$newInfo[] = $type;
		$newDurability = 1000;
		$binfo = array((int)$wid, $mid, $newDurability);
		$binfo = array_merge($binfo, $newInfo);
        try {
            $dalWeapon = Hapyfish2_Alchemy_Dal_Weapon::getDefaultInstance();
        	$oldWeapon = $dalWeapon->getWeaponByCid($uid, $cid);
        	if (!$oldWeapon) {
        		$newData = array();
        		$newData[] = $binfo;
        		$newWeapon = array('uid'=>$uid, 'cid'=>$cid, 'count'=>1,'data'=>json_encode($newData));
                $dalWeapon->insert($uid, $newWeapon);
        	}
        	else {
        		$oldData = json_decode($oldWeapon['data']);
        		$oldData[] = $binfo;
        		$newCount = $oldWeapon['count'] + 1;
        		$newWeapon = array('count' => $newCount, 'data' => json_encode($oldData));
        		$dalWeapon->update($uid, $cid, $newWeapon);
        	}
        	self::loadAll($uid);
        	$ok = true;
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_Weapon::addOne:'. $uid. ']' . $e->getMessage(), 'db.err');
        }
        
        if ($ok) {
    		$addItem = array($cid, 1, $wid, $newDurability);
    		$addItem = array_merge($addItem, $newInfo);
    		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'addItems', array($addItem));
			//添加图鉴
			Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $cid);
        }
        return $wid;
	}
}