package happymagic.event 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class RoleWorkEvent extends Event 
	{
		public static var ROLEWORKMOUDLECLOSE:String = "roleworkmoudleclose";
		public static var ROLEWORKSELECTCLOSE:String = "roleworkselectclose";
		public static var ROLEWORKQUICKCOMPLETE:String = "roleworkquickcomplete";
		public static var ROLETIMEOVER:String = "roletimeover";
		public static var ROLEUPDATA:String = "roleupdata";
		
		public function RoleWorkEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new RoleWorkEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("RoleWorkEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}