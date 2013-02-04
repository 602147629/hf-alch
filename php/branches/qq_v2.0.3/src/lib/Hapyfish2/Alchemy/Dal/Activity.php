<?php

class Hapyfish2_Alchemy_Dal_Activity
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_Task
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function get($uid)
    {
    	$sql = "SELECT uid, activity, `step`, `update_time` FROM alchemy_user_activity WHERE uid=:uid";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

    public function insert($uid, $tid, $time)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "INSERT INTO $tbname(uid, tid, finish_time) VALUES(:uid, :tid, :time)";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        return $wdb->query($sql, array('uid' => $uid, 'tid' => $tid, 'time' => $time));
    }

    
    public function update($uid, $data)
    {
    	$sql = "INSERT INTO alchemy_user_activity(uid, activity, `step`, `update_time`) VALUES(:uid, :activity, :step, :update_time) ON DUPLICATE KEY UPDATE activity=:activity,`update_time`=:update_time,`step`=:step";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        return $wdb->query($sql, array('uid' => $uid, 'activity' => $data['activity'], 'step'=>$data['step'], 'update_time' => $data['update_time']));
    }

}