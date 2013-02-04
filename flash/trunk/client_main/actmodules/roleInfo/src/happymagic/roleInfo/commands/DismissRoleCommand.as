package happymagic.roleInfo.commands 
{
	import flash.events.Event;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.RoleVo;
	import happymagic.roleInfo.events.RoleEvent;
	import happymagic.roleInfo.vo.HireVo;
	import happymagic.model.command.BaseDataCommand;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class DismissRoleCommand extends BaseDataCommand 
	{
		public var id:int;
		
		public function dismissRole(id:int):void 
		{
			this.id = id;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("dismissRole"), { id:id } );
			loadData();
		}
		
		//override protected function load_complete(e:Event):void 
		//{
			//super.load_complete(e);
			//if (!data.result.isSuccess) return;
			//var role:RoleVo = DataManager.getInstance().roleData.getRole(id);
			//DataManager.getInstance().roleData.removeRole(id);
			//EventManager.dispatchEvent(new RoleEvent(RoleEvent.ROLE_DISMISS, false, false, id, role));
		//}
		
	}

}