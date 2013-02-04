package happymagic.battle.model.vo 
{
	import happyfish.model.vo.BasicVo;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.LevelInfoVo;
	import happymagic.model.vo.RoleVo;
	/**
	 * 战斗VO
	 * @author XiaJunJie
	 */
	public class BattleVo extends BasicVo
	{
		public var id:int; //战斗ID
		public var bgClassName:String; //战场背景
		public var roleList:Vector.<RoleVo>; //角色列表
		public var friendSkill:Vector.<FriendSkillVo>; //好友技能
		public var assCnt:int; //可以使用的好友技能的次数
		public var extCnt:int; //充值的好友技能次数
		public var talk:Array; //开场对话
		public var result:int; //打赢/失败/逃跑
		
		/**
		 * 设置数据
		 */
		override public function setData(obj:Object):BasicVo 
		{
			var arr:Array;
			var i:int;
			
			for (var key:String in obj)
			{
				if (key == "roleList")
				{
					roleList = new Vector.<RoleVo>;
					
					arr = obj[key];
					for (i = 0; i < arr.length; i++)
					{
						var roleVo:RoleVo = new RoleVo;
						roleVo.setData(arr[i]);
						roleList.push(roleVo);
					}
				}
				else if (key == "friendSkill")
				{
					friendSkill = new Vector.<FriendSkillVo>;
					
					arr = obj[key];
					for (i = 0; i < arr.length; i++)
					{	
						var friendSkillVo:FriendSkillVo = new FriendSkillVo;
						friendSkillVo.setData(arr[i]);
						friendSkill.push(friendSkillVo);
					}
				}
				else if (hasOwnProperty(key)) this[key] = obj[key];
			}
			
			return this;
		}
		
		//常量----------------------------------------
		public static const WIN:int = 1;
		public static const LOST:int = 2;
		public static const ESC:int = 3;
		public static const ERROR:int = 4;
	}

}