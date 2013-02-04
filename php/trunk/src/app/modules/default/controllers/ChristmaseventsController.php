<?php

class ChristmaseventsController extends Hapyfish2_Controller_Action_Api {
    /*
     *  圣诞摇摇乐 数据初始
     */

    public function christmasinitAction() {
        $uid = $this->uid;
        $time = date('Ymd', mktime(0, 0, 0));
        $result = Hapyfish2_Alchemy_Bll_ChristmasEvents::ChristmasInit($uid, $time);
        if ($result < 0) {
            $this->echoError($result);
        }
        $this->flush();
    }

    /**
     *   圣诞摇摇乐 结晶获取
     */
    public function christmasgetawardAction() {
        $uid = $this->uid;
        $time = date('Ymd', mktime(0, 0, 0));
        $result = Hapyfish2_Alchemy_Bll_ChristmasEvents::ChristmasChange($uid, $time);
        if ($result < 0) {
            $this->echoError($result);
        }
        $this->flush();
    }

    /**
     *   圣诞摇摇乐 结晶兑换奖品
     */
    public function christmaschangeAction() {
        $uid = $this->uid;
        $type = $this->_request->getParam('type');
        $time = date('Ymd', mktime(0, 0, 0));
        $result = Hapyfish2_Alchemy_Bll_ChristmasEvents::ChristmasGetAward($uid, $type, $time);
        if ($result < 0) {
            $this->echoError($result);
        }
        $this->flush();
    }

    /**
     * 圣诞摇摇乐  玩家祝福
     */
    public function christmasblessingAction() {
        $uid = $this->uid;
        $fid = $this->_request->getParam('friendUid');
        $time = date('Ymd', mktime(0, 0, 0));
        $result = Hapyfish2_Alchemy_Bll_ChristmasEvents::ChristmasBlessing($uid, $fid, $time);
        if ($result < 0) {
            $this->echoError($result);
        }
        $this->flush();
    }

    /**
     * 圣诞摇摇乐  玩家摇奖重置
     */
    public function christmasrestuserAction() {
        $uid = $this->uid;
        $time = date('Ymd', mktime(0, 0, 0));
        $result = Hapyfish2_Alchemy_Bll_ChristmasEvents::ChristmasUserCount($uid, $time);
        if ($result < 0) {
            $this->echoError($result);
        }
        $this->flush();
    }

    /**
     * 圣诞摇摇乐  好友祝福次数 重置
     */
    public function christmasrestfidAction() {
        $uid = $this->uid;
        $time = date('Ymd', mktime(0, 0, 0));
        $result = Hapyfish2_Alchemy_Bll_ChristmasEvents::ChristmasUserFidCount($uid, $time);
        if ($result < 0) {
            $this->echoError($result);
        }
        $this->flush();
    }

    /**
     * 圣诞摇摇乐   连续登陆 次数+ 1
     */
    public function christmasrestloginAction() {
        $uid = $this->uid;
        $time = $this->_request->getParam('time');
        $result = Hapyfish2_Alchemy_Bll_ChristmasEvents::ChristmasUserLoginCount($uid, $time);
        if ($result < 0) {
            $this->echoError($result);
        }
        $this->flush();
    }

    /**
     * 圣诞摇摇乐   设置时间
     */
    public function christmasrestimeAction() {
        $uid = $this->uid;
        $time = $this->_request->getParam('time');
        $result = Hapyfish2_Alchemy_Cache_ChristmasEvents::updateUserChistmasTime($time,$uid);
        echo $result;
        $this->flush();
    }
    /**
     * 圣诞摇摇乐   设置时间
     */
    public function christmasawardAction() {
        $uid = $this->uid;
        $time = $this->_request->getParam('time');
        $result = Hapyfish2_Alchemy_Bll_ChristmasEvents::Christmas($uid,$time);
        $this->flush();
    }
}