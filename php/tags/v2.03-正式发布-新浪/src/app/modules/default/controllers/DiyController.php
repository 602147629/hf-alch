<?php

/**
 * Alchemy diy controller
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/02    Fox
 */
class DiyController extends Hapyfish2_Controller_Action_Api
{	
	//从背包中放入场景
	public function addAction()
    {
    	$uid = $this->uid;
        $cid = $this->_request->getParam('cid');
        $x = $this->_request->getParam('x');
        $z = $this->_request->getParam('z');
        $mirror = $this->_request->getParam('mirror', 0);
        
        $data = array(
        	'cid' => $cid,
        	'x' => $x,
        	'z' => $z,
        	'm' => $mirror
        );
        
        $status = Hapyfish2_Alchemy_Bll_Diy::add($uid, $data);
        if ($status < 0) {
        	$this->echoError($status);
        }
        $this->flush();
    }

    //从场景中移入背包
    public function removeAction()
    {
        $uid = $this->uid;
        $id = $this->_request->getParam('id');
        $status = Hapyfish2_Alchemy_Bll_Diy::remove($uid, $id);
        if ($status < 0) {
        	$this->echoError($status);
        }
        $this->flush();
    }

    //改变位置或方向
    public function editAction()
    {
        $uid = $this->uid;
        $id = $this->_request->getParam('id');
        $x = $this->_request->getParam('x');
        $z = $this->_request->getParam('z');
        $mirror = $this->_request->getParam('mirror', 0);
        
        $data = array();
        
        if ($x !== null) {
        	$data['x'] = (int)$x;
        }
        if ($z !== null) {
        	$data['z'] = (int)$z;
        }
        if ($mirror !== null) {
        	$data['m'] = (int)$mirror;
        }
        $status = Hapyfish2_Alchemy_Bll_Diy::edit($uid, $id, $data);
        if ($status < 0) {
        	$this->echoError($status);
        }
        $this->flush();
    }

}