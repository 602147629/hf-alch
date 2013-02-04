package happyfish.guide.view 
{
	import flash.display.Sprite;
	import flash.display.Stage;
	import happyfish.guide.interfaces.IGuideMasker;
	
	/**
	 * 遮罩
	 * @author lite3
	 */
	public class GuideMasker extends Sprite implements IGuideMasker
	{
		
		private var _stage:Stage;
		
		public function GuideMasker() 
		{
			name = "masker";
			mouseChildren = false;
		}
		
		/**
		 * 显示一个矩形的遮罩,如果当前没有在显示列表,则添加到显示列表顶层
		 * @param	bringToFront 是否调整到顶层 default:false
		 */
		public function show(bringToFront:Boolean = false):void
		{
			graphics.clear();
			graphics.beginFill(0, 0.5);
			graphics.drawRect(0, 0, _stage.stageWidth, _stage.stageHeight);
			graphics.endFill();
			if(!parent || bringToFront) _stage.addChild(this);
		}
		
		/**
		 * 显示并留空一个圆,如果当前没有在显示列表,则添加到显示列表顶层
		 * @param	px
		 * @param	py
		 * @param	radius
		 * @param	bringToFront 是否调整到顶层 default:false
		 */
		public function showHasCircle(px:int, py:int, radius:int, bringToFront:Boolean = false):void
		{
			graphics.clear();
			graphics.beginFill(0, 0.5);
			graphics.drawRect(0, 0, _stage.stageWidth, _stage.stageHeight);
			graphics.drawCircle(px, py, radius);
			graphics.endFill();
			if(!parent || bringToFront) _stage.addChild(this);
		}
		
		/**
		 * 移动到顶层,如果没有在显示列表里,则添加到显示列表
		 */
		public function bringToFront():void
		{
			_stage.addChild(this);
		}
		
		/**
		 * 隐藏,但是依然在显示列表里,为了不更改显示列表的顺序
		 */
		public function hide():void
		{
			graphics.clear();
		}
		
		/**
		 * 将自己从显示列表里移除
		 */
		public function remove():void
		{
			if (parent)
			{
				parent.removeChild(this);
				graphics.clear();
			}
		}
		
		/**
		 * 设置Stage
		 * @param	stage
		 */
		public function setStage(stage:Stage):void
		{
			_stage = stage;
		}
	}
}