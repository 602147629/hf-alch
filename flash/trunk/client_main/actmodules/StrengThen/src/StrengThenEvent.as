package  
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class StrengThenEvent extends Event 
	{
		public static var STRENGTHENCLOSE:String = "strengthenclose";
		
		public static var STRENGTHENCHANGE:String = "strengthenchange";
		
		public function StrengThenEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new StrengThenEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("StrengThenEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}