package happyfish.storyEdit.display 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import happyfish.display.ui.defaultList.DefaultVLineListView;
	
	/**
	 * ...
	 * @author jj
	 */
	public class EditActionVListView extends DefaultVLineListView 
	{
		
		public function EditActionVListView(uiview:Sprite,_container:DisplayObjectContainer,_pageLength:uint,_hideButton:Boolean=false,_autoAlginButton:Boolean=true) 
		{
			super(uiview as Sprite, _container, _pageLength, _hideButton, _autoAlginButton);
			
		}
		
		override protected function clickFun(e:MouseEvent):void 
		{
			switch (e.target.name) 
			{
				case "prevBtn":
					for (var i:int = 0; i < grid.data.length; i++) 
					{
						(grid.data[i].list as EditActionListView).prevPage();
					}
				break;
				
				case "nextBtn":
					for (var j:int = 0; j < grid.data.length; j++) 
					{
						(grid.data[j].list as EditActionListView).nextPage();
					}
				break;
				
			}
		}
		
		override public function initPage():void 
		{
			super.initPage();
			
			if (grid.data.length==0) 
			{
				return;
			}
			
			var tmp:EditActionListView = grid.data[0].list as EditActionListView;
			
			if (tmp.currentPage>0) 
			{
				setBtnVisible(iview["prevBtn"], true);
			}else {
				setBtnVisible(iview["prevBtn"], false);
			}
			
			if (tmp.currentPage+1<=Math.ceil(tmp.data.length/tmp.pageLength)+1) 
			{
				setBtnVisible(iview["nextBtn"], true);
			}else {
				setBtnVisible(iview["nextBtn"], false);
			}
		}
		
	}

}