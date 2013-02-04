package happymagic.roleInfo.commands 
{
	import com.brokenfunction.json.encodeJson;
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
	public class RoleFormationSetCommand extends BaseDataCommand 
	{
		public var map:Object;
		
		/**
		 * 
		 * @param	obj  {roleId:pos, roleId:pos}
		 * @param	
		 */
		public function roleFormationSet(map:Object):void 
		{
			this.map = map;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("roleFormationSet"), { map:encodeJson(map) } );
			loadData();
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			if (!data.result.isSuccess) return;
			
			var o:Object = { };
			for (var k:String in map)
			{
				o[map[k]] = k;
			}
			var list:Array = DataManager.getInstance().roleData.getMyRoles();
			for (var i:int = list.length - 1; i >= 0; i--)
			{
				var role:RoleVo = list[i] as RoleVo;
				role.pos = (role.id in o) ? int(o[role.id]) : -1;
			}
			
			commandComplete();
		}
		
	}

}