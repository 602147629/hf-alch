<?php

class Hapyfish2_Alchemy_Dal_DiamondExchange {

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
        return 'alchemy_user_change';
    }

    public function getDiamondExchange() {
        $sql = "SELECT  user_level,gold_change,brova_change,brova_diamondcost,gold_diamondcost  FROM  alchemy_event_change ";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAll($sql);
    }

    public function getUserDiamondExchange($uid) {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        $sql = "SELECT uid,goldchanger,bravechanger,change_data FROM alchemy_user_change WHERE uid = :uid";
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

    public function InsertUserDiamondExchange($uid, $data) {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_user_change (uid,goldchanger,bravechanger,change_data) VALUES(:uid,:goldchanger,:bravechanger,:change_data)";
        return $wdb->query($sql, array('uid' => $uid, 'goldchanger' => $data['goldchanger'], 'bravechanger' => $data['bravechanger'], 'change_data' => $data['change_data']));
    }

    public function updateInfo($uid, $data) {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $sql = "update alchemy_user_change set goldchanger=:goldchanger, bravechanger=:bravechanger,change_data=:change_data where uid=:uid";
        return $wdb->query($sql, array('uid' => $uid, 'goldchanger' => $data['goldchanger'], 'bravechanger' => $data['bravechanger'], 'change_data' => $data['change_data']));
    }

}
