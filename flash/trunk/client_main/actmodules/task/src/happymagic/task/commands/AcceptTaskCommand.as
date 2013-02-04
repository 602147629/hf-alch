package happymagic.task.commands 
{
	import flash.events.Event;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.events.TaskEvent;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.task.TaskState;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class AcceptTaskCommand extends BaseDataCommand 
	{
		
		private var id:int;
		public function acceptTask(id:int):void
		{
			this.id = id;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("acceptTask"), { id:id } );
			loadData();
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			//if (result.isSuccess)
			//{
				//DataManager.getInstance().taskData.getTask(id).state = TaskState.NOT_VIEW;
				//EventManager.dispatchEvent(new TaskEvent(TaskEvent.TASK_ACCEPT, false, false, null, id));
			//}
			commandComplete();
		}
		
	}

}