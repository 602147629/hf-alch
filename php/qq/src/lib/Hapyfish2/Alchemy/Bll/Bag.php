<?php

class Hapyfish2_Alchemy_Bll_Bag
{
	public static function getAll($uid)
	{
		$list = array();
		
		$furnace = Hapyfish2_Alchemy_HFC_Furnace::getInBag($uid);
		if (!empty($furnace)) {
			foreach ($furnace as $d) {
				$list[] = array(
					$d['furnace_id'],
					1,
					$d['id']
				);
			}
		}
		
		$decor = Hapyfish2_Alchemy_HFC_Decor::getBag($uid);
		if (!empty($decor)) {
			foreach ($decor as $cid => $d) {
				if ($d['count'] > 0) {
					$list[] = array(
						$cid,
						$d['count']
					);
				}
			}
		}
		
		$goods = Hapyfish2_Alchemy_HFC_Goods::getUserGoods($uid);
		if (!empty($goods)) {
			foreach ($goods as $cid => $d) {
				if ($d['count'] > 0) {
					$list[] = array(
						$cid,
						$d['count']
					);
				}
			}
		}
		
		$stuff = Hapyfish2_Alchemy_HFC_Stuff::getUserStuff($uid);
		if (!empty($stuff)) {
			foreach ($stuff as $cid => $d) {
				if ($d['count'] > 0) {
					$list[] = array(
						$cid,
						$d['count']
					);
				}
			}
		}
		
		$weapon = Hapyfish2_Alchemy_HFC_Weapon::getAll($uid);
		if (!empty($weapon)) {
			foreach ($weapon as $d) {
				if ( $d['status'] == 0 ) {
					$list[] = array(
						$d['cid'],
						1,
						$d['wid'],
						$d['durability'],
						$d['pa'],
						$d['pd'],
						$d['ma'],
						$d['md'],
						$d['speed'],
						$d['hp'],
						$d['mp'],
						$d['cri'],
						$d['dod'],
						$d['hit'],
						$d['tou'],
						$d['type'],
						$d['strLevel']
						
					);
				}
			}
		}
		
		$scroll = Hapyfish2_Alchemy_HFC_Scroll::getUserScroll($uid);
		if (!empty($scroll)) {
			foreach ($scroll as $cid => $d) {
				if ($d['count'] > 0) {
					$list[] = array(
						$cid,
						$d['count']
					);
				}
			}
		}

		return $list;
	}
}