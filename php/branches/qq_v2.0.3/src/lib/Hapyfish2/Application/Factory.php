<?php

class Hapyfish2_Application_Factory
{

    public static function newApplication(Zend_Controller_Action $actionController)
    {
        if (! defined('PLATFORM')) {
            return null;
        }
        $app = null;
        switch (PLATFORM) {
            case 'kaixin':
                $app = Hapyfish2_Application_Kaixin::newInstance($actionController);
                break;
            case 'sinaweibo':
                $app = Hapyfish2_Application_SinaWeibo::newInstance($actionController);
                break;
            case 'pengyou':
            case 'qzone':
                $app = Hapyfish2_Application_QQ::newInstance($actionController);
                break;
            case 'renren':
                $app = Hapyfish2_Application_Renren::newInstance($actionController);
                break;
            case 'taobao':
                //$app = Hapyfish2_Application_Taobao::newInstance($actionController);
                break;
            default:
                break;
        }
        return $app;
    }
}