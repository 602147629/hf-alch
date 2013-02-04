package happymagic.order.view 
{
	import happyfish.display.ui.defaultList.DefaultListView;
	import happymagic.display.view.ui.DefaultAwardItemRender;
	import happymagic.order.view.ui.OrderAwardListUI;
	import happymagic.order.view.ui.render.OrderAwardsItem;
	/**
	 * ...
	 * @author lite3
	 */
	public class OrderAwardList extends OrderAwardListUI
	{
		
		private var listView:DefaultListView;
		
		public function OrderAwardList() 
		{
			listView = new DefaultListView(this, this, 4);
			listView.init(320, 55, 70, 52, -240, -70);
			listView.setGridItem(OrderAwardsItem, DefaultAwardItemRender);
			listView.tweenDelay = 0;
			listView.tweenTime = 0;
		}
		
		public function setData(arr:Array):void
		{
			listView.setData(arr);
		}
		
	}

}