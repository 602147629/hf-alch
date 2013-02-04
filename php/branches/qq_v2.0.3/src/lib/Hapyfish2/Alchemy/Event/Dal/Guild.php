<?php

class Hapyfish2_Alchemy_Event_Dal_Guild {

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
        $sql = "select uid,`detail`,`total` from alchemy_event_guild order by total DESC";
        return $rdb->fetchAssoc($sql);
    }

    public function insertGuild($uid, $data) {
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        $sql = "INSERT INTO alchemy_event_guild (uid, `detail`, `total`) VALUES(:uid, :detail, :total) ON DUPLICATE KEY UPDATE `detail`=:detail,total=:total";
        return $wdb->query($sql, array('uid' => $data['uid'], 'detail' => $data['detail'], 'total' => $data['total']));
    }

    public function delete($uid) {
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];
        $sql = "delete from alchemy_event_guild where uid=:uid";
        return $wdb->query($sql, array('uid' => $uid));
    }

    public function getone($uid) {
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        $sql = "select uid,`detail`,`total` from alchemy_event_guild where uid=:uid";
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }

}