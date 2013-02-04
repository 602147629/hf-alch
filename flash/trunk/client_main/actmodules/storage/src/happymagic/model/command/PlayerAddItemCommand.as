package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.model.DataCommandBase;
	/**
	 * ...
	 * @author ZC
	 */
	public class PlayerAddItemCommand extends DataCommandBase
	{
		
		public function StoragItemUseCommand() 
		{
			
		}
		
		public function useItem(playid:uint,itemcid:id):void {
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("PlayerAddItem"), { id:playid,itemcid:itemcid} );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}