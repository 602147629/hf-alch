<?php

class Hapyfish2_Alchemy_Bll_WorldMap
{


    public static function getOpenWorldMap($uid, &$hasNew)
	{
        $maps = Hapyfish2_Alchemy_Cache_WorldMap::getInfo($uid);
        $mapVo = array();
        foreach ($maps as $key=>$data) {
            $mapVo[] = array(
            	'cid' => (int)$data[0],
            	'newUnlock' => (int)$data[1],
            	'newOpen' => (int)$data[2]
            );
            if ((int)$data[2] == 0) {
                $hasNew = true;
            }
        }
        return $mapVo;
	}

	/*
	 * 解锁世界地图
	 */
    public static function setWorldMapOpened($uid, $mapId)
	{
	    if ($mapId<100) {
	        return false;
	    }

	    $worldMapCid = (floor($mapId/100))*100;
	    $maps = Hapyfish2_Alchemy_Cache_WorldMap::getInfo($uid);
	    $selWm = -1;
	    foreach ($maps as $key=>$data) {
            if ($worldMapCid == $data[0]) {
                //already opened
                return false;
            }
	    }

	    if (-1 == $selWm) {
	        $maps[] = array($worldMapCid, 1, 0);
	        Hapyfish2_Alchemy_Cache_WorldMap::saveInfo($uid, $maps);
            Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'newWorldMap', $worldMapCid);
            
            //佣兵派驻打工 地点解锁,$id=$mapId
            Hapyfish2_Alchemy_Bll_MercenaryWork::setWorkOpened($uid, $mapId);
            
            //王城中酒馆
            if ( $mapId == 200 ) {
	            Hapyfish2_Alchemy_Bll_Mercenary::setHireOpened($uid, 3);
	            Hapyfish2_Alchemy_Bll_Mercenary::setHireOpened($uid, 4);
            }
            else if ( $mapId == '第三个酒馆' ) {
	            Hapyfish2_Alchemy_Bll_Mercenary::setHireOpened($uid, 5);
	            Hapyfish2_Alchemy_Bll_Mercenary::setHireOpened($uid, 6);
            }
	    }

	    return true;
	}

	public static function setWorldMapEntered($uid, $mapId)
	{
	    if ($mapId<100) {
	        return false;
	    }

	    $worldMapCid = (floor($mapId/100))*100;
	    $maps = Hapyfish2_Alchemy_Cache_WorldMap::getInfo($uid);
	    $selWm = -1;
	    foreach ($maps as $key=>$data) {
            if ($worldMapCid == $data[0] && empty($data[2])) {
                $selWm = $key;
            }
	    }

	    if (-1 != $selWm) {
	        $maps[$selWm][2] = 1;
	        Hapyfish2_Alchemy_Cache_WorldMap::saveInfo($uid, $maps);
	    }

	    Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'newWorldMap', 0);
	    return true;
	}

    public static function setWorldMapCleared($uid, $mapId)
	{
	    if ($mapId<100) {
	        return false;
	    }

	    $worldMapCid = (floor($mapId/100))*100;
	    $maps = Hapyfish2_Alchemy_Cache_WorldMap::getInfo($uid);
	    $selWm = -1;
	    foreach ($maps as $key=>$data) {
            if ($worldMapCid == $data[0] && $data[2] != 2) {
                $selWm = $key;
            }
	    }

	    if (-1 != $selWm) {
	        $maps[$selWm][2] = 2;
	        Hapyfish2_Alchemy_Cache_WorldMap::saveInfo($uid, $maps);
	    }

	    return true;
	}

	public static function costWorldMapEnterSp($uid, $mapId)
	{
	    $wldMap = Hapyfish2_Alchemy_Cache_Basic::getWorldMapList();
        $needSp = 0;
        foreach ($wldMap as $data) {
            if ($data['enter_scene'] == $mapId) {
                $needSp = $data['need_sp'];
                break;
            }
        }
        if ($needSp) {
            //sp cost
            $costRst = Hapyfish2_Alchemy_HFC_User::decUserSp($uid, $needSp);
            if (!$costRst) {
                return false;//sp not enough
            }
        }
        return true;
	}
}