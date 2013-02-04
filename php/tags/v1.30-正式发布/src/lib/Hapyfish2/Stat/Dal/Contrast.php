<?php

class Hapyfish2_Stat_Dal_Contrast
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_Fight
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
 	protected function getDB()
    {
    	$key = 'db_0';
    	return Hapyfish2_Db_Factory::getBasicDB($key);
    }

   	public function getData($tb)
   	{
   		$db = $this->getDB();
   		$rdb = $db['r'];
   		$sql = " select * from {$tb}";
   		return $rdb->fetchAll($sql);
   	}
}