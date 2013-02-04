package happymagic.display.view.promptFrame 
{
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	/**
	 * ...
	 * @author ZC
	 */
	//经营等级不足提示面板
	public class BusinessLevelNoEnoughView extends UISprite
	{
		
		private var iview:BusinessLevelNoEnoughViewUi;
		
		public function BusinessLevelNoEnoughView() 
		{
			_view = new BusinessLevelNoEnoughViewUi();
			
			iview = _view as BusinessLevelNoEnoughViewUi;
			
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "closebtn":
				case "ok":
					closeMe(true);
					break;
			}
		}
		
		public function setData(level:int):void
		{
			iview.num.text = level.toString();
		}
		
	}

}