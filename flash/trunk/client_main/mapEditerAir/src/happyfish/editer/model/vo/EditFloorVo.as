package happyfish.editer.model.vo 
{
	import com.adobe.serialization.json.JSON;
	import com.brokenfunction.json.decodeJson;
	import com.brokenfunction.json.encodeJson;
	import happyfish.model.vo.BasicVo;
	/**
	 * ...
	 * @author slamjj
	 */
	public class EditFloorVo extends BasicVo
	{
		public var cid:uint;
		public var name:String;
		public var className:String;
		public var x:uint;
		public var y:uint;
		public var z:uint;
		public var sortPriority:Number;
		public function EditFloorVo() 
		{
			
		}
		
		public function setClass(obj:EditFloorVo):EditFloorVo {
			var tmpobj:Object =JSON.decode(JSON.encode(obj));
			setData(tmpobj);
			return this;
		}
		
	}

}