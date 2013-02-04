package happymagic.order.view 
{
	import com.friendsofed.isometric.Point3D;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.utils.getQualifiedClassName;
	import flash.utils.setTimeout;
	import flash.utils.Timer;
	import happyfish.events.GameMouseEvent;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.WorldState;
	import happymagic.display.view.ui.personMsg.PersonMsgManager;
	import happymagic.model.vo.order.OrderType;
	import happymagic.model.vo.order.OrderVo;
	import happymagic.order.flow.SpreadItemFlow;
	import happymagic.order.view.ui.Face1;
	import happymagic.order.view.ui.Face2;
	import happymagic.order.view.ui.Face3;
	import happymagic.order.view.ui.OrderIcon;
	import happymagic.scene.world.control.AvatarCommand;
	import happymagic.scene.world.MagicWorld;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class Customer extends Person 
	{
		public static const NORMAL:int = 0;
		public static const REJECT:int = 1;
		public static const ACCEPT:int = 2;
		public static const FAILED:int = 3;
		public static const COMPLETE:int = 4;
		
		public var _order:OrderVo;
		private var chat:String;
		
		private var isReadyRejectLeave:Boolean;
		private var _canRequest:Boolean = false;
		
		public var moving:Boolean = false;
		
		private var curMoodName:String;
		
		public function get order():OrderVo { return _order; }
		
		public function get canRequest():Boolean { return _canRequest && OrderType.REQUEST == order.state; }
		
		public function Customer(order:OrderVo, $worldState:WorldState,_x:uint,_y:uint,__callBack:Function=null) 
		{
			_order = order;
			
			var playerObj:Object = new Object();
			
			playerObj.className = order.avatarClassName;
			playerObj.x = _x;
			playerObj.z = _y;
			playerObj.id = order.id;
			
			super(playerObj, $worldState,__callBack);
			typeName = "Customer";
			_speed = 1;
			_drawFrame = 4;
			this.gridPos.x = _x;
			this.gridPos.z = _y;
			this.grid_size_x = 1;
			this.grid_size_z = 1;
		}
		
		
		override protected function makeView():IsoSprite 
		{
			super.makeView();
			view.container.addEventListener(MouseEvent.ROLL_OVER, this.onMouseOver);
			view.container.addEventListener(MouseEvent.ROLL_OUT, this.onMouseOut);
			view.container.addEventListener(MouseEvent.MOUSE_MOVE, this.onMouseOverMove);
			_view.container.addEventListener(MouseEvent.CLICK, onClick);
			
			_view.container.addEventListener(GameMouseEvent.GAME_MOUSE_EVENT, gameMouseEventHandler);
			
			return _view;
		}
		
		private function gameMouseEventHandler(e:GameMouseEvent):void 
		{
			switch(e.mouseEventType)
			{
				case GameMouseEvent.OVER :
					if (!_canRequest) break;
					stopAndFace2Down();
					showGlow();
					break;
					
				case GameMouseEvent.OUT :
					if (!_canRequest) break;
					// 新手引导
					if (order.id.charAt(order.id.length - 1) != 'h')
					{
						fiddle();
					}
					hideGlow();
					break;
			}
		}
		
		public function rejected():void
		{
			stopAndFace2Down();
			hideRequest();
			addCommand(new AvatarCommand(null, null, null, 1000, null, function():void
			{
				showMood(getFaceName(REJECT), true);
				showFlash();
				toOut()
			}));
		}
		
		public function accepted():void
		{
			stopAndFace2Down();
			hideRequest();
			removeMood();
			curMoodName = null;
			addCommand(new AvatarCommand(null, null, null, 1000, null, function():void
			{
				var faceName:String = getFaceName(ACCEPT);
				if(faceName) showMood(faceName, true);
				showFlash();
				toOut()
			}));
		}
		
		private function stopAndFace2Down():void
		{
			clearAllCommand();
			curDir = Person.DOWN;
			playAnimation(null);
			PersonMsgManager.getInstance().delMsg(view.name, false);
		}
		
		/**
		 * 离开
		 * @param	face 离开时的表情
		 */
		public function gotoOut(state:int):void 
		{
			hideRequest();
			var faceName:String = getFaceName(state);
			if(faceName) showMood(faceName, true);
			toOut();
		}
		
		override public function clear():void 
		{
			_order = null;
			curMoodName = null;
			clearAllCommand();
			stopFlash();
			PersonMsgManager.getInstance().delMsg(view.name, false);
			super.clear();
		}
		
		/**
		 * 来了表现下动画就走
		 * @param	faceName
		 * @param	dialog
		 * @param	flow
		 */
		public function comeInAndOut(state:int, dialog:String, flow:SpreadItemFlow):void 
		{
			clearAllCommand();
			var faceName:String = getFaceName(state);
			if (faceName) showMood(faceName, true);
			chat = dialog;
			addCommand(new AvatarCommand(getRandomPoint(), showChatAndSpreadItem, null, 6000, null, toOut, "", false, [flow]));
		}
		
		public function readyRejectLeave():void
		{
			if (isReadyRejectLeave) return;
			isReadyRejectLeave = true;
			showFlash();
			showMood(getFaceName(REJECT), true);
		}
		
		private function showChatAndSpreadItem(flow:SpreadItemFlow):void
		{
			if (!alive) return;
			removeMood();
			PersonMsgManager.getInstance().addMsg(this, chat, 6000);
			if (!flow) return;
			
			flow.showAt(gridPos);
		}
		
		private function getFaceName(face:int):String
		{
			switch(face)
			{
				case NORMAL : break;
				case ACCEPT : break;
				case REJECT : return getQualifiedClassName(Face3);
				case FAILED : return getQualifiedClassName(Face2);
				case COMPLETE : return getQualifiedClassName(Face1);
			}
			return null;
		}
		
		public function comeInAndShowRequest():void 
		{
			
			chat = order.demandDialog;
			view.container.buttonMode = _canRequest = true;
			showMood(getQualifiedClassName(OrderIcon), true);
			//addCommand(new AvatarCommand(gridPos, fiddle, null, 0, null, null, "", true));
			this.fiddle();
		}
		
		public function showRequest():void
		{
			view.container.buttonMode = _canRequest = true;
		}
		
		public function hideRequest():void 
		{
			PersonMsgManager.getInstance().delMsg(view.name, false);
			view.container.buttonMode = _canRequest = false;
		}
		
		private function toOut():void
		{
			clearAllCommand();
			PersonMsgManager.getInstance().delMsg(view.name, false);
			if (curMoodName) showMood(curMoodName);
			var p:Point3D = MagicWorld(_worldState.world).getCustomDoor().gridPos;
			addCommand(new AvatarCommand(p, remove, null, 0, null, null, "", true, null, null));
			view.container.buttonMode = _canRequest = false;
		}
		
		private function getRandomPoint():Point3D
		{
			var node:Node = _worldState.getCustomRoomWalkAbleNode();
			return new Point3D(node.x, 0, node.y);
		}
		
		private var flashTimer:Timer;
		private function showFlash():void
		{
			if (!flashTimer)
			{
				flashTimer = new Timer(300);
				flashTimer.addEventListener(TimerEvent.TIMER, flashTimerHandler);
			}
			if (!flashTimer.running) flashTimer.start();
		}
		
		public function stopFlash():void
		{
			if (!flashTimer) return;
			flashTimer.stop();
			flashTimer.reset();
			hideGlow();
		}
		
		private function flashTimerHandler(e:TimerEvent):void 
		{
			(flashTimer.currentCount % 2) ? hideGlow() : showGlow();
		}
		
		override public function fiddle():void 
		{
			moving = true;
			super.fiddle();
			PersonMsgManager.getInstance().delMsg(view.name, false);
		}
		
		override protected function fiddleWaitFun():void 
		{
			if (!order) return;
			moving = false;
			if (chat)
			{
				removeMood();
				PersonMsgManager.getInstance().addMsg(this, chat, 5000, chatOverHandler);
			}else
			{
				// 新手引导的
				if (order.id.charAt(order.id.length - 1) != "h")
				{
					fiddle();
				}
			}
		}
		
		private function chatOverHandler():void
		{
			if (curMoodName) showMood(curMoodName, true);
			
			// 新手引导的
			if (order.id.charAt(order.id.length - 1) != "h")
			{
				setTimeout(fiddle, 0);
			}
		}
		
		override public function showMood(iconClass:String, showPao:Boolean = false, float:int = 0, duration:Number = 2):void 
		{
			curMoodName = iconClass;
			super.showMood(iconClass, showPao, float, duration);
			
		}
	}

}