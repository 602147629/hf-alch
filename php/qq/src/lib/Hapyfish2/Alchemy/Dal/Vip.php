<?php


class Hapyfish2_Alchemy_Dal_Vip
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
    
    protected function getEventDB()
    {
    	$key = 'db_0';
    	return Hapyfish2_Db_Factory::getEventDB($key);
    }
    
    public function getUserInfo($uid)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$rdb = $db['r'];
    	$sql = "select uid,`starttime`,`endtime`,growup,`level`,`type`,settlementtime from alchemy_user_vip where uid=:uid ";
    	return $rdb->fetchRow($sql,array('uid'=>$uid));
    }
    
    public function insertVip($data)
    {
    	$db = Hapyfish2_Db_Factory::getDB($data['uid']);
    	$wdb = $db['w'];
    	$sql = "INSERT INTO alchemy_user_vip (uid,`starttime`,`endtime`,`growup`,`level`,`type`,settlementtime) VALUES(:uid, :starttime, :endtime, :growup, :level, :type, :settlementtime) ON DUPLICATE KEY UPDATE `starttime`=:starttime,`endtime`=:endtime,growup=:growup,level=:level,`type`=:type,settlementtime=:settlementtime";
    	return $wdb->query($sql, array('uid'=>$data['uid'], 'starttime'=>$data['starttime'], 'endtime'=>$data['endtime'],'growup'=>$data['growup'],'level'=>$data['level'],'type'=>$data['type'], 'settlementtime'=> $data['settlementtime']));
    }
    
    public function getVipAddition()
    {
    	$db = $this->getDB();
    	$rdb = $db['r'];
    	$sql = "select `level`, addition, daliy from alchemy_vip_privilege";
    	return $rdb->fetchAssoc($sql);
    }
    
    public function getLevelAward($uid)
    {
    	$db = $this->getEventDB();
    	$rdb = $db['r'];
    	$sql = "select uid, `step` from alchemy_event_get where uid=:uid and `type`=3";
    	return $rdb->fetchRow($sql,array('uid'=>$uid));
    }
    
    public function clearVip($uid)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$wdb = $db['w'];
    	$sql = "delete from alchemy_user_vip where uid=:uid";
    	return $wdb->query($sql, array('uid'=>$uid));
    }
}