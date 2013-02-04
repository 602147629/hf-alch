package happyfish.display.ui 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.Sprite;
	import happyfish.display.ui.events.GridPageEvent;
	/**
	 * ...
	 * @author lite3
	 */
	public class SelectionGridPage extends GridPage 
	{
		private var _selectedIndex:int = -1;
		
		public function SelectionGridPage(ui:Sprite, _container:DisplayObjectContainer, _hideButton:Boolean = true)
		{
			super(ui, _container, _hideButton);
		}
		
		public function get selectedIndex():int { return _selectedIndex; }
		public function set selectedIndex(value:int):void
		{
			if (value < -1 || value >= data.length) value = -1;
			if (_selectedIndex != value)
			{
				var item:GridItem = selectedItem;
				if (item) item.selected = false;
				_selectedIndex = value;
				item = selectedItem;
				if (item) item.selected = true;
			}
		}
		
		public function get selectedValue():Object { return data[_selectedIndex]; }
		public function set selectedValue(value:Object):void 
		{
			selectedIndex = data.indexOf(value);
		}
		
		public function get selectedItem():GridItem  { return grid.data[_selectedIndex - (currentPage-1) * pageLength]; }
		public function set selectedItem(value:GridItem):void
		{
			var idx:int = grid.data.indexOf(value);
			if (idx != -1) idx += (currentPage-1) * pageLength;
			selectedIndex = idx;
		}
		
		override public function setData(value:*, key:String = "", keyV:* = null, saveCurrentPage:Boolean = false):void 
		{
			_selectedIndex = -1;
			super.setData(value, key, keyV, saveCurrentPage);
		}
		
		override public function initPage():void 
		{
			super.initPage();
			var item:GridItem = selectedItem;
			if (item) item.selected = true;
		}
		
		override protected function itemSelectFun(e:GridPageEvent):void 
		{
			var currItem:GridItem = selectedItem;
			if (currItem != e.item)
			{
				if (currItem) currItem.selected = false;
				e.item.selected = true;
				_selectedIndex = grid.data.indexOf(e.item);
				_selectedIndex += pageLength * (currentPage-1);
			}
			super.itemSelectFun(e);
		}
		
		override public function clear():void 
		{
			_selectedIndex = -1;
			super.clear();
		}
		
	}

}