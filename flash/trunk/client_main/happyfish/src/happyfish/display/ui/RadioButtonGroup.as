package happyfish.display.ui 
{
	import flash.events.Event;
	import flash.events.EventDispatcher;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class RadioButtonGroup extends EventDispatcher 
	{
		
		private var _selectedIndex:int = -1;
		private const list:Vector.<RadioButton> = new Vector.<RadioButton>();
		
		/**
		 * 
		 * @param	...rect 初始添加的radioButton
		 */
		public function RadioButtonGroup(...rect) 
		{
			var len:int = rect.length;
			for (var i:int = 0; i < len; i++)
			{
				if (rect[i] is RadioButton) addButton(rect[i]);
			}
		}
		
		public function get selectedIndex():int { return _selectedIndex; }
		public function set selectedIndex(value:int):void 
		{
			if (value < -1) value = -1;
			if (value >= list.length) value = list.length - 1;
			if (_selectedIndex == value) return;
			
			var btn:RadioButton = selection;
			if (btn) btn.selected = false;
			_selectedIndex = value;
			btn = selection;
			if (btn) btn.selected = true;
			dispatchEvent(new Event(Event.CHANGE));
		}
		
		public function get selection():RadioButton { return _selectedIndex != -1 ? list[_selectedIndex] : null; }
		public function set selection(value:RadioButton):void 
		{
			selectedIndex = list.indexOf(value);
		}
		
		public function addButton(btn:RadioButton):void
		{
			if (list.indexOf(btn) != -1) return;
			
			if (_selectedIndex != -1 && btn.selected) btn.selected = false;
			list.push(btn);
			if ( -1 == _selectedIndex && btn.selected) _selectedIndex = list.length - 1;
			
			btn.addEventListener(Event.CHANGE, btnChangeHandler);
		}
		
		private function btnChangeHandler(e:Event):void 
		{
			var btn:RadioButton = e.currentTarget as RadioButton;
			var idx:int = list.indexOf(btn);
			if (-1 == idx) return;
			
			if (btn.selected)
			{
				if (_selectedIndex == idx) return;
				
				if (selection) selection.selected = false;
				selectedIndex = idx;
			}else
			{
				if(selectedIndex == idx) selectedIndex = -1;
			}
		}
		
	}

}