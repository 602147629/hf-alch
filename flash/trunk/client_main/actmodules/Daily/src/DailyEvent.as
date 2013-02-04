package  
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class DailyEvent extends Event 
	{
		public static var DAILYCLOSE:String = "dailyclose";
		public static var DAILYDELETE:String = "dailydelete";
		public function DailyEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new DailyEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("DailyEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}