<?php

/**
 * Alchemy arena controller
 *
 * @copyright  Copyright (c) 2012 HapyFish
 * @create      2012/08    Nick
 */
class ArenaController extends Hapyfish2_Controller_Action_Api
{
	
    /**
     * 初始化竞技场面板
     * 
     */
    public function initarenaAction()
    {
    	$uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_Arena::initArena($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 查看对手信息
     */
	public function getopponentAction()
	{
		$uid = $this->uid;
        $fid = $this->_request->getParam('fid');
		 
		$status = Hapyfish2_Alchemy_Bll_Arena::getOpponentInfo($uid, $fid);
		
		if ($status < 0) {
			$this->echoError($status);
		}
		$this->flush();
	}
	
	/**
	 * 开始挑战
	 */
	public function challengeAction()
	{
		$uid = $this->uid;
        $fid = $this->_request->getParam('fid');
		 
		$status = Hapyfish2_Alchemy_Bll_Arena::challenge($uid, $fid);
		
		if ($status < 0) {
			$this->echoError($status);
		}
		$this->flush();
	}
	
	/**
	 * 查看排行榜
	 */
	public function initrankAction()
	{
		$uid = $this->uid;
        $from = $this->_request->getParam('from');
		 
		$status = Hapyfish2_Alchemy_Bll_Arena::initRank($uid, $from);
		
		if ($status < 0) {
			$this->echoError($status);
		}
		$this->flush();
	}

	/**
	 * 刷新竞技场对手
	 */
	public function refreshopponentsAction()
	{
		$uid = $this->uid;
		
		$status = Hapyfish2_Alchemy_Bll_Arena::refreshOpponents($uid);
	
		if ($status < 0) {
			$this->echoError($status);
		}
		$this->flush();
	}
	
	/**
	 * 查看战报
	 */
	public function getbattlerecordAction()
	{
		$uid = $this->uid;
		
		$status = Hapyfish2_Alchemy_Bll_Arena::getBattleRecord($uid);
	
		if ($status < 0) {
			$this->echoError($status);
		}
		$this->flush();
	}
	
	/**
	 * 查看战斗记录
	 */
	public function readfightAction()
	{
		$uid = $this->uid;
        $id = $this->_request->getParam('id');
        $fid = $this->_request->getParam('fid');
		
		$status = Hapyfish2_Alchemy_Bll_Arena::readFight($uid, $fid, $id);
	
		if ($status < 0) {
			$this->echoError($status);
		}
		$this->flush();
	}
	
	/**
	 * 领取竞技场奖励
	 */
	public function getprizeAction()
	{
		$uid = $this->uid;
		
		$status = Hapyfish2_Alchemy_Bll_Arena::getPrize($uid);
	
		if ($status < 0) {
			$this->echoError($status);
		}
		$this->flush();
	}
	
	public function completecdAction()
	{
		$uid = $this->uid;
		
		$status = Hapyfish2_Alchemy_Bll_Arena::completeCd($uid);
	
		if ($status < 0) {
			$this->echoError($status);
		}
		$this->flush();
	}
	
}