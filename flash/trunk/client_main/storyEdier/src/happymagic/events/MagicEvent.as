package happymagic.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author 
	 */
	public class MagicEvent extends Event 
	{
		public static const UI_INIT:String = "uiInit";//uimanager的init完成
		public static const RIGHT_CENTER_INIT:String = "rightCenterMenuInit";//右侧主菜单条初始化完成
		
		public function MagicEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new MagicEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("MagicEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}