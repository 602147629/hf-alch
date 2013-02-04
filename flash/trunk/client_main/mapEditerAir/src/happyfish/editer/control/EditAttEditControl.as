package happyfish.editer.control 
{
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import happyfish.editer.scene.view.EditIsoItem;
	import happyfish.editer.view.Main;
	/**
	 * ...
	 * @author ...
	 */
	public class EditAttEditControl extends EditerControl 
	{
		
		public function EditAttEditControl(_main:Main,mapview:Sprite) 
		{
			super(_main, mapview);
		}
		
		override protected function beginDrag(e:MouseEvent):void 
		{
			super.beginDrag(e);
			
			var item:EditIsoItem = main.mapSprite.itemContainer.getItemByPos(pos.x, pos.z);
			if (item) 
			{
				main.editer.showDataInfo(item);
			}
		}
		
	}

}