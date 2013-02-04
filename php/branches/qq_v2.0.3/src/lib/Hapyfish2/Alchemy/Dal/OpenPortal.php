<?php


class Hapyfish2_Alchemy_Dal_OpenPortal
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_OpenPortal
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
    	return 'alchemy_user_openportal_' . $id;
    }

    public function get($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT open_portal FROM $tbname WHERE uid=:uid";

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
    
    public function insUpd($uid, $info)
    {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $sql = "INSERT INTO $tbname (uid, `open_portal`)
        		VALUES (:uid, :openPortal)
        		ON DUPLICATE KEY UPDATE `open_portal`=:openPortal";

        return $wdb->query($sql, array('uid'=>$uid, 'openPortal'=>$info['open_portal']));
    }
        
    public function init($uid)
    {
        $tbname = $this->getTableName($uid);
        
        $sql = "INSERT INTO $tbname(`uid`,`open_portal`) 
        		VALUES(:uid,'')";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid));
    }
    
    
}