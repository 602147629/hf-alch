package happymagic.guide.commands 
{
	import happyfish.manager.InterfaceURLManager;
	import happymagic.model.command.BaseDataCommand;
	/**
	 * ...
	 * @author lite3
	 */
	public class UpdateGuideCommand extends BaseDataCommand
	{
		public function update(idx:int):void
		{
			//trace("send ", id, idx, isNovice);
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("updateGuide"), { idx:idx } );
			loadData();
		}
	}
}