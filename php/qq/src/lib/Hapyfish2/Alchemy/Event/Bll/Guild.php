<?php
class Hapyfish2_Alchemy_Event_Bll_Guild
{
	public static function getMember()
	{
		$dal = Hapyfish2_Alchemy_Event_Dal_Guild::getDefaultInstance();
		$all = $dal->getAll();
		if($all){
			foreach($all as $k=>&$v){
				$detail = json_decode($v['detail'], true);
				$v['login'] = $detail[0];
				$v['pay'] = $detail[1];
				$v['invite'] = $detail[2];
				$v['introduce'] = $detail[3];
			}
		}
		return $all;
	}
	
	public static function getOne($uid)
	{
		$dal = Hapyfish2_Alchemy_Event_Dal_Guild::getDefaultInstance();
		$info = $dal->getOne($uid);
		if(!$info){
			return null;
		}
		$detail = json_decode($info['detail'], true);
		$info['login'] = $detail[0];
		$info['pay'] = $detail[1];
		$info['invite'] = $detail[2];
		$info['introduce'] = $detail[3];
		return $info;
	}
	
	public static function updateOne($uid,$info)
	{
		$data = array();
		$data['uid'] = $info['uid'];
		$data['detail'] = json_encode(array($info['login'],$info['pay'],$info['invite'],$info['introduce']));
		$data['total'] = $info['total'];
		$dal = Hapyfish2_Alchemy_Event_Dal_Guild::getDefaultInstance();
		$dal->insertGuild($uid,$data);
	}
	
	public static function addPayPoint($uid,$amount)
	{
		$detail = self::getOne($uid);
		if($detail){
			$detail['pay'] += $amount;
			$detail['total'] += $amount;
			self::updateOne($uid, $detail);
		}
		return;
	}
	
	public static function addInvitePiont($uid,$amount)
	{
		$detail = self::getOne($uid);
		if($detail){
			$detail['invite'] += $amount;
			$detail['total'] += $amount;
			self::updateOne($uid, $detail);
		}
		return;
	}
	
	public static function addLoginPoint($uid,$amount)
	{
		$detail = self::getOne($uid);
		if($detail){
			$detail['login'] += $amount;
			$detail['total'] += $amount;
			self::updateOne($uid, $detail);
		}
		return;
	}
	
	public static function addIntroducePoint($uid,$num)
	{
		$detail = self::getOne($uid);
		if($detail){
			$detail['introduce'] += $num;
			$detail['total'] += $num;
			self::updateOne($uid, $detail);
		}
		return;
	}
}