package happymagic.display.view.ui 
{
	import flash.geom.Rectangle;
	import happyfish.utils.display.TextFieldUtil;
	/**
	 * ...
	 * @author lite3
	 */
	dynamic public class DefaultAwardItemRender extends DefaultAwardItemRenderUI
	{
		
		private var _icon:ItemIcon;
		public function get icon():ItemIcon { return _icon; }
		
		public function DefaultAwardItemRender() 
		{
			TextFieldUtil.autoSetTxtDefaultFormat(this);
			var rect:Rectangle = new Rectangle(border.x, border.y, border.width, border.height);
			_icon = new ItemIcon(rect.width, rect.height, rect);
			addChildAt(_icon, getChildIndex(border));
			border.visible = false;
		}
	}
}