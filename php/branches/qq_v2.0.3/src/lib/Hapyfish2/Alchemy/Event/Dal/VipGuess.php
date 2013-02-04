<?php
class Hapyfish2_Alchemy_Event_Dal_VipGuess
{
 	protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Event_Dal_Arena
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getUserGuess($uid)
    {
    	$db = Hapyfish2_Db_Factory::getEventDB('db_0');
    	$rdb = $db['r'];
    	$sql = "select uid,`price`,`get` from vip_guess where uid={$uid}";
    	return $rdb->fetchRow($sql);
    }
    
    public function update($uid,$data)
    {
    	$db = Hapyfish2_Db_Factory::getEventDB('db_0');
    	$wdb = $db['w'];
  		$sql = "INSERT INTO vip_guess (uid, `price`, `get`) VALUES(:uid, :price, :get) ON DUPLICATE KEY UPDATE `price`=:price,`get`=:get";
    	return $wdb->query($sql, array('uid'=>$data['uid'], 'price'=>$data['price'], 'get'=>$data['get']));
    }
}