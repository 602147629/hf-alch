package happyfish.scene.world.control.collision
{
	import flash.events.TimerEvent;
	import flash.geom.Rectangle;
	import flash.utils.Timer;
	import happyfish.manager.EventManager;
	/**
	 * 碰撞控制器
	 * @author XiaJunJie
	 */
	public class CollisionController 
	{
		//单例
		private static var _instance:CollisionController;
		
		public static function getInstance():CollisionController
		{
			if (!_instance) _instance = new CollisionController;
			return _instance;
		}
		
		//碰撞物列表
		private var objList:Object;
		
		//地图映射
		private var map:Vector.<Vector.<MapNode>>;
		
		//计时器
		private var timer:Timer;
		
		//物件是否发生过变化
		private var changed:Boolean;
		
		/**
		 * 构造
		 */
		public function CollisionController()
		{
			timer = new Timer(200);
			timer.addEventListener(TimerEvent.TIMER, onTimer);
			timer.start();
			
			changed = false;
		}
		
		/**
		 * 刷新场景
		 * @param	width
		 * @param	height
		 */
		public function refreshScene(width:int, height:int):void
		{
			map = new Vector.<Vector.<MapNode>>();
			for (var i:int = 0; i < width; i++)
			{
				var col:Vector.<MapNode> = new Vector.<MapNode>();
				for (var j:int = 0; j < height; j++) col.push(new MapNode(i, j));
				map.push(col);
			}
			objList = new Object;
		}
		
		/**
		 * 新增一个碰撞物
		 * @param	id
		 * @param	x
		 * @param	z
		 * @param	size_x
		 * @param	size_z
		 * @param	cRadius: 碰撞半径
		 */
		public function addObj(id:String, x:int, z:int, size_x:int, size_z:int, cRadius:int):void
		{
			if (!map) return;
			
			objList[id] = new CollisionObject(id, size_x, size_z, cRadius);
			updateObj(id, x, z);
		}
		
		/**
		 * 更新一个碰撞物的位置
		 * @param	id
		 * @param	x
		 * @param	z
		 */
		public function updateObj(id:String, x:int, z:int):void
		{
			if (!map) return;
			
			var obj:CollisionObject = objList[id];
			if (!obj) return;
			
			var rect:Rectangle = obj.selfRange;
			if (rect.x == x && rect.y == z) return;
			
			//在该物件所在的原地块中删除该物件
			for (var i:int = rect.x; i < rect.right; i++)
			{
				for (var j:int = rect.y; j < rect.bottom; j++)
				{
					map[i][j].deleteObj(obj.id);
				}
			}
			
			//重设该物件的位置
			rect.x = x;
			rect.y = z;
			for (i = rect.x; i < rect.right; i++)
			{
				for (j = rect.y; j < rect.bottom; j++)
				{
					map[i][j].addObj(obj);
				}
			}
			
			//重新设置该物件的碰撞范围
			obj.collisionRange.right = Math.min(rect.right + obj.collisionRadius, map.length-1);
			obj.collisionRange.left = Math.max(rect.x - obj.collisionRadius, 0);
			obj.collisionRange.bottom = Math.min(rect.bottom + obj.collisionRadius, map[0].length-1);
			obj.collisionRange.top = Math.max(rect.y - obj.collisionRadius, 0);
			
			obj.changed = true;
			
			changed = true;
		}
		
		/**
		 * 移除一个碰撞物
		 * @param	id
		 */
		public function removeObj(id:String):void
		{
			if (!map) return;
			
			var obj:CollisionObject = objList[id];
			if (!obj) return;
			
			obj.exist = false;
			obj.changed = true;
			
			changed = true;
		}
		
		/**
		 * 获得一个碰撞物的碰撞信息
		 * @param	id
		 * @return	一个Object 记录所有与指定物件碰撞的物件及其碰撞区域
		 * 			格式[key:目标碰撞物ID, value:碰撞区域(Rectangle)]
		 */
		public function getCollisionInfo(id:String):Object
		{
			if (!objList) return null;
			var obj:CollisionObject = objList[id];
			if (!obj) return null;
			
			return obj.collisionInfo;
		}
		
		//碰撞检测
		private function onTimer(event:TimerEvent):void
		{
			if (!map) return;
			if (!changed) return;
			
			var handlingRecord:Object = new Object; //处理记录 记录哪些事物间的碰撞已经被处理过 避免重复处理
			
			for each(var obj:CollisionObject in objList) //遍历碰撞物件表
			{
				//遍历该物件碰撞范围内的每一个地块
				for (var i:int = obj.collisionRange.x; i < obj.collisionRange.right; i++)
				{
					for (var j:int = obj.collisionRange.y; j < obj.collisionRange.bottom; j++)
					{
						var node:MapNode = map[i][j];
						for each(var obj2:CollisionObject in node.objList) //遍历地块内的每一个物件
						{
							if (obj2.id == obj.id) continue; //如果是自己 跳过
							if (handlingRecord[obj.id + "&" + obj2.id]) continue; //如果已经处理过 跳过
							
							if (obj.changed || obj2.changed) //如果两者都没发生过改变 不会执行if语句内的检测代码
							{
								handleCollision(obj, obj2);
								handleCollision(obj2, obj);
							}
							
							//将这对物件标记为已处理
							handlingRecord[obj.id + "&" + obj2.id] = true;
							handlingRecord[obj2.id + "&" + obj.id] = true;
						}
					}
				}
				
				//遍历该物件的碰撞信息表 所有碰撞的物件已经在上面处理过了 这里主要过滤掉那些与当前物件分离的物件
				for (var obj2Id:String in obj.collisionInfo)
				{
					obj2 = objList[obj2Id] as CollisionObject;
					if (handlingRecord[obj.id + "&" + obj2.id]) continue; //如果已经处理过 跳过
					
					handleCollision(obj, obj2);
					handleCollision(obj2, obj);
					
					//将这对物件标记为已处理
					handlingRecord[obj.id + "&" + obj2.id] = true;
					handlingRecord[obj2.id + "&" + obj.id] = true;
				}
			}
			
			//再次遍历
			for (var id:String in objList)
			{
				obj = objList[id] as CollisionObject;
				if (!obj.exist) delete objList[id]; //清空那些已经不存在的物件
				
				obj.changed = false; //重置当前物件的变化标记
			}
			
			changed = false; //重置标记
		}
		
		/**
		 * 两事物件的碰撞处理 处理是基于当前物件(第一个物件)的 主要分析物件2相对于当前物件的情况
		 * @param	obj 当前物件
		 * @param	obj2 物件2
		 */
		private function handleCollision(obj:CollisionObject, obj2:CollisionObject):void
		{
			if (!obj.exist) return; //以下代码都是基于"当前物件存在"的前提的
			
			var event:CollisionEvent;
			
			var collisionArea:Rectangle;
			if (obj2.exist)
			{
				collisionArea = obj.collisionRange.intersection(obj2.selfRange); //计算物件2占当前物件碰撞区的范围
				if (collisionArea.isEmpty()) collisionArea = null; //没碰到 将collisionArea置空
				else //否则 将collisionArea转换成相对量 如果不转成相对量 之后摩擦比对时可能会比对不出两次的差异
				{
					collisionArea.x -= obj.selfRange.x;
					collisionArea.y -= obj.selfRange.y;
				}
			}
			
			if (obj.collisionInfo[obj2.id] == null) //如果当前物件的碰撞信息表内没有关于物件2的碰撞记录
			{
				if (collisionArea) //而目前有碰到 说明两者刚碰上
				{
					obj.collisionInfo[obj2.id] = collisionArea; //在当前物件的碰撞信息表内计入与物件2的碰撞
					event = new CollisionEvent(CollisionEvent.COLLISION_IN, obj.id, obj2.id, collisionArea); //新建接触事件
				}
			}
			else //如果当前物件已经处于和物件2相撞的状态
			{
				if (collisionArea) //目前有碰到 需要与原来的情况比对 如果发生变化 说明两物件发生了摩擦
				{
					if (!(obj.collisionInfo[obj2.id] as Rectangle).equals(collisionArea)) //如果新的碰撞区和原来的不一样
					{
						obj.collisionInfo[obj2.id] = collisionArea; //在当前物件的碰撞信息表内计入与物件2的碰撞
						event = new CollisionEvent(CollisionEvent.COLLISION_OVER, obj.id, obj2.id, collisionArea); //新建摩擦事件
					}
				}
				else //如果没碰上 说明两者已经从接触状态分开了
				{
					delete obj.collisionInfo[obj2.id]; //删掉原先的碰撞记录
					event = new CollisionEvent(CollisionEvent.COLLISION_OUT, obj.id, obj2.id); //新建分离事件
				}
			}
			
			if (event) EventManager.getInstance().dispatchEvent(event); //抛出事件
			//if(event) trace(event.toString());
		}
		
	}

}