package happymagic.specialUpgrade.model 
{
	/**
	 * ...
	 * @author lite3
	 */
	public class Data 
	{
		
		private static var _instance:Data;
		public static function get instance():Data { return _instance ||= new Data(); }
		
		public var inited:Boolean = false;
		
		private const specialUpgradeList:Vector.<SpecialUpgaradeVo> = new Vector.<SpecialUpgaradeVo>();
		
		public function setData(arr:Array):void
		{
			var len:int = arr.length;
			specialUpgradeList.length = len;
			for (var i:int = 0; i < len; i++)
			{
				var vo:SpecialUpgaradeVo = new SpecialUpgaradeVo();
				vo.setData(arr[i]);
				specialUpgradeList[i] = vo;
			}
			inited = true;
		}
		
		public function getSpecialUpgradeVo(id:int, level:int):SpecialUpgaradeVo
		{
			for (var i:int = specialUpgradeList.length - 1; i >= 0; i--)
			{
				var vo:SpecialUpgaradeVo = specialUpgradeList[i];
				if (vo.id == id && vo.level == level)
				{
					return vo;
				}
			}
			return null;
		}
		
	}

}