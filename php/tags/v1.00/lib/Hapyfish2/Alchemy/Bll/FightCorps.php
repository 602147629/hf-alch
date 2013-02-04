<?php

class Hapyfish2_Alchemy_Bll_FightCorps
{

    //取本方战斗单位列表
    public static function getHomeSideUnitList($uid)
	{
	    //站位分布
        $posMatrix = Hapyfish2_Alchemy_Cache_FightCorps::getFightCorpsInfo($uid);
        if ($posMatrix) {
            $homeSideInfo = array();
            //self fight info
            $selfInfo = Hapyfish2_Alchemy_HFC_FightAttribute::getInfo($uid);
            //mecenarycorps fight info
            $mecenaryList = Hapyfish2_Alchemy_HFC_FightMercenary::getAll($uid);

            //basic job list
            //$basJob = Hapyfish2_Alchemy_Cache_Basic::getMercenaryList();
            //array $data(id,matrix_pos,job,level,hp,hp_max,mp,mp_max,phy_att,phy_def,mag_att,mag_def,agility,crit,dodge,weapon,skill)
            foreach ($posMatrix as $pos=>$id) {
                //主角
                if ($id == Hapyfish2_Alchemy_Bll_Fight_Simulator::ROLE_SELF) {
                    $selfInfo['id'] = 0;
                    $selfInfo['matrix_pos'] = (int)$pos;

                    //add weapon prop to attribute prop
                    //Hapyfish2_Alchemy_Bll_Fight::addWeaponProp($selfInfo);
                    $homeSideInfo[(int)$pos] = $selfInfo;
                }
                //雇佣兵
                else {
                    if (isset($mecenaryList[$id])) {
                        $mecenaryInfo = $mecenaryList[$id];
                        $mecenaryInfo['id'] = (int)$id;
                        $mecenaryInfo['matrix_pos'] = (int)$pos;

                        //add weapon prop to attribute prop
                        //Hapyfish2_Alchemy_Bll_Fight::addWeaponProp($mecenaryInfo);
                        $homeSideInfo[(int)$pos] = $mecenaryInfo;
                    }
                }
            }
            return $homeSideInfo;
        }

        return null;
	}

	//拼接前端Vo数据格式
    public static function genHomeRolesVo($uid, $homeSide)
	{
        $roleList = array();

        //佣兵冷却时间
        $fightOccupy = Hapyfish2_Alchemy_HFC_FightOccupy::getInfo($uid);
        $corpsUsed = $fightOccupy['corps_used'];

        //本方
        if ($homeSide) {
            foreach ($homeSide as $data) {
            	$weaponTemp = $data['weapon'];
            	$weaponList = array();
            	foreach ( $weaponTemp as $wid ) {
            		if ( $wid != 0 ) {
	            		$weapon = Hapyfish2_Alchemy_HFC_Weapon::getOne($uid, $wid);
	            		$info = array_values($weapon);
	            		array_splice($info, 2, 1);
	            		$weaponList[] = $info;
            		}
            		else {
            			$weaponList[] = null;
            		}
            	}

            	//佣兵下一等级经验
            	$nextLevelExp = Hapyfish2_Alchemy_Cache_Basic::getMercenaryLevelExp($data['level']+1);
            	$lastLevelExp = Hapyfish2_Alchemy_Cache_Basic::getMercenaryLevelExp($data['level']);
            	$nowExp = $data['exp'] - $lastLevelExp;
            	$nowExp = $nowExp < 0 ? 0 : $nowExp;
            	$maxExp = $nextLevelExp - $lastLevelExp;

            	//佣兵冷却时间
            	$roleId = $data['id'];
            	if ( isset($corpsUsed[$roleId]) ) {
            		$cdTime = Hapyfish2_Alchemy_Bll_FightOccupy::CORPS_COOLDOWN_INTERVAL;
            		$occCdTime = $corpsUsed[$roleId] + $cdTime;
            	}
            	else {
            		$occCdTime = 0;
            	}

                $role = array(
                	'id' => $data['id'],
                	'name' => $data['name'],
                	'sex' => (int)$data['sex'],
                	'label' => '',
                	'className' => $data['class_name'],
                	'faceClass' => $data['face_class_name'],
                	'sFaceClass' => $data['s_face_class_name'],
                	'scenePlayerClass' => $data['scene_player_class'],
                	'pos' => (int)$data['matrix_pos'],
                	'sizeX' => 1,
                	'sizeZ' => 1,
                	'profession' => (int)$data['job'],
                	'prop' => (int)$data['element'],
                	'hp' => (int)$data['hp'],
                	'maxHp' => (int)$data['hp_max'],
                	'mp' => (int)$data['mp'],
                	'maxMp' => (int)$data['mp_max'],
                	'speed' => (int)$data['agility'],
                	'speed' => (int)$data['agility'],
                    'phyAtk' => (int)$data['phy_att'],
                    'phyDef' => (int)$data['phy_def'],
                    'magAtk' => (int)$data['mag_att'],
                    'magDef' => (int)$data['mag_def'],
                    'dodge' => (int)$data['dodge'],
                    'baseDodge' => (int)$data['dodge'],
                    'crit' => (int)$data['crit'],
                    'baseCrit' => (int)$data['crit'],
                    'skills' => $data['skill'],
                    'items' => array(),
                    'equipments' => $weaponList,
                    'aiScriptId' => array(),
                    'statusList' => array(),
                    'level' => (int)$data['level'],
                    'exp' => (int)$nowExp,
                    'maxExp' => (int)$maxExp,
                    'quality' => (int)$data['rp'],
                    'occCdTime' => $occCdTime,
                    'sPhyAtk' => $data['s_phy_att'],
                    'sPhyDef' => $data['s_phy_def'],
                    'sMagAtk' => $data['s_mag_att'],
                    'sMagDef' => $data['s_mag_def'],
                    'sSpeed' => $data['s_agility'],
                    'workTime' => (int)$data['work_time'],
                    'workMaxTime' => (int)$data['work_max_time']
                );

                if (Hapyfish2_Alchemy_Bll_Fight_Simulator::ROLE_SELF == $data['id']) {
                    $role['label'] = 'MR';
                    $uInfo = Hapyfish2_Platform_Bll_User::getUser($uid);
                    $role['name'] = $uInfo['name'];
                }
                $roleList[] = $role;
            }//end for
        }

        return $roleList;
	}

    //排列战斗单位 站位矩阵
	public static function arrangeCorps($uid, $aryMatrix)
	{
	    $aryPos = array();
	    foreach ($aryMatrix as $pos=>$id) {
	        if ($pos < Hapyfish2_Alchemy_Bll_Fight_Simulator::MATRIX_HOME_MIN || $pos > Hapyfish2_Alchemy_Bll_Fight_Simulator::MATRIX_HOME_MAX) {
                //站位超范围
                info_log('arrangeCorps:position not allow', 'FightCorps');
                return false;
	        }
            if (array_key_exists($pos, $aryPos)) {
                //站位重复
                info_log('arrangeCorps:position repeat', 'FightCorps');
                return false;
            }
	        $aryPos[$pos] = 1;
	    }

        Hapyfish2_Alchemy_Cache_FightCorps::saveFightCorpsInfo($uid, $aryMatrix);
        return true;
	}
}