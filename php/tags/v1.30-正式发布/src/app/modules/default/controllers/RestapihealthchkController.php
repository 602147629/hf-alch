<?php

class RestapihealthchkController extends Hapyfish2_Controller_Action_Api
{
    protected function getRestList()
    {
        $list = array(
        	'getuser' => 'getuser',
        	'getfriend' => 'getfriend',
        	'getinvitedids' => 'getinvitedids'
        );
        return $list;
    }
    
    protected function getRest()
    {
    	$rest = OpenApi_RestFactory::getRest();
    	$rest->setUser($this->info['puid'], $this->info['session_key']);
    	return $rest;
    }

    public function listAction()
    {
        $list = $this->getRestList();
        $html = '';
        foreach ($list as $key=>$val) {
            $url = HOST . '/restapihealthchk/' . $val;
            $html .= "$key: <a href='$url' target='_blank'>click to test</a><br/>";
        }

        $html = 'Restful Api To Check:<br/>' . $html;
        echo $html;
        exit;
    }

    public function getuserAction()
    {
        $rest = $this->getRest();
    	$data = $rest->usersGetInfo();
        $this->echoResult($data);
    }

    public function getfriendAction()
    {
        $rest = $this->getRest();
    	$data = $rest->friendGetAppFriends();
        $this->echoResult($data);
    }

    public function getinvitedidsAction()
    {
        $rest = $this->getRest();
    	$data = $rest->appGetInvitedIds();
        $this->echoResult($data);
    }
    
}