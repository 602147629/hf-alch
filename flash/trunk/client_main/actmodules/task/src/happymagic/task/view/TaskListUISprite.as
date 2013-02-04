package happymagic.task.view 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.ui.accordion.Accordion;
	import happyfish.display.ui.accordion.AccordionChild;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.view.UISprite;
	import happyfish.guide.tools.GuideListUtil;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.display.view.ui.DefaultAwardItemRender;
	import happymagic.events.TaskEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.task.TaskState;
	import happymagic.model.vo.task.TaskType;
	import happymagic.model.vo.task.TaskVo;
	import happymagic.task.commands.CompleteTaskCommand;
	import happymagic.task.commands.ViewTaskCommand;
	import happymagic.task.events.TaskListEvent;
	import happymagic.task.view.render.AwardItem;
	import happymagic.task.view.render.ConditionItem;
	import happymagic.task.view.render.ConditionRender;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class TaskListUISprite extends UISprite 
	{
		private var taskVo:TaskVo;
		
		private var iview:TaskListUI;
		
		private var accordion:Accordion;
		
		private var conditionListView:DefaultListView;
		private var awardListView:DefaultListView;
		
		private var taskList:Array = [];
		
		private var eventQueue:Array = [];
		
		
		public function TaskListUISprite() 
		{
			super();
			iview = new TaskListUI();
			_view = iview;
			
			TextFieldUtil.autoSetTxtDefaultFormat(iview);
			
			eventQueue[0] = new TaskEvent(TaskEvent.TASKS_STATE_CHANGE);
			
			accordion = new Accordion(iview);
			accordion.init(203, 360, -300, -168, 200, 40);
			accordion.setChildrenItem(TaskListAccordionChild, TaskListAccordionChildUI);
			view.addEventListener(TaskListEvent.SHOW_TASK_ITEM, showTaskItemHandler);
			iview.addEventListener(MouseEvent.CLICK, clickHandler);
			
			conditionListView = new DefaultListView(iview, iview, 3);
			conditionListView.init(327, 123, 300, 35, -30, -89);
			conditionListView.setGridItem(ConditionItem, ConditionRender);
			
			awardListView = new DefaultListView(iview, iview, 4);
			awardListView.init(327, 80, 60, 80, -30, 120);
			awardListView.setGridItem(AwardItem, DefaultAwardItemRender);
			
			//EventManager.addEventListener(TaskEvent.TASK_COMPLETE, taskEventHandler);
			EventManager.addEventListener(TaskEvent.TASKS_UPDATE_FOR_DAILY, taskEventHandler);
			EventManager.addEventListener(TaskEvent.TASKS_STATE_CHANGE, taskEventHandler);
			//EventManager.addEventListener(TaskEvent.TASK_ACCEPT, taskEventHandler);
		}
		
		private function taskEventHandler(e:TaskEvent):void 
		{
			if (!iview.stage)
			{
				eventQueue.push(e);
				return;
			}
			switch(e.type)
			{
				//case TaskEvent.TASK_COMPLETE :
					//removeTask(e.id);
					//break;
					
				case TaskEvent.TASKS_UPDATE_FOR_DAILY :
					taskList[TaskType.DAILY - 1].list = DataManager.getInstance().taskData.getTasks(TaskType.DAILY).concat();
					repaintAccordionChild(TaskType.DAILY - 1);
					if (!taskVo) showFirstTask();
					break;
					
				case TaskEvent.TASKS_STATE_CHANGE :
					eventQueue.length = 0;
					reset();
					break;
			}
		}
		
		public function show():void
		{
			if (0 == eventQueue.length) return;
			
			while(eventQueue.length > 0)
			{
				taskEventHandler(eventQueue.shift());
			}
		}
		
		public function removeTask(id:int):void 
		{
			var type:int = TaskType.getType(id);
			var list:Array = taskList[type - 1].list;
			for (var i:int = list.length - 1; i >= 0; i--)
			{
				if (list[i].id == id)
				{
					list.splice(i, 1);
					break;
				}
			}
			repaintAccordionChild(type-1);
		}
		
		public function addTask(id:int):void 
		{
			var type:int = TaskType.getType(id);
			var list:Array = taskList[type - 1].list;
			for (var i:int = list.length - 1; i >= 0; i--)
			{
				if (list[i].id == id)
				{
					list.splice(i, 1);
					break;
				}
			}
			repaintAccordionChild(type-1);
		}
		
		private function repaintAccordionChild(idx:int):void 
		{
			taskList[idx].title = getTaskTypeTitle(idx + 1, taskList[idx].list.length);
			var child:AccordionChild = accordion.getChildByIndex(idx);
			var oldSelectedIdx:int = child.getListView().selectedIndex;
			child.setData(taskList[idx]);
			if (idx != accordion.selectedIndex) return;
			
			if (0 == taskList[idx].list.length)
			{
				showFirstTask();
			}else
			{
				if (oldSelectedIdx >= taskList[idx].list.length) oldSelectedIdx = 0;
				child.getListView().selectedIndex = oldSelectedIdx;
				showTask(child.getListView().selectedValue as TaskVo);
			}
		}
		
		private function getTaskTypeTitle(type:int, len:int):String 
		{
			return LocaleWords.getInstance().getWord("taskTypeTitle(len)" + type, len);
		}
		
		override public function closeMe(del:Boolean = false):void 
		{
			DisplayManager.sceneSprite.visible = true;
			super.closeMe(del);
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			switch(e.target)
			{
				case iview.closeBtn :
					closeMe();
					break;
					
				case iview.immediateBtn :
					iview.immediateBtn.mouseEnabled = false;
					new CompleteTaskCommand().completeTask(taskVo.id, taskVo.isFinish()).addEventListener(Event.COMPLETE, enabledImmediateBtn);
					break;
					
				case iview.guideBtn :
					GuideListUtil.startGuide(taskVo.id + "");
					break;
			}
			
		}
		
		private function enabledImmediateBtn(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, enabledImmediateBtn);
			iview.immediateBtn.mouseEnabled = true;
		}
		
		public function reset():void
		{
			var getTasks:Function = DataManager.getInstance().taskData.getTasks;
			taskList = [];
			
			for (var i:int = 0; i < 3; i++)
			{
				taskList[i] = { list:getTasks(i + 1).concat() };
				taskList[i].title = getTaskTypeTitle(i + 1, taskList[i].list.length);
			}
			
			accordion.setData(taskList);
			showFirstTask();
		}
		
		private function showFirstTask():void 
		{
			if (taskList[accordion.selectedIndex].list.length > 0)
			{
				accordion.selection.getListView().selectedIndex = 0;
				showTask(accordion.selection.getListView().selectedValue as TaskVo);
				return;
			}
			
			for (var i:int = 0; i < 3; i++)
			{
				if (0 == taskList[i].list.length) continue;
				accordion.selectedIndex = i;
				accordion.selection.getListView().selectedIndex = 0;
				showTask(accordion.selection.getListView().selectedValue as TaskVo);
				break;
			}
			if (3 == i) showTask(null);
		}
		
		private function showTaskItemHandler(e:TaskListEvent):void 
		{
			showTask(e.task);
		}
		
		private function showTask(taskVo:TaskVo):void
		{
			this.taskVo = taskVo;
			if (!taskVo)
			{
				iview.immediateBtn.visible = false;
				iview.immediateNumTxt.visible = false;
				iview.guideBtn.visible = false;
				iview.nameTxt.text = "";
				iview.contentTxt.text = "";
				awardListView.setData([]);
				conditionListView.setData([]);
				return;
			}
			
			conditionListView.setData(taskVo.conditions || []);
			awardListView.setData(taskVo.awards || []);
			
			iview.nameTxt.text = taskVo.name;
			iview.contentTxt.text = taskVo.content;
			
			var isFinish:Boolean = taskVo.isFinish();
			iview.guideBtn.visible = !isFinish && GuideListUtil.hasGuideById(taskVo.id + "");
			if (taskVo.nowFinishPrice > 0 && !isFinish)
			{
				iview.immediateBtn.visible = true;
				iview.immediateNumTxt.visible = true;
				iview.immediateNumTxt.text = taskVo.nowFinishPrice + "";
			}else
			{
				iview.immediateBtn.visible = false;
				iview.immediateNumTxt.visible = false;
			}
			
			if (TaskState.NOT_VIEW == taskVo.state)
			{
				taskVo.state = TaskState.VIEWED;
				new ViewTaskCommand().viewTask(taskVo.id);
			}
		}
		
	}

}