<?php


class Hapyfish2_Alchemy_Dal_FloorWall
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_FloorWall
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
		if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE == 3) {
            $id = 0;
        } else {
        	$id = floor($uid/DATABASE_NODE_NUM) % 10;
        }
        
    	return 'alchemy_user_floorwall_' . $id;
    }

    public function get($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT floor,wall FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $row = $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
        if ($row != null) {
            for($i = 0, $len = count($row); $i < $len; $i++) {
       			$row[$i] = (int)$row[$i];
       		}
        }
        
        return $row;
    }
    
    public function insert($uid, $floor, $wall)
    {
    	$tbname = $this->getTableName($uid);

        $sql = "INSERT INTO $tbname(uid, floor, wall) VALUES(:uid, :floor, :wall)";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        return $wdb->query($sql, array('uid' => $uid, 'floor' => $floor, 'wall' => $wall));
    }

    public function update($uid, $floor, $wall)
    {
        $tbname = $this->getTableName($uid);

        $sql = "UPDATE $tbname SET floor=:floor, wall=:wall WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $wdb->query($sql, array('uid' => $uid, 'floor' => $floor, 'wall' => $wall));
    }

    public function clear($uid)
    {
    	$this->update($uid, 0, 0);
    }

}