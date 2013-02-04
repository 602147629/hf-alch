<?php

class Hapyfish2_Alchemy_Bll_Feed
{
	
	public static function getFeed($uid, $pageIndex = 1, $pageSize = 50)
    {
		//get user mini feed
        $feeds = self::getFeedData($uid);
        
        if (empty($feeds)) {
        	return array();
        }
        
        Hapyfish2_Alchemy_Cache_Feed::clearNewMiniFeedCount($uid);
        
        return self::buildFeed($feeds);
    }
    
	public static function getFeedData($uid)
	{
		$data = Hapyfish2_Alchemy_Cache_Feed::getFeedData($uid);
		if ($data === false) {
			return array();
		}
		
		$result = array();
		foreach ($data as $feed) {
			$result[] = array(
				'uid' => $feed[0],
				'template_id' => $feed[1],
				'type' => $feed[2],
				'actor' => $feed[3],
				'target' => $feed[4],
				'title' => $feed[5],
				'create_time' => $feed[6],
				'id' => $feed[7],
				'isNew' => $feed[8]
			);
		}
		
		return $result;
	}
	
	public static function flushFeedData($uid)
	{
		Hapyfish2_Alchemy_Cache_Feed::flush($uid);
	}
	
	public static function insertMiniFeed($feed)
	{
	    $uid = $feed['uid'];
	    
	    $id = self::getNewFeedId($uid);
	    
	    $newfeed = array(
	    	$feed['uid'], $feed['template_id'], $feed['type'], $feed['actor'], $feed['target'], $feed['title'], $feed['create_time'], $id, 1
	    );
	    
	    Hapyfish2_Alchemy_Cache_Feed::insertMiniFeed($uid, $newfeed);
	    
		//update user feed status
        Hapyfish2_Alchemy_Cache_Feed::incNewMiniFeedCount($uid);
	}
	
    protected static function buildFeed(&$feeds)
    {
        $tpl = Hapyfish2_Alchemy_Cache_Basic::getFeedTemplate();
    	for($i = 0, $count = count($feeds); $i < $count; $i++) {
    		$template_id = $feeds[$i]['template_id'];
        	$tplTitle = isset($tpl[$template_id]) ? $tpl[$template_id] : '';
        	$feedTitle = isset($feeds[$i]['title']) ? $feeds[$i]['title'] : array();
        	$title = self::buildTemplate($feeds[$i]['actor'], $feeds[$i]['target'], $tplTitle, $feedTitle, $template_id);
    	    if ($title) {
                $feeds[$i]['title'] = $title;
            }
            else {
                $feeds[$i]['title'] = '';
            }
            //unset($feeds[$i]['uid']);
            unset($feeds[$i]['template_id']);
            $feeds[$i]['createTime'] = $feeds[$i]['create_time'];
            unset($feeds[$i]['create_time']);
            
            $feeds[$i]['uName'] = $feeds[$i]['actor'];
            unset($feeds[$i]['actor']);
            unset($feeds[$i]['target']);
            
            $feeds[$i]['content'] = $feeds[$i]['title'];
            unset($feeds[$i]['title']);
        }

        return $feeds;
    }
    
    protected static function buildTemplate($actor_id, $target_id, $tplTitle, $feedTitle, $template_id)
    {
        if ($feedTitle == null) {
            $feedTitle = array();
        }

        if (!is_array($feedTitle)) {
            return false;
        }

        $actor = Hapyfish2_Platform_Bll_User::getUser($actor_id);

        if (empty($actor)) {
            $actor_name = "____";
        }
        else {
            $actor_name = '<a href="event:' . $actor_id . '"><font color="#00CC99">' . $actor['name'] . '</font></a>';
        }

        $feedTitle['actor'] = $actor_name;

        if ($target_id) {
            $target = Hapyfish2_Platform_Bll_User::getUser($target_id);

            if (empty($target)) {
                $target_name = "____";
            }
            else {
            	$target_name = '<a href="event:' . $target_id . '"><font color="#00CC99">' .  $target['name'] . '</font></a>';
            }

            $feedTitle['target'] = $target_name;
        }

        $keys = array();
        $values = array();
        
		foreach ($feedTitle as $k => $v) {
			$keys[] = '{*' . $k . '*}';
			$values[] = $v;
		}
        
        return str_replace($keys, $values, $tplTitle);
    }

    public static function getNewFeedId($uid)
    {
        try {
            $dalUserSequence = Hapyfish2_Alchemy_Dal_UserSequence::getDefaultInstance();
            return $dalUserSequence->get($uid, 'j', 1);
        } catch (Exception $e) {
        	info_log('[Hapyfish2_Alchemy_Bll_Feed::getNewFeedId:'. $uid. ']' . $e->getMessage(), 'db.err');
        }
        return 0;
    }
	
}