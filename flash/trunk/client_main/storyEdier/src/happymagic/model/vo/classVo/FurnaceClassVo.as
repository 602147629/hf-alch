package happymagic.model.vo.classVo 
{
	import com.brokenfunction.json.decodeJson;
	import happyfish.model.vo.BasicVo;
	/**
	 * 工作台
	 * @author lite3
	 */
	public class FurnaceClassVo extends DecorClassVo 
	{
		public var mixCids:Array;
		public var types:Array;
		
		override public function setData(obj:Object):BasicVo 
		{
			if ("mixCids" in obj)
			{
				obj.mixCids = decodeJson(obj.mixCids);
			}
			if ("types" in obj)
			{
				obj.types = decodeJson(obj.types);
			}
			return super.setData(obj);
		}
	}
}