package happymagic.display.view.promptFrame 
{
	import flash.events.MouseEvent;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.view.UISprite;
	import happymagic.display.view.ui.DefaultAwardItemRender;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class GetNewItemsView extends UISprite 
	{
		private var iview:GetNewItemsViewUI;
		private var listView:DefaultListView;
		
		public function GetNewItemsView() 
		{
			iview = new GetNewItemsViewUI();
			_view = iview;
			
			listView = new DefaultListView(iview, iview, 5, false, false);
			listView.setGridItem(GetNewItemsItem, DefaultAwardItemRender);
			listView.init(iview.width, 67, 56, 67, 0, 68, "C", "C");
			
			iview.addEventListener(MouseEvent.CLICK, clickHandler);
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			switch(e.target)
			{
				case iview.closeBtn :
					closeMe(true);
					break;
					
				case iview.shareBtn :
					closeMe(true);
					break;
			}
		}
		
		public function setData(list:Array):void
		{
			listView.setData(list);
		}
		
	}

}