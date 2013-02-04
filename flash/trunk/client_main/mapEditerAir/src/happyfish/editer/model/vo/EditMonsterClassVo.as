package happyfish.editer.model.vo 
{
	import happyfish.editer.model.EditDataManager;
	import happyfish.model.vo.BasicVo;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class EditMonsterClassVo extends BasicVo 
	{
		public var cid:uint;
		public var name:String;
		public var avatarId:uint;
		public var maxHp:uint;
		public var sizeX:uint;
		public var sizeZ:uint;
		public var monsterper:uint=100;
		public var collisionRange:int;
		public var className:String;
		
		public function EditMonsterClassVo() 
		{
			
		}
		
		override public function setData(obj:Object):BasicVo 
		{
			super.setData(obj);
			if (avatarId) 
			{
				var avatar:EditAvatarClassVo = EditDataManager.getInstance().getClassFrom("avatarClass", "avatarId", avatarId);
				className = avatar.className;
			}
			
			
			return this;
		}
		
	}

}