package happyfish.scene.world 
{
	import adobe.utils.CustomActions;
	import com.friendsofed.isometric.IsoObject;
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.geom.Matrix;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import happyfish.scene.astar.AStar;
	import happyfish.scene.astar.Grid;
	import happyfish.scene.astar.Node;
	import happyfish.scene.astar.NodesUtil;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.iso.IsoView;
	import happyfish.scene.world.grid.BaseItem;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.grid.SolidObject;
	/**
	 * ...
	 * @author slam
	 */
	public class GameWorld
	{
		//游戏数据
		protected var _data:Object;
		
		//背景容器
        protected var _groundSprite:Sprite;
		//背景格子
        protected var _groundGrid:Object;
        public var groundRect:Rectangle;
		//背景的bitmapdata
		public var groundData:BitmapData;
		protected var backgroundColor:uint = 0x9355046;
		//IsoView
		protected var _view:WorldView;
		
		protected var _items:Array;
		protected var _worldState:WorldState;
		//记录此node上的物品
		public var nodeItems:Object = new Object();
		//记录此node上的物品
		public var decorInstanceItems:Object = new Object();
		//记录此node上的地板或者墙壁
		private var _nodeWallTileItems:Object = new Object();
		
		//标记当前是否正在渲染场景
		public var sceneLoading:Boolean;
		public function GameWorld($worldState:WorldState) 
		{
			this._worldState = $worldState;
			this._view = this._worldState.view;
			
			this._items = new Array();
		}
		
		public function create($data:Object, $init_flg:Boolean = true):void
		{
			
		}
		
        public function createItem($gridItem:BaseItem) : void
        {
            this.addItem($gridItem as IsoItem);
            return;
        }
		
		/**
		 * 将物件放入物件列表
		 * @param	$item
		 * @param	tmpAdd	[boolean] 临时增加进场景，此时不要增加到物品队列中
		 */
		public function addItem(item:IsoItem,tmpAdd:Boolean=false):void
		{
			this._items.push(item);
		}
		
		/**
		 * 清除物件列表内的物件
		 * @param	$item
		 */
		public function removeItem(item:IsoItem):void
		{
			for (var i:int = 0; i < this._items.length; i++ ) {
				if (IsoItem(this._items[i]) === item) {
					//删除这个项目
					_items.splice(i, 1);
					return;
				}
			}
		}
		
		/**
		 * 清除WORLD内所有内容
		 */
		public function clear():void {
			//所有墙与地板记录
			nodeWallTileItems = new Object();
			//物件记录
			nodeItems = new Object();
			//地板数据
			if(groundData) groundData.dispose();
			
		}
		
		public function destroyItems():void
		{
			
		}
		
        public function updateGroundBitmapData():void
        {
			if (groundData) groundData.dispose();
			if (_groundSprite.width == 0 || _groundSprite.height == 0) return;
			groundData = new BitmapData(_groundSprite.width, _groundSprite.height, true, 0x000000);
			var rect:Rectangle = _groundSprite.getBounds(_groundSprite);
            this.groundData.draw(this._groundSprite, new Matrix(1, 0, 0, 1, -rect.x, -rect.y));
            return;
        }
		
		/**
		 * 加入格子,设置不可行走
		 * @param	$item
		 */
		public function addToGrid(item:IsoItem,inRoom:Boolean=true):void
		{
			throw("need extends");
		}
		
		/**
		 * 回复物件下的格子的可行走和可DIY状态
		 * @param	$item
		 */
		public function removeToGrid(item:IsoItem,inRoom:Boolean=true):void
		{
			
		}
		
		/**
		 * 记录格子上所对应的对象
		 * @param	$item
		 */
		public function saveNodeItems(item:IsoItem,x:int,z:int):void
		{
			if (!this.nodeItems[x]) {
				this.nodeItems[x] = new Object();
			}
			
			//TODO 好像只记了一个物体的一个格子
			this.nodeItems[x][z] = item;
			
		}
		
		/**
		 * 从占格物件列表中移除
		 * @param	$item
		 */
		public function removeNodeItems(item:IsoItem,x:int,z:int):void {
			if (nodeItems[x]) {
				if (nodeItems[x][z]) 
				{
					this.nodeItems[x][z] = null;
				}
			}
		}
		
		/**
		 * 记录墙纸物件到列表中
		 * @param	$item
		 */
		public function saveWallTileNodeItem($item:BaseItem):void
		{
			var solid_object:IsoItem = IsoItem($item);
			
			if (solid_object.x == IsoUtil.roomStart && solid_object.z == IsoUtil.roomStart) {
				return;
			}
			
			if (!this.nodeWallTileItems[solid_object.x]) {
				this.nodeWallTileItems[solid_object.x] = new Object();
			}

			this.nodeWallTileItems[solid_object.x][solid_object.z] = solid_object;
		}
		
		public function get userInfo():Object
		{
			return {};
		}
		
		/**
		 * 待继承
		 */
		public function leaveEditMode():void
		{
			throw("待继承");
		}
		
		public function get groundSprite():Sprite		
		{
			return this._groundSprite;
		}
		
		public function get items():Array { return _items; }
		
		public function get nodeWallTileItems():Object { return _nodeWallTileItems; }
		
		public function set nodeWallTileItems(value:Object):void 
		{
			_nodeWallTileItems = value;
		}
		
		public function getWallByNode(x:int, y:int):IsoItem {
			if (nodeWallTileItems[x]) 
			{
				if (nodeWallTileItems[x][y]) 
				{
					return nodeWallTileItems[x][y];
				}
			}
			return null;
		}
		
		public function findPath(startNode:Point,endNode:Point,mustGo:Boolean=false):Array
		{
			var _path:Array;
			
			var astar:AStar = new AStar();
			this._worldState.grid.setStartNode(startNode.x, startNode.y);
			this._worldState.grid.setEndNode(endNode.x, endNode.y);
			
			if(astar.findPath(_worldState.grid,mustGo))
			{
				astar.path.splice(0, 1);
				_path = astar.path;
				
				//判断路径步数
				if (_path.length==0) 
				{
					return null;
				}
				
			} else {
				return null;
			}
			
			return _path;
		}
		
		public function getNodeItem(x:Number, z:Number):IsoItem 
		{
			if (nodeItems[x]) 
			{
				if (nodeItems[x][z]) 
				{
					return nodeItems[x][z];
				}
			}
			
			return null;
		}
		
		public function findCanWalkNodeFromRect(startNode:Node,rect:Rectangle):Node {
			var grid:Grid = _worldState.grid;
			
			//判断起点是否可走
			if (!grid.getNode(startNode.x,startNode.y).walkable) 
			{
				trace("startnode,Can not Walkable",startNode);
				//不可走,返回空
				return null;
			}
			
			rect.x -= 1;
			rect.y -= 1;
			rect.width += 2;
			rect.height += 2;
			
			//得到目标建筑周边点
			var goalOutNodes:Array = NodesUtil.getAroundNodesByRect(rect);
			
			if (goalOutNodes.length > 0 )
			{	
				//所有点先按距离排序
				var tmparr:Array = new Array();	
				var tmpdist:Number;
				var item:Node;
				for (var j:int = 0; j < goalOutNodes.length; j++) 
				{
					item = goalOutNodes[j];

					//先判断是否是在grid中的点
					if (grid.hasNode(item.x,item.y)) 
					{
						tmpdist = Point.distance(new Point(startNode.x, startNode.y), new Point(item.x, item.y));
						tmparr.push( { node:item, dist:tmpdist } );
					}
					
				}
				tmparr.sortOn("dist", Array.NUMERIC);
				
				for (var nn:int = 0 ; nn < tmparr.length ; nn++ )
				{
					var endnode:Node = tmparr[nn].node;
					
					var astar:AStar = new AStar();
					
					//过滤不可走的目标点
					if (!grid.getNode(endnode.x,endnode.y).walkable)
					{
						trace("endnode,Can not Walkable",endnode);
						//tmparr.splice(nn,1);
						continue;
					}
					
					//试着寻路
					trace("endnode can walk",endnode);
					grid.setStartNode(startNode.x, startNode.y);
					grid.setEndNode(endnode.x, endnode.y);
					if(astar.findPath(grid,true))
					{
						if (astar.path.length>1) 
						{
							return astar.path[astar.path.length-1];
						}
					}
				}
			}
			
			return null;
		}
	}

}