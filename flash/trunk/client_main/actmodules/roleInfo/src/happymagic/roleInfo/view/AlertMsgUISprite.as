package happymagic.roleInfo.view 
{
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.roleInfo.commands.DismissRoleCommand;
	import happymagic.roleInfo.view.ui.AlertMsgUI;
	import happymagic.roleInfo.view.ui.ExpandRoleErrorUI;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class AlertMsgUISprite extends UISprite 
	{
		
		private var roleId:int;
		
		private var iview:AlertMsgUI;
		private var icon:IconView;
		
		public function AlertMsgUISprite() 
		{
			iview = new AlertMsgUI();
			_view = iview;
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			iview.addEventListener(MouseEvent.CLICK, clickHandler);
			
			var rect:Rectangle = new Rectangle(iview.npcBorder.x, iview.npcBorder.y, iview.npcBorder.width, iview.npcBorder.height);
			icon = new IconView(rect.width, rect.height, rect);
			iview.addChildAt(icon, iview.getChildIndex(iview.npcBorder));
			iview.removeChild(iview.npcBorder);
		}
		
		public function setData(id:int, name:String, chat:String, npc:String):void
		{
			roleId = id;
			iview.titleTxt.text = LocaleWords.getInstance().getWord("AreYouSureWantToDismissTheRoleXXX?", name);
			iview.chatTxt.text = chat;
			icon.setData(npc);
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			switch(e.target)
			{
				case iview.okBtn :
					new DismissRoleCommand().dismissRole(roleId);
				
				case iview.closeBtn :
				case iview.cancelBtn :
					closeMe(true);
					break;
			}
		}
	}

}