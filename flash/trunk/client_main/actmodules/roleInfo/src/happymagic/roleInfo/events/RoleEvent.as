package happymagic.roleInfo.events 
{
	import flash.events.Event;
	import happymagic.model.vo.RoleVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class RoleEvent extends Event 
	{
		
		public static const ROLE_CHANGE:String = "roleChange";
		public static const ROLE_DISMISS:String = "roleDismiss";
		
		public var id:int;
		public var role:RoleVo;
		
		public function RoleEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false, id:int = 0, role:RoleVo = null) 
		{ 
			super(type, bubbles, cancelable);
			this.id = id;
			this.role = role;
		} 
		
		public override function clone():Event 
		{ 
			return new RoleEvent(type, bubbles, cancelable, id, role);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("RoleEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}