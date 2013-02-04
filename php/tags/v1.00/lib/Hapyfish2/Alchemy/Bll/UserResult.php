<?php

class Hapyfish2_Alchemy_Bll_UserResult
{
	private static $result = array(
		'status' => 1,
		'content' => '',
		'levelUP' => false,
		//'feats' => 0,
		'coin' => 0,
		'gem' => 0,
		'exp' => 0,
		'sp' => 0
	);

	private static $uid = 0;

	//任务相关
	private static $taskCompletedId = array();
	private static $taskDeletedId = array();
	private static $taskNewVo = array();//<TaskVo>
	private static $taskChangesVo = array();//<TaskVo>


	private static $field = array();

	public static function addField($uid, $name, $value)
	{
		if (self::$uid == $uid) {
			if ( $name == 'rolesChange' ) {
				self::removeField($uid, $name);
			}
			if (!isset(self::$field[$name])) {
				self::$field[$name] = $value;
			} else {
				self::$field[$name] = array_merge(self::$field[$name], $value);
			}
		}
	}
	
	//删除已有返回信息
	public static function removeField($uid, $name)
	{
		if (self::$uid == $uid) {
			if (isset(self::$field[$name])) {
				unset(self::$field[$name]);
			}
		}
	}

	//任务相关
    public static function addTaskCompletedId($uid, $id)
	{
		if (self::$uid == $uid) {
			self::$taskCompletedId[] = $id;
		}
	}
	public static function getTaskCompletedId()
	{
		return self::$taskCompletedId;
	}
    public static function addTaskDeletedId($uid, $id)
	{
		if (self::$uid == $uid) {
			self::$taskDeletedId[] = $id;
		}
	}
	public static function getTaskDeletedId()
	{
		return self::$taskDeletedId;
	}
    public static function addTaskNew($uid, $info)
	{
		if (self::$uid == $uid) {
			self::$taskNewVo[] = $info;
		}
	}
	public static function getTaskNew()
	{
		return self::$taskNewVo;
	}
    public static function addTaskChanges($uid, $info)
	{
		if (self::$uid == $uid) {
			self::$taskChangesVo[] = $info;
		}
	}
	public static function getTaskChanges()
	{
		return self::$taskChangesVo;
	}

	public static function setUser($uid)
	{
		self::$uid = $uid;
	}

	public static function mergeFeats($uid, $num)
	{
		if (self::$uid == $uid) {
			self::$result['feats'] += $num;
		}
	}

	public static function mergeCoin($uid, $coin)
	{
		if (self::$uid == $uid) {
			self::$result['coin'] += $coin;
		}
	}

	public static function mergeGem($uid, $gem)
	{
		if (self::$uid == $uid) {
			self::$result['gem'] += $gem;
		}
	}

	public static function mergeExp($uid, $exp)
	{
		if (self::$uid == $uid) {
			self::$result['exp'] += $exp;
		}
	}

	public static function mergeSp($uid, $Sp)
	{
		if (self::$uid == $uid) {
			self::$result['sp'] += $Sp;
		}
	}

	public static function setLevelUp($uid, $levelUp)
	{
		if (self::$uid == $uid) {
			self::$result['levelUP'] = $levelUp;
		}
	}

	public static function setStatus($uid, $status, $content = null)
	{
		if (self::$uid == $uid) {
			self::$result['status'] = $status;
			if ($status < 0) {
				if ($content === null) {
					$content = 'serverWord_' . abs($status);
				}
				self::$result['content'] = $content;
			} else {
				self::$result['content'] = '';
			}
		}
	}

	public static function result()
	{
		$res = array();
		$res['status'] = self::$result['status'];
		if (self::$result['content'] != '') {
			$res['content'] = self::$result['content'];
		}
		if (self::$result['levelUP'] != false) {
			$res['levelUP'] = true;
		}
		if (self::$result['feats'] !=0) {
		    $res['feats'] = self::$result['feats'];
		}
		if (self::$result['coin'] != 0) {
			$res['coin'] = self::$result['coin'];
		}
		if (self::$result['gem'] != 0) {
			$res['gem'] = self::$result['gem'];
		}
		if (self::$result['exp'] != 0) {
			$res['exp'] = self::$result['exp'];
		}
		if (self::$result['sp'] != 0) {
			$res['sp'] = self::$result['sp'];
		}
		return $res;
	}

	public static function all()
	{
		$ret = array(
			'result' => self::result()
		);

		//任务相关
		if (self::$taskCompletedId || self::$taskDeletedId || self::$taskNewVo || self::$taskChangesVo) {
		    $changeTasks = array('adds'=>self::$taskNewVo, 'dels'=>self::$taskDeletedId,
		    					 'completes'=>self::$taskCompletedId, 'changes'=>self::$taskChangesVo);
		    self::addField(self::$uid, 'changeTasks', $changeTasks);
		}

		if (count(self::$field) > 0) {
			foreach (self::$field as $k => $v) {
				$ret[$k] = $v;
			}
		}

		return $ret;
	}

	public static function flush()
	{
		$ret = self::all();

		self::$result = array(
			'status' => 1,
			'content' => '',
			'levelUP' => false,
			//'feats' => 0,
			'coin' => 0,
			'gem' => 0,
			'exp' => 0,
			'sp' => 0
		);
		self::$field = array();

		return $ret;
	}
}