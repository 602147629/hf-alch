package 
{
	import com.adobe.utils.ArrayUtil;
	import com.brokenfunction.json.decodeJson;
	import com.brokenfunction.json.encodeJson;
	import fl.data.DataProvider;
	import flash.display.BitmapData;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.editer.model.EditDataManager;
	import happyfish.editer.model.vo.EditDecorClassVo;
	import happyfish.editer.model.vo.EditFloorVo;
	import happyfish.editer.model.vo.EditMineClassVo;
	import happyfish.editer.model.vo.EditMonsterClassVo;
	import happyfish.editer.model.vo.EditPortalClassVo;
	import happyfish.editer.model.vo.MapClassVo;
	
	/**
	 * ...
	 * @author 
	 */
	public class Main extends Sprite 
	{
		private var uiView:mainUi;
		private var needLoadDatas:Array;
		
		private var mapClass:Array;
		private var decorClass:Array;
		private var portalClass:Array;
		private var mineClass:Array;
		private var monsterClass:Array;
		
		public function Main():void 
		{
			uiView = new mainUi();
			addChild(uiView);
			uiView.mapListCombox.labelField = "name";
			uiView.addEventListener(MouseEvent.CLICK, clickFun);
			
			loadCsv();
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case uiView.mapBtn:
					outData();
				break;
			}
		}
		
		private function outData():void 
		{
			var dm:EditDataManager = EditDataManager.getInstance();
			
			var obj:Object = new Object();
			
			var mapclass:MapClassVo = new MapClassVo().setData(uiView.mapListCombox.selectedItem) as MapClassVo;
			
			var arr:Array;
			var decorClassMap:Object = new Object();
			var tmpDecorClass:Object;
			var decorClass:Array = new Array();
			var portalClass:Array = new Array();
			var mineClass:Array = new Array();
			var monsterClass:Array = new Array();
			var npcClass:Array = new Array();
			var i:int;
			var j:int;
			
			var tmpsceneclass:Object=new Object();
			tmpsceneclass.sceneId = mapclass.sceneId;
			tmpsceneclass.type = mapclass.type;
			tmpsceneclass.name = mapclass.name;
			tmpsceneclass.content = mapclass.content;
			tmpsceneclass.needLevel = mapclass.needLevel;
			tmpsceneclass.bg = mapclass.bg;
			tmpsceneclass.bgSound = mapclass.bgSound;
			tmpsceneclass.nodeStr = mapclass.nodeStr;
			tmpsceneclass.numCols = mapclass.numCols;
			tmpsceneclass.numRows = mapclass.numRows;
			tmpsceneclass.isoStartX = mapclass.isoStartX;
			tmpsceneclass.isoStartZ = mapclass.isoStartZ;
			tmpsceneclass.parentSceneId = mapclass.parentSceneId;
			tmpsceneclass.entrances = mapclass.entrances;
			tmpsceneclass.jump = mapclass.jump;
			tmpsceneclass.withFog = mapclass.withFog;
			tmpsceneclass.fightBg = mapclass.fightBg;
			
			//地板
			
			arr = dm.getCustomListBySceneId("floorList", mapclass.sceneId);
			if (arr.length>0) 
			{
				tmpsceneclass.floorList = decodeJson(arr[0].data);
			}
			for (i = 0; i < tmpsceneclass.floorList.length; i++) 
			{
				for (j = 0; j < tmpsceneclass.floorList[i].length; j++) 
				{
					if (tmpsceneclass.floorList[i][j]) 
					{
						tmpDecorClass = dm.getClassFrom("floorClass", "cid", tmpsceneclass.floorList[i][j]);
						if (tmpDecorClass) 
						{
							decorClassMap[tmpsceneclass.floorList[i][j]] = tmpDecorClass;
						}
					}
				}
			}
			
			arr = dm.getCustomListBySceneId("floorList2", mapclass.sceneId);
			if (arr.length>0) 
			{
				tmpsceneclass.floorList2 = decodeJson(arr[0].data);
			}
			for (i = 0; i < tmpsceneclass.floorList2.length; i++) 
			{
				for (j = 0; j < tmpsceneclass.floorList2[i].length; j++) 
				{
					
					if (tmpsceneclass.floorList2[i][j]) 
					{
						tmpDecorClass = dm.getClassFrom("floorClass", "cid", tmpsceneclass.floorList2[i][j]);
						if (tmpDecorClass) 
						{
							decorClassMap[tmpsceneclass.floorList2[i][j]] = tmpDecorClass;
						}
					}
				}
			}
			
			for (var name:String in decorClassMap) 
			{
				decorClass.push(decorClassMap[name]);
			}
			
			
			//装饰物
			arr = dm.getCustomListBySceneId("decorList", mapclass.sceneId);
			tmpsceneclass.decorList = dm.turnCustomDataToObj(arr, ["id", "cid", "x", "z", "mirror"]);
			
			for (i = 0; i < tmpsceneclass.decorList.length; i++) 
			{
				decorClass.push(dm.getClassFrom("decorClass", "cid", tmpsceneclass.decorList[i].cid) as EditDecorClassVo);
			}
			
			
			//门
			arr = dm.getCustomListBySceneId("portalList", mapclass.sceneId);
			tmpsceneclass.portalList = dm.turnCustomDataToObj(arr, ["id", "cid", "x", "z", "mirror", "targetSceneId","targetSceneName"]);
			for (i = 0; i < tmpsceneclass.portalList.length; i++) 
			{
				decorClass.push(dm.getClassFrom("portalClass", "cid", tmpsceneclass.portalList[i].cid) as EditPortalClassVo);
			}
			//矿
			arr = dm.getCustomListBySceneId("mineList", mapclass.sceneId);
			tmpsceneclass.mineList = dm.turnCustomDataToObj(arr, ["id", "cid", "x", "z"]);
			for (i = 0; i < tmpsceneclass.mineList.length; i++) 
			{
				mineClass.push(dm.getClassFrom("mineClass", "cid", tmpsceneclass.mineList[i].cid) as EditMineClassVo);
			}
			//怪
			arr = dm.getCustomListBySceneId("monsterList", mapclass.sceneId);
			tmpsceneclass.monsterList = dm.turnCustomDataToObj(arr, ["id","cid","x","z","fiddleRangeX","fiddleRangeZ","level","fightBg"]);
			for (i = 0; i < tmpsceneclass.monsterList.length; i++) 
			{
				monsterClass.push(dm.getClassFrom("monsterClass", "cid", tmpsceneclass.monsterList[i].cid) as EditMonsterClassVo);
			}
			
			//npc
			arr = dm.getCustomListBySceneId("npcList", mapclass.sceneId);
			tmpsceneclass.npcList = dm.turnCustomDataToObj(arr, ["id","cid","x","z","faceX","faceZ","fiddleRangeX","fiddleRangeZ","clickType","clickValue"]);
			for (i = 0; i < arr.length; i++) 
			{
				npcClass.push(dm.getClassFrom("npcClass", "cid", arr[i].cid));
			}
			
			
			obj.sceneClass = tmpsceneclass;
			
			//*************静态************
			//装饰物\地板
			obj.itemClass = dm.turnCustomDataToObj(decorClass, ["cid","sizeX","sizeZ","className","name"],"cid");
			
			//门
			//obj.portalClass = dm.turnCustomDataToObj(portalClass, ["cid","sizeX","sizeZ","className","name"]);
			//矿
			obj.mineClass = dm.turnCustomDataToObj(mineClass, ["cid", "sizeX", "sizeZ", "className", "name", "maxHp", "conditions"],"cid");
			//怪
			obj.monsterClass = dm.turnCustomDataToObj(monsterClass, ["cid", "sizeX", "sizeZ", "className", "name", "maxHp", "conditions","collisionRange"],"cid");
			//npc
			obj.npcClass = dm.turnCustomDataToObj(npcClass, ["cid","name","className","faceClass"],"cid");
			
			var outJsonStr:String = encodeJson(obj);
			
			uiView.txt.text = outJsonStr;
		}
		
		private function loadCsv():void 
		{
			needLoadDatas = ["mapClass", "decorClass", "avatarClass", "mineClass", "monsterClass", "portalClass", 
			"floorClass","npcClass","mineList","monsterList","portalList","decorList","floorList","floorList2","npcList"
			];
			loadStaticData(needLoadDatas.shift());
			
		}
		
		private function loadStaticData(dataName:String):void 
		{
			trace("loadStaticData",dataName);
			var loader:CsvLoader = new CsvLoader(dataName);
			
			loader.addEventListener(Event.COMPLETE, loadStaticData_complete);
			loader.loadURL(dataName+".csv"+"?=" + new Date().getTime());
		}
		
		private function loadStaticData_complete(e:Event):void 
		{
			var loader:CsvLoader = e.target as CsvLoader;
			loader.removeEventListener(Event.COMPLETE, loadStaticData_complete);
			
			var tmpData:Array = loader.getRecords();
			
			EditDataManager.getInstance().setVar(loader.name, tmpData);
			
			if (needLoadDatas.length>0) 
			{
				loadStaticData(needLoadDatas.shift());
			}else {
				start();
			}
			
		}
		
		private function start():void 
		{
			uiView.txt.text = "加载数据完成";
			
			mapClass = EditDataManager.getInstance().getVar("mapClass");
			uiView.mapListCombox.dataProvider = new DataProvider(mapClass);
			
			decorClass=EditDataManager.getInstance().getVar("decorClass");
			portalClass=EditDataManager.getInstance().getVar("portalClass");
			mineClass=EditDataManager.getInstance().getVar("mineClass");
			monsterClass = EditDataManager.getInstance().getVar("monsterClass");
		}
		
	}
	
}