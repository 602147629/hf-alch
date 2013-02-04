package happyfish.guide.vo 
{
	import flash.events.Event;
	import happyfish.veal.RuntimeUtil;
	/**
	 * ...
	 * @author lite3
	 */
	public class GuideFinishVo
	{
		public var type:String;
		
		/** 与操作列表,必须都为true才行 */
		public var addList:Vector.<String>;
		/** 或操作列表,只有一个为true就行 */
		public var orList:Vector.<String>;
		
		public function GuideFinishVo(type:String, addList:Vector.<String> = null, orList:Vector.<String> = null) 
		{
			this.type = type;
			this.addList = addList;
			this.orList = orList;
		}
		
		public static function testFinish(e:Event, vo:GuideFinishVo):Boolean
		{
			if (e.type != vo.type) return false;
			
			var len:int = vo.addList ? vo.addList.length : 0;
			for (var i:int = 0; i < len; i++)
			{
				try {
					if (!RuntimeUtil.getValue(vo.addList[i], { event:e } )) return false;
				}catch (err:Error) { showErr(err); return false; }
			}
			len = vo.orList ? vo.orList.length : 0;
			for (i = 0; i < len; i++)
			{
				try {
					if (!RuntimeUtil.getValue(vo.orList[i], { event:e } )) return false;
				}catch (err:Error) { showErr(err); return false; }
			}
			return true;
		}
		
	}

}