<?php

class Hapyfish2_Admin_Bll_Basic
{
	public static function getBasicTbList()
	{
		$aryList = self::getBasicList();

		//$list = array_keys($aryList);
	    return $aryList;
	}

    public static function getBasicTbByName($tbName)
	{
	    $tbInfo = null;
		$tbList = self::getBasicList();
        if (isset($tbList[$tbName])) {
            $tbInfo = $tbList[$tbName];
        }
	    return $tbInfo;
	}

	public static function generateBasicDataFile($tbName, $fileName)
	{
	    try {
	        $tbInfo = Hapyfish2_Admin_Bll_Basic::getBasicTbByName($tbName);
    	    if (!$tbInfo) {
                return 'table not found,please check.';
            }
            $content = '';
            foreach ($tbInfo['column'] as $col) {
                $content .= $col . "\t";
            }
            $content .= "\n";
            $dal = Hapyfish2_Admin_Dal_Basic::getDefaultInstance();
            $lstData = $dal->getBasicList($tbName);
            foreach ($lstData as $data) {
                foreach ($data as $val) {
                    $content .= $val . "\t";
                }
                $content .= "\n";
            }

            $dir = dirname($fileName);
            if (!is_dir($dir)) {
                mkdir($dir, 0700, true);
            }

            @unlink($fileName);
            $handle = fopen($fileName, 'w');
            if (!$handle) {
                return "can not open file: $fileName";
            }
            if (fwrite($handle, $content) === FALSE) {
                return "file write failed: $fileName";
            }
            fclose($handle);
	    }
	    catch (Exception $e) {
            return "fatal error:".$e->getMessage();
	    }

        return '';
	}

	public static function clearTableData($tbName)
	{
	    try {
            $dal = Hapyfish2_Admin_Dal_Basic::getDefaultInstance();
            $dal->deleteInfo($tbName, 1, 1);
	    }
	    catch (Exception $e) {
            info_log($e->getMessage(), 'errAdmin_Bll_Basic');
            return false;
	    }
	    return true;
	}

	public static function importBasicDataFromFile($tbName, $fileName, &$aryFailed)
	{

	    try {
	        $tbInfo = Hapyfish2_Admin_Bll_Basic::getBasicTbByName($tbName);
    	    if (!$tbInfo) {
                return false;
            }

            $cntFields = count($tbInfo['column']);

            $dal = Hapyfish2_Admin_Dal_Basic::getDefaultInstance();
            $cntSuccess = 0;
    	    $rowIdx = 0;
    	    $handle = fopen($fileName, "r");
            if ($handle) {
                while (!feof($handle)) {
                    $row = fgets($handle);
                    
                    $row = trim($row);
                    $rowIdx ++;
                    if ($rowIdx==1) {
                        continue;
                    }

                    if (!$row) {
                        //$aryFailed[] = $rowIdx;
                        continue;
                    }
                    
                    $data = explode("\t", $row);
                    
                    if ($cntFields != count($data) && ($cntFields+1) != count($data)) {
                        $aryFailed[] = $rowIdx;
                        continue;
                    }

                    $info = array();
                    $colIdx = 0;
                    foreach ($tbInfo['column'] as $col=>$val) {
                        //$info[$col] = mb_convert_encoding($data[$colIdx], "UTF-8");
                        $info[$col] = ($data[$colIdx]);
                        $colIdx ++;
                    }
                    if ($colIdx > 0) {
                        $rst = $dal->addInfo($tbName, $info);
                        //info_log(json_encode($info), 'bbb');
                        $cntSuccess ++;
                    }
                }
                fclose($handle);
            }
	    }
	    catch (Exception $e) {
            info_log('importBasicDataFromFile:'.$e->getMessage(), 'errAdmin_Bll_Basic');
	    }

	    return $cntSuccess;
	}

    public static function importMapCopyDataFromFile($tbName, $fileName, &$aryFailed)
    {

        try {
            $cntFields = 0;
            $updField = '';
            if ($tbName == 'monster') {
                //id,sceneId,cid,x,z,fiddleRangeX,fiddleRangeZ,monsterper,detail,collision,goHome -> id,cid,x,z,fr_x,fr_z,per,detail,end,fightBg,alwaysInitiative
                $cntFields = 13;
            }
            else if ($tbName == 'mine') {
                //id,sceneId,cid,x,z,mineper  -> id,cid,x,z,per
                $cntFields = 6;
            }
            else if ($tbName == 'portal') {
                //id,sceneId,cid,x,z,mirror,targetSceneId,vpath,targetSceneName,tips  -> id,cid,x,z,mirror,tar,vpath,tips,needItem,lock,sortPriority
                $cntFields = 13;
            }
            $updField = $tbName.'_data';

            if (empty($cntFields)) {
                throw new Exception('col count not match');
            }

            $cntSuccess = 0;
    	    $rowIdx = 0;
    	    $handle = fopen($fileName, "r");
    	    $aryInfo = array();
            if ($handle) {
                while (!feof($handle)) {   
                    $row = fgets($handle);
                    $row = trim($row);  
                    $rowIdx ++;
                    if ($rowIdx==1) {
                        continue;
                    }

                    if (!$row) {
                        //$aryFailed[] = $rowIdx;
                        continue;
                    }
                    if ($tbName == 'monster') {
                        //$row = preg_replace("/,/", "\t", $row, 9);
                        //$data = explode("\t", $row);
                        //echo json_encode($data);exit;
                        $data = explode(",", $row);
                    }
                    else {
                        $data = explode(",", $row);
                    }
                    if ($cntFields != count($data)) {
                        $aryFailed[] = $rowIdx;
                        throw new Exception('Line:'.$rowIdx.' col count not match');
                    }

                    $mapId = (int)$data[1];
                    if (!isset($aryInfo[$mapId])) {
                        $aryInfo[$mapId] = array();
                    }

                    $id = (int)$data[0];
                    if ($tbName == 'monster') {
                        $tmpData = array(
                        	'id'=>$id,
                            'cid'=>(int)$data[2],
                            'x'=>(int)$data[3],
                            'z'=>(int)$data[4],
                            'fr_x'=>(int)$data[5],
                            'fr_z'=>(int)$data[6],
                            'per'=>(int)$data[7],
                            'detail'=>(int)$data[8],
                            'end'=>(int)$data[10],
                        	'alwaysInitiative'=>(int)$data[11]
                        );

                    }
                    else if ($tbName == 'mine') {
                        $tmpData = array(
                        	'id'=>$id,
                            'cid'=>(int)$data[2],
                            'x'=>(int)$data[3],
                            'z'=>(int)$data[4],
                            'per'=>(int)$data[5]
                        );
                    }
                    else if ($tbName == 'portal') {         	
                        $tmpData = array(
                        	'id'=>$id,
                            'cid'=>(int)$data[2],
                            'x'=>(int)$data[3],
                            'z'=>(int)$data[4],
                            'mirror'=>(int)$data[5],
                            'tar'=>(int)$data[6],
                            'vpath'=>$data[7],
                            'scene_name'=>$data[8],
                            'tips'=>$data[9],
                            'needItem'=>$data[10],
                            'lock'=>$data[11],
                            'sortPriority'=>$data[12]
                        );
                    }
                    $aryInfo[$mapId][$id] = $tmpData;
                }
                fclose($handle);
            }

            if ($aryInfo) {
                $dal = Hapyfish2_Admin_Dal_Basic::getDefaultInstance();
                foreach ($aryInfo as $mid => $mdata) {
                    $info = array('map_id'=>$mid, $updField=>json_encode($mdata));
                    $rst = $dal->addInfo('alchemy_map_copy', $info);
                    $cntSuccess ++;
                }
            }
	    }
	    catch (Exception $e) {
            info_log('importMapCopyDataFromFile:'.$e->getMessage(), 'errAdmin_Bll_Basic');
            return null;
	    }

	    return $aryInfo;
    }

    public static function getBasicList()
	{
		$list = array();

$list['line_0'] = array(
    'tbid'		=> '战斗相关',
    'name'	    => 'line'
);
		$list['alchemy_fight_restriction'] = array(
		    'name'	    => '职业元素相克表',
		    'tbid'		=> 'alchemy_fight_restriction',
			'key'		=> 'id',
			'column'	=> array('id'=>'id', 'job_pair'=>'职业互克加成（1-战士 2-弓手 3-法师）', 'element_pair'=>'元素互克加成（1-风 2-火 3-水）')
		);

		$list['alchemy_fight_declaration'] = array(
		    'name'	    => '开战对话表',
		    'tbid'		=> 'alchemy_fight_declaration',
			'key'		=> 'id',
			'column'	=> array('id'=>'id', 'talk'=>'对话宣言', 'job'=>'职业（1-战士 2-弓手 3-法师）')
		);

		$list['alchemy_fight_monster_matrix'] = array(
		    'name'	    => '怪物站位规则',
		    'tbid'		=> 'alchemy_fight_monster_matrix',
			'key'		=> 'id',
			'column'	=> array('id'=>'id', 'lev'=>'lev', 'matrix'=>'站位规则')
		);
	    /*$list['alchemy_mercenary'] = array(
		    'name'	    => '佣兵职业基础表',
		    'tbid'		=> 'alchemy_mercenary',
			'key'		=> 'id',
			'column'	=> array('cid'=>'cid', 'job'=>'职业', 'name'=>'名称', 'class_name_1'=>'class_name_1', 'class_name_2'=>'class_name_2')
		);

	    $list['alchemy_mercenary_level'] = array(
		    'name'	    => '佣兵等级基础表',
		    'tbid'		=> 'alchemy_mercenary_level',
			'key'		=> 'level,id',
			'column'	=> array('level'=>'level', 'id'=>'id', 'exp'=>'exp', 'hp'=>'hp', 'mp'=>'mp',
								 'phy_att'=>'phy_att', 'phy_def'=>'phy_def', 'mag_att'=>'mag_att', 'mag_def'=>'mag_def',
								 'agility'=>'agility', 'crit'=>'crit', 'dodge'=>'dodge')
		);

		$list['alchemy_attribute_level'] = array(
		    'name'	    => '主角等级基础表',
		    'tbid'		=> 'alchemy_attribute_level',
			'key'		=> 'level',
			'column'	=> array('level'=>'level', 'exp'=>'exp', 'hp'=>'hp', 'mp'=>'mp',
								 'phy_att'=>'phy_att', 'phy_def'=>'phy_def', 'mag_att'=>'mag_att', 'mag_def'=>'mag_def',
								 'agility'=>'agility', 'crit'=>'crit', 'dodge'=>'dodge')
		);*/

		$cKeys = array('id','tid','level','skill_id','element','job','need_count','need_lev','need_invite');
		$cVals = array('id','组id','援攻等级','技能id','需要属性','需要职业','需要好友数','需要等级 ','需要邀请(1-战士,2-弓手,3-法师)');
		$list['alchemy_fight_assistance'] = array(
		    'name'	    => '援攻技能条件',
		    'tbid'		=> 'alchemy_fight_assistance',
			'key'		=> 'id',
			'column'	=> array_combine($cKeys, $cVals)
		);

		$cKeys = array('cid','name','content','class_name','jobs','target','range','area','dodge_accept','crit_accept','resist','mp','effect','disp_script','ai_script');
		$cVals = array('cid','name','content','class_name','jobs','target','range','area','dodge_accept','crit_accept','resist','mp','effect','disp_script','ai_script');
		$list['alchemy_effect'] = array(
		    'name'	    => '技能|物品效果表',
		    'tbid'		=> 'alchemy_effect',
			'key'		=> 'cid',
			'column'	=> array_combine($cKeys, $cVals)
		);

		$cKeys = array('id','kl1','kl2','kd1','kd2','max','min');
		$cVals = array('id','kl1(*100)','kl2(*100)','kd1(*100)','kd2(*100)','max(*100)','min(*100)');
		$list['alchemy_fight_exp_leveldiff'] = array(
		    'name'	    => '佣兵、怪物等级差时经验公式',
		    'tbid'		=> 'alchemy_fight_exp_leveldiff',
			'key'		=> 'id',
			'column'	=> array_combine($cKeys, $cVals)
		);
		
$list['line_1'] = array(
    'tbid'		=> '探险',
    'name'	    => 'line'
);

		$cKeys = array('cid','tid','job','name','content','avatar_class_name','class_name','face_class_name','s_face_class_name','element','level','hp','mp','phy_att','phy_def','mag_att','mag_def','agility','crit','dodge','hit','tou','size_x','size_z','collision_range','is_boss','weapon','skill','award_conditions','first_award_conditions','award_exp','talk');
		$cVals = array('cid','组id','职业','name','content','avatar_class_name','class_name','face_class_name','s_face_class_name','1风2火3水','level','hp','mp','phy_att','phy_def','mag_att','mag_def','agility','crit','dodge','命中','韧性','size_x','size_z','碰撞范围','是否boss','武器','技能','击杀奖励','首杀奖励','基础Exp','开战对白');
		$list['alchemy_monster'] = array(
		    'name'	    => '怪物基础表',
		    'tbid'		=> 'alchemy_monster',
			'key'		=> 'cid',
			'column'	=> array_combine($cKeys, $cVals)
		);

		$cKeys = array('cid','type','name','content','avatar_class_name','hp','size_x','size_z','need_conditions','award_conditions','first_award_conditions','relation');
		$cVals = array('cid','type','name','content','avatar_class_name','hp','size_x','size_z','消耗','奖励','首次奖励','relation');
		$list['alchemy_mine'] = array(
		    'name'	    => '矿基础表',
		    'tbid'		=> 'alchemy_mine',
			'key'		=> 'cid',
			'column'	=> array_combine($cKeys, $cVals)
		);

		$cKeys = array('cid','enter_scene','name','content','class_name','x','y','need_sp','links', 'role_level', 'is_lock');
		$cVals = array('cid','enter_scene','name','content','class_name','x','y','need_sp','links', 'role_level', 'is_lock');
		$list['alchemy_world_map'] = array(
		    'name'	    => '世界地图基础表',
		    'tbid'		=> 'alchemy_world_map',
			'key'		=> 'cid',
			'column'	=> array_combine($cKeys, $cVals)
		);

		$cKeys = array('map_id','jump','condition','monster_data','mine_data','portal_data','decor_data','floor_data');
		$cVals = array('map_id','是否入口','进入限制条件','怪','矿','门','decor_data','floor_data');
		$list['alchemy_map_copy'] = array(
		    'name'	    => '地图编辑器-信息',
		    'tbid'		=> 'alchemy_map_copy',
			'key'		=> 'map_id',
			'column'	=> array_combine($cKeys, $cVals)
		);

		$cKeys = array('id','cid','map_id','name','class_name','face_class','x','z','click_type','click_value','face_x','face_z','fiddle_range_x','fiddle_range_z','tips', 'exist');
		$cVals = array('id','cid','地图id','name','class_name','face_class','x','z','click_type','click_value','face_x','face_z','fiddle_range_x','fiddle_range_z','tips', 'exist');
		$list['alchemy_map_copy_person'] = array(
		    'name'	    => '地图  NPC 列表',
		    'tbid'		=> 'alchemy_map_copy_person',
			'key'		=> 'id',
			'column'	=> array_combine($cKeys, $cVals)
		);
		
		$cKeys = array('map_id','name','cost_num','order','group','entrance_id','reachable');
		$cVals = array('副本id','名称','需要道具数','排序id','地图组名称','入口场景id','能否到达,1:能,0:否');
		$list['alchemy_map_copy_transport'] = array(
		    'name'	    => '副本传送门',
		    'tbid'		=> 'alchemy_map_copy_transport',
			'key'		=> 'map_id',
			'column'	=> array_combine($cKeys, $cVals)
		);
		
$list['line_99'] = array(
    'tbid'		=> '任务相关',
    'name'	    => 'line'
);

        $cKeys = array('id','desp','is_client_action');
		$cVals = array('id','描述','是否客户端触发（默认0）');
		$list['alchemy_task_type'] = array(
		    'name'	    => '条件逻辑类型',
		    'tbid'		=> 'alchemy_task_type',
			'key'		=> 'id',
			'column'	=> array_combine($cKeys, $cVals)
		);

		$cKeys = array('id','desp','condition_type','cid','icon_cid','num','classname_type','kind','scene_id','target_id','node');
		$cVals = array('id','描述','任务逻辑类型id','类id','图片id','数量','前端区分表现图片用type','引导类型','场景id','目标id','目标格子');
		$list['alchemy_task_condition'] = array(
		    'name'	    => '任务条件',
		    'tbid'		=> 'alchemy_task_condition',
			'key'		=> 'id',
			'column'	=> array_combine($cKeys, $cVals)
		);

		$cKeys = array('id','label','priority','condition_ids','complete_cost','need_user_level','need_fight_level','front_task_id','next_task_id','from_type','is_auto_complete','title','npc_id','npc_name','npc_classname','worldmap_id','foreword','help_desp','done_desp','guide','accept_story','story','awards');
		$cVals = array('id','label','priority','condition_ids','complete_cost','need_user_level','need_fight_level','front_task_id','next_task_id','from_type','is_auto_complete','title','npc_id','npc_name','npc_classname','worldmap_id','foreword','help_desp','done_desp','guide','accept_story','story','awards');
		$list['alchemy_task'] = array(
		    'name'	    => '任务表',
		    'tbid'		=> 'alchemy_task',
			'key'		=> 'id',
			'column'	=> array_combine($cKeys, $cVals)
		);

$list['line_98'] = array(
    'tbid'		=> '礼物',
    'name'	    => 'line'
);
		$cKeys = array('gid','type','need_lev','name','class_name','sort','is_online');
		$cVals = array('物品id','type','need_lev','name','class_name','sort','is_online');
		$list['alchemy_gift'] = array(
		    'name'	    => '礼物基础表',
		    'tbid'		=> 'alchemy_gift',
			'key'		=> 'gid',
			'column'	=> array_combine($cKeys, $cVals)
		);
/*************************************************************************************************/
$list['line_2'] = array(
    'tbid'		=> '基础物品',
    'name'	    => 'line'
);

	    $list['alchemy_goods'] = array(
		    'name'	    => '物品(1x)基础表',
		    'tbid'		=> 'alchemy_goods',
			'key'		=> 'cid',
			'column'	=> array('cid'=>'cid', 'type'=>'type', 'func_id'=>'物品作用cid', 'name'=>'name', 'tips'=>'tips', 'content'=>'content',
								 'class_name'=>'class_name', 'is_new'=>'is_new', 'can_buy'=>'can_buy', 'buy_coin'=>'buy_coin',
								 'buy_gem'=>'buy_gem', 'buy_coin_vip'=>'buy_coin_vip', 'buy_gem_vip'=>'buy_gem_vip', 'sale_coin'=>'售系统价格', 'worth'=>'售顾客价格', 'lose'=>'是否消耗(1消耗)', 'need_level'=>'使用物品需要等级', 'label'=>'label', 'buy_level'=>'购买需要等级', 'sort'=>'排序','useLevel'=>'使用等级','useType'=>'使用类型')
		);

	    $list['alchemy_scroll'] = array(
		    'name'	    => '卷轴(2x)基础表',
		    'tbid'		=> 'alchemy_scroll',
			'key'		=> 'cid',
			'column'	=> array('cid'=>'cid', 'mix_cid'=>'合成术cid', 'jobs'=>'职业列表', 'type'=>'type', 'element'=>'element', 'name'=>'name', 'content'=>'content',
								 'class_name'=>'class_name', 'skill_class_name'=>'skill_class_name', 'need_level'=>'need_level', 'is_new'=>'is_new', 'can_buy'=>'can_buy', 'buy_coin'=>'buy_coin',
								 'buy_gem'=>'buy_gem', 'sale_coin'=>'售系统价格', 'worth'=>'售顾客价格', 'buy_level'=>'购买需要等级', 'sort'=>'排序',
								 'skill_type'=>'技能类型(1,2,3,4,5)', 'skill_level'=>'技能等级', 'next_level_cid'=>'下等级技能cid（最高级填0）', 'levelup_needs'=>'升级需要')
		);

	    $list['alchemy_stuff'] = array(
		    'name'	    => '材料(3x)基础表',
		    'tbid'		=> 'alchemy_stuff',
			'key'		=> 'cid',
			'column'	=> array('cid'=>'cid', 'type'=>'type', 'name'=>'name', 'content'=>'content',
								 'class_name'=>'class_name', 'is_new'=>'is_new', 'can_buy'=>'can_buy', 'buy_coin'=>'buy_coin',
								 'buy_gem'=>'buy_gem', 'buy_coin_vip'=>'buy_coin_vip', 'buy_gem_vip'=>'buy_gem_vip', 'sale_coin'=>'售系统价格', 'worth'=>'售顾客价格', 'buy_level'=>'购买需要等级', 'sort'=>'排序')
		);

	    $list['alchemy_furnace'] = array(
		    'name'	    => '工作台(4x)基础表',
		    'tbid'		=> 'alchemy_furnace',
			'key'		=> 'cid',
			'column'	=> array('cid'=>'cid', 'type'=>'type', 'size_x'=>'宽', 'size_z'=>'高',
								 'mix_cids'=>'合成术id列表', 'mix_cids'=>'合成术id列表', 'mix_type'=>'获得物品type',
	    						 'name'=>'name', 'content'=>'content', 'class_name'=>'class_name', 'is_new'=>'is_new', 'can_buy'=>'can_buy',
	    						 'buy_coin'=>'buy_coin', 'buy_gem'=>'buy_gem', 'sale_coin'=>'售系统价格', 'worth'=>'售顾客价格', 'buy_level'=>'购买需要等级', 'sort'=>'排序',
	    						 'expand_needs_2'=>'第二格子的需求', 'expand_needs_3'=>'第三格子的需求', 'expand_needs_4'=>'第四格子的需求', 'expand_needs_5'=>'第五格子的需求', 'expand_needs_6'=>'第六格子的需求')
		);

	    $list['alchemy_decor'] = array(
		    'name'	    => '装饰物(5x)基础表',
		    'tbid'		=> 'alchemy_decor',
			'key'		=> 'cid',
			'column'	=> array('cid'=>'cid', 'type'=>'type', 'size_x'=>'宽', 'size_z'=>'高',
	    						 'name'=>'name', 'content'=>'content', 'class_name'=>'class_name', 'is_new'=>'is_new', 'can_buy'=>'can_buy',
	    						 'buy_coin'=>'buy_coin', 'buy_gem'=>'buy_gem', 'sale_coin'=>'售系统价格', 'worth'=>'售顾客价格', 'buy_level'=>'购买需要等级', 'sort'=>'排序')
		);

	    $list['alchemy_equipment'] = array(
		    'name'	    => '装备(6x)基础表',
		    'tbid'		=> 'alchemy_equipment',
			'key'		=> 'cid',
			'column'	=> array('cid'=>'cid', 'type'=>'type', 'level'=>'需要等级', 'jobs'=>'职业列表', 'pa'=>'物攻',
								 'pd' => '物防', 'ma'=>'魔攻', 'md'=>'魔防', 'speed'=>'速度', 'hp'=>'hp加成', 'mp'=>'mp加成', 'cri' => '爆击', 'dod'=>'闪避','hit'=>'命中','tou'=>'幸运（韧性）',
	    						 'name'=>'name', 'content'=>'content', 'class_name'=>'class_name', 'is_new'=>'is_new', 'can_buy'=>'can_buy',
	    						 'buy_coin'=>'buy_coin', 'buy_gem'=>'buy_gem', 'sale_coin'=>'售系统价格', 'worth'=>'售顾客价格', 'durability'=>'耐久',
	    						 'can_repair' => '是否可修', 'repair_level' => '修理等级', 'repair_price' => '修理价格', 'buy_level'=>'购买需要等级', 'sort'=>'排序','costCoin'=>'强化花费','strGrow'=>'强化基础值','itemLevel'=>'物品等级')
		);

	    $list['alchemy_mercenary_card'] = array(
		    'name'	    => '佣兵卡(17)基础表',
		    'tbid'		=> 'alchemy_mercenary_card',
			'key'		=> 'cid',
			'column'	=> array('cid'=>'cid', 'type'=>'type', 'func_id'=>'物品作用cid', 'name'=>'name', 'tips'=>'tips', 'content'=>'content',
								 'class_name'=>'class_name', 'is_new'=>'is_new', 'can_buy'=>'can_buy', 'buy_coin'=>'buy_coin',
								 'buy_gem'=>'buy_gem', 'buy_coin_vip'=>'buy_coin_vip', 'buy_gem_vip'=>'buy_gem_vip', 'sale_coin'=>'售系统价格', 'worth'=>'售顾客价格', 'lose'=>'是否消耗(1消耗)', 
								 'need_level'=>'need_level', 'label'=>'label', 'buy_level'=>'购买需要等级', 'sort'=>'排序','useLevel'=>'使用等级',
	    						 'rp'=>'rp', 'gid'=>'gid', 'job'=>'job', 'sex'=>'sex', 'avatar'=>'avatar', 'm_name'=>'m_name',
								 'm_class_name'=>'m_class_name', 'face_class_name'=>'face_class_name', 's_face_class_name'=>'s_face_class_name',
	    						 'scene_player_class'=>'scene_player_class',
								 'skill'=>'skill', 'hp'=>'hp', 'mp'=>'mp', 'phy_att'=>'phy_att', 'phy_def'=>'phy_def',
								 'mag_att'=>'mag_att', 'mag_def'=>'mag_def', 'agility'=>'agility', 'crit'=>'crit', 'dodge'=>'dodge',
	    						 'hit'=>'hit', 'tou'=>'tou', 'str'=>'力量', 'dex'=>'敏捷', 'mag'=>'魔法', 'phy'=>'体质',
								 'q_hp'=>'q_hp','q_mp'=>'q_mp', 'q_phy_att'=>'q_phy_att', 'q_phy_def'=>'q_phy_def',
								 'q_mag_att'=>'q_mag_att', 'q_mag_def'=>'q_mag_def', 'q_agility'=>'q_agility', 'q_crit'=>'q_crit', 'q_dodge'=>'q_dodge', 'q_hit'=>'q_hit', 'q_tou'=>'q_tou',
								 'q_str'=>'q_str', 'q_dex'=>'q_dex', 'q_mag'=>'q_mag', 'q_phy'=>'q_phy',
								 'element'=>'属性（风火水）', 'weapon'=>'装备')
		);
		
	    $list['alchemy_mix'] = array(
		    'name'	    => '合成术基础表',
		    'tbid'		=> 'alchemy_mix',
			'key'		=> 'cid',
			'column'	=> array('cid'=>'cid', 'furnace_cid'=>'需要工作台cid', 'item_cid'=>'合成物品cid', 'fail_item'=>'失败获得材料',
	    						 'name'=>'name', 'coin'=>'需要金币', 'gem'=>'需要宝石', 'need_level'=>'需要炼金熟练度', 'needs'=>'需要材料', 'sp'=>'需要sp',
	    						 'exp'=>'获得经验', 'time'=>'需要时间', 'max_time'=>'时间上限', 'probability'=>'初始成功率',
	    						 'probability_change_value'=>'提高合成机率量', 'per_probability_gem'=>'提高合成机率花费')
		);

	    $list['alchemy_illustrations'] = array(
		    'name'	    => '图鉴基础表',
		    'tbid'		=> 'alchemy_illustrations',
			'key'		=> 'id',
			'column'	=> array('id'=>'id(对应物品cid)', 'name'=>'name', 'class_name'=>'class_name', 'content'=>'介绍',
								 'source' => '来源介绍', 'mix_cid'=>'合成术id', 'order_id'=>'排序id')
		);

	    $list['alchemy_scene'] = array(
		    'name'	    => '场景基础表',
		    'tbid'		=> 'alchemy_scene',
			'key'		=> 'id',
			'column'	=> array('id'=> 'id', 'type' => '类型', 'name'=>'name', 'content'=>'content',
								 'bg' => 'bg', 'bg_sound'=>'bg_sound', 'node_str'=>'node_str', 'entrances' => 'entrances',
								 'num_cols' => 'num_cols', 'num_rows' => 'num_rows', 'iso_star_x' => 'iso_star_x',
								 'iso_star_y' => 'iso_star_y', 'parent_sceneId' => 'parent_sceneId','need_level'=>'need_level')
		);

$list['line_3'] = array(
    'tbid'		=> '等级信息',
    'name'	    => 'line'
);

	    $list['alchemy_level'] = array(
		    'name'	    => '用户等级基础表',
		    'tbid'		=> 'alchemy_level',
			'key'		=> 'level',
			'column'	=> array('level'=>'等级', 'exp'=>'exp', 'max_sp'=>'max_sp', 'items'=>'升级赠送道具',
								 'gem' => 'gem', 'coin'=>'coin', 'title'=>'称号id', 'title_class_name'=>'称号素材','assistance'=>'援助攻击次数')
		);

	    $list['alchemy_level_room'] = array(
		    'name'	    => '房间等级基础表',
		    'tbid'		=> 'alchemy_level_room',
			'key'		=> 'level',
			'column'	=> array('level'=> '等级', 'need_level' => '需要用户等级', 'tile_x_length'=>'房间大小', 'tile_z_length'=>'房间大小',
								 'coin' => 'coin', 'gem'=>'gem', 'items'=>'消耗物品')
		);

$list['line_4'] = array(
    'tbid'		=> '经营-合成',
    'name'	    => 'line'
);

	    $list['alchemy_avatar'] = array(
		    'name'	    => '订单NPC基础表',
		    'tbid'		=> 'alchemy_avatar',
			'key'		=> 'id',
			'column'	=> array('id'=>'id', 'name'=>'name', 'face'=>'face', 'type'=>'type', 'class_name'=>'class_name')
		);

	    $list['alchemy_avatar_name'] = array(
		    'name'	    => '订单NPC名称基础表',
		    'tbid'		=> 'alchemy_avatar_name',
			'key'		=> 'id',
			'column'	=> array('id'=>'id', 'name'=>'name')
		);

	    $list['alchemy_order'] = array(
		    'name'	    => '订单基础表',
		    'tbid'		=> 'alchemy_order',
			'key'		=> 'cid',
			'column'	=> array('cid'=>'cid', 'needs'=>'nedds', 'out_time'=>'out_time', 'need_level'=>'need_level', 'coin'=>'coin', 'exp'=>'exp',
								 'awards'=>'awards', 'level'=>'level', 'award_type'=>'award_type', 'avatar_ids'=>'avatar_ids', 'avatar_name'=>'avatar_name',
								 'need_iid'=>'需求图鉴id', 'start_dialog_1'=>'需求时对白-1',
								 'finish_dialog_1'=>'完成时对白-1', 'drop_dialog_1'=>'失败时对白-1', 'start_dialog_2'=>'需求时对白-2',
	    						 'finish_dialog_2'=>'完成时对白-2', 'drop_dialog_2'=>'失败时对白-2', 'start_dialog_3'=>'需求时对白-3',
	    						 'finish_dialog_3'=>'完成时对白-3', 'drop_dialog_3'=>'失败时对白-3')
		);

	    $list['alchemy_order_pro'] = array(
		    'name'	    => '订单概率表',
		    'tbid'		=> 'alchemy_order_pro',
			'key'		=> 'user_level',
			'column'	=> array('user_level'=>'用户等级', 'level_1'=>'1级订单', 'level_2'=>'2级订单', 'level_3'=>'3级订单', 'level_4'=>'4级订单',
								 'level_5'=>'5级订单', 'level_6'=>'6级订单', 'level_7'=>'7级订单', 'level_8'=>'8级订单', 'level_9'=>'9级订单',
								 'level_10'=>'10级订单')
		);

$list['line_5'] = array(
    'tbid'		=> '佣兵',
    'name'	    => 'line'
);

	   /*  $list['alchemy_mercenary_model_old'] = array(
		    'name'	    => '佣兵模型表-老-停用',
		    'tbid'		=> 'alchemy_mercenary_model_old',
			'key'		=> 'cid',
			'column'	=> array('cid'=>'cid', 'rp'=>'rp', 'job'=>'job', 'name_id'=>'name_id',
								 'hp'=>'hp', 'mp'=>'mp', 'phy_att'=>'phy_att', 'phy_def'=>'phy_def',
								 'mag_att'=>'mag_att', 'mag_def'=>'mag_def', 'agility'=>'agility', 'crit'=>'crit', 'dodge'=>'dodge')
		);

	    $list['alchemy_mercenary_grow_class'] = array(
		    'name'	    => '佣兵成长比较模板表-老-停用',
		    'tbid'		=> 'alchemy_mercenary_grow_class',
			'key'		=> 'rp',
			'column'	=> array('rp'=>'rp', 'job'=>'职业 1-战士，2-弓手，3-法师', 'hp'=>'hp', 'mp'=>'mp', 'phy_att'=>'phy_att', 'phy_def'=>'phy_def', 'mag_att'=>'mag_att', 'mag_def'=>'mag_def', 'agility'=>'agility')
		);
		
	    $list['alchemy_mercenary_grow_old'] = array(
		    'name'	    => '佣兵成长表-老-停用',
		    'tbid'		=> 'alchemy_mercenary_grow_old',
			'key'		=> 'id',
			'column'	=> array('id'=>'id', 'rp'=>'rp', 'job'=>'job', 'hp'=>'hp', 'mp'=>'mp', 'phy_att'=>'phy_att', 'phy_def'=>'phy_def',
								 'mag_att'=>'mag_att', 'mag_def'=>'mag_def', 'agility'=>'agility', 'crit'=>'crit', 'dodge'=>'dodge',
								 'coin'=>'coin', 'feats'=>'神勇点', 'skill'=>'skill', 'hire_type'=>'hire_type')
		); */

	    $list['alchemy_mercenary_model'] = array(
	    		'name'	    => '佣兵模型表-新',
	    		'tbid'		=> 'alchemy_mercenary_model',
	    		'key'		=> 'job',
	    		'column'	=> array('job'=>'job', 'hp'=>'hp', 'mp'=>'mp', 'phy_att'=>'phy_att', 'phy_def'=>'phy_def',
	    				'mag_att'=>'mag_att', 'mag_def'=>'mag_def', 'agility'=>'agility', 'crit'=>'crit', 'dodge'=>'dodge',
	    				'hit'=>'hit', 'tou'=>'tou',
	    				'str'=>'力量', 'dex'=>'敏捷', 'mag'=>'魔法', 'phy'=>'体质')
	    );
	    
	    $list['alchemy_mercenary_grow'] = array(
	    		'name'	    => '佣兵成长表-新',
	    		'tbid'		=> 'alchemy_mercenary_grow',
	    		'key'		=> 'job',
	    		'column'	=> array('job'=>'job', 'hp'=>'hp', 'mp'=>'mp', 'phy_att'=>'phy_att', 'phy_def'=>'phy_def',
	    				'mag_att'=>'mag_att', 'mag_def'=>'mag_def', 'agility'=>'agility', 'crit'=>'crit', 'dodge'=>'dodge',
	    				'hit'=>'hit', 'tou'=>'tou',
	    				'str'=>'力量', 'dex'=>'敏捷', 'mag'=>'魔法', 'phy'=>'体质')
	    );
	     
	    $list['alchemy_mercenary_name'] = array(
		    'name'	    => '佣兵名称表',
		    'tbid'		=> 'alchemy_mercenary_name',
			'key'		=> 'id',
			'column'	=> array('id'=>'id', 'name'=>'name', 'content'=>'content', 'sex'=>'sex',
								 'class_name_1'=>'class_name_1', 'class_name_2'=>'class_name_2',
								 'face_class_name_1'=>'face_class_name_1', 'face_class_name_2'=>'face_class_name_2',
								 's_face_class_name_1'=>'s_face_class_name_1', 's_face_class_name_2'=>'s_face_class_name_2',
								 'scene_player_class_1' => 'scene_player_class_1', 'scene_player_class_2'=>'scene_player_class_2',
								 'job'=>'job', 'hire_type'=>'hire_type', 'hire_level'=>'hire_level')
		);

	    $list['alchemy_mercenary_level'] = array(
		    'name'	    => '佣兵等级经验表',
		    'tbid'		=> 'alchemy_mercenary_level',
			'key'		=> 'level',
			'column'	=> array('level'=>'level', 'exp'=>'exp')
		);

	    /*$list['alchemy_mercenary_strengthen'] = array(
		    'name'	    => '佣兵强化表',
		    'tbid'		=> 'alchemy_mercenary_strengthen',
			'key'		=> 'id',
			'column'	=> array('id'=>'id', 'job'=>'job', 'type'=>'type', 'level'=>'level', 
								 'min_phy_att'=>'min_phy_att', 'min_phy_def'=>'min_phy_def', 'min_mag_att'=>'min_mag_att', 'min_mag_def'=>'min_mag_def', 
								 'min_agility'=>'min_agility', 'min_crit'=>'min_crit', 'min_dodge'=>'min_dodge', 'min_hit'=>'min_hit', 'min_tou'=>'min_tou',
								 'max_phy_att'=>'max_phy_att', 'max_phy_def'=>'max_phy_def', 'max_mag_att'=>'max_mag_att', 'max_mag_def'=>'max_mag_def', 
								 'max_agility'=>'max_agility', 'max_crit'=>'max_crit', 'max_dodge'=>'max_dodge', 'max_hit'=>'max_hit', 'max_tou'=>'max_tou')
		);*/

	    $list['alchemy_mercenary_strengthen'] = array(
	    		'name'	    => '佣兵强化表',
	    		'tbid'		=> 'alchemy_mercenary_strengthen',
	    		'key'		=> 'id',
	    		'column'	=> array('id'=>'id', 'job'=>'job', 'type'=>'type', 'level'=>'level',
	    				'min_str'=>'min_str', 'min_dex'=>'min_dex', 'min_mag'=>'min_mag', 'min_phy'=>'min_phy',
	    				'max_str'=>'max_str', 'max_dex'=>'max_dex', 'max_mag'=>'max_mag', 'max_phy'=>'max_phy')
	    );
	    
	    $list['alchemy_mercenary_position'] = array(
		    'name'	    => '佣兵位置扩展表',
		    'tbid'		=> 'alchemy_mercenary_position',
			'key'		=> 'point_id',
			'column'	=> array('position'=>'position', 'coin'=>'coin', 'gem'=>'gem', 'level'=>'level')
		);
		
	    $list['alchemy_mercenary_work'] = array(
		    'name'	    => '佣兵派驻打工表',
		    'tbid'		=> 'alchemy_mercenary_work',
			'key'		=> 'id',
			'column'	=> array('id'=>'派驻点id', 'bg_id'=>'地图id', 'name'=>'name', 'icon_class'=>'icon_class',
								 'x'=>'x', 'y'=>'y', 'awards'=>'固定奖励', 'sp'=>'sp', 'role_level'=>'需要佣兵等级', 'role_num'=>'需要佣兵个数',
								 'need_time'=>'需要时间', 'random_award_pro'=>'出现随机奖励的概率', 'random_award_data'=>'随机奖励[[3334,1,80],[cid,num,pro]]')
		);

	    $list['alchemy_mercenary_rp_rand'] = array(
	    		'name'	    => '佣兵星级随机表',
	    		'tbid'		=> 'alchemy_mercenary_rp_rand',
	    		'key'		=> 'level',
	    		'column'	=> array('level'=>'level', 'type'=>'type,酒馆刷新类型:0-3', 'pro1'=>'pro1', 'pro2'=>'pro2', 'pro3'=>'pro3', 'pro4'=>'pro4', 'pro5'=>'pro5', 
	    							 'pro6'=>'pro6', 'pro7'=>'pro7', 'pro8'=>'pro8', 'pro9'=>'pro9', 'pro10'=>'pro10')
	    );

	    $list['alchemy_mercenary_quality'] = array(
	    		'name'	    => '佣兵属性品质随机表',
	    		'tbid'		=> 'alchemy_mercenary_quality',
	    		'key'		=> 'rp',
	    		'column'	=> array('rp'=>'rp', 'type'=>'type,酒馆刷新类型:0-3', 'quality_1'=>'品质D概率(%)', 'quality_2'=>'品质C概率(%)', 'quality_3'=>'品质B概率(%)', 'quality_4'=>'品质A概率(%)', 'quality_5'=>'品质S概率(%)')
	    );

	    $list['alchemy_mercenary_pro_contrast'] = array(
	    		'name'	    => '佣兵属性对照表',
	    		'tbid'		=> 'alchemy_mercenary_pro_contrast',
	    		'key'		=> 'job',
	    		'column'	=> array('job'=>'job', 'type'=>'type(1-力量，2-敏捷，3-魔法，4-体质)', 'hp'=>'hp（数值放大100倍）', 'mp'=>'mp', 'phy_att'=>'phy_att', 'phy_def'=>'phy_def',
				    				 'mag_att'=>'mag_att', 'mag_def'=>'mag_def', 'agility'=>'agility', 'crit'=>'crit', 'dodge'=>'dodge',
				    				 'hit'=>'hit', 'tou'=>'tou', 'sum'=>'11属性总和')
	    );
	    
$list['line_6'] = array(
    'tbid'		=> '剧情',
    'name'	    => 'line'
);

	    $list['alchemy_story'] = array(
		    'name'	    => '剧情表',
		    'tbid'		=> 'alchemy_story',
			'key'		=> 'sid',
			'column'	=> array('sid'=>'sid', 'scene_id'=>'scene_id', 'end_at'=>'end_at', 'coin'=>'coin',
								 'gem'=>'gem', 'items'=>'items', 'action_ids'=>'action_ids', 'npc_ids'=>'npc_ids', 
								 'task_id'=>'task_id', 'next_sid'=>'next_sid', 'open_scene'=>'open_scene',
								 'add_person'=>'add_person', 'remove_person'=>'remove_person', 'add_hero'=>'add_hero', 'remove_hero'=>'remove_hero')
		);

	    $list['alchemy_story_action'] = array(
		    'name'	    => '动作表',
		    'tbid'		=> 'alchemy_story_action',
			'key'		=> 'aid',
			'column'	=> array('aid'=>'aid', 'nid'=>'nid', 'x'=>'x', 'y'=>'y', 'faceX'=>'faceX', 'faceY'=>'faceY',
								 'content'=>'content', 'chat_time'=>'chat_time', 'camera'=>'camera', 'wait'=>'wait', 'immediately'=>'immediately',
								 'hide'=>'hide', 'class_name'=>'class_name', 'shock_screen_time'=>'shock_screen_time',
								 'action_label'=>'action_label', 'label_times'=>'label_times', 'to_stop'=>'to_stop')
		);

	    $list['alchemy_story_npc'] = array(
		    'name'	    => '演员表',
		    'tbid'		=> 'alchemy_story_npc',
			'key'		=> 'nid',
			'column'	=> array('nid'=>'nid', 'name'=>'name', 'class_name'=>'class_name', 'head'=>'head')
		);

	    $list['alchemy_story_dialog'] = array(
		    'name'	    => '对白表',
		    'tbid'		=> 'alchemy_story_dialog',
			'key'		=> 'id',
			'column'	=> array('id'=>'id', 'nid'=>'nid', 'scene_id'=>'scene_id', 'dialog'=>'dialog',
								 'user_level'=>'user_level', 'fight_level'=>'fight_level', 'task_id'=>'task_id', 
								 'fight_id'=>'fight_id', 'fight_type'=>'战斗类型(1:必须成功,0:可以失败)')
		);

$list['line_7'] = array(
    'tbid'		=> '升级',
    'name'	    => 'line'
);

	    $list['alchemy_level_home'] = array(
		    'name'	    => '自宅等级表',
		    'tbid'		=> 'alchemy_level_home',
			'key'		=> 'level',
			'column'	=> array('level'=>'level', 'order_count'=>'order_count', 'max_mercenary_count' => '最大佣兵位置数', 'tile_x_length'=>'tile_x_length', 'tile_z_length'=>'tile_z_length',
								 'content'=>'content', 'need_level'=>'need_level', 'need_items'=>'need_items', 'need_coin'=>'need_coin', 'need_prestige'=>'need_prestige',
								 'npc_class'=>'npc_class', 'npc_chat'=>'npc_chat', 'cd_time' => 'cd_time')
		);

	    $list['alchemy_level_tavern'] = array(
		    'name'	    => '酒馆等级表',
		    'tbid'		=> 'alchemy_level_tavern',
			'key'		=> 'level',
			'column'	=> array('id'=>'id', 'level'=>'level', 'content'=>'content', 'need_level'=>'need_level',
								 'need_items'=>'need_items', 'need_coin'=>'need_coin', 'need_prestige'=>'need_prestige',
								 'npc_class'=>'npc_class', 'npc_chat'=>'npc_chat', 'refresh_price'=>'refresh_price', 'need_time'=>'需要时间(秒)', 'cd_time' => 'cd_time')
		);

	    $list['alchemy_level_smithy'] = array(
		    'name'	    => '铁匠铺等级表',
		    'tbid'		=> 'alchemy_level_smithy',
			'key'		=> 'level',
			'column'	=> array('level'=>'level', 'content'=>'content', 'need_level'=>'need_level',
								 'need_items'=>'need_items', 'need_coin'=>'need_coin', 'need_prestige'=>'need_prestige',
								 'npc_class'=>'npc_class', 'npc_chat'=>'npc_chat', 'discount'=>'降低费用(%)', 'cd_time' => 'cd_time')
		);

	    $list['alchemy_level_arena'] = array(
		    'name'	    => '竞技场等级表',
		    'tbid'		=> 'alchemy_level_arena',
			'key'		=> 'level',
			'column'	=> array('level'=>'level', 'content'=>'content', 'need_level'=>'need_level',
								 'need_items'=>'need_items', 'need_coin'=>'need_coin', 'need_prestige'=>'need_prestige',
								 'npc_class'=>'npc_class', 'npc_chat'=>'npc_chat', 'cd_time' => 'cd_time')
		);

	    $list['alchemy_level_training'] = array(
	    		'name'	    => '训练营等级表',
	    		'tbid'		=> 'alchemy_level_training',
	    		'key'		=> 'level',
	    		'column'	=> array('level'=>'level', 'content'=>'content', 'need_level'=>'need_level',
	    				'need_items'=>'need_items', 'need_coin'=>'need_coin', 'need_prestige'=>'need_prestige',
	    				'npc_class'=>'npc_class', 'npc_chat'=>'npc_chat', 'cd_time' => 'cd_time')
	    );
	    
	    $list['alchemy_level_role'] = array(
		    'name'	    => '主角资质表',
		    'tbid'		=> 'alchemy_level_role',
			'key'		=> 'level',
			'column'	=> array('level'=>'level','need_level'=>'need_level', 'need_role_level'=>'need_role_level', 'need_coin'=>'need_coin', 
								 'need_items'=>'need_items', 'npc_class'=>'npc_class', 'npc_chat'=>'npc_chat')
		);

$list['line_8'] = array(
    'tbid'		=> '初始化',
    'name'	    => 'line'
);

	    $list['alchemy_init_role'] = array(
		    'name'	    => '主角初始值',
		    'tbid'		=> 'alchemy_init_role',
			'key'		=> 'id',
			'column'	=> array('id'=>'id', 'rp'=>'rp', 'gid'=>'gid', 'job'=>'job', 'sex'=>'sex', 'avatar'=>'avatar',
								 'class_name'=>'class_name', 'face_class_name'=>'face_class_name', 's_face_class_name'=>'s_face_class_name',
	    						 'scene_player_class'=>'scene_player_class',
								 'skill'=>'skill', 'hp'=>'hp', 'mp'=>'mp', 'phy_att'=>'phy_att', 'phy_def'=>'phy_def',
								 'mag_att'=>'mag_att', 'mag_def'=>'mag_def', 'agility'=>'agility', 'crit'=>'crit', 'dodge'=>'dodge', 'hit'=>'hit', 'tou'=>'tou',
								 'str'=>'str', 'dex'=>'dex', 'mag'=>'mag', 'phy'=>'phy',
								 'q_hp'=>'q_hp','q_mp'=>'q_mp', 'q_phy_att'=>'q_phy_att', 'q_phy_def'=>'q_phy_def',
								 'q_mag_att'=>'q_mag_att', 'q_mag_def'=>'q_mag_def', 'q_agility'=>'q_agility', 'q_crit'=>'q_crit', 'q_dodge'=>'q_dodge', 'q_hit'=>'q_hit', 'q_tou'=>'q_tou',
								 'q_str'=>'q_str', 'q_dex'=>'q_dex', 'q_mag'=>'q_mag', 'q_phy'=>'q_phy',
								 'name'=>'临时英雄名字', 'element'=>'临时英雄属性（风火水）', 'weapon'=>'临时英雄装备', 'fight_corps'=>'佣兵站位(9-16)', 'task_id'=>'临时英雄对应任务id')
		);

	    $list['alchemy_init_user'] = array(
		    'name'	    => '用户初始值',
		    'tbid'		=> 'alchemy_init_user',
			'key'		=> 'id',
			'column'	=> array('id'=>'id', 'coin'=>'coin', 'gem'=>'gem', 'sp'=>'sp', 'home_size'=>'房间大小', 'wall'=>'wall', 'floor'=>'floor', 
	    						'goods'=>'物品', 'stuff'=>'材料', 'furnace'=>'工作台', 'decor'=>'包裹内装饰物', 'mix'=>'合成术', 'open_scene'=>'打开场景', 'scroll'=>'卷轴', 
	    						'equipment'=>'装备', 'home_decor'=>'房间摆设', 'fight_matrix'=>'fight_matrix', 'illustration'=>'图鉴')
		);
		
	    $list['alchemy_help'] = array(
		    'name'	    => '新手引导',
		    'tbid'		=> 'alchemy_help',
			'key'		=> 'id',
			'column'	=> array('id'=>'引导id', 'content'=>'介绍', 'idx'=>'小索引列表', 'awards'=>'awards')
		);

$list['line_9'] = array(
    'tbid'		=> '竞技场',
    'name'	    => 'line'
);
		
	    $list['alchemy_arena_award'] = array(
		    'name'	    => '竞技场战斗奖励',
		    'tbid'		=> 'alchemy_arena_award',
			'key'		=> 'id',
			'column'	=> array('id'=>'id', 'cid'=>'奖励cid', 'num'=>'奖励num', 'pro'=>'概率', 'type'=>'type:1-战斗胜利,2-失败', 'status'=>'status:1-允许抽奖;0-不可抽奖')
		);

$list['line_10'] = array(
    		'tbid'		=> '训练营',
    		'name'	    => 'line'
);
	    
	    $list['alchemy_training'] = array(
	    		'name'	    => '训练营训练表',
	    		'tbid'		=> 'alchemy_training',
	    		'key'		=> 'id',
	    		'column'	=> array('id'=>'id', 'type'=>'type:类别的ID:1佣兵生命2佣兵魔法3佣兵物理攻击4佣兵魔法攻击5佣兵暴击率6佣兵物理防御7佣兵魔法防御', 'level'=>'训练等级', 'job'=>'职业', 'add_num'=>'普通训练增加值', 
	    							'needs'=>'普通需要物品:[{\"type\":\"2\",\"id\":\"coin\",\"num\":\"150\"},{\"type\":\"2\",\"id\":\"gem\",\"num\":\"10\"},{\"type\":\"1\",\"id\":\"1515\",\"num\":\"10\"}]', 
	    							'add_num_s'=>'高等训练增加值', 'needs_s'=>'高等训练普通需要物品',
	    							'need_training_level'=>'需要训练营等级', 'need_time'=>'升级需要时间', 'name'=>'名称', 'content'=>'介绍')
	    );
	     
$list['line_11'] = array(
    'tbid'		=> '统计表',
    'name'	    => 'line'
);
		
	    $list['alchemy_stat_user_action'] = array(
		    'name'	    => '用户操作记录',
		    'tbid'		=> 'alchemy_stat_user_action',
			'key'		=> 'id',
			'column'	=> array('id'=>'id', 'name'=>'name', 'type'=>'类型:1-引导,2-剧情,3-怪物,9-其他', 'cid'=>'cid', 'order'=>'排序')
		);
		
	    return $list;
	}
}