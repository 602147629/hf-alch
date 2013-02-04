<?php

class Hapyfish2_Alchemy_Event_Dal_Activity {

    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Event_Dal_Arena
     */
    public static function getDefaultInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getAll() {
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        $sql = "select actitivty_list_id,`actitivty_list_award` from alchemy_event_actitivty_list";
        return $rdb->fetchAssoc($sql);
    }

    public function getAllS() {
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        $sql = "select actitivty_list_id from alchemy_event_actitivty_list";
        return $rdb->fetchAssoc($sql);
    }

    public function getActivityCdkey($cdkey) {
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        $sql = 'select cdkey_id,cdkey_limit,cdkey_type,cdkey_use,cdkey_used,cdkey_actitivty_id,cdkey_user_id,cdkey_times from alchemy_event_actitivty_cdkey where cdkey_cardNo = :cdkey_cardNo';
        return $rdb->fetchRow($sql, array('cdkey_cardNo' => $cdkey));
    }

    public function getone($id) {
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        $sql = "select actitivty_list_id,actitivty_list_award from alchemy_event_actitivty_list where actitivty_list_id=:actitivty_list_id";
        return $rdb->fetchRow($sql, array('actitivty_list_id' => $id));
    }

    public function updateActitityCdkey($data) {
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_event_actitivty_cdkey (cdkey_id,cdkey_use,cdkey_used) VALUES(:cdkey_id, :cdkey_use, :cdkey_used) ON DUPLICATE KEY UPDATE cdkey_use=:cdkey_use,cdkey_used=:cdkey_used";
        return $wdb->query($sql, array('cdkey_id' => $data['cdkey_id'], 'cdkey_use' => $data['cdkey_use'], 'cdkey_used' => $data['cdkey_used']));
    }

    public function InsertUserCdkeyActitity($uid, $data) {  //插入cdkey 玩家兑换记录
        $db = Hapyfish2_Db_Factory::getDB(0);
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_user_cdkey (recode_cdkey_type,recode_user_id,atitivty_cdkey_id) VALUES(:recode_cdkey_type, :recode_user_id,:atitivty_cdkey_id)";
        $wdb->query($sql, array('recode_cdkey_type' => $data['cdkey_type'], 'recode_user_id' => $uid, 'atitivty_cdkey_id' => $data['cdkey_id']));
    }

    public function getCdkeyUserRecord($uid, $cdkey_type) {  //获取该用户在此批cdkey中 使用的次数
        $db = Hapyfish2_Db_Factory::getDB(0);
        $rdb = $db['r'];
        $sql = 'select count(recode_cdkey_type) as type_count from alchemy_user_cdkey where recode_user_id = :recode_user_id AND recode_cdkey_type = :recode_cdkey_type';
        return $rdb->fetchRow($sql, array('recode_user_id' => $uid, 'recode_cdkey_type' => $cdkey_type));
    }

    public function updateUserDemond($data, $uid) {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_user_diamond (uid,award_count,award_time) VALUES(:uid, :award_count,:award_time) ON DUPLICATE KEY UPDATE award_count=:award_count,award_time=:award_time";
        $wdb->query($sql, array('uid' => $uid, 'award_count' => $data['award_count'], 'award_time' => $data['award_time']));
    }

    public function updateUserDemondFeed($data, $uid) {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_user_demond_feed (uid,feed_result,feed_time) VALUES(:uid, :feed_time,:feed_result) ON DUPLICATE KEY UPDATE feed_time=:feed_time,feed_result=:feed_result";
        $wdb->query($sql, array('uid' => $uid, 'feed_result' => $data['feed_result'], 'feed_time' => $data['feed_time']));
    }

    public function InsertUserLoading($uid) {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_ (cdkey_id,cdkey_use,cdkey_used) VALUES(:cdkey_id, :cdkey_use, :cdkey_used) ON DUPLICATE KEY UPDATE cdkey_use=:cdkey_use,cdkey_used=:cdkey_used";
        return $wdb->query($sql, array('cdkey_id' => $data['cdkey_id'], 'cdkey_use' => $data['cdkey_use'], 'cdkey_used' => $data['cdkey_used']));
    }

    public function getAllLoading() {
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        $sql = "select award  from alchemy_loading_gift";
        return $rdb->fetchAssoc($sql);
    }

    public function updateLoadingGift($award) {
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        $sql = "UPDATE   alchemy_loading_gift set award=:award  WHERE  gift_type = 1";
        $wdb->query($sql, array('award' => $award));
    }

    public function getEveryNotice() {
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        $sql = "select sign_notice_id,sign_notice from alchemy_sign_notice";
        return $rdb->fetchAssoc($sql);
    }

    public function getEveryLabaAward() {
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        $sql = "select day,normal_award,vip_second_award,vip_frist_award from  alchemy_every_award";
        return $rdb->fetchAssoc($sql);
    }

    public function InsertEveryNotice($data) {
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        if ($data['type'] == 0) {
            $sql = "INSERT INTO alchemy_sign_notice (sign_notice) VALUES(:sign_notice)";
            $wdb->query($sql, array('sign_notice' => $data['sign_notice']));
        } else {
            $sql = "INSERT INTO alchemy_sign_notice (sign_notice_id, sign_notice) VALUES(:sign_notice_id, :sign_notice) ON DUPLICATE KEY UPDATE sign_notice=:sign_notice";
            $wdb->query($sql, array('sign_notice_id' => $data['sign_notice_id'], 'sign_notice' => $data['sign_notice']));
        }
    }

    public function DeleteEveryNotice($id) {
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        $sql = "DELETE FROM alchemy_sign_notice WHERE  sign_notice_id=:id";
        return $wdb->query($sql, array('id' => $id));
    }

    public function updateEveryLabaAward($data) {
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_every_award (day,normal_award,vip_frist_award,vip_second_award) VALUES(:day, :normal_award, :vip_frist_award,:vip_second_award) ON DUPLICATE KEY UPDATE normal_award=:normal_award,vip_frist_award=:vip_frist_award,vip_second_award=:vip_second_award";
        return $wdb->query($sql, array('day' => $data['day'], 'normal_award' => $data['normal_award'], 'vip_frist_award' => $data['vip_frist_award'], 'vip_second_award' => $data['vip_second_award']));
    }
    public function getEveryFiveAndSevenAward() {
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        $sql = "SELECT *  FROM alchemy_every_active";
         return $rdb->fetchAssoc($sql);
    }

    public function updateEveryFiveAndSeven($data) {
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_every_active (id,award) VALUES(:id, :award) ON DUPLICATE KEY UPDATE award=:award";
        return $wdb->query($sql, array('id' => $data['id'], 'award' => $data['award']));
    }
}

?>