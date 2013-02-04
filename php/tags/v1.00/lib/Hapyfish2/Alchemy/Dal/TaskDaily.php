<?php

class Hapyfish2_Alchemy_Dal_TaskDaily
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_TaskDaily
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getTableName($uid)
    {
        $id = floor($uid/DATABASE_NODE_NUM) % 10;
        if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE==3) {
            $id = floor($uid/DATABASE_NODE_NUM) % 1;
        }
    	return 'alchemy_user_task_daily_' . $id;
    }

    public function get($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT `list`,`data`,refresh_tm FROM $tbname WHERE uid=:uid";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }

    public function update($uid, $info)
    {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
    	$where = $wdb->quoteinto('uid = ?', $uid);
        return $wdb->update($tbname, $info, $where);
    }

    public function init($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "INSERT INTO $tbname(uid, `list`, `data`, refresh_tm) VALUES(:uid, '[]', '[]', 0)";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        return $wdb->query($sql, array('uid' => $uid));
    }

    public function clear($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "DELETE FROM $tbname WHERE uid=:uid";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        return $wdb->query($sql, array('uid' => $uid));
    }
}