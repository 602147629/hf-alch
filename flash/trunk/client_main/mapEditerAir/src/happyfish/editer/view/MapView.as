package happyfish.editer.view
{
	import flash.display.Bitmap;
	import flash.display.BitmapData;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Matrix;
	import flash.geom.Rectangle;
	import happyfish.cacher.SwfClassCache;
	import happyfish.events.SwfClassCacheEvent;
	import happymagic.scene.world.bigScene.BigSceneBg;
	/**
	 * ...
	 * @author jj
	 */
	public class MapView extends Bitmap
	{
		private var className:String;
		private var toFrame:uint;
		private var data:Object;
		private var bgtype:int;
		
		public function MapView() 
		{
			
		}
		
		public function setData(_className:String,_bgType:uint,frame:uint=0):void {
			toFrame = frame;
			className = _className;
			bgtype = _bgType;
			if (className) 
			{
				loadClass(className);
			}else {
				bgtype = 3;
				create();
			}
		}
		
		public function clear():void 
		{
			if(bitmapData) bitmapData.dispose();
			
		}
		
		private function loadClass(_className:String):void {
			className = _className;
			
			if (SwfClassCache.getInstance().hasClass(className)) 
			{
				create();
			}else {
				SwfClassCache.getInstance().addEventListener(SwfClassCacheEvent.COMPLETE, loadClass_complete);
				SwfClassCache.getInstance().loadClass(className);
			}
			
		}
		
		private function loadClass_complete(e:Event):void 
		{
			SwfClassCache.getInstance().removeEventListener(SwfClassCacheEvent.COMPLETE, loadClass_complete);
			create();
		}
		
		private function create():void
		{
			var tmpclass:Class = SwfClassCache.getInstance().getClass(className);
			smoothing = true;
			var rect:Rectangle;
			var bd:BitmapData;
			switch (bgtype) 
			{
				case 1:
					//图片
					bitmapData = new tmpclass(2000, 1300) as BitmapData;
					rect = bitmapData.rect;
				break;
				
				case 2:
					var tmpmc:MovieClip = new tmpclass() as MovieClip;
					tmpmc.gotoAndStop(toFrame);
					rect = tmpmc.getRect(tmpmc);
					var tmpmatrix:Matrix = new Matrix();
					tmpmatrix.translate(-rect.x,-rect.y);
					
					bd = new BitmapData(tmpmc.width, tmpmc.height, true, 0xffffff);
					bd.draw(tmpmc, tmpmatrix);
					bitmapData = bd;
				break;
				
				case 3:
					bd = new BitmapData(2000, 1300, false, 0x000000);
					bitmapData = bd;
					rect = bitmapData.rect;
				break;
			}
			
			x = -rect.width/2;
			y = -rect.height/2;
		}
	}

}