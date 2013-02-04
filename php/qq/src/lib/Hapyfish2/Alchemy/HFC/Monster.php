<?php

class Hapyfish2_Alchemy_HFC_Monster
{
	/**
	 * 已遇到怪物列表
	 * @param int $uid
	 */
	public static function getMonster($uid)
    {
    	$key = 'a:u:monster:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
		if($data === false) {
			try {
			    $dal= Hapyfish2_Alchemy_Dal_Monster::getDefaultInstance();
	            $data = $dal->get($uid);
	            
	            if ( $data ) {
	            	$cache->add($key, $data);
	            } else {
	            	return array();
	            }
			}catch (Exception $e) {
				err_log($e->getMessage());
				return array();
			}
		}
		
		if ( $data == "" ) {
			$result = array();
		}
		else {
			$result = explode(',', $data);
		}
        return $result;
    }
    
    public static function updateMonster($uid, $mine, $savedb = false)
    {
        $key = 'a:u:monster:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        
        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        
        $data = implode(',', $mine);
        if ($savedb) {
            $ok = $cache->save($key, $data);
            if ($ok) {
                //save to db
                try {
                    $info = array(
                        'monster' => $data
                    );
                    
                    $dal = Hapyfish2_Alchemy_Dal_Monster::getDefaultInstance();
                    $dal->insUpd($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_Monster::updateMonster]' . $e->getMessage(), 'db.err');
                }
            }
        } else {
            $ok = $cache->update($key, $data);
        }
        
        return $ok;
    }
    
    /**
     * 添加遇到怪物
     * @param int $uid
     * @param int $cid,怪物cid
     */
    public static function addMonster($uid, $cid)
    {
    	$list = self::getMonster($uid);
    	if ( !in_array($cid, $list) ) {
    		$list[] = $cid;
    		return self::updateMonster($uid, $list);
    	}
    	return false;
    }
    
    /**
     * 判断是否已经遇到过此怪物
     * @param int $uid
     * @param int $cid,怪物cid
     */
    public static function isNewMonster($uid, $cid)
    {
    	$list = self::getMonster($uid);
    	if ( !in_array($cid, $list) ) {
    		return true;
    	}
    	return false;
    }
    
	
	
	
}