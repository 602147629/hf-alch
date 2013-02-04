<?php

class Hapyfish2_Project_Dal_AppCacheVersion
{
    protected static $_instance;

    protected function getDB()
    {
    	$key = 'db_0';
    	return Hapyfish2_Db_Factory::getBasicDB($key);
    }

    /**
     * Single Instance
     *
     * @return Hapyfish2_Project_Dal_AppCacheVersion
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function get($key = 'def')
    {
        $sql = "SELECT `val` FROM app_cache_version WHERE keyname=:keyname";
        
    	$db = $this->getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchOne($sql, array('keyname' => $key));
    }
    
    public function update($key, $value)
    {
        $tbname = 'app_cache_version';
        
        $db = $this->getDB();
        $wdb = $db['w'];
    	$where = $wdb->quoteinto('keyname = ?', $key);
    	$info = array('val' => $value);
        $wdb->update($tbname, $info, $where);
    }
}