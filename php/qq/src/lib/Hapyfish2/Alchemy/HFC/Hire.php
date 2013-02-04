<?php

class Hapyfish2_Alchemy_HFC_Hire
{
    public static function getAll($uid)
    {
    	$key = 'a:u:hirelistall:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
		$temp = array();
        if ($data === false) {
        	try {
	            $dalHire = Hapyfish2_Alchemy_Dal_Hire::getDefaultInstance();
	            $data = $dalHire->get($uid);
	            if ($data) {
	            	for ( $i = 1;$i < 4;$i++ ) {
	            		$field = 'hire_' . $i;
	            		$temp[$i] = json_decode($data[$field]);
	            	}
	            	$data = $temp;
                    $cache->add($key, $data);
                } else {
                    return null;
                }
            } catch (Exception $e) {
            }
        }
        
        $list = array();
        $nowTime = time();
        foreach ( $data as $v ) {
        	//fortest
        	/* if ($v[13]==null) {
        		$v[13] = 'battle.2.Fm2';
        	}
        	if ($v[14]==null) {
        		$v[14] = 'battle.1.Ff2';
        	} */
        	$list[$v[0]] = array('id' 			=> $v[0],	//雇佣位id
        						 'type'			=> $v[1],	//酒馆类型id，1-自宅，2-王城,3-外村
        						 'level'		=> $v[2],	//酒馆等级
        						 'cid' 			=> $v[3],	//佣兵模型cid，对应 mercenary_model-cid
        						 'gid' 			=> $v[4],	//佣兵成长gid，对应 mercenary_grow-id
        						 'rp' 			=> $v[5],	//佣兵资质级别,1-1星,2-2星...
        						 'job' 			=> $v[6],	//佣兵类型，1-战士，2-弓手，3-法师
        						 'element' 		=> $v[7],	//元素属性，1-风，2-火，3-水
        						 'name' 		=> $v[8],	//佣兵名字
        						 'content' 		=> $v[9],	//佣兵描述
        						 'sex' 			=> $v[10],
        						 'class_name' 	=> $v[11],
        						 'face_class_name'=> $v[13],
        						 's_face_class_name'=> $v[14],
        						 'scene_player_class'=> $v[15],
        						 'remainingTime'=> 0,	
        						 'skill' => $v[16],
        						 'hp' => $v[17],
        						 'mp' => $v[18],
        						 'phy_att' => $v[19],
        						 'phy_def' => $v[20],
        						 'mag_att' => $v[21],
        						 'mag_def' => $v[22],
        						 'agility' => $v[23],
        						 'crit' => $v[24],
        						 'dodge' => $v[25],
        						 'hit' => $v[26],
        						 'tou' => $v[27],
        						 'q_hp' => $v[28],
        						 'q_mp' => $v[29],
        						 'q_phy_att' => $v[30],
        						 'q_phy_def' => $v[31],
        						 'q_mag_att' => $v[32],
        						 'q_mag_def' => $v[33],
        						 'q_agility' => $v[34],
        						 'q_crit' => $v[35],
        						 'q_dodge' => $v[36],
        						 'q_hit' => $v[37],
        						 'q_tou' => $v[38],
        						 'coin' => $v[39],
        						 'feats' => $v[40],
        						 'str' => $v[41],
        						 'dex' => $v[42],
        						 'mag' => $v[43],
        						 'phy' => $v[44],
        						 'q_str' => $v[45],
        						 'q_dex' => $v[46],
        						 'q_mag' => $v[47],
        						 'q_phy' => $v[48],
        						 'q_role' => $v[49]);
        	
        }
                
        return $list;
    }
    
    public static function loadAll($uid)
    {
    	$key = 'a:u:hirelistall:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        
        try {
	    	$dalHire = Hapyfish2_Alchemy_Dal_Hire::getDefaultInstance();
	        $data = $dalHire->get($uid);
        } catch (Exception $e) {
    		return false;
        }
        
    	if ($data) {
            for ( $i = 1;$i < 4;$i++ ) {
	            $field = 'hire_' . $i;
	            $list[$i] = json_decode($data[$field]);
            }
            $cache->update($key, $list);
            return true;
    	} else {
    		return false;
    	}
    }
    
    public static function updateOne($uid, $id, $hire)
    {
		$info = array($hire['id'], $hire['type'], $hire['level'], $hire['cid'], 
					  $hire['gid'], $hire['rp'], $hire['job'], $hire['element'], 
					  $hire['name'], $hire['content'], $hire['sex'], $hire['class_name'], 
					  $hire['start_time'], $hire['face_class_name'], $hire['s_face_class_name'], 
					  $hire['scene_player_class'], $hire['skill'], 
					  $hire['hp'], $hire['mp'], $hire['phy_att'], $hire['phy_def'], $hire['mag_att'], 
				      $hire['mag_def'], $hire['agility'], $hire['crit'], $hire['dodge'], $hire['hit'], $hire['tou'], 
					  $hire['q_hp'], $hire['q_mp'], $hire['q_phy_att'], $hire['q_phy_def'], $hire['q_mag_att'], 
				      $hire['q_mag_def'], $hire['q_agility'], $hire['q_crit'], $hire['q_dodge'], $hire['q_hit'], $hire['q_tou'], 
					  $hire['coin'], $hire['feats'], $hire['str'], $hire['dex'], $hire['mag'], $hire['phy'], 
					  $hire['q_str'], $hire['q_dex'], $hire['q_mag'], $hire['q_phy'], $hire['q_role']);
    	
		$newHire = array();
		$field = 'hire_' . $id;
		$newHire[$field] = json_encode($info);
		
        try {
			$dalHire = Hapyfish2_Alchemy_Dal_Hire::getDefaultInstance();
	        $data = $dalHire->update($uid, $newHire);
        } catch (Exception $e) {
    		return false;
        }
        
        self::loadAll($uid);
        
        return true;
    }

    public static function getHireData($uid)
    {
    	$key = 'a:u:uhiredata:';
    	$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
    	$key = $key . $uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$data = $cache->get($key);

    	if ($data === false) {
    		try {
    			$dalHire = Hapyfish2_Alchemy_Dal_Hire::getDefaultInstance();
    			$info = $dalHire->get($uid);

    			if ($info && $info['data']) {
    				$hireData = json_decode($info['data'], true);
    			} else {
    				$nowTm = time();
    				$data0 = array('times' => 20,
    								 'endTime' => 0,
    								 'price' => 200,
    								 'fastGemPrice' => 10);
    				$data1 = array('times' => -1,
    								 'endTime' => 0,
    								 'price' => 200,
    								 'fastGemPrice' => 50);
    				$data2 = array('times' => -1,
    								 'endTime' => 0,
    								 'price' => 100,
    								 'fastGemPrice' => 200);
    				$data3 = array('times' => -1,
    								 'endTime' => 0,
    								 'price' => 50,
    								 'fastGemPrice' => 500);
    				$hireData = array('0' => $data0,
	    							  '1' => $data1,
	    							  '2' => $data2,
	    							  '3' => $data3);
    			}
    			
    			$data = array('data' => $hireData,
	    					'first_refresh_2' => $info['first_refresh_2'],
	    					'first_refresh_3' => $info['first_refresh_3']);
    			$cache->add($key, $data);
    		} catch (Exception $e) {
    		}
    	}
    	return $data;
    }
    
    public static function updateHireData($uid, $data, $savedb = true)
    {
    	$key = 'a:u:uhiredata:';
    	$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
    	$key = $key . $uid;
    
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	if (!$savedb) {
    		$savedb = $cache->canSaveToDB($key, 900);
    	}
    	if ($savedb) {
    		$ok = $cache->save($key, $data);
    		$info = array('data' => json_encode($data['data']),
    					  'first_refresh_2' => $data['first_refresh_2'],
    					  'first_refresh_3' => $data['first_refresh_3']);
    		if ($ok) {
    			try {
    				$dal = Hapyfish2_Alchemy_Dal_Hire::getDefaultInstance();
    				$dal->update($uid, $info);
    			} catch (Exception $e) {
    			}
    		}
    		return $ok;
    	} else {
    		return $cache->update($key, $data);
    	}
    }

    public static function getHireFirstRefresh($uid)
    {
    	$key = 'a:u:hirefirstref:';
    	$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
    	$key = $key . $uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$data = $cache->get($key);
    	 
    	if ($data === false) {
    		try {
    			$dalHire = Hapyfish2_Alchemy_Dal_Hire::getDefaultInstance();
    			$info = $dalHire->get($uid);
    
    			if ($info) {
    				$data = $info['data'];
    				$cache->add($key, $data);
    			} else {
    				return 0;
    			}
    		} catch (Exception $e) {
    		}
    	}
    	return $data;
    }
    
    public static function updateHireFirstRefresh($uid, $data, $savedb = true)
    {
    	$key = 'a:u:hirefirstref:';
    	$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
    	$key = $key . $uid;
    
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	if (!$savedb) {
    		$savedb = $cache->canSaveToDB($key, 900);
    	}
    	if ($savedb) {
    		$ok = $cache->save($key, $data);
    		$info = array('data' => json_encode($data));
    		if ($ok) {
    			try {
    				$dal = Hapyfish2_Alchemy_Dal_Hire::getDefaultInstance();
    				$dal->update($uid, $info);
    			} catch (Exception $e) {
    			}
    		}
    		return $ok;
    	} else {
    		return $cache->update($key, $data);
    	}
    }
    
    public static function getWine($uid)
    {
    	$key = 'a:u:wine:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
		$date = date('Ymd');
        if ($data === false || $date != $data['date']) {
        	return array('list' => array(), 'used' => 0, 'date'=>$date);
        }
        return $data;
    }
    
    public static function updateWine($uid, $data)
    {
    	$key = 'a:u:wine:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        return $cache->update($key, $data);
    }
    
    public static function getBotWine($uid)
    {
    	$key = 'a:u:wine:bot:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
		$date = date('Ymd');
        if ($data === false || $date != $data['date']) {
        	return array('list' => array(), 'used' => 0, 'date'=>$date);
        }
        return $data;
    }

    public static function updateBotWine($uid, $data)
    {
    	$key = 'a:u:wine:bot:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        return $cache->update($key, $data);
    }

}