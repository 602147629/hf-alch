<?php

/**
 * Alchemy api controller
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/02    Nick
 */
class FriendsController extends Zend_Controller_Action
//class MixController extends Hapyfish2_Controller_Action_Api
{
    public function init()
    {
    	$this->uid = '1011';
    	
    	$controller = $this->getFrontController();
        $controller->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler');
        $controller->setParam('noViewRenderer', true);
    }

    protected function echoResult($data)
    {
        header("Cache-Control: no-store, no-cache, must-revalidate");
        echo json_encode($data);
        exit;
    }
    
    public function indexAction()
    {
    	$uid = $this->uid;
                
        $result = array();

        $this->echoResult($result);
    }
    
}