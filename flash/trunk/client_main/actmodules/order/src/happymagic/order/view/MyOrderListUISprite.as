package happymagic.order.view 
{
	import flash.display.DisplayObject;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.Pagination;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.order.OrderVo;
	import happymagic.order.events.OrderEvent;
	import happymagic.order.flow.SubmitFinishOrderMovieFlow;
	import happymagic.order.view.ui.MyOrderListRender;
	import happymagic.order.view.ui.MyOrderListUI;
	import happymagic.order.view.ui.render.MyOrderListItem;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class MyOrderListUISprite extends UISprite 
	{
		private var iview:MyOrderListUI;
		private var listView:DefaultListView;
		
		private var satisfactionBar:SatisfactionBar;
		
		private var needShow:Boolean = false;
		private var lastFlow:SubmitFinishOrderMovieFlow;
		
		public function MyOrderListUISprite() 
		{
			iview = new MyOrderListUI();
			_view = iview;
			iview.closeBtn.addEventListener(MouseEvent.CLICK, closeHandler);
			
			listView = new DefaultListView(iview, iview, 3, false, false);
			listView.init(468, 426, 468, 138, -234, -202);
			listView.setGridItem(MyOrderListItem, MyOrderListRender);
			listView.pagination = new Pagination();
			listView.pagination.y = 230;
			listView.pagination.x = 0
			
			satisfactionBar = new SatisfactionBar(iview.satisfactionBar);
			
			EventManager.addEventListener(OrderEvent.COMPLETE_ORDER, completeHandler, false, 10);
			iview.addEventListener(Event.CLOSE, closeHandler);
		}
		
		public function getFirstView():DisplayObject
		{
			return listView.getItemByIndex(0).view;
		}
		
		private function completeHandler(e:OrderEvent):void 
		{
			var list:Vector.<OrderVo> = DataManager.getInstance().orderData.getOrderList();
			var order:OrderVo = null;
			for (var i:int = list.length - 1; i >= 0; i--)
			{
				if (list[i].id == e.id)
				{
					order = list[i];
					break;
				}
			}
			if (!order) return;
			var item:GridItem = listView.getItemByValue(order);
			if (!item) return;
			
			var p:Point = item.view.localToGlobal(new Point(item.view.width / 2, item.view.height / 2));
			lastFlow = new SubmitFinishOrderMovieFlow().showAt(p.x, p.y, item.view.stage, flowComplete);
			
		}
		
		private function flowComplete(flow:SubmitFinishOrderMovieFlow):void 
		{
			if (flow != lastFlow) return;
			
			lastFlow = null;
			if (needShow) show();
		}
		
		private function closeHandler(e:Event):void 
		{
			e.stopImmediatePropagation();
			closeMe();
		}
		
		public function show():void
		{
			if (lastFlow)
			{
				needShow = true;
				return;
			}
			
			needShow = false;
			var list:Vector.<OrderVo> = DataManager.getInstance().orderData.getAcceptOrderList();
			if (!list || 0 == list.length)
			{
				closeMe();
				return;
			}
			listView.setData(list);
			
			EventManager.addEventListener(OrderEvent.FAILED_ORDER, refreshData);
			EventManager.addEventListener(OrderEvent.COMPLETE_ORDER, refreshData);
		}
		
		private function refreshData(e:OrderEvent):void 
		{
			show();
		}
		
		override public function closeMe(del:Boolean = false):void 
		{
			EventManager.removeEventListener(OrderEvent.FAILED_ORDER, refreshData);
			EventManager.removeEventListener(OrderEvent.COMPLETE_ORDER, refreshData);
			super.closeMe(del);
		}
		
	}

}