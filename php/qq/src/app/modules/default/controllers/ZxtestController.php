<?php

class ZxtestController extends Hapyfish2_Controller_Action_External
{
	
    //清静态数据
	public function clearbascacheAction()
	{
	    $list = Hapyfish2_Alchemy_Cache_MemkeyList::mapBasicMcKey();
	    $localcache = Hapyfish2_Cache_LocalCache::getInstance();
	    $cache = Hapyfish2_Cache_Factory::getBasicMC('mc_0');
	    foreach ($list as $key) {
	        $cache->delete($key);
	        $localcache->delete($key);
	        echo $key;
	        echo '<br/>';
	    }

	    //task relate
	    Hapyfish2_Alchemy_Cache_Basic::loadAllTaskConditionInfo();
	    Hapyfish2_Alchemy_Cache_Basic::loadAllTaskInfo();
		Hapyfish2_Alchemy_Cache_Basic::loadWeaponList();
	    $v = '1.0';
	    @unlink(TEMP_DIR . '/initvo.' . $v . '.cache');
	    @unlink(TEMP_DIR . '/initvo.' . $v . '.cache.zip');

	    @unlink(TEMP_DIR . '/giftvo.' . $v . '.cache');
	    @unlink(TEMP_DIR . '/giftvo.' . $v . '.cache.zip');
	    exit;
	}

	//清静态副本地图数据
    public function clearbasmapcopyAction()
	{
	    $ids = $this->_request->getParam('ids');
	    $aryMapId = explode(',', $ids);
	    foreach ($aryMapId as $mapId) {
	        $data = Hapyfish2_Alchemy_Cache_Basic::loadMapCopyTranscriptList($mapId);
	        $localcache = Hapyfish2_Cache_LocalCache::getInstance();
	        $delKey = Hapyfish2_Alchemy_Cache_MemkeyList::mapBasicMcKey('alchemy:bas:mapcopydetail:') . $mapId;
	        $localcache->delete($delKey);
	        echo '<br/>';
	        echo $mapId.' CLEAR OK! ';
	        echo '<br/>';
	    }

	    Hapyfish2_Alchemy_Cache_Basic::loadMapCopyVerList();
	    exit;
	}

    public function getbasmapcopyAction()
	{
	    $ids = $this->_request->getParam('ids');
	    $aryMapId = explode(',', $ids);
	    foreach ($aryMapId as $mapId) {
	        $data = Hapyfish2_Alchemy_Cache_Basic::getMapCopyTranscriptList($mapId);
	        echo json_encode($data);
	        echo '<br/><br/>';
	    }

	    exit;
	}

	public function refreshmapcopyAction()
	{
        $uid = $this->_request->getParam('uid');
        $mapId = $this->_request->getParam('mapid');
        $upd = (int)$this->_request->getParam('upd');
        $bef = Hapyfish2_Alchemy_Cache_MapCopy::getMapCopySeries($uid);
        echo json_encode($bef);
        echo '<br />';
        if ($upd) {
            $mapSeries = substr($mapId, 0, -2);
    	    $seriesInfo = Hapyfish2_Alchemy_Cache_MapCopy::getMapCopySeriesById($uid, $mapSeries);
    	    $seriesInfo['refreshTm'] = time();
            Hapyfish2_Alchemy_Cache_MapCopy::setMapCopySeriesById($uid, $mapSeries, $seriesInfo);
        }
        $aft = Hapyfish2_Alchemy_Cache_MapCopy::getMapCopySeries($uid);
        echo json_encode($aft);
        echo '<br />';
        exit;
	}

	public function overfightAction()
	{
	    $uid = $this->_request->getParam('uid');
	    $info = Hapyfish2_Alchemy_Cache_Fight::getFightInfo($uid);
	    $info['status'] = 4;
        echo Hapyfish2_Alchemy_Cache_Fight::saveFightInfo($uid, $info, true);
        exit;
	}
	
	public function changeviptimeAction()
	{
		$uid = $this->_request->getParam('uid');
		$starttime = $this->_request->getParam('starttime');
		$endtime = $this->_request->getParam('endtime');
		$vip = new Hapyfish2_Alchemy_Bll_Vip();
		$info = $vip->getVipInfo($uid);
		if($starttime){
			$info['starttime'] = strtotime($starttime);
		}
		if($endtime){
			$info['endtime'] = strtotime($endtime);
		}
		Hapyfish2_Alchemy_Cache_Vip::updateVip($info);
		print_r($info);
		exit;
	}
}