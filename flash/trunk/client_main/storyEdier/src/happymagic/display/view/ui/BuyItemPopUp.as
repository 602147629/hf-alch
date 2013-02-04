package happymagic.display.view.ui 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import happyfish.display.ui.NumSelecterView;
	import happyfish.display.view.IconView;
	import happyfish.display.view.UISprite;
	import happyfish.manager.local.LocaleWords;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.command.BuyItemCommand;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	import happymagic.model.vo.MoneyType;
	/**
	 * 购买物品框
	 * @author ...
	 */
	public class BuyItemPopUp extends UISprite
	{
		public var data:BaseItemClassVo;
		private var iview:BuyItemPopUpUi;
		private var numSelecter:NumSelecterView;
		private var icon:IconView;
		private	var type:uint;
		private	var num:uint;
		
		public function BuyItemPopUp() 
		{
			super();
			_view = new BuyItemPopUpUi();
			
			iview = _view as BuyItemPopUpUi;
			iview.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			numSelecter = new NumSelecterView(iview.numSelector,1);
			numSelecter.addEventListener(Event.CHANGE, numSelecterclickfun);
		}
		
		private function numSelecterclickfun(e:Event):void 
		{
			if (data.gem != 0) num = data.gem;
			else num = data.coin;
			
			num = num * e.target.num;
		    iview.priceTxt.text = num.toString();
		}
		
		public function setData(value:BaseItemClassVo, num:int = 1):void
		{
			data = value;
			
			iview.itemNameTxt.text = DataManager.getInstance().itemData.getItemClass(data.cid).name;
			numSelecter.setNum(num);
			
			if (data.gem) 
			{
				type = MoneyType.getPriceType(data);
				num = MoneyType.getPriceNum(data);
				iview.moneyIcon.gotoAndStop(2);
			}else {
				type = MoneyType.COIN;
				num = data.coin;
				iview.moneyIcon.gotoAndStop(1);
			}
			
            iview.priceTxt.text = num.toString();
			loadIcon();
		}
		
		private function loadIcon():void
		{
			if (icon) 
			{
				icon.parent.removeChild(icon);
				icon = null;
			}
			icon = new IconView(60, 60);
			icon.setData(data.className);
			iview.itemIconContainer.addChild(icon);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target.name) 
			{
				case "closeBtn":
					iview.removeEventListener(MouseEvent.CLICK, clickFun, true);
					numSelecter.removeEventListener(Event.CHANGE, numSelecterclickfun);
					closeMe(true);
				break;
				
				case "okBtn":
					if (data.gem == 0) //卖游戏币的
					{
						if (DataManager.getInstance().currentUser.coin < int(iview.priceTxt.text))
						{
							DisplayManager.showSysMsg(LocaleWords.getInstance().getWord("buyitemmsgcoin"));
						}
						else buyItem();
					}
				    else //卖充值币的
					{
						if (DataManager.getInstance().currentUser.gem < int(iview.priceTxt.text))
						{
							DisplayManager.showSysMsg(LocaleWords.getInstance().getWord("buyitemmsggem"));
						}
						else buyItem();
					}
				break;
			}
		}
		
		private function buyItem():void
		{
			var command:BuyItemCommand = new BuyItemCommand();
			command.addEventListener(Event.COMPLETE, buyItem_complete);
			command.buyItem(data.cid, numSelecter.num);
		}
		
		private function buyItem_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, buyItem_complete);
			iview.removeEventListener(MouseEvent.CLICK, clickFun, true);
			numSelecter.removeEventListener(Event.CHANGE, numSelecterclickfun);
			
			if (e.target.data.result.isSuccess) 
			{
				DisplayManager.showPiaoStr(5,LocaleWords.getInstance().getWord("buySuccess"),new Point(x,y));
			}
			closeMe(true);
		}
		
		
	}

}