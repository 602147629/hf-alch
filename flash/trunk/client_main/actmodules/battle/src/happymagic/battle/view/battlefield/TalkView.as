package happymagic.battle.view.battlefield 
{
	import flash.display.Stage;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.geom.Point;
	import flash.text.TextFieldAutoSize;
	import flash.utils.Timer;
	import happymagic.battle.view.TalkBgUi;
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class TalkView extends TalkBgUi
	{
		private var timer:Timer;
		private var callback:Function;
		
		public function TalkView(target:Role,str:String,callback:Function,stage:Stage) 
		{
			mouseChildren = false;
			mouseEnabled = false;
			
			this.callback = callback;
			
			var p:Point = target.top;
			x = p.x;
			y = p.y;
			
			txt.autoSize = TextFieldAutoSize.LEFT;
			txt.wordWrap = true;
			txt.htmlText = str;
			
			bg.height = txt.textHeight + 30;
			txt.y = bg.y - 15;
			
			timer = new Timer(TALK_DURATION, 1);
			timer.addEventListener(TimerEvent.TIMER_COMPLETE, closeMe);
			timer.start();
			
			stage.addEventListener(MouseEvent.MOUSE_UP, closeMe);
		}
		
		public function closeMe(event:Event = null):void
		{
			timer.stop();
			timer.removeEventListener(TimerEvent.TIMER_COMPLETE, closeMe);
			stage.removeEventListener(MouseEvent.MOUSE_UP, closeMe);
			if (parent) parent.removeChild(this);
			
			callback();
		}
		
		public static const TALK_DURATION:int = 1000;
		
	}

}