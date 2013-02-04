<?php


class Hapyfish2_Alchemy_Dal_Weapon
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_Weapon
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
    	return 'alchemy_user_weapon_' . $id;
    }

    /*public function get($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "SELECT wid,cid,durability,status FROM $tbname WHERE uid=:uid";

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
    }*/

    public function get($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "SELECT cid,count,data FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        $data = $rdb->fetchAll($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
        if ($data != null) {
        	foreach ($data as &$row) {
        		for($i = 0, $len = count($row); $i < $len; $i++) {
        			if ( $i < 2 ) {
        				$row[$i] = (int)$row[$i];
        			}
        			else {
        				$row[$i] = $row[$i];
        			}
        		}
        	}
        }
        
        return $data;
    }
    public function getOne($uid, $wid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "SELECT wid,cid,durability,status FROM $tbname WHERE wid=:wid AND uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $row = $rdb->fetchRow($sql, array('wid' => $wid, 'uid' => $uid), Zend_Db::FETCH_NUM);
        if ($row != null) {
            for($i = 0, $len = count($row); $i < $len; $i++) {
       			$row[$i] = (int)$row[$i];
       		}
        }
        
        return $row;
    }

    public function getWeaponByCid($uid, $cid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "SELECT cid,count,data FROM $tbname WHERE cid=:cid AND uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('cid' => $cid, 'uid' => $uid));
    }
    
    public function insert($uid, $info)
    {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $ret = $wdb->insert($tbname, $info);
        return $ret;
    }

    public function update($uid, $cid, $info)
    {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $uid = $wdb->quote($uid);
        $cid = $wdb->quote($cid);
        $where = "uid=$uid AND cid=$cid";
        return $wdb->update($tbname, $info, $where);
    }

    public function delete($uid, $cid)
    {
        $tbname = $this->getTableName($uid);
        
        $sql = "DELETE FROM $tbname WHERE uid=:uid AND cid=:cid";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        $wdb->query($sql, array('uid' => $uid, 'cid' => $cid));
    }
    
}