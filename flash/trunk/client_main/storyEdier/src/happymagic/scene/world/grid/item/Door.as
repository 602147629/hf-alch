package happymagic.scene.world.grid.item 
{
	import com.friendsofed.isometric.Point3D;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.geom.Point;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import flash.utils.Timer;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.SoundEffectManager;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.grid.Wall;
	import happyfish.scene.world.WorldState;
	import happyfish.scene.world.WorldView;
	import happyfish.utils.display.McShower;
	import happyfish.utils.SysTracer;
	import happymagic.events.ActionStepEvent;
	import happymagic.events.DataManagerEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.DecorVo;
	import happymagic.scene.world.MagicWorld;
	
	/**
	 * 门的几种状态
	 * 1.倒计时中,不能点(传送中)
	 * 2.倒计时结束,场景中有空闲位置,可点
	 * 3.倒计时结束,场景中无空闲位置,不可点
	 * @author slam
	 */
	public class Door extends WallDecor
	{
		private var waitTimer:Timer;
		private var doorMove:MovieClip;
		private var doorOpen:MovieClip;
		
		public var showTipFlag:Boolean = false;
		private var opening:Boolean;
		private var callCloseId:uint;
		public function Door($data:Object, $worldState:WorldState,__callBack:Function=null) 
		{
			super($data, $worldState,__callBack);
			view.container.sortPriority = 10;
		}
		
		override protected function makeView():IsoSprite
		{
			
			var iso:IsoSprite = super.makeView();
			
			//this.countDown();
			return iso;
		}
		
		override protected function view_complete():void 
		{
			super.view_complete();
			view.container.buttonMode = true;
		}
		
		override public function remove():void 
		{
			super.remove();
			//通知world从门队列中清除此门
			(_worldState.world as MagicWorld).removeDoorFromList(this);
		}
		
		override protected function bodyComplete():void 
		{
			super.bodyComplete();
		}
		
		override public function setMirror($x:int):void 
		{
			super.setMirror($x);
		}
		
		public function resetWallView():void {
			//还原背后的墙
			var wall:Wall = _worldState.world.getWallByNode(gridPos.x,gridPos.z) as Wall;
			if(wall) wall.resetWallView();
		}
		
		override public function move($grid_pos:Point3D):void 
		{
			super.move($grid_pos);
			
			if (this.isDoorArea(this.gridPos.x, this.gridPos.z)) {
				
				//this.removeIsoTile();
				
				this.setMirror($grid_pos.x);
				
				//this.addIsoTile();
			}
		}
		
		
		override public function finishMove():void 
		{
			super.finishMove();
			
			(_worldState.world as MagicWorld).addDoorToList(this);
			
			//挖洞
			var wall:Wall = _worldState.world.getWallByNode(gridPos.x, gridPos.z) as Wall;
			if(wall) wall.cutDoor(asset.bitmap_movie_mc);
		}
		
		public function getOutIsoPosition():Point3D {
			var tmpP:Point3D = gridPos.clone();
			if (mirror) 
			{
				tmpP.z += 1;
			}else {
				tmpP.x += 1;
			}
			return tmpP;
		}
		
		public function getNode():Node {
			return new Node(gridPos.x, gridPos.z);
		}
		
		public function getInOutNode():Array {
			var tmparr:Array = new Array();
			
			if (mirror) 
			{
				tmparr.push(new Node(gridPos.x, gridPos.z+1));
				tmparr.push(new Node(gridPos.x, gridPos.z-1));
			}else {
				tmparr.push(new Node(gridPos.x+1, gridPos.z));
				tmparr.push(new Node(gridPos.x-1, gridPos.z));
			}
			return tmparr;
		}
		
		/**
		 * 学生出来请求完成事件
		 * @param	e
		 */
		/*public function outDoorStudents(e:Event):void 
		{
			loadingState = true;
			e.target.removeEventListener(Event.COMPLETE, outDoorStudents);
			
			if (!e.target.data.students) 
			{
				return;
			}else {
				if (e.target.data.students.length<=0) 
				{
					return;
				}
			}
			var p:Point = new Point(view.container.screenX, view.container.screenY);
				p = view.container.parent.localToGlobal(p);
				p = DisplayManager.sceneSprite.globalToLocal(p);
				
				var openMv:McShower = new McShower(teachMv, DisplayManager.sceneSprite);
				openMv.x = p.x;
				openMv.y = p.y;
				if (mirror) {
					openMv.setMcScaleXY( -1, 1);
				}
				
			openDoor();
			var event:DataManagerEvent = new DataManagerEvent(DataManagerEvent.USERINFO_CHANGE);
			EventManager.getInstance().dispatchEvent(event);
			
			//引导事件
			EventManager.getInstance().dispatchEvent(new ActionStepEvent(ActionStepEvent.ACTION_HAPPEN, ActionStepEvent.ON_DOOR_CLICK));
		}*/
		
		/**
		 * 返回门的入口
		 * @return
		 */
		private function getOutDoorPosition():Point3D
		{
			var position:Point3D;
			if (mirror) {
				position = new Point3D(this.x, 0, this.z-1);
			} else {
				position = new Point3D(this.x-1, 0, this.z);
			}
			return position;
		}
		
		/**
		 * 是否可放置判断
		 * @return
		 */
		override public function positionIsValid():Boolean 
		{
			if (!isDoorArea(this.gridPos.x, this.gridPos.z)) {
				return false;
			}
			
			var node:Node;
			var canPut:Boolean = true;
			
			var xsize:uint = grid_size_x;
			var zsize:uint = grid_size_z;
			
			for (var i:int = 0; i < xsize; i++) {
				for (var j:int = 0; j < zsize; j++) {
					//如果是建筑自己的所在位置则验证通过
					if (this.nodes[this.gridPos.x + i]) {
						if (this.nodes[this.gridPos.x + i][this.gridPos.z + j]) {
							continue;
						}
					}
					
					if (!this._worldState.checkInRoom(this.gridPos.x + i, this.gridPos.z + j)) {
						canPut=false;
					}else {
						
						var tmpitem:IsoItem = _worldState.world.getNodeItem(gridPos.x,gridPos.z);
						
						if (tmpitem) 
						{
							if (tmpitem is WallDecor) 
							{
								canPut= false;
							}
							
						}
					}
				}
			}
			
			//根据镜像判断门口的格子是否有东西
			var checkNode:IsoItem;
			if (mirror==0) 
			{
				checkNode = _worldState.world.getNodeItem(this.gridPos.x + 1, this.gridPos.z);
			}else {
				checkNode = _worldState.world.getNodeItem(this.gridPos.x, this.gridPos.z + 1);
			}
			if (checkNode) 
			{
				if (checkNode!=this) 
				{
					canPut = false;
				}
				
			}
			
			
			//如果不是所有格都是自己,就返回false
			return canPut;
		}
		
		public function openDoor():void
		{
			
			if (!opening) 
			{
				opening = true;
				//门打开
				asset.bitmap_movie_mc.gotoAndPlayLabels("open",true);
				//this.asset.bitmap_movie_mc.playToStop();
				
				//音效
				SoundEffectManager.getInstance().playSound(new sound_opendoor());
			}
			if (callCloseId) 
			{
				clearTimeout(callCloseId);
			}
			callCloseId=setTimeout(callCloseDoor, 2000);
		}
		
		private function callCloseDoor():void
		{
			callCloseId = 0;
			closeDoor();
		}
		
		public function closeDoor():void {
			opening = false;
			asset.bitmap_movie_mc.gotoAndPlayLabels("close",true);
			//this.asset.bitmap_movie_mc.playToStop();
			//音效
			SoundEffectManager.getInstance().playSound(new sound_opendoor());
		}
		
		override public function set diyState(value:Boolean):void 
		{
			super.diyState = value;
			
			if (doorMove) {
				doorMove.visible = !value;
			}
			
			if (doorOpen) 
			{
				doorOpen.visible = !value;
			}
		}
		
		public function get width():int
		{
			return this._view.container.getChildAt(0).width;
		}
		
		public function get height():int
		{
			return this._view.container.getChildAt(0).height;
		}
		
        override protected function onClick(event:MouseEvent) : void
        {
			typeName = "Door";
			if (event.target==doorMove) 
			{
				view.container.dispatchEvent(new GameMouseEvent(GameMouseEvent.CLICK, this, typeName, event));
			}else if (event.target==doorOpen) 
			{
				view.container.dispatchEvent(new GameMouseEvent(GameMouseEvent.CLICK, this, typeName, event));
			}else {
				super.onClick(event);
			}
			
			typeName = "WallDecor";
        }
	}

}