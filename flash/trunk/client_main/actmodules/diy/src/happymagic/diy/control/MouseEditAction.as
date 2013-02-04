package happymagic.diy.control 
{
	import flash.geom.Point;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.WorldState;
	import happymagic.display.view.PiaoMsgType;
	import happymagic.events.PiaoMsgEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.scene.world.control.MouseMagicAction;
	import happymagic.scene.world.grid.item.Decor;
	import happymagic.scene.world.grid.item.Door;
	import happymagic.scene.world.grid.item.WallDecor;
	import happymagic.scene.world.MagicWorld;
	/**
	 * ...
	 * @author slam
	 */
	public class MouseEditAction extends MouseMagicAction
	{
		
		protected var item:IsoItem;
		public function MouseEditAction($state:WorldState) 
		{
			super($state);
			(state.world as MagicWorld).enterEditMode();
		}
		
		public function onFurnaceDecorOver(event:GameMouseEvent):void {
			(event.item as IsoItem).showGlow();
		}
		
		public function onFurnaceDecorOut(event:GameMouseEvent):void {
			(event.item as IsoItem).hideGlow();
		}
		
		public function onFurnaceDecorClick(event:GameMouseEvent):void {
			(event.item as IsoItem).hideGlow();
			setItem(event.item);
		}
		
        override public function onDecorOver(event:GameMouseEvent) : void
        {
            (event.item as IsoItem).showGlow();
            return;
        }
		
		override public function onDecorClick(event:GameMouseEvent):void
		{
			Decor(event.item).hideGlow();
			setItem(event.item);
		}
		
		override public function onDecorOut(event:GameMouseEvent) : void
        {
            (event.item as IsoItem).hideGlow();
            return;
        }
		
		override public function onDoorClick(event:GameMouseEvent):void
		{
			Door(event.item).hideGlow();
			setItem(event.item);
		}
		
		public function setItem(value:IsoItem):void {
			this.item = value;
		}
		
        
		
        override public function onWallDecorOver(event:GameMouseEvent) : void
        {
            WallDecor(event.item).showGlow();
            return;
        }
		
		override public function onWallDecorClick(event:GameMouseEvent):void
		{
			WallDecor(event.item).hideGlow();
			setItem(event.item);
		}
		
        override public function onWallDecorOut(event:GameMouseEvent) : void
        {
			WallDecor(event.item).hideGlow();
            return;
        }
		
		override public function onBackgroundClick(event:GameMouseEvent) : void
		{
			if (this.item != null) {
				new MouseCarryIsoAction(this.item, this.state);
				//显示物件的占格表现
				this.item.addIsoTile();
			}
			this.item = null;
		}
		
	}

}