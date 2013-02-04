package happymagic.specialUpgrade.commands 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class UpgradeCommand extends BaseDataCommand 
	{
		
		private var callback:Function;
		
		public function upgrade(id:int, callback:Function):void
		{
			this.callback = callback;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("specialUpgrade"), { id:id } );
			loadData();
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			if (objdata.maxOrder)
			{
				DataManager.getInstance().currentUser.maxOrder = objdata.maxOrder;
			}
			
			if (callback != null) callback(result.isSuccess);
		}
		
	}

}