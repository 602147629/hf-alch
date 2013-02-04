<?php

/**
 * User DAL Class
 *
 */
class Hapyfish2_Alchemy_Dal_User
{
    protected static $_instance;

    /**
     * Single Instance
     *
     * @return Hapyfish2_Alchemy_Dal_User
     */
    public static function getDefaultInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * get the table name by user's id
     *
     * @param int $uid
     * @return string
     */
    public function getTableName($uid)
    {
		if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE == 3) {
            $id = 0;
        } else {
        	$id = floor($uid/DATABASE_NODE_NUM) % 10;
        }

    	return 'alchemy_user_info_' . $id;
    }

    /**
     * get user's exp data
     *
     * @param int $uid
     * @return int|false
     */
    public function getExp($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT exp FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchOne($sql, array('uid' => $uid));
        if ($data !== false) {
        	$data = (int)$data;
        }

        return $data;
    }

    /**
     * get user's coin data
     *
     * @param int $uid
     * @return int|false
     */
    public function getCoin($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT coin FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchOne($sql, array('uid' => $uid));
        if ($data !== false) {
        	$data = (int)$data;
        }

        return $data;
    }

    /**
     * get user's gem data
     *
     * @param int $uid
     * @return int|false
     */
    public function getGem($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT gem FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchOne($sql, array('uid' => $uid));
        if ($data !== false) {
        	$data = (int)$data;
        }

        return $data;
    }

    /**
     * increase user's gem number
     *
     * @param int $uid
     * @param int $gem
     * @return bool
     */
    public function incGem($uid, $gem)
    {
        $tbname = $this->getTableName($uid);
        $sql = "UPDATE $tbname SET gem=gem+:gem WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

		$stmt = $wdb->query($sql, array('uid' => $uid, 'gem' => $gem));
        $rowCount = $stmt->rowCount();
        if ($rowCount == 0) {
        	return false;
        } else {
        	return true;
        }
    }

    /**
     * decrease user's gem number
     *
     * @param int $uid
     * @param int $gem
     * @return bool
     */
    public function decGem($uid, $gem)
    {
        $tbname = $this->getTableName($uid);
        $sql = "UPDATE $tbname SET gem=gem-:gem WHERE uid=:uid AND gem>=:gem";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

        $stmt = $wdb->query($sql, array('uid' => $uid, 'gem' => $gem));
		$rowCount = $stmt->rowCount();
        if ($rowCount == 0) {
        	return false;
        } else {
        	return true;
        }
    }

    /**
     * get user's level data
     *
     * @param int $uid
     * @return int|false
     */
    public function getLevel($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT level FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchOne($sql, array('uid' => $uid));
        if ($data !== false) {
        	$data = (int)$data;
        }

        return $data;
    }

    /**
     * get scene data
     *
     * @param int $uid
     * @return array|null
     */
    public function getScene($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT tile_x_length,tile_z_length,cur_scene_id,open_scene_list FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $row = $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
        if ($row != null) {
       		$row[0] = (int)$row[0];
       		$row[1] = (int)$row[1];
       		$row[2] = (int)$row[2];
        }

        return $row;
    }

    /**
     * get user's avatar id
     *
     * @param int $uid
     * @return int|false
     */
    public function getAvatar($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT avatar FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchOne($sql, array('uid' => $uid));
        if ($data !== false) {
        	$data = (int)$data;
        }

        return $data;
    }

	/**
	 * get user's SP data
	 *
	 * @param int $uid
	 * @return array|null
	 */
    public function getSp($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT sp,max_sp,sp_set_time FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $row = $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
        if ($row != null) {
            for($i = 0, $len = count($row); $i < $len; $i++) {
       			$row[$i] = (int)$row[$i];
       		}
        }

        return $row;
    }

    /**
     * get order count
     *
     * @param int $uid
     * @return int|false
     */
    public function getOrderCount($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT order_count FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchOne($sql, array('uid' => $uid));
        if ($data !== false) {
        	$data = (int)$data;
        }

        return $data;
    }

    /**
     * get max mercenary count
     *
     * @param int $uid
     * @return int|false
     */
    public function getMaxMercenaryCount($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT mercenary_count FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchOne($sql, array('uid' => $uid));
        if ($data !== false) {
        	$data = (int)$data;
        }

        return $data;
    }

    /**
     * get max order count
     *
     * @param int $uid
     * @return int|false
     */
    public function getMaxOrderCount($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT order_count FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchOne($sql, array('uid' => $uid));
        if ($data !== false) {
        	$data = (int)$data;
        }

        return $data;
    }

    /**
     * get tavern level
     *
     * @param int $uid
     * @return int|false
     */
    public function getTavernLevel($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT tavern_level,tavern_city_level FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchRow($sql, array('uid' => $uid));

        return $data;
    }

    /**
     * get smithy level
     *
     * @param int $uid
     * @return int|false
     */
    public function getSmithyLevel($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT smithy_level FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchOne($sql, array('uid' => $uid));
        if ($data !== false) {
        	$data = (int)$data;
        }

        return $data;
    }
    
    /**
     * get arena level
     *
     * @param int $uid
     * @return int|false
     */
    public function getArenaLevel($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT arena_level FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchOne($sql, array('uid' => $uid));
        if ($data !== false) {
        	$data = (int)$data;
        }

        return $data;
    }
    
    /**
     * get home level
     *
     * @param int $uid
     * @return int|false
     */
    public function getHomeLevel($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT home_level FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchOne($sql, array('uid' => $uid));
        if ($data !== false) {
        	$data = (int)$data;
        }

        return $data;
    }

    /**
     * get training level
     *
     * @param int $uid
     * @return int|false
     */
    public function getTrainingLevel($uid)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT training_level FROM $tbname WHERE uid=:uid";
    
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$rdb = $db['r'];
    
    	$data = $rdb->fetchOne($sql, array('uid' => $uid));
    	if ($data !== false) {
    		$data = (int)$data;
    	}
    
    	return $data;
    }
    
    /**
     * 训练营-当前训练位置数
     *
     * @param int $uid
     * @return int|false
     */
    public function getTrainingPosNum($uid)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT training_pos_num FROM $tbname WHERE uid=:uid";
    
    	$db = Hapyfish2_Db_Factory::getDB($uid);
    	$rdb = $db['r'];
    
    	$data = $rdb->fetchOne($sql, array('uid' => $uid));
    	if ($data !== false) {
    		$data = (int)$data;
    	}
    
    	return $data;
    }
    
    public function getLoginInfo($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT last_login_time,today_login_count,active_login_count,max_active_login_count,all_login_count,login_day_count FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }

    public function getFightAssistInfo($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT assist_bas_count,assist_ext_count FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('uid' => $uid), Zend_Db::FETCH_NUM);
    }

    public function getFeats($uid)
    {
        $tbname = $this->getTableName($uid);
    	$sql = "SELECT feats FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchOne($sql, array('uid' => $uid));
        if ($data !== false) {
        	$data = (int)$data;
        }
        return $data;
    }

    /**
     * update some user's field info
     *
     * @param int $uid
     * @param array $info
     * @return bool
     */
    public function update($uid, $info)
    {
        $tbname = $this->getTableName($uid);

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];

    	$where = $wdb->quoteinto('uid = ?', $uid);

        $rowCount = $wdb->update($tbname, $info, $where);
        if ($rowCount == 0) {
        	return false;
        } else {
        	return true;
        }
    }

    public function insert($uid, $info)
    {
        $tbname = $this->getTableName($uid);
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        $ret = $wdb->insert($tbname, $info);
        return $ret;
    }
    
    public function getAll($i)
    {
        //$tbname = $this->getTableName($uid);
        $tbname = 'alchemy_user_info_'.$i;
    	$sql = "SELECT uid FROM $tbname ";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchAll($sql);

        return $data;
    }
    
    public function getCreateTime($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "SELECT create_time FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchOne($sql, array('uid' => $uid));
    }
    
    public function get($uid)
    {
        $tbname = $this->getTableName($uid);
        $sql = "SELECT * FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        return $rdb->fetchRow($sql, array('uid' => $uid));
    }
    
    public function clearUser($uid)
    {
    	
		if (defined('APP_SERVER_TYPE') && APP_SERVER_TYPE == 3) {
            $id = 0;
        } else {
        	$id = floor($uid/DATABASE_NODE_NUM) % 10;
        }

    	$sql = "DELETE FROM alchemy_user_decor_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_decor_inbag_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_fight_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_fight_attribute_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_fight_corps_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_fight_mercenary_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_floorwall_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_furnace_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_gift_bag_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_gift_wish_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_goods_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_help_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_hire_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_illustrations_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_info_".$id." WHERE uid=:uid;
				
				DELETE FROM alchemy_user_log_add_gem_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_log_consume_coin_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_log_consume_gem_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_map_copy_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_mercenary_work_".$id." WHERE uid=:uid;
				
				DELETE FROM alchemy_user_mix_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_occupy_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_order_".$id." WHERE uid=:uid;
				
				DELETE FROM alchemy_user_scroll_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_seq_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_story_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_story_dialog_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_stuff_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_task_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_task_daily_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_task_open_".$id." WHERE uid=:uid;
				
				DELETE FROM alchemy_user_unique_item_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_unlockfunc_".$id." WHERE uid=:uid;
				
				DELETE FROM alchemy_user_weapon_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_world_map_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_map_copy_person_".$id." WHERE uid=:uid;
				
				DELETE FROM alchemy_user_openportal_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_openportal_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_map_copy_person_".$id." WHERE uid=:uid;
				DELETE FROM alchemy_user_monster_".$id." WHERE uid=:uid;
                DELETE FROM alchemy_user_map_copy_transport_".$id." WHERE uid=:uid;
                DELETE FROM alchemy_user_openmine_".$id." WHERE uid=:uid;
                DELETE FROM alchemy_user_fight_mercenary_training_".$id." WHERE uid=:uid;
				
				DELETE FROM alchemy_user_event_gift WHERE uid=:uid;
				
				";
    	
        
        $db = Hapyfish2_Db_Factory::getDB($uid);
        $wdb = $db['w'];
        
        return $wdb->query($sql, array('uid' => $uid));
    }
    
    public function getUid($id)
    {
    	$tbname = $this->getTableName($id);
    	$sql = "select uid from $tbname";
    	$db = Hapyfish2_Db_Factory::getDB($id);
    	$rdb = $db['r'];
        return $rdb->fetchCol($sql);
    	
    }
    
    public function getTotalPay($uid)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT total_pay FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchOne($sql, array('uid' => $uid));
        if ($data !== false) {
        	$data = (int)$data;
        }

        return $data;
    }
    
    public function getTotalInvite($uid)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT total_invite FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchOne($sql, array('uid' => $uid));
        if ($data !== false) {
        	$data = (int)$data;
        }

        return $data;
    }
    
    public function getKeyNum($uid)
    {
    	$tbname = $this->getTableName($uid);
    	$sql = "SELECT box_key FROM $tbname WHERE uid=:uid";

        $db = Hapyfish2_Db_Factory::getDB($uid);
        $rdb = $db['r'];

        $data = $rdb->fetchOne($sql, array('uid' => $uid));
        if ($data !== false) {
        	$data = (int)$data;
        }

        return $data;
    }
}