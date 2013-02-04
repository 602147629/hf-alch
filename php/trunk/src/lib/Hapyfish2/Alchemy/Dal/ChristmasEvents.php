<?php

class Hapyfish2_Alchemy_Dal_ChristmasEvents {

    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_Fight
     */
    public static function getDefaultInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    protected function getDB() {
        $key = 'db_0';
        return Hapyfish2_Db_Factory::getBasicDB($key);
    }

    protected function getEventDB() {
        $key = 'db_0';
        return Hapyfish2_Db_Factory::getEventDB($key);
    }

    public function getTableName($uid) {
        $id = floor($uid / DATABASE_NODE_NUM) % 10;
        if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE == 3) {
            $id = floor($uid / DATABASE_NODE_NUM) % 1;
        }
        return 'alchemy_user_bless_' . $id;
    }

    public function getTableLoginName($uid) {
        $id = floor($uid / DATABASE_NODE_NUM) % 10;
        if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE == 3) {
            $id = floor($uid / DATABASE_NODE_NUM) % 1;
        }
        return 'alchemy_user_bless_login_' . $id;
    }

    public function getTableChistmasInfoName($uid) {
        $id = floor($uid / DATABASE_NODE_NUM) % 10;
        if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE == 3) {
            $id = floor($uid / DATABASE_NODE_NUM) % 1;
        }
        return 'alchemy_user_chistmas_ernie_' . $id;
    }

    public function getUserByBless($uid, $bless_time) {
        $tablename = $this->getTableName($uid);
        $sql = "SELECT  bless_time,uid,fid,bless_count  FROM  $tablename  WHERE  uid =:uid";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

    public function InsertUserBless($uid, $data) {
        $tablename = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $sql = "INSERT INTO $tablename (uid,bless_time,fid,bless_count) VALUES(:uid,:bless_time,:fid,:bless_count)";
        return $wdb->query($sql, array('uid' => $uid, 'bless_time' => $data['bless_time'], 'fid' => $data['fid'], 'bless_count' => $data['bless_count']));
    }

    public function updateInfo($uid, $data) {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $uid = $wdb->quote($uid);
        $where = "uid=$uid";
        return $wdb->update($tbname, $data, $where);
    }

    public function getUserChistmasLogin($uid) {
        $tablename = $this->getTableLoginName($uid);
        $sql = "SELECT  login_time,login_uid,login_times  FROM  $tablename  WHERE login_uid =:login_uid";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchRow($sql, array('login_uid' => $uid));
    }

    public function InsertUserChistmasLogin($uid, $data) {
        $tablename = $this->getTableLoginName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $sql = "INSERT INTO $tablename (login_uid,login_time,login_times) VALUES(:login_uid,:login_time,:login_times)";
        return $wdb->query($sql, array('login_uid' => $uid, 'login_time' => $data['login_time'], 'login_times' => $data['login_times']));
    }

    public function updateChistmasLoginInfo($uid, $data) {
        $tbname = $this->getTableLoginName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $uid = $wdb->quote($uid);
        $where = "login_uid=$uid";
        return $wdb->update($tbname, $data, $where);
    }

    public function getUserChistmasInfo($uid) {
        $tablename = $this->getTableChistmasInfoName($uid);
        $sql = "SELECT  user_ernie_time,user_ernie_bless,user_ernie_times,user_ernie_uid,user_ernie_count,user_sina_attention,user_christmas_crystallization  FROM  $tablename  WHERE user_ernie_uid =:user_ernie_uid ";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchRow($sql, array('user_ernie_uid' => $uid));
    }

    public function InsertUserChistmasErnie($uid, $data) {
        $tablename = $this->getTableChistmasInfoName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $sql = "INSERT INTO $tablename (user_ernie_uid,user_ernie_time,user_ernie_count,user_sina_attention,user_ernie_bless) VALUES(:user_ernie_uid,:user_ernie_time,:user_ernie_count,:user_sina_attention,:user_ernie_bless)";
        return $wdb->query($sql, array('user_ernie_uid' => $uid, 'user_ernie_time' => $data['user_ernie_time'], 'user_ernie_count' => $data['user_ernie_count'], 'user_sina_attention' => $data['user_sina_attention'],'user_ernie_bless'=>$data['user_ernie_bless']));
    }

    public function updateChistmasUserInfo($uid, $data) {
        $tbname = $this->getTableChistmasInfoName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $uid = $wdb->quote($uid);
        $where = "user_ernie_uid=$uid";
        return $wdb->update($tbname, $data, $where);
    }

}
