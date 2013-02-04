package happymagic.hire.commands 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.hire.data.HireData;
	import happymagic.hire.vo.HireVo;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.RoleVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class HireRoleCommand extends BaseDataCommand 
	{
		public var npcId:int;
		public var pos:int;
		public var hireVo:HireVo;
		private var callbackFun:Function;
		
		public function hireRole(npcId:int, pos:int, callbackFun:Function):void
		{
			this.npcId = npcId;
			this.pos = pos;
			this.callbackFun = callbackFun;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("hireRole"), { npcId:npcId, pos:pos } );
			loadData();
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			if (result.isSuccess)
			{
				//hireVo = HireData.instance.getHire(npcId, pos);
				//var role:RoleVo = new RoleVo();
				//role.setData(hireVo);
				//role.id = objdata.roleId;
				//DataManager.getInstance().roleData.addRole(role);
				
				hireVo = new HireVo();
				hireVo.setData(objdata.hireVo);
				HireData.instance.setHire(npcId, pos, hireVo);
			}
			
			callbackFun(result.isSuccess, npcId, pos, hireVo);
		}
		
	}

}