package happyfish.editer.control 
{
	import adobe.utils.CustomActions;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import happyfish.editer.scene.view.EditSceneView;
	import happyfish.editer.scene.view.EditTileItemView;
	import happyfish.editer.scene.view.EditTileMapView;
	import happyfish.editer.view.Main;
	import happyfish.utils.display.FiltersDomain;
	
	/**
	 * ...
	 * @author 
	 */
	public class EditGetSampleControl extends EditerControl  
	{
		private var type:String;
		private var sceneView:EditSceneView;
		
		public function EditGetSampleControl(_main:Main,mapview:Sprite,type:String) 
		{
			super(_main, mapview);
			this.type = type;
			sceneView = main.mapSprite;
			main.editer.getSampleBtn.filters = [FiltersDomain.blueGlow];
		}
		
		override protected function beginDrag(e:MouseEvent):void 
		{
			super.beginDrag(e);
			var tmpfloor:EditTileItemView;
			switch (type) 
			{
				case "地板":
					tmpfloor = sceneView.tileMap.getTileView(pos.x, pos.z, 1);
					main.editer.setCurSelectClassByCid(tmpfloor.data.cid);
				break;
				case "地板2":
					tmpfloor = sceneView.tileMap.getTileView(pos.x, pos.z, 2);
					main.editer.setCurSelectClassByCid(tmpfloor.data.cid);
				break;
			}
			
			//var item:EditIsoItem = main.mapSprite.itemContainer.getItemByPos(pos.x, pos.z);
			//if (item) 
			//{
				//main.editer.showDataInfo(item);
			//}
			main.editer.getSampleBtn.filters = [];
			main.editer.initControl();
			
			//clear();
		}
		
		
		
	}

}