package happyfish.display.ui 
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.MouseEvent;
	/**
	 * ...
	 * @author lite3
	 */
	public class RadioButton extends EventDispatcher
	{
		/** Child在Accordion的索引, 由Accordion修改 */
		public var index:int;
		
		public var view:MovieClip;
		
		protected var _selected:Boolean;
		
		public function RadioButton(view:MovieClip) 
		{
			this.view = view;
			selected = false;
			view.addEventListener(MouseEvent.CLICK, clickHandler);
		}
		
		private function clickHandler(e:MouseEvent):void 
		{
			if (!_selected) selected = true;
		}
		
		public function get selected():Boolean { return _selected; }
		public function set selected(value:Boolean):void 
		{
			_selected = value;
			view.gotoAndStop(value ? "Select" : "Unselect");
			dispatchEvent(new Event(Event.CHANGE));
		}
		
	}

}