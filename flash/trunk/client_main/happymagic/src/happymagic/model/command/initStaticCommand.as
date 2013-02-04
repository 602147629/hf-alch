package happymagic.model.command 
{
	import com.brokenfunction.json.decodeJson;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import happyfish.feed.vo.FeedVo;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.model.vo.GuidesClassVo;
	import happymagic.manager.DataManager;
	import happymagic.model.MagicUrlLoader;
	import happymagic.model.vo.ai.AIScriptVo;
	import happymagic.model.vo.AvatarVo;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	import happymagic.model.vo.classVo.DecorClassVo;
	import happymagic.model.vo.classVo.IllustrationsClassVo;
	import happymagic.model.vo.classVo.WorldMapClassVo;
	import happymagic.model.vo.EnemyClassVo;
	import happymagic.model.vo.GameSettingVo;
	import happymagic.model.vo.ItemType;
	import happymagic.model.vo.ItemVo;
	import happymagic.model.vo.LevelInfoVo;
	import happymagic.model.vo.MineVo;
	import happymagic.model.vo.MiningScriptVo;
	import happymagic.model.vo.MonsterVo;
	import happymagic.model.vo.NpcChatVo;
	import happymagic.model.vo.NpcClassVo;
	import happymagic.model.vo.NpcVo;
	import happymagic.model.vo.RoomLevelVo;
	import happymagic.model.vo.SceneClassVo;
	import happymagic.model.vo.SignAwardVo;
	import happymagic.model.vo.SkillAndItemVo;
	import happymagic.model.vo.TaskClassVo;
	import happymagic.model.vo.TaskTipsVo;
	/**
	 * 初始静态信息
	 * @author slam
	 */
	public class initStaticCommand extends EventDispatcher
	{
		
		public function initStaticCommand() 
		{
			
		}
		
		public function load():void {
			var loader:MagicUrlLoader = new MagicUrlLoader();
			loader.retry = true;
			loader.compress = true;
			loader.addEventListener(Event.COMPLETE, load_complete);
			
			var request:URLRequest = new URLRequest(InterfaceURLManager.getInstance().getUrl('loadstatic',false));
			request.method = URLRequestMethod.GET;
			var vars:URLVariables = new URLVariables();
			vars.tmp = 1;
			
			request.data = vars;
			loader.load(request);
		}
		
		private function load_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, load_complete);
			
			//return;
			var data:Object = decodeJson(e.target.data);
			
			var i:int;
			
			
			//存入datamanager
			var datam:DataManager = DataManager.getInstance();
			datam.initStaticData = data;
			
			
			/*var decor_array:Array = new Array();
			//装饰物类
			for (var name:String in data.decorClass) {
				var decor_class_vo:DecorClassVo =  new DecorClassVo();
				decor_class_vo.setData(data.decorClass[name]);
				if (decor_class_vo.d_id == 191001) {
					decor_class_vo.size_z = 2;
				}
				
				decor_array.push(decor_class_vo);
			}
			if (data.dungeonDecorClass) //地下城装饰物 2011.11.24
			{
				for (name in data.dungeonDecorClass) 
				{
					decor_class_vo =  new DecorClassVo();
					decor_class_vo.setData(data.dungeonDecorClass[name]);
					decor_array.push(decor_class_vo);
				}
			}
			datam.decorClass = decor_array;*/
			
			// 雇用为价格列表
			datam.roleData.rolePosPriceMap = data.rolePosPrices;
			delete data.rolePosPrices;
			
			//合成术类
			datam.mixData.setMixClassList(data.mixClass);
			delete data.mixClass;
			
			//道具基础类
			datam.itemData.setItemClassList(data.itemClass);
			delete data.itemClass;
			
			//任务类
			//if (data.taskClass) 
			//{
				//var taskArr:Array = new Array();
				//var tmpTask:TaskClassVo = new TaskClassVo();
				//for (i = 0; i < data.taskClass.length; i++) 
				//{
					//tmpTask =  new TaskClassVo().setData(data.taskClass[i]) as TaskClassVo;
					//taskArr.push(tmpTask);
				//}
				//datam.taskClass = taskArr;
			//}
			
			
			//avatar
			if (data.avatarClass) 
			{
				for (i = 0; i < data.avatarClass.length; i++) 
				{
					datam.avatars.push(new AvatarVo().setData(data.avatarClass[i]));
				}
			}
			
			
			//用户等级信息
			datam.levelInfos = new Array();
			if (data.levelInfos) 
			{
				for (i = 0; i < data.levelInfos.length; i++) 
				{
					datam.levelInfos.push(new LevelInfoVo().setData(data.levelInfos[i]));
				}
			}
			
			//房间等级信息
			datam.roomLevels = new Vector.<RoomLevelVo>();
			if (data.roomLevels) 
			{
				for (i = 0; i < data.roomLevels.length; i++) 
				{
					datam.roomLevels.push(new RoomLevelVo().setData(data.roomLevels[i]));
				}
			}
			
			//技能与物品作用
			datam.skillAndItems = new Vector.<SkillAndItemVo>();
			if (data.skillAndItems) 
			{
				for (i = 0; i < data.skillAndItems.length; i++) 
				{
					datam.skillAndItems.push(new SkillAndItemVo().setData(data.skillAndItems[i]));
				}
			}
			
			//战斗ai脚本
			datam.aIScripts = new Vector.<AIScriptVo>();
			if (data.aIScripts) 
			{
				for (i = 0; i < data.aIScripts.length; i++) 
				{
					datam.aIScripts.push(new AIScriptVo().setData(data.aIScripts[i]));
				}
			}
			
			//场景类
			if (data.sceneClass) 
			{
				var sceneClass:Array = new Array();
				for (i = 0; i < data.sceneClass.length; i++) 
				{
					sceneClass.push(new SceneClassVo().setData(data.sceneClass[i]));
				}
				data.sceneClass = sceneClass;
				datam.sceneClass = sceneClass;
			}
			
			//npc类
			if (data.npcClass) 
			{
				var npcClass:Array = new Array();
				for (i = 0; i < data.npcClass.length; i++) 
				{
					npcClass.push(new NpcClassVo().setData(data.npcClass[i]));
				}
				data.npcClass = npcClass;
				datam.npcClass = npcClass;
			}
			
			if (data.guideClass) 
			{
				var tmpGuideClass:Array = new Array();
				for (i = 0; i < data.guideClass.length; i++) 
				{
					tmpGuideClass.push(new GuidesClassVo().setData(data.guideClass[i]));
				}
				data.guideClass = tmpGuideClass;
				datam.guidesClass = tmpGuideClass;
			}
			
			//feed类
			if (data.feedClass) 
			{
				var tmpFeedClass:Array = new Array();
				for (i = 0; i < data.feedClass.length; i++) 
				{
					tmpFeedClass.push(new FeedVo().setData(data.feedClass[i]));
				}
				data.feedClass = tmpFeedClass;
				datam.feedClass = tmpFeedClass;
			}
			
			//连续登陆类
			if (data.signAwardClass)
			{
				var tmpsignAwardClass:Array = new Array();
				for (i = 0; i < data.signAwardClass.length; i++) 
				{
					tmpsignAwardClass.push(new SignAwardVo().setVaule(data.signAwardClass[i]));
				}
				data.signAwardClass = tmpsignAwardClass;
				datam.signAwardClass = tmpsignAwardClass;				
			}
			
			//任务说明类
			if (data.taskTips) 
			{
				//var taskTips:Array = new Array();
				//for (var j:int = 0; j < data.taskTips.length; j++) 
				//{
					//taskTips.push(new TaskTipsVo().setData(data.taskTips[j]));
				//}
				//data.taskTips = taskTips;
				//datam.taskTips = taskTips;
			}		
			
			//配置信息
			//datam.basicClass = data.basicClass;
			
			//2011.11.11
			datam.monsterClass = data.monsterClass ? data.monsterClass : [];
			datam.mineClass = data.mineClass ? data.mineClass : [];
			datam.npcClass = data.npcClass ? data.npcClass : [];
			
			
			//图鉴静态数据
			if (data.illustrationsClass)
			{
				for (i = 0; i < data.illustrationsClass.length; i++)
				{
					datam.illustratedData.illustratedHandbookStatic.push(new IllustrationsClassVo().setData(data.illustrationsClass[i]));
				}				
			}

			//世界地图静态数据
			if (data.worldMapClass)
			{
				for (i = 0; i < data.worldMapClass.length; i++)
				{
					datam.worldData.worldmapStaticData.push(new WorldMapClassVo().setVaule(data.worldMapClass[i]));
				}				
			}			
			
			
			//游戏配置数据
			if (data.gameData) 
			{
				datam.gameSetting = new GameSettingVo();
				datam.gameSetting.setData(data.gameData);
			}
			
			//地图静态数据
			if (data.mapcopyClass) datam.mapCopyClass = data.mapcopyClass;
			
			//职业相克和属性相克
			if (data.jobAdj) datam.jobAdj = data.jobAdj;
			if (data.propAdj) datam.propAdj = data.propAdj;
			
			//商店商品列表
			var shopItemListArr:Array = data.shopItemList as Array;
			if (shopItemListArr)
			{
				datam.shopItemList = new Array;
				datam.shopMatList = new Array;
				for (i = 0; i < shopItemListArr.length; i++)
				{
					var itemClassVo:BaseItemClassVo = datam.itemData.getItemClass(int(shopItemListArr[i]));
					if (itemClassVo.type == ItemType.Stuff) datam.shopMatList.push(itemClassVo);
					else datam.shopItemList.push(itemClassVo);
				}
			}			
			
			//特殊功能的物品列表
			if (data.autoUseList)
			{
				datam.itemData.autoUseStaticList = data.autoUseList;
			}
			
			
			dispatchEvent(new Event(Event.COMPLETE));
		}
	}

}