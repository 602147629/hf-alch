<?php
class Hapyfish2_Stat_Dal_Upgrade
{
 protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Stat_Dal_ActiveUserLevel
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function insert($tb, $data)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
    	return $wdb->insert($tb, $data);
    }
    
    
    public function getUpgrade($day)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select * from day_upgrade where `date`={$day}";
    	return $rdb->fetchRow($sql);
    }
    
}