<?php
class Hapyfish2_Stat_Dal_EventGift
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
    
    protected $_tb = 'day_eventGift';
    
    public function insert($tb, $data)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
    	return $wdb->insert($tb, $data);
    }
    public function getRepair($day)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select * from day_repair where `date`={$day}";
    	return $rdb->fetchRow($sql);
    }
    
    public function getNewUser($day)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select * from day_newUser where `date`={$day}";
    	return $rdb->fetchRow($sql);
    }
    
}