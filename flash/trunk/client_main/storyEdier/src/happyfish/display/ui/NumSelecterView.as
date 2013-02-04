package happyfish.display.ui 
{
	import flash.display.MovieClip;
	import flash.display.SimpleButton;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.MouseEvent;
	import flash.text.TextField;
	import flash.utils.clearTimeout;
	import flash.utils.setTimeout;
	import happyfish.utils.display.FiltersDomain;
	
	/**
	 * 数量显示组件
	 * 可 增加\减少\输入 数量
	 * @author jj
	 */
	public class NumSelecterView extends EventDispatcher
	{
	
		private var _num:uint;
		private var _minNum:uint;
		private var _maxNum:uint;
		private var _snapInterval:int = 1;
		private var checkTimeId:uint;
		
		public var view:MovieClip;
		public var checkDelay:uint;
		private var numTxt:TextField;
		private var addButton:SimpleButton;
		private var subButton:SimpleButton;
		
		public function NumSelecterView(__uiview:MovieClip,startNum:uint=1,maxLength:int=5) 
		{
			view = __uiview;
			if (maxLength <= 0) maxLength = 5;
			view.addEventListener(MouseEvent.CLICK, clickFun, true);
			
			numTxt = view.numTxt;
			addButton = view.addButton;
			subButton = view.subButton;
			
			_minNum = 1;
			_maxNum = uint.MAX_VALUE;
			_num = uint.MAX_VALUE == startNum ? 0 : uint.MAX_VALUE;
			
			numTxt.restrict = "0-9";
			numTxt.maxChars = maxLength;
			numTxt.addEventListener(Event.CHANGE, textInput);
			
			setNum(startNum);
		}
		
		private function textInput(e:Event):void 
		{
			if (checkTimeId) 
			{
				clearTimeout(checkTimeId);
				checkTimeId = 0;
			}
			checkTimeId = setTimeout(setNum, checkDelay, parseInt(numTxt.text));
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
				case addButton:
					setNum(num + snapInterval);
				break;
				
				case subButton:
					setNum(num - snapInterval);
				break;
			}
		}
		
		public function setNumLength(value:uint):void {
			numTxt.maxChars = value;
		}
		
		public function get num():uint { return _num; }
		public function setNum(value:uint):void
		{
			// 如果max不是snapInterval的倍数,将不能到达max,所以当value==max时忽略倍数
			value = value >= _maxNum ? _maxNum : value - ((value-_minNum) % snapInterval);
			if (value < _minNum) value = _minNum;
			
			var enabled:Boolean = value < _maxNum;
			if (addButton.mouseEnabled != enabled)
			{
				addButton.mouseEnabled = enabled;
				addButton.filters = enabled ? [] : [FiltersDomain.grayFilter];
			}
			enabled = value > _minNum;
			if (subButton.mouseEnabled != enabled)
			{
				subButton.mouseEnabled = enabled;
				subButton.filters = enabled ? [] : [FiltersDomain.grayFilter];
			}
			
			var change:Boolean = value != _num;
			_num = value;
			numTxt.text = value + "";
			if (change)
			{
				dispatchEvent(new Event(Event.CHANGE));
			}
		}
		
		public function get minNum():uint { return _minNum; }
		
		public function set minNum(value:uint):void 
		{
			_minNum = value;
			setNum(_num);
		}
		
		public function get maxNum():uint { return _maxNum; }
		
		public function set maxNum(value:uint):void 
		{
			_maxNum = value;
			setNum(_num);
		}
		
		public function get snapInterval():int { return _snapInterval; }
		public function set snapInterval(value:int):void
		{
			_snapInterval = value < 1 ? 1 : value;
		}
		
		public function get x():Number { return view.x; }
		
		public function set x(value:Number):void 
		{
			view.x = value;
		}
		
		public function get y():Number { return view.y; }
		
		public function set y(value:Number):void 
		{
			view.y = value;
		}
		
		public function get text():String { return numTxt.text; }
		
		public function set text(value:String):void 
		{
			numTxt.text = value;
		}
		
		
	}

}