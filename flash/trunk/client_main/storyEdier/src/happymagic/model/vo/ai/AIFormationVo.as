package happymagic.model.vo.ai 
{
	/**
	 * 站位VO
	 * @author XiaJunJie
	 */
	public class AIFormationVo
	{
		public var side:int; //阵营 SELF/ENEMY
		public var shape:int; //形 ROW/COL/CROSS/ALL
		public var threshold:int; //人数阀值
		
		public var formationInCondition:int = -1; //当前脚本的条件中的某一个站位
		
		//格式有两种
		//指定 数组 [side,shape,threshold]
		//索引 int formationInCondition
		public function setData(obj:Object):AIFormationVo
		{
			var arr:Array = obj as Array;
			if (arr)
			{
				side = arr[0];
				shape = arr[1];
				threshold = arr[2];
			}
			else formationInCondition = obj as int;
			
			return this;
		}
		
		//常量----------------------------------
		public static const SELF:int = 2; //己方
		public static const ENEMY:int = 1; //敌方
		
		public static const ROW:int = 2; //行
		public static const COL:int = 3; //列
		public static const CROSS:int = 4; //十字
		public static const ALL:int = 5; //全体
	}

}