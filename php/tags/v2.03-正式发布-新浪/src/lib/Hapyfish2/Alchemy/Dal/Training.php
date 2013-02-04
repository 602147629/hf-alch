<?php


class Hapyfish2_Alchemy_Dal_Training
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_Training
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
    	return 'alchemy_user_fight_mercenary_training_' . $id;
    }

    public function getAll($uid)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT * FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchAll($sql, array('uid' => $uid));
    }

    public function getOne($uid, $id)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT * FROM $tbname WHERE uid=:uid AND mid=:mid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchRow($sql, array('uid' => $uid, 'mid' => $id));
    }

    public function getIds($uid)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT mid FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        $ids = $rdb->fetchAll($sql, array('uid' => $uid));
        if (empty($ids)) {
        	return '';
        } else {
        	$d = array();
        	foreach ($ids as $v) {
        		$d[] = $v['mid'];
        	}
        	return implode(',', $d);
        }
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
    	$where = "uid=$uid AND mid=$id";
    	
        return $wdb->update($tbname, $info, $where);
    }

    public function delete($uid, $id)
    {
        $tbname = $this->getTableName($uid);
        $sql = "DELETE FROM $tbname WHERE uid=:uid AND mid=:mid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        return $wdb->query($sql, array('uid' => $uid, 'mid' => $id));
    }


    public function clear($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "DELETE FROM $tbname WHERE uid=:uid ";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        return $wdb->query($sql, array('uid' => $uid));
    }
}