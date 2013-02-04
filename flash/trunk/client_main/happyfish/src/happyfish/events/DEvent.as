package happyfish.events 
{
	import flash.events.Event;
	/**
	 * ...
	 * @author xjj
	 */
	public class DEvent extends Event
	{
		public var data:Object;
		
		public function DEvent(type:String, data:Object = null) 
		{
			super(type);
			this.data = data;
		}
		
	}

}