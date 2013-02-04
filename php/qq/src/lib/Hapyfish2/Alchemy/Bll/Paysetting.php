<?php

class Hapyfish2_Alchemy_Bll_Paysetting
{
	public static function getInfo()
	{
		$info = null;
		$list = Hapyfish2_Alchemy_Cache_Basic::getPaySettingList();
		if ($list) {
			$info = self::getActive($list);
			if (!$info) {
				$info = self::getDefault($list);
			}
		}
		
		return $info;
	}
	
	public static function getActive($list)
	{
		foreach ($list as $v) {
			if ($v['active'] == 1) {
				if ($v['end_time'] > 0) {
					$t = time();
					if ($v['end_time'] < $t) {
						if ($v['next_id'] > 0) {
							return self::getNext($list, $v['next_id']);
						} else {
							return null;
						}
					}
				}
				return $v;
			}
		}
		
		return null;
	}
	
	public static function getNext($list, $id)
	{
		if (isset($list[$id])) {
			$item = $list[$id];
			if ($item['end_time'] > 0) {
				$t = time();
				if ($item['end_time'] < $t) {
					if ($item['next_id'] > 0) {
						return self::getNext($list, $item['next_id']);
					} else {
						return null;
					}
				}
			}
			
			return $item;
		}
		
		return null;
	}
	
	public static function getDefault($list)
	{
		if (isset($list[0])) {
			return $list[0];
		}
		
		return null;
	}
}