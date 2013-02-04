package happymagic.view 
{
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author ZC
	 */
	public class SelectPlayerAddView extends UISprite
	{
		private var iview:SelectPlayerAddViewUi;
		private var list:StorageListView;
		
		public function SelectPlayerAddView() 
		{
			_view = new SelectPlayerAddViewUi();
			iview = _view as SelectPlayerAddViewUi;
			
		    list = new StorageListView(new StorageListViewUi(), iview, 3, true, false);
			list.init(240, 300, 229.95, 73, 0,0);
			list.setGridItem(SelectPlayerAddItemView, SelectPlayerAddItemViewUi);
			list.x = -116;
			list.y = -97;
			list.tweenTime = 0;				
			iview.addEventListener(MouseEvent.CLICK, clickrun);
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "closebtn":
					closeMe(true);
					break;
			}
		}
		
		public function setData(itemcid:int,conentarray:Array):void
		{
			DataManager.getInstance().setVar("itemcid", itemcid);
			DataManager.getInstance().setVar("conentarray", conentarray);

			list.setData(DataManager.getInstance().roleData.getMyRoles());			
		}
	}

}