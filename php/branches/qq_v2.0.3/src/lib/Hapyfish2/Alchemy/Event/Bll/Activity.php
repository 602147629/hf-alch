<?php

class Hapyfish2_Alchemy_Event_Bll_Activity {

    public static function getActivity() {
        $dal = Hapyfish2_Alchemy_Event_Dal_Activity::getDefaultInstance();
        $all = $dal->getAll();
        return $all;
    }

    public static function getActivityId() {
        $dal = Hapyfish2_Alchemy_Event_Dal_Activity::getDefaultInstance();
        $all = $dal->getAllS();
        return $all;
    }

    /**
     *    获取loading礼包 奖励内容
     * @return type
     */
    public static function getLoadingData() {
        $dal = Hapyfish2_Alchemy_Event_Dal_Activity::getDefaultInstance();
        $all = $dal->getAllLoading();
        return $all;
    }

    public static function updateLoadingData($award) {
        $dal = Hapyfish2_Alchemy_Event_Dal_Activity::getDefaultInstance();
        $all = $dal->updateLoadingGift($award);
        return $all;
    }

    /**
     *   获取每日签到 每日趣闻
     */
    public static function getEveryNotice() {
        $dal = Hapyfish2_Alchemy_Event_Dal_Activity::getDefaultInstance();
        $data = $dal->getEveryNotice();
        return $data;
    }

    /**
     *   修改每日趣闻
     */
    public static function updateEveryNotice($data) {
        $dal = Hapyfish2_Alchemy_Event_Dal_Activity::getDefaultInstance();
        $data = $dal->InsertEveryNotice($data);
    }

    /**
     *  删除每日趣闻
     */
    public static function deleteNotice($id) {
        $dal = Hapyfish2_Alchemy_Event_Dal_Activity::getDefaultInstance();
        $data = $dal->DeleteEveryNotice($id);
    }

    /**
     * 
     */
    public static function getEveryLabaAward() {
        $dal = Hapyfish2_Alchemy_Event_Dal_Activity::getDefaultInstance();
        $data = $dal->getEveryLabaAward();
        return $data;
    }

    public static function updateEveryLaba($data) {
        $dal = Hapyfish2_Alchemy_Event_Dal_Activity::getDefaultInstance();
        $data = $dal->updateEveryLabaAward($data);
        return $data;
    }

    public static function getEveryFiveAndSevenAward() {
        $dal = Hapyfish2_Alchemy_Event_Dal_Activity::getDefaultInstance();
        $data = $dal->getEveryFiveAndSevenAward();
        return $data;
    }
    public static function updateFiveAndSeven($data) {
        $dal = Hapyfish2_Alchemy_Event_Dal_Activity::getDefaultInstance();
        $data = $dal->updateEveryFiveAndSeven($data);
        return $data;
    }
}

?>