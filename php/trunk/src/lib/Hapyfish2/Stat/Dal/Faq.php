<?php

class Hapyfish2_Stat_Dal_Faq
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

    public function insert($data)
    {
        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
    	return $wdb->insert('stat_faq', $data);
    }
    
    public function getFaq($where, $limit= 'limit 50')
    { 
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$wheres = 'where 1 ';
    	if($where){
    		foreach($where as $k=>$v){
    			$wheres .= ' and '.$k.'='.$v;
    		}
    	}
    	$sql = "select * from stat_faq  ".$wheres ." order by create_time DESC ".$limit;
    	return $rdb->fetchAll($sql);
    }
    
    public function getCount($start, $end, $type, $status)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$and = " and 1";
    	if($type != 3){
    		$and = "and `type`={$type}";
    	}
    	if($status <= 1){
    		$and = "and `status`={$status}";
    	}
    	$sql = "select count(id) from stat_faq where create_time>={$start} and create_time <={$end} ".$and;
    	return $rdb->fetchOne($sql);
    }
    
	public function getApiFaq($start, $end, $type, $status, $limit= 'limit 50',$id)
    { 
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$and = " and 1";
    	if($type != 3){
    		$and .= " and `type`={$type}";
    	}
    	if($status <= 1){
    		$and .= " and `status`={$status}";
    	}
    	if($id > 0){
    		$and .= " and `id`={$id}";
    	}
    	$sql = "select id,uid,`type`,`title`,`content`,`create_time`,`status`,`ip`,`diqu`,`xianlu`,`flash`,`browser`,`tag`,`answer`,`image_type` from stat_faq where create_time>={$start} and create_time <={$end} ".$and." order by create_time DESC ". $limit;
    	return $rdb->fetchAll($sql);
    }
    
    public function getEApiFaq($start, $end, $type, $status)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$and = " and 1";
    	if($type != 3){
    		$and .= " and `type`={$type}";
    	}
    	if($status <= 1){
    		$and .= " and `status`={$status}";
    	}
    	$sql = "select * from stat_faq where create_time>={$start} and create_time <={$end} ".$and." order by create_time DESC ";
    	return $rdb->fetchAll($sql);
    }
    
    public function getimage($id)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select `image`,`image_type` from stat_faq where id={$id}";
    	return $rdb->fetchRow($sql);
    }
    
    public  function updateFaq($id, $data)
    {
    	$tbname = 'stat_faq';
        $db = Hapyfish2_Db_FactoryStat::getStatLogDB();
        $wdb = $db['w'];
    	$where = $wdb->quoteinto('id = ?', $id);
    	
        $wdb->update($tbname, $data, $where);
    }
    
    public function updatetag()
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$wdb = $db['w'];
    	$sql = "update stat_faq set `tag` = 0";
    	 $wdb->query($sql);
    }
    
    public function getdetail($id)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$rdb = $db['r'];
    	$sql = "select id, `title`,`content`,`answer`,`create_time` from stat_faq where id={$id}";
    	return $rdb->fetchRow($sql);
    }
    
    public function detelefaq($ids)
    {
    	$db = Hapyfish2_Db_FactoryStat::getStatLogDB();
    	$wdb = $db['w'];
    	$sql = "delete from stat_faq where id in ({$ids})";
    	$wdb->query($sql);
    }
}