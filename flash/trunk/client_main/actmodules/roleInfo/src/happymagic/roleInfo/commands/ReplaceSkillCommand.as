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
	public class ReplaceSkillCommand extends BaseDataCommand 
	{
		public var id:int;
		public var pos:int;
		public var cid:int;
		
		public function replaceSkill(id:int, pos:int, cid:int):void 
		{
			this.id = id;
			this.pos = pos;
			this.cid = cid;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("replaceSkill"), { id:id, idx:pos, cid:cid } );
			loadData();
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			if (!data.result.isSuccess) return;
			
			var old:RoleVo = DataManager.getInstance().roleData.getRole(id);
			old.skills[pos - 1] = cid;
			EventManager.dispatchEvent(new RoleEvent(RoleEvent.ROLE_CHANGE, false, false, old.id, old));
		}
		
	}

}