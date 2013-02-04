package happymagic.display.view.friends 
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.display.StageDisplayState;
	import flash.events.MouseEvent;
	import happyfish.display.ui.GridItem;
	import happymagic.model.MagicJSManager;
	
	/**
	 * ...
	 * @author 
	 */
	public class FriendBlankItemView extends GridItem 
	{
		
		public function FriendBlankItemView(uiview:MovieClip) 
		{
			super(uiview);
			uiview.mouseChildren = false;
			uiview.addEventListener(MouseEvent.CLICK, clickFun);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			MagicJSManager.getInstance().goInvite();
			view.stage.displayState = StageDisplayState.NORMAL;
		}
		
	}

}