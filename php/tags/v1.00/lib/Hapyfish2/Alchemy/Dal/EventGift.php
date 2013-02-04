<?php


class Hapyfish2_Alchemy_Dal_EventGift
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_Fight
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
    	return 'alchemy_user_fight_' . $id;
    }

    public function getTimeGift()
    {
    	$sql = "select `type`,id,`time`,next_id,list from alchemy_time_gift order by id";
    	$db = $this->getDB();
    	$rdb = $db['r'];
        return $rdb->fetchAll($sql);
    }
    
    public function getUserEvent($uid, $type)
    {
    	$sql = "select id from alchemy_user_event_gift where uid=:uid and type=:type";
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$rdb = $db['r'];
    	return $rdb->fetchOne($sql, array('uid'=>$uid,'type'=>$type));
    }
    
    public function getSevenGift()
    {
    	$sql = "select `type`,`day`,awards from alchemy_seven_gift order by `day`";
    	$db = $this->getDB();
    	$rdb = $db['r'];
        return $rdb->fetchAll($sql);
    }
    
    public function getLevelGift()
    {
    	$sql = "select `type`,level,nextLevel,awards from alchemy_level_gift order by `level`";
    	$db = $this->getDB();
    	$rdb = $db['r'];
        return $rdb->fetchAll($sql);
    }
    
    public function updateEventGift($uid,$id,$type)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$wdb = $db['w'];
    	$sql = "INSERT INTO alchemy_user_event_gift (uid, id, `type`) VALUES(:uid, :id, :type) ON DUPLICATE KEY UPDATE id=:id";
    	return $wdb->query($sql, array('uid'=>$uid, 'id'=>$id, 'type'=>$type));
    }
    
    public function getMinLevel()
    {
    	$sql = "select min(level) from alchemy_level_gift";
    	$db = $this->getDB();
    	$rdb = $db['r'];
        return $rdb->fetchOne($sql);
    }
    
    public function clear($uid)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$wdb = $db['w'];
    	$sql = "delete from alchemy_user_event_gift where uid=:uid";
    	return $wdb->query($sql, array('uid'=>$uid));
    }
    
    public function insertTgift($data)
    {
    	$db = $this->getDB();
    	$wdb = $db['w'];
    	$sql = "INSERT INTO alchemy_time_gift (id, next_id, `time`, list, `type`) VALUES(:id, :next_id, :time, :list, :type) ON DUPLICATE KEY UPDATE next_id=:next_id,time=:time,list=:list";
    	return $wdb->query($sql, array('id'=>$data['id'], 'next_id'=>$data['next_id'], 'type'=>$data['type'],'list'=>$data['list'],'time'=>$data['time']));
    }
    
    public function deleteg($data)
    {
    	$db = $this->getDB();
    	$wdb = $db['w'];
    	$sql = "delete from alchemy_time_gift where id=:id and `type`=:type";
    	return $wdb->query($sql, array('id'=>$data['id'],'type'=>$data['type']));
    }
    
    public function insertLgift($data)
    {
    	$db = $this->getDB();
    	$wdb = $db['w'];
    	$sql = "INSERT INTO alchemy_level_gift (level, nextLevel, `awards`, `type`) VALUES(:level, :nextLevel, :awards, :type) ON DUPLICATE KEY UPDATE nextLevel=:nextLevel,awards=:awards";
    	return $wdb->query($sql, array('level'=>$data['level'], 'nextLevel'=>$data['nextLevel'], 'type'=>$data['type'],'awards'=>$data['awards']));
    }
    
	public function deletel($data)
    {
    	$db = $this->getDB();
    	$wdb = $db['w'];
    	$sql = "delete from alchemy_level_gift where level=:level and `type`=:type";
    	return $wdb->query($sql, array('level'=>$data['level'],'type'=>$data['type']));
    }
    
    public function deletes($data)
    {
    	$db = $this->getDB();
    	$wdb = $db['w'];
    	$sql = "delete from alchemy_seven_gift where day=:day and `type`=:type";
    	return $wdb->query($sql, array('day'=>$data['day'],'type'=>$data['type']));	
    }
    
    public function insertSgift($data)
    {
    	$db = $this->getDB();
    	$wdb = $db['w'];
    	$sql = "INSERT INTO alchemy_seven_gift (`day`, `awards`, `type`) VALUES(:day, :awards, :type) ON DUPLICATE KEY UPDATE awards=:awards";
    	return $wdb->query($sql, array('day'=>$data['day'],'type'=>$data['type'],'awards'=>$data['awards']));
    }
}