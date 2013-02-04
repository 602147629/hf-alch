<?php
class Hapyfish2_Stat_Dal_StatHour
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
    	$sql = "INSERT INTO $tb (`date`,`allUser`,`payUser`,`oldUser`,`newUser`,`pay`,`dau`) VALUES(:date, :allUser, :payUser, :oldUser, :newUser,:pay,:dau) ON DUPLICATE KEY UPDATE `allUser`=:allUser,`payUser`=:payUser,oldUser=:oldUser,newUser=:newUser,pay=:pay,dau=:dau";
    	return $wdb->query($sql, array('date'=>$data['date'], 'allUser'=>$data['allUser'], 'payUser'=>$data['payUser'],'oldUser'=>$data['oldUser'],'newUser'=>$data['newUser'],'pay'=>$data['pay'],'dau'=>$data['dau']));
    }
    
    public function getHour($day)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select * from day_hour_user where `date`={$day}";
    	return $rdb->fetchRow($sql);
    }
    
}