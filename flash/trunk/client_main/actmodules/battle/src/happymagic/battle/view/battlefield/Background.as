package happymagic.battle.view.battlefield 
{
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.events.Event;
	import flash.geom.Point;
	import happyfish.cacher.SwfClassCache;
	import happyfish.events.SwfClassCacheEvent;
	
	/**
	 * 背景
	 * @author jj
	 */
	public class Background extends Bitmap
	{
		public var className:String;
		
		public function Background() 
		{
			super(new BitmapData(640, 445, true, 0x733F0B), "auto", true);
			
		}
		
		public function loadClass(_className:String):void
		{
			className = _className;
			
			if (SwfClassCache.getInstance().hasClass(className)) create();
			else
			{
				SwfClassCache.getInstance().addEventListener(SwfClassCacheEvent.COMPLETE, loadClass_complete);
				SwfClassCache.getInstance().loadClass(className);
			}
		}
		
		private function loadClass_complete(e:SwfClassCacheEvent):void 
		{
			if (e.className != className) return;
			SwfClassCache.getInstance().removeEventListener(SwfClassCacheEvent.COMPLETE, loadClass_complete);
			create();
		}
		
		private function create():void
		{
			var tmpclass:Class = SwfClassCache.getInstance().getClass(className);
			smoothing = true;
			bitmapData = new tmpclass(640, 445) as BitmapData;
			
			dispatchEvent(new Event(Event.COMPLETE));
		}
		
		/**
		 * 碰撞测试
		 * @param	point: 碰撞点 全局坐标 相对于stage
		 */
		public function hitTest(point:Point):Boolean
		{
			if (!stage) return false;
			if (!bitmapData) return false;
			var localPoint:Point = globalToLocal(point);
			var alpha:uint = bitmapData.getPixel32(localPoint.x, localPoint.y) >> 24 & 0xFF;
			return alpha >= 0x11;
		}
		
	}
}