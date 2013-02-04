package happymagic.roleInfo.commands 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.roleInfo.vo.HireVo;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.RoleVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class ExpandRoleLimitCommand extends BaseDataCommand 
	{
		
		public function expandRole(count:int, type:int, callBack:Function = null):void 
		{
			this.callBack = callBack;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("expandRoleLimit"), { count:count, type:type } );
			loadData();
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			if (!data.result.isSuccess) return;
			
			var max:int = objdata.max;
			DataManager.getInstance().currentUser.maxRoleNum = max;
			commandComplete();
		}
		
	}

}