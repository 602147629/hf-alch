<?php

class LogController extends Hapyfish2_Controller_Action_Api
{

	public function reportAction()
	{
	    $info = $this->vailid();
        $uid = $info['uid'];
		$type = $this->_request->getParam('type');
		$aryLog = null;
		$log = Hapyfish2_Util_Log::getInstance();
		if ('cLoadTm' == $type) {
            $tm1 = $this->_request->getParam('tm1', 0);
    		$tm2 = $this->_request->getParam('tm2', 0);
    		$tm3 = $this->_request->getParam('tm3', 0);
    		$tm4 = $this->_request->getParam('tm4', 0);
    		$isNew = $this->_request->getParam('isNew', 0);
            $aryLog = array($uid, $tm1, $tm2, $tm3, $tm4, $isNew);

            /*//噪点数据
            if ($tm2 != 0) {
                if ($tm2<$tm1 || $tm2-$tm1 > 600000) {
                    $aryLog = false;
                }
            }
            if ($tm2 != 0 && $tm3 != 0) {
                if ($tm3 != 0 && ($tm3<$tm2 || $tm3-$tm2 > 600000)) {
                    $aryLog = false;
                }
            }
            if ($tm4<$tm3 || $tm4-$tm3 > 86400000) {
                $aryLog = false;
            }*/
		}
		else if ('noflash' == $type) {
		    $isNew = $this->_request->getParam('isNew', 0);
		    $ver = MyLib_Browser::getBrowser();
            $aryLog = array($uid, $ver, $isNew);
		}
		else if ('nocookie' == $type) {
		    $isNew = $this->_request->getParam('isNew', 0);
            $ver = MyLib_Browser::getBrowser();
            $aryLog = array($uid, $ver, $isNew);
		}
        else if ('guide' == $type) {
            $step = $this->_request->getParam('step');
            if ($step) {
                $aryLog = array($uid, $step);
            }
        }else if ('newuser' == $type) {
            $step = $this->_request->getParam('step');
            if ($step) {
                $aryLog = array($uid, $step);
            }
        }
        else if ('flashloadlog' == $type) {
    		$isNew = $this->_request->getParam('isNew', 0);
            $step = $this->_request->getParam('step');
            if ($step) {
            	$actionAry = array('type' => 9, 'cid' => $step);
            	Hapyfish2_Alchemy_Bll_Stat::addActionLog($uid, $actionAry);
                $aryLog = array($uid, $step, $isNew);
            }
        }

		if ($aryLog) {
		    $log->report($type, $aryLog);
		}
		header("HTTP/1.0 204 No Content");
		exit;
	}

	
	public function flashloadAction()
	{
		$info = $this->vailid();
		$uid = $info['uid'];
		
		$aryLog = null;
		$log = Hapyfish2_Util_Log::getInstance();

		$step = $this->_request->getParam('step');
		if ($step) {
			$actionAry = array('type' => 9, 'cid' => $step);
			$rst = Hapyfish2_Alchemy_Bll_Stat::addActionLog($uid, $actionAry);
		}
		if ( $rst ) {
			$result = array('result' => array('status' => 1));
		}
		else {
			$result = array('result' => array('status' => -1));
	        info_log('flashload:failed:'.$step, 'Hapyfish2_Alchemy_Bll_Stat');
		}

		header("Cache-Control: no-store, no-cache, must-revalidate");
		echo json_encode($result);
		exit;
	}
	
 }