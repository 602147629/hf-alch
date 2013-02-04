package happymagic.diy.control 
{
	import happyfish.events.GameMouseEvent;
	import happyfish.scene.world.WorldState;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.ItemVo;
	import happymagic.scene.world.control.MouseMagicAction;
	import happymagic.scene.world.MagicWorld;
	/**
	 * ...
	 * @author 
	 */
	public class MouseWallChangeAction extends MouseMagicAction
	{
		var item:ItemVo;//墙纸物品数据
		public function MouseWallChangeAction(_item:ItemVo, $state:WorldState) 
		{
			item = _item;
			super($state, true);
			(state.world as MagicWorld).enterEditMode();
		}
		
		override public function onBackgroundClick(event:GameMouseEvent):void 
		{
			
		}
		
	}

}