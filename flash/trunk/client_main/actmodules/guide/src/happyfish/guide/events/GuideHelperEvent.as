package happyfish.guide.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class GuideHelperEvent extends Event 
	{
		public var data:*;
		public function GuideHelperEvent(type:String, data:*) 
		{ 
			super(type, bubbles, cancelable);
			this.data = data;
		} 
		
		public override function clone():Event 
		{ 
			return new GuideHelperEvent(type, data);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("GuideEventHelperEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}