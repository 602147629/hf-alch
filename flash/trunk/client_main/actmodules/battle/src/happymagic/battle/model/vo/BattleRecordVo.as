package happymagic.battle.model.vo 
{
	import happymagic.battle.model.vo.BattleRecordTargetVo;
	import happymagic.battle.view.battlefield.Role;
	import happymagic.model.vo.SkillAndItemVo;
	/**
	 * 战斗记录
	 * 记录攻击、使用技能、物品、防御、逃跑的成功、失败、造成伤害等各种信息
	 * @author XiaJunJie
	 */
	public class BattleRecordVo 
	{
		public var id:int; //操作编号 操作型的战斗记录(SKILL_AND_ITEM/ESCAPE)都有编号 表明这是第几次操作
		
		public var type:int; //操作类型 SKILL_AND_ITEM/ESCAPE/STATUS
		
		public var initiatorIndex:int; //发起者pos
		
		public var targetGrid:int; //所选择的目标地块
		
		public var targetInfo:Vector.<BattleRecordTargetVo>; //对每个目标的处理信息
		
		public var skillAndItemVo:SkillAndItemVo; //使用的技能或物品的信息
		
		public var itemId:int; //使用的物品的ID
		
		public var isFriendSkill:Boolean; //是否好友技能
		
		public var escSuccess:Boolean; //是否逃跑成功
		
		
		//关键地块 角色施放技能时所站的地块
		//该属性纯粹用于表现 即告诉程序叠加动画应该出现在场景的哪个位置 以及发起者应该跑到哪个位置施放技能
		public var keyGrid:int;
		
		//常量-----------------------------
		public static const SKILL_AND_ITEM:int = 1;
		public static const STATUS:int = 2;
		public static const ESCAPE:int = 3;
		
		public static const NONE:int = 0;
		public static const CRIT:int = 1;
		public static const DODGE:int = 2;
		public static const RESIST:int = 3;
	}

}