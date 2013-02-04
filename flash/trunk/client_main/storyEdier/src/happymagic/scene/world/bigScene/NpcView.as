package happymagic.scene.world.bigScene 
{
	import com.friendsofed.isometric.IsoUtils;
	import com.friendsofed.isometric.Point3D;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import happyfish.cacher.CacheSprite;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.WorldState;
	import happyfish.scene.world.WorldView;
	import happyfish.utils.CustomTools;
	import happymagic.display.view.ui.PersonPaoView;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.NpcVo;
	import happymagic.model.vo.task.TaskType;
	import happymagic.model.vo.task.TaskVo;
	import happymagic.scene.world.bigScene.events.BigSceneEvent;
	import happymagic.scene.world.control.AvatarCommand;
	import happymagic.scene.world.MagicWorld;
	import happymagic.scene.world.SceneType;
	
	/**
	 * ...
	 * @author jj
	 */
	public class NpcView extends Person
	{
		private var paoIcon:PersonPaoView;
		private var fiddleRange:Array;
		public var npcvo:NpcVo;
		
		public var state:uint;	// 1 未说过的剧情对话 	2 可接收的任务 	3 已说过的剧情对话	4 其它
		
		public static const STATE_NEWSTORY:uint = 1;
		public static const STATE_TASK:uint = 2;
		public static const STATE_STORY:uint = 3;
		public static const STATE_ACTMODULE:uint = 3;
		public static const STATE_OTHER:uint = 4;
		public var curTask:TaskVo;
		private var standId:Number;
		public function NpcView($data:Object, $worldState:WorldState,__callBack:Function=null) 
		{
			super($data, $worldState, __callBack);
			_speed = 2;
			typeName = "Npc";
			
			npcvo = data as NpcVo;
			
			view.container.buttonMode = true;
			
		}
		
		/**
		 * 开始闲逛
		 */
		override public function fiddle():void
		{
			if (!alive) return;
			var node:Node = CustomTools.customFromArray(fiddleRange);
			
			var point3d:Point3D = new Point3D(node.x, 0, node.y);
			
			addCommand( new AvatarCommand(point3d, fiddleWaitFun));
		}
		
		override protected function fiddleWaitFun():void 
		{
			var command:AvatarCommand = new AvatarCommand();
			command.setAction("wait", 4000,0, fiddle);
			addCommand(command);
		}
		
		override protected function makeView():IsoSprite 
		{
			super.makeView();
			
			//鼠标事件
			view.container.addEventListener(MouseEvent.MOUSE_OVER, onMouseOver);
			view.container.addEventListener(MouseEvent.MOUSE_OUT, onMouseOut);
			view.container.addEventListener(MouseEvent.MOUSE_MOVE, onMouseOverMove);
			view.container.addEventListener(MouseEvent.CLICK, onClick);
			
			return view;
		}
		
		override protected function view_complete():void 
		{
			super.view_complete();
			
			if (data.faceX || data.faceZ) 
			{
				faceTowardsSpace(new Point3D(data.faceX, 0, data.faceZ));
			}
			
			fiddleRange = new Array;
			if (data["fiddleRangeX"] && data["fiddleRangeZ"]) 
			{
				
			
				var fiddleWidth:int = Math.max(data["fiddleRangeX"], 1);
				var fiddleHeight:int = Math.max(data["fiddleRangeZ"], 1);
				
				for (var i:int = 0; i < fiddleWidth; i++)
				{
					for (var j:int = 0; j < fiddleHeight; j++)
					{
						var node:Node = _worldState.grid.getNode(Number(data.x) + i, Number(data.z) + j);
						if (node) 
						{
							if (node.walkable) fiddleRange.push(node);
						}
					}
				}
			}
			
			if (fiddleRange.length>0) 
			{
				var tmpnode:Node = CustomTools.customFromArray(fiddleRange);
				setPos(new Point3D(tmpnode.x, 0, tmpnode.y));
				fiddle();
			}else {
				stand();
			}
			
			initPaoIcon();
		}
		
		private function stand():void 
		{
			standId = 0;
			
			var command:AvatarCommand = new AvatarCommand();
			command.setAction("wait", 800,0, stand_complete);
			addCommand(command);
		}
		
		private function stand_complete():void 
		{
			stopAnimation("move");
			standId = setTimeout(stand, CustomTools.customInt(3000,8000));
		}
		
		override public function remove():void 
		{
			super.remove();
		}
		
		
		public function initPaoIcon():void
		{
			if (paoIcon) 
			{
				paoIcon.remove();
			}
			
			removeMood();
			var curSceneType:uint = DataManager.getInstance().curSceneType;
			if (curSceneType!=SceneType.TYPE_HOME && curSceneType!=SceneType.TYPE_SELF_VILIAGE && curSceneType != SceneType.TYPE_EXPLORE) 
			{
				return;
			}
			
			//paoIcon = new PersonPaoView(this);
			var tasks:Array = DataManager.getInstance().taskData.getTasksByNpcId(npcvo.id,false,true);
			
			if (tasks.length>0) 
			{
				//是否可接任务
				state = STATE_TASK;
				curTask = tasks[0] as TaskVo;
				if (curTask.type == TaskType.MAIN) 
				{
					showMood("npcTaskIcon1");
					//paoIcon.setIconClass("npcTaskIcon1");
				}else if (curTask.type == TaskType.LATERAL) 
				{
					showMood("npcTaskIcon2");
					//paoIcon.setIconClass("npcTaskIcon2");
				}
				
				
			}else if (npcvo.chatState==1 && npcvo.chats.length>0) 
			{
				//是否有未说过的NPC剧情对话
				showMood("npcChat_unread");
				//paoIcon.setIconClass("npcChat_unread");
				state = STATE_NEWSTORY;
			}else if (npcvo.chatState==2 && npcvo.chats.length>0) 
			{
				//是否有已说过的NPC对话
				state = STATE_STORY;
				showMood("npcChat_readed");
				//paoIcon.setIconClass("npcChat_readed");
			}else {
				//外连,商店等标记
				state = STATE_OTHER;
			}
			
			
		}
		
		override public function clear():void 
		{
			super.clear();
			if (standId) clearTimeout(standId);
		}
		
	}

}