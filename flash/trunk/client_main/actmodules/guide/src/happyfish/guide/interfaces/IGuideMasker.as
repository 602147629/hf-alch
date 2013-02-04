package happyfish.guide.interfaces 
{
	import flash.display.DisplayObjectContainer;
	import flash.display.Stage;
	
	/**
	 * 指引的遮罩,同时也是显示对象
	 * @author lite3
	 */
	public interface IGuideMasker 
	{
		/**
		 * 显示一个矩形的遮罩,如果当前没有在显示列表,则添加到显示列表顶层
		 * @param	bringToFront 是否调整到顶层 default:false
		 */
		function show(bringToFront:Boolean = false):void;
		
		/**
		 * 显示并留空一个圆,如果当前没有在显示列表,则添加到显示列表顶层
		 * @param	px
		 * @param	py
		 * @param	radius
		 * @param	bringToFront 是否调整到顶层 default:false
		 */
		function showHasCircle(px:int, py:int, radius:int, bringToFront:Boolean = false):void;
		
		/**
		 * 将自己从显示列表里移除
		 */
		function remove():void;
		
		/** 设置是否可见 */
		function get visible():Boolean;
		/** 设置是否可见 */
		function set visible(value:Boolean):void;
		
		/**
		 * 设置Stage
		 * @param	stage
		 */
		function setStage(stage:Stage):void;
		
		/**
		 * 移动到顶层,如果没有在显示列表里,则添加到显示列表
		 */
		function bringToFront():void;
		
		/**
		 * 隐藏,但是依然在显示列表里,为了不更改显示列表的顺序
		 */
		function hide():void;
	}
	
}