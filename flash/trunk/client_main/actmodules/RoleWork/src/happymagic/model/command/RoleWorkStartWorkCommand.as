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
	public class RoleWorkStartWorkCommand extends BaseDataCommand
	{
		
		public function RoleWorkStartWorkCommand() 
		{
			
		}
	
		public function setData(_id:int,roleIds:String):void 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("roleWroldStartWork"),{id:_id,roleIds:roleIds});
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			var vo:RoleWorkPointVo = new RoleWorkPointVo();
			if (objdata.pointVo)
			{
				vo.setVaule(objdata.pointVo);
				var currentvo:RoleWorkPointVo = RoleWorkManager.getInstance().roleWorkData.getRoleWorkVo(vo.id);
				currentvo.state = vo.state;
				currentvo.time = vo.time;
				currentvo.awards = vo.awards;
				currentvo.roleIds = vo.roleIds;				
			}
			

			//currentvo.id = vo.id;
			
			commandComplete();
			
		}
		
	}

}