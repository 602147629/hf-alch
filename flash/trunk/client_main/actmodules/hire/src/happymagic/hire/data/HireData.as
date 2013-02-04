package happymagic.hire.data 
{
	import happymagic.hire.vo.HireVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class HireData 
	{
		private var list:Array = [];
		
		public var inited:Boolean;
		
		public function getHireList(npcId:int):Array
		{
			var arr:Array = list[npcId - 1];
			if (!arr) list[npcId - 1] = arr = [];
			return arr;
		}
		
		public function getHire(npcId:int, pos:int):HireVo
		{
			return getHireList(npcId)[pos - 1] as HireVo;
		}
		
		public function setHire(npcId:int, pos:int, vo:HireVo):void
		{
			getHireList(npcId)[pos - 1] = vo;
		}
		
		public function setData(arr:Array):void
		{
			for (var i:int = arr.length - 1; i >= 0; i--)
			{
				list[i] = [];
				var tmp:Array = arr[i];
				for (var j:int = tmp.length - 1; j >= 0; j--)
				{
					list[i][j] = new HireVo().setData(tmp[j]);
				}
			}
		}
		
		
		private static var _instance:HireData;
		public static function get instance():HireData
		{
			if (!_instance) _instance = new HireData();
			return _instance;
		}
	}

}