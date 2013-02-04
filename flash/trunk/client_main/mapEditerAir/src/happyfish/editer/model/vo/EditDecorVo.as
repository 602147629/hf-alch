package happyfish.editer.model.vo 
{
	import com.adobe.serialization.json.JSON;
	import happyfish.editer.model.EditDataManager;
	/**
	 * ...
	 * @author slamjj
	 */
	public class EditDecorVo extends EditDecorClassVo
	{
		public var id:uint;
		public var x:uint;
		public var z:uint;
		public var mirror:int;
		public var sceneId:uint;
		public function EditDecorVo() 
		{
			
		}
		
		public function setValue(value:Object):EditDecorVo {
			setData(value);
			
			var objClass:EditDecorClassVo = EditDataManager.getInstance().getClassFrom("decorClass", "cid", cid) as EditDecorClassVo;
			var obj:Object = JSON.decode(JSON.encode(objClass));
			setData(obj);
			
			return this;
		}
		
	}

}