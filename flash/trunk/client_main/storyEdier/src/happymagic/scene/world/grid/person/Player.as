package happymagic.scene.world.grid.person 
{
	import com.friendsofed.isometric.IsoUtils;
	import com.friendsofed.isometric.Point3D;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.utils.Timer;
	import happyfish.display.view.PersonChatsView;
	import happyfish.scene.astar.Node;
	import happyfish.scene.camera.CameraControl;
	import happyfish.scene.fog.FogManager;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.control.collision.CollisionController;
	import happyfish.scene.world.control.MouseCursorAction;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.WorldState;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.control.AvatarCommand;
	import happymagic.scene.world.control.MouseDefaultAction;
	import happymagic.scene.world.MagicWorld;
	
	/**
	 * ...
	 * @author slam
	 */
	public class Player extends Person
	{
		public var userVo:UserVo;
		private var transTimer:Timer;
		public var currentAction:int;
		public var untouchable:Boolean; //一个标记 为TRUE时不可被怪吸住进入战斗
		
		public function Player($data:Object, $worldState:WorldState,_x:uint,_y:uint,__callBack:Function=null) 
		{
			userVo = $data as UserVo;
			
			var playerObj:Object = new Object();
			
			playerObj.className = userVo.className;
			playerObj.faceClass = userVo.className;
			playerObj.name = userVo.name;
			playerObj.x = _x;
			playerObj.z = _y;
			playerObj.id = "0";
			
			super(playerObj, $worldState,__callBack);
			typeName = "Player";
			
			
			this.gridPos.x = _x;
			this.gridPos.z = _y;
			this.grid_size_x = 1;
			this.grid_size_z = 1;
			setCollision();
			
			//迷雾
			FogManager.getInstance().addExplorer("0", _x, _y, 1);
		}
		
		
		/**
		 * 根据当前数据重设用户形象
		 */
		public function refreshView(_callBack:Function=null):void {
			var classname:String = userVo.className;
			resetView(classname, _callBack);
		}
		
		override protected function makeView():IsoSprite 
		{
			super.makeView();
			mouseEvent = false;
			return _view;
		}
		
		override protected function view_complete():void 
		{
			super.view_complete();
			
			//光环
			var halo:playerHalo = new playerHalo();
			//var halo:playerHalo_old = new playerHalo_old();
			halo.mouseChildren=
			halo.mouseEnabled = false;
			if (isSelf) {
				//如果是自己,就显示光环
				view.container.addChildAt(halo, 0);
			}else {
				//如果是好友,就显示他的名字
				showName(userVo.name);
			}
			
			hideGlow();
			view.container.mouseChildren = false;
			
		}
		
		override public function go(e:MouseEvent = null):void 
		{
			//CameraControl.getInstance().followTarget(view.container, _worldState.view.isoView.camera);
			var tmpp:Point3D = _worldState.view.targetGrid();
			(_worldState.world as MagicWorld).setPlayerFlag(tmpp);
			
			super.go(e);
			
			
			/*var targetP:Point3D = IsoUtils.screenToIso(new Point(this.isoView.camera.mouseX-6 + IsoUtil.TILE_SIZE/2, 
							this.isoView.camera.mouseY + IsoUtil.TILE_SIZE/2-isoView.sceneY));*/
			/*var avatar_command:AvatarCommand = new AvatarCommand();
			avatar_command.setMovePos(_worldState.view.targetGrid(), null, testCallback);
			avatar_command.setAction("mine",0,3,testCallback);
			this.addCommand(avatar_command);*/
		}
		
		override protected function reachedGoal():void 
		{
			(_worldState.world as MagicWorld).clearPlayerFlag();
			super.reachedGoal();
		}
		
		public function get isSelf():Boolean {
			return userVo.uid == DataManager.getInstance().currentUser.uid;
		}
		
		override protected function onEnterFrame(event:Event):void 
		{
			if (!moveAble) return;
			
			//在迷雾管理器中更新自己
			FogManager.getInstance().updateExplorer("0",gridPos.x, gridPos.z);
			
			//if (_path && _index < _path.length)
			//{
				//var collisionInfo:Object = CollisionController.getInstance().getCollisionInfo(data["id"]);
				//if (collisionInfo)
				//{
					//var currentGoal:Node = _path[_index] as Node; //下一格
					//var goalRect:Rectangle = new Rectangle(currentGoal.x - gridPos.x, currentGoal.y - gridPos.z, 1, 1); //将下一格包装成一个方块(相对量)
					//for each(var collisionArea:Rectangle in collisionInfo) //遍历受到碰撞的区域
					//{
						//if (!collisionArea.intersection(goalRect).isEmpty()) //如果受到碰撞的区域里包含当前目标格
						//{
							//stopMove(); //停止移动
							//currentAction = WAITING;
							//return;
						//}
					//}
				//}
			//}
			
			super.onEnterFrame(event);
		}
		
		override protected function fiddleWaitFun():void 
		{
			var command:AvatarCommand = new AvatarCommand();
			command.setAction("wait", 14000,0, fiddle);
			addCommand(command);
		}
		
		public function get isBusy():Boolean
		{
			return currentAction == FIGHTING || currentAction == NOTICED || currentAction == MINING;
		}
		
		public static const WAITING:int = 0; //什么也不干
		public static const MOVING:int = 1; //移动
		public static const MINING:int = 2; //挖矿
		public static const FIGHTING:int = 3; //战斗
		public static const NOTICED:int = 4; //被盯上
	}

}