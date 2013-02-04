<?php

class Hapyfish2_Platform_Dal_Entry_SinaWeibo_User
{
    protected static $_instance;

    /**
     *
     *
     * @return Hapyfish2_Platform_Dal_Entry_SinaWeibo_User
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
        $id = floor($uid / DATABASE_NODE_NUM) % 10;
        if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE == 3) {
            $id = floor($uid / DATABASE_NODE_NUM) % 1;
        }
        return 'platform_user_info_' . $id;
    }

    /**
     * get inner uid
     *
     * @param string $puid
     * @return integer
     */
    public function update($uid, $info)
    {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $where = $wdb->quoteinto('uid = ?', $uid);
        return $wdb->update($tbname, $info, $where);
    }

    public function getVerified($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "SELECT verified FROM $tbname WHERE uid=:uid";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchOne($sql, array(
            'uid' => $uid
        ));
    }

    public function getIdentity($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "SELECT identity FROM $tbname WHERE uid=:uid";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchOne($sql, array(
            'uid' => $uid
        ));
    }
}