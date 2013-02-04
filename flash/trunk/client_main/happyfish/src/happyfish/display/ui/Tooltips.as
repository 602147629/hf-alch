package happyfish.display.ui
{
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import flash.geom.Point;
	import flash.geom.Rectangle;
	import flash.text.TextField;
	import flash.text.TextFormat;
	import flash.utils.clearTimeout;
	import flash.utils.Dictionary;
	import flash.utils.setTimeout;
	
	/**
	 * ToolTips类
	 * 单例 是管理类
	 * @author jj
	 */
	public class Tooltips 
	{
		private static var instance:Tooltips;
		private var map:Dictionary;
		private var maxWidth:uint=120;
		private var maxHeight:uint=30;
		private var tips:Sprite;
		private var container:DisplayObjectContainer;
		private var tipsTxt:TextField;
		private var showTimeId:uint;
		public var delay:uint=300;
		private var tipsformat:TextFormat;
		private var bgs:Object;
		
		private var curTarget:DisplayObject;
		
		public var buffer:int = 5;
		
		// left:9 right:9, top:9, bottom:11
		public var arrowPadding:int = 11;
		public var otherPadding:int = 9;
		
		public static const DEFAULT_BG:String = "defaultBg";
		
		public static const TYPE_DRAG:String = "dragTips";
		public static const TYPE_STAND:String = "standTips";
		
		public static const LEFT:int = 1;
		public static const TOP:int = 2;
		public static const RIGHT:int = 3;
		public static const BOTTOM:int = 0;
		
		public function Tooltips(access:Private) 
		{
			
			if (access != null)
			{	
				if (instance == null)
				{				
					instance = this;
					//init
				bgs = new Object();
				map = new Dictionary();
				tips = new Sprite();
				tips.mouseChildren =
				tips.mouseEnabled = false;
				createTips();
				}
			}
			else
			{	
				throw new Error( "Tooltips"+"单例" );
			}
		}
		
		public static function getInstance():Tooltips
		{
			if (instance == null)
			{
				instance = new Tooltips( new Private() );
				
			}
			return instance;
		}
		
		public function setContainer(_Container:DisplayObjectContainer):void {
			container = _Container;
		}
		
		public function saveBg(bgname:String,dis:DisplayObject):void {
			bgs[bgname] = dis;
		}
		
		public function getBg(bgname:String):DisplayObject {
			return bgs[bgname];
		}
		
		public function getDefaultBg():DisplayObject { return bgs[DEFAULT_BG]; }
		
		public function register(target:DisplayObject, content:String = "", _bg:DisplayObject = null, type:String = TYPE_DRAG, _standPoint:Point = null, orientation:int = 0):void
		{
			if (!map[target])
			{
				target.addEventListener(MouseEvent.ROLL_OVER, overFun);
				target.addEventListener(MouseEvent.ROLL_OUT, outFun);
				target.addEventListener(Event.REMOVED_FROM_STAGE, beRemovedFun);
				map[target] = new TipInfo(type, (_bg || getDefaultBg()), content, _standPoint, orientation);
			}else {
				map[target].content = content;
			}
		}
		
		private function beRemovedFun(e:Event):void 
		{
			e.currentTarget.removeEventListener(Event.REMOVED_FROM_STAGE, beRemovedFun);
			unRegister(e.currentTarget as DisplayObject);
		}
		
		public function unRegister(target:DisplayObject):void
		{
			if (curTarget == target) curTarget = null;
			if (map[target]) 
			{
				delete map[target];
				target.removeEventListener(MouseEvent.ROLL_OVER, overFun);
				target.removeEventListener(MouseEvent.ROLL_OUT, outFun);
			}
		}
		
		private function outFun(e:MouseEvent):void 
		{
			if (showTimeId) 
			{
				clearTimeout(showTimeId);
			}
			tips.visible = false;
			container.removeEventListener(Event.ENTER_FRAME, dragFun);
		}
		
		private function overFun(e:MouseEvent):void 
		{
			if (showTimeId)
			{
				clearTimeout(showTimeId);
				showTimeId = 0;
			}
			curTarget = e.currentTarget as DisplayObject;
			showTimeId = setTimeout(initTips, delay);
		}
		
		private function createTips():void {
			tipsTxt = new TextField();
			
			tipsformat = tipsTxt.getTextFormat();
			tipsformat.align = "left";
			tips.addChild(tipsTxt);
			tips.visible = false;
		}
		
		
		
		private function initTips():void
		{
			while (tips.numChildren>0) 
			{
				tips.removeChildAt(0);
			}
			var info:TipInfo = map[curTarget] as TipInfo;
			if (!info) return;
			
			var p:Point = null;
			if (TYPE_DRAG == info.type)
			{
				p = new Point(container.mouseX, container.mouseY);
				container.addEventListener(Event.ENTER_FRAME, dragFun);
			}else {
				p = info.standPoint || getPointByTarget(curTarget, info.orientation);
				p = curTarget.parent.localToGlobal(p);
				p = container.globalToLocal(p);
			}
			
			tipsTxt.autoSize = "left";
			//tipsTxt.width = maxWidth;
			tipsTxt.multiline = true;
			tipsTxt.wordWrap = false;
			
			tipsTxt.htmlText = info.content;
			tipsTxt.setTextFormat(tipsformat);
			var padding:Rectangle = "padding" in info.bg ? info.bg["padding"] : getPadding(info.orientation);
			
			var w:int = tipsTxt.textWidth + 4 + padding.left + padding.right;
			var h:int = tipsTxt.textHeight + 4 + padding.top + padding.bottom;
			
			setTxtPos(w, h, padding, info.orientation);
			info.bg.width = w;
			info.bg.height = h;
			info.bg.x = 0;
			info.bg.y = 0;
			tips.addChild(tipsTxt);
			tips.addChildAt(info.bg, 0);
			info.border = tips.getBounds(null);
			
			alginTips(p, info.border);
			
			container.addChild(tips);
		}
		
		private function setTxtPos(w:int, h:int, padding:Rectangle, orientation:int):void
		{
			switch(orientation)
			{
				case LEFT   :
					tipsTxt.x = padding.left;
					tipsTxt.y = -h * 0.5 + padding.top;
					break;
					
				case RIGHT  :
					tipsTxt.x = -w + padding.left;
					tipsTxt.y = -h * 0.5 + padding.top;
					break;
					
				case TOP    :
					tipsTxt.x = -w * 0.5 + padding.left;
					tipsTxt.y = padding.top;
					break;
					
				case BOTTOM :
					tipsTxt.x = -w * 0.5 + padding.left;
					tipsTxt.y = -h + padding.top;
					break;
			}
		}
		
		private function getPadding(orientation:int):Rectangle
		{
			var rect:Rectangle = new Rectangle(otherPadding, otherPadding);
			switch(orientation)
			{
				case LEFT   : rect.left = arrowPadding; break;
				case RIGHT  : rect.right = arrowPadding; break;
				case TOP    : rect.top = arrowPadding; break;
				case BOTTOM : rect.bottom = arrowPadding; break;
			}
			return rect;
		}
		
		/**
		 * 
		 * @param	target
		 * @param	orientation 箭头的朝向
		 * @return
		 */
		private function getPointByTarget(target:DisplayObject, orientation:int):Point
		{
			var rect:Rectangle = target.getBounds(target.parent);
			var px:int;
			var py:int;
			switch(orientation)
			{
				case LEFT :
					px = rect.right;
					py = (rect.top + rect.bottom) * 0.5;
					break;
					
				case RIGHT :
					px = rect.left;
					py = (rect.top + rect.bottom) * 0.5;
					break;
					
				case TOP :
					px = (rect.left + rect.right) * 0.5;
					py = rect.bottom;
					break;
					
				case BOTTOM :
					px = (rect.left + rect.right) * 0.5;
					py = rect.top;
					break;
			}
			return new Point(px, py);
		}
		
		private function alginTips(p:Point, border:Rectangle):void
		{
			var maxW:int = container.stage.stageWidth;
			var maxH:int = container.stage.stageHeight;
			
			var tx:int = p.x;
			var ty:int = p.y;
			if (p.x + border.left < 0) tx = 0 - border.x;
			else if (p.x + border.right > maxW) tx = maxW - border.right;
			if (p.y + border.top < 0) ty = 0 - border.top;
			else if (p.y + border.bottom > maxH) ty = maxH - border.bottom;
			
			tips.x = tx;
			tips.y = ty;
			
			tips.visible = true;
		}
		
		private function dragFun(e:Event):void 
		{
			var info:TipInfo = map[curTarget];
			if (!info) return;
			var point:Point = new Point(container.mouseX, container.mouseY);
			alginTips(point, info.border);
		}
		
		public function setMaxSize(_w:uint, _h:uint = 0 ):void {
			maxWidth = _w;
			maxHeight = _h;
			if (tipsTxt) 
			{
				tipsTxt.width = maxWidth;
			}
		}
		
	}
	
}
import flash.display.DisplayObject;
import flash.geom.Point;
import flash.geom.Rectangle;
class Private { }

class TipInfo
{
	public var type:String;
	public var bg:DisplayObject;
	public var content:String;
	public var standPoint:Point;
	public var orientation:int;
	
	public var border:Rectangle;
	
	public function TipInfo(type:String, bg:DisplayObject, content:String, standPoint:Point, orientation:int)
	{
		this.bg = bg;
		this.type = type;
		this.content = content;
		this.standPoint = standPoint;
		this.orientation = orientation;
	}
	
}