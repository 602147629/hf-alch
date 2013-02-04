<?php

class OpenApi_RestFactory
{
    public static function getRest()
    {
        if (!defined('PLATFORM')) {
            return null;
        }

        if ('renren' == PLATFORM) {
            $rest = OpenApi_Renren_Renren::getInstance();
        }
        else if ('kaixin' == PLATFORM){
            $rest = OpenApi_Kaixin_Kaixin::getInstance();
        }
        else if ('taobao' == PLATFORM){
            //$rest = OpenApi_Taobao_Taobao::getInstance();
        }
        else {
            $rest = null;
        }
        return $rest;
    }

}