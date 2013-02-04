package happymagic.model.vo 
{
	import happyfish.model.vo.BasicVo;
	import happymagic.manager.DataManager;
	/**
	 * 交互对象VO
	 * @author XiaJunJie
	 */
	public class ActItemClassVo extends BasicVo
	{
		public var cid:int; //类型ID
		public var name:String; //名字
		public var className:String; //显示对象className
		public var maxHp:int; //最大生命值
		public var sizeX:int; //X向尺寸
		public var sizeZ:int; //Z向尺寸
		public var conditions:Vector.<ConditionVo>; //交互消耗
		
		//解析 交互消耗 数据
		protected function parseConditions(conditionsArr:Array):void
		{
			conditions = new Vector.<ConditionVo>();
			for each(var conditionObj:Object in conditionsArr)
			{
				var conditionVo:ConditionVo = new ConditionVo;
				conditionVo.setData(conditionObj);
				conditions.push(conditionVo);
			}
		}
		
	}

}