package happymagic.order.commands 
{
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.order.OrderVo;
	import happymagic.order.events.OrderEvent;
	/**
	 * ...
	 * @author lite3
	 */
	public class RejectOrderCommand
	{
		
		private var command:BaseDataCommand;
		
		private var id:String;
		private var requestOrder:Boolean;
		private var completeCallback:Function;
		
		/**
		 * 拒绝订单
		 * @param	id 要拒绝的订单id
		 */
		public function reject(id:String, requestOrder:Boolean, completeCallback:Function):void
		{
			this.id = id;
			this.requestOrder = requestOrder;
			this.completeCallback = completeCallback;
			command = new BaseDataCommand();
			command.loadCompleteHandler = loadCompleteHandler;
			command.createLoad();
			command.createRequest(InterfaceURLManager.getInstance().getUrl("rejectOrder"), { id:id, request:requestOrder ? 1 : 0 } );
			command.loadData();
		}
		
		private function loadCompleteHandler():void 
		{
			var success:Boolean = command.data.result.isSuccess;
			
			if (success)
			{
				if (command.objdata.order && requestOrder)
				{
					var order:OrderVo = new OrderVo();
					order.setData(command.objdata.order);
				}
			}
			EventManager.dispatchEvent(new OrderEvent(OrderEvent.REJECT_ORDER, false, false, id, order));
			
			if (completeCallback != null) completeCallback(success);
		}
		
	}

}