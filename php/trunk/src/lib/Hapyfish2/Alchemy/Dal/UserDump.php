<?php

class Hapyfish2_Alchemy_Dal_UserDump
{
    protected static $_instance;

    protected function getDB()
    {
        $key = 'db_0';
        return Hapyfish2_Db_Factory::getBasicDB($key);
    }

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_UserDump
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getTableName()
    {
        return 'user_dump';
    }
    
    public function get()
    {
        $tbname = $this->getTableName();
        $sql = "SELECT * FROM $tbname ORDER BY file_name ";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAll($sql);
    }

    public function insert($info)
    {
        $tbname = $this->getTableName();
        $db = $this->getDB();
        $wdb = $db['w'];
        $ret = $wdb->insert($tbname, $info);
        return $ret;
    }
    
    public function del($id)
    {
        $tbname = $this->getTableName();
        $sql = "DELETE FROM $tbname WHERE id=:id";
        $db = $this->getDB();
        $wdb = $db['w'];
        return $wdb->query($sql, array('id' => $id));
    }
    
    public function getOne($id)
    {
        $tbname = $this->getTableName();
        $sql = "SELECT * FROM $tbname WHERE id=:id";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchRow($sql, array('id' => $id));
    }

    public function update($uid, $info)
    {
        $tbname = $this->getTableName();

        $db = $this->getDB();
        $wdb = $db['w'];

        $where = $wdb->quoteinto('id = ?', $info['id']);

        $rowCount = $wdb->update($tbname, $info, $where);
        if ($rowCount == 0) {
            return false;
        } else {
            return true;
        }
    }
}