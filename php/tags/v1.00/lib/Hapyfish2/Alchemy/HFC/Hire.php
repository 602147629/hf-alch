<?php

class Hapyfish2_Alchemy_HFC_Hire
{
    public static function getAll($uid)
    {
    	$key = 'a:u:hirelist:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
		$temp = array();
        if ($data === false) {
        	try {
	            $dalHire = Hapyfish2_Alchemy_Dal_Hire::getDefaultInstance();
	            $data = $dalHire->get($uid);
	            if ($data) {
	            	for ( $i = 1;$i < 7;$i++ ) {
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
        	if ( $v[3] == 0 ) {
        		$remainTime = 0;
        	}
        	else {
	        	$needTime = Hapyfish2_Alchemy_Bll_Mercenary::getNeedTime($v[2]);
				$remainTime = ($needTime + $v[12]) - $nowTime;
				$remainTime = $remainTime < 0 ? 0 : $remainTime;
        	}
        	
        	//fortest
        	if ($v[13]==null) {
        		$v[13] = 'battle.2.Fm2';
        	}
        	if ($v[14]==null) {
        		$v[14] = 'battle.1.Ff2';
        	}
        	
        	$list[$v[0]] = array('id' 			=> $v[0],	//雇佣位id
        						 'type'			=> $v[1],	//酒馆类型id，1-自宅，2-外村,3-王城
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
        						 'remainingTime'=> $remainTime);//最后一次更新时间);	//1-男，2-女
        }
        
        return $list;
    }
    
    public static function loadAll($uid)
    {
    	$key = 'a:u:hirelist:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        
        try {
	    	$dalHire = Hapyfish2_Alchemy_Dal_Hire::getDefaultInstance();
	        $data = $dalHire->get($uid);
        } catch (Exception $e) {
    		return false;
        }
        
    	if ($data) {
            for ( $i = 1;$i < 7;$i++ ) {
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
					  $hire['scene_player_class']);
    	
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
    

    public static function getWine($uid)
    {
    	$key = 'a:u:wine:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	return array('list' => array(), 'used' => 0);
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


}