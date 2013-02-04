package happymagic.display.view.dungeon 
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.filters.DropShadowFilter;
	import flash.geom.Rectangle;
	import flash.text.TextField;
	import flash.text.TextFormat;
	import happyfish.cacher.SwfClassCache;
	import happyfish.events.SwfClassCacheEvent;
	import happymagic.display.view.ui.ItemIconView;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.classVo.BaseItemClassVo;
	/**
	 * ...
	 * @author XiaJunJie
	 */
	public class DungeonActHand extends Sprite
	{
		private static var _instance:DungeonActHand;
		public static function getInstance():DungeonActHand
		{
			if (!_instance) _instance = new DungeonActHand;
			return _instance;
		}
		
		public var numTxt:TextField;
		public var currentItemClassVo:BaseItemClassVo;
		
		private var icon:MovieClip;
		private var loadingItem:BaseItemClassVo;
		private var iconLib:Object;
		
		public function DungeonActHand() 
		{
			numTxt = new TextField;
			
			var textFormat:TextFormat = new TextFormat();
			textFormat.font = "Arial";
			//textFormat.font = "Tahoma";
			textFormat.bold = true;
			textFormat.size = 12;
			numTxt.defaultTextFormat = textFormat;
			
			numTxt.filters = [new DropShadowFilter(2,45,0x003366,1,4,4,10)];
			
			numTxt.text = "num";
			numTxt.height = numTxt.textHeight + 4;
			
			numTxt.x = 13;
			numTxt.y = -numTxt.height/2;
			
			addChild(numTxt);
			
			iconLib = new Object;
		}
		
		public function update(cid:int, num:int):void
		{
			if (!currentItemClassVo || currentItemClassVo.cid != cid)
			{
				currentItemClassVo = DataManager.getInstance().itemData.getItemClass(cid);
				
				if (iconLib[currentItemClassVo.cid])
				{
					if (icon) removeChild(icon);
					icon = iconLib[currentItemClassVo.cid];
					addChild(icon);
				}
				else
				{
					if (loadingItem) return;
					loadingItem = currentItemClassVo;
					SwfClassCache.getInstance().addEventListener(SwfClassCacheEvent.COMPLETE, classGeted);
					SwfClassCache.getInstance().loadClass(loadingItem.className);
				}
			}
			
			numTxt.text = "x" + num;
			numTxt.textColor = num == 0 ? 0xDD0000 : 0xDDDDDD;
		}
		
		private function classGeted(e:SwfClassCacheEvent):void 
		{
			if (e.className==loadingItem.className) 
			{
				SwfClassCache.getInstance().removeEventListener(SwfClassCacheEvent.COMPLETE,classGeted);
				
				if (currentItemClassVo.cid == loadingItem.cid)
				{
					if (icon) removeChild(icon);
					var tmpclass:Class = SwfClassCache.getInstance().getClass(loadingItem.className);
					icon = new tmpclass() as MovieClip;
					addChild(icon);
					
					layoutBody();
				}
				iconLib[loadingItem.cid] = icon;
				loadingItem = null;
			}
		}
		
		public function layoutBody():void {
			var iconrect:Rectangle = icon.getBounds(icon);
			
			var iconScale:Number;
			var wScale:Number = 30 / iconrect.width;
			var hScale:Number = 30 / iconrect.height;
			if (wScale>hScale) 
			{
				iconScale = hScale;
			}else {
				iconScale = wScale;
			}
			iconScale = Number(iconScale.toFixed(2));
			icon.scaleX=
			icon.scaleY = iconScale;
			
			var tmprect:Rectangle = new Rectangle(-15,-15,30,30);
			icon.x = tmprect.x + (tmprect.width / 2) - iconrect.width * iconScale / 2 -(iconrect.left-icon.x)* iconScale-6*iconScale;
			icon.y = tmprect.y + (tmprect.height / 2) - iconrect.height * iconScale / 2 - (iconrect.top - icon.y) * iconScale-6 * iconScale;
		}
		
	}

}