package happymagic.roleInfo.events 
{
	import flash.events.Event;
	import happymagic.roleInfo.vo.RoleFormationVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class RoleFormationEvent extends Event 
	{
		public static const ROLE_FORMATION_SELECT:String = "roleFormationSlect";
		public static const ROLE_FORMATION_CANCEL:String = "roleFormationCancel";
		
		public var role:RoleFormationVo;
		
		public function RoleFormationEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false, role:RoleFormationVo = null) 
		{ 
			super(type, bubbles, cancelable);
			this.role = role;
		} 
		
		public override function clone():Event 
		{ 
			return new RoleFormationEvent(type, bubbles, cancelable, role);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("RoleFormationEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}