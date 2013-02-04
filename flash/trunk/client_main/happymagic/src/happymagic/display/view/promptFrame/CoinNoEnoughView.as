package happymagic.display.view.promptFrame 
{
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	/**
	 * ...
	 * @author ZC 
	 */
	//金币不足的提示框
	public class CoinNoEnoughView extends UISprite
	{
		private var iview:CoinNoEnoughViewUi;
		
		public function CoinNoEnoughView() 
		{
			_view = new CoinNoEnoughViewUi();
			
			iview = _view as CoinNoEnoughViewUi;
			
			iview.addEventListener(MouseEvent.CLICK, clickrun );
			
			
			
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
		
		public function setData():void
		{
			
		}
		
	}

}