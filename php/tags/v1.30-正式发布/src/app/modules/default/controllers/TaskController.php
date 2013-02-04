<?php

/**
 * Alchemy task controller
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/03    zx
 */
class TaskController extends Hapyfish2_Controller_Action_Api
{
	/**
	 *
	 * 接任务
	 */
	public function acceptAction()
    {
    	$uid = $this->uid;
        $taskId = $this->_request->getParam('id');

        $status = Hapyfish2_Alchemy_Bll_Task::acceptTask($uid, $taskId);
        if ($status < 0) {
        	$this->echoError($status);
        }
        $this->flush();
    }

    /**
	 *
	 * 完成任务
	 */
    public function completeAction()
    {
        $uid = $this->uid;
        $taskId = $this->_request->getParam('id');
        $isComplete = (int)$this->_request->getParam('isFinish');

        $status = Hapyfish2_Alchemy_Bll_Task::completeTask($uid, $taskId, $isComplete);
        if ($status < 0) {
        	$this->echoError($status);
        }
        $this->flush();
    }

    /**
	 *
	 * 取得日常任务列表
	 */
    public function dailyAction()
    {
        $uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_Task::getDailyTask($uid);
        if ($status < 0) {
        	$this->echoError($status);
        }
        $this->flush();
    }

	/**
	 *
	 * 任务已读标记
	 */
    public function readAction()
    {
        $uid = $this->uid;
        $taskId = (int)$this->_request->getParam('id');

        $status = Hapyfish2_Alchemy_Bll_Task::setRead($uid, $taskId);
        if ($status < 0) {
        	$this->echoError($status);
        }
        $this->flush();
    }
    
    public function getactivityAction()
    {
		$uid = $this->uid;
        $status = Hapyfish2_Alchemy_Bll_Activity::getTaskVo($uid);
        if ($status < 0) {
        	$this->echoError($status);
        }
        $this->flush();
    }
    
    public function initactivityAction()
    {
    	$uid = $this->uid;
        $status = Hapyfish2_Alchemy_Bll_Activity::init($uid);
        if ($status < 0) {
        	$this->echoError($status);
        }
        $this->flush();
    }
    
    public function getactivityawardAction()
    {
    	$uid = $this->uid;
    	$type = (int)$this->_request->getParam('type');
    	$status = Hapyfish2_Alchemy_Bll_Activity::getActivityAward($uid, $type);
    	 if ($status < 0) {
        	$this->echoError($status);
        }
        $this->flush();
    }

}