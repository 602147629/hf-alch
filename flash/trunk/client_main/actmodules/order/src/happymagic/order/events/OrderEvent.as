package happymagic.order.events 
{
	import flash.events.Event;
	import happymagic.model.vo.order.OrderVo;
	import happymagic.model.vo.ResultVo;
	import happymagic.order.flow.SpreadItemFlow;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class OrderEvent extends Event 
	{
		
		public static const REJECT_ORDER:String = "rejectOrder";
		public static const ACCEPT_ORDER:String = "acceptOrder";
		public static const FAILED_ORDER:String = "failedOrder";
		public static const COMPLETE_ORDER:String = "completeOrder";
		public static const REFRESH_ORDER:String = "refreshOrder";
		public static const DELAY_ORDER:String = "delayOrder";
		
		public var id:String;
		
		// 仅当 ACCEPT_ORDER的时候才使用order
		public var order:OrderVo;
		
		// 仅当 COMPLETE_ORDER的时候才使用flow
		public var flow:SpreadItemFlow;
		
		public function OrderEvent(type:String, bubbles:Boolean = false, cancelable:Boolean = false, id:String = null, order:OrderVo = null, flow:SpreadItemFlow = null) 
		{ 
			super(type, bubbles, cancelable);
			this.id = id;
			this.order = order;
			this.flow = flow;
		} 
		
		public override function clone():Event 
		{ 
			return new OrderEvent(type, bubbles, cancelable, id, order, flow);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("OrderEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}