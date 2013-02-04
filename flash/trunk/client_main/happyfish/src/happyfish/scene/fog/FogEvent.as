package happyfish.scene.fog
{
	import flash.events.Event;

	public class FogEvent extends Event
	{
		public var list:Vector.<FogNode>;
		public var immediately:Boolean;
		
		public function FogEvent(list:Vector.<FogNode>,type:String = FOG_CHANGE)
		{
			this.list = list;
			
			super(type);
		}
		
		public static const FOG_CHANGE:String = "fogChange";
		public static const FOG_SAVE:String = "fogSave";
	}
}