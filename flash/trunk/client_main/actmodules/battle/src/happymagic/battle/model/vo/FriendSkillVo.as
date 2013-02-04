package happymagic.battle.model.vo 
{
	import happyfish.model.vo.BasicVo;
	import happymagic.battle.view.battlefield.BattleField;
	import happymagic.battle.view.battlefield.Role;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.RoleVo;
	import happymagic.model.vo.SkillAndItemVo;
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class FriendSkillVo extends BasicVo
	{
		public var role:Role;
		public var skill:SkillAndItemVo;
		public var level:int;
		
		public var needFriendJob:int; //升到下一级需要的好友的职业
		public var needFriendProp:int; //升到下一级需要的好友的属性
		public var needFriendNum:int; //升到下一级需要的好友的数量
		public var needFriendLevel:int; //升到下一级需要的好友的等级
		
		override public function setData(obj:Object):BasicVo 
		{
			skill = DataManager.getInstance().getSkillAndItemVo(obj["skillId"]);
			
			level = obj["lev"];
			
			var arr:Array = obj["next"];
			needFriendNum = arr[0];
			needFriendLevel = arr[1];
			needFriendJob = arr[2];
			needFriendProp = arr[3];
			
			if (level > 0)
			{
				var roleVo:RoleVo = new RoleVo;
				roleVo.className = obj["avatar"];
				roleVo.pos = BattleField.FRIEND_INDEX;
				roleVo.name = obj["name"];
				
				role = new Role(roleVo);
				role.showName();
			}
			
			return this;
		}
		
	}

}