package happymagic.diy.control 
{
	import flash.events.Event;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.local.LocaleWords;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.WorldState;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.diy.model.SaveDiyRemoveCommand;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.scene.world.control.MouseMagicAction;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.item.FurnaceDecor;
	import happymagic.scene.world.MagicWorld;
	/**
	 * ...
	 * @author 
	 */
	public class MouseRemoveItemAction extends MouseEditAction
	{
		public function MouseRemoveItemAction($state:WorldState) 
		{
			super($state);
		}
		
		override public function onBackgroundClick(event:GameMouseEvent):void 
		{
			if (!item) 
			{
				return;
			}
			
			if (item is Door && (state.world as MagicWorld).doorList.length<=1) 
			{
				//门不可全部移除
				DisplayManager.showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("doorCantMove"));
				return;
			}
			
			//有合成进行中的装饰物不可移除
			if (item is FurnaceDecor) 
			{
				if (DataManager.getInstance().mixData.getCurMix(item.data.id)) 
				{
					DisplayManager.showPiaoStr(PiaoMsgType.TYPE_BAD_STRING, LocaleWords.getInstance().getWord("furnaceCantMove"));
					return;
				}
			}
			
			//请示移除物件
			requestRemove();
			
			//移除物件item
			if (item is Door) {
				(item as Door).resetWallView();
			}
			item.remove();
			item = null;
		}
		
		private function requestRemove():void 
		{
			var command:SaveDiyRemoveCommand = new SaveDiyRemoveCommand();
			command.addEventListener(Event.COMPLETE, requestRemove_complete);
			command.remove(item.data.id);
		}
		
		private function requestRemove_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, requestRemove_complete);
			
			
		}
		
	}

}