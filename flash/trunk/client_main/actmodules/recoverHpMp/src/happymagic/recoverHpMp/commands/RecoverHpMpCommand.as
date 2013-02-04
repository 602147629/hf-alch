package happymagic.recoverHpMp.commands 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class RecoverHpMpCommand extends BaseDataCommand 
	{
		private var callback:Function;
		public function recover(callback:Function):void
		{
			this.callback = callback;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("recoverHpMp"));
			loadData();
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			if (callback != null) callback(result.isSuccess);
		}
		
	}

}