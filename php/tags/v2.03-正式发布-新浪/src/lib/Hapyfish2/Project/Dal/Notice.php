<?php

class Hapyfish2_Project_Dal_Notice
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
     * @return Hapyfish2_Project_Dal_Notice
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
		return 'alchemy_notice';
	}
   
    /**
     * 公告信息
     */
    public function getNoticeList()
    {
    	$tbname = $this->getTableName();
		$sql = "SELECT id,title,position,priority,link,opened,create_time FROM $tbname WHERE opened=1 ORDER BY position ASC,create_time DESC,priority ASC";
    	
        $db = $this->getDB();
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql);
    }
    
    /**
     * 更新公告
     *
     * @param int $id
     * @param Array $info
     */
    public function updateNotice($id, $info)
    {
        $tbname = $this->getTableName();
        
        $db = $this->getDB();
        $wdb = $db['w'];
    	$where = $wdb->quoteinto('id = ?', $id);
    	
        $wdb->update($tbname, $info, $where);
    }
    
    public function addNotice($info)
    {
		$tbname = $this->getTableName();

        $db = $this->getDB();
        $wdb = $db['w'];
        
    	$wdb->insert($tbname, $info);
        return $wdb->lastInsertId();
    }
    
}