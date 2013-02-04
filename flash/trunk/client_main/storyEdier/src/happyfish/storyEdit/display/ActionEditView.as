package happyfish.storyEdit.display 
{
	import fl.data.DataProvider;
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.view.IconView;
	import happyfish.manager.EventManager;
	import happyfish.storyEdit.events.StoryEditEvent;
	import happyfish.storyEdit.model.EditDataManager;
	import happyfish.storyEdit.model.vo.EditStoryActionVo;
	import happyfish.storyEdit.sceneAction.NodeGetterAction;
	import happyfish.utils.display.McShower;
	import happymagic.manager.DataManager;
	/**
	 * ...
	 * @author jj
	 */
	public class ActionEditView extends actionEditUi 
	{
		private var actorIcon:IconView;
		public var type:int;
		public var stepIndex:int;
		public var actorId:int;
		public var data:EditStoryActionVo;
		
		
		public static const TYPE_EDIT:int = 1;
		public static const TYPE_CREATE:int = 2;
		
		public function ActionEditView(type:int) 
		{
			this.type = type;
			
			
			actionTypeList.addEventListener(Event.CHANGE, actionTypeChange);
			
			addEventListener(MouseEvent.CLICK, clickFun);
			
			
		}
		
		public function setData(actorId:int,stepIndex:int,action:EditStoryActionVo=null):void 
		{
			this.stepIndex = stepIndex;
			this.actorId = actorId;
			
			if (action) 
			{
				data = action;
			}else {
				data = new EditStoryActionVo();
				data.index = stepIndex;
			}
			
			actionTypeList.selectedIndex = data.type;
			actionTypeChange();
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case saveBtn:
					outData();
					closeMe();
				break;
				
				
				case cancelBtn:
					closeMe();
				break;
				
			case delBtn:
				delAction();
				closeMe();
				break;
				
			case copyBtn:
				copyAction();
				closeMe();
				break;
			
			
			case pastetBtn:
				pasteAction();
				
				break;
			
			
			case actionEdit_1.getXYBtn:
				visible = false;
				
				new NodeGetterAction(DataManager.getInstance().worldState);
				EventManager.getInstance().addEventListener(StoryEditEvent.GET_NODE, getXYNode);
				break;
				
			case actionEdit_1.getFaceBtn:
				visible = false;
				
				new NodeGetterAction(DataManager.getInstance().worldState);
				EventManager.getInstance().addEventListener(StoryEditEvent.GET_NODE, getFaceNode);
				break;
			}
		}
		
		private function getFaceNode(e:StoryEditEvent):void 
		{
			e.target.removeEventListener(StoryEditEvent.GET_NODE, getFaceNode);
			visible = true;
			actionEdit_1.faceXInput.text = e.node.x.toString();
			actionEdit_1.faceYInput.text = e.node.z.toString();
		}
		
		private function getXYNode(e:StoryEditEvent):void 
		{
			e.target.removeEventListener(StoryEditEvent.GET_NODE, getXYNode);
			visible = true;
			actionEdit_1.xInput.text = e.node.x.toString();
			actionEdit_1.yInput.text = e.node.z.toString();
		}
		
		private function pasteAction():void 
		{
			data = EditDataManager.getInstance().getVar("copyAction");
			data = data.clone();
			data.npcId = actorId;
			data.index = stepIndex;
			
			setData(actorId, stepIndex, data);
		}
		
		private function copyAction():void 
		{
			EditDataManager.getInstance().setVar("copyAction", data.clone());
		}
		
		private function delAction():void 
		{
			data = null;
			dispatchEvent(new Event(Event.COMPLETE));
		}
		
		private function outData():void 
		{
			var curType:int = actionTypeList.selectedItem.data;
			var out:EditStoryActionVo = new EditStoryActionVo();
			out.type = actionTypeList.selectedIndex;
			out.index = data.index;
			switch (curType) 
			{
			case StoryActionType.MOVE:
				out.npcId = actorId;
				out.x = int(actionEdit_1.xInput.text);
				out.y = int(actionEdit_1.yInput.text);
				out.faceX = int(actionEdit_1.faceXInput.text);
				out.faceY = int(actionEdit_1.faceYInput.text);
				
				out.wait = int(actionEdit_1.waitCheckBox.selected);
				out.camera = int(actionEdit_1.cameraCheckBox.selected);
				out.immediately = int(actionEdit_1.immediatelyCheckBox.selected);
				break;
				
			case StoryActionType.CHAT:
				out.npcId = actorId;
				out.content = actionEdit_2.chatInput.text;
				out.chatTime = actionEdit_2.chatTimeSelecter.value;
				out.wait = int(actionEdit_2.waitCheckBox.selected);
				out.camera = int(actionEdit_2.cameraCheckBox.selected);
				break;
				
			case StoryActionType.SHOCK:
				out.shockScreenTime = actionEdit_3.shockTimeSelecter.value;
				out.wait = int(actionEdit_3.waitCheckBox.selected);
				break;
				
			case StoryActionType.ACTION:
				out.npcId = actorId;
				out.actionLabel = actionEdit_4.labelCombox.text;
				out.labelTimes = actionEdit_4.playTimesSelecter.value;
				out.toStop = int(actionEdit_4.toStopCheckBox.selected);
				
				out.wait = int(actionEdit_4.waitCheckBox.selected);
				out.camera = int(actionEdit_4.cameraCheckBox.selected);
				break;
				
			case StoryActionType.MOVIE:
				out.npcId = actorId;
				out.className = actionEdit_5.classNameInput.text;
				
				out.wait = int(actionEdit_5.waitCheckBox.selected);
				out.camera = int(actionEdit_5.cameraCheckBox.selected);
				break;
				
			case StoryActionType.HIDE:
				out.npcId = actorId;
				out.hide = 1;
				break;
			}
			data = out;
			dispatchEvent(new Event(Event.COMPLETE));
		}
		
		private function closeMe():void 
		{
			parent.removeChild(this);
		}
		
		private function actionTypeChange(e:Event=null):void 
		{
			var curType:int = actionTypeList.selectedItem.data;
			data.type = actionTypeList.selectedIndex;
			for (var i:int = 1; i <= actionTypeList.dataProvider.length; i++) 
			{
				if (curType == i) 
				{
					this["actionEdit_" + i.toString()].visible = true;
				}else {
					this["actionEdit_" + i.toString()].visible = false;
				}
			}
			if (actorId) 
			{
				var className:String =  EditDataManager.getInstance().getClassFrom("actorList", "id", actorId).className;
				actorIcon = new IconView(30, 30);
				actorIcon.addEventListener(Event.COMPLETE, actorIcon_complete);
				actorIcon.setData(className);
				actorIcon.x = 0;
				actorIcon.y = -120;
				addChild(actorIcon);
			}
			
				
			switch (curType) 
			{
			case StoryActionType.MOVE:
				
				actionEdit_1.xInput.text = data.x.toString();
				actionEdit_1.yInput.text = data.y.toString();
				actionEdit_1.faceXInput.text = data.faceX.toString();
				actionEdit_1.faceYInput.text = data.faceY.toString();
				
				actionEdit_1.waitCheckBox.selected = Boolean(data.wait);
				actionEdit_1.cameraCheckBox.selected = Boolean(data.camera);
				actionEdit_1.immediatelyCheckBox.selected = Boolean(data.immediately);
				
				break;
				
			case StoryActionType.CHAT:
				
				actionEdit_2.chatInput.text = data.content;
				actionEdit_2.chatTimeSelecter.value = data.chatTime;
				
				actionEdit_2.waitCheckBox.selected = Boolean(data.wait);
				actionEdit_2.cameraCheckBox.selected = Boolean(data.camera);
				break;
				
			case StoryActionType.SHOCK:
				actionEdit_3.shockTimeSelecter.value = data.shockScreenTime;
				actionEdit_3.waitCheckBox.selected = Boolean(data.wait);
				break;
				
			case StoryActionType.ACTION:
				
				actionEdit_4.labelCombox.text=data.actionLabel;
				actionEdit_4.playTimesSelecter.value = data.labelTimes;
				actionEdit_4.toStopCheckBox.selected = Boolean(data.toStop);
				
				actionEdit_4.waitCheckBox.selected = Boolean(data.wait);
				actionEdit_4.cameraCheckBox.selected = Boolean(data.camera);
				break;
				
			case StoryActionType.MOVIE:
				actionEdit_5.classNameInput.text = data.className;
				
				actionEdit_5.waitCheckBox.selected = Boolean(data.wait);
				actionEdit_5.cameraCheckBox.selected = Boolean(data.camera);
				break;
				
			}
			
			
		}
		
		private function actorIcon_complete(e:Event):void 
		{
			if (data.type==StoryActionType.ACTION-1) 
			{
				var str:String;
				var tmp:Array = actorIcon.icon.currentLabels;
				var obj:Object = new Object();
				for (var i:int = 0; i < tmp.length; i++) 
				{
					
					str = (tmp[i].name as String).split("_")[1];
					if (str == "left" || str == "right" || str == "up" || str == "down") 
					{
						str = (tmp[i].name as String).split("_")[0]
					}else {
						str = tmp[i].name;
					}
					obj[str] = str;
				}
				var labels:Array = new Array(); 
				for (var name:String in obj) 
				{
					labels.push( { label:name } );
				}
				
				actionEdit_4.labelCombox.dataProvider = new DataProvider(labels);
			}
			
			//actionEdit_4.labelCombox.text=data.actionLabel;
		}
		
		
		
	}

}