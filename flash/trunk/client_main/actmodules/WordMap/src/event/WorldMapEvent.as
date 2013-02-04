package event 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class WorldMapEvent extends Event 
	{
		public static var WORLDMAPCLOSE:String = "worldmapclose";
		//绘制路线通知
		public static var DRAWMAPOPEN:String = "drawmapopen";
		
		public function WorldMapEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new WorldMapEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("WorldMapEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}