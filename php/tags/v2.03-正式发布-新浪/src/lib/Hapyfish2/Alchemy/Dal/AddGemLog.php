<?php

class Hapyfish2_Alchemy_Dal_AddGemLog
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_AddGemLog
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getTableName($uid)
    {
        if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE == 3) {
            $id = 0;
        } else {
        	$id = floor($uid/DATABASE_NODE_NUM) % 50;
        }
        
    	return 'alchemy_user_log_add_gem_' . $id;
    }
    
    public function getAll($uid)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT uid,gold,`type`,create_time FROM $tbname WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('uid' => $uid));
    }
    
    public function getAllByTime($uid, $time)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT uid,gold,`type`,create_time FROM $tbname WHERE uid=:uid AND `create_time`>$time ORDER BY `create_time`";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('uid' => $uid));
    }
    
    public function insert($uid, $info)
    {
    	$tbname = $this->getTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info); 	
    }
    
    public function clear($uid)
    {
        $tbname = $this->getTableName($uid);
        
        $sql = "DELETE FROM $tbname WHERE uid=:uid";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid));
    }
    
}