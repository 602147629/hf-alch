<?php

class Hapyfish2_Alchemy_Bll_Scene
{
    public static $homeSceneId = 1;
    public static $vilaSceneId = 2;

	public static function getRoomData($uid, $fid = null)
	{
		if (empty($fid)) {
			$fid = $uid;
		}

		$decorData = Hapyfish2_Alchemy_HFC_Decor::getScene($fid);
		$furnaceData = Hapyfish2_Alchemy_HFC_Furnace::getOnRoom($fid);
		$decorList = array();

		//临时送一个门，测试用
		if (empty($decorData)) {
			//放入场景中
            $info = array(
                'cid' => 1000555,
                'x' => 3,
                'z' => 0,
            	'm' => 1
            );
			Hapyfish2_Alchemy_HFC_Decor::addScene($fid, $info);
			$decorData = Hapyfish2_Alchemy_HFC_Decor::getScene($fid);
		}
		
		if (!empty($decorData)) {
			foreach ($decorData as $d) {
				$id = $d['id'] . substr($d['cid'], -2);
				$decorList[] = array(
					'id' => (int)$id,
					'cid' => $d['cid'],
					'x' => $d['x'],
					'z' => $d['z'],
					'mirror' => $d['m']
				);
			}
		}

		if (!empty($furnaceData) && isset($furnaceData['furnaces'])) {
			foreach ($furnaceData['furnaces'] as $d) {
				$id = $d['id'] . substr($d['furnace_id'], -2);
				$decorList[] = array(
					'id' => (int)$id,
					'cid' => $d['furnace_id'],
					'x' => $d['x'],
					'z' => $d['z'],
					'mirror' => $d['m']
				);
			}
		}

		//get user info
        $userVo = Hapyfish2_Alchemy_Bll_User::getUserInit($fid);

        $floorAndWall = Hapyfish2_Alchemy_Bll_FloorWall::getData($fid, $userVo['tileX'], $userVo['tileZ']);

		$scene = array(
			'sceneId' => self::$homeSceneId,
			'user' => $userVo,
			'decorList' => $decorList,
			'floorList' => $floorAndWall != null ? $floorAndWall[0] : array(),
			'wallList' => $floorAndWall != null ? $floorAndWall[1] : array(),
		);

		return $scene;
	}


    public static function goHomeScene($uid, $fid = null)
	{

	    $sceneVo = self::getRoomData($uid, $fid);

        $fid = empty($fid) ? $uid : $fid;
        //change current scene to home
        if ($uid == $fid) {
            if ($sceneVo['user']['currentSceneId'] != self::$homeSceneId) {
                $usrScene = Hapyfish2_Alchemy_HFC_User::getUserScene($uid);
        		$usrScene['cur_scene_id'] = self::$homeSceneId;
        		Hapyfish2_Alchemy_HFC_User::updateUserScene($uid, $usrScene, true);
            }
        }
	
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'scene', $sceneVo);

        //临时剧情
        //$storyVo = Hapyfish2_Alchemy_Bll_Story::startStory($uid, 1);
        //$resultVo['story'] = $storyVo;

        return 1;
	}
}