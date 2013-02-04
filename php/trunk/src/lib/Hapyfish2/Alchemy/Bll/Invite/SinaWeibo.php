<?php

class Hapyfish2_Alchemy_Bll_Invite_SinaWeibo
{
    public static function handle(Hapyfish2_Application_Abstract $application)
    {
        if (isset($_REQUEST['inviter_id'])) {
            $inviterId = $_REQUEST['inviter_id'];
            $rowInviter = Hapyfish2_Platform_Bll_UidMap::getUser($inviterId);
            $uid = $application->getUserId();
            $sessionKey = $application->getSessionKey();
            Hapyfish2_Alchemy_Bll_Invite::add($rowInviter['uid'], $uid);
            $application->getRest()->setUser($sessionKey);
            $application->getRest()->ignoreAllInvite();
            info_log($rowInviter['uid'] . ' -> ' . $uid, 'invitedone');
        }
    }
}