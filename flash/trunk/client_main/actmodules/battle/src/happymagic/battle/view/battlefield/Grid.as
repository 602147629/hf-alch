package happymagic.battle.view.battlefield 
{
	import flash.geom.Point;
	import happymagic.battle.view.GridUI;
	/**
	 * 地块
	 * @author XiaJunJie
	 */
	public class Grid extends GridUI
	{
		public var index:int = -1;
		
		private var _selectable:Boolean;
		
		public function Grid()
		{
			gotoAndStop(1);
		}
		
		public function set selectable(value:Boolean):void
		{
			_selectable = value;
			if (_selectable) gotoAndStop(BLUE);
			else gotoAndStop(NOCOLOR);
		}
		
		public function get selectable():Boolean
		{
			return _selectable;
		}
		
		public function set selected(value:Boolean):void
		{
			if (value) gotoAndStop(RED);
			else selectable = _selectable;
		}
		
		/**
		 * 碰撞测试
		 * @param	point
		 * @return
		 */
		public function hitTest(point:Point):Boolean
		{
			if (!stage) return false;
			return hitTestPoint(point.x, point.y,true);
		}
		
		private static var NOCOLOR:int = 1;
		private static var RED:int = 2;
		private static var BLUE:int = 3;
	}

}