<?php

/**
 * seq payorder for sina
 *
 *
 * @package    Dal
 * @create      2011/07/01    zx
 */
class Hapyfish2_Platform_Dal_QqpayByToken
{

    protected static $_instance;

    /**
     *
     *
     * @return Hapyfish2_Platform_Dal_UidMap
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getTokenTable($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
    	return 'alchemy_user_qq_token_' . $id;
    }
    
    public function getPayDoneTable($uid)
    {
    	$id = floor($uid/DATABASE_NODE_NUM) % 10;
    	return 'alchemy_user_qq_payDone_' . $id;
    }

    public function insertToken($uid,$data)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $tb = $this->getTokenTable($uid);
        $sql = "INSERT INTO $tb (uid, token, `payitem`, pl) VALUES(:uid, :token, :payitem, pl) ON DUPLICATE KEY UPDATE payitem=:payitem, pl=:pl";
    	return $wdb->query($sql, array('uid'=>$uid, 'token'=>$id, 'payitem'=>$data['payitem'], 'pl'=>$data['pl']));
    }
    
    public function getToken($uid,$token)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $tb = $this->getTokenTable($uid);
        $sql = "select * from $tb where uid=:uid and token=:token";
        return $rdb->fetchRow($sql, array('uid' => $uid, 'token'=>$token));
    }
    
    public function insertPayDone($uid,$data)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $tb = $this->getPayDoneTable($uid);
        $sql = "INSERT INTO $tb (uid, billno,`payItme`,totalPay) VALUES(:uid, :billno, :payItme, :totalPay) ON DUPLICATE KEY UPDATE payItme=:payItme,totalPay=:totalPay";
    	return $wdb->query($sql, array('uid'=>$uid, 'billno'=>$data['billno'], 'payItme'=>$data['payItme'], 'totalPay'=>$data['totalPay']));
    }
    
    public function getPayDone($uid,$billno)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $tb = $this->getPayDoneTable($uid);
        $sql = "select * from $tb where uid=:uid and billno=:billno";
        return $rdb->fetchRow($sql, array('uid' => $uid, 'billno'=>$billno));
    }
}