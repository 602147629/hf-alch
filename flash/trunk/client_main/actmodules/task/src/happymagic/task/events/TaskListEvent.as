package happymagic.task.events 
{
	import flash.events.Event;
	import happymagic.model.vo.task.TaskVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class TaskListEvent extends Event 
	{
		public static const SHOW_TASK_ITEM:String = "showTaskItem";
		
		
		public var task:TaskVo;
		
		public function TaskListEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false, task:TaskVo = null) 
		{ 
			super(type, bubbles, cancelable);
			this.task = task;
		} 
		
		public override function clone():Event 
		{ 
			return new TaskListEvent(type, bubbles, cancelable, task);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("TaskListEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}