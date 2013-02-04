<?php


class Hapyfish2_Alchemy_Dal_Order
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_Order
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
    	return 'alchemy_user_order_' . $id;
    }

    public function get($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT order FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }

    public function insert($uid, $info)
    {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $ret = $wdb->insert($tbname, $info);
        return $ret;
    }
    
    public function update($uid, $info)
    {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	$uid = $wdb->quote($uid);
    	$where = "uid=$uid";
        return $wdb->update($tbname, $info, $where);
    }

    public function getOne($uid, $id)
    {
        $tbname = $this->getTableName($uid);
        $sql = "SELECT id,needs,out_time,start_time,state,awards,avatar_id,dialog 
                FROM $tbname WHERE id=:id AND uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('id' => $id, 'uid' => $uid), Zend_Db::FETCH_NUM);
    }
    
    /**
     * 用户当前已接订单数
     * 
     * @param $uid
     */
    public function getUserCurOrderCount($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "SELECT COUNT(1) FROM $tbname WHERE uid=:uid AND state>1";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchOne($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }

    public function init($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "INSERT INTO $tbname(uid, `order`) VALUES(:uid, '[]')";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $wdb->query($sql, array('uid' => $uid));
    }
    
    
    
}