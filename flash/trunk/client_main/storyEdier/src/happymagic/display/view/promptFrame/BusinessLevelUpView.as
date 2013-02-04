package happymagic.display.view.promptFrame 
{
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author ZC
	 */
	//经营等级升级提示面板
	public class BusinessLevelUpView extends UISprite
	{
		
		private var iview:BusinessLevelUpViewUi;
		
		public function BusinessLevelUpView() 
		{
			_view = new BusinessLevelUpViewUi();
			
			iview = _view as BusinessLevelUpViewUi;
			
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
		
		public function setData(_levelnum:int):void
		{
			iview.num.text = DataManager.getInstance().currentUser.level.toString();
		}
		
	}

}