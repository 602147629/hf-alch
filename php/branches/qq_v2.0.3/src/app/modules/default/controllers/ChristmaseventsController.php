<?php

class ChristmaseventsController extends Hapyfish2_Controller_Action_Api {

    /**
     *
     *  圣诞摇摇乐 活动 调用新浪微博 游戏微博 关注接口
     */
    public function initAction() {
        $context = Hapyfish2_Util_Context::getDefaultInstance();
        $sessionKey = $context->get('session_key');
        $rest = OpenApi_SinaWeibo_Client::getInstance();
        $rest->setUser($sessionKey);
        $data = $rest->isFans();
        print_r($data);
    }

}