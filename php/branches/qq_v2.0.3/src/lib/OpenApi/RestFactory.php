<?php

class OpenApi_RestFactory
{

    public static function getRest()
    {
        if (! defined('PLATFORM')) {
            return null;
        }
        $rest = null;
        switch (PLATFORM) {
            case 'kaixin':
                $rest = OpenApi_Kaixin_Kaixin::getInstance();
                break;
            case 'sinaweibo':
                $rest = OpenApi_SinaWeibo_Client::getInstance();
                break;
            case 'pengyou':
            case 'qzone':
                $rest = OpenApi_QQ_Client::getInstance();
                break;
            case 'renren':
                $rest = OpenApi_Renren_Renren::getInstance();
                break;
            case 'taobao':
                //$rest = OpenApi_Taobao_Taobao::getInstance();
                break;
            default:
                break;
        }
        return $rest;
    }
}