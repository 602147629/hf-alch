package happymagic.display.view.ui 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.geom.Rectangle;
	import happyfish.display.control.FloatController;
	import happyfish.display.view.IconView;
	import happyfish.scene.world.grid.IsoItem;
	/**
	 * 人物头顶的心情泡
	 * @author jj
	 */
	public class PersonPaoView extends Sprite
	{
		private var target:IsoItem;
		private var iconClass:String;
		private var bubble:IconView;
		private var bg:ui_tanchupao;
		private var floatControl:FloatController;
		
		public function PersonPaoView(_target:IsoItem,_iconClass:String="",showPao:Boolean=false,size:Number=26 ) 
		{
			target = _target;
			iconClass = _iconClass;
			
			mouseEnabled = mouseChildren = false;
			
			if (showPao) 
			{
				bg = new ui_tanchupao();
				bg.cacheAsBitmap = true;
				addChild(bg);
			}
			if (bg) 
			{
				bubble = new IconView(size, size, new Rectangle( -11, -35, 22, 22));
			}else {
				bubble = new IconView(size, size, new Rectangle( -11, -35 + 15, 22, 22));
			}
			
			if(iconClass) setIconClass(iconClass);
			addChild(bubble);
			
			target.view.container.addChild(this);
			
			initPosition();
		}
		
		public function setIconClass(className:String):void {
			iconClass = className;
			bubble.addEventListener(Event.COMPLETE, iconCompleteHandler);
			bubble.setData(iconClass);
		}
		
		private function iconCompleteHandler(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, iconCompleteHandler);
			bubble.realIcon.play();
		}
		
		public function initPosition():void {
			if (bg) 
			{
				y = target.asset.getBounds(target.asset.parent).top;
			}else {
				y = target.asset.getBounds(target.asset.parent).top;
			}
			
		}
		
		public function remove():void {
			if (parent) 
			{
				parent.removeChild(this);
			}
			if (floatControl) 
			{
				floatControl.stop();
				floatControl = null;
			}
			if (bubble)
			{
				bubble.removeEventListener(Event.COMPLETE, iconCompleteHandler);
				if (bubble.realIcon) bubble.realIcon.stop();
			}
			
			target = null;
		}
		
		/**
		 * 设置泡泡漂浮
		 * @param	float		漂浮距离
		 * @param	duration	间隔时间,秒 
		 */
		public function setFloat(float:int,duration:Number=2):void 
		{
			if (!floatControl) {
				floatControl = new FloatController(this, float,duration);
			}else {
				floatControl.stop();
				floatControl.duration = duration;
				floatControl.height = float;
			}
			floatControl.start();
		}
		
	}

}