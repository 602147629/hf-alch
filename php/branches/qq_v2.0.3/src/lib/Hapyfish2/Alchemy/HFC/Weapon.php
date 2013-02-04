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
        			if(!isset($v[14])){
        				$v[14] = 1;
        			}
        			if(!isset($v[15])){
        				$v[15] = 0;
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
        									'hit' => isset($v[12])?$v[12]:0,
        									'tou' => isset($v[13])?$v[13]:0,
        									'type'=> (int)$v[14],
        									'strLevel' => $v[15],       				
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
    	foreach ( $userWeapon as $weapon ) {
    		$cid = $weapon['cid'];
    		if ( $weapon['durability'] == 1000 && $weapon['status'] == 0 ) {
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
    
    /**
     * 查询满耐久度的完美装备
     * 
     * @param int $uid
     */
    public static function getNewPerfectWeapon($uid)
    {
    	$userWeapon = self::getAll($uid);
    	
    	$newWeapon = array();
    	foreach ( $userWeapon as $weapon ) {
    		$cid = $weapon['cid'];
    		if ( $weapon['durability'] == 1000 && $weapon['type'] != 4 && $weapon['status'] == 0 ) {
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
    
    public static function delPerfectWeaponByCid($uid, $cid, $count = 1)
    {
    	$result = false;
    	
    	$delCount = 0;
        $allWeapon = self::getAll($uid);
        foreach ( $allWeapon as $weapon ) {
        	if ( $delCount == $count ) {
        		return array('status' => $result, 'id' => 0);
        	}
        	if ( $weapon['cid'] == $cid && $weapon['durability'] == 1000 && $weapon['type'] != 4 && $weapon['status'] == 0 ) {
        		$result = self::delWeapon($uid, $weapon['wid']);
        		$delCount++;
        		$wid = $weapon['wid'];
        		if ( $result ) {
	                $removeItems = array($cid, 1, $wid);
	                Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeItems', array($removeItems));
        		}
        	}
        }
        return array('status' => $result, 'id' => $wid);
    }
    
    public static function delWeaponByCid($uid, $cid, $count = 1)
    {
    	$result = false;
    	
    	$delCount = 0;
        $allWeapon = self::getAll($uid);
        foreach ( $allWeapon as $weapon ) {
        	if ( $delCount == $count ) {
        		return array('status' => $result, 'id' => 0);
        	}
        	if ( $weapon['cid'] == $cid && $weapon['durability'] == 1000 && $weapon['status'] == 0 ) {
        		$result = self::delWeapon($uid, $weapon['wid']);
        		$delCount++;
        		$wid = $weapon['wid'];
        		if ( $result ) {
	                $removeItems = array($cid, 1, $wid);
	                Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeItems', array($removeItems));
        		}
        	}
        }
        return array('status' => $result, 'id' => $wid);
    }
    
    public static function delWeapon($uid, $wid)
    {
    	$ok = false;
    	
        $cid = substr($wid, -7, 7);
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
    		$wid = Hapyfish2_Alchemy_Bll_EventGift::addUserWeapon($uid, $cid, 1);
    		if (!$wid) {
    			return false;
    		}
    	}
    	return $wid;
    }
    
    public static function addUserDamagedWeapon($uid, $cid)
    {
    	$wid = self::addOne($uid, $cid, 0);
    	if ( $wid ) {
    		return true;
    	}
    	return false;
    }
    
    public static function addOne($uid, $cid, $newDurability = 1000)
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
		$rate = rand(0,100);
		$i = 0;
		foreach($detail as $k => $v){
			if($i >=4 && $i<=14){
				$dnum = json_decode($v, true);
				$newInfo[] = self::getNew($dnum[0], $dnum[1], $rate);
			}
			$i ++;
		}
		$type = self::getWeaponQuality($rate);
		$newInfo[] = $type;
		$newInfo[] = 0;
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
        return $wid;
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
	    	if($oldData){
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
    
    public static function getWeaponQuality($rate)
    {
    	$step = floor(100/3);
    	if($rate == 100){
    		return 4;
    	}else if($rate >= 2*$step){
    		return 3;
    	}else if($rate >= $step){
    		return 2;
    	}else{
    		return 1;
    	}
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
		$rate = rand(0,100);
		$i = 0;
		foreach($detail as $k => $v){
			if($i >=4 && $i<=14){
				$dnum = json_decode($v, true);
				$newInfo[] = self::getNew($dnum[0], $dnum[1], $rate);
			}
			$i ++;
		}
		$type = self::getWeaponQuality($rate);
		$newInfo[] = $type;
		$newInfo[] = 0;
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
	
	public static function getNew($min, $max, $rate)
	{
		$data = $min + floor(($max - $min)*$rate/100);
		return $data;
	}
}