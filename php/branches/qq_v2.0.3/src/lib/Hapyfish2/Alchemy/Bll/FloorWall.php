<?php

class Hapyfish2_Alchemy_Bll_FloorWall
{
	public static function getData($uid, $xSize, $zSize)
	{
		$info = Hapyfish2_Alchemy_HFC_FloorWall::getFloorWall($uid);
		if (!$info) {
			return null;
		}
		
		$floor = array();
		$floorCid = $info['floor'];
        for($i = 0; $i < $xSize; $i++) {
        	for($j = 0; $j < $zSize; $j++) {
        		$floor[$i][$j] = $floorCid;
        	}
        }
        
		$wall = array();
		$wallCid = $info['wall'];
        for($i = 0; $i < 2; $i++) {
        	for($j = 0; $j < $zSize; $j++) {
        		$wall[$i][$j] = $wallCid;
        	}
        }

		return array($floor, $wall);
	}
}