package happyfish.guide.vo 
{
	import flash.display.DisplayObject;
	import flash.display.DisplayObjectContainer;
	import flash.events.MouseEvent;
	import happyfish.guide.utils.AfterCall;
	/**
	 * ...
	 * @author lite3
	 */
	public class MouseEventVo 
	{
		/** 鼠标的type */
		public var type:String;
		/** 点击的目标或者目标的容器 */
		public var target:Function;
		/** 点击后执行的代码 */
		public var handler:Function;
		
		public function MouseEventVo(type:String, target:Function, handler:Function) 
		{
			this.target = target;
			this.handler = handler;
			this.type = type;
		}
		
		public function doClick(e:MouseEvent):Boolean
		{
			if (e.type != type) return false;
			try {
				var _target:DisplayObject = target();
			}catch (err:Error) { showErr(err); }
			
			var success:Boolean = _target == e.target;
			if (!success && _target is DisplayObjectContainer)
			{
				success = DisplayObjectContainer(_target).contains(e.target as DisplayObject);
			}
			
			if (success && handler != null)
			{
				AfterCall.call(handler);
			}
			
			return success;
		}
		
	}

}