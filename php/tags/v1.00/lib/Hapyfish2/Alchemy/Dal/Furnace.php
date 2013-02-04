<?php


class Hapyfish2_Alchemy_Dal_Furnace
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_Furnace
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
    	if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE == 3) {
            $id = 0;
        } else {
        	$id = floor($uid/DATABASE_NODE_NUM) % 10;
        }
    	return 'alchemy_user_furnace_' . $id;
    }

    public function getAll($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT id,furnace_id,x,z,m,cid,need_time,cur_probability,num,status FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        
        $data = $rdb->fetchAll($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
        if ($data != null) {
        	foreach ($data as &$row) {
        		for($i = 0, $len = count($row); $i < $len; $i++) {
        			$row[$i] = (int)$row[$i];
        		}
        	}
        }
        
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
        $sql = "SELECT id,furnace_id,x,z,m,cid,start_time,need_time,cur_probability,num,status FROM $tbname WHERE id=:id AND uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $row = $rdb->fetchRow($sql, array('id' => $id, 'uid' => $uid), Zend_Db::FETCH_NUM);
        if ($row != null) {
            for($i = 0, $len = count($row); $i < $len; $i++) {
       			$row[$i] = (int)$row[$i];
       		}
        }
        
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
    
    public function init($uid, $id, $cid)
    {
    	$info = array('uid' => $uid, 'id' => $id, 'furnace_id' => $cid);
    	$this->insert($uid, $info);
    }

}