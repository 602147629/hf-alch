package happymagic.events 
{
	import flash.events.Event;
	import happymagic.model.vo.ItemVo;
	
	/**
	 * ...
	 * @author 
	 */
	public class DiyEvent extends Event 
	{
		public static const MOVE_ITEM:String = "diyMoveItem";
		public static const MIRROR_ITEM:String = "diyMirrorItem";
		public static const REMOVE_ITEM:String = "diyRemoveItem";
		public static const ADD_ITEM:String = "diyAddItem";
		
		public var item:ItemVo;
		public function DiyEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new DiyEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("DiyEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}