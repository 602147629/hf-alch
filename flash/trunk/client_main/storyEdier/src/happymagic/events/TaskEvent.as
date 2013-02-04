package happymagic.events 
{
	import flash.events.Event;
	
	/**
	 * ...
	 * @author jj
	 */
	public class TaskEvent extends Event 
	{
		
		// 自动处理的数据
		public static const CHANGE_TASKS_AUTO_EXECUTE:String = "changeTasksAutoExecute";
		
		public static const TASK_SHOW_ACCEPT_PANEL:String = "taskShowAcceptPanel";
		
		public static const TASK_ACCEPT:String = "taskAccept";
		public static const TASK_COMPLETE:String = "taskComplete";
		public static const TASK_CAN_COMPLETE:String = "taskCanComplete";
		
		public static const TASKS_UPDATE_FOR_DAILY:String = "taskUpdateForDaily";
		
		
		public static const TASKS_STATE_CHANGE:String = "taskStateChange";
		
		public var data:Object;
		public var id:int;
		
		
		//public var changeTasks:Array;
		//public var addTasks:Array;
		//public var finishTasks:Array;
		
		public function TaskEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false, data:Object = null, id:int = 0) 
		{ 
			super(type, bubbles, cancelable);
			//changeTasks = new Array();
			//addTasks = new Array();
			//finishTasks = new Array();
			this.data = data;
			this.id = id;
		} 
		
		public override function clone():Event 
		{ 
			return new TaskEvent(type, bubbles, cancelable, data);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("TaskEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}