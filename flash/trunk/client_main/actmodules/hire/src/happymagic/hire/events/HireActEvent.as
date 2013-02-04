package happymagic.hire.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class HireActEvent extends Event 
	{
		public static const HIRE_CLOSE:String = "hireClose";
		
		public function HireActEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new HireActEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("HireActEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}