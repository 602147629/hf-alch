<?php


class Hapyfish2_Alchemy_Dal_SpecialShop
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_Help
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    protected function getDB()
    {
    	$key = 'db_0';
    	return Hapyfish2_Db_Factory::getBasicDB($key);
    }
    
    public function getTableName($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
        if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE==3) {
            $id = floor($uid/DATABASE_NODE_NUM) % 1;
        }
    	return 'alchemy_user_helltower_' . $id;
    }

    public function getSpecialShop()
    {
       	$sql = "SELECT `id`,`type`,`can_buy`,`buyLimit`,`total`,`buyMore` FROM alchemy_special_shop";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }
    
    public function getSpecialShopItem()
    {
    	$sql = "SELECT `id`,`level`,`awards`,`price`,`newPrice` FROM alchemy_special_shop_item";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAll($sql);
    }
    
    public function insert($data)
    {
    	$tbname = $this->getTableName($data['uid']);
    	$db = Hapyfish2_Db_Factory::getDB($data['uid']);
        $wdb = $db['w'];
        $sql = "INSERT INTO $tbname (uid, `type`, `refreshTime`, `current`, `max`, `totalexp`, totalcoin,`open`) VALUES(:uid, :type, :refreshTime, :current, :max, :totalexp, :totalcoin,:open) ON DUPLICATE KEY UPDATE refreshTime=:refreshTime, current=:current, `max`=:max, totalexp=:totalexp, totalcoin=:totalcoin,open:open";
        return $wdb->query($sql, array('uid' => $data['uid'], 'type' => $data['type'], 'refreshTime' => $data['refreshTime'], 'current'=>$data['current'], 'max'=>$data['max'],'totalexp'=>$data['totalexp'],'totalcoin'=>$data['totalcoin'],'open'=>$data['open']));
    }
    
}