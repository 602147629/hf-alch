<?php

class PaysinawbController extends Hapyfish2_Controller_Action_Page
{
    protected $uid;

    protected $info;

    public function init()
    {
    	$info = $this->vailid();
    	if (!$info) {
    		echo '<html><body><script type="text/javascript">window.top.location="http://game.weibo.com/'.APP_NAME.'/";</script></body></html>';
    		exit;
    	}
    
    	$this->info = $info;
    	$this->uid = $info['uid'];
    
    	$this->view->baseUrl = $this->_request->getBaseUrl();
    	$this->view->staticUrl = STATIC_HOST;
    	$this->view->hostUrl = HOST;
    	$this->view->appId = APP_ID;
    	$this->view->appKey = APP_KEY;
    	$this->view->uid = $info['uid'];
    	$this->view->platformUid = $info['puid'];
    }
    
    protected function vailid()
    {
    	$skey = isset($_COOKIE['hf_skey'])?$_COOKIE['hf_skey']:'';
    	if (!$skey) {
    		return false;
    	}
    
    	$tmp = explode('.', $skey);
    	if (empty($tmp)) {
    		return false;
    	}
    	$count = count($tmp);
    	if ($count != 5 && $count != 6) {
    		return false;
    	}
    
    	$uid = $tmp[0];
    	$puid = $tmp[1];
    	$session_key = base64_decode($tmp[2]);
    	$t = $tmp[3];
    
    	$rnd = -1;
    	if ($count == 5) {
    		$sig = $tmp[4];
    		$vsig = md5($uid . $puid . $session_key . $t . APP_SECRET);
    		if ($sig != $vsig) {
    			return false;
    		}
    	} else if ($count == 6) {
    		$rnd = $tmp[4];
    		$sig = $tmp[5];
    		$vsig = md5($uid . $puid . $session_key . $t . $rnd . APP_SECRET);
    		if ($sig != $vsig) {
    			return false;
    		}
    	}
    
    	//max long time one day
    	if (time() > $t + 86400) {
    		return false;
    	}
    
    	return array('uid' => $uid, 'puid' => $puid, 'session_key' => $session_key,  't' => $t, 'rnd' => $rnd);
    }
    
    public function indexAction()
    {
		$uid = $this->uid;
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		$user['face'] = $user['figureurl'];
		$user['gem'] = Hapyfish2_Alchemy_Bll_Gem::get($uid);
		$this->view->user = $user;
		
		$section = array();
		$note = '';
		$settingInfo = Hapyfish2_Alchemy_Bll_Paysetting::getInfo($uid);
		if ($settingInfo) {
			$section = $settingInfo['section'];
			$note = $settingInfo['note'];
		}
		$this->view->section = $section;
		$this->view->note = $note;
		$this->view->appServerTtpe = APP_SERVER_TYPE;
		$this->render();
    }
    
	public function orderAction()
	{
		$uid = $this->uid;
		$type = $this->_request->getParam('type');
		$result = array('status' => 0);
		if (empty($type) || $type<1 || $type>4) {
		    $result['msg'] = 'invalide';
		    echo json_encode($result);
			exit;
		}

		$rest = OpenApi_SinaWeibo_Client::getInstance();
		try {
            $rest->setUser($this->info['session_key']);
		} catch (Exception $e) {
            $result['msg'] = 'invalide';
		    echo json_encode($result);
			exit;
        }
        
        $order = Hapyfish2_Alchemy_Bll_Payment::regOrderForSinawb($uid, $type);
        if ( !$order ) {
        	$result['msg'] = 'create order';
		    echo json_encode($result);
			exit;
        }
        
		$orderId = $order['orderid'];
		$amount = $order['amount'];
		//$amount = 1;
		$desc = $order['pname'];

		$sign = md5($orderId.'|'.$amount.'|' . $desc . '|' . APP_SECRET);
		
		if ( APP_SERVER_TYPE == 1 ) {
			info_log('$orderId2:'.$orderId, 'wb_test');
			//get token from wb rest api
	        $rowToken = $rest->getPayToken($orderId, $amount, $desc, $sign);
		}
		else {
			info_log('$orderId1:'.$orderId, 'wb_test');
			//get token from wb rest api
	        $rowToken = $rest->getPayTokenTest($orderId, $amount, $desc, $sign);
		}
        
        if ($rowToken) {
            $token = $rowToken['token'];
            $puid = $rowToken['order_uid'];
            $order['token'] = $token;
            
            $rst = Hapyfish2_Alchemy_Bll_Payment::addOrderForSinawb($uid, $order);
            if ($rst) {
                $info = array('token'=>$token, 'amount'=>$amount, 'order_id'=>$orderId, 'desc'=>$desc);
                $result['status'] = 1;
                $result['info'] = $info;
                echo json_encode($result);
			    exit;
            }
        }

        $result['msg'] = 'failed get token';
	    echo json_encode($result);
		exit;
	}
}