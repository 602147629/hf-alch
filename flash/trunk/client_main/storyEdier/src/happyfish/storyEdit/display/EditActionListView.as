package happyfish.storyEdit.display 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.Sprite;
	import happyfish.display.ui.defaultList.DefaultListView;
	
	/**
	 * ...
	 * @author jj
	 */
	public class EditActionListView extends DefaultListView 
	{
		
		public function EditActionListView(uiview:Sprite,_container:DisplayObjectContainer,_pageLength:uint,_hideButton:Boolean=false,_autoAlginButton:Boolean=true) 
		{
			super(uiview as Sprite, _container, _pageLength,_hideButton,_autoAlginButton);
		}
		
		override public function nextPage():void 
		{
			currentPage++;
			initPage();
		}
		
	}

}