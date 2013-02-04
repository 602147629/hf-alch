package happyfish.time 
{
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	/**
	 * ...
	 * @author 
	 */
	public class TimeActionCommand 
	{
		private var actions:Object;
		private var curIndex:uint=0;
		
		public function TimeActionCommand() 
		{
			actions = new Object();
		}
		
		/**
		 * 设置一个时间戳，到时执行回调	
		 * @param	date		时间点，时间戳
		 * @param	callback	
		 * @param	callParams
		 */
		public function setTimeAction(date:int,callback:Function,callParams:Array=null):void {
			var timeout:int = Time.getRemainingTimeByEnd(date);
			var id:Number = setTimeout(timeCallBack, timeout, curIndex);
			actions[curIndex] = { id:id, callback:callback, callParams:callParams };
			curIndex++;
		}
		
		private function timeCallBack(index:uint):void {
			if (!actions[index]) 
			{
				return;
			}
			if (actions[index].callback!=null) 
			{
				actions[index].callback.apply(null, actions[index].callParams);
			}
			actions[index] = null;
		}
		
		public function clearAll():void {
			for (var name:String in actions) 
			{
				clearTimeout(Number(name));
			}
			
			actions = new Object();
			curIndex = 0;
		}
		
	}

}