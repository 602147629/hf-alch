<?php


class Hapyfish2_Alchemy_Dal_MercenaryWork
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_MercenaryWork
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
    	return 'alchemy_user_mercenary_work_' . $id;
    }

    public function getAll($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT id,finish_time,role_ids,awards,state FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        $data = $rdb->fetchAll($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
        
        return $data;
    }

    public function getAllIds($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "SELECT id FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchCol($sql, array('uid' => $uid));
    }
    
    public function getOne($uid, $id)
    {
        $tbname = $this->getTableName($uid);
        $sql = "SELECT id,finish_time,role_ids,awards,state FROM $tbname WHERE id=:id AND uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $row = $rdb->fetchRow($sql, array('id' => $id, 'uid' => $uid), Zend_Db::FETCH_NUM);
        
        return $row;
    }
    
    public function insert($uid, $info)
    {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $ret = $wdb->insert($tbname, $info);
        return $ret;
    }
    
    public function update($uid, $id, $info)
    {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	$uid = $wdb->quote($uid);
        $id = $wdb->quote($id);
    	$where = "uid=$uid AND id=$id";
        return $wdb->update($tbname, $info, $where);
    }
    

}