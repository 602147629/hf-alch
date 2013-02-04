<?php

class Hapyfish2_Platform_Dal_Entry_Pengyou_User
{
    protected static $_instance;

    /**
     *
     *
     * @return Hapyfish2_Platform_Dal_Entry_Pengyou_User
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
    
    public function getYellowInfo($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "SELECT is_yellow_vip,is_yellow_year_vip,yellow_vip_level FROM $tbname WHERE uid=:uid";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchRow($sql, array(
            'uid' => $uid
        ), Zend_Db::FETCH_NUM);
    }
}