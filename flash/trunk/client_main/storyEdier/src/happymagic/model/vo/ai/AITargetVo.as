package happymagic.model.vo.ai 
{
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class AITargetVo
	{
		public var role:AIRoleVo;
		public var formation:AIFormationVo;
		
		public function setData(obj:Object):AITargetVo
		{
			if (obj["role"] != null)
			{
				role = new AIRoleVo().setData(obj["role"]) as AIRoleVo;
			}
			else if (obj["formation"] != null)
			{
				formation = new AIFormationVo().setData(obj["formation"]);
			}
			
			return this;
		}
	}

}