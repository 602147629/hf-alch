<?php
class Hapyfish2_Alchemy_Cache_EventGift
{

 	protected static $_cacheKeyPrex = 'alchemy:bas:';

	public static function getKey($word)
	{
		return self::$_cacheKeyPrex.$word;
	}

	public static function getBasicMC()
	{
		$key = 'mc_0';
		return Hapyfish2_Cache_Factory::getBasicMC($key);
	}
	
	public static function getTimeGift($type)
	{
		$list = array();
		$key = 'timeGift';
		$key = self::getKey($key);
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		if($data == false){
			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
			$data = $dal->getTimeGift();
			if($data){
				foreach($data as $t=>&$info){
					$info['list'] = json_decode($info['list']);
				}
				$cache->set($key, $data);
			}else{
				return null;
			}
		}
		if($data){
			foreach($data as $v){
				if($v['type'] == $type){
					$list[] = $v;
				}
			}
		}
		return $list;
	}
	
	public static function getTGDetail($type, $id)
	{
		$list = self::getTimeGift($type);
		foreach($list as $v){
			if($v['id'] == $id){
				return $v;
			}
		}
		return null;
	}
	
	public static function getUserTGift($uid)
	{
		$key = 'a:u:timeGift:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
			$info = $dal->getUserEvent($uid, 1);
			if($info){
				$data['id'] = $info;
				$data['end'] = 0;
			}else{
				$data['id'] = 0;
				$data['end'] = 0;
			}
			$cache->set($key, $data);
		}
		return $data;
	}
	
	public static function updateUserTGift($uid, $userGift)
	{
		$key = 'a:u:timeGift:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $userGift);
		self::updateEventGift($uid,$userGift['id'],1);
	}
	
	public static function getSevenGift($type)
	{
		$key = 'sevenGift';
		$key = self::getKey($key);
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		$list = array();
		if($data == false){
			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
			$data = $dal->getSevenGift();
			if($data){
				foreach($data as $id=>&$info){
					$info['awards'] = json_decode($info['awards']);
				}
				$cache->set($key, $data);
			}else{
				return null;
			}
		}
		if($data){
			foreach($data as $v){
				if($type == $v['type']){
					$list[] = $v;
				}
			}
		}
		return $list;
	}
	
	public static function getLevelGift($type)
	{
		$list = array();
		$key = 'levelGift';
		$key = self::getKey($key);
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		if($data == false){
			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
			$data = $dal->getLevelGift();
			if($data){
				foreach($data as $t=>&$info){
					$info['awards'] = json_decode($info['awards']);
				}
				$cache->set($key, $data);
			}else{
				return null;
			}
		}
		if($data){
			foreach($data as $v){
				if($v['type'] == $type){
					$list[] = $v;
				}
			}
		}
		return $list;
	}
	
	public static function getUserSGift($uid)
	{
		$key = 'a:u:sevenGift:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
			$info = $dal->getUserEvent($uid, 2);
			$date = date('Ymd');
			if($info){
				$data['id'] = $info;
				$data['date'] = $date;
			}else{
				$data['id'] = 0;
				$data['date'] = 0;
			}
			$cache->set($key, $data);
		}
		return $data;
	}
	
	public static function getSGDetail($type, $id)
	{
		$list = self::getSevenGift($type);
		if($list){
			foreach($list as $v){
				if($v['day'] == $id){
					return $v;
				}
			}
		}
		return null;
	}
	
	public static function updateUserSGift($uid, $userGift)
	{
		$key = 'a:u:sevenGift:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $userGift);
		self::updateEventGift($uid,$userGift['id'],2);
	}
	
	public static function updateEventGift($uid,$id,$type)
	{
		try{
			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
			$dal->updateEventGift($uid,$id,$type);
		} catch (Exception $e) {
            return "fatal error:".$e->getMessage();
	    }
	}
	
	public static function getUserLGift($uid)
	{
		$key = 'a:u:levelGift:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
			$info = $dal->getUserEvent($uid, 3);
			if($info){
				$data = $info;
			}else{
				$data = 0;
			}
			$cache->set($key, $data);
		}
		return $data;
	}
	
	
	public static function getLGDetail($type, $id)
	{
		$list = self::getLevelGift($type);
		if($list){
			foreach($list as $v){
				if($v['level'] == $id){
					return $v;
				}
			}
		}
		return null;
	}
	
	public static function updateUserLGift($uid, $data)
	{
		$key = 'a:u:levelGift:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $data);
		self::updateEventGift($uid,$data,3);
	}
	
	public static function getPackage($type)
	{
		$list = array();
		$key = 'package:';
		$key = self::getKey($key);
		$cache = self::getBasicMC();
		$data = $cache->get($key);
		if($data == false){
			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
			$data = $dal->getPackage();
			if($data){
				foreach($data as $t=>&$info){
					$info['awards'] = json_decode($info['awards']);
				}
				$cache->set($key, $data);
			}else{
				return null;
			}
		}
		if($data){
			foreach($data as $v){
				if($v['type'] == $type){
					$list[] = $v;
				}
			}
		}
		return $list;
	}
	
	public static function getPackageDetail($cid, $type)
	{
		$list = self::getPackage($type);
		if($list){
			foreach($list as $v){
				if($v['cid'] == $cid){
					return $v;
				}
			}
		}
		return null;
	}
	
	public static function getUTGift($uid)
	{
		$key = 'a:u:testGift:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
			$data = $dal->getTestGift($uid);
			if($data){
				$cache->set($key, $data);
			}else{
				return null;
			}
		}
		return $data;
	}
	
	public static function updateTestGift($uid, $data)
	{
		$key = 'a:u:testGift:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$cache->set($key, $data);
		try{
			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
			$dal->insertTestGift($data);
		} catch (Exception $e) {
            return "fatal error:".$e->getMessage();
	    }
	}
	
	public static function getUserIgift($uid)
	{
		$key = 'a:u:InviteGift:'.$uid;
		$cache = Hapyfish2_Cache_Factory::getMC($uid);
		$data = $cache->get($key);
		if($data === false){
			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
			$data = $dal->getInviteGift($uid);
			if($data){
				$cache->set($key, $data);
			}else{
				return null;
			}
		}
		return $data;
	}
	
	public static function updateInviteGift($data)
	{
		$key = 'a:u:InviteGift:'.$data['uid'];
		$cache = Hapyfish2_Cache_Factory::getMC($data['uid']);
		$cache->set($key, $data);
		try{
			$dal = Hapyfish2_Alchemy_Dal_EventGift::getDefaultInstance();
			$dal->insertInviteGift($data);
		} catch (Exception $e) {
            return "fatal error:".$e->getMessage();
	    }
	}
}