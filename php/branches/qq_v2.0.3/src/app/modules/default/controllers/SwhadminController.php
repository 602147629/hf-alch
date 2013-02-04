<?php

class SwhadminController extends Zend_Controller_Action {

    //Admin Username & Password
    private $_admins = array('majun' => 'junma',
    );
    private $_curAdmin;

    public function init() {
        $loginU = $_SERVER['PHP_AUTH_USER'];
        $loginP = $_SERVER['PHP_AUTH_PW'];
        if (!isset($loginU) || !isset($loginP)
                || !array_key_exists($loginU, $this->_admins) || $this->_admins[$loginU] != $loginP) {
            Header("WWW-Authenticate: Basic realm=Happy magic admin, Please Login");
            Header("HTTP/1.0 401 Unauthorized");

            echo <<<EOB
				<html><body>
				<h1>Rejected!</h1>
				<big>Wrong Username or Password!</big>
				</body></html>
EOB;
            exit;
        }

        $appInfo = Hapyfish2_Project_Bll_AppInfo::getAdvanceInfo();
        $this->view->appTitle = $appInfo['app_title'];
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
        $this->view->appId = APP_ID;
        $this->view->appKey = APP_KEY;
        $this->view->adminName = $loginU;
        $this->_curAdmin = $loginU;
    }

    protected function echoResult($data) {
        header("Cache-Control: no-store, no-cache, must-revalidate");
        echo json_encode($data);
        exit();
    }

    function indexAction() {
        $this->render();
    }

    function timegiftAction() {
        $zhanshi = array();
        $gongshou = array();
        $fashi = array();
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $list = $dal->getTimeGift();
        foreach ($list as $k => $v) {
            if ($v['type'] == 1) {
                $zhanshi[] = $v;
            } else if ($v['type'] == 2) {
                $gongshou[] = $v;
            } else if ($v['type'] == 3) {
                $fashi[] = $v;
            }
        }
        $this->view->zhanshi = $zhanshi;
        $this->view->gongshou = $gongshou;
        $this->view->fashi = $fashi;
        $this->render();
    }

    function addtAction() {
        $data['id'] = $this->_request->getParam('id');
        $data['next_id'] = $this->_request->getParam('next_id');
        $data['time'] = $this->_request->getParam('time');
        $data['list'] = $this->_request->getParam('list');
        $data['type'] = $this->_request->getParam('type');
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $dal->insertTgift($data);
        exit;
    }

    function deletetAction() {
        $data['id'] = $this->_request->getParam('id');
        $data['type'] = $this->_request->getParam('type');
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $dal->deleteg($data);
        exit;
    }

    function actitivtydeletesAction() {
        $data['id'] = $this->_request->getParam('id');
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $dal->deleteactitivty($data);
    }

    function levelgiftAction() {
        $zhanshi = array();
        $gongshou = array();
        $fashi = array();
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $list = $dal->getLevelGift();
        foreach ($list as $k => $v) {
            if ($v['type'] == 1) {
                $zhanshi[] = $v;
            } else if ($v['type'] == 2) {
                $gongshou[] = $v;
            } else if ($v['type'] == 3) {
                $fashi[] = $v;
            }
        }
        $this->view->zhanshi = $zhanshi;
        $this->view->gongshou = $gongshou;
        $this->view->fashi = $fashi;
        $this->render();
    }

    function addlAction() {
        $data['level'] = $this->_request->getParam('id');
        $data['nextLevel'] = $this->_request->getParam('next_id');
        $data['awards'] = $this->_request->getParam('list');
        $data['type'] = $this->_request->getParam('type');
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $dal->insertLgift($data);
        exit;
    }

    function deletelAction() {
        $data['level'] = $this->_request->getParam('id');
        $data['type'] = $this->_request->getParam('type');
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $dal->deletel($data);
        exit;
    }

    function sevengiftAction() {
        $zhanshi = array();
        $gongshou = array();
        $fashi = array();
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $list = $dal->getSevenGift();
        foreach ($list as $k => $v) {
            if ($v['type'] == 1) {
                $zhanshi[] = $v;
            } else if ($v['type'] == 2) {
                $gongshou[] = $v;
            } else if ($v['type'] == 3) {
                $fashi[] = $v;
            }
        }
        $this->view->zhanshi = $zhanshi;
        $this->view->gongshou = $gongshou;
        $this->view->fashi = $fashi;
        $this->render();
    }

    function addsAction() {
        $data['day'] = $this->_request->getParam('id');
        $data['awards'] = $this->_request->getParam('list');
        $data['type'] = $this->_request->getParam('type');
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $dal->insertSgift($data);
        exit;
    }

    function deletesAction() {
        $data['day'] = $this->_request->getParam('id');
        $data['type'] = $this->_request->getParam('type');
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $dal->deletes($data);
        exit;
    }

    function packageAction() {
        $zhanshi = array();
        $gongshou = array();
        $fashi = array();
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $list = $dal->getpackage();
        foreach ($list as $k => $v) {
            if ($v['type'] == 1) {
                $zhanshi[] = $v;
            } else if ($v['type'] == 2) {
                $gongshou[] = $v;
            } else if ($v['type'] == 3) {
                $fashi[] = $v;
            }
        }
        $this->view->zhanshi = $zhanshi;
        $this->view->gongshou = $gongshou;
        $this->view->fashi = $fashi;
        $this->render();
    }

    function addpAction() {
        $data['cid'] = $this->_request->getParam('cid');
        $data['awards'] = $this->_request->getParam('awards');
        $data['type'] = $this->_request->getParam('type');
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $dal->insertpackage($data);
        exit;
    }

    function deletepAction() {
        $cid = $this->_request->getParam('cid');
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $dal->deletep($cid);
        exit;
    }

    function daliytaskAction() {
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $list = $dal->getdaliyactivity();
        $aclist = $dal->getactivityaward();
        $this->view->list = $list;
        $this->view->aclist = $aclist;
        $this->render();
    }

    function adddAction() {
        $data['tid'] = $this->_request->getParam('tid');
        $data['activity'] = $this->_request->getParam('activity');
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $dal->insertDaliyTask($data);
        exit;
    }

    function deletedAction() {
        $tid = $this->_request->getParam('tid');
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $dal->deleted($tid);
        exit;
    }

    function deleteaAction() {
        $id = $this->_request->getParam('id');
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $dal->deletea($id);
        exit;
    }

    function addaAction() {
        $data['awards'] = $this->_request->getParam('awards');
        $data['activity'] = $this->_request->getParam('ac');
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $dal->insertActivityAwards($data);
        exit;
    }

    function updateaAction() {
        $data['id'] = $this->_request->getParam('id');
        $data['awards'] = $this->_request->getParam('awards');
        $data['activity'] = $this->_request->getParam('activity');
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $dal->updateActivityAwards($data);
        exit;
    }

    function updatetocacheAction() {
        $type = $this->_request->getParam('type');
        $cache = Hapyfish2_Alchemy_Cache_Activity::getBasicMC();
        if ($type == 'activity') {
            $key = 'a:u:activity:';
            $key1 = 'a:u:activity:award:';
            $cache->delete($key);
            $cache->delete($key1);
        } else if ($type == 'package') {
            $key = 'alchemy:bas:package:';
            $cache->delete($key);
        }
        echo "ok";
        exit;
    }

    function activityAction() {
        $list = Hapyfish2_Alchemy_Event_Bll_Activity::getActivity(); //获取所有活动列表
        $this->view->list = $list;
        $this->render();
    }

    function activitycdkeyAction() {
        $list = Hapyfish2_Alchemy_Event_Bll_Activity::getActivityId(); //获取所有活动列表
        $this->view->list = $list;
        $this->render();
    }

    function actitivtycdkeysaddsAction() {
        $data['id'] = $this->_request->getParam('id');
        $data['number'] = $this->_request->getParam('number');
        $data['times'] = $this->_request->getParam('times');
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $dal->insertActivityCdKeys($data);
        die;
    }

    function actitivtyaddsAction() {
        $data['id'] = $this->_request->getParam('id');
        $data['list'] = $this->_request->getParam('list');
        $data['type'] = $this->_request->getParam('type');
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $dal->insertActivityGift($data);
    }

    function guildAction() {
        $list = Hapyfish2_Alchemy_Event_Bll_Guild::getMember();
        $this->view->list = $list;
        $this->render();
    }

    function addguildAction() {
        $uid = $this->_request->getParam('uid');
        $data['uid'] = $uid;
        $data['login'] = 0;
        $data['pay'] = 0;
        $data['invite'] = 0;
        $data['introduce'] = 0;
        $data['total'] = 0;
        Hapyfish2_Alchemy_Event_Bll_Guild::updateOne($uid, $data);
        exit;
    }

    function delguildAction() {
        $uid = $this->_request->getParam('uid');
        $dal = Hapyfish2_Alchemy_Event_Dal_Guild::getDefaultInstance();
        $dal->delete($uid);
    }

    function addpointAction() {
        $uid = $this->_request->getParam('uid');
        $num = $this->_request->getParam('num');
        Hapyfish2_Alchemy_Event_Bll_Guild::addIntroducePoint($uid, $num);
        exit;
    }

    function dmAction() {
        $dmConfig = Hapyfish2_Alchemy_Cache_Dm::getDmConfig();
        $this->view->config = $dmConfig;
        $this->render();
    }

    function updatedmAction() {
        $data['id'] = $this->_request->getParam('id');
        $data['dm'] = $this->_request->getParam('dm');
        $data['btn'] = $this->_request->getParam('btn');
        Hapyfish2_Alchemy_Cache_Dm::updateDmConfig($data);
        $key = 'a:u:config:dm';
        $cache = Hapyfish2_Alchemy_Cache_Activity::getBasicMC();
        $cache->delete($key);

        exit;
    }

    function loadinggiftAction() {
        $list = Hapyfish2_Alchemy_Event_Bll_Activity::getLoadingData(); //获取所有活动列表
        $this->view->list = $list;
        $this->render();
    }

    function loadingaddsAction() {
        $award = $this->_request->getParam('award');
        Hapyfish2_Alchemy_Event_Bll_Activity::updateLoadingData($award);
    }

    function everysignnoticeAction() {
        $list = Hapyfish2_Alchemy_Event_Bll_Activity::getEveryNotice(); //获取每日签到趣闻
        $this->view->list = $list;
        $this->render();
    }

    function everysignnoticeaddAction() {
        $data['type'] = $this->_request->getParam('type');
        $data['sign_notice_id'] = $this->_request->getParam('id');
        $data['sign_notice'] = $this->_request->getParam('list');
        $list = Hapyfish2_Alchemy_Event_Bll_Activity::updateEveryNotice($data);
        die;
    }

    function everynoicedeletesAction() {
        $id = $this->_request->getParam('id');
        $dal = Hapyfish2_Alchemy_Event_Bll_Activity::deleteNotice($id);
        die;
    }

    function everysignnoticerest() {
        $key = 'a:u:everynotice';
        $cache = Hapyfish2_Cache_Factory::getHFC(0);
        $cache->delete($key);
        die;
    }

    /**
     *   每日签到 拉霸奖励
     */
    function everysignlabaAction() {
        $list = Hapyfish2_Alchemy_Event_Bll_Activity::getEveryLabaAward(); //获取每日签到趣闻
        $this->view->list = $list;
        $this->render();
    }

    /**
     *   每日签到 拉霸奖励修改
     */
    function everysignawardaddAction() {
        $data['day'] = $this->_request->getParam('id');
        $data['normal_award'] = $this->_request->getParam('normal_award');
        $data['vip_frist_award'] = $this->_request->getParam('vip_frist_award');
        $data['vip_second_award'] = $this->_request->getParam('vip_frist_award');
        $list = Hapyfish2_Alchemy_Event_Bll_Activity::updateEveryLaba($data);
        die;
    }

    /**
     *   每日签到 拉霸奖励  缓存
     */
    function everysignlabarest() {
        $key = 'a:u:everylabaaward';
        $cache = Hapyfish2_Cache_Factory::getHFC(0);
        $cache->delete($key);
        die;
    }

    function everysignfiveandsevenAction() {
        $list = Hapyfish2_Alchemy_Event_Bll_Activity::getEveryFiveAndSevenAward(); //获取每日签到趣闻
        $this->view->list = $list;
        $this->render();
    }

    function everysignfiveandsevenawardaddAction() {
        $data['id'] = $this->_request->getParam('id');
        $data['award'] = $this->_request->getParam('award');
        $list = Hapyfish2_Alchemy_Event_Bll_Activity::updateFiveAndSeven($data);
        die;
    }

    function everysignfiveandsevenrest() {
        $key = 'a:u:everyactiveaward';
        $cache = Hapyfish2_Cache_Factory::getHFC(0);
        $cache->delete($key);
        die;
    }

}
