<?php


class Hapyfish2_Alchemy_Dal_Helltower
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

    public function getTableName($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
        if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE==3) {
            $id = floor($uid/DATABASE_NODE_NUM) % 1;
        }
    	return 'alchemy_user_helltower_' . $id;
    }

    public function get($uid, $type)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT uid,`type`,`refreshTime`,`current`, `max`, totalexp, totalcoin,`open` FROM $tbname WHERE uid=:uid and type=:type";
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        return $rdb->fetchRow($sql, array('uid' => $uid, 'type'=>$type));
    }
    
    public function insert($data)
    {
    	$tbname = $this->getTableName($data['uid']);
    	$db = Hapyfish2_Db_Factory::getDB($data['uid']);
        $wdb = $db['w'];
        $sql = "INSERT INTO $tbname (uid, `type`, `refreshTime`, `current`, `max`, `totalexp`, totalcoin,`open`) VALUES(:uid, :type, :refreshTime, :current, :max, :totalexp, :totalcoin,:open) ON DUPLICATE KEY UPDATE refreshTime=:refreshTime, current=:current, `max`=:max, totalexp=:totalexp, totalcoin=:totalcoin,`open`=:open";
        return $wdb->query($sql, array('uid' => $data['uid'], 'type' => $data['type'], 'refreshTime' => $data['refreshTime'], 'current'=>$data['current'], 'max'=>$data['max'],'totalexp'=>$data['totalexp'],'totalcoin'=>$data['totalcoin'],'open'=>$data['open']));
    }
    
}