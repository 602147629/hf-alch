package happymagic.hire.commands 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.hire.data.HireData;
	import happymagic.model.command.BaseDataCommand;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class InitHireCommand extends BaseDataCommand 
	{
		
		public var list:Array;
		private var completeCallback:Function;
		
		public function InitHireCommand() 
		{
			
		}
		
		public function initHire(completeCallback:Function):void
		{
			this.completeCallback = completeCallback;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("initHire"));
			loadData();
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			HireData.instance.setData(objdata.list);
			
			commandComplete();
			if (completeCallback != null) completeCallback();
		}
		
	}

}