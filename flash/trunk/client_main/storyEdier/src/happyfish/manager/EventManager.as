package happyfish.manager
{
	import flash.events.Event;
	import flash.events.EventDispatcher;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class EventManager extends EventDispatcher
	{
		
		public static function addEventListener(type:String, listener:Function, useCapture:Boolean = false, priority:int = 0, useWeakReference:Boolean = false):void
		{
			dispatcher.addEventListener(type, listener, useCapture, priority, useWeakReference);
		}
		
		public static function dispatchEvent(event:Event):Boolean
		{
			return dispatcher.dispatchEvent(event);
		}
		
		public static function hasEventListener(type:String):Boolean
		{
			return dispatcher.hasEventListener(type);
		}
		
		public static function removeEventListener(type:String, listener:Function, useCapture:Boolean = false):void
		{
			dispatcher.removeEventListener(type, listener, useCapture);
		}
		
		public static function willTrigger(type:String):Boolean
		{
			return dispatcher.willTrigger(type);
		}
		
		public static function getInstance():EventDispatcher
		{
			return dispatcher;
		}
		private static const dispatcher:EventDispatcher = new EventDispatcher();
	}
	
}