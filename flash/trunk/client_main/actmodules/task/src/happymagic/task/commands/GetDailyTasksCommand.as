package happymagic.task.commands 
{
	import flash.events.Event;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.events.TaskEvent;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.task.TaskState;
	import happymagic.model.vo.task.TaskType;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class GetDailyTasksCommand extends BaseDataCommand 
	{
		
		public function getDailyTasks():void
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("getDailyTasks"));
			loadData();
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			if (result.isSuccess && objdata.tasks)
			{
				
				DataManager.getInstance().taskData.setDailyTasks(objdata.tasks);
				EventManager.dispatchEvent(new TaskEvent(TaskEvent.TASKS_UPDATE_FOR_DAILY));
			}
			commandComplete();
		}
		
	}

}