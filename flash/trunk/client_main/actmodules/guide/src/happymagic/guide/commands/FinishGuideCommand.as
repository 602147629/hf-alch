package happymagic.guide.commands 
{
	import happyfish.manager.InterfaceURLManager;
	import happyfish.model.DataCommandBase;
	/**
	 * ...
	 * @author lite3
	 */
	public class FinishGuideCommand extends DataCommandBase
	{
		public function finishGuide():void
		{
			//trace("finishGuide", id, isNovice);
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("finishGuide"));
			loadData();
		}
	}
}