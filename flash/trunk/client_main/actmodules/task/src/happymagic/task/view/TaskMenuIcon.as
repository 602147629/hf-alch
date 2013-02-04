package happymagic.task.view 
{
	import flash.events.MouseEvent;
	import flash.utils.setTimeout;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.manager.module.ModuleManager;
	import happymagic.events.TaskEvent;
	import happymagic.manager.DisplayManager;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class TaskMenuIcon extends TaskMenuIconUI 
	{
		public function TaskMenuIcon() 
		{
			showState(true);
			addEventListener(MouseEvent.CLICK, clickHandler);
			EventManager.addEventListener(TaskEvent.TASK_ACCEPT, taskEventHandler);
			EventManager.addEventListener(TaskEvent.TASKS_UPDATE_FOR_DAILY, taskEventHandler);
		}
		
		private function taskEventHandler(e:TaskEvent):void 
		{
			switch(e.type)
			{
				case TaskEvent.TASK_ACCEPT :
				case TaskEvent.TASKS_UPDATE_FOR_DAILY :
					showState(false);
					break;
					
			}
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			var module:TaskListUISprite = ModuleManager.getInstance().showModule(ModuleName.TaskListUISprite) as TaskListUISprite;
			//DisplayManager.uiSprite.setBg(module);
			DisplayManager.sceneSprite.visible = false;
			module.show();
			
			showState(true);
		}
		
		private function showState(isNormal:Boolean):void
		{
			gotoAndStop(isNormal ? "Normal" : "Flicker");
		}
		
		
	}

}