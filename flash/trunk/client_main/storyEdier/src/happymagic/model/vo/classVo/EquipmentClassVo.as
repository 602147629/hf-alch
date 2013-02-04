package happymagic.model.vo.classVo 
{
	import com.brokenfunction.json.decodeJson;
	import happyfish.model.vo.BasicVo;
	/**
	 * 装备基础数据
	 * @author lite3
	 */
	public class EquipmentClassVo extends BaseItemClassVo 
	{
		// 装备所需角色等级
		public var level:int;
		// hp加成
		public var hp:int;
		// mp加成
		public var mp:int;
		// 物攻
		public var pa:int;
		// 物防
		public var pd:int;
		// 魔攻
		public var ma:int;
		// 魔防
		public var md:int;
		// 暴击率
		public var cri:int;
		// 闪躲率
		public var dod:int;
		// 速度
		public var speed:int;
		// 最大耐久度
		public var maxWear:int;
		
		// 是否能修理
		public var canFix:Boolean;
		// 修理所需的铁匠铺等级
		public var fixLevel:int;
		
		// Array<int> 可装备的职业列表
		public var jobs:Array;
		
		override public function setData(obj:Object):BasicVo 
		{
			if ("jobs" in obj)
			{
				obj.jobs = decodeJson(obj.jobs);
			}
			super.setData(obj);
			canFix = 1 == obj.canFix;
			return this;
		}
	}
}