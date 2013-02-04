<?php

class Hapyfish2_Alchemy_HFC_Scroll
{
	//卷轴,type:2
	public static function getUserScroll($uid)
    {
        $key = 'a:u:scroll:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);
		$data = $cache->get($key);
        if ($data === false) {
        	try {
	            $dalScroll = Hapyfish2_Alchemy_Dal_Scroll::getDefaultInstance();
	            $result = $dalScroll->get($uid);
	            if ($result) {
                    $data = array();
                    foreach ($result as $cid => $count) {
                        $data[$cid] = array($count, 0);
                    }
                    $cache->add($key, $data);
                } else {
                    return null;
                }
            } catch (Exception $e) {
                info_log('[Hapyfish2_Alchemy_HFC_Scroll::getUserScroll:'. $uid. ']' . $e->getMessage(), 'db.err');
            }
        }

        $scroll = array();
        if ( is_array($data) ) {
            foreach ($data as $cid => $item) {
                $scroll[$cid] = array('count' => $item[0], 'update' => $item[1]);
            }
        }

        return $scroll;
    }

    public static function updateUserScroll($uid, $scroll, $savedb = false)
    {
        $key = 'a:u:scroll:' . $uid;
        $cache = Hapyfish2_Cache_Factory::getHFC($uid);

        if (!$savedb) {
            $savedb = $cache->canSaveToDB($key, 900);
        }

        if ($savedb) {
            $data = array();
            foreach ($scroll as $cid => $item) {
                $data[$cid] = array($item['count'], 0);
            }
            $ok = $cache->save($key, $data);
            if ($ok) {
                try {
                    $dalScroll = Hapyfish2_Alchemy_Dal_Scroll::getDefaultInstance();
                    foreach ($scroll as $cid => $item) {
                        if ($item['update']) {
                            $dalScroll->update($uid, $cid, $item['count']);
                        }
                    }
                } catch (Exception $e) {
                    info_log('[Hapyfish2_Alchemy_HFC_Scroll::updateUserScroll:'. $uid. ']' . $e->getMessage(), 'db.err');
                }
            }
            return $ok;
        } else {
            $data = array();
            foreach ($scroll as $cid => $item) {
                $data[$cid] = array($item['count'], $item['update']);
            }
            return $cache->update($key, $data);
        }
    }

    public static function addUserScroll($uid, $cid, $count = 1, $scroll = null)
    {
        //check if gained this unique item
        if (substr($cid, -2) == '21') {
            $uniqueItems = Hapyfish2_Alchemy_Cache_UniqueItem::getInfo($uid);
            //had already gained
            if (in_array($cid, $uniqueItems)) {
                return false;
            }

            $count = 1;
            $uniqueItems[] = (int)$cid;
            $saved = Hapyfish2_Alchemy_Cache_UniqueItem::saveInfo($uid, $uniqueItems);
            if ($saved) {
                $addItem = array((int)$cid, $count);
                //Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'showNewItem', array($addItem));
                
                $okMix = Hapyfish2_Alchemy_Bll_Card::mixScroll($uid, $cid);
                
                return $okMix;
            }
        }

        if (!$scroll) {
            $scroll = self::getUserScroll($uid);
            if ($scroll === null) {
                $scroll = array();
            }
        }

        $cid = (int)$cid;
        $count = (int)$count;

        if (isset($scroll[$cid])) {
            $scroll[$cid]['count'] += $count;
            $scroll[$cid]['update'] = 1;
        } else {
            $scroll[$cid] = array('count' => $count, 'update' => 1);
        }

        $ok = self::updateUserScroll($uid, $scroll);
        if ($ok) {
    		$addItem = array($cid, $count);
    		Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'addItems', array($addItem));

			//添加图鉴
			Hapyfish2_Alchemy_Bll_Illustrations::addUserIllustrations($uid, $cid);
        }
        return $ok;
    }

    public static function useUserScroll($uid, $cid, $count = 1, $scroll = null)
    {
        if (!$scroll) {
            $scroll = self::getUserScroll($uid);
            if ($scroll === null) {
                return false;
            }
        }

        $cid = (int)$cid;
        $count = (int)$count;

        if (!isset($scroll[$cid]) || $scroll[$cid]['count'] < $count) {
            return false;
        } else {
            $scroll[$cid]['count'] -= $count;
            $scroll[$cid]['update'] = 1;

            $ok = self::updateUserScroll($uid, $scroll, true);
    		if ($ok) {
    			$removeItems = array($cid, $count);
				Hapyfish2_Alchemy_Bll_UserResult::addField($uid, 'removeItems', array($removeItems));
    		}
    		return $ok;
        }
    }

}