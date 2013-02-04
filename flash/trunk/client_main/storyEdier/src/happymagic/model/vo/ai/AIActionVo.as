package happymagic.model.vo.ai 
{
	import happyfish.model.vo.BasicVo;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.SkillAndItemVo;
	/**
	 * 动作VO
	 * @author XiaJunJie
	 */
	public class AIActionVo extends BasicVo
	{
		public var type:int;
		
		public var talkScript:Array; //说话脚本
		
		public var skillAndItemCid:int; //使用的技能或物品的ID SkillAndItemVo.cid
		public var skillAndItemVo:SkillAndItemVo; //根据skillAndItemCid从静态数据中获得
		
		override public function setData(obj:Object):BasicVo 
		{
			super.setData(obj);
			
			//根据skillAndItemCid获得SkillAndItemVo
			//skillAndItemVo = DataManager.getInstance().getSkillAndItemVo(skillAndItemCid);
			
			return this;
		}
		
		//常量----------------------------
		public static const TALK:int = 1; //说话
		public static const ATTACK:int = 2; //攻击
		public static const SKILL_AND_ITEM:int = 3; //使用技能或物品
		public static const DEFEND:int = 4; //防御
		public static const ESCAPE:int = 5; //逃跑
	}

}