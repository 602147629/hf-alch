package happymagic.battle.model.vo 
{
	import happymagic.model.vo.StatusVo;
	/**
	 * 每个目标的处理信息
	 * @author XiaJunJie
	 */
	public class BattleRecordTargetVo
	{
		public var index:int; //Role.pos
		public var hpChange:int; //血量变化
		public var mpChange:int; //法力变化
		public var critOrDodgeOrResist:int; //是否闪躲或暴击或抵抗 值参考BattleRecordVo.as的静态常量
		public var down:Boolean; //是否死亡
		public var canAction:Boolean = true; //是否可以继续行动
		public var canUseSkill:Boolean = true; //是否可以使用技能
		
		//以下三个数组 每个元素也都是一个子数组 记录每个状态的信息 可用StatusVo.toArray()获得
		public var addStatusList:Vector.<StatusVo> = new Vector.<StatusVo>(); //增加的状态列表
		public var removeStatusList:Vector.<StatusVo> = new Vector.<StatusVo>(); //移除的状态列表
		public var leftStatusList:Vector.<StatusVo> = new Vector.<StatusVo>(); //尚存的状态列表
		
		//调试用数据
		public var dodgeProb:Number; //闪避几率
		public var dodgeRandom:Number; //闪避判定所使用的随机数
		public var dodgeRandomIndex:int = -1; //闪避判定所使用的随机数在随机数列中的索引
		public var critProb:Number; //暴击几率
		public var critRandom:Number; //暴击判定所使用的随机数
		public var critRandomIndex:int = -1; //暴击判定所使用的随机数在随机数列中的索引
		public var resistProb:Number; //抵抗几率
		public var resistRandom:Number; //抵抗判定所使用的随机数
		public var resistRandomIndex:int = -1; //抵抗判定所使用的随机数在随机数列中的索引
	}

}