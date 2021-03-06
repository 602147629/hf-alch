package happymagic.display.view.task 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.GridPage;
	import happymagic.display.view.ui.DefaultAwardItemRender;
	import happymagic.display.view.ui.DefaultAwardItemRenderUI;
	import happymagic.model.vo.ConditionVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class TaskNeedItemListView extends DefaultListView
	{
		public var selectItem:ConditionVo;
		public function TaskNeedItemListView(uiview:MovieClip,_container:DisplayObjectContainer,_pageLength:uint=5,hidebotton:Boolean = false) 
		{
			super(uiview as MovieClip, _container,_pageLength,hidebotton);
			//iview.addEventListener(MouseEvent.CLICK, selectFun,true);
			
			setGridItem(TaskNeedItemView, DefaultAwardItemRenderUI);
		}
		
		override protected function createItem(value:Object,index:int):GridItem 
		{
			var tmp:GridItem;
			
			if (itemUiClass==null) 
			{
				tmp = new itemClass() as GridItem;
			}else {
				tmp = new itemClass(new itemUiClass()) as GridItem;
			}
			
			tmp.setData(value);
			
			return tmp;
		}
		
		private function selectFun(e:MouseEvent):void 
		{
			if (e.target is taskConditionItemUi) 
			{
				selectItem = (e.target.control as TaskNeedItemView).data;
				dispatchEvent(new Event(Event.SELECT));
			}
		}
		
	}

}