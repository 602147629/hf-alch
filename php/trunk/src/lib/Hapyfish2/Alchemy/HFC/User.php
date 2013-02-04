<?php

class Hapyfish2_Alchemy_HFC_User {

    public static function getUserVO($uid) {
        $keys = array(
            'a:u:exp:' . $uid, //经验
            'a:u:coin:' . $uid, //游戏币
            'a:u:gem:' . $uid, //充值币
            'a:u:level:' . $uid, //等级
            'a:u:scene:' . $uid,
            'a:u:avatar:' . $uid,
            'a:u:sp:' . $uid,
            'a:u:maxmercyct:' . $uid,
            'a:u:maxodrcut:' . $uid
        );

        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->getMulti($keys);
        if ($data === false) {
            return null;
        }

        $userVO = array('uid' => $uid);

        $userExp = $data[$keys[0]];
        if ($userExp === null) {
            $userExp = self::loadExp($uid, $keys[0]);
            if ($userExp === false) {
                return null;
            }
        }
        $userVO['exp'] = $userExp;

        $userCoin = $data[$keys[1]];
        if ($userCoin === null) {
            $userCoin = self::loadCoin($uid, $keys[1]);
            if ($userCoin === false) {
                return null;
            }
        }
        $userVO['coin'] = $userCoin;

        $userGem = $data[$keys[2]];
        if ($userGem === null) {
            $userGem = self::loadGem($uid, $keys[2]);
            if ($userGem === false) {
                return null;
            }
        }
        $userVO['gem'] = $userGem;

        $userLevel = $data[$keys[3]];
        if ($userLevel === null) {
            $userLevel = self::loadLevel($uid, $keys[3]);
            if ($userLevel === false) {
                return null;
            }
        }
        $userVO['level'] = $userLevel;

        $userScene = $data[$keys[4]];
        if ($userScene === null) {
            $userScene = self::loadScene($uid, $keys[4]);
            if ($userScene === null) {
                return null;
            }
        }
        $userVO['tile_x_length'] = $userScene[0];
        $userVO['tile_z_length'] = $userScene[1];
        $userVO['cur_scene_id'] = $userScene[2];
        $userVO['open_scene_list'] = $userScene[3];

        $userAvatar = $data[$keys[5]];
        if ($userAvatar === null) {
            $userAvatar = self::loadAvatar($uid, $keys[5]);
            if ($userAvatar === false) {
                return null;
            }
        }
        $userVO['avatar'] = $userAvatar;

        $userSpData = $data[$keys[6]];
        if ($userSpData === null) {
            $userSpData = self::loadSp($uid, $keys[6]);
            if ($userSpData === null) {
                return null;
            }
        }
        $userSp = array('sp' => $userSpData[0], 'max_sp' => $userSpData[1], 'sp_set_time' => $userSpData[2]);
        self::resumeSP($uid, $userSp);
        $userVO['sp'] = $userSp['sp'];
        $userVO['max_sp'] = $userSp['max_sp'];
        $userVO['sp_set_time'] = $userSp['sp_set_time'];

        $userVO['next_level_exp'] = (int) Hapyfish2_Alchemy_Cache_Basic::getUserLevelExp($userVO['level'] + 1);

        $userMaxMercenaryCut = $data[$keys[7]];
        if ($userMaxMercenaryCut === null) {
            $userMaxMercenaryCut = self::loadUserMaxMercenaryCount($uid, $keys[7]);
            if ($userMaxMercenaryCut === false) {
                return null;
            }
        }
        $userVO['maxRoleNum'] = $userMaxMercenaryCut;

        $userMaxOrderCut = $data[$keys[8]];
        if ($userMaxOrderCut === null) {
            $userMaxOrderCut = self::loadUserMaxOrderCount($uid, $keys[8]);
            if ($userMaxOrderCut === false) {
                return null;
            }
        }
        $userVO['maxOrder'] = $userMaxOrderCut;

        return $userVO;
    }

    public static function getUser($uid, $fields) {
        $keys = array();
        $getExp = false;
        $getCoin = false;
        $getLevel = false;

        if (isset($fields['exp'])) {
            $keyExp = 'a:u:exp:' . $uid;
            $keys[] = $keyExp;
            $getExp = true;
        }
        if (isset($fields['coin'])) {
            $keyCoin = 'a:u:coin:' . $uid;
            $keys[] = $keyCoin;
            $getCoin = true;
        }
        if (isset($fields['level'])) {
            $keyLevel = 'a:u:level:' . $uid;
            $keys[] = $keyLevel;
            $getLevel = true;
        }
        if (count($keys) == 0) {
            return null;
        }

        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->getMulti($keys);
        if ($data === false) {
            return null;
        }

        $user = array('uid' => $uid);
        if ($getExp) {
            $userExp = $data[$keyExp];
            if ($userExp === null) {
                $userExp = self::loadExp($uid, $keyExp);
                if ($userExp === false) {
                    return null;
                }
            }
            $user['exp'] = $userExp;
        }
        if ($getCoin) {
            $userCoin = $data[$keyCoin];
            if ($userCoin === null) {
                $userCoin = self::loadCoin($uid, $keyCoin);
                if ($userCoin === false) {
                    return null;
                }
            }
            $user['coin'] = $userCoin;
        }
        if ($getLevel) {
            $userLevel = $data[$keyLevel];
            if ($userLevel === null) {
                $userLevel = self::loadLevel($uid, $keyLevel);
                if ($userLevel === false) {
                    return null;
                }
            }
            $user['level'] = $userLevel;
        }
        return $user;
    }

    public static function getUserScene($uid) {
        $key = 'a:u:scene:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $userScene = $cache->get($key);
        if ($userScene === false) {
            $userScene = self::loadScene($uid, $key);
            if ($userScene === null) {
                return null;
            }
        }
        return array(
            'tile_x_length' => $userScene[0],
            'tile_z_length' => $userScene[1],
            'cur_scene_id' => $userScene[2],
            'open_scene_list' => $userScene[3]
        );
    }

    public static function getUserAvatar($uid) {
        $key = 'a:u:avatar:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $avatar = $cache->get($key);
        if ($avatar === false) {
            $avatar = self::loadAvatar($uid, $key);
            if ($avatar === false) {
                return null;
            }
        }
        return $avatar;
    }

    public static function getUserExp($uid) {
        $key = 'a:u:exp:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $exp = $cache->get($key);
        if ($exp === false) {
            $exp = self::loadExp($uid, $key);
            if ($exp === false) {
                return null;
            }
        }
        return $exp;
    }

    public static function getUserCoin($uid) {
        $key = 'a:u:coin:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $coin = $cache->get($key);
        if ($coin === false) {
            $coin = self::loadCoin($uid, $key);
            if ($coin === false) {
                return null;
            }
        }
        return $coin;
    }

    public static function getUserGem($uid) {
        $key = 'a:u:gem:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $gem = $cache->get($key);
        if ($gem === false) {
            $gem = self::loadGem($uid, $key);
            if ($gem === false) {
                return null;
            }
        }
        return $gem;
    }

    public static function reloadUserGem($uid) {
        try {
            $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
            $gem = $dalUser->getGem($uid);
            if ($gem !== false) {
                $key = 'a:u:gem:' . $uid;
                $cache = Hapyfish2_Cache_Factory::getHFC($uid);
                $cache->save($key, $gem);
                return $gem;
            }
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_User::reloadUserGem:' . $uid . ']' . $e->getMessage(), 'db.err');
        }
        return null;
    }

    public static function getUserLevel($uid) {
        $key = 'a:u:level:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $level = $cache->get($key);
        if ($level === false) {
            $level = self::loadLevel($uid, $key);
            if ($level === false) {
                return null;
            }
        }
        return $level;
    }

    public static function getUserSp($uid) {
        $key = 'a:u:sp:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->get($key);
        if ($data === false) {
            $data = self::loadSp($uid, $key);
            if ($data === null) {
                return null;
            }
        }
        $userSp = array('sp' => $data[0], 'max_sp' => $data[1], 'sp_set_time' => $data[2]);
        self::resumeSP($uid, $userSp);
        return $userSp;
    }

    public static function resumeSP($uid, &$userSp) {
        $t = time();
        $sp_set_time = $userSp['sp_set_time'];
        if (empty($sp_set_time)) {
            $userSp['sp_set_time'] = $t;
            self::updateUserSp($uid, $userSp);
            return false;
        }

		$resume_sp_time = SP_RECOVERY_TIME;
		//行动力已经满了
		if ($userSp['sp'] >= 50) {
			//时间间隔超过恢复间隔
			if ($userSp['sp_set_time'] + $resume_sp_time < $t) {
				$userSp['sp_set_time'] = $t;
				self::updateUserSp($uid, $userSp);
			}
			return false;
		}

		//行动力没有满并且超过恢复间隔
		if ($userSp['sp_set_time'] + $resume_sp_time < $t) {
			$rate = floor(($t - $userSp['sp_set_time'])/$resume_sp_time);
			$spChange = SP_RECOVERY_SP*$rate;
			if ($userSp['sp'] + $spChange >= 50) {
				$userSp['sp'] = 50;
			} else {
				$userSp['sp'] += $spChange;
			}
			$userSp['sp_set_time'] += $rate*$resume_sp_time;
			self::updateUserSp($uid, $userSp);
			return true;
		}
		return false;
	}

    public static function updateUserSp($uid, $spInfo, $savedb = false) {
        $data = array($spInfo['sp'], $spInfo['max_sp'], $spInfo['sp_set_time']);
        $key = 'a:u:sp:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $ok = $cache->save($key, $data);
            if ($ok) {
                try {
                    $info = array('sp' => $spInfo['sp'], 'max_sp' => $spInfo['max_sp'], 'sp_set_time' => $spInfo['sp_set_time']);
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserSp:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $data);
        }
    }

    public static function updateUserScene($uid, $sceneInfo, $savedb = false) {
        $data = array(
            $sceneInfo['tile_x_length'],
            $sceneInfo['tile_z_length'],
            $sceneInfo['cur_scene_id'],
            $sceneInfo['open_scene_list']
        );

        $key = 'a:u:scene:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $ok = $cache->save($key, $data);
            if ($ok) {
                try {
                    $info = array(
                        'tile_x_length' => $sceneInfo['tile_x_length'],
                        'tile_z_length' => $sceneInfo['tile_z_length'],
                        'cur_scene_id' => $sceneInfo['cur_scene_id'],
                        'open_scene_list' => $sceneInfo['open_scene_list']
                    );
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserScene:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $data);
        }
    }

    public static function updateUserAvatar($uid, $avatar, $savedb = false) {
        $key = 'a:u:avatar:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $ok = $cache->save($key, $avatar);
            if ($ok) {
                try {
                    $info = array('avatar' => $avatar);
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserAvatar:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $avatar);
        }
    }

    public static function updateUserLevel($uid, $level, $savedb = false) {
        $key = 'a:u:level:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $ok = $cache->save($key, $level);
            if ($ok) {
                try {
                    $info = array('level' => $level);
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserLevel:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $level);
        }
    }

    public static function updateUserExp($uid, $exp, $savedb = false) {
        $key = 'a:u:exp:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $ok = $cache->save($key, $exp);
            if ($ok) {
                try {
                    $info = array('exp' => $exp);
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserExp:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $exp);
        }
    }

    public static function incUserExp($uid, $expChange) {
        if ($expChange <= 0) {
            return false;
        }
        $expChange = Hapyfish2_Alchemy_Bll_VipWelfare::getVipLevelExp($uid, $expChange);
        $userExp = self::getUserExp($uid);
        if ($userExp === null) {
            return false;
        }
        $userExp += $expChange;
        $ok = self::updateUserExp($uid, $userExp);
        if ($ok) {
            Hapyfish2_Alchemy_Bll_UserResult::mergeExp($uid, $expChange);
            Hapyfish2_Alchemy_Bll_User::checkLevelUp($uid);
        }
        return $ok;
    }

    public static function updateUserCoin($uid, $coin, $savedb = false) {
        $key = 'a:u:coin:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $ok = $cache->save($key, $coin);
            if ($ok) {
                try {
                    $info = array('coin' => $coin);
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserCoin:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $coin);
        }
    }

    public static function incUserCoin($uid, $coinChange,$channel=99,$savedb = false) {
        if ($coinChange <= 0) {
            return false;
        }

        $userCoin = self::getUserCoin($uid);
        if ($userCoin === null) {
            return false;
        }
        $userCoin += $coinChange;
        $ok = self::updateUserCoin($uid, $userCoin, $savedb);
        if ($ok) {
            Hapyfish2_Alchemy_Bll_UserResult::mergeCoin($uid, $coinChange);
            //触发任务处理
            $event = array('uid' => $uid, 'data' => $coinChange);
            Hapyfish2_Alchemy_Bll_TaskMonitor::coinGain($event);
            $log = Hapyfish2_Util_Log::getInstance();
            $log->report('coin', array($uid, $coinChange,1,$channel));
        }
        return $ok;
    }

    public static function decUserCoin($uid, $coinChange,$channel=99,$savedb = false) {
        if ($coinChange <= 0) {
            return false;
        }

        $userCoin = self::getUserCoin($uid);
        if ($userCoin === null) {
            return false;
        }
        if ($userCoin < $coinChange) {
            return false;
        }
        $userCoin -= $coinChange;
        $ok = self::updateUserCoin($uid, $userCoin, $savedb);
        if ($ok) {
            Hapyfish2_Alchemy_Bll_UserResult::mergeCoin($uid, -$coinChange);
            $log = Hapyfish2_Util_Log::getInstance();
            $log->report('coin', array($uid, $coinChange, 2,$channel));
        }
        return $ok;
    }

    public static function incUserExpAndCoin($uid, $expChange, $coinChange, $savedb = false) {
        self::incUserExp($uid, $expChange);
        self::incUserCoin($uid, $coinChange,21,$savedb);
    }

    public static function decUserSp($uid, $spChange, $savedb = false) {
        if ($spChange <= 0) {
            return false;
        }

        $userSp = self::getUserSp($uid);
        if ($userSp === null) {
            return false;
        }
        if ($userSp['sp'] < $spChange) {
            return false;
        }
        $userSp['sp'] -= $spChange;
        $ok = self::updateUserSp($uid, $userSp, $savedb);
        if ($ok) {
            Hapyfish2_Alchemy_Bll_UserResult::mergeSp($uid, -$spChange);
        }
        return $ok;
    }

    public static function incUserSp($uid, &$spChange, $savedb = false) {
        if ($spChange <= 0) {
            return false;
        }

    	$userSp = self::getUserSp($uid);
    	if ($userSp === null) {
    		return false;
    	}
    	if ($userSp['sp'] + $spChange > 50) {
    		$spChange = 50 - $userSp['sp'];
    		$userSp['sp'] = 50;
    	} else {
    		$userSp['sp'] += $spChange;
    	}
    	$ok = self::updateUserSp($uid, $userSp, $savedb);
    	if ($ok) {
    		Hapyfish2_Alchemy_Bll_UserResult::mergeSp($uid, $spChange);
    	}
    	return $ok;
    }

    public static function getUserLoginInfo($uid) {
        $key = 'a:u:login:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->get($key);
        if ($data === false) {
            try {
                $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                $data = $dalUser->getLoginInfo($uid);
                if ($data) {
                    $cache->add($key, $data);
                } else {
                    return null;
                }
            } catch (Exception $e) {
                info_log('[Hapyfish2_Alchemy_HFC_User::getUserLoginInfo:' . $uid . ']' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        $loginInfo = array(
            'last_login_time' => (int) $data[0],
            'today_login_count' => (int) $data[1],
            'active_login_count' => (int) $data[2],
            'max_active_login_count' => (int) $data[3],
            'all_login_count' => (int) $data[4],
            'login_day_count' => (int) $data[5],
        );
        return $loginInfo;
    }

    public static function updateUserLoginInfo($uid, $loginInfo, $savedb = false) {
        $key = 'a:u:login:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 3600);
        }
        $data = array(
            $loginInfo['last_login_time'], $loginInfo['today_login_count'],
            $loginInfo['active_login_count'], $loginInfo['max_active_login_count'],
            $loginInfo['all_login_count'], $loginInfo['login_day_count']
        );
        if ($savedb) {
            $ok = $cache->save($key, $data);
            if ($ok) {
                try {
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $loginInfo);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserLoginInfo:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $data);
        }
    }

    public static function getUserFightAssistInfo($uid) {
        $key = 'a:u:fightassist:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->get($key);
        if ($data === false) {
            try {
                $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                $data = $dalUser->getFightAssistInfo($uid);
                if ($data) {
                    $cache->add($key, $data);
                } else {
                    return null;
                }
            } catch (Exception $e) {
                info_log('[Hapyfish2_Alchemy_HFC_User::getUserFightAssistInfo:' . $uid . ']' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        $assistInfo = array(
            'assist_bas_count' => (int) $data[0],
            'assist_ext_count' => (int) $data[1]
        );
        return $assistInfo;
    }

    public static function updateUserFightAssistInfo($uid, $assistInfo, $savedb = false) {
        $key = 'a:u:fightassist:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 3600);
        }
        $data = array(
            (int) $assistInfo['assist_bas_count'], (int) $assistInfo['assist_ext_count']
        );
        if ($savedb) {
            $ok = $cache->save($key, $data);
            if ($ok) {
                try {
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $assistInfo);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserFightAssistInfo:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $data);
        }
    }

    public static function getUserFeats($uid) {
        $key = 'a:u:feats:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->get($key);
        if ($data === false) {
            try {
                $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                $data = $dalUser->getFeats($uid);
                if ($data) {
                    $cache->add($key, $data);
                } else {
                    return null;
                }
            } catch (Exception $e) {
                info_log('[Hapyfish2_Alchemy_HFC_User::getUserFeats:' . $uid . ']' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        return $data;
    }

    public static function updateUserFeats($uid, $num, $savedb = false) {
        $key = 'a:u:feats:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $ok = $cache->save($key, $num);
            if ($ok) {
                try {
                    $info = array('feats' => $num);
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserFeats:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $num);
        }
    }

    public static function incUserFeats($uid, $num, $savedb = false) {
        if ($num <= 0) {
            return false;
        }

        $userFeats = self::getUserFeats($uid);
        if ($userFeats === null) {
            return false;
        }
        $userFeats += $num;
        $ok = self::updateUserFeats($uid, $userFeats, $savedb);
        if ($ok) {
            Hapyfish2_Alchemy_Bll_UserResult::mergeFeats($uid, $num);
            //触发任务处理
            //$event = array('uid' => $uid, 'data' => $coinChange);
            //Hapyfish2_Alchemy_Bll_TaskMonitor::coinGain($event);
        }
        return $ok;
    }

    public static function decUserFeats($uid, $change, $savedb = false) {
        if ($change <= 0) {
            return false;
        }

        $userFeats = self::getUserFeats($uid);
        if ($userFeats === null) {
            return false;
        }
        if ($userFeats < $change) {
            return false;
        }
        $userFeats -= $change;
        $ok = self::updateUserFeats($uid, $userFeats, $savedb);
        if ($ok) {
            Hapyfish2_Alchemy_Bll_UserResult::mergeFeats($uid, -$change);
        }
        return $ok;
    }

    public static function getUserMaxOrderCount($uid) {
        $key = 'a:u:maxodrcut:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $count = $cache->get($key);
        if ($count === false) {
            try {
                $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                $count = $dalUser->getOrderCount($uid);
                if ($count !== false) {
                    $cache->add($key, $count);
                } else {
                    return null;
                }
            } catch (Exception $e) {
                info_log('[Hapyfish2_Alchemy_HFC_User::getUserMaxOrderCount:' . $uid . ']' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        return $count;
    }

    public static function updateUserMaxOrderCount($uid, $count, $savedb = false) {
        $key = 'a:u:maxodrcut:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $ok = $cache->save($key, $count);
            if ($ok) {
                try {
                    $info = array('order_count' => $count);
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserMaxOrderCount:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $count);
        }
    }

    public static function getUserMaxMercenaryCount($uid) {
        $key = 'a:u:maxmercyct:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $count = $cache->get($key);
        if ($count === false) {
            try {
                $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                $count = $dalUser->getMaxMercenaryCount($uid);
                if ($count !== false) {
                    $cache->add($key, $count);
                } else {
                    return null;
                }
            } catch (Exception $e) {
                info_log('[Hapyfish2_Alchemy_HFC_User::getUserMaxMercenaryCount:' . $uid . ']' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        return $count;
    }

    public static function updateUserMaxMercenaryCount($uid, $count, $savedb = false) {
        $key = 'a:u:maxmercyct:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $ok = $cache->save($key, $count);
            if ($ok) {
                try {
                    $info = array('mercenary_count' => $count);
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserMaxMercenaryCount:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $count);
        }
    }

    /**
     * 酒馆等级
     * @param unknown_type $uid
     */
    public static function getUserTavernLevelList($uid) {
        $key = 'a:u:tavernlevel:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->get($key);
        if ($data === false) {
            try {
                $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                $data = $dalUser->getTavernLevel($uid);
                if ($data !== false) {
                    $cache->add($key, $data);
                } else {
                    return null;
                }
            } catch (Exception $e) {
                info_log('[Hapyfish2_Alchemy_HFC_User::getTavernLevel:' . $uid . ']' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        return $data;
    }

    public static function updateUserTavernLevel($uid, $data, $savedb = false) {
        $key = 'a:u:tavernlevel:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $ok = $cache->save($key, $data);
            if ($ok) {
                try {
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $data);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserTavernLevel:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $data);
        }
    }

    /**
     * 铁匠铺等级
     * @param unknown_type $uid
     */
    public static function getUserSmithyLevel($uid) {
        $key = 'a:u:smithylevel:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $count = $cache->get($key);
        if ($count === false) {
            try {
                $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                $count = $dalUser->getSmithyLevel($uid);
                if ($count !== false) {
                    $cache->add($key, $count);
                } else {
                    return null;
                }
            } catch (Exception $e) {
                info_log('[Hapyfish2_Alchemy_HFC_User::getSmithyLevel:' . $uid . ']' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        return $count;
    }

    public static function updateUserSmithyLevel($uid, $level, $savedb = false) {
        $key = 'a:u:smithylevel:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $ok = $cache->save($key, $level);
            if ($ok) {
                try {
                    $info = array('smithy_level' => $level);
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserSmithyLevel:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $level);
        }
    }

    /**
     * 竞技场等级
     * @param int $uid
     */
    public static function getUserArenaLevel($uid) {
        $key = 'a:u:arenalevel:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $count = $cache->get($key);
        if ($count === false) {
            try {
                $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                $count = $dalUser->getArenaLevel($uid);
                if ($count !== false) {
                    $cache->add($key, $count);
                } else {
                    return null;
                }
            } catch (Exception $e) {
                info_log('[Hapyfish2_Alchemy_HFC_User::getArenaLevel:' . $uid . ']' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        return $count;
    }

    public static function updateUserArenaLevel($uid, $level, $savedb = false) {
        $key = 'a:u:arenalevel:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $ok = $cache->save($key, $level);
            if ($ok) {
                try {
                    $info = array('arena_level' => $level);
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserArenaLevel:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $level);
        }
    }

    public static function getUserHomeLevel($uid) {
        $key = 'a:u:homelevel:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $count = $cache->get($key);
        if ($count === false) {
            try {
                $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                $count = $dalUser->getHomeLevel($uid);
                if ($count !== false) {
                    $cache->add($key, $count);
                } else {
                    return null;
                }
            } catch (Exception $e) {
                info_log('[Hapyfish2_Alchemy_HFC_User::getHomeLevel:' . $uid . ']' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        return $count;
    }

    public static function updateUserHomeLevel($uid, $level, $savedb = false) {
        $key = 'a:u:homelevel:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $ok = $cache->save($key, $level);
            if ($ok) {
                try {
                    $info = array('home_level' => $level);
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserHomeLevel:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $level);
        }
    }

    /**
     * 训练营等级
     * @param int $uid
     */
    public static function getUserTrainingLevel($uid) {
        $key = 'a:u:traininglevel:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $count = $cache->get($key);
        if ($count === false) {
            try {
                $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                $count = $dalUser->getTrainingLevel($uid);
                if ($count !== false) {
                    $cache->add($key, $count);
                } else {
                    return null;
                }
            } catch (Exception $e) {
                info_log('[Hapyfish2_Alchemy_HFC_User::getTrainingLevel:' . $uid . ']' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        return $count;
    }

    public static function updateUserTrainingLevel($uid, $level, $savedb = false) {
        $key = 'a:u:traininglevel:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $ok = $cache->save($key, $level);
            if ($ok) {
                try {
                    $info = array('training_level' => $level);
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserTrainingLevel:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $level);
        }
    }

    /**
     * 训练营-当前训练位置数
     * @param int $uid
     */
    public static function getUserTrainingPosNum($uid) {
        $key = 'a:u:trainingposnum:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $count = $cache->get($key);
        if ($count === false) {
            try {
                $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                $count = $dalUser->getTrainingPosNum($uid);
                if ($count !== false) {
                    $cache->add($key, $count);
                } else {
                    return null;
                }
            } catch (Exception $e) {
                info_log('[Hapyfish2_Alchemy_HFC_User::getUserTrainingPosNum:' . $uid . ']' . $e->getMessage(), 'db.err');
                return null;
            }
        }
        return $count;
    }

    public static function updateUserTrainingPosNum($uid, $num, $savedb = false) {
        $key = 'a:u:trainingposnum:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $ok = $cache->save($key, $num);
            if ($ok) {
                try {
                    $info = array('training_pos_num' => $num);
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserTrainingPosNum:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $num);
        }
    }

    //////////////////////////////////////////////////////////////////
    //private static mothed group

    private static function loadExp($uid, $key) {
        try {
            $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
            $exp = $dalUser->getExp($uid);
            if ($exp !== false) {
                $cache = Hapyfish2_Cache_Factory::getHFC($uid);
                $cache->add($key, $exp);
                return $exp;
            }
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_User::loadExp:' . $uid . ']' . $e->getMessage(), 'db.err');
        }
        return false;
    }

    private static function loadCoin($uid, $key) {
        try {
            $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
            $coin = $dalUser->getCoin($uid);
            if ($coin !== false) {
                $cache = Hapyfish2_Cache_Factory::getHFC($uid);
                $cache->add($key, $coin);
                return $coin;
            }
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_User::loadCoin:' . $uid . ']' . $e->getMessage(), 'db.err');
        }
        return false;
    }

    private static function loadGem($uid, $key) {
        try {
            $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
            $gem = $dalUser->getGem($uid);
            if ($gem !== false) {
                $cache = Hapyfish2_Cache_Factory::getHFC($uid);
                $cache->add($key, $gem);
                return $gem;
            }
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_User::loadGem:' . $uid . ']' . $e->getMessage(), 'db.err');
        }
        return false;
    }

    private static function loadLevel($uid, $key) {
        try {
            $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
            $level = $dalUser->getLevel($uid);
            if ($level !== false) {
                $cache = Hapyfish2_Cache_Factory::getHFC($uid);
                $cache->add($key, $level);
                return $level;
            }
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_User::loadLevel:' . $uid . ']' . $e->getMessage(), 'db.err');
        }
        return false;
    }

    private static function loadAvatar($uid, $key) {
        try {
            $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
            $avatar = $dalUser->getAvatar($uid);
            if ($avatar !== false) {
                $cache = Hapyfish2_Cache_Factory::getHFC($uid);
                $cache->add($key, $avatar);
                return $avatar;
            }
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_User::loadAvatar:' . $uid . ']' . $e->getMessage(), 'db.err');
        }
        return false;
    }

    private static function loadScene($uid, $key) {
        try {
            $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
            $scene = $dalUser->getScene($uid);
            if ($scene !== null) {
                $cache = Hapyfish2_Cache_Factory::getHFC($uid);
                $cache->add($key, $scene);
                return $scene;
            }
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_User::loadScene:' . $uid . ']' . $e->getMessage(), 'db.err');
        }
        return null;
    }

    private static function loadSp($uid, $key) {
        try {
            $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
            $spData = $dalUser->getSp($uid);
            if ($spData !== null) {
                $cache = Hapyfish2_Cache_Factory::getHFC($uid);
                $cache->add($key, $spData);
                return $spData;
            }
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_User::loadSp:' . $uid . ']' . $e->getMessage(), 'db.err');
        }
        return null;
    }

    private static function loadUserMaxMercenaryCount($uid, $key) {
        try {
            $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
            $count = $dalUser->getMaxMercenaryCount($uid);
            if ($count !== false) {
                $cache = Hapyfish2_Cache_Factory::getHFC($uid);
                $cache->add($key, $count);
                return $count;
            }
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_User::loadUserMaxMercenaryCount:' . $uid . ']' . $e->getMessage(), 'db.err');
        }
        return false;
    }

    private static function loadUserMaxOrderCount($uid, $key) {
        try {
            $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
            $count = $dalUser->getMaxOrderCount($uid);
            if ($count !== false) {
                $cache = Hapyfish2_Cache_Factory::getHFC($uid);
                $cache->add($key, $count);
                return $count;
            }
        } catch (Exception $e) {
            info_log('[Hapyfish2_Alchemy_HFC_User::loadUserMaxOrderCount:' . $uid . ']' . $e->getMessage(), 'db.err');
        }
        return false;
    }

    public static function getTotalPay($uid) {
        $key = 'a:u:totalpay:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $count = $cache->get($key);
        if ($count === false) {
            try {
                $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                $count = $dalUser->getTotalPay($uid);
                if ($count !== false) {
                    $cache->add($key, $count);
                } else {
                    return 0;
                }
            } catch (Exception $e) {
                info_log('[Hapyfish2_Alchemy_HFC_User::getUserTrainingPosNum:' . $uid . ']' . $e->getMessage(), 'db.err');
                return 0;
            }
        }
        return $count;
    }

    public static function updateTotalPay($uid, $pay, $savedb = false) {
        $key = 'a:u:totalpay:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $ok = $cache->save($key, $pay);
            if ($ok) {
                try {
                    $info = array('total_pay' => $pay);
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserLevel:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $pay);
        }
    }

    public static function getTotalInvite($uid) {
        $key = 'a:u:totalInvite:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $count = $cache->get($key);
        if ($count === false) {
            try {
                $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                $count = $dalUser->getTotalInvite($uid);
                if ($count !== false) {
                    $cache->add($key, $count);
                } else {
                    return 0;
                }
            } catch (Exception $e) {
                info_log('[Hapyfish2_Alchemy_HFC_User::getUserTrainingPosNum:' . $uid . ']' . $e->getMessage(), 'db.err');
                return 0;
            }
        }
        return $count;
    }

    public static function updateTotalInvite($uid, $invite, $savedb = false) {
        $key = 'a:u:totalInvite:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $ok = $cache->save($key, $invite);
            if ($ok) {
                try {
                    $info = array('total_invite' => $invite);
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserLevel:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $invite);
        }
    }

    public static function getUserFriendKey($uid) {
        $key = 'a:u:Friend:key:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $count = $cache->get($key);
        if ($count === false) {
            try {
                $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                $count = $dalUser->getKeyNum($uid);
                if ($count !== false) {
                    $cache->add($key, $count);
                } else {
                    return 0;
                }
            } catch (Exception $e) {
                info_log('[Hapyfish2_Alchemy_HFC_User::getUserTrainingPosNum:' . $uid . ']' . $e->getMessage(), 'db.err');
                return 0;
            }
        }
        return $count;
    }

    public static function updateFriendKey($uid, $keyNum, $savedb = false) {
        $key = 'a:u:Friend:key:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }
        if ($savedb) {
            $ok = $cache->save($key, $keyNum);
            if ($ok) {
                try {
                    $info = array('box_key' => $keyNum);
                    $dalUser = Hapyfish2_Alchemy_Dal_User::getDefaultInstance();
                    $dalUser->update($uid, $info);
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_User::updateUserLevel:' . $uid . ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            return $cache->update($key, $keyNum);
        }
    }

    public static function addTotalInvite($uid, $invite) {
        $key = floor($invite / 2);
        $haveGetKey = array(0);
        $keyData = Hapyfish2_Alchemy_Cache_EventGift::getInviteKey($uid);
        $getKey = json_decode($keyData['step']);
        if (!empty($getKey)) {
            $haveGetKey = $getKey;
        }
        if ($key - $haveGetKey[0] > 0) {
            $keyData['step'] = json_encode($haveGetKey);
            Hapyfish2_Alchemy_Cache_EventGift::updateInviteKey($keyData);
            $keyNum = self::getUserFriendKey($uid);
            $keyNum += $key - $haveGetKey[0];
            self::updateFriendKey($uid, $keyNum);
        }
        self::updateTotalInvite($uid, $invite);
        if ($invite == 1) {
            $uInviteAward['uid'] = $uid;
            $uInviteAward['step'] = '[1]';
            $uInviteAward['type'] = 1;
            Hapyfish2_Alchemy_Cache_EventGift::updateInviteGift($uInviteAward);
        }
    }

    public static function getStrCoolTime($uid) {
        $key = 'a:u:str:c:t:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->get($key);
        if ($data === false) {
            return array(
                'endtime' => 0,
                'canStr' => 1
            );
        }
        return $data;
    }

    public static function updateStrCoolTime($uid, $data) {
        $key = 'a:u:str:c:t:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $cache->update($key, $data);
    }

    public static function getUserStatus($uid) {
        $key = 'a:u:dm:s:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $data = $cache->get($key);
        $date = date('Ymd');
        if ($data === false || $data['date'] != $date) {
            $data['date'] = $date;
            $data['status'] = 0;
        }
        return $data;
    }

    public static function updateUserStatus($uid, $data) {
        $key = 'a:u:dm:s:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
        $cache->update($key, $data);
    }

}
