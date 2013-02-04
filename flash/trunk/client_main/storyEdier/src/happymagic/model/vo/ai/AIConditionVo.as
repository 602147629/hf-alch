package happymagic.model.vo.ai 
{
	import happyfish.model.vo.BasicVo;
	
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class AIConditionVo extends BasicVo
	{
		public var type:int; //类型 PHASE/ROLE/FORMATION
		
		public var phase:int; //战斗阶段 BEGINNING/INPROGRESS/END
		
		public var role:AIRoleVo; //特定角色
		
		public var formation:AIFormationVo; //特定站位
		
		
		override public function setData(obj:Object):BasicVo 
		{
			for (var key:String in obj)
			{
				if (key == "role")
				{
					role = new AIRoleVo().setData(obj[key]) as AIRoleVo;
				}
				else if (key == "formation")
				{
					formation = new AIFormationVo().setData(obj[key]);
				}
				else if (hasOwnProperty(key)) this[key] = obj[key];
			}
			return this;
		}
		
		//常量--------------------------
		public static const PHASE:int = 1; //战斗阶段
		public static const ROLE:int = 2; //角色条件
		public static const FORMATION:int = 3; //站位条件
		
		public static const BEGINNING:int = 1; //战斗开始阶段
		public static const INPROGRESS:int = 2; //战斗进行中
		public static const END:int = 3; //战斗结束阶段
		
	}

}