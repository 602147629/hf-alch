package  happyfish.editer.view
{
	import com.adobe.serialization.json.JSON;
	import com.brokenfunction.json.decodeJson;
	import fl.data.DataProvider;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.events.TextEvent;
	import flash.filesystem.File;
	import flash.filesystem.FileMode;
	import flash.filesystem.FileStream;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.system.System;
	import happyfish.display.view.IconView;
	import happyfish.editer.control.EditAttEditControl;
	import happyfish.editer.control.EditDecorControl;
	import happyfish.editer.control.EditerControl;
	import happyfish.editer.control.EditFloorControl;
	import happyfish.editer.control.EditGetSampleControl;
	import happyfish.editer.control.EditGridEditControl;
	import happyfish.editer.control.EditMapDragControl;
	import happyfish.editer.control.EditMineControl;
	import happyfish.editer.control.EditMirrorControl;
	import happyfish.editer.control.EditMonsterControl;
	import happyfish.editer.control.EditNpcControl;
	import happyfish.editer.control.EditPortalControl;
	import happyfish.editer.model.EditDataManager;
	import happyfish.editer.model.vo.MapClassVo;
	import happyfish.editer.scene.view.EditIsoItem;
	import happyfish.editer.view.FloorModeView;
	import happyfish.scene.astar.Node;
	import happyfish.scene.iso.IsoUtil;
	/**
	 * ...
	 * @author jj
	 */
	public class EditerView extends editerUi
	{
		private var main:Main;
		private var mapClass:Array;
		private var decorClass:Array;
		private var portalClass:Array;
		private var mineClass:Array;
		private var monsterClass:Array;
		public var data:Object;
		public var mapSize:Number;
		private var control:EditerControl;
		public var dataInfoView:ItemDataInfoView;
		private var classIconView:IconView;
		private var floorModeView:FloorModeView;
		private var curClassFilter:String;
		public function EditerView(_main:Main) 
		{
			main = _main;
			
			EditDataManager.getInstance().showOutStrFunc = addStr;
			
			addEventListener(MouseEvent.CLICK, clickFun, true);
			
			tileSizeInput.text = IsoUtil.TILE_SIZE.toString();
			tileLengthInput.text = "60";
			
			mapListCombox.labelField = "name";
			
			classList.labelField = "name";
			classList.addEventListener(Event.CHANGE, setCurSelectClass);
			
			hideGridCheck.addEventListener(MouseEvent.CLICK, hideGridCheckFun);
			
			//mapListCombox.addEventListener(Event.CHANGE, mapList_change);
			editTypeList.addEventListener(Event.CHANGE, initControl);
			
			classTypeList.addEventListener(Event.CHANGE, classTypeChange);
			
			dataInfoView = new ItemDataInfoView(dataInfo);
			dataInfoView.visible = false;
			
			floorModeView = new FloorModeView(main);
			floorModeView.visible = false;
			
			
			editTypeList.selectedIndex = 0;
			classTypeList.selectedIndex = 0;
			
			classFilterInput.addEventListener(Event.CHANGE, classFilterChange);
			
			classIconView = new IconView(50, 50, new Rectangle(159, 211, 50, 50));
			
		}
		
		private function classFilterChange(e:Event=null):void 
		{
			if (curClassFilter == classFilterInput.text) 
			{
				return;
			}
			curClassFilter = classFilterInput.text;
			initClassList();
		}
		
		/**
		 * 左侧类别更改时
		 * @param	e
		 */
		private function classTypeChange(e:Event):void 
		{
			initClassList();
			initControl();
		}
		
		public function showClassIcon(className:String):void {
			addChild(classIconView);
			classIconView.setData(className);
			classIconView.visible = true;
		}
		
		public function hideClassIcon():void {
			classIconView.visible = false;
		}
		
		public function initControl(e:Event=null):void 
		{
			
			if (control) 
			{
				control.clear();
				control = null;
			}
			switch (editTypeList.selectedItem.data) 
			{
				case "1":
					control = new EditMapDragControl(main, main.mapSprite);
				break;
				
				case "2":
					control = new EditGridEditControl(main, main.mapSprite);
				break;
				
				case "3":
					control = new EditGridEditControl(main, main.mapSprite);
				break;
				
				case "4":
				case "5":
				case "8":
					switch (classTypeList.selectedItem.data) 
					{
						case "floorClass":
							if (classTypeList.selectedItem.label=="地板2") 
							{
								control = new EditFloorControl(main, main.mapSprite,2);
								
							}else {
								control = new EditFloorControl(main, main.mapSprite);
							}
							setCurSelectClass();
						break;
						
						case "decorClass":
							control = new EditDecorControl(main, main.mapSprite);
							setCurSelectClass();
						break;
						
						case "portalClass":
							control = new EditPortalControl(main, main.mapSprite);
							setCurSelectClass();
						break;
						
						case "mineClass":
							control = new EditMineControl(main, main.mapSprite);
							setCurSelectClass();
						break;
						
						case "monsterClass":
							control = new EditMonsterControl(main, main.mapSprite);
							setCurSelectClass();
						break;
						
						case "npcClass":
							control = new EditNpcControl(main, main.mapSprite);
							setCurSelectClass();
						break;
					}
				break;
				
				case "6":
					control = new EditAttEditControl(main, main.mapSprite);
				break;
				
				case "7":
					control = new EditMirrorControl(main, main.mapSprite);
				break;
				
				
				
			}
			//control = new EditerControl(this,mapSprite);
		}
		
		public function setCurSelectClassByCid(cid:int):void {
			var tmparr:Array = classList.dataProvider.toArray();
			for (var i:int = 0; i < tmparr.length; i++) 
			{
				if (tmparr[i].value.cid==cid) 
				{
					classList.selectedIndex = i;
					setCurSelectClass();
					return;
				}
			}
		}
		
		/**
		 * 设置当前选中的地板\装饰\怪\矿\NPC的类数据
		 */
		public function setCurSelectClass(e:Event=null):void 
		{
			if (classList.dataProvider.length<=0) 
			{
				return;
			}
			if (classList.selectedItem) 
			{
				EditDataManager.getInstance().setVar("curSelectClass", classList.selectedItem.value);
				showClassIcon(classList.selectedItem.value.className);
			}else {
				EditDataManager.getInstance().setVar("curSelectClass", null);
				hideClassIcon();
			}
		}
		
		private function mapList_change(e:MouseEvent):void 
		{
			initClassList();
			initControl();
		}
		
		private function hideGridCheckFun(e:MouseEvent):void 
		{
			main.mapSprite.gridView.visible = !hideGridCheck.selected;
		}
		
		/**
		 * 设置数据
		 * @param	value	{mapClass}
		 */
		public function init():void {
			mapClass = EditDataManager.getInstance().getVar("mapClass");
			mapListCombox.dataProvider = new DataProvider(mapClass);
			
			decorClass=EditDataManager.getInstance().getVar("decorClass");
			portalClass=EditDataManager.getInstance().getVar("portalClass");
			mineClass=EditDataManager.getInstance().getVar("mineClass");
			monsterClass = EditDataManager.getInstance().getVar("monsterClass");
			
			initClassList();
			initControl();
		}
		
		/**
		 * 设置左侧的素材列表
		 */
		private function initClassList():void 
		{
			var arr:Array = EditDataManager.getInstance().getVar(classTypeList.selectedItem.data);
			
			//filter
			var tmpfilters:Array;
			var arr2:Array = arr;
			if (curClassFilter) 
			{
				if (curClassFilter.length>0) 
				{
					arr2 = new Array();
					tmpfilters = curClassFilter.split(" ");
					for (var j:int = 0; j < arr.length; j++) 
					{
						for (var k:int = 0; k < tmpfilters.length; k++) 
						{
							if (arr[j].name.indexOf(tmpfilters[k])>-1) 
							{
								arr2.push(arr[j]);
								break;
							}
						}
						
					}
				}
			}
			
			var tmparr:Array = new Array();
			for (var i:int = 0; i < arr2.length; i++) 
			{
				tmparr.push( { name:arr2[i].name, value:arr2[i] } );
			}
			
			
			classList.dataProvider = new DataProvider(tmparr);
			if (tmparr.length>0) 
			{
				if (tmparr[0].hasOwnProperty("name")) 
				{
					classList.dataProvider.sortOn("name");
				}
			}
			
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case mapBtn:
					loadMap();
				break;
				
				case outBtn:
					saveData();
					outBtn.enabled = false;
					stage.mouseChildren = false;
				break;
				
				case floorModeBtn:
					floorModeView.visible = true;
					//var p:Point = new Point(stage.stageWidth / 2, stage.stageHeight / 2);
					//p = globalToLocal(p);
					//floorModeView.x = p.x;
					//floorModeView.y = p.y;
					
					floorModeView.x = classList.x;
					floorModeView.y = classList.y;
					
					floorModeView.setData();
					addChild(floorModeView);
				break;
				
				case getSampleBtn:
					if (control) 
					{
						control.clear();
						control = null;
					}
					control = new EditGetSampleControl(main, main.mapSprite, classTypeList.selectedItem.label);
				break;
				
			}
		}
		
		private function saveData():void
		{
			var obj:Object = main.mapSprite.outData();
			
			EditDataManager.getInstance().saveSceneData(obj.mapClass,obj.floor,obj.floor2,obj.decors,obj.portals,obj.mines,obj.monsters,obj.npcs);
			
		}
		
		private function saveJsonData():void {
			//var obj:Object = main.mapSprite.outData();
			//EditDataManager.getInstance().saveSceneToData(obj.mapClass,obj.floor,obj.decors,obj.portals,obj.mines,obj.monsters);
		}
		
		/*private function saveSceneClass():void {
			var dm:EditDataManager = EditDataManager.getInstance();
			
			var obj:Object = new Object();
			
			var mapclass:MapClassVo = main.mapSprite.data;
			var mapobj:Object = main.mapSprite.outData();
			
			var tmpsceneclass:Object=new Object():
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
			//地板
			tmpsceneclass.floorList = decodeJson(dm.initFloorDatasToObj(mapobj.floor).data);
			tmpsceneclass.floorList2 = decodeJson(dm.initFloorDatasToObj(mapobj.floor2).data);
			//门
			var tmpportals:Array = new Array();
			
			//矿
			
			//怪
			
			
			obj.sceneClass = tmpsceneclass;
		}*/
		
		private function loadMap():void
		{
			main.mapSprite.clear();
			main.mapSprite.setMapClass(mapListCombox.selectedItem);
		}
		
		public function addStr(str:String,type:uint=1):void
		{
			if (type==1) 
			{
				txt.appendText(str + "\n");
				txt.scrollV = txt.maxScrollH;
			}else {
				outTxt.appendText(str + "\n");
				outTxt.scrollV = outTxt.maxScrollH;
			}
			
		}
		
		public function setStr(str:String):void
		{
			txt.text=str;
		}
		
		/**
		 * 显示指定物件的属性面板
		 * @param	item
		 */
		public function showDataInfo(item:EditIsoItem):void 
		{
			dataInfoView.visible = true;
			dataInfoView.setData(item, ["targetSceneId", "cid", "id", "fiddleRangeX", "fiddleRangeZ",
				"faceX","faceZ","detail","goHome","targetSceneName","fightBg"]);
		}
		
	}

}