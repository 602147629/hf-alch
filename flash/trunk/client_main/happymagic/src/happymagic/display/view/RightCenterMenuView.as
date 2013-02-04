package happymagic.display.view 
{
	import flash.display.DisplayObject;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import flash.utils.setTimeout;
	import happyfish.display.view.UISprite;
	import happyfish.manager.EventManager;
	import happymagic.events.FunctionFilterEvent;
	import happymagic.events.MagicEvent;
	import happymagic.manager.DataManager;
	
	/**
	 * ...
	 * @author 
	 */
	public class RightCenterMenuView extends UISprite 
	{
		private var mcs:Array;
		
		public function RightCenterMenuView() 
		{
			super();
			_view = new MovieClip();
			mcs = new Array();
			setTimeout(dispatchInitEvent, 100);
			EventManager.addEventListener(FunctionFilterEvent.UNLOCK, unlockFuncHandler);
		}
		
		private function unlockFuncHandler(e:FunctionFilterEvent):void 
		{
			sortMc();
		}
		
		private function dispatchInitEvent():void 
		{
			EventManager.getInstance().dispatchEvent(new MagicEvent(MagicEvent.RIGHT_CENTER_INIT));
		}
		
		public function addMc(name:String,mc:DisplayObject,priority:int,callBack:Function=null,callParams:Array=null):void {
			
			
			mcs.push( { name:name, mc:mc, priority:priority, callback:callBack, params:callParams,
						rect:mc.getBounds(null) } );
			if (callBack!=null) 
			{
				mc["mouseChildren"] = false;
				mc["addEventListener"](MouseEvent.CLICK, mcClick);
			}
			sortMc();
		}
		
		private function mcClick(e:MouseEvent):void 
		{
			for (var i:int = 0; i < mcs.length; i++) 
			{
				if (mcs[i].mc==e.target) 
				{
					mcs[i].callback.apply(null, mcs[i].params);
					
					return;
				}
			}
		}
		
		public function removeMc(name:String):void {
			for (var i:int = 0; i < mcs.length; i++) 
			{
				if (mcs[i].name == name) 
				{
					mcs.splice(i, 1);
					if (mcs[i].mc.parent == view) view.removeChild(mcs[i].mc);
					sortMc();
					return;
				}
			}
		}
		
		public function getMc(name:String):DisplayObject {
			for (var i:int = 0; i < mcs.length; i++) 
			{
				if (mcs[i].name == name) 
				{
					return mcs[i].mc; 
				}
			}
			return null;
		}
		
		private function sortMc():void 
		{
			var isLock:Function = DataManager.getInstance().functionFilterData.isLock;
			mcs.sortOn("priority", Array.NUMERIC | Array.DESCENDING);
			var offsetY:int = 0;
			for (var i:int = 0; i < mcs.length; i++) 
			{
				if (isLock(mcs[i].name)) continue;
				var mc:DisplayObject = mcs[i].mc;
				var rect:Rectangle = mcs[i].rect;
				mc.x = -rect.x - rect.width / 2;
				mc.y = offsetY - rect.y;
				offsetY += rect.height + 15;
				view.addChild(mc)
			}
		}
		
	}

}