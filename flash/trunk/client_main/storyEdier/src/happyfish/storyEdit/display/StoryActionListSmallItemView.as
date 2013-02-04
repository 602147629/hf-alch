package happyfish.storyEdit.display 
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.display.ui.GridItem;
	import happyfish.display.ui.Tooltips;
	import happyfish.manager.EventManager;
	import happyfish.storyEdit.events.StoryEditEvent;
	import happyfish.storyEdit.model.EditDataManager;
	import happyfish.storyEdit.model.vo.EditStoryActionVo;
	import happyfish.storyEdit.model.vo.EditStoryGridVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class StoryActionListSmallItemView extends GridItem 
	{
		private var data:EditStoryActionVo;
		private var iview:actionListItemUi;
		
		public function StoryActionListSmallItemView(uiview:MovieClip) 
		{
			super(uiview);
			iview = view as actionListItemUi;
			iview.addEventListener(MouseEvent.CLICK, clickFun);
			
			
			
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			var event:StoryEditEvent;
			if (data) 
			{
				event = new StoryEditEvent(StoryEditEvent.REQUEST_EDIT_ACTION);
				event.actionStep = index;
				event.actorId = (generalData as EditStoryGridVo).actorId;
				event.actor = (generalData as EditStoryGridVo).actor;
				event.action = data;
				EventManager.getInstance().dispatchEvent(event);
			}else {
				event = new StoryEditEvent(StoryEditEvent.REQUEST_ADD_ACTION);
				event.actionStep = index;
				event.actorId = (generalData as EditStoryGridVo).actorId;
				event.actor = (generalData as EditStoryGridVo).actor;
				EventManager.getInstance().dispatchEvent(event);
			}
		}
		
		override public function setData(value:Object):void 
		{
			data = null;
			var arr:Array = (generalData as EditStoryGridVo).actions;
			for (var i:int = 0; i < arr.length; i++) 
			{
				if (arr[i].index == index) 
				{
					data = arr[i];
				}
			}
			
			if (data) 
			{
				iview.bg.gotoAndStop(2);
				Tooltips.getInstance().register(iview, typeName);
				iview.waitIcon.visible = Boolean(data.wait);
			}else {
				iview.bg.gotoAndStop(1);
				iview.waitIcon.visible = false;
			}
			iview.txt.text = index.toString();
			
			
		}
		
		public function get typeName():String {
			
			var arr:Array = ["移动", "说话", "振屏", "动作", "叠加动画", "隐藏"];
			return arr[data.type];
		}
		
	}

}