package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.RoleWorkManager;
	import happymagic.model.vo.RoleWorkPointVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class RoleWorkInitCommand extends BaseDataCommand
	{
		
		public function RoleWorkInitCommand() 
		{
			
		}
	
		public function setData():void 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("loadRoleWorkInit"));
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			var arr:Array = new Array();
			var vo:RoleWorkPointVo;
			if (objdata.pointVos)
			{
				for (var i:int = 0; i < objdata.pointVos.length; i++) 
				{
					vo = new RoleWorkPointVo();
					vo.setVaule(objdata.pointVos[i]);
					arr.push(vo);
				}
			}
			RoleWorkManager.getInstance().roleWorkData.roleWorkDataInit = arr;
			
			commandComplete();
			
		}
		
	}

}