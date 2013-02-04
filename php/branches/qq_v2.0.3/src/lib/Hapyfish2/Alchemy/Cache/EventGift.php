<?php

class Hapyfish2_Alchemy_Cache_EventGift {

    protected static $_cacheKeyPrex = 'alchemy:bas:';

    public static function getKey($word) {
        return self::$_cacheKeyPrex . $word;
    }

    public static function getBasicMC() {
        $key = 'mc_0';
        return Hapyfish2_Cache_Factory::getBasicMC($key);
    }

    public static function getTimeGift($type) {
        $list = array();
        $key = 'timeGift';
        $key = self::getKey($key);
        $cache = self::getBasicMC();
        $data = $cache->get($key);
        if ($data == false) {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $data = $dal->getTimeGift();
            if ($data) {
                foreach ($data as $t => &$info) {
                    $info['list'] = json_decode($info['list']);
                }
                $cache->set($key, $data);
            } else {
                return null;
            }
        }
        if ($data) {
            foreach ($data as $v) {
                if ($v['type'] == $type) {
                    $list[] = $v;
                }
            }
        }
        return $list;
    }

    public static function getTGDetail($type, $id) {
        $list = self::getTimeGift($type);
        foreach ($list as $v) {
            if ($v['id'] == $id) {
                return $v;
            }
        }
        return null;
    }

    public static function getUserTGift($uid) {
        $key = 'a:u:timeGift:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data === false) {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $info = $dal->getUserEvent($uid, 1);
            if ($info) {
                $data['id'] = $info;
                $data['end'] = 0;
            } else {
                $data['id'] = 0;
                $data['end'] = 0;
            }
            $cache->set($key, $data);
        }
        return $data;
    }

    public static function updateUserTGift($uid, $userGift) {
        $key = 'a:u:timeGift:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, $userGift);
        self::updateEventGift($uid, $userGift['id'], 1);
    }

    public static function getSevenGift($type) {
        $key = 'sevenGift';
        $key = self::getKey($key);
        $cache = self::getBasicMC();
        $data = $cache->get($key);
        $list = array();
        if ($data == false) {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $data = $dal->getSevenGift();
            if ($data) {
                foreach ($data as $id => &$info) {
                    $info['awards'] = json_decode($info['awards']);
                }
                $cache->set($key, $data);
            } else {
                return null;
            }
        }
        if ($data) {
            foreach ($data as $v) {
                if ($type == $v['type']) {
                    $list[] = $v;
                }
            }
        }
        return $list;
    }

    public static function getLevelGift($type) {
        $list = array();
        $key = 'levelGift';
        $key = self::getKey($key);
        $cache = self::getBasicMC();
        $data = $cache->get($key);
        if ($data == false) {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $data = $dal->getLevelGift();
            if ($data) {
                foreach ($data as $t => &$info) {
                    $info['awards'] = json_decode($info['awards']);
                }
                $cache->set($key, $data);
            } else {
                return null;
            }
        }
        if ($data) {
            foreach ($data as $v) {
                if ($v['type'] == $type) {
                    $list[] = $v;
                }
            }
        }
        return $list;
    }

	public static function getYellowGift($type)
	{
		$list = array();
		$key = 'yellowGift';
		$key = self::getKey($key);
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		if($data == false){
			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
			$data = $dal->getYellowGift();
			if($data){
				foreach($data as $t=>&$info){
					$info['awards'] = json_decode($info['awards']);
				}
				$cache->set($key, $data);
			}else{
				return null;
			}
		}
		if($data){
			foreach($data as $v){
				if($v['type'] == $type){
					$list[] = $v;
				}
			}
		}
		return $list;
	}
	
    public static function getUserSGift($uid) {
        $key = 'a:u:sevenGift:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data === false) {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $info = $dal->getUserEvent($uid, 2);
            $date = date('Ymd');
            if ($info) {
                $data['id'] = $info;
                $data['date'] = $date;
            } else {
                $data['id'] = 0;
                $data['date'] = 0;
            }
            $cache->set($key, $data);
        }
        return $data;
    }

    public static function getSGDetail($type, $id) {
        $list = self::getSevenGift($type);
        if ($list) {
            foreach ($list as $v) {
                if ($v['day'] == $id) {
                    return $v;
                }
            }
        }
        return null;
    }

    public static function updateUserSGift($uid, $userGift) {
        $key = 'a:u:sevenGift:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, $userGift);
        self::updateEventGift($uid, $userGift['id'], 2);
    }

    public static function updateEventGift($uid, $id, $type) {
        try {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $dal->updateEventGift($uid, $id, $type);
        } catch (Exception $e) {
            return "fatal error:" . $e->getMessage();
        }
    }

    public static function getUserLGift($uid) {
        $key = 'a:u:levelGift:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data === false) {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $info = $dal->getUserEvent($uid, 3);
            if ($info) {
                $data = $info;
            } else {
                $data = 0;
            }
            $cache->set($key, $data);
        }
        return $data;
    }

    public static function getLGDetail($type, $id) {
        $list = self::getLevelGift($type);
        if ($list) {
            foreach ($list as $v) {
                if ($v['level'] == $id) {
                    return $v;
                }
            }
        }
        return null;
    }

    public static function getYGdcetail($type, $id) {
        $list = self::getYellowGift($type);
        if ($list) {
            foreach ($list as $v) {
                if ($v['level'] == $id) {
                    return $v;
                }
            }
        }
        return null;
    }

	public static function getYGDetail($type, $id)
	{
		$list = self::getYellowGift($type);
		if($list){
			foreach($list as $v){
				if($v['level'] == $id){
					return $v;
				}
			}
		}
		return null;
	}
	
    public static function updateUserLGift($uid, $data) {
        $key = 'a:u:levelGift:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, $data);
        self::updateEventGift($uid, $data, 3);
    }

	public static function updateUserYGift($uid, $data)
	{
		$key = 'a:u:yellowGift:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $data);
		self::updateEventGift($uid,$data,7);
	}
	
    public static function getPackage($type) {
        $list = array();
        $key = 'package:';
        $key = self::getKey($key);
        $cache = self::getBasicMC();
        $data = $cache->get($key);
        if ($data == false) {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $data = $dal->getPackage();
            if ($data) {
                foreach ($data as $t => &$info) {
                    $info['awards'] = json_decode($info['awards']);
                }
                $cache->set($key, $data);
            } else {
                return null;
            }
        }
        if ($data) {
            foreach ($data as $v) {
                if ($v['type'] == $type) {
                    $list[] = $v;
                }
            }
        }
        return $list;
    }

    public static function getPackageDetail($cid, $type) {
        $list = self::getPackage($type);
        if ($list) {
            foreach ($list as $v) {
                if ($v['cid'] == $cid) {
                    return $v;
                }
            }
        }
        return null;
    }

    public static function getUTGift($uid) {
        $key = 'a:u:testGift:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data === false) {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $data = $dal->getTestGift($uid);
            if ($data) {
                $cache->set($key, $data);
            } else {
                return null;
            }
        }
        return $data;
    }

    public static function updateTestGift($uid, $data) {
        $key = 'a:u:testGift:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, $data);
        try {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $dal->insertTestGift($data);
        } catch (Exception $e) {
            return "fatal error:" . $e->getMessage();
        }
    }

    public static function getUserIgift($uid) {
        $key = 'a:u:InviteGift:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data === false) {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $data = $dal->getInviteGift($uid);
            if ($data) {
                $cache->set($key, $data);
            } else {
                return null;
            }
        }
        return $data;
    }

    public static function updateInviteGift($data) {
        $key = 'a:u:InviteGift:' . $data['uid'];
        $cache = Hapyfish2_Cache_Factory::getMC($data['uid']);
        $cache->set($key, $data);
        try {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $dal->insertInviteGift($data);
        } catch (Exception $e) {
            return "fatal error:" . $e->getMessage();
        }
    }

    public static function getFirshPay($uid) {
        $key = 'a:u:firshpay:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data === false) {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $data = $dal->getFirstPay($uid);
            if ($data) {
                $cache->set($key, $data);
            } else {
                return null;
            }
        }
        return $data;
    }

    public static function updateFrishPay($data) {
        $key = 'a:u:firshpay:' . $data['uid'];
        $cache = Hapyfish2_Cache_Factory::getMC($data['uid']);
        $cache->set($key, $data);
        try {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $dal->insertInviteGift($data);
        } catch (Exception $e) {
            return "fatal error:" . $e->getMessage();
        }
    }

    public static function getVipLevelUp($uid) {
        $key = 'a:u:v:l:u:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data === false) {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $data = $dal->getVipLevelIp($uid);
            if ($data) {
                $cache->set($key, $data);
            } else {
                return null;
            }
        }
        return $data;
    }

    public static function updateVipLevelUp($data) {
        $key = 'a:u:v:l:u:' . $data['uid'];
        $cache = Hapyfish2_Cache_Factory::getMC($data['uid']);
        $cache->set($key, $data);
        try {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $dal->insertInviteGift($data);
        } catch (Exception $e) {
            return "fatal error:" . $e->getMessage();
        }
    }

    public static function getVipPay($uid) {
        $key = 'a:u:v:l:pay:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data === false) {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $data = $dal->getVipPay($uid);
            if ($data) {
                $cache->set($key, $data);
            } else {
                return null;
            }
        }
        return $data;
    }

    public static function updateVipPay($data) {
        $key = 'a:u:v:l:pay:' . $data['uid'];
        $cache = Hapyfish2_Cache_Factory::getMC($data['uid']);
        $cache->set($key, $data);
        try {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $dal->insertInviteGift($data);
        } catch (Exception $e) {
            return "fatal error:" . $e->getMessage();
        }
    }

    public static function getInviteKey($uid) {
        $key = 'a:u:v:l:invite:key:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data === false) {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $data = $dal->getInvite($uid);
            if ($data) {
                $cache->set($key, $data);
            } else {
                return null;
            }
        }
        return $data;
    }

    public static function updateInviteKey($data) {
        $key = 'a:u:v:l:invite:key:' . $data['uid'];
        $cache = Hapyfish2_Cache_Factory::getMC($data['uid']);
        $cache->set($key, $data);
        try {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $dal->insertInviteGift($data);
        } catch (Exception $e) {
            return "fatal error:" . $e->getMessage();
        }
    }

    public static function getDayFriendBox($uid) {
        $key = 'a:u:f:b:open:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        $date = date('Ymd');
        if ($data === false || $data['date'] != $date) {
            $data['date'] = $date;
            $data['open'] = 0;
            $data['get'] = 0;
        }
        return $data;
    }

    public static function updateFriendBox($uid, $data) {
        $key = 'a:u:f:b:open:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, $data);
        return $data;
    }

	public static function getUserYGift($uid)
	{
		$key = 'a:u:yellowGift:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
			$info = $dal->getUserEvent($uid, 7);
			if($info){
				$data = $info;
			}else{
				$data = 0;
			}
			$cache->set($key, $data);
		}
		return $data;
	}
	
    public static function updateUserDemoed($uid, $data) {
        $key = 'a:u:UserDemoedNumber:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, $data);
        return $data;
    }

    public static function getUserDemoed($uid) {
        $key = 'a:u:UserDemoedNumber:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data === false) {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $info = $dal->getUserDemoedStatus($uid);
            if ($info) {
                $today = date('Ymd');
                if ($today > $info['award_time']) {
                    $info['award_old_time'] = $info['award_time'];
                } else {
                    $info['award_old_time'] = $today;
                }
            } else {
                $info['award_time'] = 0;
                $info['award_count'] = 0;
                $info['award_old_time'] = 0;
            }
            $cache->set($key, $info);
            return $info;
        } else {
            return $data;
        }
    }

    public static function updateUserDemoedFeed($uid, $data) {
        $key = 'a:u:UserDemoedNumber:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->set($key, $data);
        return $data;
    }

    /**
     * 获取玩家loading礼包的状态  
     * @param  int  $data
     * @return   0 为未领取  1为已领取
     */
    public static function getUserLoading($uid) {
        $key = 'a:u:UserLoadingNumber:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $data = $cache->get($key);
        if ($data == false) {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $data = $dal->getUserLoadingStatus($uid);
            $data = $data['loading_c'];
            $cache->set($key, $data);
        }
        return $data;
    }
    /**
     *  loading礼包 重置
     */
    public static function deleteLoading($uid){
        $key = 'a:u:UserLoadingNumber:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);
        $cache->delete($key);
        $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
        $dal->deleteUserLoadingStatus($uid);
    }

    /**
     * 获取loading礼包 奖励内容
     * @
     */
    public static function getLoadingGift() {
        $key = 'LoadingGift';
        $key = self::getKey($key);
        $cache = self::getBasicMC();
        $data = $cache->get($key);
        if ($data == false) {
            $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
            $data = $dal->getLoadingGift();
            $cache->set($key, $data);
        }
        return $data;
    }

    /**
     *  loading礼包 更新玩家 缓存 和 数据库
     * @param type $uid
     * @param int  $info  0为未领取 1为领取
     * @param type $savedb
     * @return type
     */
    public static function updateLoadingInfo($uid, $info, $savedb = false) {
        $key = 'a:u:UserLoadingNumber:';
        //$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $ok = $cache->save($key, $info);
            if ($ok) {
                //save to db
                try {
                    $dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
                    $dal->updateUserLoadingStatus($uid, $info);
                } catch (Exception $e) {
                    err_log('Hapyfish2_Alchemy_HFC_FightAttribute:updateInfo:' . $e->getMessage());
                }
            }
        } else {
            $ok = $cache->update($key, $info);
        }

        return $ok;
    }

}