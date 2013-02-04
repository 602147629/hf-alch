package happyfish.display.control 
{
	import com.greensock.TweenLite;
	import flash.display.DisplayObject;
	import flash.events.TimerEvent;
	import flash.utils.Timer;
	/**
	 * 可以控制可视对象进行漂浮
	 * @author XiaJunJie
	 */
	public class FloatController 
	{
		private var target:DisplayObject;
		private var tween:TweenLite;
		private var timer:Timer;
		private var oriHeight:Number;
		
		private var reverse:Boolean;
		
		public var height:int;
		public var duration:Number;
		
		/**
		 * 
		 * @param	target 要漂浮的显示对象
		 * @param	height 浮动高度
		 * @param	duration 历时 单位:秒
		 */
		public function FloatController(target:DisplayObject, height:int = 10, duration:Number = 2)
		{
			this.target = target;
			oriHeight = target.y;
			this.height = height;
			this.duration = duration;
			
			start();
		}
		
		public function start():void
		{
			if (tween) stop();
			tween = TweenLite.to(target, duration / 2, { y:height + oriHeight, onComplete:onTweenComplete } );
			
			timer = new Timer(duration*1000, 1);
			timer.addEventListener(TimerEvent.TIMER_COMPLETE, onTimerComplete);
			timer.start();
		}
		
		private function onTweenComplete():void
		{
			tween.reverse(false);
		}
		
		private function onTimerComplete(event:TimerEvent):void
		{
			tween.restart();
			timer.start();
		}
		
		public function stop():void
		{
			if (!target) return;
			
			if (timer)
			{
				timer.stop();
				timer.removeEventListener(TimerEvent.TIMER_COMPLETE, onTimerComplete);
			}
			
			TweenLite.killTweensOf(target);
			tween = null;
			
			target.y = oriHeight;
		}
	}

}