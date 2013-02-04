package  
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class TigerMachineEvent extends Event 
	{
		public static var TIGERMACHCLOSEMOUDLUE:String =  "tigermachclosemoudlue";
		public static var TIGERCOMPLETE:String = "tigercomplete"; 
		
		public function TigerMachineEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new TigerMachineEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("TigerMachineEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}