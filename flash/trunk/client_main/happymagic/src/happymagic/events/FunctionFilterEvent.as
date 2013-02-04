package happymagic.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class FunctionFilterEvent extends Event 
	{
		public static const UNLOCK:String = "functionUnlock";
		
		public var name:String;
		
		public function FunctionFilterEvent(type:String, name:String) 
		{ 
			super(type);
			this.name = name;
		} 
		
		public override function clone():Event 
		{ 
			return new FunctionFilterEvent(type, name);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("FunctionFilterEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}