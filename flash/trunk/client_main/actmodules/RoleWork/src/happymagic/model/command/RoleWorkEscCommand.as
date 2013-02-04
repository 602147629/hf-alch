package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	/**
	 * ...
	 * @author ZC
	 */
	public class RoleWorkEscCommand extends BaseDataCommand
	{
		
		public function RoleWorkEscCommand() 
		{
			
		}
	
		public function setData(_id:int):void 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("roleWorkEsc"),{id:_id});
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
			
		}
		
	}

}