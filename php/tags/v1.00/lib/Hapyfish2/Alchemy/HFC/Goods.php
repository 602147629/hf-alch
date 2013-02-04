<?php

class Hapyfish2_Alchemy_HFC_Goods
{
    //物品,type:1
	public static function getUserGoods($uid)
    {
        $key = 'a:u:goods:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dalGoods = Hapyfish2_Alchemy_Dal_Goods::getDefaultInstance();
	            $result = $dalGoods->get($uid);
	            if ($result) {
                    $data = array();
                    foreach ($result as $cid => $count) {
                        $data[$cid] = array($count, 0);
                    }
                    $cache->add($key, $data);
                } else {
                    return null;
                }
            } catch (Exception $e) {
                info_log('[Hapyfish2_Alchemy_HFC_Goods::getUserGoods:'. $uid. ']' . $e->getMessage(), 'db.err');
            }
        }

        $goods = array();
        if ( is_array($data) ) {
            foreach ($data as $cid => $item) {
                $goods[$cid] = array('count' => $item[0], 'update' => $item[1]);
            }
        }

        return $goods;
    }

    public static function updateUserGoods($uid, $goods, $savedb = true)
    {
        $key = 'a:u:goods:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }

        if ($savedb) {
            $data = array();
            foreach ($goods as $cid => $item) {
                $data[$cid] = array($item['count'], 0);
            }
            $ok = $cache->save($key, $data);
            if ($ok) {
                try {
                    $dalGoods = Hapyfish2_Alchemy_Dal_Goods::getDefaultInstance();
                    foreach ($goods as $cid => $item) {
                        if ($item['update']) {
                            $dalGoods->update($uid, $cid, $item['count']);
                        }
                    }
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_Goods::updateUserGoods:'. $uid. ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            $data = array();
            foreach ($goods as $cid => $item) {
                $data[$cid] = array($item['count'], $item['update']);
            }
            return $cache->update($key, $data);
        }
    }

    public static function addUserGoods($uid, $cid, $count = 1, $goods = null)
    {
        //check if gained this unique item
        if (substr($cid, -2) == '16') {
            $uniqueItems = Hapyfish2_Alchemy_Cache_UniqueItem::getInfo($uid);
            //had already gained
            if (in_array($cid, $uniqueItems)) {
                return false;
            }

            $count = 1;
            $uniqueItems[] = (int)$cid;
            $saved = Hapyfish2_Alchemy_Cache_UniqueItem::saveInfo($uid, $uniqueItems);
            if ($saved) {
                $addItem = array((int)$cid, $count);
                //Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'showNewItem', array($addItem));
            }
        }

        if (!$goods) {
            $goods = self::getUserGoods($uid);
            /*if ($goods === null) {
                return false;
            }*/
        }

        $cid = (int)$cid;
        $count = (int)$count;

        if (isset($goods[$cid])) {
            $goods[$cid]['count'] += $count;
            $goods[$cid]['update'] = 1;
        } else {
            $goods[$cid] = array('count' => $count, 'update' => 1);
        }

        $ok = self::updateUserGoods($uid, $goods);
        if ($ok) {
    		$addItem = array($cid, $count);
    		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'addItems', array($addItem));

			//添加图鉴
			Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $cid);
        }
        return $ok;
    }

    public static function useUserGoods($uid, $cid, $count = 1, $goods = null)
    {
        if (!$goods) {
            $goods = self::getUserGoods($uid);
            if ($goods === null) {
                return false;
            }
        }

        $cid = (int)$cid;
        $count = (int)$count;

        if (!isset($goods[$cid]) || $goods[$cid]['count'] < $count) {
            return false;
        } else {
            $goods[$cid]['count'] -= $count;
            $goods[$cid]['update'] = 1;

            $ok = self::updateUserGoods($uid, $goods, true);
    		if ($ok) {
    			$removeItems = array($cid, $count);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeItems', array($removeItems));
				//触发任务处理
                $event = array('uid' => $uid, 'data' => array($cid=>$count));
				Hapyfish2_Alchemy_Bll_TaskMonitor::useItem($event);
				
				$cardInfo = Hapyfish2_Alchemy_Cache_Basic::getGoodsInfo($cid);
				for ( $i=0;$i<$count;$i++ ) {
					//report log,统计玩家使用道具分布
					$logger = Hapyfish2_Util_Log::getInstance();
					$logger->report('321', array($uid, $cid, $cardInfo['name']));
				}
    		}
    		return $ok;
        }
    }
    
 /*   public static function getUserAuto($uid)
    {
    	$key = 'a:u:goods:auto:'.$uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$data = $cache->get($key);
    	if($data === false){
    		return array();
    	}
    	return $data;
    }
	//type:0关闭自动， 1开启
    public static function updateUserAuto($uid, $id, $type)
    {
    	$info = Hapyfish2_Alchemy_Cache_Basic::getGoodsInfo($id);
    	if($info['auto'] != 1){
    		return -200;
    	}
    	$list = self::getUserAuto($uid);
    	$key = array_search($id, $list);
    	if($type == 0){
    		if($key){
    			unset($list[$key]);
    		}else{
    			return 1;
    		}
    	}else{
    		if($key){
    			return 1;
    		}else{
    			$list[] = $id;
    		}
    	}
    	$key = 'a:u:goods:auto:'.$uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$cache->set($key, $list);
    	return 1;
    }*/
    
    public static function getUserPond($uid)
    {
    	$key = 'a:u:goods:pond:'.$uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$data = $cache->get($key);
    	if($data === false){
    		return array(
    			'hp'=>0,
    			'mp'=>0
    		);
    	}
    	return $data;
    }
    
    public static function updateUserPond($uid, $pond)
    {
    	$key = 'a:u:goods:pond:'.$uid;
    	$cache = Hapyfish2_Cache_Factory::getHFC($uid);
    	$cache->update($key, $pond);
    }
    
    public static function getBloodVo($uid)
    {
    	$list = self::getUserPond($uid);
    	$data = array(
    		array(
    			'type'=>1,
    			'num'=>$list['hp']
    		),
    		array(
    			'type'=>2,
    			'num'=>$list['mp']
    		),
    	);
    	return $data;
    }
    
}