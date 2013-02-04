package happymagic.roleInfo.view 
{
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.roleInfo.view.ui.ExpandRoleErrorUI;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class ExpandRoleErrorUISprite extends UISprite 
	{
		
		private var iview:ExpandRoleErrorUI;
		
		public function ExpandRoleErrorUISprite() 
		{
			iview = new ExpandRoleErrorUI();
			_view = iview;
			TextFieldUtil.autoSetDefaultFormat(iview.txt);
			iview.addEventListener(MouseEvent.CLICK, clickHandler);
		}
		
		public function setData(level:int):void
		{
			iview.txt.text = level + "";
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			switch(e.target)
			{
				case iview.closeBtn :
				case iview.okBtn :
					closeMe(true);
					break;
			}
		}
	}

}