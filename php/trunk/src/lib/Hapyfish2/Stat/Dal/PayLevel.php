<?php
class Hapyfish2_Stat_Dal_PayLevel
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
    
    
    
    public function getPayLevel($day)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select * from day_PayLevel where `date`={$day}";
    	return $rdb->fetchRow($sql);
    }
    
    public function getGemLevel($day)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select * from day_GemLevel where `date`={$day}";
    	return $rdb->fetchRow($sql);
    }
    
    public function getBuyLevel($day)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select * from day_BuyLevel where `date`={$day}";
    	return $rdb->fetchRow($sql);
    }
    
}