package happyfish.editer.scene.view 
{
	import com.friendsofed.isometric.IsoUtils;
	import com.friendsofed.isometric.Point3D;
	import flash.display.DisplayObjectContainer;
	import flash.display.Sprite;
	import flash.geom.Point;
	import happyfish.cacher.CacheSprite;
	import happyfish.editer.model.vo.EditIsoItemVo;
	import happyfish.editer.scene.view.EditIsoLayer;
	import happyfish.scene.iso.IsoSprite;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.WorldLayerDict;
	import happyfish.scene.world.WorldState;
	
	/**
	 * ...
	 * @author ...
	 */
	public class EditIsoItem 
	{
		private var _bodyCompleteCallBack:Function;
		private var _data:Object;
		private var container:EditIsoLayer;
		public var asset:CacheSprite;
		
		//iso下的像素坐标
		public var x:uint;
		public var y:uint;
		public var z:uint;
		public var sortPriority:Number=0;
		public function EditIsoItem($data:Object,_container:EditIsoLayer,__callBack:Function=null) 
		{
			_data = $data;
			container = _container;
			
			_bodyCompleteCallBack = __callBack;
			
			asset = new CacheSprite(false);
			asset.bodyComplete_callback = view_complete;
			
			if (data.hasOwnProperty("sortPriority")) 
			{
				sortPriority = data.sortPriority;
			}
			
			
			container.addIsoChild(this);
			
			makeView();
		}
		
		/**
		 * 设置刷新显示对象
		 * @param	className	素材类名
		 * @param	_callBack
		 */
		public function resetView(className:String = "", _callBack:Function = null):void {
			
			//container.removeIsoChild(this);
			
			_data.className = className;
			
			_bodyCompleteCallBack = _callBack;
			
			
			//asset = new CacheSprite(true);
			asset.clearView();
			asset.bodyComplete_callback = view_complete;
			
			//container.addIsoChild(this);
			
			//setGridPos(_data.x,data.z);
			
			if (_data.className) 
			{
				this.asset.className = _data.className;
			}else {
				view_complete();
			}
			
			
		}
		
		public function positionIsValid():Boolean
		{
			
			return true;
		}
		
		public function rotate():void {
			if (data.hasOwnProperty("mirror")) 
			{
				data.mirror = data.mirror==1 ? 0 : 1;
				initMirror();
			}
		}
		
		/**
		 * 设置镜像
		 * @param	value
		 */
		public function setMirror(value:int):void {
			if (data.hasOwnProperty("mirror")) 
			{
				data.mirror = value;
				initMirror();
			}
		}
		
		protected function initMirror():void 
		{
			if (data.hasOwnProperty("mirror")) 
			{
				if (data.mirror) 
				{
					asset.scaleX = -1;
				}else {
					asset.scaleX = 1;
				}
			}
		}
		
		/**
		 * 设置格子坐标位置
		 * @param	p	格子坐标
		 */
		public function setGridPos(_x:uint, _z:uint):void {
			data.x = _x;
			data.z = _z;
			
			var p:Point3D = IsoUtil.gridToIso(new Point3D(_x, 0, _z));
			x = p.x;
			y = p.y;
			z = p.z;
			
			initSceenPosition();
		}
		
		/**
		 * 设置iso下像素坐标
		 * @param	_x
		 * @param	_y
		 * @param	_z
		 */
		public function setIsoPos(_x:uint,_y:uint,_z:uint):void {
			x = _x;
			y = _y;
			z = _z;
			initSceenPosition();
		}
		
		/**
		 * 按当前可视对象的像素坐标设置DATA内GRID坐标
		 */
		public function initDataPosition():void {
			var p:Point3D = IsoUtil.isoToGrid(IsoUtil.screenToIso(new Point(asset.x, asset.y)));
			data.x = p.x;
			data.z = p.z;
		}
		
		/**
		 * 更新像素xy坐标
		 */
		private function initSceenPosition():void {
			var p:Point = IsoUtils.isoToScreen(IsoUtil.gridToIso(new Point3D(data.x, 0, data.z)));
			asset.x = p.x;
			asset.y = p.y;
		}
		
		protected function makeView():void 
		{
			setGridPos(_data.x,data.z);
			if (_data.className) 
			{
				this.asset.className = _data.className;
			}else {
				view_complete();
			}
			
		}
		
		private function view_complete():void
		{
			initMirror();
			if (_bodyCompleteCallBack!=null) 
			{
				_bodyCompleteCallBack.call();
				_bodyCompleteCallBack = null;
			}
		}
		
		public function get depth():Number {
			//return (x + z) * .866 - y * .707 - asset.height * .05 +asset.width * .02;
			return (x + z) * .866 - y * .707;
		}
		
		public function get name():String {
			return asset.name;
		}
		
		public function get data():Object 
		{
			return _data;
		}
		
		public function set data(value:Object):void 
		{
			_data = value;
		}
	}

}