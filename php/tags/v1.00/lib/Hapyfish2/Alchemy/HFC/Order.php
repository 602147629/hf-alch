<?php

class Hapyfish2_Alchemy_HFC_Order
{
	
    public static function getOrderList($uid)
    {
    	$key = 'a:u:orderlist:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dalOrder = Hapyfish2_Alchemy_Dal_Order::getDefaultInstance();
	            $data = $dalOrder->get($uid);
	            if ($data) {
	            	$data = json_decode($data);
                    $cache->add($key, $data);
                } else {
                    return null;
                }
            } catch (Exception $e) {
            }
        }
        
        $orderList = array();
        $nowTime = time();
        if ($data) {
            foreach ($data as $order) {
            	$startTime = $order[3];
            	$outTime = $order[2];
            	$remainTime = $startTime + $outTime - $nowTime;
            	$remainTime = $remainTime < 0 ? 0 : $remainTime;
                $orderList[$order[0]] = array('id' => $order[0],
                					 'needs' => $order[1],			//需求物品列表,[[cid,num],[131,2]]
                					 'outTime' => $order[2],		//NPC要出去的时间
                					 'remainingTime' => $remainTime,//订单开始时间
                					 'state' => $order[4],			//订单状态,1:未接,2:已接,未完成,3:已完成,4:已过期
                					 'awards' => $order[5],			//订单奖励列表,[[cid,num],[131,2]]
                					 'dialog' => $order[6],			//npc对白,开始对白&&完成后对白&&失败后对白
                					 'satisfaction' => $order[7],	//满意度提升值	
				                     'coin' => $order[8],			//奖励金币
				                     'avatarName' => $order[9],		//NPC名字
				                     'avatarFaceClass' => $order[10],//NPC头像
				                     'avatarClassName' => $order[11],//NPC素材
				                     'exp' => $order[12],			//奖励经验
				                     'cid' => $order[13],			//订单类id ->cid
				                     'totalTime' => $order[14],
				                     'startTime' => $order[3],
				                     'awardType' => $order[15]);			
            }
        }
        
        return $orderList;
    }
    
    public static function updateOrderList($uid, $orderList, $savedb = true)
    {
    	$key = 'a:u:orderlist:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        
        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
    
        $data = array();
        foreach ($orderList as $order) {
        	$data[] = array($order['id'], $order['needs'], $order['outTime'], $order['startTime'], 
                			$order['state'], $order['awards'], $order['dialog'], $order['satisfaction'], 
                			$order['coin'], $order['avatarName'], $order['avatarFaceClass'], $order['avatarClassName'], 
                			$order['exp'], $order['cid'], $order['totalTime'], $order['awardType']);
        }
        if ($savedb) {
            $ok = $cache->save($key, $data);
            $info = array('order' => json_encode($data));
            if ($ok) {
                try {
                    $dalOrder = Hapyfish2_Alchemy_Dal_Order::getDefaultInstance();
                    $dalOrder->update($uid, $info);
                } catch (Exception $e) {
                }
            }
            
            return $ok;
        } else {
            return $cache->update($key, $data);
        }
    }
    
    public static function getRequestList($uid)
    {
    	$key = 'a:u:orderreqlist:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	return array();
        }
        
        return $data;
    }
    
    public static function updateRequestList($uid, $list)
    {
        $key = 'a:u:orderreqlist:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $result = $cache->update($key, $list);
        
    	return $result;
    }
        
	/**
	 * 添加临时请求订单
	 * 
	 * @param int $uid
	 * @param array $order
	 */
    public static function requestOne($uid, $order, $id = null)
    {
    	$result = 0;
    	try {
    		if ( $id == null ) {
    			$id = self::getNewOrderId($uid); 
    		}
    		
    		if ($id > 0) {
    			$order['id'] = $id;
    			
    			$list = self::getRequestList($uid);
    			$list[$id] = $order;
    			
    			self::updateRequestList($uid, $list);

	    		$result = $id;
    		}
    	} catch (Exception $e) {
    	}
    	return $result;
    }
    
    /**
     * 读取临时订单
     * 
     * @param int $uid
     * @param int $id
     */
    public static function getRequestOne($uid, $id)
    {
    	$list = self::getRequestList($uid);

    	if ( !isset($list[$id]) ) {
    		return null;
    	}
    	$item = $list[$id];
    	
        $data = array('uid' => $uid,
                      'id' => $id,
                      'needs' => $item['needs'],
                      'outTime' => $item['outTime'],
                      'startTime' => $item['startTime'],
                      'state' => $item['state'],
                      'awards' => $item['awards'],
                      'dialog' => $item['dialog'],
                      'satisfaction' => $item['satisfaction'],
                      'coin' => $item['coin'],
                      'avatarName' => $item['avatarName'],
                      'avatarFaceClass' => $item['avatarFaceClass'],
                      'avatarClassName' => $item['avatarClassName'],
                      'exp' => $item['exp'],
                      'cid' => $item['cid'],
                      'totalTime' => $item['totalTime'],
                      'awardType' => $item['awardType']);
        
        return $data;
    }
    
    public static function delRequestOne($uid, $id)
    {
    	$list = self::getRequestList($uid);
    	if (isset($list[$id])) {
    		unset($list[$id]);
    		$result = self::updateRequestList($uid, $list);
    		return $result;
    	}
    	return false;
    }
    
    public static function addOne($uid, $newOrder, $orderList = null)
    {
    	if ( !$orderList ) {
    		$orderList = self::getOrderList($uid);
    	}
    	
    	$orderList[$newOrder['id']] = $newOrder;
    	
    	return self::updateOrderList($uid, $orderList);
    }
    
    public static function delOne($uid, $id, $orderList = null)
    {
    	if ( !$orderList ) {
    		$orderList = self::getOrderList($uid);
    	}
    	
    	if ( isset($orderList[$id]) ) {
    		unset($orderList[$id]);
    		return self::updateOrderList($uid, $orderList);
    	}
    	return false;
    }
    
    public static function getSatisfaction($uid)
    {
        $key = 'a:u:satisfaction:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->get($key);
        
        if ($data === false) {
            $data = 0;
        }
        return $data;
    }
    
    public static function updateSatisfaction($uid, $change, $satisfaction = null)
    {
    	if ( !$satisfaction ) {
    		$satisfaction = self::getSatisfaction($uid);
    	}
    	$satisfaction = $satisfaction + $change;
    	
    	$satisfaction = $satisfaction > 100 ? 100 : $satisfaction;
    	$satisfaction = $satisfaction < 0 ? 0 : $satisfaction;
    	
        $key = 'a:u:satisfaction:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $cache->update($key, $satisfaction);
        
    	return $satisfaction;
    }
    
    public static function getLastRequestTime($uid)
    {
        $key = 'a:u:lastrequtime:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->get($key);
        
        if ($data === false) {
            $data = 0;
        }
        return $data;
    }
    
    public static function updateLastRequestTime($uid, $time = null)
    {
    	if ( !$time ) {
    		$time = time();
    	}
    	
        $key = 'a:u:lastrequtime:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $cache->update($key, $time);
    	return $time;
    }
    
    public static function getNewOrderId($uid)
    {
        try {
            $dalUserSequence = Hapyfish2_Alchemy_Dal_UserSequence::getDefaultInstance();
            return $dalUserSequence->get($uid, 'i', 1);
        } catch (Exception $e) {
        }
        return 0;
    }
    
    //数据结构，$data = array('10019'=>1,'10020'=>1);
    public static function getOrderFids($uid)
    {
    	$key = 'a:u:orderfids:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	return array();
        }
        
        return $data;
    }
    
    public static function addOrderFids($uid, $fid)
    {
    	$data = self::getOrderFids($uid);
    	$data[$fid] = 1;
    	
    	self::updateOrderFids($uid, $data);
    }
    
    public static function updateOrderFids($uid, $data)
    {
    	$key = 'a:u:orderfids:';
        $key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $cache->update($key, $data);
    	return $data;
    }
    
}