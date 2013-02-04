package happyfish.editer.model.vo 
{
	import com.adobe.serialization.json.JSON;
	import happyfish.editer.model.EditDataManager;
	/**
	 * ...
	 * @author 
	 */
	public class EditNpcVo extends EditNpcClassVo 
	{
		public var id:uint;
		public var x:uint;
		public var z:uint;
		public var clickType:String="";
		public var clickValue:String=""; //1 module 	2 buyItem 	3 movie	 0 无 	4 url
		public var faceX:uint;
		public var faceZ:uint;
		public var fiddleRangeX:uint;
		public var fiddleRangeZ:uint;
		public var sceneId:int;
		public function EditNpcVo() 
		{
			
		}
		
		public function setValue(value:Object):EditNpcVo {
			setData(value);
			
			var objClass:Object = EditDataManager.getInstance().getClassFrom("npcClass", "cid", cid) as Object;
			//var obj:Object = JSON.decode(JSON.encode(objClass));
			setData(objClass);
			
			return this;
		}
		
	}

}