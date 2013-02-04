package happyfish.guide.interfaces 
{
	import flash.display.DisplayObjectContainer;
	
	/**
	 * 指引的箭头
	 * @author lite3
	 */
	public interface IGuideArrow 
	{
		/**
		 * 在某点显示
		 * @param	x 在container中的x坐标
		 * @param	y 在container中的y坐标
		 * @param	container 要显示到的容器
		 */
		function showAt(x:int, y:int, container:DisplayObjectContainer):void;
		
		/**
		 * 从显示列表中移除自己
		 */
		function remove():void;
	}
	
}