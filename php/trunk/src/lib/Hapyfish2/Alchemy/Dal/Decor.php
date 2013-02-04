<?php


class Hapyfish2_Alchemy_Dal_Decor
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_Decor
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getBagTableName($uid)
    {
        if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE == 3) {
            $id = 0;
        } else {
        	$id = floor($uid/DATABASE_NODE_NUM) % 50;
        }
        
    	return 'alchemy_user_decor_inbag_' . $id;
    }

    public function getInBag($uid)
    {
        $tbname = $this->getBagTableName($uid);
    	$sql = "SELECT cid,count FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchPairs($sql, array('uid' => $uid));
    }

    public function updateInBag($uid, $cid, $count)
    {
        $tbname = $this->getBagTableName($uid);
        $sql = "INSERT INTO $tbname (uid, cid, count) VALUES($uid, $cid, $count) ON DUPLICATE KEY UPDATE count=$count";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $wdb->query($sql);
    }

    public function getSceneTableName($uid)
    {
        if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE == 3) {
            $id = 0;
        } else {
        	$id = floor($uid/DATABASE_NODE_NUM) % 10;
        }
        
    	return 'alchemy_user_decor_' . $id;
    }
    
    public function getInScene($uid)
    {
        $tbname = $this->getSceneTableName($uid);
    	$sql = "SELECT id,cid,x,z,m FROM $tbname WHERE uid=:uid AND s=1";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchAll($sql, array('uid' => $uid));
    }
    
    public function insertInScene($uid, $info)
    {
        $tbname = $this->getSceneTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        return $wdb->insert($tbname, $info);
    }
    
    public function updateInScene($uid, $id, $info)
    {
        $tbname = $this->getSceneTableName($uid);
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	$uid = $wdb->quote($uid);
        $id = $wdb->quote($id);
    	$where = "uid=$uid AND id=$id";

        return $wdb->update($tbname, $info, $where);
    }
    
    public function getSceneEmptyId($uid)
    {
    	$tbname = $this->getSceneTableName($uid);
    	$sql = "SELECT id FROM $tbname WHERE uid=:uid AND s=0 LIMIT 1";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }

}