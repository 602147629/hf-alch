<?php


class Hapyfish2_Alchemy_Dal_Story
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_Story
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
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
        if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE==3) {
            $id = floor($uid/DATABASE_NODE_NUM) % 1;
        }
    	return 'alchemy_user_story_' . $id;
    }

    public function getDialogTableName($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
        if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE==3) {
            $id = floor($uid/DATABASE_NODE_NUM) % 1;
        }
    	return 'alchemy_user_story_dialog_' . $id;
    }
    
    public function get($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT list FROM $tbname WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }
    
    public function insert($uid, $info)
    {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $ret = $wdb->insert($tbname, $info);
        return $ret;
    }
    
    public function update($uid, $info)
    {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	$uid = $wdb->quote($uid);
    	$where = "uid=$uid";
        return $wdb->update($tbname, $info, $where);
    }

    public function getDialog($uid)
    {
        $tbname = $this->getDialogTableName($uid);
    	$sql = "SELECT list FROM $tbname WHERE uid=:uid";
    	
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
    	
        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function insertDialog($uid, $info)
    {
        $tbname = $this->getDialogTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $ret = $wdb->insert($tbname, $info);
        return $ret;
    }
    
    public function updateDialog($uid, $info)
    {
        $tbname = $this->getDialogTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	$uid = $wdb->quote($uid);
    	$where = "uid=$uid";
        return $wdb->update($tbname, $info, $where);
    }

    public function initStory($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "INSERT INTO $tbname(uid, `list`) VALUES(:uid, '')";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $wdb->query($sql, array('uid' => $uid));
    }

    public function initDialog($uid)
    {
        $tbname = $this->getDialogTableName($uid);
        //$temp = '{"2":{"122":1,"322":1,"422":1,"522":1}}';
        $temp = array('2' => array('122'=>1,'322'=>1,'422'=>1,'522'=>1),
        			  '201' => array('2422'=>1,'2522'=>1));
        $temp = json_encode($temp);
        
        $sql = "INSERT INTO $tbname(uid, `list`) VALUES(:uid, '$temp')";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $wdb->query($sql, array('uid' => $uid));
    }
    
    public function getAll($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "SELECT list FROM $tbname WHERE uid=:uid";
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        
        return $rdb->fetchRow($sql, array('uid' => $uid));
    }
    
}