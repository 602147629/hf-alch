package happymagic.order.commands 
{
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.order.OrderVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class RequestOrderCommand
	{
		
		private var command:BaseDataCommand;
		private var callback:Function;
		
		/**
		 * 请求订单
		 * @param	callback 请求后的回调函数  function (vo:OrderVo);
		 */
		public function request(callback:Function, isNovice:Boolean = false):void
		{
			this.callback = callback;
			command = new BaseDataCommand();
			command.loadCompleteHandler = loadCompleteHandler;
			command.createLoad();
			var vars:Object = isNovice ? { isNovice:1 } : null;
			command.createRequest(InterfaceURLManager.getInstance().getUrl("requestOrder"), vars);
			command.loadData();
		}
		
		private function loadCompleteHandler():void 
		{
			if (!command.data.result.isSuccess) return;
			
			var order:OrderVo = new OrderVo();
			order.setData(command.objdata.order);
			//order.id = Math.random() * 10000 + "";
			
			callback(order);
			callback = null;
		}
		
	}

}