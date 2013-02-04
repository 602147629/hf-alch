<?php


class Hapyfish2_Alchemy_Event_Dal_Arena
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

    public function getTableName()
    {
    	return 'alchemy_arena_rank';
    }

    public function insUpd($uid, $info)
    {
        $tbname = $this->getTableName();
        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
    	$wdb = $db['w'];
    
    	$sql = "INSERT INTO $tbname (uid, `score`)
    	VALUES (:uid, :score)
    	ON DUPLICATE KEY UPDATE `score`=:score";
    
    	return $wdb->query($sql, array('uid'=>$uid, 'score'=>$info['score']));
    }
    
    public function getAll()
    {
        $tbname = $this->getTableName();
    	$sql = "SELECT uid,score FROM $tbname ";

        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        return $rdb->fetchAll($sql);
    }

    public function getUserScore($uid)
    {
    	$tbname = $this->getTableName();
    	$sql = "SELECT score FROM $tbname WHERE uid=:uid ";
    
    	$db = Hapyfish2_Db_Factory::getEventDB('db_0');
    	$rdb = $db['r'];
    	return $rdb->fetchOne($sql, array('uid'=>$uid));
    }
    
    public function getUserRank($uid, $score)
    {
    	$tbname = $this->getTableName();
    	$sql = "SELECT COUNT(*) FROM $tbname WHERE score>:score OR (score=:score AND uid<:uid) ";
    
    	$db = Hapyfish2_Db_Factory::getEventDB('db_0');
    	$rdb = $db['r'];
    	return $rdb->fetchOne($sql, array('score'=>$score, 'uid'=>$uid));
    }
    
    public function getUidsByScore($score, $pageSize = 100)
    {
    	$tbname = $this->getTableName();
    	$sql = "SELECT uid,score FROM $tbname WHERE score>:score ORDER BY score ASC,uid ASC LIMIT 0,$pageSize ";
    
    	$db = Hapyfish2_Db_Factory::getEventDB('db_0');
    	$rdb = $db['r'];
    	$r = $rdb->fetchAll($sql, array('score'=>$score));
    	return $r;
    }

    public function getUidsTop($pageSize)
    {
    	$tbname = $this->getTableName();
    	$sql = "SELECT uid,score FROM $tbname ORDER BY score DESC,uid ASC LIMIT 0,$pageSize ";
    
    	$db = Hapyfish2_Db_Factory::getEventDB('db_0');
    	$rdb = $db['r'];
    	return $rdb->fetchAll($sql);
    }
    
    public function update($uid, $info)
    {
    	$tbname = $this->getTableName();

    	$db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $wdb = $db['w'];

    	$where = $wdb->quoteinto('uid = ?', $uid);

        $rowCount = $wdb->update($tbname, $info, $where);
        if ($rowCount == 0) {
        	return false;
        } else {
        	return true;
        }
    }
    
    /*public function getRankList($pageSize)
    {
        $tbname = $this->getTableName();
    	$sql = "SELECT uid,score FROM $tbname ";

        $db = Hapyfish2_Db_Factory::getEventDB('db_0');
        $rdb = $db['r'];
        return $rdb->fetchAll($sql);
    }*/
    

}