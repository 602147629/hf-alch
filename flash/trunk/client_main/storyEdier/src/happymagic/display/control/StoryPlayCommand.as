package happymagic.display.control 
{
	import com.friendsofed.isometric.Point3D;
	import flash.display.SimpleButton;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.utils.setTimeout;
	import happyfish.events.ModuleEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.ModuleManager;
	import happyfish.scene.camera.CameraControl;
	import happyfish.scene.camera.MovieMaskView;
	import happyfish.scene.fog.FogManager;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.personAction.PersonActionVo;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.WorldState;
	import happyfish.utils.display.CameraSharkControl;
	import happyfish.utils.display.McShower;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.ui.AwardResultView;
	import happymagic.display.view.ui.personMsg.PersonMsgManager;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.GetSceneClassCommand;
	import happymagic.model.command.MoveSceneCommand;
	import happymagic.model.command.ShowStoryCommand;
	import happymagic.model.vo.AvatarVo;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.PortalVo;
	import happymagic.model.vo.SceneClassVo;
	import happymagic.model.vo.StoryActionVo;
	import happymagic.model.vo.StoryVo;
	import happymagic.scene.world.bigScene.StoryPersonView;
	import happymagic.scene.world.control.AvatarCommand;
	import happymagic.scene.world.control.ChangeSceneCommand;
	import happymagic.scene.world.grid.person.Player;
	import happymagic.scene.world.MagicWorld;
	/**
	 * ...
	 * @author slamjj
	 */
	public class StoryPlayCommand 
	{
		public var story:StoryVo;
		public var storyId:int; //连续触发时 要触发的剧情的ID
 		private var npcs:Object;
		private var needInitNpcNum:uint;
		private var currentActionIndex:uint;
		private var _worldState:WorldState;
		private var beDarkMv:BeDarkMv;
		private var skipBtn:SimpleButton;
		private var killed:Boolean;
		
		private var posInOriScene:Point3D; //主角在上一个场景中的位置
		
		private static var _instance:StoryPlayCommand;
		public static function getInstance():StoryPlayCommand
		{
			if (!_instance) _instance = new StoryPlayCommand;
			return _instance;
		}
		
		public function StoryPlayCommand()
		{
			beDarkMv = new BeDarkMv;
			beDarkMv.mouseChildren = beDarkMv.mouseEnabled = false;
		}
		
		public function play(storyVo:StoryVo):void
		{
			story = storyVo; //记录剧情
			checkAndPlay();
		}
		
		public function checkAndPlay(e:Event = null):void
		{
			if (e) e.target.removeEventListener(SceneEvent.SCENE_COMPLETE, checkAndPlay);
			
			if (storyId)
			{
				new ShowStoryCommand(storyId);
				storyId = 0;
			}
			else if (story)
			{
				if (story.actions.length == 0) //空剧情
				{
					new MoveSceneCommand().moveScene(story.endAt, 0, story.id); //直接跳转
					storyId = int(story.nextStoryId);
					story = null;
					return;
				}
				
				//记录一下主角在当前场景内的位置
				var storyData:Object = DataManager.getInstance().getVar("storyData");
				if (!storyData)
				{
					storyData = new Object;
					storyData.oriSceneId = DataManager.getInstance().currentUser.currentSceneId;
					var player:Player = (DataManager.getInstance().worldState.world as MagicWorld).player;
					if (player) 
					{
						storyData.posInOriScene = player.gridPos;
						DataManager.getInstance().setVar("storyData",storyData);
					}
					
				}
				
				//场景变暗
				_worldState = DataManager.getInstance().worldState;
				var sceneContainer:Sprite = _worldState.view.isoView;
				sceneContainer.addChild(beDarkMv);
				beDarkMv.gotoAndPlay(1);
				beDarkMv.addEventListener(Event.COMPLETE, darkComplete);
			}
		}
		
		private function darkComplete(e:Event):void
		{
			e.target.removeEventListener(Event.COMPLETE, darkComplete);
			
			if (story.sceneId == DataManager.getInstance().currentUser.currentSceneId) start();
			else
			{
				if (DataManager.getInstance().getSceneClassById(story.sceneId) == null)
				{
					var getSceneClassCommand:GetSceneClassCommand = new GetSceneClassCommand;
					getSceneClassCommand.addEventListener(Event.COMPLETE, prepareScene);
					getSceneClassCommand.getSceneClass(story.sceneId);
				}
				else prepareScene();
			}
		}
		
		private function prepareScene(event:Event = null):void
		{
			if(event) event.target.removeEventListener(Event.COMPLETE, prepareScene);
			
			var tmdm:DataManager = DataManager.getInstance();
			var sceneClassVo:SceneClassVo = DataManager.getInstance().getSceneClassById(story.sceneId);
			
			IsoUtil.isoStartX = sceneClassVo.isoStartX?sceneClassVo.isoStartX:0;
			IsoUtil.isoStartZ = sceneClassVo.isoStartZ?sceneClassVo.isoStartZ:0;
			
			DataManager.getInstance().currentUser.currentSceneId = sceneClassVo.sceneId;
			
			//场景建筑数据
			tmdm.decorList = new Object;
			
			var decorList:Array;
			if (sceneClassVo.decorList) decorList = sceneClassVo.decorList.concat();
			else decorList = new Array;
			
			var i:int;
			
			if (decorList.length>0) 
			{
				for (i = 0; i < decorList.length; i++) 
				{
					var decor_vo:DecorVo =  new DecorVo();
					decor_vo.setValue(decorList[i]);
					decor_vo.setData(tmdm.itemData.getItemClass(decor_vo.cid));
					//分类
					if (!DataManager.getInstance().decorList[decor_vo.type2]) {
						tmdm.decorList[decor_vo.type2] = new Array();
					}
					tmdm.decorList[decor_vo.type2].push(decor_vo);
				}
			}
			
			tmdm.floorList = sceneClassVo.floorList; //地板
			tmdm.floorList2 = sceneClassVo.floorList2; //第二层地板
			tmdm.wallList = sceneClassVo.wallList; //墙壁
			
			//传送门
			if (sceneClassVo.portalList)
			{
				tmdm.portalList = new Vector.<PortalVo>();
				for (i = 0; i < sceneClassVo.portalList.length; i++)
				{
					var portalVo:PortalVo = new PortalVo;
					portalVo.setData(sceneClassVo.portalList[i]);
					var portalClassVo:BaseItemClassVo = tmdm.itemData.getItemClass(portalVo.cid);
					portalVo.setData(portalClassVo);
					tmdm.portalList.push(portalVo);
				}
			}
			else tmdm.portalList = null;
			
			tmdm.monsterList = null; //没有怪
			tmdm.mineList = null; //没有矿
			
			EventManager.getInstance().addEventListener(SceneEvent.SCENE_COMPLETE,start);
			new ChangeSceneCommand();
		}
		
		public function start(e:Event = null):void 
		{
			if (e) e.target.removeEventListener(SceneEvent.SCENE_COMPLETE, start);
			else //场景未变化
			{
				(DataManager.getInstance().worldState.world as MagicWorld).fogVisible = false; //隐藏迷雾
			}
			
			DisplayManager.sceneSprite.mouseChildren = false;
			DisplayManager.uiSprite.mouseChildren = false;
			
			//初始化角色
			needInitNpcNum = story.actorList.length;
			npcs = new Object();
			
			for (var i:int = 0; i < story.actorList.length; i++) 
			{
				if (int(story.actorList[i].id) == 0)
				{
					var player:Player = (DataManager.getInstance().worldState.world as MagicWorld).player;
					story.actorList[i].className = player.asset.className; //主角的话 需设下形象
					story.actorList[i].name = DataManager.getInstance().currentUser.name;
					var avatarId:uint = DataManager.getInstance().currentUser.avatar;
					var avataVo:AvatarVo = DataManager.getInstance().getAvatarVo(avatarId);
					story.actorList[i].head = avataVo.face;
				}
				var npc:StoryPersonView = new StoryPersonView(story.actorList[i], DataManager.getInstance().worldState, initNpc_callback);
				_worldState.world.addItem(npc);
				npcs[story.actorList[i].id] = npc;
			}
		}
		
		private function initNpc_callback():void 
		{
			needInitNpcNum--;
			if (needInitNpcNum <= 0)
			{
				DisplayManager.uiSprite.visible = false; //隐藏UI	
				(DataManager.getInstance().worldState.world as MagicWorld).hidePlayer(); //隐藏所有npc与学生
				
				beDarkMv.gotoAndPlay(31);
				
				setTimeout(startPlay,500);
			}
		}
		
		private function startPlay():void
		{
			//显示SKIP按钮
			skipBtn = new storySkipBtn();
			DisplayManager.storyUiSprite.addChild(skipBtn);
			skipBtn.addEventListener(MouseEvent.CLICK, storyComplete);
			
			currentActionIndex = 0;
			takeAction(story.actions[currentActionIndex]);
		}
		
		private function takeAction(action:StoryActionVo):void
		{
			var npc:StoryPersonView = npcs[action.npcId];
			
			if (action.x != -1 && action.y != -1) //如果需要改变位置
			{
				if (action.immediately) //立即把NPC移动到指定位置
				{
					npc.setPos(new Point3D(action.x, 0, action.y));
					if (action.faceX || action.faceY) 
					{
						npc.faceTowardsSpace(new Point3D(action.faceX, 0, action.faceY));
						npc.stopAnimation(Person.MOVE);
					}
					nextAction(); //执行下一步
				}
				else //如果需要移动
				{
					if (action.wait) //需要等待此步完成
					{
						npc.addCommand(new AvatarCommand(new Point3D(action.x, 0, action.y), nextAction,new Point3D(action.faceX,0,action.faceY)));
					}
					else //无需要等待此步完成立即执行下一条
					{
						npc.addCommand(new AvatarCommand(new Point3D(action.x, 0, action.y),null,new Point3D(action.faceX,0,action.faceY)));
						nextAction();
					}
				}
			}
			else if (action.content) //如果需要说话
			{
				//显示对话
				var content:String = action.content;
				content = content.replace("{player}", DataManager.getInstance().currentUser.name);
				if (action.wait) PersonMsgManager.getInstance().addStoryMsg(npc, npc.data["head"], npc.data["name"], content, action.chatTime, nextAction);
				else
				{
					PersonMsgManager.getInstance().addStoryMsg(npc, npc.data["head"], npc.data["name"], content, action.chatTime);
					nextAction();
				}
			}
			else if (action.hide) //如果要隐藏角色
			{
				clearNpc(npc.data.id);
				nextAction();
			}
			else if (action.actionLabel) //如果要播放角色动画
			{
				var command:AvatarCommand = new AvatarCommand;
				if (action.wait) command.setAction(action.actionLabel, 0, action.labelTimes, nextAction, action.toStop != 0);
				else
				{
					command.setAction(action.actionLabel, 0, action.labelTimes, null, action.toStop != 0);
					nextAction();
				}
				npc.addCommand(command);
			}
			else if (action.className && action.className != "") //如果要叠加动画
			{
				if (action.wait) new McShower(action.className, npc.asset, null, null, nextAction);
				else
				{
					new McShower(action.className, npc.asset);
					nextAction();
				}
			}
			else if (action.shockScreenTime != 0) //如果需要振屏
			{
				var view:Sprite = DataManager.getInstance().worldState.view;
				if (action.wait) CameraSharkControl.shark(view, 5, action.shockScreenTime, nextAction);
				else
				{
					CameraSharkControl.shark(view, 5, action.shockScreenTime);
					nextAction();
				}
			}
			
			checkCamera(action, npc); //镜头跟随
		}
		
		//显示奖励
		private function showAward():void
		{
			var awards:Array = new Array();
			
			if (story.coin)
			{
				awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_COIN, num:story.coin } ));
			}
			if (story.gem)
			{
				awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_GEM, num:story.gem } ));
			}
			if (story.items)
			{
				for (var i:int = 0; i < story.items.length; i++) 
				{
					awards.push(new ConditionVo().setData( { type:ConditionType.ITEM, id:story.items[i][0], num:story.items[i][1] } ));
				}
			}
			
			if (awards.length == 0) return;
			
			var awardwin:AwardResultView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_AWARD_RESULT, ModuleDict.MODULE_AWARD_RESULT_CLASS,true) as AwardResultView;
			awardwin.setData( { name:LocaleWords.getInstance().getWord("awardTile"), awards:awards } );
			DisplayManager.uiSprite.setBg(awardwin);
		}
		
		//镜头跟随
		private function checkCamera(action:StoryActionVo, npc:StoryPersonView):void
		{
			if (!action.camera) return;
			CameraControl.getInstance().followTarget(npc.asset, _worldState.view.isoView.camera);
		}
		
		//执行下一个动作
		private function nextAction():void
		{	
			if (killed || !story) return;
			
			currentActionIndex++;
			if (currentActionIndex <= story.actions.length-1) 
			{
				takeAction(story.actions[currentActionIndex]);
			}
			else storyComplete();
		}
		
		/**
		 * 剧情完成
		 */
		private function storyComplete(e:MouseEvent = null):void
		{
			if (story.nextStoryId != 0) //连续触发
			{
				new ShowStoryCommand(story.nextStoryId);
				clear();
			}
			else
			{
				showAward(); //显示奖励
				
				var sceneContainer:Sprite = _worldState.view.isoView;
				sceneContainer.addChild(beDarkMv);
				beDarkMv.gotoAndPlay(1);
				beDarkMv.addEventListener(Event.COMPLETE, closeMovieMask_complete);
			}
		}
		
		private function closeMovieMask_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, closeMovieMask_complete);
			
			backToTheOriWorld();
			clear(); //清除command
			
			DisplayManager.uiSprite.visible = true; //显示UI和主角
			(DataManager.getInstance().worldState.world as MagicWorld).showPlayer(); //显示所有npc与学生
			
			beDarkMv.gotoAndPlay(31);
		}
		
		private function clearNpc(name:String):void
		{
			var item:StoryPersonView = npcs[name];
			item.remove();
			//npcs[name] = null;
		}
		
		private function clear():void 
		{
			skipBtn.removeEventListener(MouseEvent.CLICK, storyComplete);
			skipBtn.parent.removeChild(skipBtn);
			skipBtn = null;
			
			//去除所有人物
			for (var name:String in npcs) 
			{
				var item:StoryPersonView = npcs[name];
				if(item) item.remove();
			}
			
			story = null;
			npcs = null;
		}
		
		private function backToTheOriWorld():void
		{
			DisplayManager.sceneSprite.mouseChildren = DisplayManager.uiSprite.mouseChildren = true;
			
			var storyData:Object = DataManager.getInstance().getVar("storyData");
			
			if (story.endAt != story.sceneId) //剧情的表现场景不是剧情结束后的场景
			{
				var command:MoveSceneCommand = new MoveSceneCommand;
				command.moveScene(story.endAt, 0, story.id);
				
				//如果角色会回到之前所在的场景 则重设其位置
				if (story.endAt == storyData.oriSceneId)
				{
					EventManager.getInstance().addEventListener(SceneEvent.SCENE_COMPLETE, onSceneComplete);
					posInOriScene = storyData.posInOriScene;
				}
			}
			else //场景没有变化
			{
				if (npcs[0]) //如果演员中有演主角的
				{
					var player:Player = (DataManager.getInstance().worldState.world as MagicWorld).player;
					player.setPos((npcs[0] as StoryPersonView).gridPos); //将真正的主角移到演员的位置
				}
				var world:MagicWorld = (DataManager.getInstance().worldState.world as MagicWorld);
				world.centerCameraToPlayer(); //摄像机移动到主角
				world.fogVisible = true; //显示迷雾
			}
			
			DataManager.getInstance().setVar("storyData", null);
		}
		
		private function onSceneComplete(event:SceneEvent):void
		{
			event.target.removeEventListener(SceneEvent.SCENE_COMPLETE, onSceneComplete);
			
			var player:Player = (DataManager.getInstance().worldState.world as MagicWorld).player;
			player.setPos(posInOriScene); //重设主角的位置
			FogManager.getInstance().updateExplorer("0", player.gridPos.x, player.gridPos.z); //在迷雾管理器中更新
			
			var world:MagicWorld = (DataManager.getInstance().worldState.world as MagicWorld);
			world.centerCameraToPlayer(); //摄像机移动到主角
			world.fogVisible = true; //显示迷雾
		}
		
	}

}