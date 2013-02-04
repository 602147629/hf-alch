package happymagic.task.commands 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.events.TaskEvent;
	import happymagic.manager.DataManager;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.task.TaskState;
	import happymagic.model.vo.task.TaskVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class ViewTaskCommand extends BaseDataCommand 
	{
		public var id:int;
		public function viewTask(id:int):void
		{
			this.id = id;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("viewTask"), { id:id } );
			loadData();
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			if (result.isSuccess)
			{
				var vo:TaskVo = DataManager.getInstance().taskData.getTask(id);
				if(vo) vo.state = TaskState.VIEWED;
			}
			commandComplete();
		}
		
	}

}