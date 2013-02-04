package happymagic.shop.controller 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.filters.GlowFilter;
	import flash.geom.Rectangle;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.IconView;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	import happymagic.shop.model.event.ShopEvent;
	import happymagic.shop.view.ShopItemUi;
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class ShopItem extends GridItem
	{
		private var iview:ShopItemUi;
		private var _data:BaseItemClassVo;
		private var _tip:ShopItemTipUI;
		
		public function ShopItem(_uiview:MovieClip) 
		{
			super(_uiview);
			
			iview = _uiview as ShopItemUi;
			iview.addEventListener(MouseEvent.CLICK, clickrun);
			iview.addEventListener(MouseEvent.ROLL_OVER, onRollOver);
			iview.addEventListener(MouseEvent.ROLL_OUT, onRollOut);
			iview.mouseEnabled = iview.mouseChildren = true;
			
			//iview.buyBtn.visible = false;
			
			EventManager.getInstance().addEventListener(ShopEvent.CLOSE, onClose);
		}
		
		override public function setData(data:Object):void
		{
			_data = data as BaseItemClassVo;
			
			iview.itemNameTxt.text = _data.name;
			
			var itemIcon:IconView = new IconView(48, 48);
			itemIcon.setData(_data.className);
			while (iview.itemIconContainer.numChildren > 0) iview.itemIconContainer.removeChildAt(0);
			iview.itemIconContainer.addChild(itemIcon);
			
			if (_data.gem)
			{
				iview.moneyIcon.gotoAndStop(2);
				iview.priceTxt.text = "" + _data.gem;
			}
			else
			{
				iview.moneyIcon.gotoAndStop(1);
				iview.priceTxt.text = "" + _data.coin;
			}
			
		}
		
		private function clickrun(event:MouseEvent):void
		{
			switch(event.target.name)
			{
				case "buyBtn":
					//弹出购买框
					var moduleVo:ModuleVo = DataManager.getInstance().getModuleVo("buyItemPopUp");
					DisplayManager.uiSprite.addModuleByVo(moduleVo)["setData"](_data);
				break;
			}
		}
		
		private function onRollOver(event:MouseEvent):void
		{
			iview.filters = [new GlowFilter(0xFF6600, 1, 10, 10, 2.5, 1, false, false)];
			
			if (!_tip){
				_tip = new ShopItemTipUI;
				var rect:Rectangle = this.iview.getBounds(iview.parent);
				_tip.x = rect.right;
				_tip.y = (rect.top + rect.bottom) / 2;
			}
			_tip.content.text = _data.content;
			this.iview.parent.addChild(_tip);
		}
		
		private function onRollOut(event:MouseEvent):void
		{
			iview.filters = [];
			
			if (_tip && _tip.parent) _tip.parent.removeChild(_tip);
		}
		
		private function onClose(event:ShopEvent):void
		{
			iview.removeEventListener(MouseEvent.CLICK, clickrun);
			iview.removeEventListener(MouseEvent.ROLL_OVER, onRollOver);
			iview.removeEventListener(MouseEvent.ROLL_OUT, onRollOut);
			EventManager.getInstance().removeEventListener(ShopEvent.CLOSE, onClose);
		}
		
	}

}