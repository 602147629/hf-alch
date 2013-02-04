package happyfish.scene.fog
{
	import flash.display.DisplayObject;
	import flash.events.EventDispatcher;
	import flash.events.TimerEvent;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.net.SharedObject;
	import flash.utils.ByteArray;
	import flash.utils.Dictionary;
	import flash.utils.Timer;

	/**
	 * 
	 * @author XiaJunJie
	 */
	public class FogManager extends EventDispatcher
	{
		private static var _instance:FogManager;
		public static function getInstance():FogManager
		{
			if(!_instance) _instance = new FogManager;
			return _instance;
		}
		
		private var map:Vector.<Vector.<FogNode>>;
		private var explorers:Object; //探索者
		
		private var timer:Timer; //计时器
		private var saveList:Vector.<FogNode>; //存档列表
		
		private var sceneId:int;
		
		public function FogManager(saveInterval:int=5000)
		{
			timer = new Timer(saveInterval);
			timer.addEventListener(TimerEvent.TIMER,saveData);
			timer.start();
			
			saveList = new Vector.<FogNode>();
		}
		
		public function refreshScene(size_x:int, size_z:int, sceneId:int, initData:ByteArray, hasScenePic:Boolean = false):void
		{
			saveData();
			
			this.sceneId = sceneId;
			
			initData.position = 4;
			
			map = new Vector.<Vector.<FogNode>>;
			var changeList:Vector.<FogNode> = new Vector.<FogNode>();
			//由于每张地图都会在原有的尺寸上向外扩一圈 因此起始是从-1开始 至size+1格结束
			for (var i:int = -1; i < size_x + 1; i++)
			{
				var col:Vector.<FogNode> = new Vector.<FogNode>;
				for (var j:int = -1; j < size_z + 1; j++)
				{
					var fogNode:FogNode = new FogNode(i, j);
					if (initData.readByte() != 0)
					{
						fogNode.explored = true;
						changeList.push(fogNode);
					}
					col.push(fogNode);
				}
				map.push(col);
			}
			if (changeList.length > 0)
			{
				var event:FogEvent = new FogEvent(changeList);
				event.immediately = true;
				dispatchEvent(event);
			}
			
			explorers = new Object;
		}
		
		public function setNode(x:int,z:int,explored:Boolean = true):void
		{
			map[x][z].explored = explored;
		}
		
		public function addExplorer(id:String,x:int,z:int,range:int):void
		{
			if(!explorers) return;
			explorers[id] = new FogExplorer(id);
			updateExplorer(id,x,z,range);
		}
		
		/**
		 * 更新探索者的位置或探索范围
		 */ 
		public function updateExplorer(id:String,x:int,z:int,range:int=-1):void
		{
			if(!explorers) return;
			
			var explorer:FogExplorer = explorers[id] as FogExplorer;
			if (!explorer) return;
			
			if (id != "0") trace("异常迷雾探索者:" + id);
			
			var xChange:Boolean = x != explorer.x;
			var zChange:Boolean = z != explorer.z;
			var rangeChange:Boolean = !(range==-1 || range == explorer.range);
			
			if (xChange) explorer.x = x;
			if (zChange) explorer.z = z;
			if (rangeChange) explorer.range = range;
			
			explorer.changed = xChange || zChange || rangeChange;
			
			if (explorer.changed) scan();
		}
		
		public function removeExplorer(id:String):void
		{
			if(!explorers) return;
			if(explorers[id]!=null) delete explorers[id];
		}
		
		
		public function isNodeExplored(x:int,z:int):Boolean
		{
			if(!hasNode(x,z)) return false;
			return map[x][z].explored;
		}
		
		private function scan():void
		{
			var changeList:Vector.<FogNode> = new Vector.<FogNode>;
			
			for each(var explorer:FogExplorer in explorers)
			{
				if(!explorer.changed) continue;
				
				var rect:Rectangle = explorer.getRangeRect();
				for(var i:int=rect.left;i<rect.right;i++)
				{
					for(var j:int=rect.top;j<rect.bottom;j++)
					{
						if(hasNode(i,j) && !map[i][j].explored)
						{
							map[i][j].explored = true;
							changeList.push(map[i][j]);
							saveList.push(map[i][j].clone());
						}
					}
				}
				explorer.changed = false; //重置标记
			}
			
			if(changeList.length>0)
			{
				dispatchEvent(new FogEvent(changeList));
			}
		}
		
		private function hasNode(x:int,z:int):Boolean
		{
			if(!map) return false;
			if(x>=0 && x<map.length)
			{
				if(z>=0 && z<map[0].length) return true;
			}
			return false;
		}
		
		private function saveData(event:TimerEvent=null):void
		{
			if (saveList.length > 0)
			{
				dispatchEvent(new FogEvent(saveList, FogEvent.FOG_SAVE));
				saveList = new Vector.<FogNode>();
			}
		}
		
		public function clear():void
		{
			saveData();
			if (timer)
			{
				timer.stop();
				timer.removeEventListener(TimerEvent.TIMER, saveData);
			}
		}
		
		public static function getEmptySceneData(size_x:int, size_z:int):ByteArray
		{
			var result:ByteArray = new ByteArray;
			result.writeShort(size_x);
			result.writeShort(size_z);
			//每张地图都会在原有的基础上向外扩一圈 因此实际长宽都是原来的数+2
			for (var i:int = 0; i < size_x+2; i++)
			{
				for (var j:int = 0; j < size_z+2; j++)
				{
					result.writeByte(0);
				}
			}
			return result;
		}
		public static function writeNodeIntoByteArray(node:FogNode,ba:ByteArray):void
		{
			ba.position = 0;
			var size_x:int = ba.readShort();
			var size_z:int = ba.readShort();
			var pos:int = (node.x + 1) * (size_z + 2) + (node.z + 1) + 4;
			ba[pos] = node.explored ? 1 : 0;
		}
	}
}