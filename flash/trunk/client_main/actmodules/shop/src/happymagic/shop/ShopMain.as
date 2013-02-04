package happymagic.shop
{
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.shop.controller.ShopUISprite;
	import happymagic.shop.model.event.ShopEvent;
	import happymagic.shop.view.ShopView;
	
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class ShopMain extends ActModuleBase
	{
		override public function init(actVo:ActVo, _type:uint = 1):void 
		{
			super.init(actVo, 1);
			
			var shopUiSprite:ShopUISprite = DisplayManager.uiSprite.addModule("Shop", "happymagic.shop.controller.ShopUISprite") as ShopUISprite;
			var shopView:ShopView = shopUiSprite.view as ShopView;
			var itemList:Array = DataManager.getInstance()["shopItemList"];
			var matList:Array = DataManager.getInstance()["shopMatList"];
			shopView.setData(itemList, matList);
			
			EventManager.getInstance().addEventListener(ShopEvent.CLOSE, onClose);
		}
		
		private function onClose(event:ShopEvent):void
		{
			EventManager.getInstance().removeEventListener(ShopEvent.CLOSE, onClose);
			
			close();
		}
		
	}
	
}