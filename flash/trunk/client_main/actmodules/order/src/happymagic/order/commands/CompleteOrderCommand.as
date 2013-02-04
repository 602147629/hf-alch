package happymagic.order.commands 
{
	import flash.events.Event;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.order.events.OrderEvent;
	import happymagic.order.flow.SpreadItemFlow;
	/**
	 * ...
	 * @author lite3
	 */
	public class CompleteOrderCommand extends BaseDataCommand
	{
		
		private var id:String;
		private var isFailed:Boolean;
		
		/**
		 * 完成订单
		 * @param	id 要接受的订单id
		 */
		public function complete(id:String, isFailed:Boolean):void
		{
			this.id = id;
			this.isFailed = isFailed;
			takeResult = false;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("completeOrder"), { id:id, isFailed:(isFailed ? 1 : 0) } );
			loadData();
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			if (!data.result.isSuccess) return;
			
			var type:String;
			var flow:SpreadItemFlow;
			if (!isFailed)
			{
				type = OrderEvent.COMPLETE_ORDER;
				var tip:int = objdata.tip;
				result.coin += tip;
				flow = new SpreadItemFlow(objdata.tip, data.result, data.addItems);
			}else
			{
				type = OrderEvent.FAILED_ORDER;
			}
			
			EventManager.dispatchEvent(new OrderEvent(type, false, false, id, null, flow));
		}
		
	}

}