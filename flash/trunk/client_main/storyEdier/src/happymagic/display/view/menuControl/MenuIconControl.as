package happymagic.display.view.menuControl 
{
	import com.greensock.TweenMax;
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	/**
	 * ...
	 * @author 
	 */
	public class MenuIconControl 
	{
		private var icon:DisplayObject;
		private var offX:int;
		private var offY:int;
		private var layer:int;
		private var _enabled:Boolean; //开关，是否启用
		public var bg:MovieClip;
		
		public function MenuIconControl(icon:DisplayObject) 
		{
			this.icon = icon;
		}
		
		public function set enabled(value:Boolean):void {
			_enabled = value;
			visible = value;
			algin();
			TweenMax.from(this, .2, { x:"-10", alpha:0 } );
		}
		
		public function get enabled():Boolean {
			return _enabled;
		}
		
		public function get alpha():Number {
			return bg.alpha;
		}
		
		public function set alpha(value:Number):void {
			bg.alpha = value;
		}
		
		public function get visible():Boolean {
			return bg.visible;
		}
		
		public function set visible(value:Boolean):void {
			if (_enabled) 
			{
				bg.visible = value;
			}else {
				bg.visible = false;
			}
			bg.alpha = 1;
		}
		
		public function get x():Number 
		{
			return bg.x;
		}
		
		public function set x(value:Number):void 
		{
			bg.x = value;
		}
		
		public function get y():Number 
		{
			return bg.y;
		}
		
		public function set y(value:Number):void 
		{
			bg.y = value;
		}
		
		public function algin():void {
			
			bg.x = icon.x+offX;
			bg.y = icon.y+offY;
		}
		
		public function setBg(bg:MovieClip,offX:int,offY:int,layer:int=0):void {
			this.layer = layer;
			this.offY = offY;
			this.offX = offX;
			
			if (bg) hideBg();
			this.bg = bg;
			bg.mouseChildren = bg.mouseEnabled = false;
			showBg();
		}
		
		public function showBg():void {
			if (bg) 
			{
				icon.parent.addChildAt(bg, icon.parent.getChildIndex(icon)+layer);
				algin();
			}
		}
		
		public function hideBg():void {
			if (bg) 
			{
				if (bg.parent) bg.parent.addChild(bg);
			}
		}
		
	}

}