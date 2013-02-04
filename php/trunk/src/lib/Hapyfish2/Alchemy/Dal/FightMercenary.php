<?php


class Hapyfish2_Alchemy_Dal_FightMercenary
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_FightMercenary
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
    	return 'alchemy_user_fight_mercenary_' . $id;
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

    public function getOwnMercenaryIds($uid)
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

    public function init($uid, $name)
    {
        $tbname = $this->getTableName($uid);

        $defaultAryChar = "'[]'";
        $sql = "INSERT INTO $tbname
        		(uid,mid,name,cid,rp,sex,element,exp,`level`,hp,hp_max,mp,mp_max,phy_att,phy_def,mag_att,mag_def,agility,crit,dodge,weapon,skill)
        		VALUES
				(:uid, 12, :name, 1, 15, 1, 1, 0, 1, 60, 60, 20, 20, 30, 30, 15, 15, 3, 30, 20, $defaultAryChar, $defaultAryChar)";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        return $wdb->query($sql, array('uid' => $uid, 'name' => $name));
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