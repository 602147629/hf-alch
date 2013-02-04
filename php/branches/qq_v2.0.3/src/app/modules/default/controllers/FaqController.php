<?php

class FaqController extends  Hapyfish2_Controller_Action_Api
{
	
    protected function getClientIP()
    {
    	$ip = false;
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode (', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip) {
				array_unshift($ips, $ip);
				$ip = false;
			}
			for ($i = 0, $n = count($ips); $i < $n; $i++) {
				if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])) {
					$ip = $ips[$i];
					break;
				}
			}
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
    }
	
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
        $this->view->ip = $this->getClientIP();
        $this->view->browser = MyLib_Browser::getBrowser();
		$this->render();
    }
    
    public function updateAction()
    {
    	$uid = (int)$this->_request->getParam('uid');
    	$type = $this->_request->getParam('type');
    	$title = $this->_request->getParam('title');
    	$content = $this->_request->getParam('content');
    	$ip = $this->_request->getParam('ip');
    	$diqu = $this->_request->getParam('diqu');
    	$xianlu = $this->_request->getParam('xianlu');
    	$sig = $this->_request->getParam('sig');
    	$t = $this->_request->getParam('time');
    	$flash = $this->_request->getParam('flash');
    	$browser = $this->_request->getParam('browser');
    	$photo = $_FILES['photo'];
    	$msig = md5(HOST.'.'.$t.'.'.$uid);
     	$key = 'lock:faq:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
    	//get lock
		$ok = $lock->lock($key,30);
//		if (!$ok) {
//            $msg = '两次提问间隔不能小于30秒';
//            $this->_redirect("faq/index?uid=".$uid.'&msg='.$msg);
//		}
    	if($msig != $sig){
    		$msg = '别捣乱';
    		$this->_redirect("faq/index?uid=".$uid.'&msg='.$msg);
    	}
    	$check = Hapyfish2_Alchemy_Bll_Upload::check($photo);
    	if(is_array($check)){
    		$data['image'] = $check['image'];
    		$data['image_type'] = $check['type'];
    	}else{
    		if($check !== null){
    			$this->_redirect("faq/index?uid=".$uid.'&msg='.$check);
    		}
    	}
    	$time = time();
    	$data['uid'] = $uid;
    	$data['type'] = $type;
    	$data['title'] = $title;
    	$data['content'] = $content;
    	$data['create_time'] = $time;
    	$data['ip'] = $ip;
    	$data['diqu'] = $diqu;
    	$data['xianlu'] = $xianlu;
    	$data['flash'] = $flash;
    	$data['browser'] = $browser;
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
	public function deleteAction()
	{
		$uid = (int)$this->_request->getParam('uid');
		$ids = $this->_request->getParam('ids');
		$dal = Hapyfish2_Stat_Dal_Faq::getDefaultInstance();
		if(!empty($ids)){
			$ids = implode(',', $ids);
			$dal->detelefaq($ids);
		}
		$this->_redirect("faq/myfaq?uid=".$uid);
	}
	
	public function showdetailAction()
	{
		$id = $this->_request->getParam('id');
		$uid = (int)$this->_request->getParam('uid');
		$dal = Hapyfish2_Stat_Dal_Faq::getDefaultInstance();
		$data = $dal->getdetail($id);
		$this->view->hostUrl = HOST;
        $this->view->staticUrl = STATIC_HOST;
		$this->view->data = $data;
		$this->view->uid = $uid;
        $this->render();
	}
    
}