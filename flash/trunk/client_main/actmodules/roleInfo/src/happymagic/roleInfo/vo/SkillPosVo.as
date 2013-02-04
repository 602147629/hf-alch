package happymagic.roleInfo.vo 
{
	import happymagic.model.vo.classVo.ScrollClassVo;
	import happymagic.model.vo.SkillAndItemVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class SkillPosVo 
	{
		public var roleId:int;
		public var pos:int;
		public var skill:SkillAndItemVo;
		public var scroll:ScrollClassVo;
		public function SkillPosVo(roleId:int, pos:int, skill:SkillAndItemVo, scroll:ScrollClassVo) 
		{
			this.roleId = roleId;
			this.pos = pos;
			this.skill = skill;
			this.scroll = scroll;
		}
		
	}

}