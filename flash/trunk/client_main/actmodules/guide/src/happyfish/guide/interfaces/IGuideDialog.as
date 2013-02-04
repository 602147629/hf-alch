package happyfish.guide.interfaces 
{
	import flash.display.DisplayObjectContainer;
	import flash.events.IEventDispatcher;
	import happyfish.guide.vo.DialogVo;
	
	/**
	 * 对话框的接口,对话框同时也是一个显示对象
	 * @author lite3
	 */
	public interface IGuideDialog extends IEventDispatcher
	{
		/**
		 * 显示对话框
		 * @param	list
		 * @param	container
		 */
		function showDialog(list:Vector.<DialogVo>, container:DisplayObjectContainer):void;
		
		/**
		 * 当对话结束后的回调函数
		 * @param	callback function (vo:DialogVo):void;
		 */
		function setEndCallback(callback:Function):void;
		
		/**
		 * 清除
		 */
		function clear():void;
	}
	
}