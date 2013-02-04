<?php

class DiamondexchangeController extends Hapyfish2_Controller_Action_Api {

    /**
     *
     * 基础信息
     */
    public function listAction() {
        header('Cache-Control: max-age=31104000');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31104000) . ' GMT');
        $gz = $this->_request->getParam('gz', 0);
        if ($gz == 1) {
            header('Content-Type: application/octet-stream');
            echo Hapyfish2_Alchemy_Bll_BasicInfo::getGiftVoData('1.0', true);
        } else {
            echo Hapyfish2_Alchemy_Bll_Gift::getGiftVoData();
        }
        exit;
    }

    /**
     *  聚神点金  初始化  获得 玩家现在可以兑换的次数
     */
    public function cashcowinitAction() {
        $uid = $this->uid;
        $result = Hapyfish2_Alchemy_Bll_DiamondExchange::init($uid);
        if ($result < 0) {
            $this->echoError($result);
        }
        $this->flush();
    }

    /**
     *  聚神点金  静态数据 获取 该玩家 现在可以兑换的数据
     */
    public function cshcowstaticAction() {
        $uid = $this->uid;
        $result = Hapyfish2_Alchemy_Bll_DiamondExchange::CashCowStatic($uid);
        if ($result < 0) {
            $this->echoError($result);
        }
        $this->flush();
    }

    /**
     *  聚神点金  根据类型  进行兑换
     */
    public function cshcowAction() {
        $uid = $this->uid;
        $type = $this->_request->getParam('type');
        $result = Hapyfish2_Alchemy_Bll_DiamondExchange::CashQQCowCommand($uid, $type);
        if ($result < 0) {
            $this->echoError($result);
        }
        $this->flush();
    }

    /**
     *  聚神点金  重置
     */
    public function cshcowrestAction() {
        $uid = $this->uid;
        $action = $this->_request->getParam('action');
        $result = Hapyfish2_Alchemy_Bll_DiamondExchange::cshcowrest($uid,$action);
        if ($result < 0) {
            $this->echoError($result);
        }
        $this->flush();
    }

}