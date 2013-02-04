<?php
class Hapyfish2_Stat_Dal_Stat2x
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
    
    public function getMain($day)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select * from day_fight where `date`={$day}";
    	return $rdb->fetchAll($sql);
    }
    
    public function getMonster($day)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select * from day_monster where `date`={$day}";
    	return $rdb->fetchAll($sql);
    }
    
    public function getMater($day)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select * from day_mater where `date`={$day}";
    	return $rdb->fetchAll($sql);
    }
    
    public function getMitial($day)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select * from day_mutual where `date`={$day}";
    	return $rdb->fetchRow($sql);
    }
    
    public function getRepair($day)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select * from day_repair where `date`={$day}";
    	return $rdb->fetchRow($sql);
    }
    
}