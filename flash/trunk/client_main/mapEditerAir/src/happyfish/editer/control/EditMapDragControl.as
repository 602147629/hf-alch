package happyfish.editer.control 
{
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import happyfish.editer.view.Main;
	/**
	 * ...
	 * @author slamjj
	 */
	public class EditMapDragControl extends EditerControl 
	{
		
		public function EditMapDragControl(_main:Main,mapview:Sprite) 
		{
			super(_main, mapview);
		}
		
		override protected function beginDrag(e:MouseEvent):void 
		{
			super.beginDrag(e);
			
			main.mapSprite.startDrag();
		}
		
		override protected function upDrag(e:MouseEvent):void 
		{
			super.upDrag(e);
			
			main.mapSprite.stopDrag();
		}
		
	}

}