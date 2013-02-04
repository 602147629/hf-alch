<?php

class Hapyfish2_Project_Dal_AppServer
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
     * @return Hapyfish2_Project_Dal_AppServer
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getServerList()
    {
    	$sql = "SELECT id,name,pub_ip,local_ip,type,add_time FROM app_server";
    	
        $db = $this->getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAssoc($sql);
    }
    
    public function getWebServerList()
    {
    	$sql = "SELECT id,name,pub_ip,local_ip,type,add_time FROM app_server WHERE type='WEB'";
    	
        $db = $this->getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAssoc($sql);
    }

}