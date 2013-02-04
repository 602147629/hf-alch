package happymagic.roleInfo.view 
{
	import flash.events.MouseEvent;
	import happyfish.display.ui.RadioButton;
	import happyfish.display.ui.RadioButtonGroup;
	import happyfish.display.view.UISprite;
	import happyfish.utils.display.FiltersDomain;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.roleInfo.commands.ExpandRoleLimitCommand;
	import happymagic.roleInfo.view.ui.ExpandRoleSelectPriceUI;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class ExpandRoleSelectPriceUISprite extends UISprite 
	{
		private var radioGroup:RadioButtonGroup;
		private var iview:ExpandRoleSelectPriceUI;
		
		private var expandCallback:Function;
		
		private var count:int;
		private var coin:int;
		private var gem:int;
		
		public function ExpandRoleSelectPriceUISprite() 
		{
			iview = new ExpandRoleSelectPriceUI();
			_view = iview;
			
			iview.coinRadio.mouseEnabled = false;
			iview.coinRadio.txt.mouseEnabled = false;
			iview.gemRadio.mouseEnabled = false;
			iview.gemRadio.txt.mouseEnabled = false;
			
			TextFieldUtil.autoSetDefaultFormat(iview.coinRadio.txt);
			TextFieldUtil.autoSetDefaultFormat(iview.gemRadio.txt);
			
			radioGroup = new RadioButtonGroup(new RadioButton(iview.coinRadio), new RadioButton(iview.gemRadio));
			
			iview.addEventListener(MouseEvent.CLICK, clickHandler);
		}
		
		public function setData(count:int, coin:int, gem:int, expandCallback:Function):void
		{
			this.count = count;
			this.coin = coin;
			this.gem = gem;
			this.expandCallback = expandCallback;
			iview.coinRadio.txt.text = coin + "";
			iview.coinRadio.mouseChildren = coin > 0;
			iview.coinRadio.filters = coin > 0 ? [] : [FiltersDomain.grayFilter];
			iview.gemRadio.txt.text = gem + "";
			iview.gemRadio.mouseChildren = gem > 0;
			iview.gemRadio.filters = gem > 0 ? [] : [FiltersDomain.grayFilter];
			
			radioGroup.selectedIndex = coin > 0 ? 0 : 1;
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			switch(e.target)
			{
				case iview.closeBtn :
					closeMe(true);
					break;
					
				case iview.okBtn :
					var selectGem:Boolean = 1 == radioGroup.selectedIndex;
					var money:int = selectGem ? gem : coin;
					var curMoney:int = selectGem ? DataManager.getInstance().currentUser.gem :DataManager.getInstance().currentUser.coin;
					if (curMoney < money)
					{
						if (selectGem) DisplayManager.showGemNoEnoughView();
						else DisplayManager.showCoinNoEnoughView();
						closeMe(true);
						break;
					}
					
					new ExpandRoleLimitCommand().expandRole(count, radioGroup.selectedIndex + 1, expandCallback);
					closeMe(true);
					break;
			}
		}
		
		override public function closeMe(del:Boolean = false):void 
		{
			this.expandCallback = null;
			super.closeMe(del);
		}
		
	}

}