package happymagic.illustratedHandbook.event 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class IllustratedHandbookEvent extends Event 
	{
		public static var ILLUSTRATESELECT:String = "illustratedselect";
		public static var ILLUSTRATEDHANDBOOKVIEWCHANGE:String = "illustratedhandbookviewchange";
		public static var ILLUSTRATEDHANDBOOKVIEWCHANGECOMPLETE:String = "illustratedhandbookviewchangecomplete";		
		public static var ILLUSTRATEMODULECLOSE:String = "illustratedmoudleclose";
		public var obj:Object;
		public function IllustratedHandbookEvent(type:String, _obj:Object = null,bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			obj = _obj;
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new IllustratedHandbookEvent(type, obj,bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("IllustratedHandbookEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}