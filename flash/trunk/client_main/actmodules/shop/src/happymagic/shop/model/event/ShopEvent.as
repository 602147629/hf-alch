package happymagic.shop.model.event 
{
	import flash.events.Event;
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class ShopEvent extends Event 
	{
		
		public function ShopEvent(type:String) 
		{
			super(type);
		}
		
		public static const CLOSE:String = "closeShop";
	}

}