package happyfish.display.ui 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import xrope.HLineLayout;
	import xrope.LayoutAlign;
	import xrope.VLineLayout;
	/**
	 * 点页码组件
	 * @author lite3
	 */
	public class Pagination extends Sprite
	{
		private var _x:Number;
		private var _y:Number;
		
		public var gap:Number=3;//显示个数
		
		private var _curIndex:int;
		private var _maxValue:int;
		
		//单元素材类
		private var freeItemClass:Class = paginationFreeItemUi
		private var selectItemClass:Class = paginationSelectItemUi;
		private var btwX:Number;
		
		private var maxW:Number;
		private var layouter:HLineLayout;
		
		
		public function Pagination(maxW:Number = 100, gap:Number=3 ) 
		{
			layouter = new HLineLayout(this, -maxW/2, -20, maxW, 40, LayoutAlign.CENTER, gap,true);
			this.gap = gap;
			this.maxW = maxW;
			//btwX = (maxW) / gap;
		}
		
		public function setData(_curIndex:int,_maxValue:int=0):void {
			this._curIndex = Math.max(0,_curIndex);
			if (_maxValue > 0) {
				this._maxValue = _maxValue;
			}else {
				_maxValue = 0;
			}
			
			initPage();
		}
		
		public function get curIndex():int {
			return _curIndex;
		}
		
		public function get maxValue():int 
		{
			return _maxValue;
		}
		
		private function initPage():void {
			//trace(_curIndex);
			layouter.removeAll();
			while (numChildren>0) 
			{
				removeChildAt(0);
			}
			if (_maxValue<=0) 
			{
				return;
			}
			
			var halfNum:int = Math.floor(gap / 2);
			
			var startIndex:int = _curIndex - halfNum;
			startIndex = Math.max(startIndex, 0);
			var endIndex:int = startIndex + gap;
			endIndex = Math.min(endIndex, _maxValue+1);
			
			var num:int = endIndex - startIndex;
			startIndex += num - gap;
			startIndex = Math.max(startIndex, 0);
			
			var tmp:MovieClip;
			var curI:int = 0;
			
			for (var i:int = startIndex; i < endIndex; i++) 
			{
				if (i > _maxValue || curI>=gap) 
				{
					break;
				}
				if (i==_curIndex) 
				{
					tmp = new selectItemClass() as MovieClip;
				}else {
					
					tmp = new freeItemClass() as MovieClip;
				}
				//tmp.x = -maxW / 2 + btwX * curI + btwX / 2;
				layouter.add(tmp);
				//curI++;
			}
			layouter.layout();
		}
		
		public function nextPage():void {
			_curIndex++;
			_curIndex = Math.min(_curIndex, _maxValue);
			
			initPage();
		}
		
		public function prevPage():void {
			_curIndex--;
			_curIndex = Math.max(_curIndex, 0);
			initPage();
		}
		
	}

}