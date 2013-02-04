package happyfish.storyEdit.model.command 
{
	import happyfish.model.DataCommandBase;
	
	/**
	 * ...
	 * @author jj
	 */
	public class LoadStoryListCommand extends DataCommandBase 
	{
		
		public function LoadStoryListCommand() 
		{
			createLoad();
			
		}
		
		override public function loadData(url:String):void 
		{
			createLoad();
			createRequest(url);
			
			super.loadData();
		}
		
	}

}