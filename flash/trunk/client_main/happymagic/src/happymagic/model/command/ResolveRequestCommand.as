package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	/**
	 * ...
	 * @author zc
	 */
	public class ResolveRequestCommand extends BaseDataCommand
	{
		
		public function ResolveRequestCommand() 
		{
			
		}

		public function setdata(_d_id:uint,_num:uint):void {
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("ResolveRequestCommand"), { d_id:_d_id, num:_num } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}		
		
	}

}