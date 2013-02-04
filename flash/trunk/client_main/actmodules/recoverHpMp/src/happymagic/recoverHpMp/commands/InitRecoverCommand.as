package happymagic.recoverHpMp.commands 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.recoverHpMp.model.Data;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class InitRecoverCommand extends BaseDataCommand 
	{
		public function init(callback:Function):void
		{
			this.callBack = callback;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("initRecover"));
			loadData();
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			Data.instance.setData(objdata.RoleUpgradeStarVo);
			if (callBack != null) callBack();
		}
		
	}

}