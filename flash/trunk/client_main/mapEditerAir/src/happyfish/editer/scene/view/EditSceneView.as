package happyfish.editer.scene.view 
{
	import com.brokenfunction.json.decodeJson;
	import flash.display.Sprite;
	import flash.geom.PerspectiveProjection;
	import flash.utils.getQualifiedClassName;
	import happyfish.editer.model.EditDataManager;
	import happyfish.editer.model.vo.EditFloorVo;
	import happyfish.editer.model.vo.MapClassVo;
	import happyfish.editer.view.GridWorld;
	import happyfish.editer.view.ItemDataInfoView;
	import happyfish.editer.view.MapView;
	import happyfish.scene.iso.IsoLayer;
	import happyfish.scene.iso.IsoUtil;
	
	/**
	 * ...
	 * @author ...
	 */
	public class EditSceneView extends Sprite 
	{
		public var data:MapClassVo;
		private var items:Object;
		public var map:MapView;
		public var gridView:GridWorld;
		public var itemContainer:EditIsoLayer;
		public var tileMap:EditTileMapView;
		private var camera:Sprite;
		
		
		public function EditSceneView() 
		{
			camera = new Sprite();
			addChild(camera);
			
			map = new MapView();
			
			itemContainer = new EditIsoLayer(true);
			
			gridView = new GridWorld();
			
			tileMap = new EditTileMapView();
			
			camera.addChild(map);
			camera.addChild(tileMap);
			camera.addChild(itemContainer);
			camera.addChild(gridView);
		}
		
		public function clear():void {
			map.clear();
			
			gridView.clear();
			
			tileMap.clear();
			
			itemContainer.clear();
		}
		
		public function setMapClass(value:Object):void {
			
			var i:int;
			
			data = new MapClassVo().setData(value) as MapClassVo;
			EditDataManager.getInstance().curMapClass = data;
			
			itemContainer.y = -(IsoUtil.TILE_SIZE / 2) * data.numCols+16;
			itemContainer.x = 32;
			
			items = new Object();
			
			//加载背景
			map.setData(data.bg,data.bgType, data.frame);
			
			//创建行走格表现
			gridView.setData(data.nodeStr, 32, data.numCols);
			
			//创建地板
			//获得此场景中地板列表
			var tmpfloorList:Array = EditDataManager.getInstance().getFloors(data.sceneId);
			var tmpfloorList2:Array = EditDataManager.getInstance().getFloors2(data.sceneId);
			
			//如果地板为空,就创建一组空地板数据
			if (!tmpfloorList) 
			{
				tmpfloorList = createFloorList(data.numCols);
			}
			if (!tmpfloorList2) 
			{
				tmpfloorList2 = createFloorList(data.numCols,1);
			}
			//tmpfloorList2.forEach(initFloor2Data);
			
			tileMap.setData(tmpfloorList,tmpfloorList2);
			
			//创建门
			var portals:Array = EditDataManager.getInstance().getCustomListBySceneId("portalList",data.sceneId);
			for (i = 0; i < portals.length; i++) 
			{
				addItemByData(EditPortalView, portals[i]);
			}
			
			//创建DECOR
			var decors:Array = EditDataManager.getInstance().getCustomListBySceneId("decorList",data.sceneId);
			for (i = 0; i < decors.length; i++) 
			{
				addItemByData(EditDecorView, decors[i]);
			}
			
			//创建矿
			var mines:Array = EditDataManager.getInstance().getCustomListBySceneId("mineList",data.sceneId);
			for (i = 0; i < mines.length; i++) 
			{
				addItemByData(EditMineView, mines[i]);
			}
			
			//创建怪
			var monsters:Array = EditDataManager.getInstance().getCustomListBySceneId("monsterList",data.sceneId);
			for (i = 0; i < monsters.length; i++) 
			{
				addItemByData(EditMonsterView, monsters[i]);
			}
			
			//创建NPC
			var npcs:Array = EditDataManager.getInstance().getCustomListBySceneId("npcList",data.sceneId);
			for (i = 0; i < npcs.length; i++) 
			{
				addItemByData(EditNpcView, npcs[i]);
			}
		}
		
		private function initFloor2Data(item:*,index:Number,arr:Array):void 
		{
			item.sortPriority = 1;
		}
		
		private function addItemByData(typeClass:Class,value:Object):void {
			var item:EditIsoItem = new typeClass(value,itemContainer,addItemByData_complete) as EditIsoItem;
		}
		
		private function addItemByData_complete():void 
		{
			itemContainer.sort();
		}
		
		public function setFloor(_x:uint, _z:uint, floorVo:EditFloorVo):void {
			floorVo.sortPriority = 0;
			tileMap.setTile(_x, _z, floorVo);
		}
		public function setFloor2(_x:uint, _z:uint, floorVo:EditFloorVo):void {
			floorVo.sortPriority = 1;
			tileMap.setTile(_x, _z, floorVo);
		}
		
		public function createFloorList(size:uint,sortPriority:int=0):Array {
			var arr:Array = new Array();
			var tmptile:EditFloorVo;
			for (var i:int = 0; i < size; i++) 
			{
				arr.push(new Array());
				for (var j:int = 0; j < size; j++) 
				{
					tmptile = new EditFloorVo();
					tmptile.x = i;
					tmptile.z = j;
					tmptile.sortPriority = sortPriority;
					arr[i].push(tmptile);
				}
			}
			return arr;
		}
		
		/**
		 * 输出当前场景的数据
		 * @return
		 */
		public function outData():Object {
			
			var obj:Object = new Object();
			
			var curMapData:String = gridView.outData();
			data.nodeStr = curMapData;
			
			obj.mapClass = data;
			
			//FLOOR
			var floor:Object = EditDataManager.getInstance().getCustomListBySceneId("floorList", data.sceneId);
			if (!floor) 
			{
				floor = new Object();
				floor.sceneId = data.sceneId;
			}
			floor.floorList = getListFromIsoLayer(tileMap.tileSprite, EditTileItemView);
			floor = EditDataManager.getInstance().initFloorDatasToObj(floor);
			obj.floor = floor;
			
			var floor2:Object = EditDataManager.getInstance().getCustomListBySceneId("floorList2", data.sceneId);
			if (!floor2) 
			{
				floor2 = new Object();
				floor2.sceneId = data.sceneId;
			}
			floor2.floorList = getListFromIsoLayer(tileMap.tileSprite, EditTileItemView,1);
			floor2 = EditDataManager.getInstance().initFloorDatasToObj(floor2);
			obj.floor2 = floor2;
			
			//DECOR
			var decors:Array = getListFromIsoLayer(itemContainer, EditDecorView);
			obj.decors = decors;
			
			//PORTAL
			var portals:Array = getListFromIsoLayer(itemContainer, EditPortalView);
			obj.portals = portals;
			
			//MINE
			var mines:Array = getListFromIsoLayer(itemContainer, EditMineView);
			obj.mines = mines;
			
			//MONSTER
			var monsters:Array = getListFromIsoLayer(itemContainer, EditMonsterView);
			obj.monsters = monsters;
			
			//NPC
			var npcs:Array = getListFromIsoLayer(itemContainer, EditNpcView);
			obj.npcs = npcs;
			
			return obj;
			
		}
		
		private function getListFromIsoLayer(layer:EditIsoLayer, keyClass:Class,sortP:int=0):Array {
			var arr:Array = new Array();
			for (var name:String in layer.items) 
			{
				//trace(getQualifiedClassName(layer.items[name]),getQualifiedClassName(keyClass));
				if (getQualifiedClassName(layer.items[name]) == getQualifiedClassName(keyClass)) 
				{
					if ((layer.items[name] as EditIsoItem).sortPriority==sortP) 
					{
						arr.push((layer.items[name] as EditIsoItem).data);
					}
					
				}
			}
			
			return arr;
		}
		
	}

}