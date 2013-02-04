package happyfish.editer.model.vo 
{
	import happyfish.editer.model.EditDataManager;
	import happyfish.model.vo.BasicVo;
	import happymagic.model.vo.AvatarVo;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class EditMineClassVo extends BasicVo 
	{
		public var cid:uint;
		public var name:String;
		public var avatarId:uint;
		public var maxHp:uint;
		public var sizeX:uint;
		public var sizeZ:uint;
		public var mineper:uint=100;
		
		public var className:String;
		public var conditions:String;
		public function EditMineClassVo() 
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