<?php

/**
 * Alchemy mix controller
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/02    Nick
 */
//class MixController extends Zend_Controller_Action
class MixController extends Hapyfish2_Controller_Action_Api
{    
    /**
     * 
     */
    public function startmixAction()
    {
        $uid = $this->uid;
        $mixCid = $this->_request->getParam('mixCid');
        $num = $this->_request->getParam('num', 1);
        $probability = $this->_request->getParam('probability');
        $furnaceId = $this->_request->getParam('id', 0);

        $key = 'startmix:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

        //get lock
        $ok = $lock->lock($key);
        if (!$ok) {
            $result = array('status' => -1, 'content' => 'serverWord_103');
            $this->echoResult(array('result' => $result));
        }

        $furnaceId = substr($furnaceId, 0, -2);
        $status = Hapyfish2_Alchemy_Bll_Mix::startMix($uid, $mixCid, $furnaceId, $num, $probability);

        //release lock
        $lock->unlock($key);
        
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
    public function completemixAction()
    {
        $uid = $this->uid;
        $furnaceId = $this->_request->getParam('id');
        $isFinish = $this->_request->getParam('isFinish', 1);
        
        $key = 'completemix:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);

        //get lock
        $ok = $lock->lock($key);
        if (!$ok) {
            $result = array('status' => -1, 'content' => 'serverWord_103');
            $this->echoResult(array('result' => $result));
        }

        $furnaceId = substr($furnaceId, 0, -2);
        $status = Hapyfish2_Alchemy_Bll_Mix::completeMix($uid, $furnaceId, $isFinish);

        //release lock
        $lock->unlock($key);

        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
    }
    
}