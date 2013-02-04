package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	/**
	 * ...
	 * @author ZC
	 */
	public class StoragItemUseCommand extends BaseDataCommand
	{
		
		public function StoragItemUseCommand() 
		{
			
		}
		
		public function useItem(_cid:uint,_roleid:uint = 0):void {
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("useitem"), { cid:_cid, roleid:_roleid } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}