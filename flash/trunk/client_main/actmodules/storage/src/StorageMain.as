package 
{
	import flash.display.Sprite;
	import flash.events.Event;
	import happyfish.manager.actModule.display.ActModuleBase;
	import happyfish.manager.actModule.vo.ActVo;
	import happyfish.manager.EventManager;
	import happyfish.manager.module.AlginType;
	import happymagic.event.StorageEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.view.StorageView;
	
	/**
	 * ...
	 * @author ZC
	 */
	public class StorageMain extends ActModuleBase 
	{
		
		public function StorageMain():void 
		{
           EventManager.getInstance().addEventListener(StorageEvent.CLOSEMOUDLE,closecomplete);			
		}
		
		private function closecomplete(e:StorageEvent):void 
		{
			super.close();
		}
		
		override public function init(actVo:ActVo, _type:uint = 1):void 
		{
			removeEventListener(Event.ADDED_TO_STAGE, init);
			// entry point
			super.init(actVo, 1);
			
			var storageView:StorageView = DisplayManager.uiSprite.addModule("StorageView", "happymagic.view.StorageView", false, AlginType.CENTER, 0, 0) as StorageView;
			DisplayManager.uiSprite.setBg(storageView);				
			
		}
		
	}
	
}