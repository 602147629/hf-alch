package happyfish.editer.scene.view 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import happyfish.editer.model.EditDataManager;
	import happyfish.editer.model.vo.MapClassVo;
	/**
	 * ...
	 * @author ...
	 */
	public class EditIsoLayer extends Sprite
	{
		private var sortAble:Boolean;
		private var sortCount:uint;
		private var sortDelay:uint=10;
		private var needSort:Boolean;
		public var items:Object;
		protected var _objects:Array;
		public function EditIsoLayer(_sortAble:Boolean=false) 
		{
			sortAble = _sortAble;
			_objects = new Array();
			items = new Object();
			
			if (_sortAble) 
			{
				addEventListener(Event.ENTER_FRAME, sortFun);
			}
		}
		
		public function addIsoChild(child:EditIsoItem):void
		{
			if (items[child.data.x + "_" + child.data.z+"_"+child.sortPriority]) 
			{
				return;
			}
			addChild(child.asset);
			_objects.push(child);
			
			items[child.data.x + "_" + child.data.z+"_"+child.sortPriority] = child;
			
			sort();
		}
		
		public function sort():void
		{
			needSort = true;
		}
		
		public function sortFun(e:Event=null):void {
			//return;
			
			sortCount++;
			if (sortCount>sortDelay) 
			{
				if (!needSort) 
				{
					return;
				}
				_objects.sortOn("name");
				
				_objects.sortOn("depth", Array.NUMERIC);
				
				for(var i:int = 0; i < _objects.length; i++)
				{
					if (getChildIndex(_objects[i].asset)!=i) 
					{
						setChildIndex(_objects[i].asset, i);
					}
				}
				sortCount = 0;
				needSort = false;
			}
		}
		
		public function removeIsoChild($isoSprite:EditIsoItem):void
		{
			if ($isoSprite.asset.parent) 
			{
				removeChild($isoSprite.asset);
			}
			
			var index:int = _objects.indexOf($isoSprite);
			_objects.splice(index, 1);
			
			items[$isoSprite.data.x + "_" + $isoSprite.data.z+"_"+$isoSprite.sortPriority] = null;
			
			$isoSprite = null;
		}
		
		public function clear():void {
			while (numChildren>0) 
			{
				removeChildAt(0);
			}
			_objects = new Array();
			items = new Object();
		}
		
		public function moveItemByPos(item:EditIsoItem, x:Number, z:Number, sortP:Number = 0):void {
			if (x<0 || x>mapClass.numCols-1 || z<0 || z>mapClass.numCols-1) 
			{
				//trace("非法位置" + x + " " + z );
				return;
			}
			
			if (items[x + "_" + z+"_"+sortP]) 
			{
				//trace("此位置：" + x + " " + z + " 已有物件" + item.data.name );
				return;
			}else {
				//trace("移动到");
				items[item.data.x + "_" + item.data.z + "_" + item.sortPriority] = null;
				item.setGridPos(x,z);
				items[x + "_" + z + "_" + sortP] = item;
			}
			sort();
		}
		
		public function getItemByPos(x:Number, z:Number,sortP:Number=0):EditIsoItem 
		{
			return items[x + "_" + z+"_"+sortP];
		}
		
		public function get objects():Array 
		{
			return _objects;
		}
		
		public function set objects(value:Array):void 
		{
			_objects = value;
		}
		
		public function get mapClass():MapClassVo {
			return EditDataManager.getInstance().curMapClass;
		}
	}

}