package view 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import happyfish.display.ui.defaultList.DefaultListView;
	/**
	 * ...
	 * @author ZC
	 */
	public class DailyListView extends DefaultListView
	{
		
		public function DailyListView(uiview:MovieClip, _container:DisplayObjectContainer, _pageLength:uint = 5, hidebotton:Boolean = false, _autoAlginButton:Boolean = true) 
		{
			super(uiview as MovieClip, _container, _pageLength, hidebotton, _autoAlginButton);	
		}
		
	}

}