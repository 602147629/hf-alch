package happymagic.model.command 
{
	import adobe.utils.CustomActions;
	import com.brokenfunction.json.decodeJson;
	import com.brokenfunction.json.encodeJson;
	import com.friendsofed.isometric.IsoUtils;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.geom.Point;
	import flash.net.URLRequest;
	import flash.net.URLVariables;
	import flash.utils.setTimeout;
	import happyfish.manager.EventManager;
	import happyfish.model.DataCommandBase;
	import happyfish.model.UrlConnecter;
	import happyfish.scene.astar.Grid;
	import happyfish.scene.astar.Node;
	import happyfish.scene.camera.CameraControl;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.time.Time;
	import happyfish.utils.display.McShower;
	import happyfish.utils.SysTracer;
	import happymagic.control.command.ChangeUserDataCommand;
	import happymagic.display.control.StoryPlayCommand;
	import happymagic.events.DataManagerEvent;
	import happymagic.events.DiaryEvent;
	import happymagic.events.FriendsEvent;
	import happymagic.events.SceneEvent;
	import happymagic.events.SysMsgEvent;
	import happymagic.events.TaskEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.control.TakeResultVoControl;
	import happymagic.model.MagicUrlLoader;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.DiaryVo;
	import happymagic.model.vo.IllustrationsVo;
	import happymagic.model.vo.ItemVo;
	import happymagic.model.vo.MineVo;
	import happymagic.model.vo.MonsterVo;
	import happymagic.model.vo.NpcVo;
	import happymagic.model.vo.PortalVo;
	import happymagic.model.vo.ResultVo;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.SceneClassVo;
	import happymagic.model.vo.StoryActionVo;
	import happymagic.model.vo.StoryVo;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.bigScene.NpcView;
	import happymagic.scene.world.control.RoomUpgradeMvCommand;
	import happymagic.scene.world.grid.item.Decor;
	import happymagic.scene.world.grid.item.SpecialBuild;
	//import happymagic.scene.world.grid.item.Door;
	//import happymagic.scene.world.grid.person.Student;
	import happymagic.scene.world.MagicWorld;
	
	[Event(name = "complete", type = "flash.events.Event")]
	
	/**
	 * ...
	 * @author jj
	 */
	public class BaseDataCommand extends DataCommandBase
	{
		public var result:ResultVo;
		//是否显示错误信息
		public var takeResult:Boolean = true;
		//是否自动播放剧情
		public var playStory:Boolean = true;
		//是否用飘屏显示错误信息
		public var piaoMsg:Boolean = true;
		public var piaoPoint:Point;
		
		public var compress:Boolean;//读取的数据是否压缩过的
		public function BaseDataCommand(_callBack:Function=null) 
		{
			super(_callBack);
		}
		
		override public function createLoad():void 
		{
			loader = new MagicUrlLoader() as UrlConnecter;
			loader.compress = compress;
			//loader.retry = true;
			loader.addEventListener(Event.COMPLETE, load_complete);
		}
		
		override protected function load_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, load_complete);
			
			//SysTracer.systrace(e.target.data);
			
			var tmdm:DataManager = DataManager.getInstance();
			var decor_vo:DecorVo;
			var user_vo:UserVo;
			
			// decodeJson 反编译JS
			objdata = decodeJson(e.target.data);
			data = new Object();
			
			if (objdata.systime)
			{
				Time.setCurrTime(objdata.systime);
			}
			
			for (var name:String in objdata) 
			{
				var i:int;
				var j:int;
				switch (name) 
				{
					case "result":
						data.result = result = new ResultVo().setValue(objdata.result);
						if (takeResult || !data.result.isSuccess)
						{
						    TakeResultVoControl.getInstance().take(result, piaoMsg, piaoPoint);							
						}else if(result.sp<0){
							var tmpresult2:ResultVo = new ResultVo();
							tmpresult2.sp = result.sp;
							TakeResultVoControl.getInstance().take(tmpresult2, piaoMsg, piaoPoint);	
						}
					break;
					
					case "changeUsers":
						//data.changeUsers = new Array();
						//更新用户数据及形象表现
						for (var k:int = 0; k < objdata.changeUsers.length; k++) 
						{
							new ChangeUserDataCommand(objdata.changeUsers[k]);
						}
					break;
					
					case "refreshScene":
						DisplayManager.showSysMsg("更新中,请稍等");
						setTimeout(refreshScene, 1000, objdata.refreshScene);
						
					break;
					
					case "friends":
						var friends:Array = new Array();
						var tmpfriends:UserVo;
						objdata.friends.sortOn("exp", Array.NUMERIC | Array.DESCENDING);
						for (i= 0; i < objdata.friends.length; i++) 
						{
							tmpfriends = new UserVo().setData(objdata.friends[i]) as UserVo;
							tmpfriends.index = i;
							friends.push(tmpfriends);
						}
						data.friends = friends;
						tmdm.friends = friends;
						EventManager.getInstance().dispatchEvent(new FriendsEvent(FriendsEvent.FRIENDS_DATA_COMPLETE));
					break;
						
					case "addItems":
						var len:int = objdata.addItems.length;
						data.addItems = [];
						data.addItems.length = len;
						for (i = 0; i < len; i++) 
						{
							var itemVo:ItemVo = new ItemVo();
							itemVo.setData( { cid:objdata.addItems[i][0], num:objdata.addItems[i][1], id:objdata.addItems[i][2], wear:objdata.addItems[i][3] } );
							tmdm.itemData.addItem(itemVo);
							data.addItems[i] = itemVo;
						}
						//通知道具箱更新显示
						EventManager.getInstance().dispatchEvent(new DataManagerEvent(DataManagerEvent.ITEMS_CHANGE));
						break;
						
					case "removeItems":
						len = objdata.removeItems.length;
						data.removeItems = [];
						data.removeItems.length = len;
						for (i = 0; i < len; i++) 
						{
							itemVo = new ItemVo();
							itemVo.setData( { cid:objdata.removeItems[i][0], num:objdata.removeItems[i][1], id:objdata.removeItems[i][2], wear:objdata.removeItems[i][3] } );
							data.removeItems[i] = itemVo;
							tmdm.itemData.removeItem(itemVo);
						}
						EventManager.getInstance().dispatchEvent(new DataManagerEvent(DataManagerEvent.ITEMS_CHANGE));
					break;
					
					case "changeTasks":
						EventManager.dispatchEvent(new TaskEvent(TaskEvent.CHANGE_TASKS_AUTO_EXECUTE, false, false, objdata.changeTasks));
						//var tmpTaskChangeEvent:TaskEvent = new TaskEvent(TaskEvent.TASKS_STATE_CHANGE);
						//
						//data.changeTasks = new Array();
						//data.addTasks = new Array();
						//var tmpchangetask:TaskVo;
						//for (i = 0; i < objdata.changeTasks.length; i++) 
						//{
							//tmpchangetask = new TaskVo().setValue(objdata.changeTasks[i]);
							//更新任务数据,并判断是否新任务
							//if (DataManager.getInstance().setTask(tmpchangetask)) {
								//data.addTasks.push(tmpchangetask);
								//tmpTaskChangeEvent.addTasks.push(tmpchangetask);
							//}else {
								//data.changeTasks.push(tmpchangetask);
								//tmpTaskChangeEvent.changeTasks.push(tmpchangetask);
							//}
						//}
						//
						//EventManager.getInstance().dispatchEvent(tmpTaskChangeEvent);
					break;
					
					case "results":
						data.results = new Array();
						var tmpresult:ResultVo;
						for (i = 0; i < objdata.results.length; i++) 
						{
							tmpresult = new ResultVo().setValue(objdata.results[i]);
							data.results.push(tmpresult);
						}
					break;
					
					case "scene":
						//赋值userVo
						if (objdata.scene.user)
						{
							user_vo =  new UserVo().setData(objdata.scene.user) as UserVo;
							tmdm.curSceneUser = user_vo;
							tmdm.curSceneUser.currentSceneId = objdata.scene.sceneId;
							if (tmdm.currentUser) 
							{
								if (user_vo.uid==tmdm.currentUser.uid) 
								{
									tmdm.currentUser = user_vo;
								}
							}
							else tmdm.currentUser = user_vo;
						}
						
						tmdm.currentUser.currentSceneId = objdata.scene.sceneId;
						var sceneClassVo:SceneClassVo = DataManager.getInstance().getSceneClassById(objdata.scene.sceneId);
						
						
						IsoUtil.isoStartX = sceneClassVo.isoStartX?sceneClassVo.isoStartX:0;
						IsoUtil.isoStartZ = sceneClassVo.isoStartZ?sceneClassVo.isoStartZ:0;
						
						//场景建筑数据
						tmdm.decorList = new Object;
						
						var decorList:Array;
						
						if (sceneClassVo.decorList) decorList = sceneClassVo.decorList.concat();
						else decorList = new Array;
						
						if (objdata.scene.decorList)
						{
							for (i = 0; i < objdata.scene.decorList.length; i++) decorList.push(objdata.scene.decorList[i]);
						}
						
						if (decorList.length>0) 
						{
							for ( i= 0; i < decorList.length; i++) 
							{
								decor_vo =  new DecorVo();
								decor_vo.setValue(decorList[i]);
								decor_vo.setData(tmdm.itemData.getItemClass(decor_vo.cid));
								//分类
								if (!DataManager.getInstance().decorList[decor_vo.type2]) {
									tmdm.decorList[decor_vo.type2] = new Array();
								}
								tmdm.decorList[decor_vo.type2].push(decor_vo);
							}
						}
						
						//地板
						tmdm.floorList = objdata.scene.floorList ? objdata.scene.floorList : sceneClassVo.floorList;
						tmdm.floorList2 = objdata.scene.floorList2 ? objdata.scene.floorList2 : sceneClassVo.floorList2;
						//墙壁
						tmdm.wallList = objdata.scene.wallList ? objdata.scene.wallList : sceneClassVo.wallList;
						
						//2011.11.11--------------------------------------
						tmdm.portalList = new Vector.<PortalVo>();
						if (sceneClassVo.portalList)
						{
							for (i = 0; i < sceneClassVo.portalList.length; i++)
							{
								var portalVo:PortalVo = new PortalVo;
								portalVo.setData(sceneClassVo.portalList[i]);
								var portalClassVo:BaseItemClassVo = tmdm.itemData.getItemClass(portalVo.cid);
								portalVo.setData(portalClassVo);
								tmdm.portalList.push(portalVo);
							}
						}
						
						tmdm.monsterList = new Vector.<MonsterVo>();
						if (sceneClassVo.monsterList)
						{
							for (i = 0; i < sceneClassVo.monsterList.length; i++)
							{
								var monsterVo:MonsterVo = new MonsterVo;
								monsterVo.setData(sceneClassVo.monsterList[i]);
								monsterVo.setData(tmdm.getMonsterClassByCid(monsterVo.cid));
								for (j = 0; j < objdata.scene.monsterList.length; j++)
								{
									if (objdata.scene.monsterList[j].id == monsterVo.id)
									{
										monsterVo.setData(objdata.scene.monsterList[j]);
										if (monsterVo.currentHp > 0) tmdm.monsterList.push(monsterVo);
										break;
									}
								}
							}
						}
						
						tmdm.mineList = new Vector.<MineVo>();
						if (sceneClassVo.mineList)
						{
							for (i = 0; i < sceneClassVo.mineList.length; i++)
							{
								var mineVo:MineVo = new MineVo;
								mineVo.setData(sceneClassVo.mineList[i]);
								mineVo.setData(tmdm.getMineClassByCid(mineVo.cid));
								for (j = 0; j < objdata.scene.mineList.length; j++)
								{
									if (objdata.scene.mineList[j].id == mineVo.id)
									{
										mineVo.setData(objdata.scene.mineList[j]);
										if (mineVo.currentHp > 0) tmdm.mineList.push(mineVo);
										break;
									}
								}
							}
						}
						
						//npc
						tmdm.npcList = new Vector.<NpcVo>();
						var tmpnpc:NpcVo;
						if (sceneClassVo.npcList)
						{
							for (i = 0; i < sceneClassVo.npcList.length; i++) 
							{
								
								tmpnpc = new NpcVo().setValue(sceneClassVo.npcList[i]);
								tmdm.npcList.push(tmpnpc);
							}
						}
						if (objdata.scene.npcList) 
						{
							/*for (var l:int = 0; l < objdata.scene.npcList.length; l++) 
							{
								tmpnpc = new NpcVo().setValue(objdata.scene.npcList[l]);
								tmdm.npcList.push(tmpnpc);
							}*/
							for (i = 0; i < objdata.scene.npcList.length; i++) 
							{
								for (var m:int = 0; m < tmdm.npcList.length; m++) 
								{
									if (tmdm.npcList[m].id==objdata.scene.npcList[i].id) 
									{
										tmdm.npcList[m].chatState = objdata.scene.npcList[i].chatState;
										tmdm.npcList[m].chats = objdata.scene.npcList[i].chats;
										tmdm.npcList[m].chatId = objdata.scene.npcList[i].chatId;
										break;
									}
								}
							}
						}
						//进入场景同时npc对话改变时，需整合npc数据
						if (objdata.npcListChange) 
						{
							for (i = 0; i < objdata.npcListChange.length; i++) 
							{
								for (j = 0; j < tmdm.npcList.length; j++) 
								{
									if (tmdm.npcList[j].id == int(objdata.npcListChange[i].id)) 
									{
										tmdm.npcList[j].chatState = objdata.npcListChange[i].chatState;
										tmdm.npcList[j].chats = objdata.npcListChange[i].chats;
										tmdm.npcList[j].chatId = objdata.npcListChange[i].chatId;
										
										break;
									}
								}
							}
							objdata.npcListChange = null;
						}
						
						
						//好友的佣兵
						//或以后自己家也可以有
						tmdm.roleList = new Vector.<RoleVo>();
						if (objdata.scene.sceneRoles) 
						{
							var tmprolevo:RoleVo;
							var tmproles:Array = objdata.scene.sceneRoles;
							for (var l:int = 0; l < tmproles.length; l++) 
							{
								tmprolevo = new RoleVo().setData(tmproles[l]) as RoleVo;
								tmdm.roleList.push(tmprolevo);
							}
						}
						
						//剩余刷新时间
						DataManager.getInstance().setVar("sceneRefreshTime", objdata.scene.tm);
						
						//-------------------------------------------------
						
					break;
					
					case "diarys":
						if (!tmdm.diarys) 
						{
							tmdm.diarys = new Array();
						}
						for (i = 0; i < objdata.diarys.length; i++) 
						{
							tmdm.diarys.push(new DiaryVo().setData(objdata.diarys[i]));
						}
						//tmdm.diarys.sortOn("createTime");
						
						EventManager.getInstance().dispatchEvent(new DiaryEvent(DiaryEvent.DIARY_ADDED));
					break;
					
					case "story":
						StoryPlayCommand.getInstance().story = new StoryVo().setData(objdata.story) as StoryVo; //记录剧情
					break;
					
					case "satisfaction" :
						var satisfaction:int = DataManager.getInstance().currentUser.satisfaction;
						satisfaction += objdata.satisfaction;
						if (satisfaction < 0) satisfaction = 0;
						DataManager.getInstance().currentUser.satisfaction = satisfaction;
						EventManager.dispatchEvent(new DataManagerEvent(DataManagerEvent.USERINFO_CHANGE));
						break;
						
					//图鉴的自动处理
					case "addillustrated":
					    var arr :Array = DataManager.getInstance().illustratedData.illustratedHandbookInit;
						var Illvo:IllustrationsVo = new IllustrationsVo();
					    Illvo.setData(objdata.addillustrated);
					    Illvo.base = DataManager.getInstance().illustratedData.getIllustrationsClassVo(Illvo.cid);
						DataManager.getInstance().illustratedData.illustratedHandbookInit.push(Illvo);
						DataManager.getInstance().isNewIllus = 1;
					break;	
					
					case "orders" :
						DataManager.getInstance().orderData.setOrderList(objdata.orders, false);
						break;
					
					case "rolesChange":
						if (objdata.rolesChange.length>0) 
						{
							tmdm.roleData.changeRoles(objdata.rolesChange);
						}
					break;
					
					case "removeRoles":
						if (objdata.removeRoles.length>0) 
						{
							tmdm.roleData.removeRoles(objdata.removeRoles);
						}
					break;
					
					case "villageBuildUpgrade":
						DataManager.getInstance().curSceneUser.viliageInfo[objdata.villageBuildUpgrade.id] = objdata.villageBuildUpgrade.level;
						(tmdm.worldState.world as MagicWorld).refreshSpecialBuild(objdata.villageBuildUpgrade.id);
					break;
					
					case "occChange":
						switch (objdata.occChange.type) 
						{
							case 1:
								DataManager.getInstance().curSceneUser.ownerUid = objdata.occChange.ownerUid;
								DataManager.getInstance().curSceneUser.ownerEndTime = objdata.occChange.ownerEndTime;
								DataManager.getInstance().curSceneUser.ownerAwardTime = objdata.occChange.ownerAwardTime;
								DataManager.getInstance().curSceneUser.ownerFace = objdata.occChange.ownerFace;
								DataManager.getInstance().curSceneUser.ownerBuildId = objdata.occChange.ownerBuildId;
								
								
								var build:SpecialBuild = (DataManager.getInstance().worldState.world as MagicWorld).getItemById(objdata.occChange.ownerBuildId) as SpecialBuild;
								if (build) 
								{
									build.initTip();
									CameraControl.getInstance().centerTweenTo(build.view.container, DisplayManager.camera);
								}
							break;
							
							case 3:
							case 2:
								DataManager.getInstance().curSceneUser.ownerUid = 0;
								DataManager.getInstance().curSceneUser.ownerEndTime = 0;
								DataManager.getInstance().curSceneUser.ownerAwardTime = 0;
								DataManager.getInstance().curSceneUser.ownerFace = "";
								DataManager.getInstance().curSceneUser.ownerBuildId = 0;
								
								var builds:Array = (DataManager.getInstance().worldState.world as MagicWorld).items;
								for (var n:int = 0; n < builds.length; n++) 
								{
									if (builds[n] is SpecialBuild) 
									{
										builds[n].initTip();
									}
								}
							break;
						}
						
					break;
					
				case "newWorldMap":
					    if (objdata.newWorldMap)
						{
							var worldobj:Object = new Object();
							worldobj.cid = objdata.newWorldMap;
							worldobj.newUnlock = 1;
							worldobj.newOpen = 0;
							
						    DataManager.getInstance().worldData.worldmapInitData.curOpenScene.push(worldobj);							
							DataManager.getInstance().isNewWorldMap = 1;
							DisplayManager.showSysMsg(DataManager.getInstance().worldData.getWorldMapClassCidVo(objdata.newWorldMap).name);								
						}
						else
						{
							DataManager.getInstance().isNewWorldMap = 0;
						}
					
					break;
					
					case "hasNewDiary":
						DataManager.getInstance().isNewDiary = 1;
				    break;
					
					case "showSysMsg":
						DisplayManager.showSysMsg(objdata.showSysMsg);
					break;
					
					case "showNewItem":
						DisplayManager.showNewItems(objdata.showNewItem);
					break;
					
				}
			}
			
			if (objdata.npcListChange) 
			{
				var world:MagicWorld = DataManager.getInstance().worldState.world as MagicWorld;
				var tmp:NpcVo;
				for (i = 0; i < objdata.npcListChange.length; i++) 
				{
					
					for (j = 0; j < world.bigSceneView.npcList.length; j++) 
					{
						tmp = (world.bigSceneView.npcList[j] as NpcView).npcvo;
						if (tmp.id==int(objdata.npcListChange[i].id)) 
						{
							tmp.chatState = objdata.npcListChange[i].chatState;
							tmp.chats = objdata.npcListChange[i].chats;
							tmp.chatId = objdata.npcListChange[i].chatId;
							(world.bigSceneView.npcList[j] as NpcView).initPaoIcon();
							break;
						}
					}
				}
				
			}
			
			// 替代loadComplete函数
			if (loadCompleteHandler != null) loadCompleteHandler();
			
			if(playStory) StoryPlayCommand.getInstance().checkAndPlay();
		}
		
		
		private function refreshScene(scenedata:Object):void {
			var tmdm:DataManager = DataManager.getInstance();
			
			//清除场景数据
			tmdm.decorList = new Object();
			tmdm.floorList = [];
			tmdm.floorList2 = [];
			tmdm.wallList = [];
			
			var i:int;
			var j:int;
			var decor_vo:DecorVo;
			var user_vo:UserVo;
			
			//场景建筑数据
			if (scenedata.decorList) 
			{
				for ( i= 0; i < scenedata.decorList.length; i++) 
				{
					decor_vo =  new DecorVo();
					decor_vo.setValue(scenedata.decorList[i]);
					//分类
					if (!DataManager.getInstance().decorList[decor_vo.type2]) {
						tmdm.decorList[decor_vo.type2] = new Array();
					}
					tmdm.decorList[decor_vo.type2].push(decor_vo);
				}
			}
			
			//地板
			tmdm.floorList = scenedata.floorList;
			tmdm.floorList2 = scenedata.floorList2;
			//墙壁
			tmdm.wallList = scenedata.wallList;
			
			//赋值userVo
			user_vo =  new UserVo().setData(scenedata.user) as UserVo;
			tmdm.curSceneUser = user_vo;
			if (tmdm.currentUser) 
			{
				if (user_vo.uid==tmdm.currentUser.uid) 
				{
					tmdm.currentUser = user_vo;
				}
			}
			
			//清除场景
			DataManager.getInstance().worldState.world.clear();
			//设置数据重新创建场景
			var world_data:Object = new Object();
			world_data['decorList'] = DataManager.getInstance().decorList;
			world_data['floorList'] = DataManager.getInstance().floorList;
			world_data['floorList2'] = DataManager.getInstance().floorList2;
			world_data['wallList'] = DataManager.getInstance().wallList;
			world_data['userInfo'] = DataManager.getInstance().curSceneUser;
			DataManager.getInstance().worldState.world.create(world_data, true);
		}
		
		override public function commandComplete():void
		{
			
			if (callBack!=null) 
			{
				if (data.result) 
				{
					if (data.result.isSuccess) 
					{
						callBack.call();
					}
				}
			}
			dispatchEvent(new Event(Event.COMPLETE));
		}
		
	}

}