package  
{
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	/**
	 * ...
	 * @author ZC
	 */
	public class TigerMachineHelpView extends UISprite
	{
		private var iview:TigerMachineHelpViewUi;
		public function TigerMachineHelpView() 
		{
			_view = new TigerMachineHelpViewUi();
			iview = _view as TigerMachineHelpViewUi;
			
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
		
	}

}