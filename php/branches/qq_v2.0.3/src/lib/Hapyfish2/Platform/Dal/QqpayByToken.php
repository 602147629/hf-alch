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
        $sql = "INSERT INTO $tb (uid, `token`, `payitem`, `pl`) VALUES(:uid, :token, :payitem, :pl) ON DUPLICATE KEY UPDATE payitem=:payitem, pl=:pl";
    	return $wdb->query($sql, array('uid'=>$uid, 'token'=>$data['token'], 'payitem'=>$data['payitem'], 'pl'=>$data['pl']));
    }
    
    public function getToken($uid,$token)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        $tb = $this->getTokenTable($uid);
        $sql = "select * from $tb where uid=:uid and token=:token";
        return $rdb->fetchRow($sql, array('uid' => $uid, 'token'=>$token));
    }
    
    public function insertPayDone($uid,$data)
    {
    	$tm = time();
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $tb = $this->getPayDoneTable($uid);
        $sql = "INSERT INTO $tb (uid, billno,`payitem`,token_id,`amt`,`payamt_coins`,`pubacct_payamt_coins`,`create_time`) 
        		VALUES(:uid, :billno, :payitem, :token_id,:amt,:payamt_coins,:pubacct_payamt_coins, :tm) 
        		ON DUPLICATE KEY UPDATE payitem=:payitem,token_id=:token_id,amt=:amt,payamt_coins=:payamt_coins,pubacct_payamt_coins=:pubacct_payamt_coins,create_time=:tm";
    	return $wdb->query($sql, array('uid'=>$uid, 'billno'=>$data['billno'], 'payitem'=>$data['payitem'], 'token_id'=>$data['token_id'],'amt'=>$data['amt'],'payamt_coins'=>$data['payamt_coins'],'pubacct_payamt_coins'=>$data['pubacct_payamt_coins'], 'tm' => $tm));
    }
    
    public function getPayDone($uid,$billno)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        $tb = $this->getPayDoneTable($uid);
        $sql = "select * from $tb where uid=:uid and billno=:billno";
        return $rdb->fetchRow($sql, array('uid' => $uid, 'billno'=>$billno));
    }
    
    public function getPayDoneInfo($uid,$token)
    {
    	$db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];
        $tb = $this->getPayDoneTable($uid);
        $sql = "select * from $tb where uid=:uid and token_id=:token";
        return $rdb->fetchRow($sql, array('uid' => $uid, 'token'=>$token));
    }
}