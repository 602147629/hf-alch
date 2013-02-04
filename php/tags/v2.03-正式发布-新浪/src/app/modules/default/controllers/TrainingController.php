<?php

/**
 * Alchemy training camp controller
 *
 * @copyright  Copyright (c) 2012 HapyFish
 * @create      2012/08    Nick
 */
class TrainingController extends Hapyfish2_Controller_Action_Api
{
	
	/**
	 * 初始化训练营，静态
	 */
	public function inittrainingstaticAction()
	{    	
		header("Cache-Control: max-age=2592000");
		$result = Hapyfish2_Alchemy_Bll_Training::initTrainingStatic();
		
		$this->echoResult($result);
	}
	
    /**
     * 初始化训练营面板，动态
     * 
     */
    public function inittrainingAction()
    {
    	$uid = $this->uid;

        $status = Hapyfish2_Alchemy_Bll_Training::initTraining($uid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 开始训练
     */
    public function startAction()
    {
    	$uid = $this->uid;
        $tid = $this->_request->getParam('id');
        $roleId = $this->_request->getParam('roleId');
        $gemType = $this->_request->getParam('gemType');

        $status = Hapyfish2_Alchemy_Bll_Training::startTraining($uid, $roleId, $tid, $gemType);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
	
    /**
     * 完成训练
     */
    public function completeAction()
    {
    	$uid = $this->uid;
        $roleId = $this->_request->getParam('roleId');

        $status = Hapyfish2_Alchemy_Bll_Training::completeTraining($uid, $roleId);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 添加训练位置
     */
    public function addposAction()
    {
    	$uid = $this->uid;
        $type = $this->_request->getParam('type');

        $status = Hapyfish2_Alchemy_Bll_Training::addPos($uid, $type);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 快速结束等待时间
     */
    public function completecdAction()
    {
    	$uid = $this->uid;
        $roleId = $this->_request->getParam('roleId');
    	
    	$status = Hapyfish2_Alchemy_Bll_Training::completeCd($uid, $roleId);
    	
    	if ($status < 0) {
    		$this->echoError($status);
    	}
    	$this->flush();
    }
    
}