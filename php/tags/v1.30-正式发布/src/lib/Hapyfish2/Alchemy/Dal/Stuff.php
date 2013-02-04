<?php


class Hapyfish2_Alchemy_Dal_Stuff
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_Stuff
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
    	return 'alchemy_user_stuff_' . $id;
    }

    public function get($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT cid,count FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        $data = $rdb->fetchPairs($sql, array('uid' => $uid));
        if ($data != null) {
        	$result = array();
            foreach ($data as $cid => $count) {
        		$result[(int)$cid] = (int)$count;
        	}
        	return $result;
        }
        return null;
    }

    public function update($uid, $cid, $count)
    {
        $tbname = $this->getTableName($uid);
        $sql = "INSERT INTO $tbname (uid, cid, count) VALUES($uid, $cid, $count) ON DUPLICATE KEY UPDATE count=$count";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $wdb->query($sql);
    }

    public function getStuffCount($uid, $cid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "SELECT `count` FROM $tbname WHERE uid=:uid AND cid=:cid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchOne($sql, array('uid' => $uid, 'cid' => $cid));
        if ($data !== false) {
        	$data = (int)$data;
        }
        
        return $data;
    }

    public function getAll($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "SELECT * FROM $tbname WHERE uid=:uid";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        
        return $rdb->fetchAll($sql, array('uid' => $uid));
    }
    
}