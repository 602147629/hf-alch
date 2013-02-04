<?php

class Hapyfish2_Alchemy_Dal_TaskOpen
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_TaskOpen
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
    	return 'alchemy_user_task_open_' . $id;
    }

    public function get($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT `list`,`list2`,`data`,buffer_list FROM $tbname WHERE uid=:uid";
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

    public function init($uid, $tskOpen=array(), $tskPrepare=array())
    {
        $tbname = $this->getTableName($uid);
        $list = json_encode($tskOpen);
        $list2 = json_encode($tskPrepare);
        $sql = "INSERT INTO $tbname(uid, `list`, `list2`, `data`, buffer_list) VALUES(:uid, :list, :list2, '[]', '[]')";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        return $wdb->query($sql, array('uid' => $uid, 'list' => $list, 'list2' => $list2));
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