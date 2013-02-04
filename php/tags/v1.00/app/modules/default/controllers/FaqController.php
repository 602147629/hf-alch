<?php

class FaqController extends  Hapyfish2_Controller_Action_Api
{
	
    public function indexAction()
    {
		$uid = (int)$this->_request->getParam('uid');
		$msg = $this->_request->getParam('msg');
        $this->view->hostUrl = HOST;
        $this->view->staticUrl = STATIC_HOST;
        $time = time();
        $sig = HOST.'.'.$time.'.'.$uid;
        $this->view->sig = md5($sig);
        $this->view->time = $time;
        $this->view->uid = $uid;
        $this->view->msg = $msg;
		$this->render();
    }
    
    public function updateAction()
    {
    	$uid = (int)$this->_request->getParam('uid');
    	$type = $this->_request->getParam('type');
    	$title = $this->_request->getParam('title');
    	$content = $this->_request->getParam('content');
    	$sig = $this->_request->getParam('sig');
    	$t = $this->_request->getParam('time');
    	$msig = md5(HOST.'.'.$t.'.'.$uid);
     	$key = 'lock:faq:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
    	//get lock
		$ok = $lock->lock($key,30);
		if (!$ok) {
            $msg = '两次提问间隔不能小于30秒';
            $this->_redirect("faq/index?uid=".$uid.'&msg='.$msg);
		}
    	if($msig != $sig){
    		$msg = '别捣乱';
    		$this->_redirect("faq/index?uid=".$uid.'&msg='.$msg);
    	}
    	$time = time();
    	$data['uid'] = $uid;
    	$data['type'] = $type;
    	$data['title'] = $title;
    	$data['content'] = $content;
    	$data['create_time'] = $time;
    	$dal = Hapyfish2_Stat_Dal_Faq::getDefaultInstance();
    	$dal->insert($data);
    	$msg = '提交成功';
    	$this->_redirect("faq/index?uid=".$uid.'&msg='.$msg);
    }
    
    public function myfaqAction()
    {
    	$this->view->hostUrl = HOST;
        $this->view->staticUrl = STATIC_HOST;
    	$uid = (int)$this->_request->getParam('uid');
    	$dal = Hapyfish2_Stat_Dal_Faq::getDefaultInstance();
        $where = array(
        	'uid'=>$uid,
        );
        $faq = $dal->getFaq($where);
        $this->view->faq = $faq;
         $this->view->uid = $uid;
        $this->render();
    }
}