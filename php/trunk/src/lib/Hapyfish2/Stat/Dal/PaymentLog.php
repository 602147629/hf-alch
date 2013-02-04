<?php


class Hapyfish2_Stat_Dal_PaymentLog
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Stat_Dal_PaymentLog
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getPaymentLogTableName($id)
    {
    	return 'alchemy_user_paylog_' . $id;
    }
    
    public function getPaymentLogData($dbId, $tbId, $begin, $end)
    {
    	$tbname = $this->getPaymentLogTableName($tbId);
    	$sql = "SELECT uid,amount,gold,user_level,create_time,role_level,is_first_pay FROM $tbname WHERE create_time>=:begin AND create_time<:end";

        $db = Hapyfish2_Db_FactoryTool::getDB($dbId);
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql, array('begin' => $begin, 'end' => $end));
    }

    public function getGoldTableName($tbId)
    {
    	return 'alchemy_user_log_consume_gem_' . $tbId;
    }
    
    public function getGold($dbId, $tbId, $begin, $end)
    {
    	$tbname = $this->getGoldTableName($tbId);
    	$sql = "SELECT SUM(cost) AS allcost FROM $tbname WHERE create_time>=:begin AND create_time<:end AND uid NOT IN (10010,10037,10030,10014,10020,10035,10023,10012,10040,10047,10027,10024,10050,52644) ";
        $db = Hapyfish2_Db_FactoryTool::getDB($dbId);
        $rdb = $db['r'];
    	
        return $rdb->fetchOne($sql, array('begin' => $begin, 'end' => $end));
    }
    
    /**
     * 查询 该用户今天之前的充值次数
     * 
     * @param int $dbId
     * @param int $tbId
     * @param int $begin
     * @param int $end
     */
    public function getPayCountBeforeToday($dbId, $tbId, $uid, $todayTm)
    {
    	$tbname = $this->getPaymentLogTableName($tbId);
    	$sql = "SELECT COUNT(1) FROM $tbname WHERE create_time<:todayTm AND uid=:uid ";

        $db = Hapyfish2_Db_FactoryTool::getDB($dbId);
        $rdb = $db['r'];
    	
        return $rdb->fetchOne($sql, array('uid' => $uid, 'todayTm' => $todayTm));
    }
    
    public function getAllPayUidList($dbId, $tbId)
    {
    	$tbname = $this->getPaymentLogTableName($tbId);
    	$sql = "SELECT uid FROM $tbname GROUP BY uid ";

        $db = Hapyfish2_Db_FactoryTool::getDB($dbId);
        $rdb = $db['r'];
    	
        return $rdb->fetchAll($sql);
    }
    
}