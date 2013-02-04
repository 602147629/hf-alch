package happyfish.editer.model 
{
	import com.brokenfunction.json.decodeJson;
	import com.brokenfunction.json.encodeJson;
	import flash.system.System;
	import happyfish.editer.model.vo.EditAvatarClassVo;
	import happyfish.editer.model.vo.EditDecorClassVo;
	import happyfish.editer.model.vo.EditDecorVo;
	import happyfish.editer.model.vo.EditFloorVo;
	import happyfish.editer.model.vo.EditIsoItemVo;
	import happyfish.editer.model.vo.EditMineClassVo;
	import happyfish.editer.model.vo.EditMineVo;
	import happyfish.editer.model.vo.EditMonsterClassVo;
	import happyfish.editer.model.vo.EditMonsterVo;
	import happyfish.editer.model.vo.EditPortalClassVo;
	import happyfish.editer.model.vo.EditPortalVo;
	import happyfish.editer.model.vo.MapClassVo;
	import happyfish.file.json.JsonFiler;
	import happymagic.manager.PublicDomain;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class EditDataManager 
	{
		public var curMapClass:MapClassVo;
		public var showOutStrFunc:Function;
		
		private const CLASSDATA_PATH:String = "classdata/";
		private const DATA_PATH:String = "data/";
		
		public function EditDataManager(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
				}
			}
			else
			{	
				throw new Error( "EditDataManager"+"单例" );
			}
		}
		
		public function showStr(...args):void {
			if (showOutStrFunc!=null) 
			{
				showOutStrFunc.apply(null, args);
			}
			
		}
		
		private function createCSVFileName(fileName:String, ver:uint):String {
			return fileName + "_" + ver.toString() + ".csv";
		}
		
		/*public function saveSceneToData(mapData:MapClassVo, floor:Object, decors:Array, portals:Array, mines:Array, monsters:Array):void {
			var arr:Array;
			
			//avatarClass
			arr = EditDataManager.getInstance().getVar("avatarClass") as Array;
			saveDataToJson("avatarClass.txt",
			["avatarId","name","className"], 
			arr);
			
			//保存DECOR CLASS
			//地板
			arr = EditDataManager.getInstance().getVar("floorClass") as Array;
			saveDataToJson("floorClass.txt",
			["cid","name","className"], 
			arr);
			
			//装饰物
			arr = EditDataManager.getInstance().getVar("decorClass") as Array;
			saveDataToJson("decorClass.txt",
			["cid","name","className","sizeX","sizeZ"], 
			arr);
			
			//保存PORTAL CLASS
			arr = EditDataManager.getInstance().getVar("portalClass") as Array;
			saveDataToJson("portalClass.txt",
			["cid","name","className","sizeX","sizeZ"], 
			arr);
			
			//保存MINE CLASS
			arr = EditDataManager.getInstance().getVar("mineClass") as Array;
			saveDataToJson("mineClass.txt",
			["cid","name","avatarId","maxHp","sizeX","sizeZ","conditions"], 
			arr);
			
			//保存MONSTER CLASS
			arr = EditDataManager.getInstance().getVar("monsterClass") as Array;
			saveDataToJson("monsterClass.txt",
			["cid","name","avatarId","maxHp","sizeX","sizeZ"], 
			arr);
			
			//场景class
			arr = EditDataManager.getInstance().getVar("mapClass") as Array;
			saveDataToJson("mapClass.txt",
			["sceneId","type","name","content","bg","bgSound","nodeStr","entrances","numCols","numRows","isoStartX","isoStartZ","parentSceneId","needLevel","jump"], 
			arr);
			
			//生成场景数据
			var sceneData:Object = new Object();
			sceneData.sceneId = mapData.sceneId;
			sceneData.jump = mapData.jump;
			sceneData.decorList = turnCustomDataToObj(decors, ["cid","id","x","z","mirror"]);
			sceneData.portalList = turnCustomDataToObj(portals, ["cid","id","x","z","targetSceneId","mirror","vpath"]);
			sceneData.monsterList = turnCustomDataToObj(monsters, ["cid","id","x","z","currentHp","fiddleRangeX","fiddleRangeZ"]);
			sceneData.mineList = turnCustomDataToObj(mines, ["cid", "id", "x", "z", "currentHp"]);
			floor = initFloorDatasToObj(floor);
			sceneData.floorList = floor.data;
			
			saveDataToJson("sceneData.txt",
			["sceneId","portalList","monsterList","mineList","floorList"], 
			[sceneData]);
			
			
		}*/
		
		public function saveSceneData(mapData:MapClassVo, floor:Object,floor2:Object, decors:Array, portals:Array, mines:Array, monsters:Array,npcs:Array):void {
			var arr:Array;
			
			//npc
			arr = switchDataListByKey("npcList", "sceneId", mapData.sceneId, npcs, "id");
			saveDataToCsv("npcList.csv",
			["id", "cid","sceneId", "name", "className", "faceClass", "x", "z", "clickType", "clickValue",
				"faceX","faceZ","fiddleRangeX","fiddleRangeZ"],
			arr,DATA_PATH);
			
			//地图数据
			arr = setDataByKey("mapClass", "sceneId", mapData.sceneId, mapData.outObject());
			
			saveDataToCsv("mapClass.csv",
			["sceneId", "type", "name", "content", "bg", "bgSound", "nodeStr", "entrances", "numCols", "numRows", "isoStartX", "isoStartZ",
			"parentSceneId","needLevel", "jump","bgType","withFog","fightBg"], 
			arr,CLASSDATA_PATH);
			
			//地板
			floor.sceneId = mapData.sceneId;
			floor = initFloorDatasToObj(floor);
			arr = setDataByKey("floorList", "sceneId", mapData.sceneId, floor);
			saveDataToCsv("floorList.csv",
			["sceneId","data"], 
			arr,DATA_PATH);
			
			floor2.sceneId = mapData.sceneId;
			floor2 = initFloorDatasToObj(floor2);
			arr = setDataByKey("floorList2", "sceneId", mapData.sceneId, floor2);
			saveDataToCsv("floorList2.csv",
			["sceneId","data"], 
			arr,DATA_PATH);
			
			//装饰物
			arr = switchDataListByKey("decorList", "sceneId", mapData.sceneId, decors,"id");
			saveDataToCsv("decorList.csv",
			["id","sceneId","cid","x","z","mirror"], 
			arr,DATA_PATH);
			
			//传送门
			arr = switchDataListByKey("portalList", "sceneId", mapData.sceneId, portals,"id");
			saveDataToCsv("portalList.csv",
			["id","sceneId","cid","x","z","mirror","targetSceneId","vpath","targetSceneName"], 
			arr,DATA_PATH);
			
			//矿
			arr = switchDataListByKey("mineList", "sceneId", mapData.sceneId, mines,"id");
			saveDataToCsv("mineList.csv",
			["id","sceneId","cid","x","z","mineper"], 
			arr,DATA_PATH);
			
			//怪
			arr = switchDataListByKey("monsterList", "sceneId", mapData.sceneId, monsters,"id");
			saveDataToCsv("monsterList.csv",
			["id", "sceneId", "cid", "x", "z", "fiddleRangeX", "fiddleRangeZ", "monsterper", "detail", "collisionRange",
				"goHome","fightBg"],
			arr,DATA_PATH);
			
			
		}
		
		public function turnCustomDataToObj(value:Array,attList:Array,key:String=""):Array {
			var tmp:Object;
			var arr:Array = new Array();
			for (var i:int = 0; i < value.length; i++) 
			{
				if (key.length>0) 
				{
					if (getObjByKeyFromArr(arr,key,value[i][key])) 
					{
						continue;
					}
				}
				
				tmp = new Object();
				for (var j:int = 0; j < attList.length; j++) 
				{
					if (value[i].hasOwnProperty(attList[j])) 
					{
						tmp[attList[j]] = value[i][attList[j]];
					}else {
						tmp[attList[j]] = "";
					}
				}
				arr.push(tmp);
			}
			
			return arr;
		}
		
		public function getObjByKeyFromArr(arr:Array,keyName:String,keyValue:*):Object {
			for (var i:int = 0; i < arr.length; i++) 
			{
				if (arr[i][keyName] == keyValue) 
				{
					return arr[i];
				}
			}
			
			return null;
		}
		
		/**
		 * 输出成JSON文件
		 * @param	fileName
		 * @param	attList
		 * @param	value
		 * @return
		 */
		public function saveDataToJson(fileName:String,attList:Array,value:Array):void {
			
			var arr:Array = turnCustomDataToObj(value, attList);
			
			var loader:JsonFiler = new JsonFiler();
			loader.saveFile(fileName, arr);
		}
		
		/**
		 * 保存数据到CSV文件
		 * @param	fileName
		 * @param	attList
		 * @param	value
		 */
		public function saveDataToCsv(fileName:String,attList:Array,value:Array,path:String):void {
			
			var arr:Array = turnCustomDataToObj(value, attList);
			
			var loader:CsvLoader = new CsvLoader("saveDataCsv");
			loader.saveFile(path+fileName, arr,attList);
			
		}
		
		
		/**
		 * 删除指定表内指定key的所有数据,再插入新的一组新数据
		 * @param	type
		 * @param	keyName
		 * @param	key
		 * @param	value
		 */
		public function switchDataListByKey(type:String, keyName:String, key:*, value:Array,idKey:String=""):Array {
			
			var arr:Array = PublicDomain.getInstance().getVar(type) as Array;
			
			
			//按最大ID数重设所有数据的ID
			if (idKey) 
			{
				var idLimit:uint=1;
				if (arr.length>0) 
				{
					arr.sortOn(idKey, Array.NUMERIC | Array.DESCENDING);
					idLimit = uint(arr[0][idKey])+1;
				}
				for (var j:int = 0; j < value.length; j++) 
				{
					if (value[j][idKey]==0) {
						value[j][idKey] = idLimit;
						idLimit++;
					}
				}
			}
			
			for (var i:int = 0; i < arr.length; i++) 
			{
				if (arr[i][keyName]==key) 
				{
					arr.splice(i, 1);
					i--;
				}
			}
			
			arr = arr.concat(value);
			return arr;
		}
		
		/**
		 * 替换指定类型表内的指定KEY的数据
		 * @param	type
		 * @param	keyName
		 * @param	key
		 * @param	value
		 */
		public function setDataByKey(type:String, keyName:String, key:*, value:*):Array {
			
			var arr:Array = PublicDomain.getInstance().getVar(type) as Array;
			for (var i:int = 0; i < arr.length; i++) 
			{
				if (arr[i][keyName]==key) 
				{
					arr[i] = value;
					return arr;
				}
			}
			
			arr.push(value);
			return arr;
		}
		
		public function getClassFrom(name:String,keyName:String,key:*):*{
			var arr:Array = EditDataManager.getInstance().getVar(name) as Array;
			
			for (var i:int = 0; i < arr.length; i++) 
			{
				if (arr[i][keyName]==key) 
				{
					return arr[i];
				}
			}
			
			return null;
		}
		
		public function getFloors(sceneId:uint):Array {
			var floorList:Array = EditDataManager.getInstance().getVar("floorList");
			
			for (var i:int = 0; i < floorList.length; i++) 
			{
				if (uint(floorList[i].sceneId)==sceneId) 
				{
					return parserFloors(decodeJson(floorList[i].data),"floorList");
				}
			}
			
			return null;
		}
		
		public function getFloors2(sceneId:uint):Array {
			var floorList:Array = EditDataManager.getInstance().getVar("floorList2");
			
			for (var i:int = 0; i < floorList.length; i++) 
			{
				if (uint(floorList[i].sceneId)==sceneId) 
				{
					return parserFloors(decodeJson(floorList[i].data),"floorList2");
				}
			}
			
			return null;
		}
		
		private function parserFloors(value:Array, name:String):Array {
			var arr:Array;
			var tmparr:Array;
			var i:int;
			var j:int;
			var k:int;
			
			
			var tmpobj:Array;
			tmpobj = new Array();
			
			tmparr = value;
			for ( j= 0; j < tmparr.length; j++) 
			{
				if (!tmpobj[j]) 
				{
					tmpobj[j] = new Array();
				}
				for (k = 0; k < tmparr[j].length; k++) 
				{
					if (tmparr[j][k]) 
					{
						tmpobj[j][k] = new EditFloorVo().setClass(getFloorClass(tmparr[j][k]));
						(tmpobj[j][k] as EditFloorVo).x = j;
						(tmpobj[j][k] as EditFloorVo).z = k;
					}else {
						tmpobj[j][k] = new EditFloorVo();
						(tmpobj[j][k] as EditFloorVo).x = j;
						(tmpobj[j][k] as EditFloorVo).z = k;
					}
					if (name=="floorList") 
					{
						tmpobj[j][k].sortPriority = 0;
					}else {
						tmpobj[j][k].sortPriority = 1;
					}
				}
			}
		
			return tmpobj;
		}
		
		public function setVar(name:String, value:*):void {
			var arr:Array;
			var tmparr:Array;
			var i:int;
			var j:int;
			var k:int;
			switch (name) 
			{
				
				case "avatarClass":
					arr = new Array();
					tmparr = value as Array;
					for (i = 0; i < tmparr.length; i++) 
					{
						arr.push(new EditAvatarClassVo().setData(tmparr[i]));
					}
					value = arr;
				break;
				
				case "mineClass":
					arr = new Array();
					tmparr = value as Array;
					for (i = 0; i < tmparr.length; i++) 
					{
						arr.push(new EditMineClassVo().setData(tmparr[i]));
					}
					value = arr;
				break;
				
				case "mineList":
					arr = new Array();
					tmparr = value as Array;
					for (i = 0; i < tmparr.length; i++) 
					{
						arr.push(new EditMineVo().setValue(tmparr[i]));
					}
					value = arr;
				break;
				
				case "monsterClass":
					arr = new Array();
					tmparr = value as Array;
					for (i = 0; i < tmparr.length; i++) 
					{
						arr.push(new EditMonsterClassVo().setData(tmparr[i]));
					}
					value = arr;
				break;
				
				case "monsterList":
					arr = new Array();
					tmparr = value as Array;
					for (i = 0; i < tmparr.length; i++) 
					{
						arr.push(new EditMonsterVo().setValue(tmparr[i]));
					}
					value = arr;
				break;
				
				case "portalClass":
					arr = new Array();
					tmparr = value as Array;
					for (i = 0; i < tmparr.length; i++) 
					{
						arr.push(new EditPortalClassVo().setData(tmparr[i]));
					}
					value = arr;
				break;
				
				case "portalList":
					arr = new Array();
					tmparr = value as Array;
					for (i = 0; i < tmparr.length; i++) 
					{
						arr.push(new EditPortalVo().setValue(tmparr[i]));
					}
					value = arr;
				break;
				
				case "decorClass":
					arr = new Array();
					tmparr = value as Array;
					for (i = 0; i < tmparr.length; i++) 
					{
						arr.push(new EditDecorClassVo().setData(tmparr[i]));
					}
					value = arr;
				break;
				
				case "decorList":
					arr = new Array();
					tmparr = value as Array;
					for (i = 0; i < tmparr.length; i++) 
					{
						arr.push(new EditDecorVo().setValue(tmparr[i]));
					}
					value = arr;
				break;
				
				case "floorClass":
					arr = new Array();
					tmparr = value as Array;
					for (i = 0; i < tmparr.length; i++) 
					{
						arr.push(new EditFloorVo().setData(tmparr[i]));
					}
					value = arr;
				break;
				
				case "floorList":
				case "floorList2":
					
					
				break;
			}
			PublicDomain.getInstance().setVar(name, value);
		}
		
		/**
		 * 把地板数据里的floorList转化成data(DID数组)
		 * @param	obj
		 * @return
		 */
		public function initFloorDatasToObj(obj:Object):Object {
			var arr:Array = new Array();
			
			var tmparr:Array = obj.floorList as Array;
			for (var k:int = 0; k < curMapClass.numCols; k++) 
			{
				arr.push(new Array() );
				for (var l:int = 0; l < curMapClass.numCols; l++) 
				{
					arr[k].push(0);
				}
			}
			for (var i:int = 0; i < tmparr.length; i++) 
			{
				arr[tmparr[i].x][tmparr[i].z] = tmparr[i].cid;
			}
			
			obj.data = encodeJson(arr);
			return obj;
		}
		
		//private function get
		
		public function getFloorClass(cid:uint):EditFloorVo 
		{
			var arr:Array = getVar("floorClass");
			for (var i:int = 0; i < arr.length; i++) 
			{
				if (arr[i].cid==cid) 
				{
					return arr[i];
				}
			}
			
			return null;
		}
		
		public function getVar(name:String):* {
			return PublicDomain.getInstance().getVar(name);
		}
		
		public static function getInstance():EditDataManager
		{
			if (instance == null)
			{
				instance = new EditDataManager( new Private() );
			}
			return instance;
		}
		
		public function getCustomListBySceneId(listType:String, sceneId:uint):Array 
		{
			var arr:Array = new Array();
			var souceArr:Array = PublicDomain.getInstance().getVar(listType);
			for (var i:int = 0; i < souceArr.length; i++) 
			{
				if (souceArr[i].sceneId==sceneId) 
				{
					arr.push(souceArr[i]);
				}
			}
			return arr;
		}
		
		
		private static var instance:EditDataManager;
		
	}
	
}
class Private {}