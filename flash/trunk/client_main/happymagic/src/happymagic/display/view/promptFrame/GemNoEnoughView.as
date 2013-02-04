package happymagic.display.view.promptFrame 
{
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	/**
	 * ...
	 * @author ZC
	 */
	//宝石不足的提示框
	public class GemNoEnoughView extends UISprite
	{
		private var iview:GemNoEnoughViewUi;
		
		public function GemNoEnoughView() 
		{
			_view = new GemNoEnoughViewUi();
			
			iview = _view as GemNoEnoughViewUi;
			
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "closebtn":
					closeMe(true);
					break;
					
				case "pay":
					//支付
					break;
			}
		}
		
		public function setData():void
		{
			
		}
		
	}

}