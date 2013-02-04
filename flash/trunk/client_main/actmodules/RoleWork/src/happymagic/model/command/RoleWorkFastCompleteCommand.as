package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DisplayManager;
	import happymagic.manager.RoleWorkManager;
	import happymagic.model.vo.RoleWorkPointVo;
	/**
	 * ...
	 * @author ZC
	 */
	public class RoleWorkFastCompleteCommand extends BaseDataCommand 
	{
		private var id:int;
		public function RoleWorkFastCompleteCommand() 
		{
			
		}
	
		public function setData(_id:int):void 
		{
			id = _id;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("roleWorkFastComplete"),{id:_id});
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e)
			if (objdata.result.status == 1)
			{
				var pointvo:RoleWorkPointVo = RoleWorkManager.getInstance().roleWorkData.getRoleWorkVo(id);
				pointvo.time = 0;				
			}
			else
			{
				//DisplayManager.showNeedMoreItemView();
			}

			
						
			commandComplete();
			
			
		}
		
	}

}