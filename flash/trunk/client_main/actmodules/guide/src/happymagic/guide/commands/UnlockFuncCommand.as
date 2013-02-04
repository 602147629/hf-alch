package happymagic.guide.commands 
{
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.command.BaseDataCommand;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class UnlockFuncCommand extends BaseDataCommand 
	{
		
		public function unlock(name:String):void 
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("funcUnlock"), { func:name } );
			loadData();
		}
		
	}

}