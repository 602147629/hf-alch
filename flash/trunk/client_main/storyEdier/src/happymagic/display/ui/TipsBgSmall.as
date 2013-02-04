package happymagic.display.ui 
{
	import flash.geom.Rectangle;
	/**
	 * ...
	 * @author lite3
	 */
	public class TipsBgSmall extends TipsBgSmallUI
	{
		public const padding:Rectangle = new Rectangle();
		private var arrowGap:Number = 0;
		
		public function TipsBgSmall() 
		{
			mouseEnabled = false;
			mouseChildren = false;
			arrowGap = -bg.y - bg.height;
			
			padding.left = 4;
			padding.right = 4;
			padding.top = 4;
			padding.bottom = arrowGap + 4;
		}
		
		override public function set width(value:Number):void 
		{
			bg.width = value;
			bg.x = -value * 0.5;
		}
		
		override public function set height(value:Number):void 
		{
			bg.height = value - arrowGap;
			bg.y = -value;
		}
		
	}

}