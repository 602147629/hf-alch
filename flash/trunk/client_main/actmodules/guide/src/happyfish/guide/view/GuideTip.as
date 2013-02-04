package happyfish.guide.view 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.Stage;
	import flash.events.Event;
	import happyfish.guide.interfaces.IGuideTip;
	import happyfish.utils.display.TextFieldUtil;
	/**
	 * ...
	 * @author lite3
	 */
	public class GuideTip extends GuideTipUI implements IGuideTip 
	{
		private var MIN_W:int;
		private var _stage:Stage;
		public function GuideTip() 
		{
			MIN_W = width;
			TextFieldUtil.autoSetDefaultFormat(txt);
			mouseChildren = false;
			mouseEnabled = false;
		}
		
		/**
		 * 将自己从显示列表里移除
		 */
		public function remove():void
		{
			if (parent) parent.removeChild(this);
			_stage.removeEventListener(Event.RESIZE, resizeHandler);
		}
		
		/**
		 * 显示一段话
		 * @param	str
		 */
		public function show(str:String):void
		{
			txt.text = str;
			_stage.addChild(this);
			_stage.addEventListener(Event.RESIZE, resizeHandler);
			resizeHandler(null);
		}
		
		private function resizeHandler(e:Event):void 
		{
			txt.width = txt.textWidth + 4;
			txt.height = txt.textHeight + 4;
			bg.width = txt.width + 80 <= MIN_W ? MIN_W : txt.width + 80;
			
			var centerX:int = _stage.stageWidth >> 1;
			var centerY:int = _stage.stageHeight >> 1;
			
			bg.x = centerX;
			bg.y = centerY;
			txt.x = centerX - (txt.width >> 1);
			txt.y = centerY - (txt.height >> 1);
		}
		
		/**
		 * 设置Stage
		 * @param	stage
		 */
		public function setStage(stage:Stage):void
		{
			_stage = stage;
		}
		
	}

}