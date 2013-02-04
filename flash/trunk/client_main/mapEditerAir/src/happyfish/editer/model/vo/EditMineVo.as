package happyfish.editer.model.vo 
{
	import com.adobe.serialization.json.JSON;
	import happyfish.editer.model.EditDataManager;
	/**
	 * ...
	 * @author slamjj
	 */
	public class EditMineVo extends EditMineClassVo 
	{
		public var id:uint;
		public var currentHp:uint;
		public var x:uint;
		public var y:uint;
		public var z:uint;
		public var sceneId:uint;
		
		public function EditMineVo() 
		{
			
		}
		
		public function setValue(value:Object):EditMineVo {
			setData(value);
			
			var objClass:EditMineClassVo = EditDataManager.getInstance().getClassFrom("mineClass", "cid", cid) as EditMineClassVo;
			var obj:Object = JSON.decode(JSON.encode(objClass));
			setData(obj);
			
			return this;
		}
		
	}

}