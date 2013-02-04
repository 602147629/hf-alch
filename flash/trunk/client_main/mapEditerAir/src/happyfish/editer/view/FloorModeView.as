package happyfish.editer.view 
{
	import fl.controls.RadioButtonGroup;
	import fl.data.DataProvider;
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import happyfish.display.view.IconView;
	import happyfish.editer.model.EditDataManager;
	import happyfish.editer.model.vo.EditDecorClassVo;
	import happyfish.editer.model.vo.EditFloorVo;
	import happyfish.editer.scene.view.EditIsoItem;
	import happyfish.utils.display.FiltersDomain;
	/**
	 * ...
	 * @author 
	 */
	public class FloorModeView extends floorModeUi 
	{
		private var main:Main;
		private var data:Array;
		private var typeList:Array;
		private var floorMap:Object;
		private var curMapIndex:Object;
		private var floors:Array;
		private var curMap:Object;
		private var floorKeyList:Array;
		private var actionGroup:RadioButtonGroup;
		private var curSelectFloor:EditFloorVo;
		private var curType:String;
		private var curSelectKey:String;
		
		public function FloorModeView(main:Main) 
		{
			this.main = main;
			
			floors = new Array();
			
			addEventListener(MouseEvent.CLICK, clickFun);
			
			grid_1.addEventListener(MouseEvent.CLICK, gridClickFun);
			grid_2.addEventListener(MouseEvent.CLICK, gridClickFun);
			grid_3.addEventListener(MouseEvent.CLICK, gridClickFun);
			grid_4.addEventListener(MouseEvent.CLICK, gridClickFun);
			grid_A.addEventListener(MouseEvent.CLICK, gridClickFun);
			grid_B.addEventListener(MouseEvent.CLICK, gridClickFun);
			grid_C.addEventListener(MouseEvent.CLICK, gridClickFun);
			grid_D.addEventListener(MouseEvent.CLICK, gridClickFun);
			grid_E.addEventListener(MouseEvent.CLICK, gridClickFun);
			grid_F.addEventListener(MouseEvent.CLICK, gridClickFun);
			grid_G.addEventListener(MouseEvent.CLICK, gridClickFun);
			grid_H.addEventListener(MouseEvent.CLICK, gridClickFun);
			grid_class.addEventListener(MouseEvent.CLICK, gridClickFun);
			
			floorListCombox.addEventListener(Event.CHANGE, selectFloorType);
			
			floorKeyList = ["A", "1", "B", "E", "F", "4", "class", "2", "H", "G", "D", "3", "C"];
			
			actionGroup = radio_select.group;
		}
		
		private function gridClickFun(e:MouseEvent):void 
		{
			var key:String = e.target.name.split("_")[1];
			
			switch (actionGroup.selectedData) 
			{
				case "select":
					if (curSelectFloor) {
						getChildByName("floor_" + curSelectKey).filters = [];
					}
					curSelectFloor = curMap[key][curMapIndex[key]];
					curSelectKey = key;
					getChildByName("floor_" + key).filters = [FiltersDomain.yellowGlow];
					
					select();
				break;
				
				case "change":
					nextGridIndex(key);
				break;
			}
			
			
		}
		
		private function selectFloorType(e:Event=null):void 
		{
			//trace();
			selectType(floorListCombox.selectedItem.data);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case closeBtn:
					visible = false;
					parent.removeChild(this);
				break;
			}
		}
		
		private function select():void {
			if (!curSelectFloor) 
			{
				return;
			}
			var tmparr:Array = main.editer.classList.dataProvider.toArray();
			for (var i:int = 0; i < tmparr.length; i++) 
			{
				if (tmparr[i].value.cid==curSelectFloor.cid) 
				{
					main.editer.classList.selectedIndex = i;
					//main.editer.classList.selectedItem = tmparr[i];
					main.editer.setCurSelectClass();
				}
			}
		}
		
		public function setData():void {
			if (typeList) 
			{
				return;
			}
			data = EditDataManager.getInstance().getVar("floorClass");
			
			typeList = new Array();
			
			floorMap = new Object();
			var tmpname:String;
			for (var i:int = 0; i < data.length; i++) 
			{
				tmpname = data[i].className.split(".")[2];
				tmpname = tmpname.split("_")[0];
				if ( !floorMap[tmpname] ) 
				{
					floorMap[tmpname] = new Array();
					typeList.push(tmpname);
				}
				floorMap[tmpname].push(data[i]);
			}
			
			floorListCombox.dataProvider = new DataProvider(typeList);
			
			selectType(typeList[0]);
		}
		
		/**
		 * 指定位换下一张图片
		 */
		private function nextGridIndex(key:String):void {
			if (curMapIndex[key]==curMap[key].length-1) 
			{
				curMapIndex[key] = 0;
			}else {
				curMapIndex[key]++;
			}
			//selectType(floorListCombox.selectedItem.data);
			initFloorMap();
		}
		
		private function selectType(type:String):void {
			
			curType = type;
			
			var arr:Array = floorMap[type];
			
			curMap = new Object();
			curMap["class"] = new Array();
			curMap["1"] = new Array();
			curMap["2"] = new Array();
			curMap["3"] = new Array();
			curMap["4"] = new Array();
			curMap["A"] = new Array();
			curMap["B"] = new Array();
			curMap["C"] = new Array();
			curMap["D"] = new Array();
			curMap["E"] = new Array();
			curMap["F"] = new Array();
			curMap["G"] = new Array();
			curMap["H"] = new Array();
			
			curMapIndex = new Object();
			curMapIndex["class"] = 
			curMapIndex["1"] = 
			curMapIndex["2"] = 
			curMapIndex["3"] = 
			curMapIndex["4"] = 
			curMapIndex["A"] = 
			curMapIndex["B"] = 
			curMapIndex["C"] = 
			curMapIndex["D"] = 
			curMapIndex["E"] = 
			curMapIndex["F"] = 
			curMapIndex["G"] = 
			curMapIndex["H"] = 0;
			
			var tmpname:String;
			var tmpkey:String;
			for (var i:int = 0; i < arr.length; i++) 
			{
				tmpname = arr[i].className.split("_")[1];
				if (tmpname.indexOf("class")>-1) 
				{
					curMap["class"].push(arr[i]);
				}else {
					tmpkey = tmpname.charAt(0);
					curMap[tmpkey].push(arr[i]);
				}
			}
			
			initFloorMap();
		}
		
		private function initFloorMap():void 
		{
			for (var i:int = 0; i < floors.length; i++) 
			{
				if (floors[i].parent) floors[i].parent.removeChild(floors[i]);
			}
			
			floors = new Array();
			
			var tmpfloor:IconView;
			var tmpindex:int;
			var gridMc:MovieClip;
			var tmpclass:String;
			var name:String;
			for (var j:int = 0; j < floorKeyList.length; j++) 
			{
				name = floorKeyList[j];
				tmpindex = curMapIndex[name];
				tmpfloor = new IconView();
				tmpfloor.mouseChildren =
				tmpfloor.mouseEnabled = false;
				tmpclass = "";
				if (curMap[name].length>tmpindex) 
				{
					tmpclass = curMap[name][tmpindex].className;
				}
				tmpfloor.setData(tmpclass);
				tmpfloor.name = "floor_" + name;
				gridMc = this["grid_" + name];
				tmpfloor.x = gridMc.x;
				tmpfloor.y = gridMc.y;
				addChild(tmpfloor);
				floors.push(tmpfloor);
			}
		}
	}

}