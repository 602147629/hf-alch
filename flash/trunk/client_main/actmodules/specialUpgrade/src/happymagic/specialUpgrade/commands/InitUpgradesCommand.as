package happymagic.specialUpgrade.commands 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.specialUpgrade.model.Data;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class InitUpgradesCommand extends BaseDataCommand 
	{
		public function init(callback:Function):void
		{
			this.callBack = callback;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("initUpgrades"));
			loadData();
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			Data.instance.setData(objdata.SpecialUpgradeVo);
			if (callBack != null) callBack();
		}
		
	}

}