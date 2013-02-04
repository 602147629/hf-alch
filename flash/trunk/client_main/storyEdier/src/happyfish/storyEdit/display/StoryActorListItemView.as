package happyfish.storyEdit.display 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import happyfish.display.ui.GridItem;
	import happyfish.display.view.BitmapIconView;
	import happyfish.display.view.IconView;
	import happyfish.manager.EventManager;
	import happyfish.storyEdit.events.StoryEditEvent;
	import happyfish.storyEdit.model.vo.EditStoryGridVo;
	import happyfish.storyEdit.model.vo.EditStoryVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class StoryActorListItemView extends GridItem 
	{
		private var iview:actorListItemUi;
		private var data:EditStoryGridVo;
		private var icon:IconView;
		
		public function StoryActorListItemView(uiview:MovieClip) 
		{
			super(uiview);
			iview = uiview as actorListItemUi;
			iview.txt.mouseEnabled = false;
			
			icon = new IconView(30, 30);
			iview.addChild(icon);
			
			iview.mouseChildren = false;
			
			iview.addEventListener(MouseEvent.CLICK, clickFun);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			var event:StoryEditEvent = new StoryEditEvent(StoryEditEvent.SHOW_ACTOR_EDIT);
			event.actorId = data.actorId;
			event.point = new Point(e.stageX, e.stageY);
			EventManager.getInstance().dispatchEvent(event);
		}
		
		override public function setData(value:Object):void 
		{
			data = value as EditStoryGridVo;
			
			icon.setData(data.actor.className);
			iview.txt.text = data.actor.name;
			iview.txt.x = 10;
		}
		
	}

}