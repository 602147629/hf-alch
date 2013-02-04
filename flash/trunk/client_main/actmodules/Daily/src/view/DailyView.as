package view
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.ui.TabelView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.DiaryType;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class DailyView extends UISprite
	{
		private var iview:DailyViewUi;
		private var topTab:TabelView;
		private var list:DailyListView;
		private var currentselect:int;
		
		public function DailyView()
		{
			_view = new DailyViewUi();
			iview = _view as DailyViewUi;

		    list = new DailyListView(new DailyListViewUi(), iview, 4, false, false);
			list.init(570, 350, 550, 80, 0,0);
			list.setGridItem(DailyItemView, DailyItemViewUi);
			list.x = -278;
			list.y = -130.1;
			list.setButtonPosition(-15,150,580,150);
			list.tweenTime = 0;				
			
			topTab = new TabelView();
			iview.addChildAt(topTab,iview.getChildIndex(iview.ground));
			topTab.btwX = 3;
			topTab.x = 0;
			topTab.y = -180;
			topTab.setTabs([iview.FRIEND, DiaryType.FRIEND], [iview.INTERACTION, DiaryType.INTERACTION], [iview.SYSTEM, DiaryType.SYSTEM]);
			topTab.addEventListener(Event.SELECT, tab_select);
			topTab.select(0);

		
			
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			
			EventManager.getInstance().addEventListener(DailyEvent.DAILYDELETE, dailydelete);
		}
		
		private function tab_select(e:Event):void 
		{
			currentselect = (e.target as TabelView).selectValue;
			var arr:Array = DataManager.getInstance().diarys;
            switch((e.target as TabelView).selectValue)	
			{
				case DiaryType.FRIEND:
					 list.setData(DataManager.getInstance().diarys, "type", DiaryType.FRIEND);
					break;
					
				case DiaryType.INTERACTION:
					list.setData(DataManager.getInstance().diarys, "type", DiaryType.INTERACTION);
					break;					
					
				case DiaryType.SYSTEM:
					list.setData(DataManager.getInstance().diarys, "type", DiaryType.SYSTEM);
					break;					
			}
		}
		
		private function dailydelete(e:DailyEvent):void
		{
		     list.setData(DataManager.getInstance().diarys, "type", currentselect);
		}
		
		public function setData():void
		{
		
		}
		
		private function clickrun(e:MouseEvent):void
		{
			switch (e.target.name)
			{
				case "closebtn": 
					close();
					break;
			}
		}
		
		public function close():void
		{
			closeMe(true);
			EventManager.getInstance().removeEventListener(DailyEvent.DAILYDELETE, dailydelete);
			EventManager.getInstance().dispatchEvent(new DailyEvent(DailyEvent.DAILYCLOSE));
			
		}
	
	}

}