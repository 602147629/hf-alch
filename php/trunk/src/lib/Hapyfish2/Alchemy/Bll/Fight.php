<?php

class Hapyfish2_Alchemy_Bll_Fight {

    public static function getNewId($uid) {
        try {
            $dal = Hapyfish2_Alchemy_Dal_UserSequence::getDefaultInstance();
            $newId = $dal->getId($uid, 'fight');
            return $newId;
        } catch (Exception $e) {
            info_log('getId:' . $e->getMessage(), 'Bll_Fight');
        }
        return 0;
    }

    public static function getCurFightInfo($uid) {
        $info = Hapyfish2_Alchemy_Cache_Fight::getFightInfo($uid);
        return $info;
    }

    public static function regFight($uid, $id) {
        //$info = Hapyfish2_Alchemy_Cache_Fight::getFightInfo($uid);
        //if (!$info || $info['status']) {
        $info = array();
        $info['uid'] = $uid;
        $info['fid'] = self::getNewId($uid);
        $info['type'] = 0;
        $info['status'] = 0;

        $aryRnd = array();
        for ($i = 0; $i < 20; $i++) {
            $aryRnd[] = mt_rand(1, 1000);
        }

        $homeSide = Hapyfish2_Alchemy_Bll_FightCorps::getHomeSideUnitList($uid);
        if (!$homeSide) {
            return -321;
        }

        $enemySide = Hapyfish2_Alchemy_Bll_MapCopy::getEnemySideUnitList($uid, $id);
        if (!$enemySide) {
            return -322;
        }

        //体力不足时不能战斗
        $userSpInfo = Hapyfish2_Alchemy_HFC_User::getUserSp($uid);
        if ($userSpInfo['sp'] < 1) {
            return -323;
        }

        $info['rnd_element'] = $aryRnd;
        $info['home_side'] = $homeSide;
        $info['content'] = array();
        $info['create_time'] = time();

        $usrScene = Hapyfish2_Alchemy_HFC_User::getUserScene($uid);
        $info['enemy_id'] = $usrScene['cur_scene_id'] . '-' . $id;
        $saveDb = (defined('ENABLE_FIGHT_DB_LOG') && ENABLE_FIGHT_DB_LOG);
        //}
        //战斗宣言
        $canTalk = false;
        $aryTalk = array();
        $cntHomeSide = count($homeSide);
        $rndTalkRole = mt_rand(1, $cntHomeSide);
        $idx = 0;
        foreach ($homeSide as $data) {
            $idx++;
            //if ($data['id'] == 0) {
            if ($idx == $rndTalkRole) {
                $talks = Hapyfish2_Alchemy_Cache_Basic::getFightDeclareByJob($data['job']);
                if ($talks) {
                    $rndKey = mt_rand(1, count($talks));
                    $aryTalk[] = array((int) $data['matrix_pos'], $talks[$rndKey - 1]);
                }
                break;
            }
        }

        $newMonsterAry = array();
        $vip = new Hapyfish2_Alchemy_Bll_Vip();
        $vipAddition = array();
        foreach ($enemySide as $key => $data) {
            if ($data['is_boss']) {
                $canTalk = true;
                if (isset($data['talk']) && $data['talk']) {
                    $aryTalk[] = array((int) $data['matrix_pos'], $data['talk']);
                }
            }
            if (!in_array($data['cid'], $newMonsterAry)) {
                //添加遇到怪物记录，并判断是否有首杀奖励
                $isNewMonster = Hapyfish2_Alchemy_HFC_Monster::isNewMonster($uid, $data['cid']);
                if ($isNewMonster) {
                    $newMonsterAry[] = $data['cid'];
                    $data['award_conditions'] = array_merge($data['award_conditions'], $data['first_award_conditions']);
                    $enemySide[$key] = $data;

                    $newHelpId = 0;
                    if ($data['cid'] == 14571) {
                        $newHelpId = 8;
                    }
                    /* else if ( $data['cid'] == 15271 ) {
                      $newHelpId = 16;
                      } */
                    if ($newHelpId > 0) {
                        Hapyfish2_Alchemy_Bll_Help::startHelp($uid, $newHelpId);
                    }
                }
            }
            $vip->setAddition($uid, $data['award_conditions']);
            //添加图鉴
            $illResult = Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $data['tid']);

            /* if ( $illResult['result']['status'] == 1 ) {
              $data['award_conditions'] = array_merge($data['award_conditions'], $data['first_award_conditions']);
              $enemySide[$key] = $data;
              } */
        }
        //非BOSS战，不宣言
        if (!$canTalk) {
            $aryTalk = array();
        }

        $vipAddition = $vip->getAddition();
        //首次遇到怪物记录
        $info['new_monster'] = implode(',', $newMonsterAry);

        //保存初始战斗信息
        $info['enemy_side'] = $enemySide;
        Hapyfish2_Alchemy_Cache_Fight::saveFightInfo($uid, $info, $saveDb);

        //拼接Vo数据返回前端
        //我方
        $roleList1 = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
        //敌方
        $roleList2 = Hapyfish2_Alchemy_Bll_MapCopy::genEnemyRolesVo($enemySide);

        //可援助攻击
        $aryAssist = array();
        $assCnt = 0;
        $extCnt = 0;
        $assistInfo = Hapyfish2_Alchemy_HFC_User::getUserFightAssistInfo($uid);
        $assCnt = $assistInfo['assist_bas_count'];
        $extCnt = $assistInfo['assist_ext_count'];
        $aryAssist = Hapyfish2_Alchemy_Cache_Fight::getFightFriendAssistInfo($uid);
        if (!$aryAssist) {
            $aryAssist = self::getFriendAssistVo($uid);
            Hapyfish2_Alchemy_Cache_Fight::setFightFriendAssistInfo($uid, $aryAssist);
        }
        $skip = $vip->getVipSkip($uid);
        $jumpTime = $skip['max'] - $skip['num'] > 0 ? $skip['max'] - $skip['num'] : 0;
        $invite = Hapyfish2_Alchemy_HFC_User::getTotalInvite($uid);
        $isInvite = 0;
        if ($invite > 0) {
            $isInvite = 1;
        }
        $battle = array(
            'id' => $info['fid'],
            'bgClassName' => 'battlebg.1.Background',
            'roleList' => array_merge($roleList1, $roleList2),
            'talk' => $aryTalk,
//            'friendSkill' => $aryAssist,
            'assCnt' => $assCnt,
            'extCnt' => $extCnt,
            'jumpTimes' => $jumpTime,
            'vipPrize' => $vipAddition,
            'isInvite' => $isInvite
        );
        if ( $info['fid'] == 100 ) {
        	$battle['auto'] = 1;
        }

        //战斗完成消耗体力
        Hapyfish2_Alchemy_HFC_User::decUserSp($uid, 1);

        //$resultVo = array('BattleVo'=>$battle, 'RndNums'=>$info['rnd_element']);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'BattleVo', $battle);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'RndNums', $info['rnd_element']);
        return 1;
    }

    public static function completeFight($uid, $id, $aryAct, $ftRst, $isDebug = false) {
        try {
            $info = Hapyfish2_Alchemy_Cache_Fight::getFightInfo($uid);
            if ($uid == 10050) {
                info_log(json_encode($info), 'fightErr');
                info_log($id, 'fightErrid');
            }
            if ($isDebug) {
                $info = Hapyfish2_Alchemy_Cache_Fight::loadFightInfo($uid, $id);
            }
            if (!$info) {
                throw new Hapyfish2_Alchemy_Bll_Fight_Exception('fight info not found', -8301);
            }

            if ($id != $info['fid']) {
                throw new Hapyfish2_Alchemy_Bll_Fight_Exception('fight id not match', -8302);
            }

            //already completed
            if (!$isDebug && $info['status']) {
                throw new Hapyfish2_Alchemy_Bll_Fight_Exception('fight info status error', -8303);
            }

            if (count($aryAct) > 1000) {
                throw new Hapyfish2_Alchemy_Bll_Fight_Exception('fight proc too long', -8304);
            }

            $aryRandomNum = $info['rnd_element'];
            $homeSide = $info['home_side'];
            $enemySide = $info['enemy_side'];
            if (!$aryAct || !$aryRandomNum || !$homeSide || !$enemySide) {
                throw new Hapyfish2_Alchemy_Bll_Fight_Exception('fight sides data prepare error', -8305);
            }

            //basic info
            $basSkill = Hapyfish2_Alchemy_Cache_Basic::getEffectList();
            foreach ($basSkill as $cid => $data) {
                if ($data['effect']) {
                    $basSkill[$cid]['effect'] = json_decode($data['effect'], true);
                }
            }
            $simulator = new Hapyfish2_Alchemy_Bll_Fight_Simulator();
            $simulator->setBasicSkill($basSkill);
            $simulator->setRandomElements($aryRandomNum);
            $simulator->setActScripts($aryAct);
            //本方
            $simulator->setHomeUnitAry($homeSide);

            //敌方
            $simulator->setEnemyUnitAry($enemySide);

            //拥有道具列表
            $goods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
            $aryItems = array();
            if ($goods) {
                foreach ($goods as $cid => $data) {
                    $aryItems[$cid] = $data['count'];
                }
            }

            $simulator->setItemAry($aryItems);

            //属性元素 相克
            $basRestrict = array();
            $basData = Hapyfish2_Alchemy_Cache_Basic::getFightRestrict();
            $basRestrict['element_pair'] = json_decode($basData['element_pair'], true);
            $basRestrict['job_pair'] = json_decode($basData['job_pair'], true);
            $simulator->setRestrict($basRestrict);

            //援助攻击次数
            $assistInfo = Hapyfish2_Alchemy_HFC_User::getUserFightAssistInfo($uid);
            $assCnt = $assistInfo['assist_bas_count'];
            $extCnt = $assistInfo['assist_ext_count'];
            $simulator->setAssistCount($assCnt + $extCnt);

            //开启详细过程分析日志（调试用）
            $logName = 'fight-proc-' . $uid . '-' . $id;
            if ($isDebug) {
                $logName = 'test-fight-proc-' . $uid . '-' . $id;
            }
            $simulator->enableDetailLog($logName);

            //check
            $simulator->checkValid();

            $rst = $simulator->play();
//return $rst;

            if (!$rst || !isset($rst['result']) || !isset($rst['data'])) {
                throw new Hapyfish2_Alchemy_Bll_Fight_Exception('simulate failed', -8401);
            }

            if ($ftRst != $rst['result']) {
                throw new Hapyfish2_Alchemy_Bll_Fight_Exception('fight result not match', -8402);
            }

            //保存战斗结果及过程
            $info['status'] = $rst['result'];
            $info['content'] = $aryAct;
            $saveDb = (defined('ENABLE_FIGHT_DB_LOG') && ENABLE_FIGHT_DB_LOG);
            Hapyfish2_Alchemy_Cache_Fight::saveFightInfo($uid, $info, $saveDb);

            if ($aryItems) {
                $itemAfter = $rst['data']['item'];
                //使用道具结算
                foreach ($aryItems as $cid => $num) {
                    if (isset($itemAfter[$cid]) && $itemAfter[$cid] < $num) {
                        Hapyfish2_Alchemy_HFC_Goods::useUserGoods($uid, $cid, $num - abs($itemAfter[$cid]));
                    }
                }
            }

            //援助攻击剩余次数更新
            $usedAssistCnt = $simulator->getAssistUsedCount();
            if ($usedAssistCnt) {
                if ($usedAssistCnt > $assCnt) {
                    $assistInfo['assist_bas_count'] = 0;
                    $assistInfo['assist_ext_count'] = $extCnt - ($usedAssistCnt - $assCnt);
                } else {
                    $assistInfo['assist_bas_count'] = $assCnt - $usedAssistCnt;
                }
                if ($assistInfo['assist_ext_count'] < 0) {
                    $assistInfo['assist_ext_count'] = 0;
                }
                Hapyfish2_Alchemy_HFC_User::updateUserFightAssistInfo($uid, $assistInfo);
            }

            //装备耗损处理
            foreach ($rst['data']['corps'] as $data) {
                Hapyfish2_Alchemy_Bll_Weapons::calcDurableAfterFight($uid, $data['id'], $rst['result']);
            }

            //交互战斗处理
            if ($info['type'] == Hapyfish2_Alchemy_Bll_FightOccupy::MODE_AGGRESS
                    || $info['type'] == Hapyfish2_Alchemy_Bll_FightOccupy::MODE_GAINST
                    || $info['type'] == Hapyfish2_Alchemy_Bll_FightOccupy::MODE_SUCCOR) {
                return Hapyfish2_Alchemy_Bll_FightOccupy::completeOccupy($uid, $info, $rst);
            }//对白触发战斗处理
            else if ($info['type'] == Hapyfish2_Alchemy_Bll_FightOccupy::MODE_DIALOG) {
                if ($rst['result'] == Hapyfish2_Alchemy_Bll_Fight_Simulator::RESULT_WIN) {
                    Hapyfish2_Alchemy_Bll_Story::completeFightDialog($uid, $info, $rst);
                }
            }//1V1竞技场战斗处理
            else if ($info['type'] == Hapyfish2_Alchemy_Bll_FightOccupy::MODE_ARENA) {
                Hapyfish2_Alchemy_Bll_Arena::completeFightArena($uid, $info, $rst, $id, $rst['result']);
            }
	     	else if ( $info['type'] == Hapyfish2_Alchemy_Bll_FightOccupy::MODE_WATER ) {
            	$resetExp = Hapyfish2_Alchemy_Bll_Helltower::completeFightWater($uid, $info, $rst, $id, $rst['result']);
            }else if ( $info['type'] == Hapyfish2_Alchemy_Bll_FightOccupy::MODE_ABYSS ) {
            	$resetExp = Hapyfish2_Alchemy_Bll_Helltower::completeFightAbyss($uid, $info, $rst, $id, $rst['result']);
            }

            //统计分析log
            $userLevel = Hapyfish2_Alchemy_HFC_User::getUserLevel($uid);
            $log = Hapyfish2_Util_Log::getInstance();

            $expVo = array();
            $changedMerc = array();
            //胜利时结算
            if ($rst['result'] == Hapyfish2_Alchemy_Bll_Fight_Simulator::RESULT_WIN) {
                //首杀胜利，记录首杀怪物id
                $newMonsterAry = explode(',', $info['new_monster']);
                if (!empty($info['new_monster'])) {
                    foreach ($newMonsterAry as $monster) {
                        Hapyfish2_Alchemy_HFC_Monster::addMonster($uid, $monster);

                        //用户操作记录-首杀怪物记录
                        $actionAry = array('type' => 3, 'cid' => $monster);
                        Hapyfish2_Alchemy_Bll_Stat::addActionLog($uid, $actionAry);
                    }
                }

                //引导战斗不需要清除地图怪物
                if ($info['type'] != 9) {
                    //清除地图怪物
                    $mapMst = explode('-', $info['enemy_id']);
                    $mapId = $mapMst[0];
                    Hapyfish2_Alchemy_Bll_MapCopy::beatMonster($uid, $mapId, $mapMst[1]);
                }
                $corps = $rst['data']['corps'];

                $awardItem = array();
                $monsterExp = array();
                $gpMonster = array();
                $bossMonster = array();
                foreach ($enemySide as $data) {
                	$data['award_conditions'] = !empty($data['award_conditions'])?$data['award_conditions']:array();
                    //怪物掉落物
                    $awardItem = array_merge($awardItem, $data['award_conditions']);
                    //怪物经验值
                    $monsterExp[$data['id']] = array((int) $data['level'], (int) $data['award_exp']);

                    //boss怪
                    if ($data['is_boss']) {
                        $bossMonster[$data['cid']] = 1;
                        //统计log
                        $log->report('222', array($uid, $mapId, $data['cid'], 1));
                        $dayBox = Hapyfish2_Alchemy_Cache_EventGift::getDayFriendBox($uid);
                        if ($dayBox['get'] == 0) {
                            $dayBox['get'] = 1;
                            Hapyfish2_Alchemy_Cache_EventGift::updateFriendBox($uid, $dayBox);
                            Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'friendAwardBox', true);
                        }
                        //platform feed
                        Hapyfish2_Alchemy_Bll_PlatformFeed_Feed::killBoss($uid, $data['cid']);
                    }
                    //怪物组别
                    else {
                        if (array_key_exists($data['tid'], $gpMonster)) {
                            $gpMonster[$data['tid']] += 1;
                        } else {
                            $gpMonster[$data['tid']] = 1;
                        }
                        //统计log
                        $log->report('221', array($uid, $mapId, $data['cid'], 1));
                    }
                }
                //掉落物结算
                $changeResult = array();
                $awardItem = Hapyfish2_Alchemy_Bll_VipWelfare::getFightVipAward($uid, $awardItem);
                Hapyfish2_Alchemy_Bll_MapCopy::awardCondition($uid, $awardItem, $changeResult, 1);

                //触发任务处理
                $event = array('uid' => $uid, 'data' => $changeResult['gain']);
                Hapyfish2_Alchemy_Bll_TaskMonitor::exgStuffGain($event);
                $vip = new Hapyfish2_Alchemy_Bll_Vip();
                $vipInfo = $vip->getVipInfo($uid);
                //非竞技场战斗时，结算 Hp，mp,exp
                if ($info['type'] != Hapyfish2_Alchemy_Bll_FightOccupy::MODE_ARENA) {
                    //hp mp 经验结算
                    foreach ($corps as $pos => $data) {
                        $awardExp = 0;



                        if ($vipInfo['level'] >= 1 && $vipInfo['vipStatus'] == 1) {
                            foreach ($monsterExp as $v) {
                                $awardExp += self::_calcExpByLevDiff($v[1], $v[0] - $data['level']);
                                //info_log($pos.':'.$v[1].':'.($v[0]-$data['level']).':'.$awardExp, 'exp');
                            }
                            $awardExp = round($awardExp);
                            if ($awardExp == 0) {
                                $awardExp = 1;
                            }
                            if ('sinaweibo' == PLATFORM) {
                                $awardExp = Hapyfish2_Platform_Bll_User::getIdentityExp($uid, $awardExp);
                            }
                        } else {
                            //if not dead in fight
                            if ($data['hp'] > 0) {
                                foreach ($monsterExp as $v) {
                                    $awardExp += self::_calcExpByLevDiff($v[1], $v[0] - $data['level']);
                                    //info_log($pos.':'.$v[1].':'.($v[0]-$data['level']).':'.$awardExp, 'exp');
                                }
                                $awardExp = round($awardExp);
                                if ($awardExp == 0) {
                                    $awardExp = 1;
                                }
                                if ('sinaweibo' == PLATFORM) {
                                    $awardExp = Hapyfish2_Platform_Bll_User::getIdentityExp($uid, $awardExp);
                                }
                            }
                        }
                        //self
                        if ($data['id'] == Hapyfish2_Alchemy_Bll_Fight_Simulator::ROLE_SELF) {
                            //update self info
                            $selfInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
                            $addExp = Hapyfish2_Alchemy_Bll_VipWelfare::getVipFightExp($uid, $awardExp);
                            $selfInfo['exp'] += $awardExp + $addExp;
                            $selfInfo['hp'] = self::autoSupply($uid, $selfInfo, $data['hp'], 1, $data['id']);
                            $selfInfo['mp'] = self::autoSupply($uid, $selfInfo, $data['mp'], 2, $data['id']);
                            Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $selfInfo);
                            $userMerc = $selfInfo;
                        }
                        //corps
                        else {
                            //update mercenary info
                            $mercInfo = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $data['id']);
                            $addExp = Hapyfish2_Alchemy_Bll_VipWelfare::getVipFightExp($uid, $awardExp);
                            $mercInfo['exp'] += $awardExp + $addExp;
                            $mercInfo['hp'] = self::autoSupply($uid, $mercInfo, $data['hp'], 1, $data['id']);
                            $mercInfo['mp'] = self::autoSupply($uid, $mercInfo, $data['mp'], 2, $data['id']);
                            Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $data['id'], $mercInfo);
                            $userMerc = $mercInfo;
                        }
                        //check level up
                        $levelUp = Hapyfish2_Alchemy_Bll_Mercenary::checkMercenaryLevelUp($uid, $data['id'], $userMerc);
                        if ($levelUp) {
                            $nextExp = Hapyfish2_Alchemy_Cache_Basic::getMercenaryLevelExp($userMerc['level'] + 1);
                            $uexp = $userMerc['exp'] - $nextExp;
                            $newLevel = (int) $userMerc['level'] + 2;
                            $nextLevelExp = Hapyfish2_Alchemy_Cache_Basic::getMercenaryLevelExp($newLevel);
                            $expVo[] = array($pos, $awardExp, (int) $userMerc['level'] + 1, (int) $uexp, (int) $nextLevelExp, $addExp);
                        } else {
                            $expVo[] = array($pos, $awardExp, 0, 0, 0, $addExp);
                            $userMerc['id'] = (int) $data['id'];
                            $userMerc['matrix_pos'] = $pos;
                            $changedMerc[] = $userMerc;
                        }
                    }//end for 经验结算
                }

                //触发任务处理
                if ($gpMonster) {
                    $event = array('uid' => $uid, 'data' => $gpMonster);
                    //Hapyfish2_Alchemy_Bll_TaskMonitor::killMonster($event);
                }
                if ($bossMonster) {
                    $event = array('uid' => $uid, 'data' => $bossMonster);
                    $event1 = array('uid' => $uid, 'data' => 1);
                    Hapyfish2_Alchemy_Bll_TaskMonitor::killRandomBoss($event);
                    //Hapyfish2_Alchemy_Bll_TaskMonitor::killBoss($event);
                }
                Hapyfish2_Alchemy_Bll_TaskMonitor::killMonster($event);
                $event = array('uid' => $uid, 'data' => 1);
                Hapyfish2_Alchemy_Bll_TaskMonitor::winFight($event);
            }//end if 胜利
            //失败 逃跑 其他 结算
            else {
                //非竞技场战斗时，结算 Hp，mp,exp
                if ($info['type'] != Hapyfish2_Alchemy_Bll_FightOccupy::MODE_ARENA) {
                    $corps = $rst['data']['corps'];
                    foreach ($corps as $pos => $data) {
                        //self
                        if ($data['id'] == Hapyfish2_Alchemy_Bll_Fight_Simulator::ROLE_SELF) {
                            //update self info
                            $selfInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
                            $selfInfo['hp'] = self::autoSupply($uid, $selfInfo, $data['hp'], 1, $data['id']);
                            $selfInfo['mp'] = self::autoSupply($uid, $selfInfo, $data['mp'], 2, $data['id']);
                            Hapyfish2_Alchemy_HFC_FightAttribute::updateInfo($uid, $selfInfo);
                            $selfInfo['id'] = $data['id'];
                            $selfInfo['matrix_pos'] = $pos;
                            $changedMerc[] = $selfInfo;
                        }
                        //corps
                        else {
                            //update mercenary info
                            $mercInfo = Hapyfish2_Alchemy_HFC_FightMercenary::getOne($uid, $data['id']);
                            $mercInfo['hp'] = self::autoSupply($uid, $mercInfo, $data['hp'], 1, $data['id']);
                            $mercInfo['mp'] = self::autoSupply($uid, $mercInfo, $data['mp'], 2, $data['id']);
                            Hapyfish2_Alchemy_HFC_FightMercenary::updateOne($uid, $data['id'], $mercInfo);
                            $mercInfo['id'] = $data['id'];
                            $mercInfo['matrix_pos'] = $pos;
                            $changedMerc[] = $mercInfo;
                        }
                    }

                    foreach ($enemySide as $data) {
                        //boss怪
                        if ($data['is_boss']) {
                            //统计log
                            $log->report('222', array($uid, $mapId, $data['cid'], 0));
                        }
                        //怪物组别
                        else {
                            //统计log
                            $log->report('221', array($uid, $mapId, $data['cid'], 0));
                        }
                    }
                }
            }//end if 失败 逃跑 其他

            $blood = Hapyfish2_Alchemy_HFC_Goods::getBloodVo($uid);
            Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'bloodBank', $blood);
            //$resultVo['confirm'] = 1;
            //$resultVo['exp'] = $expVo;
            if(isset($resetExp)){
            	foreach($expVo as &$v){
            		$v[1] = $resetExp;
            		$count = count($v);
            		$v[$count-1] = 0;
            	}
            }
            Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'confirm', 1);
            Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'exp', $expVo);
        } catch (Hapyfish2_Alchemy_Bll_Fight_Exception $fe) {
            info_log('fight-proc-' . $uid . '-' . $id . 'Error:' . $fe->getCode() . ':' . $fe->getMessage(), 'err-Fight-Simulator');
            Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'confirm', 0);
            return $fe->getCode();
        } catch (Exception $e) {
            //err_log('fight-proc-'.$uid.'-'.$id.'Error:'.':'.$e->getMessage());
            err_log('Hapyfish2_Alchemy_Bll_Fight:completeFight:' . $uid . '-' . $id . ':' . $e->getMessage());
            Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'confirm', 0);
            return -200;
        }

        //新手引导战斗完成后触发剧情
        if ($id == 100) {
            //Hapyfish2_Alchemy_Bll_Story::startStory($uid, 271);
        }

        $resultVo = Hapyfish2_Alchemy_Bll_UserResult::all();
        if (isset($resultVo['rolesChange'])) {
            //佣兵与主角数据
            Hapyfish2_Alchemy_Bll_UserResult::removeField($uid, 'rolesChange');
        }
        $homeSide = Hapyfish2_Alchemy_Bll_FightMercenary::getAllRolesList($uid);
        $rolesChange = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
        Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'rolesChange', $rolesChange);
        return 1;
    }

    /**
     * calculate gain exp through baseExp,levelDiff,3 coefficient number
     *
     * @param int $baseExp
     * @param int $levDiff，怪物等级-佣兵等级
     * @param int $kl1 加成值1
     * @param int $kl2 加成值2
     * @param int $kd1 衰减值1
     * @param int $kd2 衰减值2
     * @param int $max 上限值
     * @param int $min 下限值
     * @return integer
     */
    private static function _calcExpByLevDiff($baseExp, $levDiff) {
        if (!$baseExp) {
            return 0;
        }

        $retExp = $baseExp;
        if ($levDiff == 0) {
            return $retExp;
        }

        $expLevDiff = Hapyfish2_Alchemy_Cache_Basic::getFightExpLevDiff();
        $kl1 = $expLevDiff['kl1'] / 100;  //加成值1
        $kl2 = $expLevDiff['kl2'] / 100;  //加成值2
        $kd1 = $expLevDiff['kd1'] / 100;  //衰减值1
        $kd2 = $expLevDiff['kd2'] / 100;  //衰减值2
        $max = $expLevDiff['max'] / 100;  //上限值
        $min = $expLevDiff['min'] / 100;  //下限值

        if ($levDiff > 0) {
            $retExp = $baseExp * (1 + pow($levDiff, $kl1) * $kl2);
        } else {
            $retExp = $baseExp / (1 + pow(abs($levDiff), $kl1) * $kd2);
        }

        $minExp = $baseExp * $min;
        $maxExp = $baseExp * $max;

        if ($retExp > $maxExp) {
            $retExp = $maxExp;
        }
        if ($retExp < $minExp) {
            $retExp = $minExp;
        }

        /* if ($levDiff > 0) {
          $retExp = $baseExp*(1+($coefNum1/100)*$levDiff);
          }
          else {
          $retExp = $baseExp/(1+($coefNum2/100)*abs($levDiff));
          }

          $minExp = $baseExp*($coefNum3/100);
          $maxExp = $baseExp*($coefNum4/100);

          if ($retExp > $maxExp) {
          $retExp = $maxExp;
          }
          if ($retExp < $minExp) {
          $retExp = $minExp;
          } */
        /* $absLevDiff = abs($levDiff);
          for ($i=1; $i<=$absLevDiff; $i++) {
          $regularCoefNum1 = $i*$coefNum1;
          $regularCoefNum2 = $i*$coefNum2;
          $retExp += ( $baseExp/($coefNum3+$regularCoefNum1)*$levDiff/(abs($levDiff)+$regularCoefNum2) );
          } */

        return $retExp;
    }

    public static function getFriendAssistInvite($uid) {
        $inviteData = Hapyfish2_Alchemy_Bll_InviteLog::get($uid);
        $list = $inviteData['list'];
        $jobAry = array('1' => 0, '2' => 0, '3' => 0);
        if ($list) {
            foreach ($list as $v) {
                $friendRole = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($v['fid']);
                //1-战士，2-弓手，3-法师
                if ($friendRole['job'] == 1) {
                    $jobAry[1]++;
                } else if ($friendRole['job'] == 2) {
                    $jobAry[2]++;
                } else if ($friendRole['job'] == 3) {
                    $jobAry[3]++;
                }
            }
        }

        return $jobAry;
    }

    public static function getFriendAssistVo($uid) {
        $maxLev = 100;
        $basAssist = Hapyfish2_Alchemy_Cache_Basic::getFightAssistanceList();
        $friendJobLevStat = Hapyfish2_Alchemy_Bll_Friend::getFriendFightJobLevSpread($uid, 1, 600);
//echo json_encode($friendJobLevStat);exit;
        $aryAssist = array();

        $assAvatar = 'battle.6.Mf1';
        $assName = '炼金大冒险';

        //begin and try to find the max level assistance skill
        foreach ($basAssist as $assData) {
            //援攻技能等级条件
            $needElement = (int) $assData['element'];
            $needJob = (int) $assData['job'];
            $needLev = (int) $assData['need_lev'];
            $needCnt = (int) $assData['need_count'];
            $needInvite = json_decode($assData['need_invite']);

            //init assist list when lev =1
            if (1 == $assData['level']) {
                if (!empty($needInvite)) {
                    $aryAssist[(int) $assData['tid']] = array('avatar' => $assAvatar, 'name' => $assName, 'lev' => 0,
                        'skillId' => (int) $assData['skill_id'],
                        'next' => array((int) $needInvite[0][1], (int) $assData['need_lev'], (int) $needInvite[0][0], (int) $assData['element']));
                } else {
                    $aryAssist[(int) $assData['tid']] = array('avatar' => $assAvatar, 'name' => $assName, 'lev' => 0,
                        'skillId' => (int) $assData['skill_id'],
                        'next' => array((int) $needCnt, (int) $assData['need_lev'], (int) $needJob, (int) $assData['element']));
                }
            }
            $tid = (int) $assData['tid'];
            $actCnt = 0;
            $assister = '';
            $matched = false;
            //用户邀请信息
            $userInviteInfo = self::getFriendAssistInvite($uid);
            //邀请条件判断
            $invite = true;
            if (!empty($needInvite)) {
                foreach ($needInvite as $needInv) {
                    $needJob = $needInv[0];
                    $needJobCnt = $needInv[1];

                    if ($userInviteInfo[$needJob] < $needJobCnt) {
                        $invite = false;
                    }
                }
            }

            //所有职业（都）
            if ($needJob == 9) {
                $actCntW = $actCntR = $actCntM = 0;
                $requireJE1 = array('1-1', '1-2', '1-3');
                $requireJE2 = array('2-1', '2-2', '2-3');
                $requireJE3 = array('3-1', '3-2', '3-3');
                foreach ($friendJobLevStat as $jobEle => $data1) {
                    if (in_array($jobEle, $requireJE1)) {
                        foreach ($data1 as $lev => $data2) {
                            if ($lev >= $needLev) {
                                $actCntW += $data2[0];
                                if (!$assister || mt_rand(1, 10) == 7) {
                                    $assister = $data2[1];
                                }
                            }
                        }
                    } else if (in_array($jobEle, $requireJE2)) {
                        foreach ($data1 as $lev => $data2) {
                            if ($lev >= $needLev) {
                                $actCntR += $data2[0];
                                if (!$assister || mt_rand(1, 10) == 7) {
                                    $assister = $data2[1];
                                }
                            }
                        }
                    } else if (in_array($jobEle, $requireJE3)) {
                        foreach ($data1 as $lev => $data2) {
                            if ($lev >= $needLev) {
                                $actCntM += $data2[0];
                                if (!$assister || mt_rand(1, 10) == 7) {
                                    $assister = $data2[1];
                                }
                            }
                        }
                    }
                }//end for (stat)
                //符合该援攻技能等级的条件
                if ($actCntW >= $needCnt && $actCntR >= $needCnt && $actCntM >= $needCnt) {
                    $matched = true;
                }
            }
            //各种职业 属性 条件
            else {
                if ($needJob && $needJob < 4 && $needElement && $needElement < 4) {
                    $requireJE = array($needJob . '-' . $needElement);
                } else if ($needJob && $needJob < 4 && !$needElement) {
                    $requireJE = array($needJob . '-1', $needJob . '-2', $needJob . '-3');
                } else if (!$needJob && $needElement && $needElement < 4) {
                    $requireJE = array('1-' . $needElement, '2-' . $needElement, '3-' . $needElement);
                } else {
                    $requireJE = array('1-1', '1-2', '1-3', '2-1', '2-2', '2-3', '3-1', '3-2', '3-3');
                }
                foreach ($friendJobLevStat as $jobEle => $data1) {
                    if (in_array($jobEle, $requireJE)) {
                        foreach ($data1 as $lev => $data2) {
                            if ($lev >= $needLev) {
                                $actCnt += $data2[0];
                                if (!$assister || mt_rand(1, 10) == 7) {
                                    $assister = $data2[1];
                                }
                            }
                        }
                    }
                }//end for (stat)
                //符合该援攻技能等级的条件
                if ($actCnt >= $needCnt) {
                    $matched = true;
                }
            }//end if
            //符合该援攻技能等级的条件
            if ($matched && $invite) {
                if ($assister) {
                    $tmp = explode('|', $assister);
                    $assAvatar = $tmp[0];
                    $assName = $tmp[1];
                }
                $nextId = $tid . ((int) $assData['level'] + 1);
                $nextAssData = array('need_count' => '999', 'need_lev' => '999');
                if (isset($basAssist[$nextId])) {
                    $nextAssData = $basAssist[$nextId];
                }

                $aryAssist[$tid] = array('avatar' => $assAvatar, 'name' => $assName, 'lev' => (int) $assData['level'],
                    'skillId' => (int) $assData['skill_id'],
                    'next' => array((int) $nextAssData['need_count'], (int) $nextAssData['need_lev'], (int) $nextAssData['job'], (int) $nextAssData['element']));
            }
        }//end for

        $ret = array();
        foreach ($aryAssist as $data) {
            $ret[] = $data;
        }
        return $ret;
    }

    //武器装备属性增加值 加到单位属性中
    public static function addWeaponProp(&$unitProp) {
        try {
            if ($unitProp && isset($unitProp['weapon'])) {
                $aryWeapon = $unitProp['weapon'];
                if ($aryWeapon) {
                    $basWeapon = Hapyfish2_Alchemy_Cache_Basic::getWeaponList();
                    $addPhyAtt = $addPhyDef = $addMagAtt = $addMagDef = $addAgi = $addHp = $addMp = $addCrit = $addDodge = 0;
                    foreach ($aryWeapon as $cid) {
                        $weaponInfo = $basWeapon[$cid];
                        if ($weaponInfo) {
                            $addPhyAtt += $weaponInfo['pa'];
                            $addPhyDef += $weaponInfo['pd'];
                            $addMagAtt += $weaponInfo['ma'];
                            $addMagDef += $weaponInfo['md'];
                            $addAgi += $weaponInfo['speed'];
                            $addMp += $weaponInfo['hp'];
                            $addHp += $weaponInfo['mp'];
                            $addCrit += $weaponInfo['cri'];
                            $addDodge += $weaponInfo['dod'];
                        }
                    }
                    $unitProp['phy_att'] += $addPhyAtt;
                    $unitProp['phy_def'] += $addPhyDef;
                    $unitProp['mag_att'] += $addMagAtt;
                    $unitProp['mag_def'] += $addMagDef;
                    $unitProp['agility'] += $addAgi;
                    $unitProp['hp'] += $addHp;
                    $unitProp['mp'] += $addMp;
                    $unitProp['crit'] += $addCrit;
                    $unitProp['dodge'] += $addDodge;
                }
            }
        } catch (Exception $e) {
            info_log('addWeaponProp:failed' . $e->getMessage(), 'Bll_Fight');
        }
        return;
    }

    //自动补给 1为hp 2为mp
    public static function autoSupply($uid, $info, $num, $type, $roleId) {
        $data = 0;
        $pond = Hapyfish2_Alchemy_HFC_Goods::getUserPond($uid);
        $num = $num <= 0 ? 1 : $num;
        if ($type == 1) {
            $last = $pond['hp'];
            $max = $info['hp_max'];
        } else {
            $last = $pond['mp'];
            $max = $info['mp_max'];
        }
        $dec = ($max - $num) > 0 ? ($max - $num) : 0;
        if ($dec > 0) {
            if ($last >= $dec) {
                $last -= $dec;
                $data = $max;
            } else {
                $data = $num + $last;
                $last = 0;
            }
            if ($type == 1) {
                $pond['hp'] = $last;
            } else {
                $pond['mp'] = $last;
            }
            Hapyfish2_Alchemy_HFC_Goods::updateUserPond($uid, $pond);
        } else {
            $data = $num;
        }
        return $data;
    }

    /**
     * 对白触发战斗
     * @param int $uid
     * @param int $detailId,战斗站位id-alchemy_fight_monster_matrix 表
     * @param int $dialogId,对白id
     * @param int $fightType,需求战斗结果,1:必须成功,0:可以失败
     */
    public static function dialogFight($uid, $detailId, $dialogId, $fightType) {
        $info = array();
        $info['uid'] = $uid;
        $info['fid'] = Hapyfish2_Alchemy_Bll_Fight::getNewId($uid);
        $info['type'] = 8;
        $info['status'] = 0;

        $aryRnd = array();
        for ($i = 0; $i < 20; $i++) {
            $aryRnd[] = mt_rand(1, 1000);
        }

        //本方
        $homeSide = Hapyfish2_Alchemy_Bll_FightCorps::getHomeSideUnitList($uid);
        if (!$homeSide) {
            return -321;
        }

        //敌方
        $basMonsterMatrix = Hapyfish2_Alchemy_Cache_Basic::getFightMonsterMatixList();
        //$detailId = 20;
        if (!isset($basMonsterMatrix[$detailId])) {
            return -322;
        }
        $detail = json_decode($basMonsterMatrix[$detailId]['matrix'], true);
        if (!$detail) {
            return -322;
        }

        //basic monster info
        $basMonster = Hapyfish2_Alchemy_Cache_Basic::getMonsterList();
        $id = 1;
        foreach ($detail as $pos => $cid) {
            $posMonster[$pos] = array('id' => $id, 'cid' => $cid);
            $id++;
        }
        $enemySide = array();
        foreach ($posMonster as $pos => $data) {
            $enemyInfo = array();
            $monsterInfo = $basMonster[$data['cid']];
            if ($monsterInfo) {
                $monsterInfo['id'] = (int) $data['id'];
                $monsterInfo['matrix_pos'] = (int) $pos;
                $monsterInfo['hp_max'] = (int) $monsterInfo['hp'];
                $monsterInfo['mp_max'] = (int) $monsterInfo['mp'];
                $monsterInfo['skill'] = json_decode($monsterInfo['skill'], true);
                $monsterInfo['weapon'] = json_decode($monsterInfo['weapon'], true);
                $monsterInfo['award_conditions'] = Hapyfish2_Alchemy_Bll_MapCopy::_preCalcAwardCondition($monsterInfo['award_conditions']);
                unset($monsterInfo['content']);
                unset($monsterInfo['avatar_class_name']);

                //add weapon prop to attribute prop
                Hapyfish2_Alchemy_Bll_Fight::addWeaponProp($monsterInfo);
                $enemySide[$pos] = $monsterInfo;
            }
        }

        if (!$enemySide) {
            return -322;
        }

        $info['rnd_element'] = $aryRnd;
        $info['home_side'] = $homeSide;
        $info['enemy_side'] = $enemySide;
        $info['content'] = array();
        $info['create_time'] = time();

        $info['enemy_id'] = $fightType . '-' . $dialogId;
        $saveDb = (defined('ENABLE_FIGHT_DB_LOG') && ENABLE_FIGHT_DB_LOG);
        Hapyfish2_Alchemy_Cache_Fight::saveFightInfo($uid, $info, $saveDb);

        //拼接Vo数据返回前端
        //我方
        $roleList1 = Hapyfish2_Alchemy_Bll_FightCorps::genHomeRolesVo($uid, $homeSide);
        //敌方
        $roleList2 = Hapyfish2_Alchemy_Bll_MapCopy::genEnemyRolesVo($enemySide);

        //战斗宣言
        $aryTalk = array();
        $cntHomeSide = count($homeSide);
        $rndTalkRole = mt_rand(1, $cntHomeSide);
        $idx = 0;
        foreach ($homeSide as $data) {
            $idx++;
            if ($idx == $rndTalkRole) {
                $talks = Hapyfish2_Alchemy_Cache_Basic::getFightDeclareByJob($data['job']);
                if ($talks) {
                    $rndKey = mt_rand(1, count($talks));
                    $aryTalk[] = array((int) $data['matrix_pos'], $talks[$rndKey - 1]);
                }
                break;
            }
        }

        foreach ($enemySide as $data) {
            if ($data['is_boss']) {
                if ($data['talk']) {
                    $aryTalk[] = array((int) $data['matrix_pos'], $data['talk']);
                }
            }

            //添加图鉴
            Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $data['tid']);
        }

        $battle = array(
            'id' => $info['fid'],
            'bgClassName' => 'battlebg.1.Background',
            'roleList' => array_merge($roleList1, $roleList2),
            'talk' => $aryTalk,
//            'friendSkill' => array(),
            'assCnt' => 0,
            'extCnt' => 0
        );

        $result = array('BattleVo' => $battle,
            'RndNums' => $info['rnd_element']);

        return $result;
    }

}
