package happymagic.event 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class StorageEvent extends Event 
	{
		public static var CLOSEMOUDLE:String = "storageEvent";
		public static var USEITEMCOMPLETE:String = "storageuseitemcomplete";
		
		public function StorageEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new StorageEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("StorageEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}