<?php

/**
 * Alchemy api controller
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/02    Nick
 */
class RestapiController extends Hapyfish2_Controller_Action_Api
{
    protected function getRest()
    {
    	$rest = OpenApi_RestFactory::getRest();
    	$rest->setUser($this->info['puid'], $this->info['session_key']);
    	return $rest;
    }
	
	public function appinvitedAction()
    {
    	$uid = $this->uid;
    	$rest = $this->getRest();
    	$list = $rest->appGetInvitedIds();
    	$count = 0;
    	if (!empty($list)) {
    		$count = Hapyfish2_Alchemy_Bll_Invite::refresh($uid, $list);
    	}
    	$ret = array('count' => $count);
    	$this->echoResult($ret);
    }
    
    public function appfriendAction()
    {
        $rest = $this->getRest();
    	$ids = $rest->friendGetAppFriends();
    	$count = 0;
        if ($ids !== null) {
        	$count = count($ids);
        	$fids = Hapyfish2_Platform_Bll_User::getUids($ids);
        	Hapyfish2_Platform_Bll_Friend::updateFriend($uid, $fids);
        }
    	$ret = array('count' => $count);
    	$this->echoResult($ret);
    }
}