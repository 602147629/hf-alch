<?php

/**
 * Alchemy api controller
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/02    Nick
 */
//class OrderController extends Zend_Controller_Action
class OrderController extends Hapyfish2_Controller_Action_Api
{    
    public function getorderlistAction()
    {
    	$uid = $this->uid;
                
        $result = Hapyfish2_Alchemy_Bll_Order::getOrderList($uid);

        $this->echoResult($result);
    }
    
    public function clearorderlistAction()
    {
    	$uid = $this->uid;
                
        $result = Hapyfish2_Alchemy_HFC_Order::updateOrderList($uid, array());

        $this->echoResult($result);
    }
    
    public function getrequestorderAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
                
        $result = Hapyfish2_Alchemy_Bll_Order::getRequestOrder($uid, $id);

        $this->echoResult($result);
    }

    public function delrequestAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
                
        $result = Hapyfish2_Alchemy_Bll_Order::delRequestOrder($uid, $id);

        $this->echoResult($result);
    }
    
    public function clearsatisAction()
    {
    	$uid = $this->uid;
    	$satisfaction = $this->_request->getParam('satis');
    	                
        $result = Hapyfish2_Alchemy_HFC_Order::updateSatisfaction($uid, $satisfaction);

        $this->echoResult($result);
    }
    
    public function getrandorderAction()
    {
    	$uid = $this->uid;
    	$level = $this->_request->getParam('level', 10);
    	                
        $result = Hapyfish2_Alchemy_Bll_Order::getRandOrder($uid, $level);

        $this->echoResult($result);
    }

    public function getrequestlistAction()
    {
    	$uid = $this->uid;
    	
        $result = Hapyfish2_Alchemy_HFC_Order::getRequestList($uid);

        $this->echoResult($result);
    }
    
    public function clearrequestlistAction()
    {
    	$uid = $this->uid;
    	
        $result = Hapyfish2_Alchemy_Bll_Order::clearRequestOrder($uid);

        $this->echoResult($result);
    }
    
/***************************************************************************/
    
    /**
     * 读取所有临时订单
     */
    public function getallrequestAction()
    {
    	$uid = $this->uid;
                
        $status = Hapyfish2_Alchemy_Bll_Order::getAllRequest($uid);
        
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 请求新订单
     */
    public function requestorderAction()
    {
    	$uid = $this->uid;
    	//$isNovice = $this->_request->getParam('isNovice');
        
        $status = Hapyfish2_Alchemy_Bll_Order::requestOrder($uid);
        
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 接受订单
     */
    public function acceptorderAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	
    	$status = Hapyfish2_Alchemy_Bll_Order::acceptOrder($uid, $id);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }

    /**
     * 完成订单
     */
    public function completeorderAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	$isFailed = $this->_request->getParam('isFailed', 0);
    	$wid = $this->_request->getParam('itemId', 0);
    	$status = Hapyfish2_Alchemy_Bll_Order::completeOrder($uid, $id, $isFailed, $wid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 放弃订单
     */
    public function rejectorderAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	$request = $this->_request->getParam('request');
    	
    	$status = Hapyfish2_Alchemy_Bll_Order::rejectOrder($uid, $id, $request);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    /**
     * 刷新订单
     */
    public function refreshorderAction()
    {
    	$uid = $this->uid;
    	$status = Hapyfish2_Alchemy_Bll_Order::refreshOrder($uid);
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    public function initorderAction()
    {
    	$type = $this->_request->getParam('type', 0);
    	$uid = $this->uid;
    	$status = Hapyfish2_Alchemy_Bll_Order::initorder($uid,$type);
    	if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    public function completeneworderAction()
    {
    	$uid = $this->uid;
    	$id = $this->_request->getParam('id');
    	$wid = $this->_request->getParam('itemId', 0);
    	$status = Hapyfish2_Alchemy_Bll_Order::completeNewOrder($uid, $id, $wid);
    	
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
}