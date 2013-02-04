package happymagic.recoverHpMp.model 
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
		
		private const roleUpgradeStarList:Vector.<RoleUpgaradeStarVo> = new Vector.<RoleUpgaradeStarVo>();
		
		public function setData(arr:Array):void
		{
			var len:int = arr.length;
			roleUpgradeStarList.length = len;
			for (var i:int = 0; i < len; i++)
			{
				var starVo:RoleUpgaradeStarVo = new RoleUpgaradeStarVo();
				starVo.setData(arr[i]);
				roleUpgradeStarList[i] = starVo;
			}
			
			inited = true;
		}
		
		public function getInfo(quality:int):RoleUpgaradeStarVo
		{
			for (var i:int = roleUpgradeStarList.length - 1; i >= 0; i--)
			{
				if (roleUpgradeStarList[i].quality == quality) return roleUpgradeStarList[i];
			}
			return null;
		}
	}

}