package happymagic.specialUpgrade.view 
{
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.specialUpgrade.view.ui.NotEnoughLevelUI;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class NotEnoughLevelUISprite extends UISprite 
	{
		private var iview:NotEnoughLevelUI;
		
		public function NotEnoughLevelUISprite() 
		{
			iview = new NotEnoughLevelUI();
			_view = iview;
			
			TextFieldUtil.autoSetDefaultFormat(iview.levelTxt);
			
			iview.okBtn.addEventListener(MouseEvent.CLICK, closeHandler);
		}
		
		public function setData(level:int):void
		{
			iview.levelTxt.text = level + "";
		}
		
		private function closeHandler(e:MouseEvent):void 
		{
			closeMe(true);
		}
		
	}

}