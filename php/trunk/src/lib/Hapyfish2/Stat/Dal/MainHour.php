<?php


class Hapyfish2_Stat_Dal_MainHour
{
    protected static $_instance;
    
    private $_tb_day_main_hour = 'day_main_hour';

    private $_tb_day_stat_main_hour = 'day_stat_main_hour';
    
    /**
     * Single Instance
     *
     * @return Hapyfish2_Stat_Dal_MainHour
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getDay($day)
    {
    	$tbname = $this->_tb_day_main_hour;
    	$stime = $day . '00';
    	$etime = $day . '23';
    	$sql = "SELECT log_time,add_user,active_user FROM $tbname WHERE log_time>=:stime AND log_time<=:etime";
    	
        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('stime' => $stime, 'etime' => $etime));
    }
    
    public function getRow($time)
    {
    	$tbname = $this->_tb_day_stat_main_hour;
    	$sql = "SELECT * FROM $tbname WHERE log_time=:log_time";

        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('log_time' => $time));
    }

    public function deleteRow($time)
    {
        $tbname = $this->_tb_day_stat_main_hour;
        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
    	$sql = "DELETE FROM $tbname WHERE log_time=:log_time ";

        return $wdb->query($sql, array('log_time'=>$time));
    }
    
    public function insertStat($info)
    {
    	$tbname = $this->_tb_day_stat_main_hour;
        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];

        return $wdb->insert($tbname, $info);
    }
    
    
    
}