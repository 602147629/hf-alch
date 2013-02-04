package happyfish.display.ui.accordion 
{
	import com.adobe.utils.VectorUtil;
	import flash.display.DisplayObjectContainer;
	import flash.display.Sprite;
	import flash.events.EventDispatcher;
	import flash.events.MouseEvent;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class AccordionBase extends EventDispatcher 
	{
		public const view:Sprite = new Sprite();
		private var _data:Array;
		public function get data():Array { return _data; }
		
		public var listField:String = "list";
		public var titleField:String = "title";
		
		protected var width:int;
		protected var height:int;
		protected var childTitleWidth:int;
		protected var childTitleHeight:int;
		protected var gap:int;
		
		protected var oneContentHeight:int;
		
		private var childClass:Class;
		private var childUIClass:Class;
		
		protected const childList:Vector.<AccordionChild> = new Vector.<AccordionChild>();
		
		public function AccordionBase(container:DisplayObjectContainer) 
		{
			container.addChild(view);
		}
		
		public function init(width:int, height:int, x:int, y:int, childTitleWidth:int = 10, childTitleHeight:int = 10, gap:int = 5):void
		{
			view.x = x;
			view.y = y;
			this.width = width;
			this.height = height;
			this.childTitleWidth = childTitleWidth;
			this.childTitleHeight = childTitleHeight;
			this.gap = gap;
		}
		
		public function setData(arr:*):void
		{
			_data = (arr is Array) ? arr : VectorUtil.toArray(arr);
			var len:int = _data.length;
			for (var i:int = childList.length - 1; i >= len; i--)
			{
				var child:AccordionChild = childList[i];
				child.getTitleBar().removeEventListener(MouseEvent.CLICK, titleClickHandler);
				child.clear();
				view.removeChild(child.view);
			}
			
			initChildren();
		}
		
		public function getChildByIndex(idx:int):AccordionChild
		{
			if (idx<0 || idx>=childList.length) return null;
			return childList[idx];
		}
		
		protected function titleClickHandler(e:MouseEvent):void { }
		
		public function setChildrenItem(childClass:Class, childUIClass:Class):void
		{
			this.childClass = childClass;
			this.childUIClass = childUIClass;
		}
		
		protected function createChild():AccordionChild
		{
			var child:AccordionChild;
			if (!childUIClass)  child = new childClass() as AccordionChild;
			else child = new childClass(new childUIClass()) as AccordionChild;
			
			child.listField = listField;
			child.titleField = titleField;
			
			child.getTitleBar().addEventListener(MouseEvent.CLICK, titleClickHandler);
			child.setTitleWidth(childTitleWidth);
			child.setTitleHeight(childTitleHeight);
			return child;
		}
		
		public function clear():void 
		{
			for (var i:int = childList.length - 1; i >= 0; i--)
			{
				var child:AccordionChild = childList[i];
				child.getTitleBar().removeEventListener(MouseEvent.CLICK, titleClickHandler);
				child.clear();
			}
			childList.length = 0;
		}
		
		protected function initChildren():void 
		{
			var len:int = childList.length;
			oneContentHeight = height;
			
			for (var i:int = 0; i < len; i++)
			{
				var child:AccordionChild = childList[i];
				child.clear();
				oneContentHeight -= child.getTitleHeight() + gap;
			}
			len = _data.length;
			for (i; i < len; i++)
			{
				child = createChild();
				childList.push(child);
				oneContentHeight -= child.getTitleHeight() + gap;
			}
			if (len > 0) oneContentHeight += gap;
			if (oneContentHeight < 0) oneContentHeight = 1;
			
			for (i = 0; i < len; i++)
			{
				child = childList[i];
				child.index = i;
				child.resize(width, oneContentHeight + child.getTitleHeight());
				child.setData(_data[i]);
				view.addChild(child.view);
			}
		}
		
	}

}