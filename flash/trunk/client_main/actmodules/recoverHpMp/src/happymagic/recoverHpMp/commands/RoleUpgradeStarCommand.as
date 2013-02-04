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
	public class RoleUpgradeStarCommand extends BaseDataCommand 
	{
		private var callback:Function;
		public function upgrade(callback:Function):void
		{
			this.callback = callback;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("roleUpgradeQuality"));
			loadData();
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			if (result.isSuccess)
			{
				DataManager.getInstance().roleData.getRole(0).quality = objdata.quality;
			}
			if (callback != null) callback(result.isSuccess);
		}
		
	}

}