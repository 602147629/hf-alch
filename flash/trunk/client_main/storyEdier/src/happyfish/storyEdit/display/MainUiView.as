package happyfish.storyEdit.display 
{
	import com.brokenfunction.json.decodeJson;
	import fl.controls.dataGridClasses.DataGridColumn;
	import fl.controls.ScrollPolicy;
	import fl.data.DataProvider;
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.events.MouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.storyEdit.control.StoryActionGridControl;
	import happyfish.storyEdit.events.StoryEditEvent;
	import happyfish.storyEdit.model.EditDataManager;
	import happyfish.storyEdit.model.vo.EditStoryActionVo;
	import happyfish.storyEdit.model.vo.EditStoryActorVo;
	import happyfish.storyEdit.model.vo.EditStoryGridVo;
	import happyfish.storyEdit.model.vo.EditStoryVo;
	import happymagic.display.control.StoryPlayCommand;
	import happymagic.model.command.MoveSceneCommand;
	/**
	 * ...
	 * @author jj
	 */
	public class MainUiView extends mainUi 
	{
		private var curStoryVo:EditStoryVo;
		private var dm:EditDataManager;
		private var actorColum:DataGridColumn;
		
		private var gridControl:StoryActionGridControl;
		private var grid:StoryActionGrid;
		
		public function MainUiView() 
		{
			dm = EditDataManager.getInstance();
			
			dm.editUi = this;
			
			dm.showOutStrFunc = traceStr;
			
			addEventListener(MouseEvent.CLICK, clickFun);
			
			storyClassList.labelField = "name";
			
			saveStoryBtn.visible = 
			actorListUi.visible = false;
			
			EventManager.getInstance().addEventListener(StoryEditEvent.REQUEST_ADD_ACTION, requestAddAction);
			EventManager.getInstance().addEventListener(StoryEditEvent.REQUEST_EDIT_ACTION, requestAddAction);
			EventManager.getInstance().addEventListener(StoryEditEvent.SHOW_ACTOR_EDIT, showActorEdit);
		}
		
		public function traceStr(str:String,clearFlag:Boolean=false):void 
		{
			if (clearFlag) 
			{
				mainTxt.text = str;
			}else {
				mainTxt.appendText(str + "\n");
			}
			
		}
		
		private function showActorEdit(e:StoryEditEvent):void 
		{
			var tmp:ActorEditView = new ActorEditView(e.actorId);
			tmp.addEventListener(Event.COMPLETE, removeActor);
			addChild(tmp);
			tmp.x = e.point.x;
			tmp.y = e.point.y;
		}
		
		private function removeActor(e:Event):void 
		{
			var tmp:ActorEditView = e.target as ActorEditView;
			e.target.removeEventListener(Event.COMPLETE, removeActor);
			
			if (tmp.delFlag) 
			{
				grid.removeActor(tmp.actorId);
			}
		}
		
		private function requestAddAction(e:StoryEditEvent):void 
		{
			
			var type:int;
			if (e.type == StoryEditEvent.REQUEST_ADD_ACTION) 
			{
				type = ActionEditView.TYPE_CREATE;
			}else if (e.type == StoryEditEvent.REQUEST_EDIT_ACTION) 
			{
				type = ActionEditView.TYPE_EDIT;
				
			}
			var tmp:ActionEditView = new ActionEditView(type);
			tmp.addEventListener(Event.COMPLETE, requestAddAction_complete);
			tmp.setData(e.actorId, e.actionStep,e.action);
			tmp.x = stage.stageWidth / 2;
			tmp.y = stage.stageHeight / 2;
			addChild(tmp);
		}
		
		private function requestAddAction_complete(e:Event):void 
		{
			var editer:ActionEditView = e.target as ActionEditView;
			grid.saveAction(editer.actorId,editer.stepIndex,editer.data);
		}
		
		public function init():void 
		{
			var storys:Array = dm.getVar("storyList");
			storyClassList.dataProvider = new DataProvider(storys);
			
			storyClassList.selectedIndex = 0;
			
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
			case loadStoryBtn:
					loadStory();
				break;
				
			case addActorBtn:
					actorListUi.visible = true;
					actorListUi.selectedIndex = -1;
				break;
				
			case saveStoryBtn:
				saveStory();
				break;
				
			case createStoryBtn:
				createStory();
				break;
			}
		}
		
		private function createStory():void 
		{
			var tmp:EditStoryVo = new EditStoryVo();
			tmp.id = getStoryMixId();
			
			var editer:StoryEditView = new StoryEditView();
			editer.x = stage.stageWidth / 2;
			editer.y = stage.stageHeight / 2;
			editer.setData(tmp);
			editer.addEventListener(Event.COMPLETE, createStory_complete);
			addChild(editer);
		}
		
		private function createStory_complete(e:Event):void 
		{
			var editer:StoryEditView = e.target as StoryEditView;
			e.target.removeEventListener(Event.COMPLETE, createStory_complete);
			
			if (editer.saved) 
			{
				var arr:Array = dm.getVar("storyList");
				for (var i:int = 0; i < arr.length; i++) 
				{
					if (arr[i].id==editer.story.id) 
					{
						arr[i] = editer.story;
						return;
					}
				}
				
				arr.push(editer.story);
				init();
				saveStoryCsv();
			}
		}
		
		private function saveStoryCsv():void {
			var arr:Array = dm.getVar("storyList");
			
			dm.saveDataToCsv("storyList.csv",
			["name", "id", "sceneId", "endAt", "coin", "gem", "items", "actionIds", "npcIds", "taskId",
				"nextStoryId","unlockSceneId"],
			arr, dm.DATA_PATH);
		}
		
		private function getStoryMixId():int {
			var arr:Array = dm.getVar("storyList");
			if (arr.length==0) 
			{
				return 1;
			}
			arr.sortOn("id", Array.NUMERIC | Array.DESCENDING);
			var mix:int = int(arr[0].id)+1;
			return mix;
		}
		
		private function saveStory():void 
		{
			grid.saveOutData();
			
			var storyObj:Object = grid.curStoryVo.outObj();
			
			var arr:Array = dm.getVar("storyList");
			var getted:Boolean = false;
			for (var i:int = 0; i < arr.length; i++) 
			{
				if (arr[i].id==curStoryVo.id) 
				{
					getted = true;
					arr[i] = storyObj;
					break;
				}
			}
			if (!getted) 
			{
				arr.push(storyObj);
			}
			
			dm.saveDataToCsv("storyList.csv",
			["name", "id", "sceneId", "endAt", "coin", "gem", "items", "actionIds", "npcIds", "taskId",
				"nextStoryId","unlockSceneId"],
			arr, dm.DATA_PATH);
			
			
			arr = grid.curStoryVo.actions;
			var tmpid:int = 1;
			for (var j:int = 0; j < arr.length; j++) 
			{
				arr[j].id = tmpid;
				tmpid++;
			}
			dm.saveDataToCsv("action_" + grid.curStoryVo.id.toString() + ".csv",
			["id", "npcId", "x", "y", "faceX", "faceY", "content", "chatTime", "camera", "wait",
				"immediately", "hide", "className", "shockScreenTime", "actionLabel", "labelTimes",
				"toStop","index","type","index"],
			arr, dm.DATA_PATH);
			
		}
		
		private function loadStory():void 
		{
			curStoryVo = new EditStoryVo();
			curStoryVo.setData(storyClassList.selectedItem);
			
			var arr:Array;
			
			arr = curStoryVo.npcIds.length > 0 ? curStoryVo.npcIds.split(",") : [];
			curStoryVo.actorList = new Array();
			for (var i:int = 0; i < arr.length; i++) 
			{
				curStoryVo.actorList.push(dm.getClassFrom("actorList", "id", arr[i]));
			}
			
			
			var loader:CsvLoader = new CsvLoader("actionLoader");
			loader.addEventListener(Event.COMPLETE, loadStory_complete);
			loader.addEventListener(IOErrorEvent.IO_ERROR, loadStoryIoError);
			loader.loadURL("data/"+"action_"+curStoryVo.id.toString()+".csv"+"?=" + new Date().getTime());
		}
		
		private function loadStoryIoError(e:IOErrorEvent):void 
		{
			var loader:CsvLoader = e.target as CsvLoader;
			loader.removeEventListener(Event.COMPLETE, loadStory_complete);
			loader.removeEventListener(IOErrorEvent.IO_ERROR, loadStoryIoError);
			
			var actions:Array = new Array();
			curStoryVo.actions = actions;
			EditDataManager.getInstance().setVar("actions", actions);
			
			goEditState();
		}
		
		private function loadStory_complete(e:Event):void 
		{
			var loader:CsvLoader = e.target as CsvLoader;
			loader.removeEventListener(Event.COMPLETE, loadStory_complete);
			loader.removeEventListener(IOErrorEvent.IO_ERROR, loadStoryIoError);
			
			var actions:Array=new Array();
			var tmparr:Array = loader.getRecords();
			for (var i:int = 0; i < tmparr.length; i++) 
			{
				actions.push(new EditStoryActionVo().setData(tmparr[i]));
			}
			
			curStoryVo.actions = actions;
			EditDataManager.getInstance().setVar("actions", actions);
			
			goEditState();
		}
		
		private function goEditState():void 
		{
			
			storyClassList.visible=
			createStoryBtn.visible = 
			loadStoryBtn.visible = false;
			
			saveStoryBtn.visible = true;
			
			actorListUi.visible = false;
			actorListUi.labelField = "name";
			actorListUi.dataProvider = new DataProvider(dm.getVar("actorList") as Array);
			actorListUi.addEventListener(Event.CHANGE, addActorSelected);
			
			//gridControl = new StoryActionGridControl(storyActionGrid,curStoryVo);
			//gridControl.initGrid();
			
			grid = new StoryActionGrid();
			addChild(grid);
			
			grid.setData(curStoryVo);
			
			new MoveSceneCommand().moveScene(curStoryVo.sceneId);
			
		}
		
		private function addActorSelected(e:Event):void 
		{
			grid.addActor(new EditStoryActorVo().setData(actorListUi.selectedItem) as EditStoryActorVo);
			actorListUi.visible = false;
		}
		
	}

}