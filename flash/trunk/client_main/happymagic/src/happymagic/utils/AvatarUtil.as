package happymagic.utils 
{
	import happymagic.model.vo.RoleVo;
	/**
	 * ...
	 * @author lite3
	 */
	public class AvatarUtil 
	{
		
		public static function getAvatar(role:RoleVo):String
		{
			return role.pos > 8 ? role.className + "_back" : role.className;
		}
		
		public static function getBackAvatar(role:RoleVo):String
		{
			return role.className + "_back";
		}
	}

}