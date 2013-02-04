package happyfish.editer.model.vo 
{
	import com.adobe.serialization.json.JSON;
	import happyfish.editer.model.EditDataManager;
	/**
	 * ...
	 * @author slamjj
	 */
	public class EditPortalVo extends EditPortalClassVo 
	{
		public var id:uint;
		public var x:uint;
		public var z:uint;
		public var mirror:int;
		public var sceneId:uint;
		public var targetSceneId:uint;
		public var targetSceneName:String;
		public function EditPortalVo() 
		{
			
		}
		
		public function setValue(value:Object):EditPortalVo {
			setData(value);
			
			var objClass:EditPortalClassVo = EditDataManager.getInstance().getClassFrom("portalClass", "cid", cid) as EditPortalClassVo;
			var obj:Object = JSON.decode(JSON.encode(objClass));
			setData(obj);
			
			return this;
		}
		
	}

}