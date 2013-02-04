<?php

class Hapyfish2_Alchemy_Bll_PlatformFeed_Sinawb
{
    const TASK_COMPLETED = 1;
	const GIFT_WISH = 2;
	const KILL_BOSS = 3;
	const BUILDING_LEVELUP = 4;
	const OCCUPY_HELP = 5;
	const USER_ROLE_PROMOTION = 6;
	
	const IMAGE_SIZE = '80x80';
	const IMAGE_SUFFIX = '.gif';
    const TITLE_SUFFIX = ' --- 来自新浪微游戏';
	
    protected static function getName($uid)
    {
		$name = '';
		$user = Hapyfish2_Platform_Bll_User::getUser($uid);
		if (!empty($user)) {
			$name = $user['name'];
		}
		
		return $name;
    }
	
	public static function getFeedData($type, $info, $imgUrl = '', $linkUrl = '')
    {
    	$templateInfo = Hapyfish2_Alchemy_Cache_Basic::getPlatformFeedTemplateInfo($type);
    	if ($templateInfo) {
    		if (isset($info['actor'])) {
    			$info['actor'] = self::getName($info['actor']);
    			$info['USER'] = $info['actor'];
    			$info['USER_TA'] = 'TA';
    		}
    	    if (isset($info['target'])) {
    			$info['target'] = self::getName($info['target']);
    		}
    		
    		$data = array();
    		$data['text'] = self::buildTemplate($templateInfo['title'], $info) . self::TITLE_SUFFIX;
    		$data['linktext'] = $templateInfo['linktext'];
    		$data['img'] = $imgUrl;
    		$data['link'] = $linkUrl;
    		$data['templateContent'] = $templateInfo['comment'];	//默认文字
    		return json_encode($data);
    	}
    	
    	return '';
    }
	
	protected static function buildTemplate($tpl, $data)
    {
        if (empty($data)) {
        	return $tpl;
        }

        $keys = array();
        $values = array();
    	foreach ($data as $k => $v) {
            $keys[] = '{*' . $k . '*}';
            $values[] = $v;
        }

        foreach ($data as $k => $v) {
        	$keys[] = '{_' . $k . '_}';
        	$values[] = $v;
        }
        
        return str_replace($keys, $values, $tpl);
    }
    
    public static function getFeedCode($uid, $type, $dt = '')
    {
    	$t = time();
    	$id = 10000 + $type;
    	$sig = md5($uid . $id . $dt . $t . APP_SECRET);
    	$code = $uid . '.' . $id . '.' . $dt . '.' . $t . '.' . $sig;
    	return base64_encode($code);
    }

    //任务完成[1]
    public static function taskCompleted($uid, $id, $title)
    {
    	$feedType = self::TASK_COMPLETED;
    	$feed = array(
    			'type' => $feedType,
    			'id' => $id,
    			'auto' => 0
    	);
    	$feedInfo = array(
    			'actor' => $uid,
    			'title' => $title
    	);
    	$imgUrl = STATIC_HOST . '/alchemy/image/share/' . $feedType . '/default_' . self::IMAGE_SIZE . self::IMAGE_SUFFIX;
    	$linkUrl = HOST . '/entrance/feed?fcode=' . self::getFeedCode($uid, $feedType, $id);
    	$feed['data'] = self::getFeedData($feedType, $feedInfo, $imgUrl, $linkUrl);
    	Hapyfish2_Alchemy_Bll_UserResult::addPlatformFeed($uid, $feed);
    
    	//自动弹出的没有奖励
    	if ($feed['auto'] == 0) {
    		Hapyfish2_Alchemy_Cache_PlatformFeedAward::add($uid);
    	}
    }
    
    //礼物愿望[2]
    public static function giftWish($uid, $gid)
    {
		$feedType = self::GIFT_WISH;
		$feed = array(
			'type' => $feedType,
			'auto' => 0
		);
		$giftInfo = Hapyfish2_Alchemy_Cache_Gift::getBasicGiftInfo($gid);
		$feedInfo = array(
			'actor' => $uid,
			'gift' => $giftInfo['name']
		);
		$imgUrl = STATIC_HOST . '/alchemy/image/share/' . $feedType . '/default_' . self::IMAGE_SIZE . self::IMAGE_SUFFIX; 
		$linkUrl = HOST . '/entrance/feed?fcode=' . self::getFeedCode($uid, $feedType);
		$feed['data'] = self::getFeedData($feedType, $feedInfo, $imgUrl, $linkUrl);
		Hapyfish2_Alchemy_Bll_UserResult::addPlatformFeed($uid, $feed);
		
		//自动弹出的没有奖励
		if ($feed['auto'] == 0) {
			Hapyfish2_Alchemy_Cache_PlatformFeedAward::add($uid);
		}
    }
    
    //BOSS战胜利[3]
    public static function killBoss($uid, $id)
    {
		$feedType = self::KILL_BOSS;
		$feed = array(
			'type' => $feedType,
			'id' => $id,
			'auto' => 0
		);
		$monster = Hapyfish2_Alchemy_Cache_Basic::getMonsterInfo($id);
		$feedInfo = array(
			'actor' => $uid,
			'boss' => $monster['name']
		);
		$imgUrl = STATIC_HOST . '/alchemy/image/share/' . $feedType . '/default_' . self::IMAGE_SIZE . self::IMAGE_SUFFIX; 
		$linkUrl = HOST . '/entrance/feed?fcode=' . self::getFeedCode($uid, $feedType, $id);
		$feed['data'] = self::getFeedData($feedType, $feedInfo, $imgUrl, $linkUrl);
		Hapyfish2_Alchemy_Bll_UserResult::addPlatformFeed($uid, $feed);
		
		//自动弹出的没有奖励
		if ($feed['auto'] == 0) {
			Hapyfish2_Alchemy_Cache_PlatformFeedAward::add($uid);
		}
    }
    
    //建筑升级[4]
    //id 
    //1: 家, 2: 村铁匠铺 3: 村酒馆  4: 王城酒馆
    public static function buildingLevelup($uid, $id)
    {
		$feedType = self::BUILDING_LEVELUP;
		$feed = array(
			'type' => $feedType,
			'id' => $id,
			'auto' => 0
		);
		$feedInfo = array(
			'actor' => $uid
		);
		$imgUrl = STATIC_HOST . '/alchemy/image/share/' . $feedType . '/default_' . self::IMAGE_SIZE . self::IMAGE_SUFFIX;  
		$linkUrl = HOST . '/entrance/feed?fcode=' . self::getFeedCode($uid, $feedType, $id);
		$feed['data'] = self::getFeedData($feedType, $feedInfo, $imgUrl, $linkUrl);
		Hapyfish2_Alchemy_Bll_UserResult::addPlatformFeed($uid, $feed);
		
		//自动弹出的没有奖励
		if ($feed['auto'] == 0) {
			Hapyfish2_Alchemy_Cache_PlatformFeedAward::add($uid);
		}
    }
    
    //占领帮助[5]
    public static function occupyHelp($uid)
    {
		$feedType = self::OCCUPY_HELP;
		$feed = array(
			'type' => $feedType
		);
		
		$feedInfo = array(
			'actor' => $uid
		);
		$imgUrl = STATIC_HOST . '/alchemy/image/share/' . $feedType . '/default_' . self::IMAGE_SIZE . self::IMAGE_SUFFIX;
		$linkUrl = HOST . '/entrance/feed?fcode=' . self::getFeedCode($uid, $feedType);
		$feed['data'] = self::getFeedData($feedType, $feedInfo, $imgUrl, $linkUrl);
		return $feed;
    }
    
    //主角晋级[6]
    public static function userRolePromotion($uid, $level)
    {
		//升级到3级的时候发送
    	if ($level >= 3) {
	    	$feedType = self::USER_ROLE_PROMOTION;
			$feed = array(
				'type' => $feedType,
				'id' => $level,
				'auto' => 0
			);
			$feedInfo = array(
				'actor' => $uid,
				'level' => $level
			);
			$imgUrl = STATIC_HOST . '/alchemy/image/share/' . $feedType . '/default_' . self::IMAGE_SIZE . self::IMAGE_SUFFIX;
			$linkUrl = HOST . '/entrance/feed?fcode=' . self::getFeedCode($uid, $feedType);
			$feed['data'] = self::getFeedData($feedType, $feedInfo, $imgUrl, $linkUrl);
			Hapyfish2_Alchemy_Bll_UserResult::addPlatformFeed($uid, $feed);
			
			//自动弹出的没有奖励
			if ($feed['auto'] == 0) {
				Hapyfish2_Alchemy_Cache_PlatformFeedAward::add($uid);
			}
		}
    }
        
}