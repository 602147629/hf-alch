package happymagic.diy.model 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.command.BaseDataCommand;
	
	/**
	 * ...
	 * @author 
	 */
	public class SaveDiyRemoveCommand extends BaseDataCommand 
	{
		
		public function SaveDiyRemoveCommand() 
		{
			
		}
		
		public function remove(id:int):void {
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("diyRemove"), { id:id } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}