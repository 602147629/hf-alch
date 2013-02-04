package happymagic.guide.api 
{
	import happymagic.guide.commands.UnlockFuncCommand;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author lite3
	 */
	public class FunctionFilterAPI 
	{
		public function hasLock(name:String):Boolean
		{
			return DataManager.getInstance().functionFilterData.filterMap[name];
		}
		
		public function unlock(name:String):void
		{
			if (DataManager.getInstance().functionFilterData.isLock(name))
			{
				new UnlockFuncCommand().unlock(name);
				DataManager.getInstance().functionFilterData.removeFilter(name);
			}
		}
		
	}

}