package happymagic.model.vo.classVo 
{
	import com.brokenfunction.json.decodeJson;
	import happyfish.model.vo.BasicVo;
	/**
	 * 合成术基础结构
	 * @author lite3
	 */
	public class MixClassVo extends BasicVo 
	{
		/** 合成术cid */ 
		public var cid:int;
		/** 合成术对应的实物cid */ 
		public var itemCid:int;
		/** 可以制作的炉子的cid */
		public var furnaceCid:int;
		public var coin:int;
		public var gem:int;
		/** 消耗的sp,速度 */ 
		public var sp:int;
		/** 获得的经验 */ 
		public var exp:int;
		/** 合成一个所用的时间 */ 
		public var time:int;
		/** 可合成的最大时间 */
		public var maxTime:int;
		/** 基础几率 */ 
		public var probability:int;
		/** 每1点几率所花费的gem */ 
		public var perProbabilityGem:int;
		/** 几率调整的最小变化量 */ 
		public var probabilityInterval:int;
		public var name:String;
		// 所需列表
		public var needs:Array;
		
		override public function setData(obj:Object):BasicVo 
		{
			if ("needs" in obj)
			{
				obj.needs = decodeJson(obj.needs);
			}
			return super.setData(obj);
		}
	}
}