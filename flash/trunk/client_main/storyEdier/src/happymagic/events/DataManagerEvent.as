package happymagic.events 
{
	import flash.events.Event;
	import happymagic.model.vo.UserVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class DataManagerEvent extends Event 
	{
		static public const ITEMS_CHANGE:String = "itemsChange";
		//当前用户信息改变
		public static const USERINFO_CHANGE:String = "userInfoChange";
		//public static const DECORBAG_CHANGE:String = "decorBagChange";
		
		//佣兵与主角战斗角色数据改变
		public static const ROLEDATA_CHANGE:String = "rolesDataChange";
		
		public var userChange:UserInfoChangeVo;
		
		public var role_addOrRemove:Boolean;
		public function DataManagerEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false) 
		{ 
			super(type, bubbles, cancelable);
			
		} 
		
		public override function clone():Event 
		{ 
			return new DataManagerEvent(type, bubbles, cancelable);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("DataManagerEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}