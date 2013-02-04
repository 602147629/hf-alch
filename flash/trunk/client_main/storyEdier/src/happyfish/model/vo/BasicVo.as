package happyfish.model.vo 
{
	import flash.utils.describeType;
	import flash.utils.getDefinitionByName;
	import flash.utils.getQualifiedClassName;
	/**
	 * ...
	 * @author slam
	 */
	public class BasicVo
	{
		
		public function BasicVo() 
		{
			
		}
		
		public function setData(obj:Object):BasicVo {
			for (var name:String in obj) 
			{
				if ( this.hasOwnProperty(name)) 
				{
					this[name] = obj[name];
				}
			}
			if ("Object" == getQualifiedClassName(obj)) return this;
			
			var thisObj:Object = BasicVo.toObject(this, false, true, true);
			var sourceObj:Object = BasicVo.toObject(obj, true, false, true);
			for (var key:String in thisObj)
			{
				if (sourceObj[key] !== undefined) this[key] = sourceObj[key];
			}
			
			return this;
		}
		
		/**
		 * 
		 * @param	source
		 * @param	readonly
		 * @param	writeonly 只写时返回null
		 * @param	readwrite
		 * @return
		 */
		public static function toObject(source:*, readonly:Boolean = true, writeonly:Boolean = false, readwrite:Boolean = true):Object
		{
			var accMap:Object = { readonly:readonly, writeonly:writeonly, readwrite:readwrite };
			var xml:XML = describeType(source);
			var o:Object = { };
			for each(var tmp:XML in xml.accessor)
			{
				var acc:String = tmp.@access;
				if (accMap[acc])
				{
					var k:String = tmp.@name;
					o[k] = "writeonly" == acc ? null : source[k];
				}
			}
			for each(tmp in xml.variable)
			{
				k = tmp.@name;
				o[k] = source[k];
			}
			return o;
		}
		
	}

}