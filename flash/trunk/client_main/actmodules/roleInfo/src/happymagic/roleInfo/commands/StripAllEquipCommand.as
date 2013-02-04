package happymagic.roleInfo.commands 
{
	import flash.events.Event;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.roleInfo.events.RoleEvent;
	import happymagic.roleInfo.vo.HireVo;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.RoleVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class StripAllEquipCommand extends BaseDataCommand 
	{
		public var id:int;
		public var roleVo:RoleVo;
		
		public function stripAllEquip(id:int):void 
		{
			this.id = id;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("stripAllEquip"), { id:id } );
			loadData();
		}
		
		//override protected function load_complete(e:Event):void 
		//{
			//super.load_complete(e);
			//if (!data.result.isSuccess) return;
			//
			//var old:RoleVo = DataManager.getInstance().roleData.getRole(id);
			//old.setData(objdata.roleChange);
			//old.equipments = [];
			//EventManager.dispatchEvent(new RoleEvent(RoleEvent.ROLE_CHANGE, false, false, old.id, old));
		//}
		
	}

}