package happyfish.display.ui.accordion 
{
	import flash.display.DisplayObjectContainer;
	import flash.events.Event;
	import flash.events.MouseEvent;
	
	[Event(name = "select", type = "flash.events.Event")]
	
	/**
	 * ...
	 * @author lite3
	 */
	public class Accordion extends AccordionBase
	{
		
		private var _selectedIndex:int;
		
		public function Accordion(container:DisplayObjectContainer) 
		{
			super(container);
		}
		
		public function get selectedIndex():int { return _selectedIndex; }
		public function set selectedIndex(value:int):void 
		{
			if (value<0 || value >= childList.length) value = 0;
			_selectedIndex = value;
			
			resetPos();
		}
		
		private function resetPos():void 
		{
			var len:int = childList.length;
			var pos:int = 0;
			for (var i:int = 0; i < len; i++)
			{
				var child:AccordionChild = childList[i];
				child.view.y = pos;
				if (_selectedIndex == i) pos += oneContentHeight;
				child.selected = _selectedIndex == i;
				pos += gap + child.getTitleHeight();
			}
		}
		
		public function get selectedValue():Object { return data ? data[_selectedIndex] : null; }
		public function set selectedValue(value:Object):void 
		{
			if (!data) return;
			selectedIndex = data.indexOf(value);
		}
		
		public function get selection():AccordionChild { return childList[_selectedIndex]; }
		public function set selection(value:AccordionChild):void 
		{
			selectedIndex = childList.indexOf(value);
		}
		
		override public function setData(arr:*):void 
		{
			super.setData(arr);
			selectedIndex = _selectedIndex;
		}
		
		override protected function titleClickHandler(e:MouseEvent):void 
		{
			super.titleClickHandler(e);
			
			for (var i:int = childList.length - 1; i >= 0; i--)
			{
				if (childList[i].getTitleBar() == e.target)
				{
					selectedIndex = i;
					dispatchEvent(new Event(Event.SELECT));
					break;
				}
			}
		}
		
		override public function clear():void 
		{
			_selectedIndex = 0;
			super.clear();
		}
		
		
		
	}

}