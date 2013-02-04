<?php

/**
 * Alchemy gift controller
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/04    zx
 */
class GiftController extends Hapyfish2_Controller_Action_Api
{
	/**
	 *
	 * 基础信息
	 */
    public function listAction()
	{
	    header('Cache-Control: max-age=31104000');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31104000). ' GMT');
		$gz = $this->_request->getParam('gz', 0);
		if ($gz == 1) {
			header('Content-Type: application/octet-stream');
			echo Hapyfish2_Alchemy_Bll_BasicInfo::getGiftVoData('1.0', true);
		}
		else {
			echo Hapyfish2_Alchemy_Bll_Gift::getGiftVoData();
		}
		exit;
	}

	/**
	 *
	 * 动态信息
	 */
	public function userAction()
	{
		$uid = $this->uid;
		$newReceCnt = 0;
		$receiveList = Hapyfish2_Alchemy_Bll_Gift::getReceiveList($uid, $newReceCnt);
		$requestList = Hapyfish2_Alchemy_Bll_Gift::getRequestList($uid);

		//can send wish today
		$canWish = true;
	    $today = date('Ymd');
		//read today wish cache
        //get my wish
		$wishCache = Hapyfish2_Alchemy_Bll_Gift::getMywish($uid);
	    if ( $wishCache && isset($wishCache['create_time']) && date('Ymd', $wishCache['create_time']) == $today ) {
            $canWish = false;
        }
        $giftMyWish = array();
        for ($i=0; $i<3; $i++) {
            $giftMyWish[] = array('id' => 0, 'type' => 0);
        }
        if ($wishCache) {
            if ($wishCache['gid_1']) {
                $giftInfo = Hapyfish2_Alchemy_Cache_Gift::getBasicGiftInfo($wishCache['gid_1']);
                $giftMyWish[0] = array('id' => $giftInfo['gid'], 'type' => $giftInfo['type']);
            }
            if ($wishCache['gid_2']) {
                $giftInfo = Hapyfish2_Alchemy_Cache_Gift::getBasicGiftInfo($wishCache['gid_2']);
                $giftMyWish[1] = array('id' => $giftInfo['gid'], 'type' => $giftInfo['type']);
            }
            if ($wishCache['gid_3']) {
                $giftInfo = Hapyfish2_Alchemy_Cache_Gift::getBasicGiftInfo($wishCache['gid_3']);
                $giftMyWish[2] = array('id' => $giftInfo['gid'], 'type' => $giftInfo['type']);
            }
        }

        $hasNewGift = $newReceCnt ? true : false;

		$giftUser = array('giftNum' => $newReceCnt, 'giftRequestNum' => count($requestList),
						  'isReleaseWish' => $canWish, 'isNewGift' => $hasNewGift);

		$rankResult = Hapyfish2_Alchemy_Bll_Friend::getFriendList($uid, 1, 600);
		$friendList = $rankResult['friends'];
		$mkey2 = 'a:u:gift:sent:g:uids:' . $uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
        $sentCache = $cache->get($mkey2);
		foreach ($friendList as $key=>$data) {
		    $friendList[$key]['giftAble'] = true;
		    if ($sentCache && isset($sentCache['dt']) && $sentCache['dt'] == $today && isset($sentCache['ids'])) {
    		    if (in_array($data['uid'], $sentCache['ids'])) {
    		        $friendList[$key]['giftAble'] = false;
    		    }
		    }

		    $friendList[$key]['giftRequestAble'] = true;
		}

		$result = array('giftDiarys' => $receiveList, 'giftRequests' => $requestList,
						'giftUser' => $giftUser, 'giftFriendUser' => $friendList, 'giftMyWish' => $giftMyWish);

		$this->echoResult($result);

	}

	/**
	 *
	 * 送出礼物-实现 好友愿望
	 */
	public function friendrequestAction()
	{
        $uid = $this->uid;
		$id = $this->_request->getParam('giftRequestId');
		$giftId = $this->_request->getParam('giftId');

	    $aryId = explode('|', base64_decode(urldecode($id)));
		if ( !(isset($aryId[0]) && isset($aryId[1])) ) {
		    $this->echoError(-502);
		}

        $key = 'checkfriendrequest:' . $aryId[1];
        $lock = Hapyfish2_Cache_Factory::getLock($aryId[1]);
    	//get lock
		$ok = $lock->lock($key, 2);
	    if (!$ok) {
            $this->echoError(-102);
		}

   		$status = Hapyfish2_Alchemy_Bll_Gift::sendWish($id, $giftId);

        //release lock
        $lock->unlock($key);

		if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
	}

	/**
	 *
	 * 送出礼物
	 */
	public function sendAction()
	{
		$uid = $this->uid;
		$giftId = $this->_request->getParam('giftId');
		$fids = $this->_request->getParam('friendId');

        $fids = explode('-', $fids);

        $key = 'giftsend:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
    	//get lock
		$ok = $lock->lock($key, 2);
		if (!$ok) {
            $this->echoError(-102);
		}

        $status = Hapyfish2_Alchemy_Bll_Gift::send($uid, $giftId, $fids);

        //release lock
        $lock->unlock($key);

		if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
	}

	/**
	 *
	 * 忽略礼物
	 */
	public function ignoregiftAction()
	{
        $uid = $this->uid;
        $id = $this->_request->getParam('giftDiaryId');
        $ids = array($id);

        $key = 'checkreceivegift:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
    	//get lock
		$ok = $lock->lock($key, 2);
	    if (!$ok) {
            $this->echoError(-102);
		}

		$status = Hapyfish2_Alchemy_Bll_Gift::ignore($uid, $ids);

        //release lock
        $lock->unlock($key);

		if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
	}

	/**
	 *
	 * 接受礼物
	 */
	public function receivegiftAction()
	{
        $uid = $this->uid;
        $ids = $this->_request->getParam('giftDiaryId');

	    $key = 'checkreceivegift:' . $uid;
        $lock = Hapyfish2_Cache_Factory::getLock($uid);
    	//get lock
		$ok = $lock->lock($key, 2);
		if (!$ok) {
            $this->echoError(-102);
		}

		$ids = explode('-', $ids);
    	$status = Hapyfish2_Alchemy_Bll_Gift::accept($uid, $ids);

    	//release lock
        $lock->unlock($key);

		if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
	}

	/**
	 *
	 * 我的愿望
	 */
	public function mywishAction()
	{
		$uid = $this->uid;
		$gids = $this->_request->getParam('giftId');

	    $gids = explode('-', $gids);
		$status = Hapyfish2_Alchemy_Bll_Gift::mywish($uid, $gids);
		if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
	}

	/**
	 *
	 * 设置新礼物消息已读
	 */
    public function hadreadAction()
	{
	    $uid = $this->uid;
	    $status = Hapyfish2_Alchemy_Bll_Gift::readReceive($uid);
        //$result = array('status' => $status);
        if ($status < 0) {
			$this->echoError($status);
		}
        $this->flush();
	}
	
	public function initeventgiftAction()
	{
		 $uid = $this->uid;
	   	 $result = Hapyfish2_Alchemy_Bll_EventGift::init($uid);
        //$result = array('status' => $status);
//        if ($status < 0) {
//			$this->echoError($status);
//		}
       $this->echoResult($result);
	}
	
	public function receiveawardAction()
	{
		$type = $this->_request->getParam('type');
		$uid = $this->uid;
		$data = Hapyfish2_Alchemy_Bll_EventGift::receiveGift($uid, $type);
		$this->flush();
	}
	public function eventgiftinfoAction()
	{
		$uid = $this->uid;
		$data = Hapyfish2_Alchemy_Bll_EventGift::eventGiftInfo($uid);
		$this->echoResult($data);
	}

}