package happymagic.model.data 
{
	import happyfish.manager.EventManager;
	import happymagic.events.FunctionFilterEvent;
	/**
	 * ...
	 * @author lite3
	 */
	public class FunctionFilterData 
	{
		
		// Map<String:功能名(比如Order), true>
		public const filterMap:Object = { };
		public function FunctionFilterData() 
		{
			
		}
		
		public function isLock(name:String):Boolean
		{
			return filterMap[name] != null;
		}
		
		public function addFilter(name:String):void
		{
			if (!filterMap[name])
			{
				filterMap[name] = true;
			}
		}
		
		public function removeFilter(name:String):void
		{
			if (filterMap[name])
			{
				delete filterMap[name];
				EventManager.dispatchEvent(new FunctionFilterEvent(FunctionFilterEvent.UNLOCK, name));
			}
			
		}
		
		public function setLockList(arr:Array):void 
		{
			for (var i:int = arr.length - 1; i >= 0; i--)
			{
				addFilter(arr[i]);
			}
		}
	}

}