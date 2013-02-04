<?php

/**
 * Alchemy api controller
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/02    Nick
 */
class FeedapiController extends Hapyfish2_Controller_Action_Api
{
	public function feedawardAction()
	{
		$uid = $this->uid;
		$ret = Hapyfish2_Alchemy_Cache_PlatformFeedAward::checkout($uid);
		if ($ret <= 0) {
			$this->echoError($ret);
		}
		$this->flush();
	}
	
	public function staticfeedAction()
	{
		$uid = $this->uid;
		$type = $this->_getParam('type');
		if ($type == 5) {
			$feed = Hapyfish2_Alchemy_Bll_PlatformFeed_Kaixin::occupyHelp($uid);
			$result = array('status' => 1, 'feed' => $feed);
			$this->echoResult($result);
		}
		$this->echoError(-1);
	}
	
}