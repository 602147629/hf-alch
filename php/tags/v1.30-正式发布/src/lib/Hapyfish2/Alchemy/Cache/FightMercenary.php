<?php

class Hapyfish2_Alchemy_Cache_FightMercenary
{
	public static function getMercenaryIds($uid)
	{
		$key = 'a:u:fightmercenaryids:';
    	$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
        $key = $key . $uid;
        $cache = Hapyfish2_Cache_Factory::getMC($uid);

		$ids = $cache->get($key);
		if ($ids === false) {
			try {
				$dal = Hapyfish2_Alchemy_Dal_FightMercenary::getDefaultInstance();
				$ids = $dal->getOwnMercenaryIds($uid);
				if ($ids) {
					$cache->add($key, $ids);
				} else {
					return null;
				}
			} catch (Exception $e) {
				return null;
			}
		}

		return explode(',', $ids);
	}

	public static function reloadMercenaryIds($uid)
	{
		try {
			$dal = Hapyfish2_Alchemy_Dal_FightMercenary::getDefaultInstance();
			$ids = $dal->getOwnMercenaryIds($uid);
			if ($ids) {
				$key = 'a:u:fightmercenaryids:';
            	$key = Hapyfish2_Alchemy_Cache_MemkeyList::mapUserMcKey($key);
                $key = $key . $uid;
				$cache = Hapyfish2_Cache_Factory::getMC($uid);
				$cache->set($key, $ids);
			} else {
				return null;
			}
		} catch (Exception $e) {
			return null;
		}

		return explode(',', $ids);
	}

}