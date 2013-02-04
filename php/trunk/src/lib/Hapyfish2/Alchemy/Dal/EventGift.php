<?php

class Hapyfish2_Alchemy_Dal_EventGift {

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
        return 'alchemy_user_fight_' . $id;
    }

    public function getTimeGift() {
        $sql = "select `type`,id,`time`,next_id,list from alchemy_time_gift order by id";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAll($sql);
    }

    public function getUserEvent($uid, $type) {
        $sql = "select id from alchemy_user_event_gift where uid=:uid and type=:type";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchOne($sql, array('uid' => $uid, 'type' => $type));
    }

    public function getSevenGift() {
        $sql = "select `type`,`day`,awards from alchemy_seven_gift order by `day`";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAll($sql);
    }

    public function getLevelGift() {
        $sql = "select `type`,`level`,nextLevel,awards from alchemy_level_gift order by `level`";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAll($sql);
    }

    public function updateEventGift($uid, $id, $type) {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_user_event_gift (uid, id, `type`) VALUES(:uid, :id, :type) ON DUPLICATE KEY UPDATE id=:id";
        return $wdb->query($sql, array('uid' => $uid, 'id' => $id, 'type' => $type));
    }

    public function getMinLevel() {
        $sql = "select min(`level`) from alchemy_level_gift";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchOne($sql);
    }

    public function getMinYellowLevel() {
        $sql = "select min(`level`) from alchemy_yellow_gift";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchOne($sql);
    }

    public function clear($uid) {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $sql = "delete from alchemy_user_event_gift where uid=:uid";
        return $wdb->query($sql, array('uid' => $uid));
    }

    public function insertTgift($data) {
        $db = $this->getDB();
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_time_gift (id, next_id, `time`, list, `type`) VALUES(:id, :next_id, :time, :list, :type) ON DUPLICATE KEY UPDATE next_id=:next_id,time=:time,list=:list";
        return $wdb->query($sql, array('id' => $data['id'], 'next_id' => $data['next_id'], 'type' => $data['type'], 'list' => $data['list'], 'time' => $data['time']));
    }

    public function insertActivityGift($data) {
        $db = $this->getEventDB();
        $wdb = $db['w'];
        if ($data['type'] == 0) {
            $sql = "INSERT INTO alchemy_event_actitivty_list (actitivty_list_award) VALUES(:actitivty_list_award)";
            $wdb->query($sql, array('actitivty_list_award' => $data['list']));
        } else {
            $sql = "INSERT INTO alchemy_event_actitivty_list (actitivty_list_id, actitivty_list_award) VALUES(:actitivty_list_id, :actitivty_list_award) ON DUPLICATE KEY UPDATE actitivty_list_award=:actitivty_list_award";
            $wdb->query($sql, array('actitivty_list_id' => $data['id'], 'actitivty_list_award' => $data['list']));
        }
    }

    public function getActivityGift($id) {
        $db = $this->getEventDB();
        $sql = 'select count(actitivty_list_id) AS  actitivty_count  from alchemy_event_actitivty_list where actitivty_list_id = :actitivty_list_id ';
        $rdb = $db['r'];
        return $rdb->fetchOne($sql, array('actitivty_list_id' => $id));
    }

    public function getCdkeyType($times) {  //获取cdkey数据中 最高的类型
        $db = $this->getEventDB();
        $rdb = $db['r'];
        $sql = "select count(cdkey_type) as type from alchemy_event_actitivty_cdkey order by cdkey_type limit 1";
        return $rdb->fetchOne($sql);
    }

    public function insertActivityCdKeys($data) {
        $db = $this->getEventDB();
        $wdb = $db['w'];
        $cdkey_user_id = 0;
        $cdkey_cardNoArray = array();  //存放cdkey的数组
        if ($data['times'] > 0) {
            $type = $this->getCdkeyType($data['times']) + 1;
        } else {
            $type = 0;
        }
        if ($data['number'] > 0) {
            $ActivityGiftCount = $this->getActivityGift($data['id']);  //验证活动礼包是否存在
            if ($ActivityGiftCount > 0) {
                $html = '';
                for ($i = 0; $i < $data['number']; $i++) {
                    $time = time() + rand(1, 100) + $i;
                    $cdkey_cardNo = md5($data['id'] . $i . $time);
                    $sql = "INSERT INTO alchemy_event_actitivty_cdkey (cdkey_cardNo,cdkey_user_id,cdkey_actitivty_id,cdkey_limit,cdkey_times,cdkey_type) VALUES(:cdkey_cardNo, :cdkey_user_id,:cdkey_actitivty_id,:cdkey_limit,:cdkey_times,:cdkey_type)";
                    $wdb->query($sql, array('cdkey_cardNo' => $cdkey_cardNo, 'cdkey_user_id' => 0, 'cdkey_actitivty_id' => $data['id'], 'cdkey_limit' => 1, 'cdkey_times' => $data['times'], 'cdkey_type' => $type));
                    $html .= $cdkey_cardNo . '<br />';
                }
                $result['cdkey'] = $html;
                $result['resp'] = 0;
            } else {
                $result['resp'] = '活动礼包不存在';
            }
        } else {
            $result['resp'] = '生成数量小于1';
        }
        echo json_encode($result);
        die;
        //return json_encode($data);
    }

    public function deleteg($data) {
        $db = $this->getDB();
        $wdb = $db['w'];
        $sql = "DELETE FROM alchemy_time_gift WHERE id=:id AND `type`=:type";
        return $wdb->query($sql, array('id' => $data['id'], 'type' => $data['type']));
    }

    public function deleteactitivty($data) {
        $db = $this->getEventDB();
        $wdb = $db['w'];
        $sql = "DELETE FROM alchemy_event_actitivty_list $data actitivty_list_id=:id";
        return $wdb->query($sql, array('id' => $data['id']));
    }

    public function insertLgift($data) {
        $db = $this->getDB();
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_level_gift (level, nextLevel, `awards`, `type`) VALUES(:level, :nextLevel, :awards, :type) ON DUPLICATE KEY UPDATE nextLevel=:nextLevel,awards=:awards";
        return $wdb->query($sql, array('level' => $data['level'], 'nextLevel' => $data['nextLevel'], 'type' => $data['type'], 'awards' => $data['awards']));
    }

    public function deletel($data) {
        $db = $this->getDB();
        $wdb = $db['w'];
        $sql = "DELETE FROM alchemy_level_gift WHERE `level`=:level AND `type`=:type";
        return $wdb->query($sql, array('level' => $data['level'], 'type' => $data['type']));
    }

    public function deletes($data) {
        $db = $this->getDB();
        $wdb = $db['w'];
        $sql = "DELETE FROM alchemy_seven_gift WHERE day=:day AND `type`=:type";
        return $wdb->query($sql, array('day' => $data['day'], 'type' => $data['type']));
    }

    public function insertSgift($data) {
        $db = $this->getDB();
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_seven_gift (`day`, `awards`, `type`) VALUES(:day, :awards, :type) ON DUPLICATE KEY UPDATE awards=:awards";
        return $wdb->query($sql, array('day' => $data['day'], 'type' => $data['type'], 'awards' => $data['awards']));
    }

    public function getPackage() {
        $sql = "select `type`,cid,awards from alchemy_package";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAll($sql);
    }

    public function insertpackage($data) {
        $db = $this->getDB();
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_package (cid, `awards`, `type`) VALUES(:cid, :awards, :type) ON DUPLICATE KEY UPDATE type=:type,awards=:awards";
        return $wdb->query($sql, array('cid' => $data['cid'], 'type' => $data['type'], 'awards' => $data['awards']));
    }

    public function deletep($cid) {
        $db = $this->getDB();
        $wdb = $db['w'];
        $sql = "delete from alchemy_package where cid=:cid";
        return $wdb->query($sql, array('cid' => $cid));
    }

    public function getdaliyactivity() {
        $sql = "select `tid`, activity from alchemy_daliy_task_activity";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    public function insertDaliyTask($data) {
        $db = $this->getDB();
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_daliy_task_activity (tid, `activity`) VALUES(:tid, :activity) ON DUPLICATE KEY UPDATE activity=:activity";
        return $wdb->query($sql, array('tid' => $data['tid'], 'activity' => $data['activity']));
    }

    public function deleted($tid) {
        $db = $this->getDB();
        $wdb = $db['w'];
        $sql = "delete from alchemy_daliy_task_activity where tid=:tid";
        return $wdb->query($sql, array('tid' => $tid));
    }

    public function getactivityaward() {
        $sql = "select * from alchemy_activity_awards";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    public function deletea($id) {
        $db = $this->getDB();
        $wdb = $db['w'];
        $sql = "delete from alchemy_activity_awards where id=:id";
        return $wdb->query($sql, array('id' => $id));
    }

    public function insertActivityAwards($data) {
        $db = $this->getDB();
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_activity_awards (awards, `activity`) VALUES(:awards, :activity)";
        return $wdb->query($sql, array('activity' => $data['activity'], 'awards' => $data['awards']));
    }

    public function updateActivityAwards($data) {
        $db = $this->getDB();
        $wdb = $db['w'];
        $sql = "update alchemy_activity_awards set awards=:awards, activity=:activity where id=:id";
        return $wdb->query($sql, array('id' => $data['id'], 'activity' => $data['activity'], 'awards' => $data['awards']));
    }

    public function getTestGift($uid) {
        $db = $this->getDB();
        $sql = "select * from alchemy_test_gift where uid=:uid";
        $rdb = $db['r'];
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

    public function insertTestGift($data) {
        $db = $this->getDB();
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_test_gift (uid, test1, test2, test3, `finish`) VALUES(:uid, :test1, :test2, :test3, :finish) ON DUPLICATE KEY UPDATE test1=:test1,test2=:test2,test3=:test3,finish=:finish";
        return $wdb->query($sql, array('uid' => $data['uid'], 'test1' => $data['test1'], 'test2' => $data['test2'], 'test3' => $data['test3'], 'finish' => $data['finish']));
    }

    public function getInviteGift($uid) {
        $db = $this->getEventDB();
        $rdb = $db['r'];
        $sql = "select uid, `step` from alchemy_event_get where uid=:uid and `type`=1";
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

    public function insertInviteGift($data) {
        $db = $this->getEventDB();
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_event_get (uid, `step`, `type`) VALUES(:uid, :step, :type) ON DUPLICATE KEY UPDATE `step`=:step";
        return $wdb->query($sql, array('uid' => $data['uid'], 'step' => $data['step'], 'type' => $data['type']));
    }

    public function getFirstPay($uid) {
        $db = $this->getEventDB();
        $rdb = $db['r'];
        $sql = "select uid, `step` from alchemy_event_get where uid=:uid and `type`=2";
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

    public function getVipLevelIp($uid) {
        $db = $this->getEventDB();
        $rdb = $db['r'];
        $sql = "select uid, `step` from alchemy_event_get where uid=:uid and `type`=3";
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

    public function getVipPay($uid) {
        $db = $this->getEventDB();
        $rdb = $db['r'];
        $sql = "select uid, `step` from alchemy_event_get where uid=:uid and `type`=4";
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

    public function getInvite($uid) {
        $db = $this->getEventDB();
        $rdb = $db['r'];
        $sql = "select uid, `step` from alchemy_event_get where uid=:uid and `type`=5";
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

    public function getPayEvent($uid) {
        $db = $this->getEventDB();
        $rdb = $db['r'];
        $sql = "select uid, totalPay from pay_event where uid=:uid ";
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

    public function getEventPay($uid) {
        $db = $this->getEventDB();
        $rdb = $db['r'];
        $sql = "select uid, `step` from alchemy_event_get where uid=:uid and `type`=6";
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

    public function insertEventPay($data) {
        $db = $this->getEventDB();
        $wdb = $db['w'];
        $sql = "INSERT INTO pay_event (uid, totalPay) VALUES(:uid, :totalPay) ON DUPLICATE KEY UPDATE totalPay=:totalPay";
        return $wdb->query($sql, array('uid' => $data['uid'], 'totalPay' => $data['totalPay']));
    }

    public function updateDm($data) {
        $db = Hapyfish2_Db_Factory::getBasicDB('db_0');
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_dm (id,dm,btn) VALUES(:id,:dm,:btn) ON DUPLICATE KEY UPDATE dm=:dm,btn=:btn";
        return $wdb->query($sql, array('id' => $data['id'], 'dm' => $data['dm'], 'btn' => $data['btn']));
    }

    public function getDm() {
        $db = Hapyfish2_Db_Factory::getBasicDB('db_0');
        $rdb = $db['r'];
        $sql = "SELECT * from  alchemy_dm";
        return $rdb->fetchRow($sql);
    }

    public function getUserDemoedStatus($uid) {
        $sql = "SELECT award_count,award_time FROM alchemy_user_diamond WHERE uid=:uid";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

    public function getUserDemoedFeedStatus($uid) {
        $sql = "SELECT feed_result,feed_time FROM  alchemy_user_demond_feed WHERE uid=:uid";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

    public function getUserLoadingStatus($uid) {
        $sql = 'SELECT count(uid) AS loading_c from alchemy_user_loading WHERE uid=:uid';
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }
    public function deleteUserLoadingStatus($uid) {
        $sql = 'DELETE  FROM  alchemy_user_loading WHERE uid=:uid';
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        return $wdb->query($sql, array('uid' => $uid));
    }
    public function updateUserLoadingStatus($uid, $status) {
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_user_loading (uid,statsus) VALUES(:uid,:statsus) ON DUPLICATE KEY UPDATE statsus=:statsus";
        return $wdb->query($sql, array('uid' => $uid, 'statsus' => $status));
    }

    public function getLoadingGift($gift_type = 1) {
        $db = $this->getEventDB();
        $rdb = $db['r'];
        $sql = 'SELECT award FROM alchemy_loading_gift WHERE gift_type=:gift_type';
        return $rdb->fetchRow($sql, array('gift_type' => $gift_type));
    }
}
