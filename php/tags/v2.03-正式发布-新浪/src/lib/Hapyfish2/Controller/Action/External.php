<?php

/**
 * External Base Controller
 * 外部调用Action基类，主要是以外部开放api形式调用
 *
 */
class Hapyfish2_Controller_Action_External extends Zend_Controller_Action
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

    /**
     * proxy for undefined methods
     * override
     * @param string $methodName
     * @param array $args
     */
    public function __call($methodName, $args)
    {
        $this->echoError(1, 'No This Method:' . $methodName);
    }
}