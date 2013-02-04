package happymagic.mix.view 
{
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.mix.view.ui.ItemTipUI;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class ItemTip extends ItemTipUI 
	{
		
		public function ItemTip() 
		{
			mouseChildren = false;
			mouseEnabled = false;
			TextFieldUtil.autoSetDefaultFormat(txt);
		}
		
		public function showText(text:String):void
		{
			txt.text = text;
		}
		
	}

}