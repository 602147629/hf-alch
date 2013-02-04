package happymagic.order.events 
{
	import flash.events.Event;
	import happymagic.model.vo.order.OrderVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class OrderViewEvent extends Event 
	{
		
		public static const SHOW_ORDER_DESCRIPTION_VIEW:String = "showOrderDescriptionView";
		
		public var order:OrderVo;
		
		public function OrderViewEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new OrderViewEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("OrderViewEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}