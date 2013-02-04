package happyfish.storyEdit.model 
{
	import com.brokenfunction.json.decodeJson;
	import com.brokenfunction.json.encodeJson;
	import com.friendsofed.isometric.Point3D;
	import flash.geom.Point;
	import flash.system.System;
	import happyfish.file.json.JsonFiler;
	import happyfish.storyEdit.display.MainUiView;
	import happyfish.storyEdit.model.vo.EditStoryActorVo;
	import happymagic.manager.PublicDomain;
	
	/**
	 * ...
	 * @author slamjj
	 */
	public class EditDataManager 
	{
		public var showOutStrFunc:Function;
		
		public const CLASSDATA_PATH:String = "classdata/";
		public const DATA_PATH:String = "data/";
		public var editUi:MainUiView;
		
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
		
		public function getIndexByKeyFromArr(arr:Array,keyName:String,keyValue:*):int {
			for (var i:int = 0; i < arr.length; i++) 
			{
				if (arr[i][keyName] == keyValue) 
				{
					return i;
				}
			}
			
			return -1;
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
		 * 找到指定数据包内所有符合KEY的数据
		 * @param	name
		 * @param	key
		 * @param	keyValue
		 * @return
		 */
		public function getArrayFromVars(name:String,key:String,keyValue:*):Array {
			var arr:Array = PublicDomain.getInstance().getVar(name) as Array;
			
			var outarr:Array = new Array();
			for (var i:int = 0; i < arr.length; i++) 
			{
				if (arr[i][key]==keyValue) 
				{
					outarr.push(arr[i]);
				}
			}
			
			return outarr;
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
		
		public function setVar(name:String, value:*):void {
			var arr:Array;
			var tmparr:Array;
			
			switch (name) 
			{
				//case "actorList":
					//arr = new Array();
					//for (var i:int = 0; i < value.length; i++) 
					//{
						//arr.push(new EditStoryActorVo().setData(value[i]));
					//}
					//value = arr;
				//break;
				
			}
			
			PublicDomain.getInstance().setVar(name, value);
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
		
		public function showNode(p:Point3D):void 
		{
			
		}
		
		
		private static var instance:EditDataManager;
		
	}
	
}
class Private {}