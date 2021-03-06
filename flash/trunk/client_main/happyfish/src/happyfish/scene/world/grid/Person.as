package happyfish.scene.world.grid 
{
	import com.adobe.utils.ArrayUtil;
	import com.friendsofed.isometric.IsoUtils;
	import com.friendsofed.isometric.Point3D;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.filters.GlowFilter;
	import flash.geom.Point;
	import flash.text.TextField;
	import flash.utils.clearTimeout;
	import flash.utils.Timer;
	import happyfish.cacher.bitmapMc.events.BitmapCacherEvent;
	import happyfish.cacher.CacheSprite;
	import happyfish.manager.EventManager;
	import happyfish.scene.astar.AStar;
	import happyfish.scene.astar.Grid;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.control.collision.CollisionController;
	import happyfish.scene.world.control.collision.CollisionEvent;
	import happyfish.scene.world.WorldState;
	import happyfish.scene.world.WorldView;
	import happyfish.utils.display.FiltersDomain;
	import happymagic.display.view.ui.PersonPaoView;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.DecorType;
	import happymagic.scene.world.control.AvatarCommand;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.person.Player;
	import happymagic.scene.world.MagicWorld;
	/**
	 * ...
	 * @author slam
	 */
	public class Person extends IsoItem
	{
		private var _player:Sprite;
		private var _layer:int;
		private var _worldView:WorldView;
		//当前走到_path里哪一步
		private var _grid:Grid;
		protected var _index:int;
		protected var _path:Array;
		private var _cellSize:int = IsoUtil.TILE_SIZE;
		public var _speed:Number = 4;
		
		//当前人物方向
		private var _curDir:String = DOWN;
		public static const LEFT:String = 'left';
		public static const RIGHT:String = 'right';
		public static const DOWN:String = 'down';
		public static const UP:String = 'up';
		
		public static const MOVE:String = 'move';
		public static const MAGIC:String = 'magic';
		public static const WAIT:String = 'wait';
		
		protected var commandQueue:Array;
		
		protected var targetPoint:Point3D;
		
		//话泡图标
		protected var bubbleUI:PersonPaoView;

		//帧率
		public var _drawFrame:uint = 2;
		
		//动画标签,如是move或学习中
		public var moviePrefix:String;
		
		private var _free:Boolean=true;
		
		public var moveAble:Boolean = true;
		
		//是否参与碰撞
		private var _canCollision:Boolean = false;
		private var curPlayTimes:uint;
		private var avatarCommandFlag:Boolean=true;//avatarCommand播放开关
		
		
		public function Person($data:Object, $worldState:WorldState,__callBack:Function=null) 
		{
			_bodyCompleteCallBack = __callBack;
			super($data, $worldState);
			this.block = false;
			this.layer = WorldView.LAYER_REALTIME_SORT;
			this._worldView = $worldState.view;
			
			this.commandQueue = [];
			
			//view.container.sortPriority = -6;
		}
		
		/**
		 * 开始闲逛,默认在房间内随机点行走
		 */
		public function fiddle():void
		{	
			var node:Node = _worldState.getCustomRoomWalkAbleNode();
			
			var point3d:Point3D = new Point3D(node.x, 0, node.y);
			this.addCommand( new AvatarCommand(point3d, fiddleWaitFun));
		}
		
		protected function fiddleWaitFun():void 
		{
			fiddle();
		}
		
		protected function set layer($layer:int):void
		{
			this._layer = $layer;
		}
		
		protected function get layer():int
		{
			return this._layer;
		}
		
		public function get free():Boolean { return _free; }
		
		public function set free(value:Boolean):void 
		{
			_free = value;
		}
		
		public function get path():Array { return _path; }
		
		/**
		 * 设置人物面向
		 */
		public function set curDir(value:String):void 
		{
			_curDir = value;
		}
		
		public function get curDir():String {
			return _curDir;
		}
		
		/**
		 * 是否参与碰撞
		 */
		public function get canCollision():Boolean
		{
			return _canCollision;
		}
		
		public function setCollision(cRange:int=0):void
		{
			if (!_data["id"]) return;
			_canCollision = true;
			CollisionController.getInstance().addObj(_data["id"], gridPos.x, gridPos.z, grid_size_x, grid_size_z, cRange);
		}
		
		public function cancelCollision():void
		{
			if (!_data["id"]) return;
			_canCollision = false;
			CollisionController.getInstance().removeObj(_data["id"]);
		}
		
		
        override protected function makeView():IsoSprite
        {
			this._view = new IsoSprite(this.layer);
			this.asset = new CacheSprite(false, _drawFrame);
			asset.bodyComplete_callback = view_complete;
			this.asset.className = this._data.className;
			
			this._view.container.addChild(this.asset);
			
			
			var pos:Point3D = new Point3D(this._data.x, 0, this._data.z);
			this._view.setPos(pos);
			
            return this._view;
        }
		
		
		/**
		 * 设置人物头顶的泡泡
		 * @param	iconClass	泡泡内图标的类名
		 * @param	showPao		是否显示泡泡背景
		 * @param	float		漂浮的距离
		 * @param	duration	漂浮的间隔时间
		 */
		public function showMood(iconClass:String, showPao:Boolean = false, float:int = 0, duration:Number = 2 ):void {
			removeMood();
				
			bubbleUI = new PersonPaoView(this, iconClass, showPao);
			if (float) bubbleUI.setFloat(float);
		}
		
		public function removeMood():void {
			if (bubbleUI) 
			{
				bubbleUI.remove();
				bubbleUI = null;
				return;
			}
		}
		
		override protected function view_complete():void
		{
			if (!alive) 
			{
				return;
			}
			
			super.view_complete();
			
			_view.container.addChild(asset);
			
			asset.bitmap_movie_mc.drawFrame = _drawFrame;
			
			playAnimation(moviePrefix);
			
			if (bubbleUI)
			{
				bubbleUI.initPosition();
				view.container.addChild(bubbleUI);
			}
			
			if (_bodyCompleteCallBack!=null) 
			{
				_bodyCompleteCallBack();
			}
			
			if (DataManager.getInstance().isDiying) 
			{
				visible = false;
			}
			
			
		}
		
		/**
		 * 添加一条队列命令
		 * @param	$avatar_command
		 */
		public function addCommand($avatar_command:AvatarCommand,index:int=-1) : void
		{
			
			
			/*for(var i:Number = commandQueue.length-1; i > -1; i--)
			{
				if ((commandQueue[i] as AvatarCommand).type=="walk") 
				{
					commandQueue.splice(i, 1);
				}
			}*/
			
			//行为加入队列
			if (index==-1) 
			{
				this.commandQueue.push($avatar_command);
			}else {
				this.commandQueue.splice(index,0,$avatar_command);
			}
			
			
			//如果现在队列只有这一条行为
            if (this.commandQueue.length == 1)
            {
				//直接设置人物走向这条的目标格
				if (avatarCommandFlag) 
				{
					playCommand();
				}
            }
		}
		
		public function clearAllCommand():void {
			avatarCommandFlag = true;
			//停止行走
			if(_view.container.hasEventListener(Event.ENTER_FRAME)) _view.container.removeEventListener(Event.ENTER_FRAME, onEnterFrame);
			//空闲状态
			playWaiting();
			//清空行为队列
			this.commandQueue = [];
		}
		
		public function playCommand():void {
			avatarCommandFlag = true;
			if (this.commandQueue.length == 0) return;
			this.setGoal(this.commandQueue[0].destination,commandQueue[0].mustGo);
		}
		
		/**
		 * 移除一条队列命令
		 */
		public function shiftCommand() : void
		{
			if (commandQueue.length==0) 
			{
				return;
			}
			var curCommand:AvatarCommand = commandQueue.shift();
			
			avatarCommandFlag = curCommand.autoNext;
			
            if (this.commandQueue.length > 0)
            {
				if (curCommand.autoNext) 
				{
					playCommand();
				}
            }
		}
		
		/**
		 * 执行一条队列命令
		 */
		public function performCommand() : void
		{
			//行为队列已为空，就跳出
			if (commandQueue.length<=0) 
			{
				return;
			}
			
            var commandPerformed:AvatarCommand;
            var timer:Timer;
            commandPerformed = this.commandQueue[0];
			//执行行走完成后的回调
            commandPerformed.doIt();
			
			//如要求完成后表现动画
            if (commandPerformed.actionDuration > 0 || commandPerformed.actionTimes>0)
            {	
                if (commandPerformed.faceTowards != null)
                {
					//如果有指定朝向,就设置
                    faceTowardsSpace(commandPerformed.faceTowards, commandPerformed.actionAnimation);
                }
                else if (commandPerformed.actionAnimation != null) //如果有指定动作动画,就播放
                {
                    playAnimation(commandPerformed.actionAnimation,commandPerformed.actionToStop);
                }
				
				if (commandPerformed.actionDuration) 
				{
					timer = new Timer(commandPerformed.actionDuration, 1);
					timer.addEventListener(TimerEvent.TIMER_COMPLETE, onTimerComplete);
					timer.start();
				}else if (commandPerformed.actionTimes) 
				{
					curPlayTimes = commandPerformed.actionTimes;
					asset.bitmap_movie_mc.addEventListener(Event.COMPLETE, playTimesCheck);
				}
            }
            else
            {
				if (commandPerformed.faceTowards != null)
                {
					//如果有指定朝向,就设置
                    this.faceTowardsSpace(commandPerformed.faceTowards);
					stopAnimation(moviePrefix);
                }
				//如果不要求事后闲逛,就直接到下一步
                this.shiftCommand();
            }
		}
		
		private function playTimesCheck(e:Event):void 
		{
			curPlayTimes--;
			if (curPlayTimes==0) 
			{
				asset.bitmap_movie_mc.removeEventListener(Event.COMPLETE, playTimesCheck);
				onTimerComplete();
			}
		}
		
		private function onTimerComplete(event:TimerEvent=null):void
		{
			if (event) 
			{
				event.target.removeEventListener(TimerEvent.TIMER_COMPLETE, onTimerComplete);
			}
			//行为完成后表现的动画完成后回调方法,如无就进行下一步操作
			var currentCommand:AvatarCommand;
			var commandPerformed:AvatarCommand;
            commandPerformed = commandQueue[0];
			//var event:* = event;
			
			if (!commandPerformed) 
			{
				shiftCommand();
				return;
			}
			if (commandPerformed.actionToStop) 
			{
				
			}else {
				//播放普通状态
				playWaiting();
			}
			
			//if (asset.bitmap_movie_mc.hasLabel(WAIT)) stopAnimation(WAIT);
			//else stopAnimation(MOVE);
			
			currentCommand = commandQueue[0];
				
			//TODO ????,好像是用来在动画timer进行到时,中间person改为做别的事了,这时就要中断原有的行为
			if (currentCommand != commandPerformed)
			{
				return;
			}
			
			shiftCommand();
			commandPerformed.fiddleDoIt();
			
			return;
		}
		
		/**
		 * 下一条command
		 */
        protected function reachedGoal() : void
        {
			//从开门控制器里去除此人物
			(_worldState.world as MagicWorld).doorControl.removePerson(this);
			//执行下一条
            this.performCommand();
            return;
        }
		
		/**
		 * 设置目标
		 * @param	target
		 * @param	mustGo	是否强制设置目标格为可行走点，用来在走到某个物件的占格上时使用，比如走到椅子所在格上
		 * @param	truePoint	最终要走到的点
		 */
		public function setGoal(target:Point3D,mustGo:Boolean=false,truePoint:Point=null) : void
		{
			if (!target) 
			{
				this.reachedGoal();
				return;
			}
			//如果目标格是0x0格
			//if ((target.x == 0 && target.y == 0 && target.z == 0) ) {
				//过掉本条command,继续下一条
				//this.reachedGoal();
				//return;
			//}
			
			
			_grid = this._worldState.grid;
			var targetP:Point3D = target;
			
			//如果目标点超出地图大小,跳过此command
			//if (!_grid.hasNode(targetP.x,targetP.z) || _worldState.isWallArea(targetP.x,targetP.z) || !_grid.getNode(targetP.x,targetP.z).walkable) 
			if (!_grid.hasNode(targetP.x,targetP.z) || !_grid.getNode(targetP.x,targetP.z).walkable) 
			{
				//过掉本条command,继续下一条
				this.reachedGoal();
				return;
			}
			
			this.targetPoint = targetP;
			_grid.setEndNode(targetP.x, targetP.z);

			var xpos:int = Math.round(this._view.position.x / IsoUtil.TILE_SIZE);
			var ypos:int = Math.round(this._view.position.z / IsoUtil.TILE_SIZE);
			_grid.setStartNode(xpos, ypos);
			
			var finded:Boolean=this.findPath(mustGo);
			//if (finded && truePoint) 
			//{
				//_path.push(truePoint
			//}
		}
		
		/**
		 * 走到鼠标在所在场景格
		 * @param	e
		 */
		public function go(e:MouseEvent=null):void
		{
			var avatar_command:AvatarCommand = new AvatarCommand(_worldState.view.targetGrid(),null,null,0,null,null,"walk");
			this.addCommand(avatar_command);
		}
		
		/**
		 * Creates an instance of AStar and uses it to find a path.
		 * @return 是否找到路径
		 */
		private function findPath(mustGo:Boolean=false):Boolean
		{
			var astar:AStar = new AStar();
			if(astar.findPath(_grid,mustGo))
			{
				astar.path.splice(0, 1);
				_path = astar.path;
				_index = 0;
				//判断路径步数
				if (_path.length==0) 
				{
					//如果步数为0,就跳过
					reachedGoal();
					return false;
				}else {
					//通知door控制器,判断是否会经过门,如果有就加入开门控制
					(_worldState.world as MagicWorld).doorControl.addPerson(this);
					_view.container.addEventListener(Event.ENTER_FRAME, onEnterFrame);
				}
				return true;
			} else {
				//未找到路径
				trace("noway",data.x,data.z,view.position);
				this.reachedGoal();
				return false;
			}
		}
		
		/**
		 * 停止行走,清空行为队列
		 */
		public function stopMove():void {
			//停止行走
			if(_view.container.hasEventListener(Event.ENTER_FRAME)) _view.container.removeEventListener(Event.ENTER_FRAME, onEnterFrame);
			//空闲状态
			playWaiting();
			//清空行为队列
			this.commandQueue = [];
			//触发走到终点
			reachedGoal();
		}
		
		/**
		 * Finds the next node on the path and eases to it.
		 */
		protected function onEnterFrame(event:Event):void
		{
			if (!moveAble) return;
			
			if (_path.length==0) 
			{
				this._view.container.removeEventListener(Event.ENTER_FRAME, onEnterFrame);
					
					this.stopAnimation(MOVE);
					
					//到终点了
					this.reachedGoal();
				return;
			}
			var targetX:Number = _path[_index].x * _cellSize;
			var targetY:Number = _path[_index].y * _cellSize;
			
			var tmpP:Point = IsoUtils.isoToScreen(new Point3D(targetX,0,targetY));
			targetX = tmpP.x;
			targetY = tmpP.y;
			
			var dx:Number = targetX - this._view.container.screenX;
			var dy:Number = targetY - this._view.container.screenY;
			var dist:Number = Math.sqrt(dx * dx + dy * dy);

			if(dist < 1)
			{
				_index++;
				if(_index >= _path.length)
				{
					this._view.container.removeEventListener(Event.ENTER_FRAME, onEnterFrame);
					
					this.stopAnimation(MOVE);
					
					//到终点了
					this.reachedGoal();
					return;
				}
			}
			else
			{
				//this._view.container.screenX = this._view.container.screenX + dx * _speed;
				//this._view.container.screenY = this._view.container.screenY + dy * _speed;
				
				if (dx > 0) {
					this._view.container.screenX += _speed;
				} else if ( dx<0 ) {
					this._view.container.screenX -= _speed;
				}
				
				if (dy > 0) {
					this._view.container.screenY += _speed * .5;
				} else if (dy < 0) {
					this._view.container.screenY -= _speed * .5;
				}
				
			}
			
			//更新位置数据
			gridPos.x = Math.floor(view.position.x / IsoUtil.TILE_SIZE);
			gridPos.z = Math.floor(view.position.z / IsoUtil.TILE_SIZE);
			data.x = gridPos.x;
			data.z = gridPos.z;
			
			//设置方向
			this.setDirection(dx, dy);
			//播放走路动画
			this.playAnimation(MOVE);
			//通知layer里可排序
			this._view.parent.sort();
			
			//在碰撞控制器中更新自己
			if (_canCollision) CollisionController.getInstance().updateObj(_data["id"], gridPos.x, gridPos.z);
			//检测开门
			(_worldState.world as MagicWorld).doorControl.checkPerson(this);
		}
		
		/**
		 * 设置人物到指定格子上
		 * @param	pos	[Point3D] 目标格子坐标
		 */
		public function setPos(pos:Point3D):void {
			gridPos = pos;
			data.x = gridPos.x;
			data.z = gridPos.z;
			view.position = new Point3D(pos.x * IsoUtil.TILE_SIZE, pos.y * IsoUtil.TILE_SIZE, pos.z * IsoUtil.TILE_SIZE);
		}
		
		override public function remove() : void
        {
			//从开门控制器里去除此人物
			(_worldState.world as MagicWorld).doorControl.removePerson(this);
			if (_view) 
			{
				if (_view.parent) 
				{
					_view.parent.removeIsoChild(this._view);
				}
			}
			
            this.alive = false;
			this._worldState.world.removeItem(this);
			if (_canCollision) cancelCollision();
			
			clear();
			
            return;
        }
		
		override public function clear():void 
		{
			if (asset) 
			{
				if (asset.bitmap_movie_mc) 
				{
					if (asset.bitmap_movie_mc.hasEventListener(Event.COMPLETE)) 
					{
						asset.bitmap_movie_mc.removeEventListener(Event.COMPLETE, playTimesCheck);
					}
				}
			}
			
			clearAllCommand();
			
			super.clear();
			
		}
		
		/**
		 * 面向指定格子，并播放指定动作动画
		 * @param	$forward
		 * @param	$action
		 */
        public function faceTowardsSpace($forward:Point3D, $action:String = null) : void
        {
            if ($forward == null)
            {
                return;
            }
            if ($action == null)
            {
                $action = MOVE;
            }
			if (!targetPoint) 
			{
				targetPoint = new Point3D(data.x, 0, data.z);
			}
            this.setDirection($forward.x * this._cellSize - this.targetPoint.x * this._cellSize, $forward.z  * this._cellSize - 
								this.targetPoint.z * this._cellSize);
            this.playAnimation($action);
			
            return;
        }
		
		/**
		 * 播放指定标签动画
		 * @param	$prefix
		 */
		public function playAnimation($prefix:String,toStop:Boolean=false):void
		{
			if (!$prefix)
			{
				playWaiting();
				return;
			}
			
			this.moviePrefix = $prefix;
			
			if (!this.asset.bitmap_movie_mc) return;
			
			var label:String = $prefix + "_" + _curDir;
			if (!asset.bitmap_movie_mc.hasLabel(label)) label = $prefix;
			if (asset.bitmap_movie_mc.hasLabel(label)) asset.bitmap_movie_mc.gotoAndPlayLabels(label,toStop);
		}
		
		/**
		 * 停止动画
		 * @param	$prefix
		 */
		public function stopAnimation($prefix:String):void
		{
			if (this.asset.bitmap_movie_mc) {
				var label:String = $prefix + "_" + _curDir;
				if (!this.asset.bitmap_movie_mc.hasLabel(label)) label = $prefix;
				this.asset.bitmap_movie_mc.gotoAndStopLabels(label);
			}
            return;
		}
		
		private function playWaiting():void
		{
			if (!asset.bitmap_movie_mc) return;
			
			//停止在移动动画第1帧
			if (asset.bitmap_movie_mc.hasLabel(WAIT))
			{
				asset.bitmap_movie_mc.gotoAndPlayLabels(WAIT);
			}
			else
			{
				var label:String = MOVE + "_" + _curDir;
				if (asset.bitmap_movie_mc.hasLabel(label)) asset.bitmap_movie_mc.gotoAndStopLabels(label);
				else asset.bitmap_movie_mc.gotoAndStop(1);
			}
		}
		
		/**
		 * 取得行走方向，水平向右为0，顺时针旋转
		 * (注意：在tile的宽=2*高时，右下 左下 左上 右上并非是45度)
		 * 0 -- 右		0度
		 * 1 -- 右下		45度
		 * 2 -- 下		90度
		 * 3 -- 左下		135度
		 * 4 -- 左		180度
		 * 5 -- 左上		-135度
		 * 6 -- 上		-90度
		 * 7 -- 右上		-45度
		 */
		public function setDirection(dx:Number, dy:Number):void
		{
			if (dx==0 && dy==0) 
			{
				return;
			}
			var radians:Number = Math.atan2(dy, dx);
			/**
			角度(degrees)和弧度(radians)之间的转换关系式是：
			radians = (Math.PI / 180) * degrees
			**/
			var degrees:Number = radians * 180 / Math.PI;	//角度
			var direction:Number;
			//八方向 360/8=45，左上角为元点，右向为横轴，逆时针角度为负，顺时针为正  
			// 也可用弧度直接算
			direction = Math.round( degrees / 45 );	
			
			if (degrees < 0)	//角度为负
			{
				direction = Math.abs(direction + 8);
			}
			
			//转成跟图片一致的
			switch (direction)
			{
				case 0:
					this._curDir = RIGHT;
					break;
				case 1:
					this._curDir = RIGHT;
					break;
				case 2:
					this._curDir = DOWN;
					break;
				case 3:
					this._curDir = DOWN;
					break;
				case 4:
					this._curDir = LEFT;
					break;
				case 5:
					this._curDir = LEFT;
					break;
				case 6:
					this._curDir = UP;
					break;
				case 7:
					this._curDir = UP;
					break;
			}
		}
		
		public function pause():void 
		{
			
		}
	}

}