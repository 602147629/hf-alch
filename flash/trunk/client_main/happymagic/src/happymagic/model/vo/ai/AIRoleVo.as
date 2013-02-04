package happymagic.model.vo.ai 
{
	import happyfish.model.vo.BasicVo;
	/**
	 * 角色VO
	 * @author XiaJunJie
	 */
	public class AIRoleVo extends BasicVo
	{
		public var type:int = -1; //角色类型 SELF/FRIEND/ENEMY/MAINROLE/BOSS
		public var profession:int = -1; //角色职业
		public var hpMoreThan:int = -1; //HP少于多少时 如果是一个0-1的小数 表示百分比
		public var hpLessThan:int = -1; //HP多于多少时 如果是一个0-1的小数 表示百分比
		public var statusList:Array; //有或没有指定的状态时 其中元素为数组 格式为[有/无,效果类型,正面或负面] 即[1/0,4/5/6...,1/2]
		
		public var roleInCondition:int = -1; //当前脚本的条件中的某一个角色
		
		//常量--------------------------------------
		public static const SELF:int = 1; //自己
		public static const FRIEND:int = 2; //友方
		public static const ENEMY:int = 3; //敌方
		public static const MAINROLE:int = 4; //主角
		public static const BOSS:int = 5; //BOSS
	}

}