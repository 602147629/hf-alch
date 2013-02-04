<?php

class VipController extends Zend_Controller_Action
{


    public function indexAction()
    {
    	 $this->view->staticUrl = STATIC_HOST;
		$this->render();
    }
    
}