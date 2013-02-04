package happyfish.storyEdit.display 
{
	import fl.controls.listClasses.CellRenderer;
	import flash.display.Sprite;
	import flash.text.TextField;
	import happyfish.display.view.BitmapIconView;
	import happyfish.display.view.IconView;
	
	/**
	 * ...
	 * @author jj
	 */
	public class ActorRender extends CellRenderer 
	{
		
		public function ActorRender() 
		{
			//trace("ActorRender");
			//data = data.actor.className;
			
			
		}
		
		override protected function draw():void 
		{
			
			var icon:BitmapIconView = new BitmapIconView(30, 30);
			//icon.x = -15;
			//icon.y = -15;
			icon.setClass(data.actor.className);
			addChild(icon);
			
			//setSize( -1, 30);
			var txt:TextField = new TextField();
			txt.text = data.actor.name;
			addChild(txt);
			txt.x = 32;
			txt.cacheAsBitmap = true;
			super.draw();
		}
		
	}

}