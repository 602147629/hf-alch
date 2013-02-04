package happymagic.task.commands 
{
	import flash.events.Event;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.events.TaskEvent;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.task.TaskVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class CompleteTaskCommand extends BaseDataCommand 
	{
		public var id:int;
		public function completeTask(id:int, isFinish:Boolean):CompleteTaskCommand
		{
			this.id = id;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("completeTask"), { id:id, isFinish:isFinish ? 1 : 0 } );
			loadData();
			return this;
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			if (result.isSuccess)
			{
				//var task:TaskVo = DataManager.getInstance().taskData.removeTask(id);
				//EventManager.dispatchEvent(new TaskEvent(TaskEvent.TASK_COMPLETE, false, false, task, id));
			}
			commandComplete();
		}
		
	}

}