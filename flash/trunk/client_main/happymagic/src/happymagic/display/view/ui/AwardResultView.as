package happymagic.display.view.ui 
{
	import flash.events.MouseEvent;
	import happyfish.display.ui.Tooltips;
	import happyfish.display.view.UISprite;
	import happymagic.display.view.task.TaskNeedItemListView;
	import xrope.LayoutAlign;
	
	/**
	 * ...
	 * @author jj
	 */
	public class AwardResultView extends UISprite
	{
		private var iview:awardResultUi;
		private var data:Object;
		private var awardsMc:TaskNeedItemListView;
		
		public function AwardResultView() 
		{
			super();
			_view = new awardResultUi();
			
			iview = _view as awardResultUi;
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			awardsMc = new TaskNeedItemListView(new defaultListUi(),_view,4,true);
			awardsMc.x = -170;
			awardsMc.y = 10;
			
			awardsMc.init(280, 90, 60, 90, 34, -50, LayoutAlign.CENTER);
		}
		
		/**
		 * 
		 * @param	value	[Object] { name:面板上显示的名字，awards:[Array][] }
		 */
		public function setData(value:Object):void {
			data = value;
			
			if (data.awards) 
			{
				awardsMc.setData(data.awards);
			}
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case iview.closeBtn:
				closeMe(true);
				break;
				
				case iview.yesBtn:
				closeMe(true);
				break;
			}
		}
		
	}

}