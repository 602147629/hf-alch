<?php

class Hapyfish2_Alchemy_Dal_UserSequence
{
    protected static $_instance;

    protected static $_seqMap = array(
    		'mercenary' 	=> 'a',//用户佣兵实例id
    		'b' 			=> 'b',
    		'c' 			=> 'c',
    		'd' 			=> 'd',	//用户装饰物
    		'e' 			=> 'e',
    		'fight' 		=> 'f', //用户战斗实例id
    		'weapon' 		=> 'g', //用户装备实例id
    		'furnace' 		=> 'h', //用户工作台实例id
    		'order' 		=> 'i', //经营订单实例id
    		'feed' 			=> 'j', //日志消息id
    );

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_UserSequence
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
    	return 'alchemy_user_seq_' . $id;
    }

    public function getId($uid, $key)
    {
    	$name = self::$_seqMap[$key];
    	return $this->get($uid, $name);
    }

    public function get($uid, $name, $step = 1)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "UPDATE $tbname SET id=LAST_INSERT_ID(id+$step) WHERE uid=:uid AND `name`=:name";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $wdb->query($sql, array('uid' => $uid, 'name' => $name));
    	$pid = (int)$wdb->lastInsertId();
    	if (empty($pid)) {
    		$pid = 100;
    		$sql = "INSERT INTO $tbname(uid, name, id) VALUES(:uid, :name, $pid)";
    		$wdb->query($sql, array('uid' => $uid, 'name' => $name));
    	}
        return $pid;
    }

    public function init($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "INSERT INTO $tbname(uid, name, id) VALUES(:uid, 'a', 100),(:uid, 'b', 100),(:uid, 'f', 100)";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $wdb->query($sql, array('uid' => $uid));
    }

    public function clear($uid)
    {
        $tbname = $this->getTableName($uid);

        $sql = "DELETE FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->query($sql, array('uid' => $uid));
    }

    public function insert($uid, $info)
    {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $ret = $wdb->insert($tbname, $info);
        return $ret;
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