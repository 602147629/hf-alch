package happyfish.editer.model.vo 
{
	import com.adobe.serialization.json.JSON;
	import happyfish.editer.model.EditDataManager;
	/**
	 * ...
	 * @author slamjj
	 */
	public class EditMonsterVo extends EditMonsterClassVo 
	{
		public var id:uint;
		public var currentHp:uint;
		public var x:uint;
		public var y:uint;
		public var z:uint;
		public var fiddleRangeX:uint;
		public var fiddleRangeZ:uint;
		public var sceneId:uint;
		public var detail:String;
		public var level:uint;
		public var goHome:uint;
		public var fightBg:String="";
		public function EditMonsterVo() 
		{
			
		}
		
		public function setValue(value:Object):EditMonsterVo {
			setData(value);
			
			var objClass:EditMonsterClassVo = EditDataManager.getInstance().getClassFrom("monsterClass", "cid", cid) as EditMonsterClassVo;
			var obj:Object = JSON.decode(JSON.encode(objClass));
			setData(obj);
			
			return this;
		}
		
	}

}