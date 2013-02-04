package happymagic.view 
{
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	/**
	 * ...
	 * @author ZC
	 */
	public class LearnFeedView extends UISprite
	{
		private var iview:LearnFeedViewUI;		
		public function LearnFeedView() 
		{
			_view = new LearnFeedViewUI();
			iview = _view as LearnFeedViewUI;	
			
			iview.addEventListener(MouseEvent.CLICK, clickrun);
		}
		
		private function clickrun(e:MouseEvent):void 
		{
			switch(e.target.name)
			{
				case "closebtn":
					  closeMe(true);
					break;
					
				case "feed":
					  closeMe(true);
					break;
			}
		}
		
		public function setData(_name:String):void
		{
			iview.nametxt.text = _name;
		}
	}

}