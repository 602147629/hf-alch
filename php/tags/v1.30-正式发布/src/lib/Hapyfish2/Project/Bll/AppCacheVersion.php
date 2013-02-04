<?php

class Hapyfish2_Project_Bll_AppCacheVersion
{	
	public static function get($name)
	{
		$data = Hapyfish2_Project_Cache_AppCacheVersion::get();
		if ($data == null || !isset($data[$name])) {
			return self::update($name);
		}
		
		return $data[$name];
	}
	
	public static function update($name, $version = null)
	{
		if ($version == null) {
			$version = date('YmdHi');
		}
		
		$data = Hapyfish2_Project_Cache_AppCacheVersion::get();
		if ($data == null) {
			$data = array();
		}
		$data[$name] = $version;
		$value = json_encode($data);
		Hapyfish2_Project_Cache_AppCacheVersion::update($value);
		
		return $version;
	}
	
	public static function refresh()
	{
		return Hapyfish2_Project_Cache_AppCacheVersion::reset();
	}
}