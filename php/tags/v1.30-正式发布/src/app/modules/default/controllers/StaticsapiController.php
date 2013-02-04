<?php

class StaticsapiController extends Zend_Controller_Action
{
	function vaild()
	{

	}

    protected function echoResult($data)
    {
    	$data['errno'] = 0;
    	echo json_encode($data);
    	exit;
    }

    protected function echoError($errno, $errmsg)
    {
    	$result = array('errno' => $errno, 'errmsg' => $errmsg);
    	echo json_encode($result);
    	exit;
    }

    public function noopAction()
    {
    	$data = array('id' => SERVER_ID, 'time' => time(), 'method' => 'noop');
    	$this->echoResult($data);
    }


	//客户端加载时间day
    public function cloadtmdayAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}

		$log = Hapyfish2_Stat_Bll_CloadTm::getDay($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}

	//客户端加载时间day range
    public function cloadtmAction()
	{
		$day1 = $this->_request->getParam('dtBegin');
		$day2 = $this->_request->getParam('dtEnd');
		if (empty($day1)) {
			$day1 = date("Ymd", strtotime("-2 day"));
		}
	    if (empty($day2)) {
			$day2 = date("Ymd", strtotime("-1 day"));
		}

		$log = Hapyfish2_Stat_Bll_CloadTm::listData($day1, $day2);
		$data = array('data' => $log);
		$this->echoResult($data);
	}

	public function mainAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		
		$log = Hapyfish2_Stat_Bll_Day::getMain($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}
	
	public function retentionAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		
		$log = Hapyfish2_Stat_Bll_Day::getRetention($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}
	
	public function paymentofcalAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Y-m-d", strtotime("-1 day"));
		}
		
		$log = Hapyfish2_Stat_Bll_Payment::cal($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}
	
	public function activeuserlevelAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		
		$log = Hapyfish2_Stat_Bll_Day::getActiveUserLevel($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}
	
	public function activeuserfightlevelAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		
		$log = Hapyfish2_Stat_Bll_Day::getActiveUserFightLevel($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}
	
	public function mainhourAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		
		$log = Hapyfish2_Stat_Bll_DayHour::getMain($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}

	public function statmainhourAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		
		$log = Hapyfish2_Stat_Bll_Statmainhour::getStatMainHour($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}
	/**************************这快留给豆豆**********************************/
	//战斗
	public function fightmainAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		$main = Hapyfish2_Stat_Bll_Stat2x::getMain($day);
		$monter = Hapyfish2_Stat_Bll_Stat2x::getMonster($day);
		$mater = Hapyfish2_Stat_Bll_Stat2x::getMater($day);
		$data = array('main' => $main, 'monter'=>$monter, 'mater'=>$mater);
		$this->echoResult(array('data'=>$data));
	}
	//交互
	public function mutualmainAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		$main = Hapyfish2_Stat_Bll_Stat2x::getMutual($day);
		$this->echoResult(array('data'=>$main));
	}
	//修理
	public function repairmainAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		$main = Hapyfish2_Stat_Bll_Stat2x::getRepair($day);
		$this->echoResult(array('data'=>$main));
	}
	
	//建筑升级
	public function upgradeAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		$main = Hapyfish2_Stat_Bll_Upgrade::getUpgrade($day);
		$this->echoResult(array('data'=>$main));
	}
	
	/**************************这给你吧 我不要了**********************************/
	//佣兵
	public function mercenarymainAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		
		$log = Hapyfish2_Stat_Bll_Mercenary::getMercenaryMain($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}

	//订单
	public function ordermainAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		
		$log = Hapyfish2_Stat_Bll_Order::getOrderMain($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}

	//道具
	public function itemmainAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		
		$log = Hapyfish2_Stat_Bll_Item::getItemMain($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}
	
	//商店
	public function shopmainAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		
		$log = Hapyfish2_Stat_Bll_Shop::getShopMain($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}
	
	//合成术
	public function mixmainAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		
		$log = Hapyfish2_Stat_Bll_Mix::getMixMain($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}
	
	public function getfaqAction()
	{
		$start = $this->_request->getParam('start', 0);
		$end = $this->_request->getParam('end', 0);
		$page = $this->_request->getParam('page', 1);
		$type = $this->_request->getParam('type', 3);
		$status = $this->_request->getParam('status', 2);
		$id = $this->_request->getParam('id', 0);
		$log = Hapyfish2_Stat_Bll_Faq::getFaq($start, $end, $page, $type,$status,$id);
		$data = array('data' => $log);
		$this->echoResult($data);
	}
	
	public function getexportfaqAction()
	{
		$start = $this->_request->getParam('start', 0);
		$end = $this->_request->getParam('end', 0);
		$type = $this->_request->getParam('type', 3);
		$status = $this->_request->getParam('status', 2);
		$id = $this->_request->getParam('id', 0);
		$log = Hapyfish2_Stat_Bll_Faq::getExportFaq($start, $end, $type,$status);
		$data = array('data' => $log);
		$this->echoResult($data);
	}
	public function contrastAction()
	{
		$tb = $this->_request->getParam('table');
		$data = Hapyfish2_Stat_Bll_Contrast::getData($tb);
		$this->echoResult($data);
	}
	
	//道具
	public function newuserAction()
	{
		$day = $this->_request->getParam('day');
		if (empty($day)) {
			$day = date("Ymd", strtotime("-1 day"));
		}
		
		$log = Hapyfish2_Stat_Bll_NewUser::getNewUser($day);
		$data = array('data' => $log);
		$this->echoResult($data);
	}
	
	public function getimageAction()
	{
		$id = $this->_request->getParam('id');
		$log = Hapyfish2_Stat_Dal_Faq::getDefaultInstance();
		$data = $log->getimage($id);
		Header( "Content-type: {$data['image_type']}");
		echo $data['image'];
		exit;
	}
	public function updatefaqAction()
	{
		$id = $this->_request->getParam('id');
		$status =  $this->_request->getParam('status');
		$answer =  $this->_request->getParam('answer');
		$tag = $this->_request->getParam('tag');
		if($status >= 0){
			$data['status'] = $status;
		}
		if($answer){
			$data['answer'] = $answer;
		}
		if($tag){
			$data['tag'] = $tag;
		}
		$dal = Hapyfish2_Stat_Dal_Faq::getDefaultInstance();
		if($tag){
			$dal -> updatetag();
		}
		$dal->updateFaq($id, $data);
		$data = array('data' => array(1));
		$this->echoResult($data);
	}
}