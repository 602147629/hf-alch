package happymagic.diy.display 
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happyfish.scene.world.WorldState;
	import happymagic.diy.control.MouseEditAction;
	import happymagic.diy.control.MouseRemoveItemAction;
	import happymagic.diy.control.MouseRotationAction;
	import happymagic.events.DiyEvent;
	import happymagic.manager.DataManager;
	import happymagic.scene.world.control.MouseDefaultAction;
	import happymagic.scene.world.MagicWorld;
	
	/**
	 * ...
	 * @author 
	 */
	public class DiyMenuView extends UISprite 
	{
		private var iview:diyMenuUi;
		
		public function DiyMenuView() 
		{
			_view = new diyMenuUi() as MovieClip;
			iview = view as diyMenuUi;
			
			iview.addEventListener(MouseEvent.CLICK, clickFun);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			var worldState:WorldState = DataManager.getInstance().worldState;
			switch (e.target) 
			{
				case iview.normalBtn:
					
					new MouseDefaultAction(worldState);
				break;
				
				case iview.moveBtn:
					new MouseEditAction(worldState);
				break;
				
				case iview.mirrorBtn:
					new MouseRotationAction(worldState);
				break;
				
				case iview.removeBtn:
					new MouseRemoveItemAction(worldState);
				break;
			}
		}
		
	}

}