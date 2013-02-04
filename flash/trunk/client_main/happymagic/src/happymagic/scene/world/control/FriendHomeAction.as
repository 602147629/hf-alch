package happymagic.scene.world.control 
{
	import com.friendsofed.isometric.Point3D;
	import flash.geom.Point;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.manager.local.LocaleWords;
	import happyfish.scene.iso.IsoUtil;
	import happyfish.scene.world.WorldState;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.DecorVo;
	import happymagic.scene.world.award.AwardItemView;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.MagicWorld;
	import happymagic.utils.RequestQueue;
	/**
	 * ...
	 * @author jj
	 */
	public class FriendHomeAction extends MouseDefaultAction
	{
		
		public function FriendHomeAction($state:WorldState) 
		{
			super($state, false);
		}
		
		override public function onAwardItemOver(event:GameMouseEvent):void 
		{
			var award:AwardItemView = event.item as AwardItemView;
			award.out();
		}
		
		override public function onBackgroundClick(event:GameMouseEvent):void 
		{
			if (skipBackgroundClick) 
			{
				skipBackgroundClick = false;
				return;
			}
			var flag:Boolean = DataManager.getInstance().isDraging;
			if (!DataManager.getInstance().isDraging) (state.world as MagicWorld).player.go();
			DataManager.getInstance().isDraging = false;
		}
		
		/**
		 * 墙上道具over事件
		 * @param	event
		 */
        override public function onWallDecorOver(event:GameMouseEvent) : void
        {
			//如果是门,显示tips
			if (event.item is Door) {
				event.item.showGlow();
			}
            return;
        }
		
		/**
		 * 墙上道具out事件
		 * @param	event
		 */
        override public function onWallDecorOut(event:GameMouseEvent) : void
        {
			//如果是门,隐藏tips
			if (event.item is Door) {
				event.item.hideGlow();
			}
            return;
        }
	}

}