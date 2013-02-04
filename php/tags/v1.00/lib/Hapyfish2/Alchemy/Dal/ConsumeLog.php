<?php

class Hapyfish2_Alchemy_Dal_ConsumeLog
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_ConsumeLog
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getCoinTableName($uid, $yearmonth)
    {
       	if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE == 3) {
            $id = 0;
        } else {
        	$id = $yearmonth . floor($uid/DATABASE_NODE_NUM) % 20;
        }
    	return 'alchemy_user_log_consume_coin_' . $id;
    }
    
    public function getGemTableName($uid, $yearmonth)
    {
       	if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE == 3) {
            $id = 0;
        } else {
        	$id = $yearmonth . floor($uid/DATABASE_NODE_NUM) % 20;
        }
    	return 'alchemy_user_log_consume_gem_' . $id;
    }
    
    public function getCoin($uid, $yearmonth, $limit = 50)
    {
    	$tbname = $this->getCoinTableName($uid, $yearmonth);
    	$sql = "SELECT cost,summary,create_time FROM $tbname WHERE uid=:uid ORDER BY create_time DESC";
    	if ($limit > 0) {
    		$sql .= ' LIMIT ' . $limit;
    	}
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('uid' => $uid));
    }
    
    public function insertCoin($uid, $info)
    {
        $yearmonth = date('Ym', $info['create_time']);
    	$tbname = $this->getCoinTableName($uid, $yearmonth);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info); 	
    }
    
    public function clearCoin($uid, $yearmonth)
    {
        $tbname = $this->getCoinTableName($uid, $yearmonth);
        
        $sql = "DELETE FROM $tbname WHERE uid=:uid";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid));
    }
    
    public function getGem($uid, $yearmonth, $limit = 50)
    {
    	$tbname = $this->getGemTableName($uid, $yearmonth);
    	$sql = "SELECT cost,summary,create_time FROM $tbname WHERE uid=:uid ORDER BY create_time DESC";
    	if ($limit > 0) {
    		$sql .= ' LIMIT ' . $limit;
    	}
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('uid' => $uid));
    }
    
    public function insertGem($uid, $info)
    {
        $yearmonth = date('Ym', $info['create_time']);
    	$tbname = $this->getGemTableName($uid, $yearmonth);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
    	return $wdb->insert($tbname, $info); 	
    }
    
    public function clearGem($uid, $yearmonth)
    {
        $tbname = $this->getGemTableName($uid, $yearmonth);
        
        $sql = "DELETE FROM $tbname WHERE uid=:uid";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid));
    }    
}