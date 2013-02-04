<?php

class Hapyfish2_Alchemy_Dal_Basic
{
    protected static $_instance;

    protected function getDB()
    {
    	$key = 'db_0';
    	return Hapyfish2_Db_Factory::getBasicDB($key);
    }

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_Basic
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /*
     * 佣兵角色模型
     */
    public function getMercenary()
    {
        $sql = "SELECT * FROM alchemy_mercenary_model";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /*
     * 佣兵角色成长曲线-模板比较值
     */
    public function getMercenaryGrowClass()
    {
        $sql = "SELECT * FROM alchemy_mercenary_grow_class";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAll($sql);
    }
    
    /*
     * 佣兵角色成长曲线
     */
    public function getMercenaryGrow()
    {
        $sql = "SELECT * FROM alchemy_mercenary_grow";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /*
     * 佣兵角色成长曲线
     */
    public function getMercenaryLevel()
    {
        $sql = "SELECT * FROM alchemy_mercenary_level";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /*
     * 佣兵星级随机表
    */
    public function getMercenaryRpRand()
    {
    	$sql = "SELECT * FROM alchemy_mercenary_rp_rand";
    	$db = $this->getDB();
    	$rdb = $db['r'];
    	return $rdb->fetchAll($sql);
    }
    
    /*
     * 佣兵角色名字
     */
    public function getMercenaryName()
    {
        $sql = "SELECT * FROM alchemy_mercenary_name";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /*
     * 佣兵强化列表
     */
    public function getMercenaryStrengthenList()
    {
        $sql = "SELECT * FROM alchemy_mercenary_strengthen";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }
    
    /*
     * 佣兵位置扩展信息
     */
    public function getMercenaryPositionList()
    {
        $sql = "SELECT * FROM alchemy_mercenary_position";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /*
     * 佣兵派驻打工
    */
    public function getMercenaryWorkList()
    {
    	$sql = "SELECT * FROM alchemy_mercenary_work";
    	$db = $this->getDB();
    	$rdb = $db['r'];
    	return $rdb->fetchAssoc($sql);
    }
    
    /*
     * 佣兵各属性品质随机表
     */
    public function getMercenaryQualityList()
    {
        $sql = "SELECT * FROM alchemy_mercenary_quality";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAll($sql);
    }

    /*
     * 佣兵属性对照表
    */
    public function getMercenaryProContrastList()
    {
    	$sql = "SELECT * FROM alchemy_mercenary_pro_contrast";
    	$db = $this->getDB();
    	$rdb = $db['r'];
    	return $rdb->fetchAll($sql);
    }
    
	/*
     * 怪物
     */
    public function getMonster()
    {
        $sql = "SELECT * FROM alchemy_monster";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

	/*
     * 矿
     */
    public function getMine()
    {
        $sql = "SELECT * FROM alchemy_mine";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

	/*
     * 战斗技能
     */
    public function getEffect()
    {
        $sql = "SELECT * FROM alchemy_effect";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

	/*
     * 战斗职业属性相克
     */
    public function getFightRestriction()
    {
        $sql = "SELECT * FROM alchemy_fight_restriction";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

	/*
     * 战斗开战宣言
     */
    public function getFightDeclaration()
    {
        $sql = "SELECT * FROM alchemy_fight_declaration";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

	/*
     * 战斗 怪物站位规则描述
     */
    public function getFightMonsterMatrix()
    {
        $sql = "SELECT * FROM alchemy_fight_monster_matrix";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

	/*
     * 战斗 援助攻击技能条件
     */
    public function getFightAssistance()
    {
        $sql = "SELECT * FROM alchemy_fight_assistance";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

	/*
     * 世界地图
     */
    public function getWorldMap()
    {
        $sql = "SELECT * FROM alchemy_world_map";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /* 地图副本 map copy related */
    public function getMapCopyVer()
    {
        $sql = "SELECT map_id,fname,ver FROM alchemy_map_copy_version";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    public function getMapCopy($mapId)
    {
    	$sql = "SELECT * FROM alchemy_map_copy WHERE map_id=:map_id ";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchRow($sql, array('map_id' => $mapId));
    }
	/* -map copy related  */

	/*
     * 任务类型
     */
    public function getTaskTypeList()
    {
    	$sql = "SELECT id,desp,is_client_action FROM alchemy_task_type";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

	/*
     * 任务条件
     */
    public function getTaskConditionList()
    {
    	$sql = "SELECT id,desp,condition_type,cid,icon_cid,num,classname_type,kind,scene_id,target_id,node FROM alchemy_task_condition";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

	/*
     * 任务
     */
    public function getTaskList()
    {
    	$sql = "SELECT id,label,priority,condition_ids,complete_cost,need_user_level,need_fight_level,front_task_id,next_task_id,from_type,is_auto_complete,title,npc_id,npc_name,npc_classname,worldmap_id,foreword,help_desp,done_desp,guide,accept_story,story,awards FROM alchemy_task";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }
/**********************************************************************************************/

    /*
     * 合成术列表
     */
    public function getMix()
    {
        $sql = "SELECT * FROM alchemy_mix";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /*
     * 物品列表
     */
    public function getGoods()
    {
        $sql = "SELECT * FROM alchemy_goods";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /*
     * 物品列表-佣兵卡列表-17
     */
    public function getMercenaryCard()
    {
        $sql = "SELECT * FROM alchemy_mercenary_card";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }
    
    /*
     * 卷轴列表
     */
    public function getScroll()
    {
        $sql = "SELECT * FROM alchemy_scroll";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /*
     * 材料列表
     */
    public function getStuff()
    {
        $sql = "SELECT * FROM alchemy_stuff";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /*
     * 工作台列表
     */
    public function getFurnace()
    {
        $sql = "SELECT * FROM alchemy_furnace";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /*
     * 装修物列表
     */
    public function getDecor()
    {
        $sql = "SELECT * FROM alchemy_decor";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /*
     * 装备列表
     */
    public function getWeapon()
    {
        $sql = "SELECT * FROM alchemy_equipment";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /*
     * 图鉴
     */
    public function getIllustrations()
    {
        $sql = "SELECT * FROM alchemy_illustrations ORDER BY order_id";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /**
     * 用户等级
     */
    public function getUserLevelList()
    {
    	$sql = "SELECT * FROM alchemy_level";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /**
     * 房间等级
     */
    public function getRoomLevelList()
    {
    	$sql = "SELECT * FROM alchemy_level_room";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /**
     *
     */
    public function getAvatar()
    {
    	$sql = "SELECT id,name,face,type,class_name FROM alchemy_avatar";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /**
     * 场景
     */
    public function getScene()
    {
    	$sql = "SELECT * FROM alchemy_scene";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /**
     * 订单
     */
    public function getOrder()
    {
    	$sql = "SELECT * FROM alchemy_order";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /**
     * 订单概率表
     */
    public function getOrderPro()
    {
    	$sql = "SELECT user_level,level_1,level_2,level_3,level_4,level_5,level_6,level_7,
    			level_8,level_9,level_10 FROM alchemy_order_pro";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /**
     * NPC名字
     */
    public function getAvatarNameList()
    {
    	$sql = "SELECT id,name FROM alchemy_avatar_name";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /**
     * 剧情列表
     */
    public function getStoryList()
    {
    	$sql = "SELECT * FROM alchemy_story";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /**
     * 剧情-动作列表
     */
    public function getStoryActionList()
    {
    	$sql = "SELECT * FROM alchemy_story_action";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /**
     * 剧情-演员列表
     */
    public function getStoryNpcList()
    {
    	$sql = "SELECT * FROM alchemy_story_npc";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /**
     * 剧情-对白表
     */
    public function getStoryDialogList()
    {
    	$sql = "SELECT * FROM alchemy_story_dialog";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /**
     * 等级-酒馆
     */
    public function getTavernLevelList()
    {
    	$sql = "SELECT * FROM alchemy_level_tavern";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAll($sql);
    }

    /**
     * 等级-铁匠铺
     */
    public function getSmithyLevelList()
    {
    	$sql = "SELECT * FROM alchemy_level_smithy";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /**
     * 等级-竞技场
     */
    public function getArenaLevelList()
    {
    	$sql = "SELECT * FROM alchemy_level_arena";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /**
     * 等级-训练营
     */
    public function getTrainingLevelList()
    {
    	$sql = "SELECT * FROM alchemy_level_training";
    	$db = $this->getDB();
    	$rdb = $db['r'];
    	return $rdb->fetchAssoc($sql);
    }
    
    /**
     * 等级-自宅
     */
    public function getHomeLevelList()
    {
    	$sql = "SELECT * FROM alchemy_level_home";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /**
     * 等级-主角
     */
    public function getRoleLevelList()
    {
    	$sql = "SELECT * FROM alchemy_level_role";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /**
     * 日志消息
     */
    public function getFeedTemplate()
    {
    	$sql = "SELECT id,title FROM alchemy_feed_template";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchPairs($sql);
    }

    /**
     * 新手引导
     */
    public function getHelpList()
    {
    	$sql = "SELECT id,idx,awards FROM alchemy_help";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }
    
    /**
     * 地图上NPC列表
     */
    public function getPersonList()
    {
    	$sql = "SELECT * FROM alchemy_map_copy_person";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }
    
    /**
     * 副本传送门
     */
    public function getTransportList()
    {
    	$sql = "SELECT * FROM alchemy_map_copy_transport";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }
    
    /**
     * 佣兵、怪物 等级相差时，获得经验计算公式
     */
    public function getFightExpLevDiff()
    {
    	$sql = "SELECT * FROM alchemy_fight_exp_leveldiff";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchRow($sql);
    }
    
    /**
     * 初始化用户信息
     */
    public function getInitUserInfo()
    {
    	$sql = "SELECT * FROM alchemy_init_user LIMIT 1";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchRow($sql);
    }

    public function getInitRoleList()
    {
    	$sql = "SELECT * FROM alchemy_init_role";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }
    
    /*
     * 平台FEED模板
     */
    public function getPlatformFeedTemplate()
    {
    	$sql = "SELECT id,title,comment,description,picname,link,linktext,award FROM platform_feed_template";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }
    
    //充值设定信息
    public function getPaySettingList()
    {
    	$sql = "SELECT `id`,`section`,`end_time`,`note`,`next_id`,`active`,`update_time` FROM alchemy_pay_setting";

        $db = $this->getDB();
        $rdb = $db['r'];

        return $rdb->fetchAssoc($sql);
    }
    
    public function updatePaySetting($id, $info)
    {
        $tbname = 'alchemy_pay_setting';
        
        $db = $this->getDB();
        $wdb = $db['w'];
    	$where = $wdb->quoteinto('id = ?', $id);
    	
        $wdb->update($tbname, $info, $where);
    }
    
    /*
     * 竞技场战斗奖励表
     */
    public function getArenaAward()
    {
        $sql = "SELECT * FROM alchemy_arena_award";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }

    /*
     * 训练营升级列表
    */
    public function getTrainingList()
    {
    	$sql = "SELECT * FROM alchemy_training";
    	$db = $this->getDB();
    	$rdb = $db['r'];
    	return $rdb->fetchAssoc($sql);
    }
    
    /*
     * 统计-用户操作记录
     */
    public function getStatUserAction()
    {
        $sql = "SELECT * FROM alchemy_stat_user_action";
        $db = $this->getDB();
        $rdb = $db['r'];
        return $rdb->fetchAssoc($sql);
    }
    
}