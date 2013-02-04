package happyfish.guide.utils 
{
	import flash.utils.setTimeout;
	/**
	 * ...
	 * @author lite3
	 */
	public class AfterCall 
	{
		
		static public function call(fun:Function):void 
		{
			if (null == fun) return;
			setTimeout(_call, 0, fun);
		}
		
		static private function _call(fun:Function):void 
		{
			//try {
				fun();
			//}catch (err:Error) { }
		}
		
	}

}