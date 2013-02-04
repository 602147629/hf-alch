<?php

require_once 'Hapyfish2/Alchemy/Bll/Fight/Exception.php';

/**
 * 1v1 Fight simulator
 *
 * @copyright  Copyright (c) 2010 HapyFish
 * @create      2012/03    zx
 */
class Hapyfish2_Alchemy_Bll_Fight_Simulator
{

    const BASE_COEFFICIENT = 1;//基础系数
    const BASE_CRIT_MULTI = 1.5;//基础暴击值倍率

    const ROLE_SELF = 0;
    const ROLE_ENEMY = 1;
    const ROLE_HOME = 2;
    const ROLE_BOSS = 3;

    //1-Warrior 2-Rogue 3-Magus
    const JOB_Warrior = 1;
    const JOB_Rogue = 2;
    const JOB_Magus = 3;

    const PROCESS_STARTED = 0;
    const PROCESS_ENDED = 1;
    const PROCESS_ENDED_RUN = 3;

    const STATUS_DEAD = 0;
    const STATUS_ALIVE = 1;

    const RESULT_WIN = 1;
    const RESULT_LOSE = 2;
    const RESULT_RUN = 3;
    const RESULT_OTHER = 4;

    const MATRIX_ENEMY_MIN = 0;
    const MATRIX_ENEMY_MAX = 8;
    const MATRIX_HOME_MIN = 9;
    const MATRIX_HOME_MAX = 17;

    const OPERATE_ATTACK = '-';
    const OPERATE_CURE = '+';
    const OPERATE_DEFENCE = 'x';
    const OPERATE_RUNAWAY = '!';
    const OPERATE_ASSIST = '#';
    
    const ACT_TYPE_PHY = 1;
    const ACT_TYPE_SKL = 2;
    const ACT_TYPE_ITM = 3;

    const SKL_AFFECT_RAT = 0;//效果值 是一个倍率值
    const SKL_AFFECT_ABS = 1;//效果值 直接使用


    //1-单体 2-直线行 3-直线列 4-十字 5-全体
    const EFFECT_AREA_SIG = 1;
    const EFFECT_AREA_ROW = 2;
    const EFFECT_AREA_COL = 3;
    const EFFECT_AREA_CRS = 4;
    const EFFECT_AREA_ALL = 5;


    const EFFECT_PROP_POSITIVE = 1;//1:正面状态
    const EFFECT_PROP_NEGATIVE = 2;//2:负面状态

    const EFFECT_TYPE_ATTACK = 1;//普通攻击
    const EFFECT_TYPE_CHANGE_HP = 2;//伤害或加血
    const EFFECT_TYPE_CHANGE_MP = 3;//烧蓝或加蓝
    const EFFECT_TYPE_ADD_CHANGE_HP_STATUS = 4;//增加影响HP状态
    const EFFECT_TYPE_ADD_CHANGE_MP_STATUS = 5;//增加影响MP状态
    const EFFECT_TYPE_ADD_CHANGE_PHYATT_STATUS = 6;//增加影响物攻状态
    const EFFECT_TYPE_ADD_CHANGE_PHYDEF_STATUS = 32;//技能增加影响物防状态
    const EFFECT_TYPE_ADD_CHANGE_MAGATT_STATUS = 8;//增加影响魔攻状态
    const EFFECT_TYPE_ADD_CHANGE_MAGDEF_STATUS = 9;//增加影响魔防状态
    const EFFECT_TYPE_ADD_CHANGE_DODGE_STATUS = 10;//增加影响闪躲状态
    const EFFECT_TYPE_ADD_CHANGE_CRIT_STATUS = 11;//增加影响暴击状态

    const EFFECT_TYPE_ADD_PETRIFACTION_STATUS = 12;//增加石化状态
    const EFFECT_TYPE_ADD_SLEEP_STATUS = 13;//增加睡眠状态
    const EFFECT_TYPE_ADD_CONFUSION_STATUS = 14;//增加混乱状态

    const EFFECT_TYPE_REMOVE_ALL_STATUS = 15;//消除所有状态
    const EFFECT_TYPE_REMOVE_ALL_POSITIVE_STATUS = 16;//消除所有正面状态
    const EFFECT_TYPE_REMOVE_ALL_NEGATIVE_STATUS = 17;//消除所有负面状态

    const EFFECT_TYPE_REMOVE_CHANGE_HP_STATUS = 18;//消除影响HP状态
    const EFFECT_TYPE_REMOVE_CHANGE_MP_STATUS = 19;//消除影响MP状态
    const EFFECT_TYPE_REMOVE_CHANGE_PHYATT_STATUS = 20;	//消除影响物攻状态
    const EFFECT_TYPE_REMOVE_CHANGE_PHYDEF_STATUS = 21;//消除影响物防状态
    const EFFECT_TYPE_REMOVE_CHANGE_MAGATT_STATUS = 22;//消除影响魔攻状态
    const EFFECT_TYPE_REMOVE_CHANGE_MAGDEF_STATUS = 23;//消除影响魔防状态
    const EFFECT_TYPE_REMOVE_CHANGE_DODGE_STATUS = 24;//消除影响闪躲状态
    const EFFECT_TYPE_REMOVE_CHANGE_CRIT_STATUS = 25;//消除影响暴击状态

    const EFFECT_TYPE_REMOVE_PETRIFACTION_STATUS = 26;//消除石化状态
    const EFFECT_TYPE_REMOVE_SLEEP_STATUS = 27;//消除睡眠状态
    const EFFECT_TYPE_REMOVE_CONFUSION_STATUS = 28;//消除混乱状态

    const EFFECT_TYPE_STEAL = 29;//偷窃
    const EFFECT_TYPE_REVIVE = 30;//复活
    const EFFECT_TYPE_SEAL = 31;//封印（技能使用不能）
	const EFFECT_TYPE_ADD_CHANGE_BY_SKILL = 7;//增加影响物防状态
	const EFFECT_TYPE_ADD_SEPARATE_BY_SKILL = 33;//增加影响物防状态
	const EFFECT_TYPE_ADD_FROZEN_BY_SKILL = 34;//冰冻
	/**
     * 技能/物品 效果 基础信息描述
     * key- cid val- data
     */
    protected $_basSkillList;

    /**
     * 职业 属性 相克 基础信息描述
     * key- element_pair/job_pair val- array
     */
    protected $_basRestrict;


    /**
     * 战斗回合队列
     * ['-',10,7,1,0,20] ['+',10,11,2,2001]
     */
    protected $_actList;

    /**
     * 随机数队列
     * val- 1~1000
     */
    protected $_rndElement;

    /**
     * 随机数队列数
     */
    protected $_cntRndElement;

    /**
     * 当前随机数指针
     */
    protected $_rndIterate;

    /**
     * 阵型站位矩阵 array
	 * key- 0~17 val-instanceId(0-self, {1_id}-enemy, {2_id}-home)
     * enemy:	 0  1  2
     * 			 3  4  5
     * 			 6  7  8
     * home:	 9 10 11
     * 			12 13 14
     * 			15 16 17
     */
    protected $_lineupMatrix;

    /**
     * 存活or死亡状态矩阵 array
	 * key- 0~17 val-status(0-dead 1-alive)
     * enemy:	 0  1  2
     * 			 3  4  5
     * 			 6  7  8
     * home:	 9 10 11
     * 			12 13 14
     * 			15 16 17
     */
    protected $_aliveMatrix;

    /**
     * 异常状态矩阵 array
	 * key- 0~17 val- Array.<EffectVo>
     * enemy:	 0  1  2
     * 			 3  4  5
     * 			 6  7  8
     * home:	 9 10 11
     * 			12 13 14
     * 			15 16 17
     */
    protected $_extStatusMatrix;

   	/**
     * 拥有道具列表
     * key- cid val- count
     */
    protected $_itemList;

    /**
     * 拥有援助攻击次数
     * integer
     */
    protected $_assistCount;

    /**
     * 已使用援助攻击次数
     * integer
     */
    protected $_assistUsedCount;


    /**
     * 战斗单位属性array
     * key- instanceId(0-self, {1_id}-enemy, {2_id}-home) val- array
     */
    protected $_unitList;

    /**
     * 行动顺序队列 array
     * key-行动顺序  val-array('pos'=> , 'speed'=>)
     */
    protected $_actSequence;

    /**
     * 行动顺序队列数
     */
    protected $_cntActSequence;

    /**
     * 行动指针
     */
    protected $_actIterate;

    /**
     * 当前回合数
     */
    protected $_roundNo;

    /**
     * 战斗是否结束标志位   0-进行中 1-结束
     */
    protected $_ended;

    /**
     * 战斗结果 数据返回
     * array('result'=>, 'data'=>)
     */
    protected $_retResult;

    protected $_detailLog;

    /**
     * construct
     * @param
     * @return void
     */
    public function __construct()
    {
        $this->_detailLog = '';
    }

    /**
     * 设置我方战斗单位
     * @param array $data(id,matrix_pos,job,element,level,hp,hp_max,mp,mp_max,phy_att,phy_def,mag_att,mag_def,agility,crit,dodge,weapon,skill)
     * @return void
     */
    public function setHomeUnit($data)
    {
        if ($data) {
            $pos = $data['matrix_pos'];
            $id = $data['id'];
            if ($id != self::ROLE_SELF) {
                $id = self::ROLE_HOME . '_' . $data['id'];
            }
            if ($pos < self::MATRIX_HOME_MIN || $pos > self::MATRIX_HOME_MAX) {
                throw new Hapyfish2_Alchemy_Bll_Fight_Exception('home_pos_not_allow:'.$pos, -8001);
            }

            //set lineup postion matrix
            $this->_lineupMatrix[$pos] = $id;
            $this->_aliveMatrix[$pos] = self::STATUS_ALIVE;
            $this->_extStatusMatrix[$pos] = array();
            //set unitAttribute info
            $this->_unitList[$id] = $data;
        }
    }

	/**
     * 设置敌方战斗单位
     * @param array $data(id,matrix_pos,job,element,level,level,hp,hp_max,mp,mp_max,phy_att,phy_def,mag_att,mag_def,agility,crit,dodge,size_x,size_y,size_z,is_boss,weapon,skill,award_conditions
     * @return void
     */
    public function setEnemyUnit($data)
    {
        if ($data) {
            $pos = $data['matrix_pos'];
            $id = self::ROLE_ENEMY . '_' . $data['id'];
            if ($pos < self::MATRIX_ENEMY_MIN || $pos > self::MATRIX_ENEMY_MAX) {
                throw new Hapyfish2_Alchemy_Bll_Fight_Exception('enemy_pos_not_allow:'.$pos, -8002);
            }

            //set lineup postion matrix
            $this->_lineupMatrix[$pos] = $id;
            $this->_aliveMatrix[$pos] = self::STATUS_ALIVE;;
            $this->_extStatusMatrix[$pos] = array();
            //set unitAttribute info
            $this->_unitList[$id] = $data;
        }
    }

	/**
     * 设置我方战斗单位
     * @param array $aryData
     * @return void
     */
    public function setHomeUnitAry($aryData)
    {
        foreach ($aryData as $data) {
            $this->setHomeUnit($data);
        }
    }

    /**
     * 设置敌方战斗单位
     * @param array $aryData
     * @return void
     */
    public function setEnemyUnitAry($aryData)
    {
        foreach ($aryData as $data) {
            $this->setEnemyUnit($data);
        }
    }

	/**
     * 设置计算用随机数列表
     * @param array $aryRandomNum
     * @return void
     */
    public function setRandomElements($aryRandomNum)
    {
        $this->_rndElement = $aryRandomNum;
        $this->_cntRndElement = count($aryRandomNum);
    }


    /**
     * 设置技能基础表
     * @param array $aryBasic
     * @return void
     */
    public function setBasicSkill($aryBasicSkill)
    {
        $this->_basSkillList = $aryBasicSkill;
    }

    /**
     * 设置相克性基础表
     * @param array $aryData
     * @return void
     */
    public function setRestrict($aryData)
    {
        $this->_basRestrict = $aryData;
    }

    /**
     * 设置背包道具列表
     * @param array $aryItem
     * @return void
     */
    public function setItemAry($aryItem)
    {
        $this->_itemList = $aryItem;
    }

    /**
     * 设置援助攻击次数
     * @param integer $count
     * @return void
     */
    public function setAssistCount($count)
    {
        $this->_assistCount = $count;
    }

    /**
     * 设置已使用的援助攻击次数
     * @param integer $count
     * @return void
     */
    public function setAssistUsedCount($count)
    {
        $this->_assistUsedCount = $count;
    }
	/**
     * 取得已使用的援助攻击次数
     * @return integer
     */
    public function getAssistUsedCount()
    {
        return $this->_assistUsedCount;
    }

    /**
     * 设置单位行动脚本列表
     * @param array $aryAct
     * @return void
     */
    public function setActScripts($aryAct)
    {
        $this->_actList = $aryAct;
    }

	/**
     * 启用详细分析辅助日志
     * @param string $logName
     * @return void
     */
    public function enableDetailLog($logName)
    {
        $this->_detailLog = $logName;
    }

    /**
     * 验证合法性
     * @return void
     */
    public function checkValid()
    {
        $rst = $this->_checkProtocolData();
        if ($rst == 1) {
            $this->_prepareData();
            $rst = $this->_checkFakeData();
        }

        if ($rst != 1) {
            throw new Hapyfish2_Alchemy_Bll_Fight_Exception('valid failed', $rst);
        }

        return $rst;
    }

    /**
     * 播放验证战斗过程
     * @return void
     */
    public function play()
    {
        $this->_logStep('Fight begin:==========');
        $this->_logStep("\n>>>>> ROUND $this->_roundNo >>>>>");

        $actList = $this->_actList;
        $actCount = count($actList);

        $idx = 0;
        //while ($idx<$actCount) {
        while (!$this->_ended && $idx<$actCount+1) {
            //get sequence pos
            $atterPos = $this->_actSequence[$this->_actIterate]['pos'];

            //非首回合
            if ($idx) {
                //行动者角色状态确认
                //是否已死
                if ($this->_aliveMatrix[$atterPos] == self::STATUS_DEAD) {
                    $this->_goNextAct();
                    //$idx ++;
                    continue;
                }

                //异常状态、技能效果结算
                if ($this->_extStatusMatrix[$atterPos]) {
                    $skip = $this->_extStatusDeal($atterPos);
                    //有-hp操作，是否应该结束战斗
                    if ($this->_ended == self::PROCESS_ENDED) {
                        return $this->_concludeResult();
	                }

	                //无法行动状态
	                if ($skip) {
	                    $this->_goNextAct();
	                    //$idx ++;
                        continue;
	                }
                }
            }

            $data = $actList[$idx];
            if ($data[1] != $atterPos) {
                $errMsg = json_encode($data);
                $errMsg.= "\n".json_encode($this->_actSequence);
                throw new Hapyfish2_Alchemy_Bll_Fight_Exception('act sequence error:'.$errMsg, -8003);
                //$atterPos = $data[1];
            }
            $operate = $data[0];
            $idx ++;

            //行动者操作
            switch ($operate) {
                case self::OPERATE_ASSIST :
                    //协助攻击
                    $deferPos = $data[2];
                    $attType = $data[3];
                    $skillCid = $data[4];
                    //if ($this->_lineupMatrix[$atterPos] != self::ROLE_SELF) {
                    //    throw new Hapyfish2_Alchemy_Bll_Fight_Exception('assist att pos not allow:', -8005);
                    //}
                    $this->_logStep('ASSIST:'.$atterPos.'->'.$deferPos . '('.$skillCid.')');
                    $this->_sklAtt($atterPos, $deferPos, $skillCid, true);

                    //有-hp操作，是否应该结束战斗
                    if ($this->_ended == self::PROCESS_ENDED) {
                        return $this->_concludeResult();
	                }
	                continue 2;
                    //break;

                case self::OPERATE_ATTACK :
                    //攻击型
                    $deferPos = $data[2];
                    $attType = $data[3];
                    //physical attack
                    if ($attType == self::ACT_TYPE_PHY) {
                        $this->_phyAtt($atterPos, $deferPos);
                    }
                    //skill attack
                    else if ($attType == self::ACT_TYPE_SKL) {
                        $skillCid = $data[4];
                        $this->_sklAtt($atterPos, $deferPos, $skillCid);
                    }
                    //item attack
                    else if ($attType == self::ACT_TYPE_ITM) {
                        $itemCid = $data[4];
                        $this->_itmAtt($atterPos, $deferPos, $itemCid);
                    }

                    //有-hp操作，是否应该结束战斗
                    if ($this->_ended == self::PROCESS_ENDED) {
                        return $this->_concludeResult();
	                }

                    break;

                case self::OPERATE_CURE :
                    //治疗型
                    $deferPos = $data[2];
                    $cureType = $data[3];
                    if ($cureType == self::ACT_TYPE_SKL) {
                        $skillCid = $data[4];
                        $this->_sklAtt($atterPos, $deferPos, $skillCid);
                    }
                    else if ($attType == self::ACT_TYPE_ITM) {
                        $itemCid = $data[4];
                        $this->_itmAtt($atterPos, $deferPos, $itemCid);
                    }

                    break;

                case self::OPERATE_DEFENCE :
                    //防御
                    $this->_defence($atterPos);
                    break;

                case self::OPERATE_RUNAWAY:
                    //逃跑
                    $this->_runAway($atterPos);
                    //有逃跑操作，是否应该结束战斗
                    if ($this->_ended == self::PROCESS_ENDED_RUN) {
                        return $this->_concludeResult();
	                }
                    break;

//                case self::OPERATE_ASTRICT :
//                    //无法行动
//                    break;

                default:
                    //null

            }//end switch

            $this->_goNextAct();
        }

        //循环操作走完，战斗未结束
        return $this->_concludeResult();
    }

    /**
     * 自动模拟战斗过程
     * @return void
     */
    public function simulaFight()
    {

        //A(10) 物理攻击 B(7)造成伤害20 ['-',10,7,1,0,20]
        $aryAct = $this->_actList;
        foreach ($aryAct as $idx=>$data) {
            $operate = $data[0];
            $atterPos = $data[1];
            //非首回合
            if ($idx) {
                //行动者角色状态确认
                //是否已死
                if ($this->_aliveMatrix[$atterPos] == self::STATUS_DEAD) {
                    $this->_goNextAct();
                    continue;
                }

                //异常状态、技能效果结算
                if ($this->_extStatusMatrix[$atterPos]) {
                    $skip = $this->_extStatusDeal($atterPos);
                    //有-hp操作，是否应该结束战斗
                    if ($this->_ended == self::PROCESS_ENDED) {
                        return $this->_concludeResult();
	                }

	                //无法行动状态
	                if ($skip) {
	                    $this->_goNextAct();
                        continue;
	                }
                }
            }

            //行动者操作
            switch ($operate) {
                case self::OPERATE_ATTACK:
                    //攻击型
                    $deferPos = $data[2];
                    $attType = $data[3];
                    //physical attack
                    if ($attType == self::ACT_TYPE_PHY) {
                        $this->_phyAtt($atterPos, $deferPos);
                    }
                    //skill attack
                    else if ($attType == self::ACT_TYPE_SKL) {
                        $skillCid = $data[4];
                        $this->_sklAtt($atterPos, $deferPos, $skillCid);
                    }
                    //item attack
                    else if ($attType == self::ACT_TYPE_ITM) {
                        $itemCid = $data[4];
                        $this->_itmAtt($atterPos, $deferPos, $itemCid);
                    }

                    //有-hp操作，是否应该结束战斗
                    if ($this->_ended == self::PROCESS_ENDED) {
                        return $this->_concludeResult();
	                }

                    break;
                case self::OPERATE_CURE:
                    //治疗型
                    $deferPos = $data[2];
                    $cureType = $data[3];
                    if ($cureType == self::ACT_TYPE_SKL) {
                        $skillCid = $data[4];
                        $this->_sklAtt($atterPos, $deferPos, $skillCid);
                    }
                    else if ($attType == self::ACT_TYPE_ITM) {
                        $itemCid = $data[4];
                        $this->_itmAtt($atterPos, $deferPos, $itemCid);
                    }

                    break;
                case self::OPERATE_DEFENCE:
                    //防御
                    $this->_defence($atterPos);
                    break;
                case self::OPERATE_RUNAWAY:
                    //逃跑
                    $this->_runAway($atterPos);
                    //有逃跑操作，是否应该结束战斗
                    if ($this->_ended == self::PROCESS_ENDED_RUN) {
                        return $this->_concludeResult();
	                }
                    break;
//                case self::OPERATE_ASTRICT:
//                    //无法行动
//
//                    break;
                case self::OPERATE_ASSIST:
                    //协助攻击

                    //有-hp操作，是否应该结束战斗
                    if ($this->_ended == self::PROCESS_ENDED) {
                        return $this->_concludeResult();
	                }
                    break;

                default:
                    //null

            }//end switch

            $this->_goNextAct();
        }//end for

	    //循环操作走完，战斗未结束
        return $this->_concludeResult();

    }

    protected function _goNextAct()
	{
        $this->_logStep('--pos'.$this->_actSequence[$this->_actIterate]['pos'].' act over--');
	    $cntNum = $this->_cntActSequence;
        if ($this->_actIterate >= ($cntNum-1)) {
            $this->_actIterate = 0;
            $this->_roundNo ++;
            $this->_logStep("\n>>>>> ROUND $this->_roundNo >>>>>");
        }
        else {
            $this->_actIterate ++;
        }
	}

    protected function _rndNumUsed()
	{
	    $this->_logStep("*RandomNum Used[$this->_rndIterate]: ".$this->_rndElement[$this->_rndIterate]." *");
        $cntNum = $this->_cntRndElement;
        if ($this->_rndIterate >= ($cntNum-1)) {
            $this->_rndIterate = 0;
        }
        else {
            $this->_rndIterate ++;
        }
	}

	protected function _prepareData()
	{

        $this->_prepareActSequence();
        $this->_roundNo = 1;
        $this->_actIterate = 0;
        $this->_rndIterate = 0;
        $this->_ended = self::PROCESS_STARTED;
	}

	protected function _prepareActSequence()
	{
	    if (!$this->_unitList) {
	        throw new Hapyfish2_Alchemy_Bll_Fight_Exception('unit_list_not_set', -8004);
	    }

	    $arySpeed = array();
	    foreach ($this->_unitList as $id=>$data) {
	        $pos = $data['matrix_pos'];
            $arySpeed[] = array('pos' => $pos, 'speed' => $data['agility']);
	    }

	    $keySpeed = array();
	    $keyPos = array();
	    foreach ($arySpeed as $key=>$data) {
            $keySpeed[$key] = $data['speed'];
            $keyPos[$key] = $data['pos'];
	    }

	    array_multisort($keySpeed, SORT_DESC, $keyPos, SORT_DESC, $arySpeed);

	    $this->_actSequence = $arySpeed;
	    $this->_cntActSequence = count($this->_actSequence);
	}

	/**
     * 普通物理攻击
     * @param  int $actorPos
     * @param  int $targetPos
     * @return void
     */
    protected function _phyAtt($actorPos, $targetPos)
	{
	    $logAttMeth = 'PHYATT';
        $actId = $this->_lineupMatrix[$actorPos];
        $tarId = $this->_lineupMatrix[$targetPos];

        //目标方是否已死
        if ($this->_aliveMatrix[$targetPos] == self::STATUS_DEAD) {
            throw new Hapyfish2_Alchemy_Bll_Fight_Exception('target_had_dead:'.$targetPos, -8202);
            return;
        }

        //begin attack
        $actor = $this->_unitList[$actId];
        $target = $this->_unitList[$tarId];

        //双方 特殊效果加成计算
        $actor = $this->_calcExtStatus($actor);
        if ($actorPos != $targetPos) {
            $target = $this->_calcExtStatus($target);
        }

        //是否闪避
        if ($this->_isDodge($actor['hit'], $target['dodge'])) {
            $this->_logStep($logAttMeth.':'.$actorPos.'->'.$targetPos . ' MISS');
            return;
        }

        //是否暴击
        $isCrit = $this->_isCrit($actor['crit'], $target['tou']);

        $attVal = $actor['phy_att'];
        if ($isCrit) {
            $attVal = $attVal*self::BASE_CRIT_MULTI;
            $logAttMeth .= '(c)';
        }

        //相克性修正率
        $jobPair = $actor['job'] . '-' . $target['job'];
        $elementPair = $actor['element'] . '-' . $target['element'];
        $adjust = (int)$this->_basRestrict['job_pair'][$jobPair] + (int)$this->_basRestrict['element_pair'][$elementPair];
        //伤害值计算
        $hurtVal = $this->_calcHurtVal($attVal, $target['phy_def'], $adjust);
        $this->_logStep($logAttMeth.':'.$actorPos.'->'.$targetPos . ' hp:'.$target['hp'].'-'.$hurtVal.'('.$attVal.','.$target['phy_def'].')');

        //deal target hp
        $this->_decHp($tarId, $hurtVal);

        return;
	}

	/**
     * 技能攻击
     * @param  int $actorPos
     * @param  int $targetPos
     * @param  int $cid
     * @param  boolean $isAssist
     * @return void
     */
    protected function _sklAtt($actorPos, $targetPos, $cid, $isAssist = false)
	{
	    $logAttMeth = 'SKLATT';

	    /*//目标方是否已死
        if ($this->_aliveMatrix[$targetPos] == self::STATUS_DEAD) {
            throw new Hapyfish2_Alchemy_Bll_Fight_Exception('target_had_dead:'.$targetPos, -8202);
            return;
        }*/

		if (!isset($this->_basSkillList[$cid])) {
		    throw new Hapyfish2_Alchemy_Bll_Fight_Exception('skill_cid_notfound:'.$cid, -8203);
            return;
        }

        //技能效果描述
        $basInfo = $this->_basSkillList[$cid];

        //攻击方 特殊效果加成计算
        $actor = $this->_calcExtStatus($actor);

        $actId = $this->_lineupMatrix[$actorPos];
        $actor = $this->_unitList[$actId];
        if (!$isAssist) {
    	    //是否装备该技能
            if (!in_array($cid, $actor['skill'])) {
                throw new Hapyfish2_Alchemy_Bll_Fight_Exception('skill_not_own:'.$cid, -8204);
                return;
            }

            //检查mp值是否足够
            if ($actor['mp'] < $basInfo['mp']) {
                throw new Hapyfish2_Alchemy_Bll_Fight_Exception('mp_not_enough:'.$actorPos, -8205);
                return;
            }
            $this->_decMp($actId, $basInfo['mp']);
        }

        //begin attack
        //受到范围攻击的单位位置
        $affPosAry = $this->_calcAffectPos($targetPos, $basInfo['area']);
        $this->_logStep('SKL('.$cid.'):AffPos:'. json_encode($affPosAry));
        foreach ($affPosAry as $idx=>$pos) {
	        if ($this->_aliveMatrix[$pos] == self::STATUS_DEAD) {
	            continue;
	        }
            //$tarId = $this->_lineupMatrix[$pos];
            //$target = $this->_unitList[$tarId];
            $this->_addExtStatus($pos, $cid, $actor);

        }//end foreach

        return;
	}

	/**
     * 道具攻击
     * @param  int $actorPos
     * @param  int $targetPos
     * @param  int $itemCid
     * @return void
     */
    protected function _itmAtt($actorPos, $targetPos, $itemCid)
	{
	    $logAttMeth = 'ITM';

        /*//目标方是否已死  有复活道具的原因 先注释掉
        if ($this->_aliveMatrix[$targetPos] == self::STATUS_DEAD) {
            throw new Hapyfish2_Alchemy_Bll_Fight_Exception('target_had_dead:'.$targetPos, -8202);
            return;
        }*/

        if (!isset($this->_basSkillList[$itemCid])) {
            throw new Hapyfish2_Alchemy_Bll_Fight_Exception('item_cid_notfound:'.$itemCid, -8203);
            return;
        }

	    //是否有道具
        if (!isset($this->_itemList[$itemCid]) || $this->_itemList[$itemCid]<=0) {
            throw new Hapyfish2_Alchemy_Bll_Fight_Exception('item_not_enough:'.$itemCid, -8206);
            return;
        }

        //begin attack
        $actId = $this->_lineupMatrix[$actorPos];
        $actor = $this->_unitList[$actId];

        //技能效果描述
        $basInfo = $this->_basSkillList[$itemCid];

        //攻击方 特殊效果加成计算
        $actor = $this->_calcExtStatus($actor);

        //use item
        $this->_itemList[$itemCid] -= 1;

        //begin attack
        //受到范围攻击的单位位置
        $affPosAry = $this->_calcAffectPos($targetPos, $basInfo['area']);
        $this->_logStep('ITM('.$itemCid.'):AffPos:'. json_encode($affPosAry));
        foreach ($affPosAry as $idx=>$pos) {
            //$tarId = $this->_lineupMatrix[$pos];
            //$target = $this->_unitList[$tarId];
            $this->_addExtStatus($pos, $itemCid, $actor);

        }//end foreach

        return;
	}

	/**
     * 防御
     * @param  int $pos
     * @return void
     */
    protected function _defence($pos)
	{
	    $logAttMeth = 'DEF';
        $actId = $this->_lineupMatrix[$pos];
        $actor = $this->_unitList[$actId];
        $this->_addExtStatus($pos, 0, $actor);
        $this->_logStep($logAttMeth.':'.$pos);
	    return;
	}


	/**
     * 逃跑
     * @param  int $pos
     * @return void
     */
    protected function _runAway($pos)
	{
        $actId = $this->_lineupMatrix[$pos];
        $actor = $this->_unitList[$actId];

        //攻击方 特殊效果加成计算
        $actor = $this->_calcExtStatus($actor);

        //get alive enemy pos
        $aliveEnemyPos = array();
        $aliveLev = 0;

        foreach ($this->_aliveMatrix as $enmPos=>$val) {
            if ($enmPos >= self::MATRIX_ENEMY_MIN && $enmPos <= self::MATRIX_ENEMY_MAX && $val == self::STATUS_ALIVE) {
                $aliveEnemyPos[] = $enmPos;
                $tarId = $this->_lineupMatrix[$enmPos];
                $target = $this->_unitList[$tarId];
                $aliveLev += (int)$target['level'];
            }
        }

        if (!$aliveEnemyPos) {
            $this->_ended = self::PROCESS_ENDED_RUN;
            $this->_logStep("pos:$pos run away- SUCCESS");
            return;
        }

        $enemyDodge = 0;

        //1/((1+对方个数)+(怪物的平均等级-我方等级*0.5))
        $tmp = $aliveLev/count($aliveEnemyPos) - $actor['level']*0.5;
        if ($tmp<0) {
            $tmp = 0;
        }
        $runawayRate = (100/(count($aliveEnemyPos) + $tmp)) * 0.9 * 10;
        $randomDig = $this->_rndElement[$this->_rndIterate];
        $this->_rndNumUsed();
        $isRun = ($randomDig <= $runawayRate);
        if ($isRun) {
            $this->_ended = self::PROCESS_ENDED_RUN;
            $this->_logStep("pos:$pos run away- SUCCESS");
        }
        else {
            $this->_logStep("pos:$pos run away- FAILED");
        }

        return;
	}

	/**
     * 使用unit前， buff加成
     * @param  array $unit
     * @return unit array $unit
     */
	protected function _calcExtStatus($unit)
	{
	    $pos = $unit['matrix_pos'];
        $effectList = $this->_extStatusMatrix[$pos];
        if ($effectList) {
            foreach ($effectList as $key=>$data) {
                //$aryTmp = explode('_', $key);
	            $type = $data['type'];
                if ($data['duration'] >= 0) {
                    $prop = '';
                    if ($type == self::EFFECT_TYPE_ADD_CHANGE_PHYATT_STATUS) {
                        $prop = 'phy_att';
                    }
                    else if ($type == self::EFFECT_TYPE_ADD_CHANGE_PHYDEF_STATUS || $type == self::EFFECT_TYPE_ADD_CHANGE_BY_SKILL) {
                        $prop = 'phy_def';
                    }
                    else if ($type == self::EFFECT_TYPE_ADD_CHANGE_MAGATT_STATUS) {
                        $prop = 'mag_att';
                    }
                    else if ($type == self::EFFECT_TYPE_ADD_CHANGE_MAGDEF_STATUS) {
                        $prop = 'mag_def';
                    }
                    else if ($type == self::EFFECT_TYPE_ADD_CHANGE_DODGE_STATUS) {
                        $prop = 'dodge';
                    }
                    else if ($type == self::EFFECT_TYPE_ADD_CHANGE_CRIT_STATUS) {
                        $prop = 'crit';
                    }
                    if ($data['isAbs'] == self::SKL_AFFECT_ABS) {
                        $unit[$prop] += $data['value'];
                    }
                    else {
                        if ($data['value'] > 0) {
                            $unit[$prop] += ceil($data['value']*$unit[$prop]);
                        }
                        else {
                            $unit[$prop] -= ceil(abs($data['value'])*$unit[$prop]);
                        }
                    }

                }
            }
        }
        return $unit;
	}

	/**
     * 异常状态存储器 增加持续效果
     * @param  int $pos
     * @param  int $cid
     * @param  unit array $actor
     * @return void
     */
	protected function _addExtStatus($pos, $cid, $actor)
	{
	    $logAttMeth = 'EXTSTA_ADD';
	    $tarId = $this->_lineupMatrix[$pos];
	    $target = $this->_unitList[$tarId];

	    if ($pos != $actor['matrix_pos']) {
    	    //被攻击方 特殊效果加成计算
            $target = $this->_calcExtStatus($target);
	    }

	    //特殊命令 防御
	    if (!$cid) {
	        $aryEffect = array();
    	    $aryEffect['cid'] = 0;
    	    $aryEffect['type'] = self::EFFECT_TYPE_ADD_CHANGE_PHYDEF_STATUS;
    	    $aryEffect['statusProp'] = self::EFFECT_PROP_POSITIVE;////1:正面状态 2:负面状态 0:无属性状态
    	    $aryEffect['value'] = 9;//作用值
    	    $aryEffect['isAbs'] = self::SKL_AFFECT_RAT;//作用值使用方式 0-绝对值 or 1-倍率
    	    $aryEffect['duration'] = 0;//持续回合  防御状态持续1回合
    	    //$aryEffect['dodgeAccept'] = 0;//闪躲使用率
    	    //$aryEffect['critAccept'] = 0;//暴击使用率
    	    //$aryEffect['resist'] = 0;//抵抗
    	    $aryEffect['isPhysic'] = 0;//是否是物理攻击
    	    $effId = $aryEffect['statusProp'] . '_' . $aryEffect['type'];
    	    $this->_extStatusMatrix[$pos][$effId] = $aryEffect;
    	    $this->_logStep($logAttMeth.':'.$pos.' effid:'.$effId);
	    }
	    else {
	        if (isset($this->_basSkillList[$cid])) {
	            $skillInfo = $this->_basSkillList[$cid];
	            if (isset($skillInfo['resist']) && $skillInfo['resist'] > 0) {
	                if ($this->_isResist($skillInfo['resist'])) {
                        $this->_logStep('SKL:'.$actor['matrix_pos'].'->'.$pos . ' RESIST');
                        return;
	                }
	            }
	            else {
    	            if ($skillInfo['dodge_accept']) {
                        //是否闪避
                        if ($this->_isDodge($actor['hit'], $target['dodge']*($skillInfo['dodge_accept']/100))) {
                            $this->_logStep('SKL:'.$actor['matrix_pos'].'->'.$pos . ' MISS');
                            return;
                        }
                    }
	            }
                $isCrit = false;
	            if ($skillInfo['crit_accept']) {
                    //是否暴击
                    $isCrit = $this->_isCrit($actor['crit']*($skillInfo['crit_accept']/100), $target['tou']);
                }
	            foreach ($skillInfo['effect'] as $idx=>$data) {
	                //init default param
	                if (!isset($data['isAbs'])) {
	                    $data['isAbs'] = 0;
	                }
	                if (!isset($data['duration'])) {
	                    $data['duration'] = 0;
	                }
	                if (!isset($data['isPhysic'])) {
	                    $data['isPhysic'] = 0;
	                }
	                if (!isset($data['statusProp'])) {
	                    $data['statusProp'] = 0;
	                }

	                //是否有抵消当前buff 效果
                    $isReduced = $this->_reduceExtStatusDeal($pos, $data);
                    if ($isReduced) {
                        continue;
                    }

	                //持续buff
	                if ($data['duration'] > 0) {
    	                $effId = $data['statusProp'] . '_' . $data['type'];
    	                $data['cid'] = $cid;
                        $aryEffect = $data;
                        $this->_extStatusMatrix[$pos][$effId] = $aryEffect;
                        $this->_logStep($logAttMeth.':'.$pos.' effid:'.$effId);
	                }

	                //直接伤害or治疗(烧蓝or加蓝)--瞬间
	                else {
	                    //复活系
	                    if ($data['type'] == self::EFFECT_TYPE_REVIVE) {
	                        $this->_aliveMatrix[$pos] = self::STATUS_ALIVE;
	                        $affectHp = abs($data['value'])*$target['hp_max'];
                            $this->_incHp($tarId, ceil($affectHp));
                            $this->_logStep('SKL-REVIVE:'.$actor['matrix_pos'].'->'.$pos . ' hp:'.$target['hp'].'+'.ceil($affectHp));
	                    }
	                    //recover 回复系
	                    else if ($data['value'] > 0) {
	                        //绝对值情况 输出值 处理
    	                    if ($data['isAbs'] == self::SKL_AFFECT_ABS) {
                                $recoverVal = abs($data['value']);
                            }
                            //倍率情况 输出值 处理
                            else {
                                $affectHp = $affectMp = 0;
                                if ($data['isPhysic']) {
                                    $recoverVal = $actor['phy_att'];
                                }
                                else {
                                    $recoverVal = $actor['mag_att'];
                                }

                                //修正attack val
                                $recoverVal = abs($data['value'])*$recoverVal;
                            }

                            if ($isCrit) {
                                $recoverVal = $recoverVal*self::BASE_CRIT_MULTI;
                                $logAttMeth .= '(c)';
                            }
                            $affectHp = $affectMp = ceil($recoverVal);

	                        if ($data['type'] == self::EFFECT_TYPE_CHANGE_HP) {
                                //deal target hp
                                $this->_incHp($tarId, $affectHp);
                                $this->_logStep('SKL:'.$actor['matrix_pos'].'->'.$pos . ' hp:'.$target['hp'].'+'.$affectHp);
	                        }
	                        else if ($data['type'] == self::EFFECT_TYPE_CHANGE_MP) {
                                //deal target mp
                                $this->_incMp($tarId, $affectMp);
                                $this->_logStep('SKL:'.$actor['matrix_pos'].'->'.$pos . ' mp:'.$target['mp'].'+'.$affectMp);
	                        }
	                    }

	                    //hurt 伤害系
	                    else {
	                        //绝对值情况 输出值 处理
    	                    if ($data['isAbs'] == self::SKL_AFFECT_ABS) {
                                $attVal = abs($data['value']);
                            }
                            //倍率情况 输出值 处理
                            else {
                                if ($data['isPhysic']) {
                                    $attVal = $actor['phy_att'];
                                }
                                else {
                                    $attVal = $actor['mag_att'];
                                }
                                //修正attack val
                                $attVal = abs($data['value'])*$attVal;
                            }

	                        if ($isCrit) {
                                $attVal = $attVal*self::BASE_CRIT_MULTI;
                                $logAttMeth .= '(c)';
                            }

	                        if ($data['isPhysic']) {
                                $defVal = $target['phy_def'];
                            }
                            else {
                                $defVal = $target['mag_def'];
                            }

	                        if ($data['type'] == self::EFFECT_TYPE_CHANGE_HP) {
	                            //相克性修正率
                                $jobPair = $actor['job'] . '-' . $target['job'];
                                $elementPair = $actor['element'] . '-' . $target['element'];
                                $adjust = (int)$this->_basRestrict['job_pair'][$jobPair] + (int)$this->_basRestrict['element_pair'][$elementPair];
                                //伤害值计算
                                $hurtVal = $this->_calcHurtVal($attVal, $defVal, $adjust);
                                $this->_decHp($tarId, $hurtVal);
                                $this->_logStep('SKL:'.$actor['matrix_pos'].'->'.$pos . ' hp:'.$target['hp'].'-'.$hurtVal);
	                        }
	                        else if ($data['type'] == self::EFFECT_TYPE_CHANGE_MP) {
                                //deal target mp
                                $this->_decMp($tarId, $attVal);
                                $this->_logStep('SKL:'.$actor['matrix_pos'].'->'.$pos . ' mp:'.$target['mp'].'-'.$attVal);
	                        }
	                    }


	                }
	            }//end foreach effect
	        }
	    }

	    return;
	}

	/**
     * 抵消特殊效果 处理
     * @param  int $pos
     * @param  array $effData
     * @return boolean
     */
	protected function _reduceExtStatusDeal($pos, $effData)
	{
	    $logAttMeth = 'EXTSTA_RMV';
	    $isReduced = false;

	    $prepEff = array();
        $effType = $effData['type'];

        $effectList = $this->_extStatusMatrix[$pos];
        $rmvEffKeys = array();

        //消除所有状态
        if ($effType == self::EFFECT_TYPE_REMOVE_ALL_STATUS) {
            foreach ($effectList as $key=>$data) {
                $rmvEffKeys[] = $key;
            }
        }
        //消除所有正面状态
        else if ($effType == self::EFFECT_TYPE_REMOVE_ALL_POSITIVE_STATUS) {
            foreach ($effectList as $key=>$data) {
                if ($data['statusProp'] == self::EFFECT_PROP_POSITIVE) {
                    $rmvEffKeys[] = $key;
                }
            }
        }
        //消除所有负面状态
	    else if ($effType == self::EFFECT_TYPE_REMOVE_ALL_NEGATIVE_STATUS) {
	        foreach ($effectList as $key=>$data) {
                if ($data['statusProp'] == self::EFFECT_PROP_NEGATIVE) {
                    $rmvEffKeys[] = $key;
                }
            }
        }
        else {
            switch ($effType) {
                case self::EFFECT_TYPE_REMOVE_CHANGE_HP_STATUS ://消除影响HP状态
                    $prepEff[] = self::EFFECT_TYPE_ADD_CHANGE_HP_STATUS;
                    break;

                case self::EFFECT_TYPE_REMOVE_CHANGE_MP_STATUS ://消除影响MP状态
                    $prepEff[] = self::EFFECT_TYPE_ADD_CHANGE_MP_STATUS;
                    break;

                case self::EFFECT_TYPE_REMOVE_CHANGE_PHYATT_STATUS ://消除影响物攻状态
                    $prepEff[] = self::EFFECT_TYPE_ADD_CHANGE_PHYATT_STATUS;
                    break;

                case self::EFFECT_TYPE_REMOVE_CHANGE_PHYDEF_STATUS ://消除影响物防状态
                    $prepEff[] = self::EFFECT_TYPE_ADD_CHANGE_BY_SKILL;
                    break;

                case self::EFFECT_TYPE_REMOVE_CHANGE_MAGATT_STATUS ://消除影响魔攻状态
                    $prepEff[] = self::EFFECT_TYPE_ADD_CHANGE_MAGATT_STATUS;
                    break;

                case self::EFFECT_TYPE_REMOVE_CHANGE_MAGDEF_STATUS ://消除影响魔防状态
                    $prepEff[] = self::EFFECT_TYPE_ADD_CHANGE_MAGDEF_STATUS;
                    break;

                case self::EFFECT_TYPE_REMOVE_CHANGE_DODGE_STATUS ://消除影响闪躲状态
                    $prepEff[] = self::EFFECT_TYPE_ADD_CHANGE_DODGE_STATUS;
                    break;

                case self::EFFECT_TYPE_REMOVE_CHANGE_CRIT_STATUS ://消除影响暴击状态
                    $prepEff[] = self::EFFECT_TYPE_ADD_CHANGE_CRIT_STATUS;
                    break;

                case self::EFFECT_TYPE_REMOVE_PETRIFACTION_STATUS ://消除石化状态
                    $prepEff[] = self::EFFECT_TYPE_ADD_PETRIFACTION_STATUS;
                    break;

                case self::EFFECT_TYPE_REMOVE_SLEEP_STATUS ://消除睡眠状态
                    $prepEff[] = self::EFFECT_TYPE_ADD_SLEEP_STATUS;
                    break;

                case self::EFFECT_TYPE_REMOVE_CONFUSION_STATUS ://消除混乱状态
                    $prepEff[] = self::EFFECT_TYPE_ADD_CONFUSION_STATUS;
                    break;

                default:
            }//end switch

            foreach ($effectList as $key=>$data) {
                //$aryTmp = explode('_', $key);
    	        $type = $data['type'];
                if (in_array($type, $prepEff)) {
                    //正面抵消正面,负面抵消负面
                    if ($data['statusProp'] == $effData['statusProp']) {
                        $rmvEffKeys[] = $key;
                    }
                    /*//负面抵消负面
                    else if ($data['value']<0 && $effData['value']<0) {
                        $rmvEffKeys[] = $key;
                    }*/
                }
            }
        }//end if case

        //remove counteract effect
        if ($rmvEffKeys) {
	        foreach ($rmvEffKeys as $idx=>$rmKey) {
	            unset($effectList[$rmKey]);
	            $this->_logStep($logAttMeth.':'.$pos.' effid:'.$rmKey);
	        }
	        $isReduced = true;
	    }

	    $this->_extStatusMatrix[$pos] = $effectList;
        return $isReduced;
	}

	/**
     * 行动方回合开始时的异常状态 持续效果处理
     * @param  int $pos
     * @return array
     */
	protected function _extStatusDeal($pos)
	{
	    $logAttMeth = 'EXTSTA_AFF';
	    $effectList = $this->_extStatusMatrix[$pos];

	    $tarId = $this->_lineupMatrix[$pos];
	    $rmKeys = array();
	    $skip = false;
	    foreach ($effectList as $key=>&$data) {
	        $data['duration'] -= 1;
	        //effect need to be remove
	        if ($data['duration'] < 0) {
	            $rmKeys[] = $key;
	        }
	        else {
    	        //$aryTmp = explode('_', $key);
    	        $type = $data['type'];
    	        //hp change
                if ($type == self::EFFECT_TYPE_ADD_CHANGE_HP_STATUS || $type == self::EFFECT_TYPE_ADD_SEPARATE_BY_SKILL) {
                    $this->_logStep($logAttMeth.':'.$pos.' HP:'.$data['value']);
                    if ($data['value'] < 0) {
                        $this->_decHp($tarId, abs($data['value']));
                        //dead
                        if ($this->_aliveMatrix[$pos] == self::STATUS_DEAD) {
                            $skip = true;
                        }
                    }
                    else {
                        $this->_incHp($tarId, $data['value']);
                    }
                }
                //mp change
                else if ($type == self::EFFECT_TYPE_ADD_CHANGE_MP_STATUS) {
                    $this->_logStep($logAttMeth.':'.$pos.' MP:'.$data['value']);
                    if ($data['value'] < 0) {
                        $this->_decMp($tarId, abs($data['value']));
                    }
                    else {
                        $this->_incMp($tarId, $data['value']);
                    }
                }

                //无法行动
                else if ($type == self::EFFECT_TYPE_ADD_PETRIFACTION_STATUS
                        || $type == self::EFFECT_TYPE_ADD_SLEEP_STATUS || $type == self::EFFECT_TYPE_ADD_FROZEN_BY_SKILL) {
                    $skip = true;
                    $this->_logStep($logAttMeth.':'.$pos.' effid:'.$key.' can not act');
                }
	        }
	    }

	    if ($rmKeys) {
	        foreach ($rmKeys as $idx=>$key) {
	            unset($effectList[$key]);
	            $this->_logStep($logAttMeth.':RMV:'.$pos.' effid:'.$key);
	        }
	    }

	    $this->_extStatusMatrix[$pos] = $effectList;
	    return $skip;
	}

	/**
     * pos位置方 是否全灭判定
     * @param  int $pos
     * @return boolean
     */
	protected function _isPosSideRuin($pos)
	{
	    //check if any side had all dead
        //敌方
	    if ($pos < self::MATRIX_HOME_MIN) {
            $begin = self::MATRIX_ENEMY_MIN;
            $end = self::MATRIX_ENEMY_MAX;
        }
        //我方
        else {
            $begin = self::MATRIX_HOME_MIN;
            $end = self::MATRIX_HOME_MAX;
        }

        $isRuined = true;
        for($i=$begin; $i<=$end; $i++) {
            if (isset($this->_aliveMatrix[$i]) && $this->_aliveMatrix[$i] == self::STATUS_ALIVE) {
                $isRuined = false;
                break;
            }
        }

        return $isRuined;
	}

	/**
     * 战斗单位dead处理
     * @param  int $tarId
     * @param  int $hurtVal
     * @return void
     */
    protected function _unitDeadDeal($tarPos)
	{

	    $this->_aliveMatrix[$tarPos] = self::STATUS_DEAD;
	    $this->_extStatusMatrix[$tarPos] = array();

        if ($this->_isPosSideRuin($tarPos)) {
            $this->_ended = self::PROCESS_ENDED;
        }
        $this->_logStep('pos:'.$tarPos.' dead');
	}

	/**
     * 扣除Hp
     * @param  int $tarId
     * @param  int $decVal
     * @return int
     */
    protected function _decHp($tarId, $decVal)
	{
	    $target = $this->_unitList[$tarId];
	    $this->_unitList[$tarId]['hp'] = (($target['hp'] - $decVal) < 0 ? 0 : ($target['hp'] - $decVal));
        if ($this->_unitList[$tarId]['hp'] == 0) {
            $this->_unitDeadDeal($target['matrix_pos']);
        }
        if($decVal > 0){
        	$this->_removeSleep($tarId);
        }
        return $this->_unitList[$tarId]['hp'];
	}
	
	protected function _removeSleep($tarId)
	{
		$target = $this->_unitList[$tarId];
		$pos = $target['matrix_pos'];
		$effectList = $this->_extStatusMatrix[$pos];
		foreach ($effectList as $key => $data) {
			if($data['type'] == self::EFFECT_TYPE_ADD_SLEEP_STATUS){
				unset($effectList[$key]);
				break;
			}
		}
		$this->_extStatusMatrix[$pos] = $effectList;
	}

	/**
     * 增加Hp
     * @param  int $tarId
     * @param  int $incVal
     * @return int
     */
    protected function _incHp($tarId, $incVal)
	{
	    $target = $this->_unitList[$tarId];
	    $this->_unitList[$tarId]['hp'] = (($target['hp'] + $incVal) > $target['hp_max'] ? $target['hp_max'] : ($target['hp'] + $incVal));
        return $this->_unitList[$tarId]['hp'];
	}

	/**
     * 扣除Mp
     * @param  int $tarId
     * @param  int $decVal
     * @return int
     */
    protected function _decMp($tarId, $decVal)
	{
	    $target = $this->_unitList[$tarId];
	    $this->_unitList[$tarId]['mp'] = (($target['mp'] - $decVal) < 0 ? 0 : ($target['mp'] - $decVal));
        return $this->_unitList[$tarId]['mp'];
	}

	/**
     * 增加Mp
     * @param  int $tarId
     * @param  int $incVal
     * @return int
     */
    protected function _incMp($tarId, $incVal)
	{
	    $target = $this->_unitList[$tarId];
	    $this->_unitList[$tarId]['mp'] = (($target['mp'] + $incVal) > $target['mp_max'] ? $target['mp_max'] : ($target['mp'] + $incVal));
        return $this->_unitList[$tarId]['mp'];
	}

	/**
     * 伤害值计算--攻击公式 伤害量=物理攻击力*[物理攻击力/（物理攻击+物理防御）*系数]
     * @param  int $attVal
     * @param  int $defVal
     * @param  int $adjust
     * @return int $hurtVal
     */
    protected function _calcHurtVal($attVal, $defVal, $adjust)
	{
	    if ($adjust) {
	        //相克性修正率
	        $hurtVal = ceil( ($attVal*($attVal/($attVal+$defVal*3)*self::BASE_COEFFICIENT)*(1+$adjust/100)) );
	        $this->_logStep('hurtAdj:'.($adjust/100));
	        $this->_logStep('canshu:'.$attVal.'----'.$defVal.'------'.$adjust);
	    }
	    else {
	        $hurtVal = ceil( ($attVal*($attVal/($attVal+$defVal*3)*self::BASE_COEFFICIENT)) );
	    }
        return $hurtVal;
	}

	/**
     * 抵抗发动判定
     * @param  int $resist
     * @return boolean
     */
    protected function _isResist($resist)
	{
        $resistRate = $resist * 10;
        $randomDig = $this->_rndElement[$this->_rndIterate];
        $this->_rndNumUsed();
        $isResist = ($randomDig <= $resistRate);
        return $isResist;
	}

	/**
     * 闪避发动判定
     * @param  int $actDodge
     * @param  int $tarDodge
     * @return boolean
     */
    protected function _isDodge($actDodge, $tarDodge)
	{
	    //闪避率=[对方闪避/（对方闪避+我方命中*3）*系数]*1000
        //$dodgeRate = ($tarDodge*($tarDodge/($tarDodge+$actDodge*3)*self::BASE_COEFFICIENT) * 10);
        $dodgeRate = ($tarDodge/($tarDodge+$actDodge*3)*self::BASE_COEFFICIENT)*1000;
        $randomDig = $this->_rndElement[$this->_rndIterate];
        $this->_rndNumUsed();
        $isDodeg = ($randomDig <= $dodgeRate);
        return $isDodeg;
	}

	/**
     * 暴击发动判定
     * @param  int $actCrit
     * @param  int $tarCrit
     * @return boolean
     */
	protected function _isCrit($actCrit, $tarCrit)
	{
        //暴击率=[我方暴击/（我方暴击+对方韧性*3）*系数]*1000
        //$critRate = ($actCrit*($actCrit/($actCrit+$tarCrit)*self::BASE_COEFFICIENT) * 10);//转换成千位数
        $critRate = ($actCrit/($actCrit+$tarCrit*3)*self::BASE_COEFFICIENT)*1000;
	    $randomDig = $this->_rndElement[$this->_rndIterate];
        $this->_rndNumUsed();
        $isCrit = ($randomDig <= $critRate);
        return $isCrit;
	}

	/**
     * 计算一方受区域效果 连带位置
     * @param  int $pos
     * @param  int $effArea
     * @return array
     */
	protected function _calcAffectPos($pos, $effArea)
	{
	    $affPos = array();
	    $posMatrix = $this->_lineupMatrix;

	    switch ($effArea) {
            case self::EFFECT_AREA_SIG :
                $affPos[] = $pos;
                break;

            case self::EFFECT_AREA_ROW :
                $aryRow = array();
                $aryRow[0] = array(0,1,2);
                $aryRow[1] = array(3,4,5);
                $aryRow[2] = array(6,7,8);
                $aryRow[3] = array(9,10,11);
                $aryRow[4] = array(12,13,14);
                $aryRow[5] = array(15,16,17);
                $find = -1;
                foreach ($aryRow as $key=>$data) {
                    if (in_array($pos, $data)) {
                        $find = $key;
                        break;
                    }
                }
                if ($find != -1) {
                    foreach ($aryRow[$find] as $key=>$val) {
                        if (array_key_exists($val, $posMatrix)) {
                            $affPos[] = $val;
                        }
                    }
                }
                break;

            case self::EFFECT_AREA_COL :
                $aryCol = array();
                $aryCol[0] = array(0,3,6);
                $aryCol[1] = array(1,4,7);
                $aryCol[2] = array(2,5,8);
                $aryCol[3] = array(9,12,15);
                $aryCol[4] = array(10,13,16);
                $aryCol[5] = array(11,14,17);
                $find = -1;
                foreach ($aryCol as $key=>$data) {
                    if (in_array($pos, $data)) {
                        $find = $key;
                        break;
                    }
                }
                if ($find != -1) {
                    foreach ($aryCol[$find] as $key=>$val) {
                        if (array_key_exists($val, $posMatrix)) {
                            $affPos[] = $val;
                        }
                    }
                }
                break;

            case self::EFFECT_AREA_CRS:
                $aryCross = array();
                $aryCross[0] = array(0,1,3);
                $aryCross[1] = array(0,1,2,4);
                $aryCross[2] = array(1,2,5);
                $aryCross[3] = array(0,3,4,6);
                $aryCross[4] = array(1,3,4,5,7);
                $aryCross[5] = array(2,4,5,8);
                $aryCross[6] = array(3,6,7);
                $aryCross[7] = array(4,6,7,8);
                $aryCross[8] = array(5,7,8);
                $aryCross[9] = array(9,10,12);
                $aryCross[10] = array(9,10,11,13);
                $aryCross[11] = array(10,11,14);
                $aryCross[12] = array(9,12,13,15);
                $aryCross[13] = array(10,12,13,14,16);
                $aryCross[14] = array(11,13,14,17);
                $aryCross[15] = array(12,15,16);
                $aryCross[16] = array(13,15,16,17);
                $aryCross[17] = array(14,16,17);

                $find = -1;
	            if (array_key_exists($pos, $aryCross)) {
	                $find = $pos;
	            }
	            if ($find != -1) {
                    foreach ($aryCross[$find] as $key=>$val) {
                        if (array_key_exists($val, $posMatrix)) {
                            $affPos[] = $val;
                        }
                    }
                }
                break;

            case self::EFFECT_AREA_ALL :
                //敌全体
                if ($pos<self::MATRIX_HOME_MIN) {
                    foreach ($posMatrix as $key=>$val) {
                        if ($key<self::MATRIX_HOME_MIN) {
                            $affPos[] = $key;
                        }
                    }
                }
                //我方全体
                else {
                    foreach ($posMatrix as $key=>$val) {
                        if ($key>self::MATRIX_ENEMY_MAX) {
                            $affPos[] = $key;
                        }
                    }
                }
                break;

            default:
                $affPos[] = $pos;
        }
		sort($affPos, SORT_NUMERIC);
        return $affPos;
	}

	/**
     * 输出战斗结束结果
     * @param  void
     * @return array
     */
	protected function _concludeResult()
	{

	    $ftResult = self::RESULT_LOSE;
	    $homeSide = array();
	    for($i=self::MATRIX_HOME_MIN; $i<=self::MATRIX_HOME_MAX; $i++) {
            if (isset($this->_aliveMatrix[$i])) {
                $id = $this->_lineupMatrix[$i];
                $unit = $this->_unitList[$id];

                if ($id != self::ROLE_SELF) {
                    $aryTmp = explode('_', $id);
                    $id = $aryTmp[1];
                }
                $homeSide[$i] = array('id' => $id, 'hp' => $unit['hp'], 'mp' => $unit['mp'], 'level' => $unit['level']);
                if ($this->_aliveMatrix[$i] == self::STATUS_ALIVE) {
                    $ftResult = self::RESULT_WIN;
                }
            }
        }

        //是否逃跑
        if ($this->_ended == self::PROCESS_ENDED_RUN) {
            $ftResult = self::RESULT_RUN;
        }
        else if ($this->_ended != self::PROCESS_ENDED) {
            $ftResult = self::RESULT_OTHER;
        }
//return $this->_debugDetailInfo();
	    $this->_logStep('==========Fight end, total round:'.$this->_roundNo . ' result:'.$ftResult);
	    $data = array('corps' => $homeSide, 'item'=> $this->_itemList);
	    return array('result' => $ftResult, 'data' => $data);
	}

	/**
     * 验证协议数据合法性
     * @return int
     */
    protected function _checkProtocolData()
    {
        $aryAct = $this->_actList;
        foreach ($aryAct as $idx=>$data) {
            $cntData = count($data);
            if ($cntData < 2) {
                return -8101;//协议数据位数不正
            }

            $operate = $data[0];
            $atterPos = $data[1];
            if ($atterPos < self::MATRIX_ENEMY_MIN || $atterPos > self::MATRIX_HOME_MAX) {
                return -8102;//协议攻击方站位不正
            }

            //行动者操作
            switch ($operate) {
                case self::OPERATE_ATTACK :
                    //攻击型
                    if ($cntData < 6) {
                        return -8101;//协议数据位数不正
                    }
                    $deferPos = $data[2];
                    if ($deferPos < self::MATRIX_ENEMY_MIN || $atterPos > self::MATRIX_HOME_MAX) {
                        return -8103;//协议被攻方站位不正
                    }
                    $attType = $data[3];
                    if ($attType < self::ACT_TYPE_PHY || $attType > self::ACT_TYPE_ITM) {
                        return -8104;//协议攻击型 类型 不正
                    }

                    break;
                case self::OPERATE_CURE :
                    if ($cntData < 5) {
                        return -8101;//协议数据位数不正
                    }
                    //治疗型
                    $deferPos = $data[2];
                    if ($deferPos < self::MATRIX_ENEMY_MIN || $atterPos > self::MATRIX_HOME_MAX) {
                        return -8103;//协议被攻方站位不正
                    }
                    $attType = $data[3];
                    if ($attType < self::ACT_TYPE_SKL || $attType > self::ACT_TYPE_ITM) {
                        return -8105;//协议治疗型 类型 不正
                    }

                    $cid = (int)$data[4];
                    if (!$cid) {
                        return -8106;//协议治疗型 技能/道具ID 不正
                    }

                    break;
                case self::OPERATE_DEFENCE :
                    //防御

                    break;
                case self::OPERATE_RUNAWAY :
                    //逃跑

                    break;
//                case self::OPERATE_ASTRICT :
//                    //无法行动
//
//                    break;
                case self::OPERATE_ASSIST :
                    //协助攻击
                    if ($cntData < 5) {
                        return -8101;//协议数据位数不正
                    }

                    break;

                default:
                    return -8107;//协议没有该操作位符号

            }//end switch
        }//end foreach

        return 1;
    }

    /**
     * 验证数据是否被伪造
     * @return int
     */
    protected function _checkFakeData()
    {
        $aryAct = $this->_actList;
        $assistCnt = 0;
        foreach ($aryAct as $idx=>$data) {
            //首回合
            $atterPos = $data[1];
            if ($idx == 0) {
                if ($atterPos != $this->_actSequence[0]['pos']) {
                    return -8201;//首回合行动者 不正
                }
            }

            $operate = $data[0];
            if ($operate == self::OPERATE_ASSIST) {
                $assistCnt += 1;
            }
        }//end foreach

        if ($assistCnt > 1 || $assistCnt > (int)$this->_assistCount ) {
            return -8207;//援助攻击次数不足
        }
        $this->setAssistUsedCount($assistCnt);
        return 1;
    }

    /**
     * 记录详细日志
     * @return void
     */
    protected function _logStep($msg)
	{
	    if ($this->_detailLog) {
            info_log_fight($msg, $this->_detailLog);
	    }
	    return;
	}

	protected function _debugDetailInfo()
	{
	    $aryTestRet = array('_lineupMatrix'=>$this->_lineupMatrix,
                    '_aliveMatrix'=>$this->_aliveMatrix,
                    '_extStatusMatrix'=>$this->_extStatusMatrix,
                    '_rndElement'=>$this->_rndElement,
                    '_actSequence'=>$this->_actSequence,
                    '_unitList'=>$this->_unitList,
                    '_basSkillList'=>$this->_basSkillList,
                    '_roundNo'=>$this->_roundNo,
                    '_actIterate'=>$this->_actIterate
        );
        return $aryTestRet;
	}
}