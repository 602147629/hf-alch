<?php

class Hapyfish2_Alchemy_Bll_Invite_Factory
{
    public static function handle(Hapyfish2_Application_Abstract $application)
    {
        if (! defined('PLATFORM')) {
            return;
        }

        switch (PLATFORM) {
            case 'sinaweibo':
                Hapyfish2_Alchemy_Bll_Invite_SinaWeibo::handle($application);
                break;
            default:
                break;
        }
    }
}