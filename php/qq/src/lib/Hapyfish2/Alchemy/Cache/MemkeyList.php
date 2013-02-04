<?php

class Hapyfish2_Alchemy_Cache_MemkeyList
{

    public static function mapUserMcKey($key = null)
    {
		$memKey = array(
		    'a:u:hfskey:' 		   	    => 'a:u:hfskey:',
			'a:u:fight:' 		   	    => 'a:u:fight:',
			'a:u:fightcorps:' 		    => 'a:u:fightcorps:',
			'a:u:fightattrib:' 		    => 'a:u:fightattrib:',
			'a:u:fightoccupy:' 		    => 'a:u:fightoccupy:',
			'a:u:fightassist:' 		    => 'a:u:fightassist:',
			'a:u:fight:friendass:' 	    => 'a:u:fight:friendass:',
			'a:u:feats:' 		        => 'a:u:feats:',
			'a:u:fightmercenaryids:'    => 'a:u:fightmercenaryids:',
			'a:u:mercenary:'            => 'a:u:mercenary:',
			'a:u:mercenarygrow:'        => 'a:u:mercenarygrow:',
			'a:u:worldmap:'             => 'a:u:worldmap:',
			'a:u:mapcopy:'              => 'a:u:mapcopy:',
			'a:u:taskopen:'             => 'a:u:taskopen:',
			'a:u:taskdly:'              => 'a:u:taskdly:',
			'a:u:alltask:'              => 'a:u:alltask:',
			'a:u:taskstatus:'           => 'a:u:taskstatus:',
			'a:u:gift:newrececnt:'      => 'a:u:gift:newrececnt:',
			'a:u:gift:wish:'            => 'a:u:gift:wish:',
			'a:u:gift:sent:g:uids:'     => 'a:u:gift:sent:g:uids:',
			'a:u:gift:sent:w:uids:'     => 'a:u:gift:sent:w:uids:',
			'a:u:uniqueitem:'           => 'a:u:uniqueitem:',

            'a:u:mix:'                  => 'a:u:mix:',
            'a:u:deocr:'                => 'a:u:deocr:',
            'a:u:fun:onroom:'           => 'a:u:fun:onroom:',
            'a:u:fun:onrids:'           => 'a:u:fun:onrids:',
            'a:u:goods:'                => 'a:u:goods:',
            'a:u:scroll:'               => 'a:u:scroll:',
            'a:u:stuff:'                => 'a:u:stuff:',
            'a:u:decor:inbag:'          => 'a:u:decor:inbag:',
            'a:u:weapon:ids:'           => 'a:u:weapon:ids:',
            'a:u:weapon:'               => 'a:u:weapon:',
            'a:u:illts:'                => 'a:u:illts:',
            'a:u:order:'                => 'a:u:order:',
            'a:u:maxodrcut:'            => 'a:u:maxodrcut:',
            'a:u:orderlist:'            => 'a:u:orderlist:',
            'a:u:maxmercrycut:'         => 'a:u:maxmercrycut:',
            'a:u:storylist:'         	=> 'a:u:storylist:',
            'a:u:dialoglist:'         	=> 'a:u:dialoglist:',
            'a:u:help:'         		=> 'a:u:help:',
            'a:u:unlockfunc:'         	=> 'a:u:unlockfunc:',
            'a:u:decor:bag:'         	=> 'a:u:decor:bag:',
            'a:u:decor:scene:'         	=> 'a:u:decor:scene:',
            'a:u:block:'         		=> 'a:u:block:',
            'a:u:goods:pond:'         	=> 'a:u:goods:pond:',

            'a:u:hirelist:'         	=> 'a:u:hirelist:',
            'a:u:wine:'         		=> 'a:u:wine:',
            'a:u:orderreqlist:'         => 'a:u:orderreqlist:',
            'a:u:satisfaction:'         => 'a:u:satisfaction:',
            'a:u:lastrequtime:'         => 'a:u:lastrequtime:',
            'a:u:orderfids:'         	=> 'a:u:orderfids:',
            'a:u:strthen:'         		=> 'a:u:strthen:',
            'a:u:scene:'         		=> 'a:u:scene:',
            'a:u:avatar:'         		=> 'a:u:avatar:',
            'a:u:exp:'         			=> 'a:u:exp:',
            'a:u:coin:'         		=> 'a:u:coin:',
            'a:u:gem:'         			=> 'a:u:gem:',
            'a:u:level:'         		=> 'a:u:level:',
            'a:u:sp:'         			=> 'a:u:sp:',
		
            'a:u:login:'         		=> 'a:u:login:',
            'a:u:createtm:'        		=> 'a:u:createtm:',
            'a:u:maxmercyct:'         	=> 'a:u:maxmercyct:',
            'a:u:tavernlevel:'         	=> 'a:u:tavernlevel:',
            'a:u:smithylevel:'         	=> 'a:u:smithylevel:',
            'a:u:hometitle:'         	=> 'a:u:hometitle:',
            'a:u:homelevel:'         	=> 'a:u:homelevel:',
            'a:u:fun:ids:'         		=> 'a:u:fun:ids:',
            'a:u:illusts:'         		=> 'a:u:illusts:',
            'a:u:mapcopy:series:'       => 'a:u:mapcopy:series:',
            'a:u:merwork:ids:'         	=> 'a:u:merwork:ids:',
		
			'a:u:timeGift:'				=>'a:u:timeGift:',
			'a:u:sevenGift:'	    	=>'a:u:sevenGift:',
			'a:u:levelGift:'	    	=>'a:u:levelGift:',
			'a:u:package:'				=>'a:u:package:',
		
            'a:u:person:'         		=> 'a:u:person:',
            'a:u:openportal:'         	=> 'a:u:openportal:',
            'a:u:openmine:'         	=> 'a:u:openmine:',
            'a:u:monster:'         		=> 'a:u:monster:',
            'a:u:opentransport:'        => 'a:u:opentransport:',
		
			//竞技场
            'a:u:arenascore:'        	=> 'a:u:arenascore:',
            'a:u:arenarank:'        	=> 'a:u:arenarank:',
            'a:u:arena:'        		=> 'a:u:arena:',
            'i:u:arenafeed:'        	=> 'i:u:arenafeed:',

			//训练营
			'a:u:trainingids:'        	=> 'a:u:trainingids:',
			'a:u:wine:bot:'				=> 'a:u:wine:bot:'
/**********************************************************************************************/
		
		
		
		);
		
		/*$allMercenary = Hapyfish2_Alchemy_HFC_FightMercenary::getAll($uid);
		foreach ( $allMercenary as $v ) {
			$keys[] = 'a:u:mercenary:' . $uid . ':' . $v['mid'];
		}
		
		$allFurnace = Hapyfish2_Alchemy_HFC_Furnace::getAll($uid);
		foreach ( $allFurnace as $m ) {
            $keys[] = 'a:u:furnace:' . $uid . ':' . $m[0];
		}
		
    	$allWordk = Hapyfish2_Alchemy_HFC_MercenaryWork::getAll($uid);
		foreach ( $allWordk as $n ) {
            $keys[] = 'a:u:merwork:' . $uid . ':' . $n['id'];
		}
        
		$allMap = Hapyfish2_Alchemy_Cache_Basic::getMapCopyVerList();
		foreach ( $allMap as $k ) {
    		$keys[] = 'a:u:mapcopy:' . $uid . ':' . $k['map_id'];
		}*/
    	
		if (!$key) {
			return $memKey;
		}

		if (!isset($memKey[$key])) {
			return $key;
		}

		return $memKey[$key];
    }

	public static function mapBasicMcKey($key = null)
    {
		$memKey = array(
			'alchemy:bas:mercenary' 		            => 'alchemy:bas:mercenary',
			'alchemy:bas:monster' 				        => 'alchemy:bas:monster',
			'alchemy:bas:mine' 				            => 'alchemy:bas:mine',
			'alchemy:bas:effect' 				        => 'alchemy:bas:effect',
			'alchemy:bas:restrict' 				        => 'alchemy:bas:restrict',
			'alchemy:bas:declare' 				        => 'alchemy:bas:declare',
			'alchemy:bas:monstermatrix' 				=> 'alchemy:bas:monstermatrix',
			'alchemy:bas:assistance' 				    => 'alchemy:bas:assistance',
			'alchemy:bas:worldmap' 				        => 'alchemy:bas:worldmap',
			'alchemy:bas:mapcopydetail:' 				=> 'alchemy:bas:mapcopydetail:',
			'alchemy:bas:mapcopyver' 				    => 'alchemy:bas:mapcopyver',
			'alchemy:bas:tasktype' 				        => 'alchemy:bas:tasktype',
			'alchemy:bas:taskcondilist' 				=> 'alchemy:bas:taskcondilist',
			'alchemy:bas:taskcondi:' 				    => 'alchemy:bas:taskcondi:',
			'alchemy:bas:tasklist' 				        => 'alchemy:bas:tasklist',
			'alchemy:bas:task:' 				        => 'alchemy:bas:task:',
			'alchemy:bas:gift' 				            => 'alchemy:bas:gift',
			'alchemy:bas:feedtemplate' 				    => 'alchemy:bas:feedtemplate',

            'alchemy:bas:mix'                           => 'alchemy:bas:mix',
            'alchemy:bas:goods'                        	=> 'alchemy:bas:goods',
            'alchemy:bas:mercenarycard'                 => 'alchemy:bas:mercenarycard',
            'alchemy:bas:scroll'                        => 'alchemy:bas:scroll',
            'alchemy:bas:stuff'                        	=> 'alchemy:bas:stuff',
            'alchemy:bas:furnace'                       => 'alchemy:bas:furnace',
            'alchemy:bas:decor'                        	=> 'alchemy:bas:decor',
            'alchemy:bas:weapon'                        => 'alchemy:bas:weapon',
            'alchemy:bas:illustrations'                 => 'alchemy:bas:illustrations',
            'alchemy:bas:userlevellist'                 => 'alchemy:bas:userlevellist',
            'alchemy:bas:roomlevellist'                 => 'alchemy:bas:roomlevellist',
            'alchemy:bas:avatar'                        => 'alchemy:bas:avatar',
            'alchemy:bas:scene'                        	=> 'alchemy:bas:scene',
            'alchemy:bas:order'                        	=> 'alchemy:bas:order',
            'alchemy:bas:avatarname'                    => 'alchemy:bas:avatarname',
            'alchemy:bas:orderpro'                   	=> 'alchemy:bas:orderpro',


			//佣兵
            'alchemy:bas:mercenarygrowclass'            => 'alchemy:bas:mercenarygrowclass',
            'alchemy:bas:mercenarygrow'                 => 'alchemy:bas:mercenarygrow',
            'alchemy:bas:mercenarylevel'                => 'alchemy:bas:mercenarylevel',
            'alchemy:bas:mercenaryname'                 => 'alchemy:bas:mercenaryname',
            'alchemy:bas:mercenarystrthen'              => 'alchemy:bas:mercenarystrthen',
            'alchemy:bas:mercenarypos'             		=> 'alchemy:bas:mercenarypos',
            'alchemy:bas:mercenarywork'             	=> 'alchemy:bas:mercenarywork',
            'alchemy:bas:mercenaryworkls'             	=> 'alchemy:bas:mercenaryworkls',
            'alchemy:bas:mercenaryrprand'               => 'alchemy:bas:mercenaryrprand',
            'alchemy:bas:mercenaryquality'              => 'alchemy:bas:mercenaryquality',
            'alchemy:bas:mercenaryprocontrast'          => 'alchemy:bas:mercenaryprocontrast',
				
			//剧情
            'alchemy:bas:storylist'                 	=> 'alchemy:bas:storylist',
            'alchemy:bas:storyactionlist'               => 'alchemy:bas:storyactionlist',
            'alchemy:bas:storynpclist'                 	=> 'alchemy:bas:storynpclist',
            'alchemy:bas:storydialoglist'               => 'alchemy:bas:storydialoglist',

			//等级-酒馆，自宅，主角资质
            'alchemy:bas:tavernlevel'               	=> 'alchemy:bas:tavernlevel',
            'alchemy:bas:homelevel'               		=> 'alchemy:bas:homelevel',
            'alchemy:bas:rolelevel'               		=> 'alchemy:bas:rolelevel',
            'alchemy:bas:smithylevel'               	=> 'alchemy:bas:smithylevel',
            'alchemy:bas:traininglevel'               	=> 'alchemy:bas:traininglevel',
            'alchemy:bas:arenalevel'               		=> 'alchemy:bas:arenalevel',
			
			//初始化数据
			'alchemy:bas:inituserinfo'                 	=> 'alchemy:bas:inituserinfo',
			'alchemy:bas:initrolelist'                 	=> 'alchemy:bas:initrolelist',

			//新手引导
			'alchemy:bas:help'                 			=> 'alchemy:bas:help',
			//活动礼包 时间礼包 七天礼包  等级礼包
			'alchemy:bas:timeGift'						=>'alchemy:bas:timeGift',
			'alchemy:bas:sevenGift'						=>'alchemy:bas:sevenGift',
			'alchemy:bas:levelGift'						=>'alchemy:bas:sevenGift',
		
			//NPC消失，显示功能
			'alchemy:bas:person'                 		=> 'alchemy:bas:person',
			'alchemy:bas:transport'                 	=> 'alchemy:bas:transport',
			'alchemy:bas:fightexplevdiff'               => 'alchemy:bas:fightexplevdiff',
			//vip福利
			'alchemy:bas:vip'							=>'alchemy:bas:vip',
		
			//竞技场奖励
			'alchemy:bas:arenaaward'                 	=> 'alchemy:bas:arenaaward',

			//训练营
			'alchemy:bas:traininglist'                 	=> 'alchemy:bas:traininglist',
				
			//统计-用户操作记录
			'alchemy:bas:statuseraction'                => 'alchemy:bas:statuseraction',

			//平台-Feed模板
			'alchemy:bas:PlatformFeedTemplate'          => 'alchemy:bas:PlatformFeedTemplate',
/**********************************************************************************************/
			'alchemy:bas:PaySettingList'          		=> 'alchemy:bas:PaySettingList',
			
		
		);

		$mapList = Hapyfish2_Alchemy_Cache_Basic::getMapCopyVerList();
		foreach ( $mapList as $v ) {
			$mapKey = 'alchemy:bas:mapcopydetail:'.$v['map_id'];
			$memKey[$mapKey] = $mapKey;
		}
		
		if(!$key) {
			return $memKey;
		}

		if (!isset($memKey[$key])) {
			return $key;
		}

		return $memKey[$key];
    }

}