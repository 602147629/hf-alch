package happymagic.roleInfo.vo 
{
	import happymagic.display.view.ui.AvatarSprite;
	import happymagic.model.vo.RoleVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class RoleFormationVo 
	{
		public var role:RoleVo;
		public var pos:int;
		public var isDungeon:Boolean;
		public var avatar:AvatarSprite;
		
		public function RoleFormationVo(role:RoleVo, isDungeon:Boolean) 
		{
			this.role = role;
			pos = role.pos;
			this.isDungeon = isDungeon;
		}
		
		public function get onBattle():Boolean { return pos != -1; }
		
	}

}