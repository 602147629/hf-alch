<?php
class Hapyfish2_Stat_Bll_Contrast
{
	public static function  getData($table)
	{
		$data = array();
		$list = array();
		if($table == 'all'){
			$tb = self::getContrastTable();
			foreach($tb as $k => $v){
				$list[] = $k;
			}
		}else{
			$list[] = $table;
		}
		$dal = Hapyfish2_Stat_Dal_Contrast::getDefaultInstance();
		foreach($list as $key => $tb){
			$info['tb'] = $tb;
			$info['data'] = $dal->getData($tb);
			$data[] = $info;
		}
		return $data;
	}
	
	public static function  getContrastTable()
	{
		$arr = array(
			'alchemy_fight_restriction'		=>'职业元素相克表',
			'alchemy_fight_declaration'		=>'开战对话表',
			'alchemy_fight_monster_matrix'	=>'怪物站位规则',
			'alchemy_fight_assistance'		=>'援攻技能条件',
			'alchemy_effect'				=>'技能|物品效果表',
			'alchemy_monster'				=>'怪物基础表',
			'alchemy_mine'					=>'矿基础表',
			'alchemy_world_map'				=>'世界地图基础表',
			'alchemy_map_copy'				=>'地图编辑器-信息',
			'alchemy_map_copy_person'		=>'地图 NPC 列表',
			'alchemy_map_copy_transport'	=>'副本传送门',
			'alchemy_task_type'				=>'条件逻辑类型',
			'alchemy_task_condition'		=>'任务条件',
			'alchemy_task'					=>'任务表',
			'alchemy_gift'					=>'礼物基础表',
			'alchemy_goods'					=>'物品(1x)基础表',
			'alchemy_scroll'				=>'卷轴(2x)基础表',
			'alchemy_stuff'					=>'材料(3x)基础表',
			'alchemy_furnace'				=>'工作台(4x)基础表',
			'alchemy_decor'					=>'装饰物(5x)基础表',	
			'alchemy_equipment'				=>'装备(6x)基础表',
			'alchemy_mix'					=>'合成术基础表',
			'alchemy_illustrations'			=>'图鉴基础表',
			'alchemy_scene'					=>'场景基础表',
			'alchemy_level'					=>'用户等级基础表',
			'alchemy_level_room'			=>'房间等级基础表',
			'alchemy_avatar'				=>'订单NPC基础表',
			'alchemy_avatar_name'			=>'订单NPC名称基础表',
			'alchemy_order'					=>'订单基础表',
			'alchemy_order_pro'				=>'订单概率表',
			'alchemy_mercenary_model'		=>'佣兵模型表',
			'alchemy_mercenary_grow_class'	=>'佣兵成长比较模板表',
			'alchemy_mercenary_grow'		=>'佣兵成长表',
			'alchemy_mercenary_name'		=>'佣兵名称表',
			'alchemy_mercenary_level'		=>'佣兵等级经验表',
			'alchemy_mercenary_strengthen'	=>'佣兵强化表',
			'alchemy_mercenary_position'	=>'佣兵位置扩展表',
			'alchemy_mercenary_work'		=>'佣兵派驻打工表',
			'alchemy_story'					=>'剧情表',
			'alchemy_story_action'			=>'动作表',
			'alchemy_story_npc'				=>'演员表',
			'alchemy_story_dialog'			=>'对白表',
			'alchemy_level_home'			=>'自宅等级表',
			'alchemy_level_tavern'			=>'酒馆等级表',
			'alchemy_level_smithy'			=>'铁匠铺等级表',
			'alchemy_level_role'			=>'主角资质表',
			'alchemy_init_role'				=>'主角初始值',
			'alchemy_init_user'				=>'用户初始值',
			'alchemy_help'					=>'新手引导',
		);
		return $arr;
	}
}
