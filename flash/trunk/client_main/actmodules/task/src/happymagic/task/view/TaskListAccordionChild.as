package happymagic.task.view 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.Sprite;
	import happyfish.display.ui.accordion.AccordionChild;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.ui.events.GridPageEvent;
	import happyfish.display.ui.Pagination;
	import happyfish.utils.display.TextFieldUtil;
	import happymagic.model.vo.task.TaskVo;
	import happymagic.task.events.TaskListEvent;
	import happymagic.task.view.render.TaskItem;
	import happymagic.task.view.render.TaskItemRender;
	/**
	 * ...
	 * @author lite3
	 */
	public class TaskListAccordionChild extends AccordionChild
	{
		private var iview:TaskListAccordionChildUI;
		private var maxPageLength:int = 7;
		
		public function TaskListAccordionChild(view:Sprite) 
		{
			super(view);
			iview = view as TaskListAccordionChildUI;
			TextFieldUtil.autoSetDefaultFormat(iview.titleTxt);
			iview.titleTxt.mouseEnabled = false;
			listView.selectCallBack = selectListItemHandler;
		}
		
		private function selectListItemHandler(e:GridPageEvent):void 
		{
			view.dispatchEvent(new TaskListEvent(TaskListEvent.SHOW_TASK_ITEM, true, false, listView.selectedValue as TaskVo));
		}
		
		override protected function createListView(view:Sprite, container:DisplayObjectContainer):DefaultListView 
		{
			var listView:DefaultListView = new DefaultListView(view, container, 6, false, false);
			listView.setGridItem(TaskItem, TaskItemRender);
			listView.pagination = new Pagination();
			listView.pagination.x = 0;
			listView.pagination.y = 0;
			return listView;
		}
		
		override public function set selected(value:Boolean):void 
		{
			super.selected = value;
			iview.titleTxt.textColor = value ? 0xFCCA58 : 0xDDC8AA;
		}
		
		override public function resize(w:int, h:int):void 
		{
			super.resize(w, h);
			listView.init(w, h - titleHeight, 183, 23, 5, titleHeight);
			
			iview.prevBtn.y = h - iview.prevBtn.height - 0;
			iview.nextBtn.y = h - iview.nextBtn.height - 0;
			listView.pagination.y =  h - iview.nextBtn.height - 0;
			listView.pagination.x = w / 2;
		}
		
		override public function setData(o:Object):void 
		{
			var hasMultipage:Boolean = o[listField].length > maxPageLength;
			listView.pageLength = hasMultipage ? maxPageLength - 1 : maxPageLength;
			super.setData(o);
			iview.prevBtn.visible = hasMultipage;
			iview.nextBtn.visible = hasMultipage;
			listView.pagination.visible = hasMultipage;
		}
		
	}

}