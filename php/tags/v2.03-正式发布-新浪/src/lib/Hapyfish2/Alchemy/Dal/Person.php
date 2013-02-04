<?php


class Hapyfish2_Alchemy_Dal_Person
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_Person
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
    	return 'alchemy_user_map_copy_person_' . $id;
    }

    public function get($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT list FROM $tbname WHERE uid=:uid";
    	
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

    public function init($uid)
    {
        $tbname = $this->getTableName($uid);
        $temp = array();
        $temp = json_encode($temp);
        
        $sql = "INSERT INTO $tbname(uid, `list`) VALUES(:uid, '$temp')";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $wdb->query($sql, array('uid' => $uid));
    }
    
}