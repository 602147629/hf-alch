package happyfish.veal.parse 
{
	/**
	 * ...
	 * @author lite3
	 */
	public class ParamMap 
	{
		
		private const o:Object = { };
		
		public function getValue(name:String):* { return o[name]; }
		
		public function merge(p:Object):Object
		{
			var r:Object = { };
			for (var key:String in o)
			{
				r[key] = o[key];
			}
			if (!p) return r;
			for (key in p)
			{
				r[key] = p[key];
			}
			return r;
		}
		
		public function addParam(key:String, value:*):Boolean
		{
			var has:Boolean = o.hasOwnProperty(key);
			o[key] = value;
			return has;
		}
		
		public function removeParam(key:String):Boolean
		{
			var has:Boolean = o.hasOwnProperty(key);
			delete o[key];
			return has;
		}
		
		public function addParamMap(p:Object):void
		{
			if (!p) return ;
			for (var key:String in p)
			{
				o[key] = p[key];
			}
		}
		
		public function removeAll():void
		{
			for (var key:String in o)
			{
				delete o[key];
			}
		}
	}
}