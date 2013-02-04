<?php

class Hapyfish2_Alchemy_Bll_Diy
{
	//从背包中放入场景中
	public static function add($uid, $data)
	{
		$cid = $data['cid'];
		//判断类型41:炼金炉, 5x:装饰
		$type = (int)substr($cid, -2);
		$subType = (int)substr($cid, -2, 1);
		$cid = (int)$cid;

		if ($subType == 5) {
			//先从背包中扣除已有物品
			$ok = Hapyfish2_Alchemy_HFC_Decor::useBag($uid, $cid, 1);

			if ($ok) {
				if ($type == 51) { //地板
					$floorAndWall = Hapyfish2_Alchemy_HFC_FloorWall::getFloorWall($uid);
					$oldFloor = $floorAndWall['floor'];
					$floorAndWall['floor'] = (int)$cid;
					$ok2 = Hapyfish2_Alchemy_HFC_FloorWall::updateFloorWall($uid, $floorAndWall);
					if (!$ok2) {
						//回退
						Hapyfish2_Alchemy_HFC_Decor::addBag($uid, $cid, 1);
						return -200;
					}
					Hapyfish2_Alchemy_HFC_Decor::addBag($uid, $oldFloor, 1);
					return 1;
				} else if ($type == 52) { //墙纸
					$floorAndWall = Hapyfish2_Alchemy_HFC_FloorWall::getFloorWall($uid);
					$oldWall = $floorAndWall['wall'];
					$floorAndWall['wall'] = (int)$cid;
					$ok2 = Hapyfish2_Alchemy_HFC_FloorWall::updateFloorWall($uid, $floorAndWall);
					if (!$ok2) {
						//回退
						Hapyfish2_Alchemy_HFC_Decor::addBag($uid, $cid, 1);
						return -200;
					}
					Hapyfish2_Alchemy_HFC_Decor::addBag($uid, $oldWall, 1);
					return 1;
				} else {
					//放入场景中
		            $info = array(
		                'cid' => $cid,
		                'x' => $data['x'],
		                'z' => $data['z'],
		            	'm' => $data['m']
		            );
					$ok2 = Hapyfish2_Alchemy_HFC_Decor::addScene($uid, $info);
					if (!$ok2) {
						//回退
						Hapyfish2_Alchemy_HFC_Decor::addBag($uid, $cid, 1);
						return -200;
					}
					$newId = (int)($info['id'] . $type);
					Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'id', $newId);
					return 1;
				}
			}
		} else if ($subType == 4) {
			$item = Hapyfish2_Alchemy_HFC_Furnace::getBagByCid($uid, $cid);
			if (!$item) {
				return -200;
			}

			$oldStatus = $item['status'];

			$id = $item['id'];
			$item['x'] = $data['x'];
			$item['z'] = $data['z'];
			$item['m'] = $data['m'];
			$item['status'] = 1;
            $ok = Hapyfish2_Alchemy_HFC_Furnace::updateOne($uid, $id, $item);

            if ($ok) {
            	if ( $oldStatus == 0 ) {
	    			$removeItems = array($cid, 1);
					Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeItems', array($removeItems));
            	}

            	$newId = (int)($id . $type);
            	Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'id', $newId);

                //触发任务处理
                $event = array('uid' => $uid, 'data' => array($cid=>1));
                Hapyfish2_Alchemy_Bll_TaskMonitor::diyFurnace($event);

            	return 1;
            }
		}

		return -200;
	}

	//从场景中放入背包
	public static function remove($uid, $id)
	{
		//判断类型41:炼金炉, 5x:装饰
		$type = (int)substr($id, -2, 1);
		$id = (int)substr($id, 0, -2);

		if ($type == 5) {
			$decor = Hapyfish2_Alchemy_HFC_Decor::getScene($uid);
			if (empty($decor) || !isset($decor[$id])) {
				return -200;
			}

			$cid = $decor[$id]['cid'];

			//先放入背包中
			$ok = Hapyfish2_Alchemy_HFC_Decor::addBag($uid, $cid, 1);

			if ($ok) {
				//从场景中移除
				$ok2 = Hapyfish2_Alchemy_HFC_Decor::removeScene($uid, $id);
				if (!$ok2) {
					//回退
					Hapyfish2_Alchemy_HFC_Decor::useBag($uid, $cid, 1);
					return -200;
				}
				return 1;
			}
		} else if ($type == 4) {
			$item = Hapyfish2_Alchemy_HFC_Furnace::getOne($uid, $id);
			if (!$item) {
				return -200;
			}
			$oldStatus = $item['status'];

			$item['x'] = 0;
			$item['z'] = 0;
			$item['m'] = 0;
			$item['cid'] = 0;
			$item['start_time'] = 0;
			$item['need_time'] = 0;
			$item['cur_probability'] = 0;
			$item['num'] = 0;
			$item['status'] = 0;

            $ok = Hapyfish2_Alchemy_HFC_Furnace::updateOne($uid, $id, $item);

            if ($ok) {
            	if ( $oldStatus == 1 ) {
		    		$addItem = array($item['furnace_id'], 1);
		    		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'addItems', array($addItem));
            	}
            	return 1;
            }
		}

		return -200;
	}

	//在场景中移动位置或改变方向
	public static function edit($uid, $id, $data)
	{
		//判断类型41:炼金炉, 5x:装饰
		$type = (int)substr($id, -2, 1);
		$id = (int)substr($id, 0, -2);

		if ($type == 5) {
			$decor = Hapyfish2_Alchemy_HFC_Decor::getScene($uid);
			if (empty($decor) || !isset($decor[$id])) {
				return -200;
			}

		    $moved = false;
			if ($decor[$id]['x'] != $data['x'] || $decor[$id]['z'] != $data['z']) {
			    $moved = true;
			}

			if (isset($data['x'])) {
				$decor[$id]['x'] = $data['x'];
			}
			if (isset($data['z'])) {
				$decor[$id]['z'] = $data['z'];
			}
			if (isset($data['m'])) {
				$decor[$id]['m'] = $data['m'];
			}

			$ok = Hapyfish2_Alchemy_HFC_Decor::updateScene($uid, $decor);

		    if ($ok) {
		        //触发任务处理
                if ($moved) {
                    $event = array('uid' => $uid, 'data' => 1);
                    Hapyfish2_Alchemy_Bll_TaskMonitor::moveDecor($event);
                }
            	return 1;
            }
		} else if ($type == 4) {
			$item = Hapyfish2_Alchemy_HFC_Furnace::getOne($uid, $id);
			if (!$item) {
				return -200;
			}

		    $moved = false;
			if ($item['x'] != $data['x'] || $item['z'] != $data['z']) {
			    $moved = true;
			}

			if (isset($data['x'])) {
				$item['x'] = $data['x'];
			}
			if (isset($data['z'])) {
				$item['z'] = $data['z'];
			}
			if (isset($data['m'])) {
				$item['m'] = $data['m'];
			}

            $ok = Hapyfish2_Alchemy_HFC_Furnace::updateOne($uid, $id, $item);

            if ($ok) {
                //触发任务处理
                if ($moved) {
                    $event = array('uid' => $uid, 'data' => 1);
                    Hapyfish2_Alchemy_Bll_TaskMonitor::moveDecor($event);
                }
            	return 1;
            }
		}

		return -200;
	}

}