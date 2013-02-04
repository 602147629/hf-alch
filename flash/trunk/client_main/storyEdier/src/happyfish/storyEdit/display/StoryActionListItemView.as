package happyfish.storyEdit.display 
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import happyfish.display.ui.defaultList.DefaultListView;
	import happyfish.display.ui.GridItem;
	import happyfish.storyEdit.model.EditDataManager;
	import happyfish.storyEdit.model.vo.EditStoryGridVo;
	
	/**
	 * ...
	 * @author jj
	 */
	public class StoryActionListItemView extends GridItem 
	{
		public var list:DefaultListView;
		public var data:EditStoryGridVo;
		
		public function StoryActionListItemView(uiview:MovieClip) 
		{
			super(uiview);
			
			uiview.mouseChildren = true;
			
			list = new EditActionListView(new Sprite(), uiview, 10);
			list.iview.mouseChildren = true;
			list.tweenTime = 0;
			list.init(480, 20, 41, 20, 0, 0);
			list.setGridItem(StoryActionListSmallItemView, actionListItemUi,StoryActionListSmallItemView, actionListItemUi);
			
		}
		
		override public function setData(value:Object):void 
		{
			data = value as EditStoryGridVo;
			list.generalData = data;
			//list.currentPage = EditDataManager.getInstance().getVar("oldActionPage");
			
			//list.setData(data.actions,"",null,true);
			list.setData(data.actions,"",null,false);
			
			
			
		}
		
	}

}