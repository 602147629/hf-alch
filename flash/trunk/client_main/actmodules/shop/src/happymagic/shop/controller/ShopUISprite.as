package happymagic.shop.controller 
{
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happymagic.shop.model.event.ShopEvent;
	import happymagic.shop.view.ShopView;
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class ShopUISprite extends UISprite
	{
		
		public function ShopUISprite() 
		{
			super();
			this._view = new ShopView();
			EventManager.getInstance().addEventListener(ShopEvent.CLOSE, onClose);
		}
		
		private function onClose(event:ShopEvent):void
		{
			closeMe(true);
		}
		
	}

}