<?php

class Hapyfish2_Alchemy_Dal_EverySign {

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
        return 'alchemy_user_every_sign';
    }

    public function getEvertAward() {
        $sql = "SELECT id,award  FROM  alchemy_every_active ";
        $db = $this->getEventDB();
        $rdb = $db['r'];
        return $rdb->fetchAll($sql);
    }

    public function getEvertLabaAward() {
        $sql = "SELECT day,normal_award,vip_frist_award,vip_second_award  FROM  alchemy_every_award ";
        $db = $this->getEventDB();
        $rdb = $db['r'];
        return $rdb->fetchAll($sql);
    }

    public function getEveryNotice() {
        $sql = "SELECT sign_notice FROM  alchemy_sign_notice ";
        $db = $this->getEventDB();
        $rdb = $db['r'];
        return $rdb->fetchAll($sql);
    }

    public function getUserInfo($uid) {
        $sql = "SELECT every_day,sign_time,award,award_five_sign,award_seven_sign  FROM  alchemy_user_every_sign WHERE user_id = :user_id";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchAll($sql, array('user_id' => $uid));
    }

    public function updateEventGift($uid, $id, $type) {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_user_event_gift (uid, id, `type`) VALUES(:uid, :id, :type) ON DUPLICATE KEY UPDATE id=:id";
        return $wdb->query($sql, array('uid' => $uid, 'id' => $id, 'type' => $type));
    }

    public function InsertUserEvery($uid, $data) {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_user_every_sign (every_day,sign_time,award,user_id,award_five_sign,award_seven_sign) VALUES(:every_day,:sign_time,:award,:user_id,:award_five_sign,:award_seven_sign)";
        return $wdb->query($sql, array('every_day' => json_encode($data['every_day']), 'sign_time' => $data['sign_time'], 'award' => json_encode($data['award']), 'user_id' => $uid, 'award_five_sign' => 0, 'award_seven_sign' => 0));
    }

//    public function updateInfoA($uid,$info) {
//        $db = Hapyfish2_Db_Factory::getDB($uid);
//        $wdb = $db['w'];
//        $sql = "UPDATE alchemy_user_every_sign  SET every_day =:every_day,sign_time= :sign_time,award =:award,award_five_sign=:award_five_sign,award_seven_sign=:award_seven_sign WHERE user_id =:user_id";
//        $wdb->query($sql, array('award' => $info['award'],'every_day'=> $info['every_day'],'sign_time'=> $info['sign_time'],'award_five_sign'=> $info['award_five_sign'],'award_seven_sign'=> $info['award_seven_sign'],'user_id'=>$uid));
//    }
    public function update($uid, $info) {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $uid = $wdb->quote($uid);
        $where = "user_id=$uid";
        return $wdb->update($tbname, $info, $where);
    }

}
