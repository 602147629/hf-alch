package happymagic.order.view 
{
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.manager.module.ModuleManager;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.order.OrderType;
	import happymagic.model.vo.order.OrderVo;
	import happymagic.order.view.ui.MyOrderIconUI;
	import happymagic.order.vo.ModuleType;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class MyOrderIcon extends MyOrderIconUI
	{
		public function MyOrderIcon() 
		{
			state.mouseChildren = false;
			state.mouseEnabled = false;
			mouseEnabled = false;
			txt.mouseEnabled = false;
			txt.text = 0+"";
			
			addEventListener(MouseEvent.CLICK, clickHandler);
			
			state.gotoAndStop("Normal");
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			state.gotoAndStop("Normal");
			var ui:MyOrderListUISprite = ModuleManager.getInstance().showModule(ModuleType.MY_ORDER_LIST_NAME) as MyOrderListUISprite;
			DisplayManager.uiSprite.setBg(ui);
			ui.show();
		}
		
		public function refreshState():void
		{
			var list:Vector.<OrderVo> = DataManager.getInstance().orderData.getAcceptOrderList();
			var len:int = list ? list.length : 0;
			txt.text = len + "";
			var hasFailed:Boolean = false;
			var hasCompleted:Boolean = false;
			for (var i:int = 0; i < len; i++)
			{
				if (!hasCompleted && OrderType.COMPLETED == list[i].state)
				{
					hasCompleted = true;
					break;
				}
				if (!hasFailed && OrderType.FAILED == list[i].state)
				{
					hasFailed = true;
				}
			}
			if (hasCompleted) state.gotoAndStop("Completed");
			else if (hasFailed) state.gotoAndStop("Failed");
			else state.gotoAndStop("Normal");
		}
		
	}

}