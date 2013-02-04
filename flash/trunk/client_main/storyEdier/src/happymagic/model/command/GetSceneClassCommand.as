package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.SceneClassVo;
	/**
	 * ...
	 * @author jj
	 */
	public class GetSceneClassCommand extends BaseDataCommand
	{
		
		public function getSceneClass(sceneId:uint):void
		{
			playStory = false;
			//compress = true;
			createLoad();
			var mapCopyClass:Object = DataManager.getInstance().mapCopyClass;
			var url:String = mapCopyClass[String(sceneId)];
			createRequest(url.replace("api","editstory") , "GET");
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			var datam:DataManager = DataManager.getInstance();
			var list:Array;
			var arr:Array;
			var i:int;
			
			if (objdata.itemClass)
			{
				arr = objdata.itemClass;
				list = new Array;
				for (i = 0; i < arr.length; i++)
				{
					if (datam.itemData.getItemByCid(arr[i]["cid"]) == null) list.push(arr[i]);
				}
				datam.itemData.setItemClassList(list);
			}
			
			if (objdata.sceneClass)
			{
				if (datam.getSceneClassById(objdata.sceneClass.sceneId) == null)
				{
					datam.sceneClass.push(new SceneClassVo().setData(objdata.sceneClass));
				}
			}
			
			if (objdata.monsterClass)
			{
				arr = objdata.monsterClass;
				for (i = 0; i < arr.length; i++) {
					if (datam.getMonsterClassByCid(arr[i].cid) == null) 
					{
						datam.monsterClass.push(arr[i]);
					}
				}
			}
			
			if (objdata.mineClass)
			{
				arr = objdata.mineClass;
				for (i = 0; i < arr.length; i++) {
					if (datam.getMineClassByCid(arr[i].cid) == null) 
					{
						datam.mineClass.push(arr[i]);
					}
				}
			}
			
			if (objdata.npcClass) 
			{
				arr = objdata.npcClass;
				for (i = 0; i < arr.length; i++) {
					if (datam.getNpcClassByNpcId(arr[i].cid) == null) 
					{
						datam.npcClass.push(arr[i]);
					}
				}
			}
			
			commandComplete();
		}
		
	}

}