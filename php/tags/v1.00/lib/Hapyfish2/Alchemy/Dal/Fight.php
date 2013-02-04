<?php


class Hapyfish2_Alchemy_Dal_Fight
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_Fight
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
    	return 'alchemy_user_fight_' . $id;
    }

    public function getAll($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT * FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchAll($sql, array('uid' => $uid));
    }

    public function getAllIds($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT fid FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchCol($sql, array('uid' => $uid));
    }

    public function getOne($uid, $id)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT * FROM $tbname WHERE uid=:uid AND fid=:fid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchRow($sql, array('uid' => $uid, 'fid' => $id));
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
    	$where = "uid=$uid AND fid=$id";
        return $wdb->update($tbname, $info, $where);
    }

    public function insUpd($uid, $info)
    {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $sql = "INSERT INTO $tbname (uid, fid, `type`, enemy_id, `status`, rnd_element, home_side, enemy_side, content, create_time)
        		VALUES (:uid, :fid, :type, :enemy_id, :status, :rnd_element, :home_side, :enemy_side, :content, :create_time)
        		ON DUPLICATE KEY UPDATE `status`=:status, content=:content ";

        return $wdb->query($sql, array('uid'=>$uid, 'fid'=>$info['fid'], 'type'=>$info['type'], 'enemy_id'=>$info['enemy_id'], 'status'=>$info['status'],
        							   'rnd_element'=>$info['rnd_element'], 'home_side'=>$info['home_side'], 'enemy_side'=>$info['enemy_side'],
        							   'content'=>$info['content'], 'create_time'=>$info['create_time']));
    }

    public function delete($uid, $id)
    {
        $tbname = $this->getTableName($uid);
        $sql = "DELETE FROM $tbname WHERE uid=:uid AND fid=:id";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        return $wdb->query($sql, array('uid' => $uid, 'fid' => $id));
    }

    public function clear($uid, $tm=0)
    {
        $tbname = $this->getTableName($uid);
        $sql = "DELETE FROM $tbname WHERE uid=:uid ";
        if ($tm) {
            $sql .= " AND create_time<$tm ";
        }
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        return $wdb->query($sql, array('uid' => $uid));
    }
}