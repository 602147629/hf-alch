package happymagic.roleInfo.commands 
{
	import flash.events.Event;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.classVo.EquipmentClassVo;
	import happymagic.roleInfo.events.RoleEvent;
	import happymagic.roleInfo.vo.EquiCompareVo;
	import happymagic.roleInfo.vo.HireVo;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.RoleVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class ReplaceEquipCommand extends BaseDataCommand 
	{
		public var vo:EquiCompareVo;
		
		public function replaceEquip(vo:EquiCompareVo):void 
		{
			this.vo = vo;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("replaceEquip"), { id:vo.roleId, equipId:vo.nowId } );
			loadData();
		}
		
		//override protected function load_complete(e:Event):void 
		//{
			//super.load_complete(e);
			//if (!data.result.isSuccess) return;
			//
			//var old:RoleVo = DataManager.getInstance().roleData.getRole(vo.roleId);
			//old.setData(objdata.roleChange);
			//old.equipments[(vo.now.type2 % 10) - 1] = [ vo.nowId, vo.now.cid, vo.nowWear ];
			//EventManager.dispatchEvent(new RoleEvent(RoleEvent.ROLE_CHANGE, false, false, vo.roleId, old));
		//}
		
	}

}