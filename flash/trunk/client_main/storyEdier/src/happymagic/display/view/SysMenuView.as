package happymagic.display.view 
{
	import flash.display.MovieClip;
	import flash.display.StageDisplayState;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.manager.BgMusicManager;
	import happyfish.manager.ShareObjectManager;
	import happyfish.manager.SoundEffectManager;
	import happymagic.manager.DisplayManager;
	/**
	 * ...
	 * @author jj
	 */
	public class SysMenuView extends sysMenuUi
	{
		private var fullScreen:Boolean;
		private var inited:Boolean;
		
		public function SysMenuView() 
		{
			
			addEventListener(MouseEvent.CLICK, clickFun);
			
		}
		
		public function init():void 
		{
			bgSoundCloseBtn.visible = ShareObjectManager.getInstance().bgSound;
			bgSoundOpenBtn.visible = !ShareObjectManager.getInstance().bgSound;
			
			soundCloseBtn.visible = ShareObjectManager.getInstance().soundEffect;
			soundOpenBtn.visible = !ShareObjectManager.getInstance().soundEffect;
			
			unFullSceenBtn.visible = (DisplayManager.uiSprite.stage.displayState == StageDisplayState.FULL_SCREEN);
			fullSceenBtn.visible = !(DisplayManager.uiSprite.stage.displayState == StageDisplayState.FULL_SCREEN);
			
			if (!inited) 
			{
				inited = true;
				DisplayManager.uiSprite.stage.addEventListener(Event.RESIZE, resizeFun);
			}
			
		}
		
		private function resizeFun(e:Event):void 
		{
			init();
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case bgSoundOpenBtn:
					ShareObjectManager.getInstance().bgSound = true;
					BgMusicManager.getInstance().soundFlag = true;
				break;
				
				case bgSoundCloseBtn:
					ShareObjectManager.getInstance().bgSound = false;
					BgMusicManager.getInstance().soundFlag = false;
				break;
				
				case soundOpenBtn:
					ShareObjectManager.getInstance().soundEffect = true;
				break;
				
				case soundCloseBtn:
					ShareObjectManager.getInstance().soundEffect = false;
				break;
				
				case fullSceenBtn:
					stage.displayState = StageDisplayState.FULL_SCREEN;
					fullScreen = true;
				break;
				
				case unFullSceenBtn:
					stage.displayState = StageDisplayState.NORMAL;
					fullScreen = false;
				break;
			}
			init();
		}
		
	}

}