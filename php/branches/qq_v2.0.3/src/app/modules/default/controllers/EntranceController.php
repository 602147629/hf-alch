<?php

/**
 * 入口
 * 处理完后将自动跳转到应用地址
 *
 */
class EntranceController extends Hapyfish2_Controller_Action_Normal
{

	public function indexAction()
	{
		$this->enterGame();
	}

	//活动连接统计
    public function promotionAction()
    {
        $pcode = $this->_request->getParam('pcode');
        if (empty($pcode)) {
            $this->enterGame();
        }

        $code = base64_decode($pcode);
        if (!$code) {
			$this->enterGame();
        }

		$ok = Hapyfish2_Log_Promotion::handle($code);

        if ($ok) {
            //set cookie
            $this->addCookie('hf_pcode', $pcode);
			$params = array('hf_pcode' => $pcode, 'hf_nolog' => 1);
	        $this->enterGame($params);
        } else {
        	$this->enterGame();
        }

        exit;
    }

    //平台Feed
    public function feedAction()
    {
        $fcode = $this->_request->getParam('fcode');
        if (empty($fcode)) {
            $this->enterGame();
        }

        $code = base64_decode($fcode);
        if (!$code) {
			$this->enterGame();
        }

		$ok = Hapyfish2_Log_Feed::handle($code);

        if ($ok) {
            //set cookie
            $this->addCookie('hf_fcode', $fcode);
			$params = array('hf_fcode' => $fcode);
	        $this->enterGame($params);
        } else {
        	$this->enterGame();
        }

        exit;
    }
}