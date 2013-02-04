package happymagic.guide.api 
{
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.external.ExternalInterface;
	import flash.utils.getDefinitionByName;
	import happyfish.veal.RuntimeUtil;
	/**
	 * ...
	 * @author lite3
	 */
	public class API 
	{
		public function get isSelfHome():Boolean
		{
			return 0// == GameConfig.getInstance().mapWho;
		}
		
		public function reportClick(id:int):void
		{
			ExternalInterface.call("reportGuide", id);
		}
		
		/**
		 * 
		 * @param	container
		 * @param	ref
		 * @param	sort [asc|des] default asc 升序
		 * @return
		 */
		public function getChildByClass(container:DisplayObjectContainer, ref:*, sort:String = "asc", filter:String = null):DisplayObject
		{
			if (!(ref is Class))
			{
				if (!(ref is String)) ref = String(ref);
				ref = getDefinitionByName(ref);
			}
			var len:int = container.numChildren;
			for (var i:int = 0; i < len; i++)
			{
				var n:int = "asc" == sort ? i : len - i - 1;
				var c:DisplayObject = container.getChildAt(n);
				if (!(c is ref)) continue;
				if (!filter) return c;
				try {
					if (RuntimeUtil.getValue("{__thisc}" + filter, { __thisc:c } )) return c;
				}catch (err:Error) { }
			}
			return null;
		}
		
	}

}