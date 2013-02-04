package happyfish.veal 
{
	import happyfish.veal.parse.ParamMap;
	import happyfish.veal.parse.Parse;
	/**
	 * ...
	 * @author lite3
	 */
	public class RuntimeUtil 
	{
		
		public static function getBaseParamMap():ParamMap
		{
			return Parse.baseParamMap;
		}
		
		/**
		 * 获取当前输入字符串的运行时值
		 * @param	str 表达式 支持".", "()"这两个操作符. 
		 * @param	paramMap Map<String, *>的类型, s里的{key}会在运行时替换为Map[key]
		 * @return
		 */
		public static function getValue(s:String,  paramMap:Object = null):*
		{
			return new Parse(s, paramMap).getValue();
		}
		
		/**
		 * 
		 * @param	s
		 * @return
		 */
		public static function getValueFun(s:String, paramMap:Object = null):Function 
		{
			return function():* {
				return new Parse(s, paramMap).getValue();
			}
		}
	}
}