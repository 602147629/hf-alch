package happymagic.scene.world.control 
{
	import com.brokenfunction.json.decodeJson;
	import com.friendsofed.isometric.IsoUtils;
	import com.friendsofed.isometric.Point3D;
	import com.greensock.TweenMax;
	import flash.display.DisplayObjectContainer;
	import flash.display.Stage;
	import flash.events.Event;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.utils.setTimeout;
	import happyfish.display.view.IconView;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.manager.module.AlginType;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.mouse.MouseManager;
	import happyfish.manager.ShareObjectManager;
	import happyfish.scene.astar.Node;
	import happyfish.scene.camera.CameraControl;
	import happyfish.scene.camera.MovieMaskView;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.grid.BaseItem;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.WorldState;
	import happyfish.utils.CustomTools;
	import happyfish.utils.display.CameraSharkControl;
	import happyfish.utils.display.McShower;
	import happyfish.utils.SysTracer;
	import happymagic.display.control.ItemEnoughCheckCommand;
	import happymagic.display.control.MagicEnoughCheckCommand;
	import happymagic.display.control.NpcChatCommand;
	import happymagic.display.view.dungeon.DungeonTip;
	import happymagic.display.view.dungeon.MiningHand;
	import happymagic.events.TaskEvent;
	import happymagic.model.vo.NpcClassVo;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.FriendActionType;
	import happymagic.scene.world.grid.item.SpecialBuild;
//	import happymagic.display.view.dungeon.DungeonFeedView;
	import happymagic.display.view.ModuleDict;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.display.view.task.TaskInfoView;
	import happymagic.display.view.ui.AwardResultView;
	import happymagic.display.view.ui.personMsg.PersonMsgManager;
	import happymagic.events.ActionStepEvent;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.events.SysMsgEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.mouse.MagicMouseIconType;
	import happymagic.model.command.CompleteChatCommand;
	import happymagic.model.command.MiningCommand;
	import happymagic.model.command.MoveSceneCommand;
	import happymagic.model.command.TestCommand;
	import happymagic.model.vo.ConditionType;
	import happymagic.model.vo.ConditionVo;
	import happymagic.model.vo.DecorVo;
	import happymagic.model.vo.ItemVo;
	import happymagic.model.vo.NpcVo;
	import happymagic.model.vo.ResultVo;
	import happymagic.scene.world.award.AwardItemManager;
	import happymagic.scene.world.award.AwardItemView;
	import happymagic.scene.world.award.AwardType;
	import happymagic.scene.world.bigScene.NpcView;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.item.Portal;
	import happymagic.scene.world.grid.person.Mine;
	import happymagic.scene.world.grid.person.Monster;
	import happymagic.scene.world.grid.person.Player;
	import happymagic.scene.world.MagicWorld;
	import happymagic.utils.RequestQueue;
	/**
	 * ...
	 * @author slam
	 */
	public class MouseDefaultAction extends MouseMagicAction
	{
		private var targetPortal:Portal;
		private var _mainPlayer:Player; //主角
		private var revoltTip:SpecialBuildingRevoltTip = new SpecialBuildingRevoltTip;
		
		public function MouseDefaultAction($state:WorldState, $stack_flg:Boolean = false) 
		{
			super($state, $stack_flg);
			if (DataManager.getInstance().isDiying) 
			{
				(state.world as MagicWorld).leaveEditMode();
			}
		}
		
		private function get mainPlayer():Player
		{
			if(!_mainPlayer) _mainPlayer = (state.world as MagicWorld).player;
			return _mainPlayer;
		}
		
		public function onSpecialBuildClick(event:GameMouseEvent):void {
			skipBackgroundClick = true;
			
			var dm:DataManager = DataManager.getInstance();
			if (dm.curSceneUser.ownerEndTime!=0 && dm.curSceneUser.ownerBuildId==event.item.data.id) 
			{
				//反抗
				DataManager.getInstance().setVar("tigerMachineData", { type:FriendActionType.RESIST,enemyUid:dm.curSceneUser.uid, enemyName:dm.curSceneUser.ownerName,enemyFace:dm.curSceneUser.ownerFace, buildId:event.item.data.id } );
				ActModuleManager.getInstance().addActModule(DataManager.getInstance().getActByName("TigerMachine"));
				
			}else if (event.item.data.id==dm.gameSetting.homeBuildId) 
			{
				new MoveSceneCommand().moveScene(dm.gameSetting.homeSceneId);
			}else if(event.item.data.id==dm.gameSetting.smithyBuildId)
			{
				ActModuleManager.getInstance().addActModule(dm.getActByName("fixEquip"));
			}else if (dm.gameSetting.barBuildIds.indexOf(int(event.item.data.id))>-1) 
			{
				ActModuleManager.getInstance().addActModule(dm.getActByName("hire"));
			}
		}
		
		public function onSpecialBuildOver(event:GameMouseEvent):void
		{
			event.item.showGlow();
			
			event.item.showName((event.item as SpecialBuild).decorVo.name);
			
			//判断是否被侵占建筑
			var curSceneUser:UserVo = DataManager.getInstance().curSceneUser;
			if (event.item.data.id == curSceneUser.ownerBuildId 
				&& curSceneUser.ownerUid.toString()!=DataManager.getInstance().currentUser.uid
			)
			{
				event.item.view.container.addChild(revoltTip);
			}
		}
		
		public function onSpecialBuildOut(event:GameMouseEvent):void
		{
			event.item.hideGlow();
			event.item.hideName();
			
			//判断是否被侵占建筑
			var curSceneUser:UserVo = DataManager.getInstance().curSceneUser;
			if (event.item.data.id == curSceneUser.ownerBuildId 
				&& curSceneUser.ownerUid.toString()!=DataManager.getInstance().currentUser.uid
			)
			{
				if(revoltTip.parent) revoltTip.parent.removeChild(revoltTip);
			}
		}
		
        override public function onDecorOver(event:GameMouseEvent) : void
        {
            return;
        }

        override public function onDecorClick(event:GameMouseEvent) : void
        {
            return;
        }

        override public function onDecorOut(event:GameMouseEvent) : void
        {
            return;
        }
		
        override public function onDeskOver(event:GameMouseEvent) : void
        {
            return;
        }
		
		override public function onMassesClick(event:GameMouseEvent):void 
		{
			//CameraControl.getInstance().followTarget(event.item.asset, state.view.isoView.camera);
			//CameraControl.getInstance().centerTweenTo(event.item.asset, state.view.isoView.camera);
		}
		
		override public function onRoomUpItemClick(event:GameMouseEvent):void {
			//屏蔽地板点击事件
			skipBackgroundClick = true;
				
			//var roomUpView:RoomUpView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_ROOMUP, ModuleDict.MODULE_ROOMUP_CLASS,false, AlginType.CENTER) as RoomUpView;
			//roomUpView.setData(DataManager.getInstance().roomSizeClass);
			//DisplayManager.uiSprite.setBg(roomUpView);
		}
		
		override public function onRoomUpItemOver(event:GameMouseEvent):void {
			event.item.showGlow();
		}
		
		override public function onRoomUpItemOut(event:GameMouseEvent):void {
			event.item.hideGlow();
		}
		
		override public function onAwardItemOver(event:GameMouseEvent):void 
		{
			//skipBackgroundClick = true;
			var award:AwardItemView = event.item as AwardItemView;
			award.out();
		}
		
		override public function onAwardItemClick(event:GameMouseEvent):void {
			
			//skipBackgroundClick = true;
			//var award:AwardItemView = event.item as AwardItemView;
			//award.out();
		}
		
		//点击地板
		override public function onBackgroundClick(event:GameMouseEvent):void 
		{
			if (mainPlayer.isBusy) return;
			
			if (skipBackgroundClick) 
			{
				skipBackgroundClick = false;
				DataManager.getInstance().isDraging = false;
				return;
			}
			//var flag:Boolean = DataManager.getInstance().isDraging;
			if (!DataManager.getInstance().isDraging)
			{
				mainPlayer.clearAllCommand();
				(state.world as MagicWorld).clearMineClickTimes();
				mainPlayer.go();
			}
			//if (!DataManager.getInstance().isDraging && state.world.player) state.world.player.setPos(state.view.targetGrid());
			DataManager.getInstance().isDraging = false;
			
			
			//镜头移动测试
			//CameraControl.getInstance().centerTweenToPoint(new Point(state.view.stage.mouseX, state.view.stage.mouseY),
				//state.view.isoView.camera);
			
			//new TestCommand().test("data/award.txt");
			
			//var awards:Array = new Array();
			//awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_GEM, num:11 } ));
			//awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_COIN, num:11 } ));
			//awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_EXP, num:1341 } ));
			//awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_MP_MAX, num:131 } ));
			//awards.push(new ConditionVo().setData( { type:ConditionType.USER, id:ConditionType.USER_DESK_LIMIT, num:1 } ));
			
			//var awardwin:AwardResultView = DisplayManager.uiSprite.addModule(ModuleDict.MODULE_AWARD_RESULT, ModuleDict.MODULE_AWARD_RESULT_CLASS,true) as AwardResultView;
				//awardwin.setData( { name:"新手引导奖励", awards:awards } );
			
			//var p:Point = new Point(state.view.stage.mouseX, state.view.stage.mouseY);
			//var mvContainer:DisplayObjectContainer = state.world.player.view.container.parent;
			//var learnMv:McShower = new McShower(learnTransMv, mvContainer);
			//learnMv.changeRate(10, 71, 83);
			//
			//p = mvContainer.globalToLocal(p);
			//learnMv.x = p.x;
			//learnMv.y = p.y;
			
			
			//PersonMsgManager.getInstance().addMsg(state.world.player, "123233sasdfasdfasdfasdfasfasdfasfdasfdasfdasd443");
			
			//if (ShareObjectManager.getInstance().soundEffect) 
			//{
				//var tmpP:Point3D = state.view.targetGrid();
			//
				//var tmptype:int;
				//var tmpArr:Array = new Array();
				//var tmpNum:int;
				//for (var i:int = 0; i < 3; i++) 
				//{
					//tmptype = (Math.floor(Math.random() * 3) + 1);
					//tmpNum = CustomTools.customInt(13, 123);
					//tmpArr.push( { type:tmptype, num:tmpNum, point:tmpP } );
					//tmpArr.push( { type:2, num:tmpNum, point:tmpP } );
				//}
				//AwardItemManager.getInstance().addAwards(tmpArr);
			//}
			
			//var tmpP:Point3D = state.view.targetGrid();
			//var tmpArr:Array = new Array();
			//tmpArr.push( { type:1, num:18, point:tmpP } );
			//tmpArr.push( { type:3, num:18, point:tmpP } );
			//AwardItemManager.getInstance().addAwards(tmpArr);
			
			//var tmpP:Point3D = state.view.targetGrid();
			//var tmpArr:Array = new Array();
			//tmpArr.push( { type:AwardType.ITEM, num:1, id:8301,point:tmpP } );
			//AwardItemManager.getInstance().addAwards(tmpArr);
			
			//var tmpP:Point3D = state.view.targetGrid();
			//var tmpArr:Array = new Array();
			//tmpArr.push( { type:AwardType.DECOR, num:1, id:191008,point:tmpP } );
			//AwardItemManager.getInstance().addAwards(tmpArr);
			
			//var icon:IconView = new IconView(50, 50,new Rectangle(DisplayManager.uiSprite.mouseX,DisplayManager.uiSprite.mouseY),true);
			//DisplayManager.uiSprite.stage.addChild(icon);
			//icon.setData("decor.1.shuiyaojin");
			
			
			//EventManager.getInstance().showPiaoStr(1, "123");
			
			//var maskmv:MovieMaskView = new MovieMaskView();
			//maskmv.showMaskMv( DisplayManager.uiSprite, DisplayManager.uiSprite.stage.stageWidth,
					//DisplayManager.uiSprite.stage.stageHeight, 100,1);
		}
		
		override public function onDoorClick(event:GameMouseEvent) : void
        {
			skipBackgroundClick = true;
			//如果是门,显示tips
			if (event.item is Door) {
				var door:Door = event.item as Door;
				(state.world as MagicWorld).player.addCommand(new AvatarCommand(door.getOutIsoPosition(),goViliage));
			}
            return;
        }
		
		private function goViliage():void 
		{
			new MoveSceneCommand().moveScene(DataManager.getInstance().gameSetting.viliageSceneId);
		}
		
		/**
		 * 墙上道具over事件
		 * @param	event
		 */
        override public function onWallDecorOver(event:GameMouseEvent) : void
        {
			//如果是门,显示tips
			if (event.item is Door) {
				
				event.item.showGlow();
			}
            return;
        }
		/**
		 * 墙上道具out事件
		 * @param	event
		 */
        override public function onWallDecorOut(event:GameMouseEvent) : void
        {
			//如果是门,隐藏tips
			if (event.item is Door) {
				event.item.hideGlow();
			}
            return;
        }
		
		override public function onNpcOver(e:GameMouseEvent):void {
			e.item.showGlow();
			(e.item as NpcView).showName(e.item.data.name);
		}
		
		override public function onNpcOut(e:GameMouseEvent):void {
			e.item.hideGlow();
			(e.item as NpcView).hideName();
		}
		
		override public function onNpcClick(e:GameMouseEvent):void {
			//屏蔽地板点击事件
			skipBackgroundClick = true;
			var tmpcommand:AvatarCommand;
			var rect:Rectangle;
			var targetNode:Node;
			switch ((e.item as NpcView).state) 
			{
				case NpcView.STATE_NEWSTORY:
					rect = new Rectangle(e.item.x, e.item.z, 1, 1);
					targetNode = state.world.findCanWalkNodeFromRect(
						new Node((state.world as MagicWorld).player.x, (state.world as MagicWorld).player.z), rect);
					if (targetNode) {
						tmpcommand = new AvatarCommand();
						tmpcommand.setMovePos(new Point3D(targetNode.x,0, targetNode.y), null, npcChat, [e.item]);
						(state.world as MagicWorld).player.addCommand(tmpcommand);
					}
				break;
				
				case NpcView.STATE_TASK:
					var event:TaskEvent = new TaskEvent(TaskEvent.TASK_SHOW_ACCEPT_PANEL);
					event.id = (e.item as NpcView).curTask.id;
					EventManager.getInstance().dispatchEvent(event);
				break;
				
				case NpcView.STATE_STORY:
					rect = new Rectangle(e.item.x, e.item.z, 1, 1);
					targetNode = state.world.findCanWalkNodeFromRect(
						new Node((state.world as MagicWorld).player.x, (state.world as MagicWorld).player.z), rect);
					if (targetNode) {
						tmpcommand = new AvatarCommand();
						tmpcommand.setMovePos(new Point3D(targetNode.x,0, targetNode.y), null, npcChat, [e.item]);
						(state.world as MagicWorld).player.addCommand(tmpcommand);
					}
				break;
				
				case NpcView.STATE_OTHER:
					var npcvo:NpcVo = (e.item as NpcView).npcvo;
					var obj:Object;
					var curSceneType:int = DataManager.getInstance().curSceneType;
					switch (npcvo.clickType) 
					{
						case NpcClassVo.CLICKTYPE_MODULE:
							obj = decodeJson(npcvo.clickValue);
							if (obj.sceneType) 
							{
								if (obj.sceneType.indexOf(curSceneType)<=-1) 
								{
									break;
								}
							}
							DataManager.getInstance().setVar("npcClickValue", obj);
							DisplayManager.uiSprite.addModuleByVo(DataManager.getInstance().getModuleVo(obj.name));
						break;
						
						case NpcClassVo.CLICKTYPE_ACTMODULE:
							obj = decodeJson(npcvo.clickValue);
							if (obj.sceneType) 
							{
								if (obj.sceneType.indexOf(curSceneType)<=-1) 
								{
									break;
								}
							}
							DataManager.getInstance().setVar("npcClickValue", obj);
							ActModuleManager.getInstance().addActModule(DataManager.getInstance().getActByName(obj.actName));
						break;
					}
				break;
				
			}
		}
		
		private function npcChat(npc:NpcView):void {
			
			new NpcChatCommand(
						(state.world as MagicWorld).player, 
						npc as Person, decodeJson(npc.npcvo.chats), 
						npc.npcvo.chatState != 1
					);
		}
		
		//地下城内容--------------------------------------------------------
		//滑入传送门
		override public function onPortalOver(event:GameMouseEvent):void
		{
			if (mainPlayer.isBusy) return;
			event.item.showGlow();
		}
		
		//滑出传送门
		override public function onPortalOut(event:GameMouseEvent):void
		{
			event.item.hideGlow();
		}
		
		//点击传送门
		override public function onPortalClick(event:GameMouseEvent):void
		{
			if (mainPlayer.isBusy) return;
			skipBackgroundClick = true;
			
			var portal:Portal = event.item as Portal;
			targetPortal = portal;
			
			var startNode:Node = new Node(mainPlayer.data.x, mainPlayer.data.z);
			var rect:Rectangle = new Rectangle(portal.x, portal.z, portal.decorVo.sizeX, portal.decorVo.sizeZ);
			var targetNode:Node = state.world.findCanWalkNodeFromRect(startNode, rect);
			if (!targetNode) return;
			
			var target:Point3D = new Point3D(targetNode.x, 0, targetNode.y);
			var dir:Point3D = new Point3D(portal.x, 0 , portal.z);
			
			var command:AvatarCommand = new AvatarCommand(target, switchScene, dir);
			mainPlayer.addCommand(command);
		}
		
		//切换场景 发包
		private function switchScene():void
		{
			DataManager.getInstance().lastSceneId = DataManager.getInstance().currentUser.currentSceneId;
			
			var command:MoveSceneCommand = new MoveSceneCommand();
			command.moveScene(targetPortal.targetSceneId, int(targetPortal.decorVo.id));
			
			mainPlayer.clearAllCommand();
		}
		
		//滑入怪
		public function onMonsterOver(event:GameMouseEvent):void
		{
			if (mainPlayer.isBusy) return;
			
			event.item.showGlow();
			
			if (!DisplayManager.dungeonTip)
			{
				DisplayManager.dungeonTip = new DungeonTip;
				state.view.isoView.addChild(DisplayManager.dungeonTip);
			}
			var monster:Monster = event.item as Monster;
			monster.stopFiddle();
			DisplayManager.dungeonTip.show(monster);
			
			MouseManager.getInstance().setTmpIcon(MouseManager.getInstance().getMouseIcon(MagicMouseIconType.SWORD_HAND));
		}
		
		//滑出怪
		public function onMonsterOut(event:GameMouseEvent):void
		{
			event.item.hideGlow();
			
			if (DisplayManager.dungeonTip) DisplayManager.dungeonTip.hide();
			MouseManager.getInstance().clearTmpIcon();
			
			var monster:Monster = event.item as Monster;
			monster.startFiddle();
		}
		
		private var target:Point3D;
		
		//点击怪
		public function onMonsterClick(event:GameMouseEvent):void
		{
			if (mainPlayer.isBusy) return;
			skipBackgroundClick = true;
			
			var monster:Monster = event.item as Monster;
			HandleBattleCommand.getInstance().playerChargeToMonster(monster);
		}
		
		//滑入矿
		public function onMineOver(event:GameMouseEvent):void
		{
			if (mainPlayer.isBusy) return;
			
			event.item.showGlow();
			
			if (!DisplayManager.dungeonTip)
			{
				DisplayManager.dungeonTip = new DungeonTip;
				state.view.isoView.addChild(DisplayManager.dungeonTip);
			}
			var mine:Mine = event.item as Mine;
			DisplayManager.dungeonTip.show(mine);
			
			var conditions:Vector.<ConditionVo> = mine.vo.conditions;
			var condition:ConditionVo;
			for (var i:int = 0; i < conditions.length; i++)
			{
				if (conditions[i].type == ConditionType.ITEM)
				{
					condition = conditions[i];
					break;
				}
			}
			if (condition)
			{
				var hand:MiningHand = MouseManager.getInstance().getMouseIcon(MagicMouseIconType.MINING_HAND) as MiningHand;
				var itemCid:int = int(condition.id);
				if (condition.num == 0) hand.update(itemCid);
				else
				{
					var itemVo:ItemVo = DataManager.getInstance().itemData.getItemByCid(itemCid);
					var num:Number = itemVo?itemVo.num:0
					hand.update(itemCid, condition.num, condition.num > num);
				}
				MouseManager.getInstance().setTmpIcon(hand);
			}
		}
		
		//滑出矿
		public function onMineOut(event:GameMouseEvent):void
		{
			event.item.hideGlow();
			
			if (DisplayManager.dungeonTip) DisplayManager.dungeonTip.hide();
			MouseManager.getInstance().clearTmpIcon();
		}
		
		//点击矿
		public function onMineClick(event:GameMouseEvent):void
		{
			if (mainPlayer.isBusy) return;
			skipBackgroundClick = true;
			
			var mine:Mine = event.item as Mine;			
			new MinningCommand(mine, mainPlayer);
		}
		
	}

}