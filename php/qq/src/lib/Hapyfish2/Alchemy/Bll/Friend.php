<?php

class Hapyfish2_Alchemy_Bll_Friend
{
	public static function getRankList($uid, $pageIndex = 1, $pageSize = 50)
	{
		$friendList = array();
        /*require_once(CONFIG_DIR . '/language.php');
		$friendList[] = array(
			'uid' => 134,
			'name' => LANG_PLATFORM_BASE_TXT_14,
			'face' => STATIC_HOST . '/apps/alchemy/images/lele2.jpg',
			'exp' => 999999999,
			'level' => 99,
			'canSteal' => 0
		);*/
		$fids = Hapyfish2_Platform_Bll_Friend::getFriendIds($uid);
		if (empty($fids)) {
			$fids = array($uid);
		} else {
			$fids[] = $uid;
		}

		$nowTm = time();
		//占领信息
		$occupyInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($uid);
		foreach ($fids as $fid) {
			$userInfo = Hapyfish2_Alchemy_HFC_User::getUser($fid, array('exp' => 1, 'level' => 1));
			$avatar = Hapyfish2_Alchemy_HFC_User::getUserAvatar($fid);
			if ($userInfo && $avatar) {
				$info = Hapyfish2_Platform_Bll_User::getUser($fid);
				//$feats = Hapyfish2_Alchemy_HFC_User::getUserFeats($fid);
				$safeTm = 0;
        		$ownerUid = 0;
        		$ownerName = '';
        		$ownerFace = '';
        		$ownerEndTime = 0;
        		$ownerAwardTime = 0;
        		$atkSafeTime = 0;
        		$ownerBuildId = 0;

				//被我占领好友
                if (isset($occupyInfo['initiative'][$fid])) {
                    if ($nowTm - (int)$occupyInfo['initiative'][$fid]['tm'] < Hapyfish2_Alchemy_Bll_FightOccupy::MAX_OCCUPY_INTERVAL) {
                        $ownerUid = $uid;
                        $ownerData = Hapyfish2_Platform_Bll_User::getUser($uid);
                        $ownerName = $ownerData['name'];
                        $ownerFace = $ownerData['figureurl'];
                        $ownerEndTime = (int)$occupyInfo['initiative'][$fid]['tm'] + Hapyfish2_Alchemy_Bll_FightOccupy::MAX_OCCUPY_INTERVAL;
                        $ownerBuildId = (int)$occupyInfo['initiative'][$fid]['house'];
                        if ($occupyInfo['initiative'][$fid]['taxTm']) {
                            $cnt = count($occupyInfo['initiative'][$fid]['taxTm']);
                            $ownerAwardTime = (int)$occupyInfo['initiative'][$fid]['taxTm'][$cnt-1] + Hapyfish2_Alchemy_Bll_FightOccupy::COLLECT_TAX_INTERVAL;
                        }
                        else {
                            $ownerAwardTime = (int)$occupyInfo['initiative'][$fid]['tm'] + Hapyfish2_Alchemy_Bll_FightOccupy::COLLECT_TAX_INTERVAL;
                        }
                    }
                }
                else {
                    $fidOccInfo = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($fid);
                    if ($nowTm - (int)$fidOccInfo['last_protect_open_tm'] < Hapyfish2_Alchemy_Bll_FightOccupy::PROTECT_INTERVAL) {
                        $safeTm = (int)$fidOccInfo['last_protect_open_tm'] + Hapyfish2_Alchemy_Bll_FightOccupy::PROTECT_INTERVAL;
                    }

                    if ($fidOccInfo['passive'] && $fidOccInfo['passive']['status'] == Hapyfish2_Alchemy_Bll_FightOccupy::STATUS_BEING_OCCUPIED) {//0-free 1-being attack 2-being occupied
                        $ownerUid = $fidOccInfo['passive']['uid'];
                        $ownerData = Hapyfish2_Platform_Bll_User::getUser($ownerUid);
                        $ownerName = $ownerData['name'];
                        $ownerFace = $ownerData['figureurl'];
                        $ownerEndTime = (int)$occupyInfo['passive']['tm'] + Hapyfish2_Alchemy_Bll_FightOccupy::MAX_OCCUPY_INTERVAL;
                        $ownerBuildId = (int)$occupyInfo['passive']['house'];
                    }

                    if ($fidOccInfo['passive'] && $fidOccInfo['passive']['status'] == Hapyfish2_Alchemy_Bll_FightOccupy::STATUS_FREE) {
        		        if ($fidOccInfo['passive']['uid'] == $uid) {
                            $atkSafeTime = (int)$fidOccInfo['passive']['tm'] + Hapyfish2_Alchemy_Bll_FightOccupy::ATT_PROTECT_INTERVAL;
        		        }
        		    }
                }

                //主角战斗信息
                $roleInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($fid);
                $roleLevel = $roleInfo['level'];
                $vip = new Hapyfish2_Alchemy_Bll_Vip();
                $vipInfo = $vip->getVipInfo($fid);
				$uVo = array(
					'uid' => $fid,
					'name' => $info['name'],
					'face' => $info['figureurl'],
					'exp' => $userInfo['exp'],
					'level' => $userInfo['level'],
					'roleLevel' => $roleLevel,
					'canSteal' => 0,
				    //'feats' => $feats,
					'ownerUid' => $ownerUid,
					'ownerName' => $ownerName,
					'ownerFace' => $ownerFace,
					'safeTime' => $safeTm,
					'ownerEndTime' => $ownerEndTime,
					'ownerAwardTime' => $ownerAwardTime,
					'ownerBuildId' => $ownerBuildId,
					'atkSafeTime' => $atkSafeTime,
					'vipLevel'	  => $vipInfo['level'],
					'vipStatus' => $vipInfo['vipStatus']
				);
				if(PLATFORM == 'sinaweibo'){
					$uVo['sinaVip'] = Hapyfish2_Platform_Bll_UserMore::getUserV($fid);
				}
				$friendList[] = $uVo;
			}
		}
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'friends', $friendList);
		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'maxPage', 1);
		return 1;
	}


    public static function getFriendList($uid, $pageIndex = 1, $pageSize = 50)
	{
		$friendList = array();

		$fids = Hapyfish2_Platform_Bll_Friend::getFriendIds($uid);
        if ($fids) {
    		foreach ($fids as $fid) {
    			$userInfo = Hapyfish2_Alchemy_HFC_User::getUser($fid, array('exp' => 1, 'level' => 1));
    			$avatar = Hapyfish2_Alchemy_HFC_User::getUserAvatar($fid);
    			if ($userInfo && $avatar) {
    				$info = Hapyfish2_Platform_Bll_User::getUser($fid);
    				$friendList[] = array(
    					'uid' => $fid,
    					'name' => $info['name'],
    					'face' => $info['figureurl'],
    					'exp' => $userInfo['exp'],
    					'level' => $userInfo['level']
    				);
    			}
    		}
        }

		return array('friends' => $friendList, 'maxPage' => 1);
	}

    public static function getFriendFightJobLevSpread($uid, $pageIndex = 1, $pageSize = 50)
	{
	    $maxLev = 100;
		/*$friendWarrior = array();
		$friendRogue = array();
		$friendMagus = array();
		$maxLevWarrior = $maxLevRogue = $maxLevMagus = 0;*/
		$friendElementJobStat = array();
		$fids = Hapyfish2_Platform_Bll_Friend::getFriendIds($uid);

        if ($fids) {
            /*for ($i=1; $i<=$maxLev; $i++) {
                $friendWarrior[$i] = 0;
                $friendRogue[$i] = 0;
                $friendMagus[$i] = 0;
            }*/
    		foreach ($fids as $fid) {
    			$userInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($fid);
    			$avatar = Hapyfish2_Alchemy_HFC_User::getUserAvatar($fid);
    			if ($userInfo && $avatar) {
    			    $fJob = (int)$userInfo['job'];
    			    $fElement = (int)$userInfo['element'];
    			    $fLev = (int)$userInfo['level'];
    			    if (isset($friendElementJobStat[$fJob.'-'.$fElement][$fLev])) {
    			        $friendElementJobStat[$fJob.'-'.$fElement][$fLev][0] += 1;
    			        if (mt_rand(1, 10) == 7) {
    			            $friendElementJobStat[$fJob.'-'.$fElement][$fLev][1] = $userInfo['class_name'] . '|' . $userInfo['name'];
    			        }
    			    }
    			    else {
    			        $friendElementJobStat[$fJob.'-'.$fElement][$fLev][0] = 1;
    			        $friendElementJobStat[$fJob.'-'.$fElement][$fLev][1] = $userInfo['class_name'] . '|' . $userInfo['name'];
    			    }


    			    /*if ($userInfo['job'] == Hapyfish2_Alchemy_Bll_Fight_Simulator::JOB_Warrior) {//1-Warrior 2-Rogue 3-Magus
    			        $userInfo['element'];
    			        $friendWarrior[][$userInfo['level']] += 1;
    			        if ($maxLevWarrior < $userInfo['level']) {
    			            $maxLevWarrior = $userInfo['level'];
    			            $friendWarrior[0] = $userInfo['class_name'] . '|' . $userInfo['name'];
    			        }
    			    }
    			    else if ($userInfo['job'] == Hapyfish2_Alchemy_Bll_Fight_Simulator::JOB_Rogue) {
    			        $friendRogue[$userInfo['level']] += 1;
    			        if ($maxLevRogue < $userInfo['level']) {
    			            $maxLevRogue = $userInfo['level'];
    			            $friendRogue[0] = $userInfo['class_name'] . '|' . $userInfo['name'];
    			        }
    			    }
    			    else if ($userInfo['job'] == Hapyfish2_Alchemy_Bll_Fight_Simulator::JOB_Magus) {
    			        $friendMagus[$userInfo['level']] += 1;
    			        if ($maxLevMagus < $userInfo['level']) {
    			            $maxLevMagus = $userInfo['level'];
    			            $friendMagus[0] = $userInfo['class_name'] . '|' . $userInfo['name'];
    			        }
    			    }*/
    			}
    		}
        }

		//return array('warrior'=>$friendWarrior, 'rogue'=>$friendRogue, 'magus'=>$friendMagus);
		return $friendElementJobStat;
	}
	
	public static function addFriend($uid, $fidlist)
	{
		$fids = Hapyfish2_Platform_Bll_Friend::getFriendIds($uid);
		if(!empty($fidlist)){
			foreach($fidlist as $fid){
				if ($fids !== null) {
        			if (!in_array($fid, $fids)) {
	        			$fids[] = $fid;
	        			Hapyfish2_Platform_Bll_Friend::updateFriend($uid, $fids);
        			}
       	 		}
        		else {
	        		$fids = array();
	        		$fids[] = $fid;
	        		Hapyfish2_Platform_Bll_Friend::addFriend($uid, $fids);
				}
			}
		}
		$friends = self::getRankList($uid);
	}
}