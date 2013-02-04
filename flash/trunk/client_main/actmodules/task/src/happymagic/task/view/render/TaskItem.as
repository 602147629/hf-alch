package happymagic.task.view.render 
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.ui.GridItem;
	import happyfish.manager.local.LocaleWords;
	import happymagic.model.vo.task.TaskState;
	import happymagic.model.vo.task.TaskVo;
	import happymagic.task.commands.CompleteTaskCommand;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class TaskItem extends GridItem 
	{
		private var taskVo:TaskVo;
		private var iview:TaskItemRender;
		public function TaskItem(ui:MovieClip) 
		{
			super(ui);
			iview = ui as TaskItemRender;
			iview.mouseChildren = true;
			iview.txt.mouseEnabled = false;
			iview.getAwardBtn.addEventListener(MouseEvent.CLICK, clickHandler);
			iview.newFlag.stop();
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			new CompleteTaskCommand().completeTask(taskVo.id, taskVo.isFinish()).addEventListener(Event.COMPLETE, enabledBtn);
		}
		
		private function enabledBtn(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, enabledBtn);
			iview.getAwardBtn.mouseEnabled = true;
		}
		
		override public function setData(value:Object):void 
		{
			taskVo = value as TaskVo;
			var isFinish:Boolean = taskVo.isFinish();
			var s:String = taskVo.name;
			if (isFinish) s += " <font color='#015F30'>" + LocaleWords.getInstance().getWord("taskFinishText") + "</font>";
			iview.txt.htmlText = s;
			iview.getAwardBtn.visible = isFinish;
			if (TaskState.NOT_VIEW == taskVo.state)
			{
				iview.newFlag.visible = true;
				iview.newFlag.play();
				iview.x = iview.txt.textWidth + iview.txt.x + 4;
			}else
			{
				iview.newFlag.visible = false;
				iview.newFlag.stop();
			}
		}
		
		override public function set selected(value:Boolean):void 
		{
			super.selected = value;
			if (value)
			{
				iview.newFlag.visible = false;
				iview.newFlag.stop();
			}
		}
	}

}