package happymagic.model.vo.classVo 
{
	import com.brokenfunction.json.decodeJson;
	import happyfish.model.vo.BasicVo;
	/**
	 * 卷轴
	 * @author lite3
	 */
	public class ScrollClassVo extends BaseItemClassVo 
	{
		
		// 可使用的职业列表
		public var jobs:Array;
		// 可使用的角色属性列表
		public var props:Array;
		// 合成术的cid
		public var mixCid:int;
		
		public var level:int;//等级限制, 技能卷轴时是战斗等级, 合成卷轴时是经营等级
		
		override public function setData(obj:Object):BasicVo 
		{
			if ("jobs" in obj)
			{
				obj.jobs = decodeJson(obj.jobs);
			}
			if ("props" in obj)
			{
				obj.props = decodeJson(obj.props);
			}
			return super.setData(obj);
		}
	}

}