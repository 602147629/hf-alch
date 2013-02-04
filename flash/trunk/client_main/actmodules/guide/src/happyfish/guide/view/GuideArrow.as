package happyfish.guide.view 
{
	import flash.display.DisplayObjectContainer;
	import happyfish.guide.interfaces.IGuideArrow;
	
	/**
	 * 指引用的箭头
	 * @author lite3
	 */
	public class GuideArrow extends GuideArrowUI implements IGuideArrow
	{
		
		public function GuideArrow() 
		{
			mouseEnabled = false;
			mouseChildren = false;
			stop();
		}
		
		/**
		 * 在某点显示
		 * @param	x 在container中的x坐标
		 * @param	y 在container中的y坐标
		 * @param	container 要显示到的容器
		 */
		public function showAt(x:int, y:int, container:DisplayObjectContainer):void
		{
			this.x = x;
			this.y = y;
			if(parent != container) container.addChild(this);
			play();
		}
		
		/**
		 * 从显示列表中移除自己
		 */
		public function remove():void
		{
			if (parent)
			{
				parent.removeChild(this);
				stop();
			}
		}
		
	}

}