package happymagic.roleInfo.commands 
{
	import flash.events.Event;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.RoleVo;
	import happymagic.roleInfo.events.RoleEvent;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class UnlockSkillCommand extends BaseDataCommand 
	{
		public var roleId:int;
		public var pos:int;
		
		public function unlockSkill(roleId:int, pos:int, callback:Function):void 
		{
			this.roleId = roleId;
			this.pos = pos;
			callBack = callback;
			
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("unlockSkill"), { id:roleId, idx:pos } );
			loadData();
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			if (!data.result.isSuccess) return;
			
			var old:RoleVo = DataManager.getInstance().roleData.getRole(roleId);
			old.skills[pos - 1] = 0;
			EventManager.dispatchEvent(new RoleEvent(RoleEvent.ROLE_CHANGE, false, false, old.id, old));
			commandComplete();
		}
		
	}

}