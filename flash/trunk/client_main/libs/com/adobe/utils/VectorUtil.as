package com.adobe.utils 
{
	/**
	 * ...
	 * @author lite3
	 */
	public class VectorUtil 
	{
		
		public static function toArray(v:*):Array
		{
			if (!v) return null;
			var len:int = v.length;
			var arr:Array = [];
			for (var i:int = 0; i < len; i++)
			{
				arr[i] = v[i];
			}
			return arr;
		}
		
	}

}