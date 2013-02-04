<?php


class Hapyfish2_Stat_Dal_Qzonepay
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Stat_Dal_Qzonepay
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getPaydoneTableName($id)
    {
    	return 'alchemy_user_qq_payDone_' . $id;
    }
    
    public function getAll($dbId, $tbId)
    {
    	$tbname = $this->getPaydoneTableName($tbId);
    	$sql = "SELECT * FROM $tbname ";

        $db = Hapyfish2_Db_FactoryTool::getDB($dbId);
        $rdb = $db['r'];
        
        return $rdb->fetchAll($sql);
    }
	
    
}