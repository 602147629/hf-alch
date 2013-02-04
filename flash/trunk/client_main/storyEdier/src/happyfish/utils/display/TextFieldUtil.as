package happyfish.utils.display 
{
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.text.TextField;
	/**
	 * ...
	 * @author lite3
	 */
	public class TextFieldUtil 
	{
		
		public static function autoSetDefaultFormat(txt:TextField):void
		{
			if (txt.length > 0) txt.defaultTextFormat = txt.getTextFormat(0, 1);
		}
		
		public static function autoSetTxtDefaultFormat(container:DisplayObjectContainer, hasChildren:Boolean = false):void
		{
			for (var i:int = container.numChildren - 1; i >= 0; i--)
			{
				var child:DisplayObject = container.getChildAt(i);
				var txt:TextField = child as TextField;
				if (txt != null)
				{
					if(txt.length > 0) txt.defaultTextFormat = txt.getTextFormat(0, 1);
				}else if (hasChildren && child is DisplayObjectContainer)
				{
					autoSetTxtDefaultFormat(DisplayObjectContainer(child), hasChildren);
				}
			}
		}
	}

}