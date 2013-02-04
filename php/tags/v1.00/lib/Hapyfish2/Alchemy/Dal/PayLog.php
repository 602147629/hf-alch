<?php


class Hapyfish2_Alchemy_Dal_PayLog
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_PayLog
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getPayLogTableName($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
        if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE==3) {
            $id = floor($uid/DATABASE_NODE_NUM) % 1;
        }
    	return 'alchemy_user_paylog_' . $id;
    }

    public function listPayLog($uid, $limit = 50)
    {
    	$tbname = $this->getPayLogTableName($uid);
    	$sql = "SELECT * FROM $tbname WHERE uid=:uid ORDER BY create_time DESC";
    	if ($limit > 0) {
    		$sql .= ' LIMIT ' . $limit;
    	}

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchAll($sql, array('uid' => $uid));
    }

    public function insert($uid, $info)
    {
		$tbname = $this->getPayLogTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
    	return $wdb->insert($tbname, $info);
    }

}