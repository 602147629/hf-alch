package happymagic.roleInfo.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class RoleInfoActEvent extends Event 
	{
		public static const ROLE_INFO_CLOSE:String = "roleInfoClose";
		
		public function RoleInfoActEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new RoleInfoActEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("RoleInfoActEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}