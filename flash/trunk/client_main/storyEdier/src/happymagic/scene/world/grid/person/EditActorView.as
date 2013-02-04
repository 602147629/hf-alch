package happymagic.scene.world.grid.person 
{
	import flash.filters.GlowFilter;
	import flash.geom.Rectangle;
	import flash.text.TextField;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.WorldState;
	/**
	 * ...
	 * @author xjj
	 */
	public class EditActorView extends Person
	{
		private var info:TextField;
		
		public function EditActorView($data:Object, $worldState:WorldState,__callBack:Function=null) 
		{
			super($data, $worldState, __callBack);
			typeName = "EditActor";
		}
		
		public function setInfo(msg:String):void
		{
			removeInfo();
			
			if (!info){
				info = new TextField;
				info.text = msg;
				info.textColor = 0xFFFFFF;
				info.filters = [new GlowFilter(0xFF6600,1,4,4,10)];
			}
			var rect:Rectangle = view.container.getBounds(view.container);
			info.y = rect.top - info.height;
			view.container.addChild(info);
		}
		
		public function removeInfo():void
		{
			if (info && info.parent) info.parent.removeChild(info);
		}
		
	}

}