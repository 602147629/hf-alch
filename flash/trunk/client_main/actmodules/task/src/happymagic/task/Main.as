package happymagic.task
{
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.TimerEvent;
	import flash.utils.getQualifiedClassName;
	import flash.utils.Timer;
	import happyfish.events.MainEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.manager.module.ModuleManager;
	import happyfish.manager.module.vo.ModuleVo;
	import happyfish.time.Time;
	import happymagic.events.MagicEvent;
	import happymagic.events.TaskEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.data.TaskData;
	import happymagic.model.vo.task.TaskState;
	import happymagic.model.vo.task.TaskVo;
	import happymagic.task.commands.GetDailyTasksCommand;
	import happymagic.task.view.AcceptTaskUISprite;
	import happymagic.task.view.CompleteTaskUISprite;
	import happymagic.task.view.ModuleName;
	import happymagic.task.view.TaskListUISprite;
	import happymagic.task.view.TaskMenuIcon;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class Main extends Sprite 
	{
		
		private var dailyTaskTimer:Timer;
		private var taskIcon:TaskMenuIcon;
		
		public function Main():void 
		{
			EventManager.addEventListener(MainEvent.MAIN_DATA_COMPELTE, init);
			EventManager.addEventListener(TaskEvent.CHANGE_TASKS_AUTO_EXECUTE, changeTaskAutoExecuteHandler);
		}
		
		private function init(e:Event):void 
		{
			EventManager.removeEventListener(MainEvent.MAIN_DATA_COMPELTE, init);
			
			EventManager.addEventListener(TaskEvent.TASK_COMPLETE, taskCompleteHandler);
			EventManager.addEventListener(TaskEvent.TASK_SHOW_ACCEPT_PANEL, taskShowAcceptPanel);
			
			var module:ModuleVo = new ModuleVo();
			module.name = ModuleName.TaskListUISprite;
			module.className = getQualifiedClassName(TaskListUISprite);
			ModuleManager.getInstance().addModule(module)
			/*var tmpDis:DisplayObject = DisplayManager.uiSprite.stage.addChildAt(ModuleManager.getInstance().addModule(module).view, 0);
			//tmpDis.parent.removeChild(tmpDis);
			setTimeout(function():void
			{
				tmpDis.parent.removeChild(tmpDis);
			}, 0);*/
			
			dailyTaskTimer = new Timer(60000);
			dailyTaskTimer.addEventListener(TimerEvent.TIMER, dailyTaskTimerHandler);
			dailyTaskTimer.start();
			
			
			taskIcon = new TaskMenuIcon();
			if (ModuleManager.getInstance().getModule("rightCenterMenu"))
			{
				showIcon(null);
			}else
			{
				EventManager.addEventListener(MagicEvent.RIGHT_CENTER_INIT, showIcon);
			}
		}
		
		private function showIcon(e:Event):void
		{
			EventManager.removeEventListener(MagicEvent.RIGHT_CENTER_INIT, showIcon);
			var rightCenterMenu:IModule = ModuleManager.getInstance().getModule("rightCenterMenu");
			rightCenterMenu["addMc"]("taskMenuIcon", taskIcon, 10000);
		}
		
		private function dailyTaskTimerHandler(e:TimerEvent):void 
		{
			var date:Date = Time.getCurDate();
			if (0 == date.hours && date.minutes < 2)
			{
				dailyTaskTimer.stop();
				new GetDailyTasksCommand().getDailyTasks();
			}
		}
		
		private function taskShowAcceptPanel(e:TaskEvent):void 
		{
			var task:TaskVo = DataManager.getInstance().taskData.getTask(e.id);
			if (!task || task.state != TaskState.NOT_ACCEPT) return;
			
			var uisprite:AcceptTaskUISprite = ModuleManager.getInstance().getModule(ModuleName.AcceptTaskUISprite) as AcceptTaskUISprite;
			if (!uisprite)
			{
				var module:ModuleVo = new ModuleVo();
				module.name = ModuleName.AcceptTaskUISprite;
				module.className = getQualifiedClassName(AcceptTaskUISprite);
				uisprite = ModuleManager.getInstance().addModule(module) as AcceptTaskUISprite;
			}
			ModuleManager.getInstance().showModule(ModuleName.AcceptTaskUISprite);
			DisplayManager.uiSprite.setBg(uisprite);
			uisprite.setData(task);
		}
		
		private function taskCompleteHandler(e:TaskEvent):void 
		{
			var task:TaskVo = e.data as TaskVo;
			if (!task) return;
			
			if (!task.awards || 0 == task.awards.length) return;
			
			var uisprite:CompleteTaskUISprite = ModuleManager.getInstance().getModule(ModuleName.CompleteTaskUISprite) as CompleteTaskUISprite;
			if (!uisprite)
			{
				var module:ModuleVo = new ModuleVo();
				module.name = ModuleName.CompleteTaskUISprite;
				module.className = getQualifiedClassName(CompleteTaskUISprite);
				uisprite = ModuleManager.getInstance().addModule(module) as CompleteTaskUISprite;
			}
			ModuleManager.getInstance().showModule(ModuleName.CompleteTaskUISprite);
			DisplayManager.uiSprite.setBg(uisprite);
			uisprite.setData(task);
		}
		
		private function changeTaskAutoExecuteHandler(e:TaskEvent):void 
		{
		
			var taskData:TaskData = DataManager.getInstance().taskData;
			list = e.data.adds;
			if (list)
			{
				for(i = list.length - 1; i >= 0; i--)
				{
					vo = new TaskVo();
					vo.setData(list[i]);
					taskData.addTask(vo);
					if (TaskState.NOT_VIEW == vo.state)
					{
						EventManager.dispatchEvent(new TaskEvent(TaskEvent.TASK_ACCEPT, false, false, null, vo.id));
					}
				}
			}
			list = e.data.changes;
			if (list)
			{
				for(i = list.length - 1; i >= 0; i--)
				{
					vo = new TaskVo();
					vo.setData(list[i]);
					var old:TaskVo = taskData.getTask(vo.id);
					var isAccept:Boolean = TaskState.NOT_ACCEPT == old.state && TaskState.NOT_VIEW == vo.state;
					taskData.updateTask(vo, isAccept);
					if (isAccept)
					{
						EventManager.dispatchEvent(new TaskEvent(TaskEvent.TASK_ACCEPT, false, false, null, vo.id));
					}
				}
			}
			var list:Array = e.data.completes;
			if (list)
			{
				for (var i:int = list.length - 1; i >= 0; i--)
				{
					var vo:TaskVo = taskData.removeTask(list[i]);
					if (!vo) continue;
					EventManager.dispatchEvent(new TaskEvent(TaskEvent.TASK_COMPLETE, false, false, vo, vo.id));
				}
			}
			
			taskData.sort();
			EventManager.dispatchEvent(new TaskEvent(TaskEvent.TASKS_STATE_CHANGE));
		}
	}
	
}