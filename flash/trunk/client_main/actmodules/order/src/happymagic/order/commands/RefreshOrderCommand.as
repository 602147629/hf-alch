package happymagic.order.commands 
{
	import flash.events.Event;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.order.OrderVo;
	import happymagic.order.events.OrderEvent;
	/**
	 * ...
	 * @author lite3
	 */
	public class RefreshOrderCommand extends BaseDataCommand
	{
		
		private var id:String;
		private var completeCallback:Function;
		
		/**
		 * 接受订单
		 * @param	id 要接受的订单id
		 */
		public function refresh(completeCallback:Function):void
		{
			this.completeCallback = completeCallback;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("refreshOrder"));
			loadData();
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			if (result.isSuccess)
			{
				DataManager.getInstance().orderData.setRequestOrderList(objdata.list, false);
				EventManager.dispatchEvent(new OrderEvent(OrderEvent.REFRESH_ORDER));
			}
			
			
			if (completeCallback != null) completeCallback(result.isSuccess);
		}
	}

}