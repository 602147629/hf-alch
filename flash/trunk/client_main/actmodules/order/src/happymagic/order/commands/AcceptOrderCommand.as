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
	public class AcceptOrderCommand
	{
		
		private var command:BaseDataCommand;
		
		private var id:String;
		private var completeCallback:Function;
		
		/**
		 * 接受订单
		 * @param	id 要接受的订单id
		 */
		public function accept(id:String, completeCallback:Function):void
		{
			this.id = id;
			this.completeCallback = completeCallback;
			command = new BaseDataCommand();
			command.loadCompleteHandler = loadCompleteHandler;
			command.createLoad();
			command.createRequest(InterfaceURLManager.getInstance().getUrl("acceptOrder"), { id:id } );
			command.loadData();
		}
		
		private function loadCompleteHandler():void 
		{
			var success:Boolean = command.data.result.isSuccess;
			
			if (success)
			{
				EventManager.dispatchEvent(new OrderEvent(OrderEvent.ACCEPT_ORDER, false, false, id));
			}
			
			if (completeCallback != null) completeCallback(success);
		}
		
	}

}