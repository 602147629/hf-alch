package happyfish.display.ui 
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import happyfish.display.ui.events.GridPageEvent;
	/**
	 * 表格列表item项基类
	 * @author jj
	 */
	public class GridItem
	{
		public var view:MovieClip;
		public var index:uint;
		private var _selected:Boolean;
		
		public function GridItem(uiview:MovieClip) 
		{
			view = uiview;
			view.control = this;
			view.mouseChildren = false;
			view.buttonMode = true;
			view.stop();
			view.addEventListener(MouseEvent.CLICK, itemSelectFun);
			selected = false;
		}
		
		protected function itemSelectFun(e:MouseEvent):void 
		{
			if (!view.parent) return;
			var event:GridPageEvent = new GridPageEvent(GridPageEvent.ITEM_SELECT, true);
			event.item = this;
			view.parent.dispatchEvent(event);
		}
		
		public function setData(value:Object):void {
			throw("need override");
		}
		
		public function add(value:Object):void
		{
			
		}
		
		public function delNum(num:uint):Array
		{
			return null;
		}
		
		public function get selected():Boolean 
		{
			return _selected;
		}
		
		public function set selected(value:Boolean):void 
		{
			_selected = value;
			view.gotoAndStop(value ? "Select" : "Unselect");
		}
		
	}

}