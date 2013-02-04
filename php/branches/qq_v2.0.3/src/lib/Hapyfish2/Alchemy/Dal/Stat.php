<?php

/**
 * Stat DAL Class
 *
 */
class Hapyfish2_Alchemy_Dal_Stat
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_Stat
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * get the table name by Stat's id
     *
     * @param int $uid
     * @return string
     */
    public function getTableName($uid)
    {
    	return 'alchemy_user_stat';
    }

    /**
     * get user's stat step
     *
     * @param int $uid
     * @return int|false
     */
    public function getStep($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT step FROM $tbname WHERE uid=:uid";

    	$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];

        $data = $rdb->fetchOne($sql, array('uid' => $uid));

        return $data;
    }

    /**
     * get user's stat step list
     *
     * @param int $uid
     * @return int|false
     */
    public function getStepList($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT step_list FROM $tbname WHERE uid=:uid";

    	$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];

        $data = $rdb->fetchOne($sql, array('uid' => $uid));

        return $data;
    }
    
    /**
     * update user stat info
     *
     * @param int $uid
     * @param int $step
     * @return bool
     */
    public function insUpd($uid, $step, $list, $createTm, $lastLoginTm)
    {
        $tbname = $this->getTableName($uid);
        $sql = "INSERT INTO $tbname (uid, `step`, `step_list`, `create_time`, `last_login_time`)
        		VALUES (:uid, :step, :list, :create_time, :last_login_time)
        		ON DUPLICATE KEY UPDATE `step`=:step, `step_list`=:list, `create_time`=:create_time, `last_login_time`=:last_login_time";

        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid, 'step' => $step, 'list' => $list, 'create_time' => $createTm, 'last_login_time' => $lastLoginTm));
    }

}