<?php

class UnittestController extends Hapyfish2_Controller_Action_External
{

	public function inituserAction()
	{
		$puid = $this->_request->getParam('puid');
		$uidInfo = Hapyfish2_Platform_Cache_UidMap::getUser($puid);
		if (!$uidInfo) {
    		$uidInfo = Hapyfish2_Platform_Cache_UidMap::newUser($puid);
    		if (!$uidInfo) {
    			echo 'inituser error: 1';
    			exit;
    		}
		}
		$uid = $uidInfo['uid'];
        $user = array();
        $user['uid'] = $uid;
        $user['puid'] = $puid;
        $user['name'] = '测试' . $puid;
        $user['figureurl'] = 'http://hdn.xnimg.cn/photos/hdn521/20091210/1355/tiny_E7Io_11729b019116.jpg';
        $user['gender'] = rand(0,1);

		Hapyfish2_Platform_Bll_User::addUser($user);

		//Hapyfish2_Magic_Bll_User::joinUser($uid);

		echo 'OK: ' . $uid;
		exit;
	}

	public function initfightuserAction()
	{
	    $uid = $this->_request->getParam('uid');
        $dal1 = Hapyfish2_Alchemy_Dal_FightAttribute::getDefaultInstance();
        $dal1->init($uid, 1);// 1,  101 ,  201

        $dal2 = Hapyfish2_Alchemy_Dal_FightMercenary::getDefaultInstance();
        $dal2->init($uid, '战士Adam1');

        $aryMatrix = array('9'=>'11', '11'=>'0');
        $rst = Hapyfish2_Alchemy_Bll_FightCorps::arrangeCorps($uid, $aryMatrix);

        echo $uid.' init:'.$rst;
        exit;
	}

    public function regfightAction()
	{
	    $uid = $this->_request->getParam('uid');
        /*$list1 = Hapyfish2_Alchemy_Bll_FightCorps::getHomeSideUnitList($uid);
        $list2 = Hapyfish2_Alchemy_Bll_MapCopy::getEnemySideUnitList($uid, 1);
        echo json_encode($list1);
        echo '<br />';
        echo json_encode($list2);
        exit;*/

	    Hapyfish2_Alchemy_HFC_FightAttribute::loadInfo($uid);
	    Hapyfish2_Alchemy_HFC_FightMercenary::reloadAll($uid);
	    Hapyfish2_Alchemy_Cache_FightCorps::loadFightCorpsInfo($uid);
        $rst = Hapyfish2_Alchemy_Bll_Fight::regFight($uid, 1);
        echo json_encode($rst);
        exit;
	}

    public function getcurfightAction()
	{
	    $uid = $this->_request->getParam('uid');
        $rst = Hapyfish2_Alchemy_Bll_Fight::getCurFightInfo($uid);

        //$dal = Hapyfish2_Alchemy_Dal_Fight::getDefaultInstance();
        //$rst = $dal->getOne($uid, $fid);
        echo json_encode($rst);
        exit;
	}

    public function comfightAction()
	{
	    $uid = $this->_request->getParam('uid');
	    $fid = $this->_request->getParam('fid');
	    //测试数据
        $aryAct = array();
        $aryAct[] = array('-', 10, 6, 2, 222, 5);
        $aryAct[] = array('-', 7, 10, 1, 0, 5);
        $aryAct[] = array('-', 8, 10, 1, 0, 5);
        $aryAct[] = array('-', 6, 10, 1, 0, 5);
        $aryAct[] = array('-', 9, 8, 1, 0, 5);

        $aryAct[] = array('-', 10, 7, 1, 0, 5);
        $aryAct[] = array('-', 7, 10, 1, 0, 5);
        $aryAct[] = array('-', 8, 10, 1, 0, 5);
        $aryAct[] = array('-', 6, 10, 1, 0, 5);
        $aryAct[] = array('-', 9, 8, 1, 0, 5);


        $act = '[["-",10,7,2,222,0],["-",7,9,1,2,0],["-",8,10,1,1,0],["-",6,9,1,1,0],["-",9,7,1,1,0],["-",10,6,1,1,0],["-",7,10,1,2,0],["-",8,10,1,1,0],["-",6,10,1,1,0],["x",9],["-",10,7,2,222,0],["-",7,10,1,2,0],["-",8,10,1,1,0],["-",9,8,1,1,0],["x",10],["-",8,10,1,1,0],["-",9,8,1,1,0]]';
        $aryAct = json_decode($act, true);



        /*$aryAct[] = array('-', 10, 7, 1, 0, 5);
        $aryAct[] = array('-', 7, 10, 1, 0, 5);
        $aryAct[] = array('-', 8, 10, 1, 0, 5);
        $aryAct[] = array('-', 6, 10, 1, 0, 5);
        $aryAct[] = array('-', 9, 8, 1, 0, 5);

        $aryAct[] = array('-', 10, 7, 1, 0, 5);
        $aryAct[] = array('-', 7, 10, 1, 0, 5);
        $aryAct[] = array('-', 8, 10, 1, 0, 5);
        $aryAct[] = array('-', 6, 10, 1, 0, 5);
        $aryAct[] = array('-', 9, 8, 1, 0, 5);*/




        $rst = Hapyfish2_Alchemy_Bll_Fight::completeFight($uid, $fid, $aryAct, 1);

        //$dal = Hapyfish2_Alchemy_Dal_Fight::getDefaultInstance();
        //$rst = $dal->getOne($uid, $fid);
        echo json_encode($rst);
        exit;
	}

	public function clearbascacheAction()
	{
	    $list = Hapyfish2_Alchemy_Cache_MemkeyList::mapBasicMcKey();
	    $localcache = Hapyfish2_Cache_LocalCache::getInstance();
	    $cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
	    foreach ($list as $key) {
	        $cache->delete($key);
	        $localcache->delete($key);
	        echo $key;
	        echo '<br/>';
	    }

	    exit;
	}

	public function testvalAction()
	{
	    $aList = array('1'=>array(111), '2'=>array(22), '3'=>array('cc'));
	    echo 'alist:';
	    print_r($aList);
	    echo '<br />';

	    $bList = $aList;
	    //$bList['2'] = array('bb');

	    $bList = null;
	    echo 'alist:';
	    print_r($aList);
	    echo '<br />';

	    echo 'blist:';
	    print_r($bList);
	    echo '<br />';

	    $a = array(-1 => 'aa', -2 => 'bb',  -3 => 'ccc');
	    echo json_encode($a);

        exit;
	}
}