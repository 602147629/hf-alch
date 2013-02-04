package happymagic.battle.events 
{
	import flash.events.Event;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.SkillAndItemVo;
	
	/**
	 * ...
	 * @author 
	 */
	public class BattleEvent extends Event 
	{
		public static const REQUEST_ATTACK:String = "battleRequestAttack"; //攻击按钮被点击
		public static const REQUEST_SKILL:String = "battleRequestSkill"; //用户按下某个技能
		public static const REQUEST_ITEM:String = "battleRequestItem"; //用户按下某个物品
		public static const REQUEST_DEFENSE:String = "battleRequestDefense"; //用户按下防御
		public static const REQUEST_ESC:String = "battleRequestEsc"; //用户按下逃跑
		public static const REQUEST_AUTO:String = "battleRequestAuto"; //用户按自动战斗
		public static const REQUEST_CLOSE_AUTO:String = "battleRequestCloseAuto"; //用户按取消自动战斗
		
		public static const NEXT_ROLE:String = "battleNextRole";//下一个角色进度条走到，通知显示当前用户头像与信息
		
		public static const LIGHT_ROLE:String = "battleLightRole";//通知高亮某角色
		public static const CLOSE_LIGHT_ROLE:String = "battleCloseLightRole";//通知停止高亮所有角色
		
		public static const START_FIGHT:String = "battleStartFight"; //人物说完开场对白 开始行动
		
		public static const CLOSE_BAG:String = "battleCloseBag"; //点击物品后关闭背包
		
		public var roleVo:RoleVo;
		public var skillAndItemVo:SkillAndItemVo;
		public var itemId:int;
		public var pos:int;
		
		public function BattleEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new BattleEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("BattleEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}