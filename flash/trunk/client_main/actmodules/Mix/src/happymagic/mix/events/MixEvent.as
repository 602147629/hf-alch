package happymagic.mix.events 
{
	import flash.events.Event;
	import happymagic.model.vo.MixVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class MixEvent extends Event 
	{
		// 合成一个item
		public static const MIX_ITEM:String = "mix_mixItem";
		// 打开一个炉子的合成面板
		public static const MIX_ITEMS:String = "mix_mixItems";
		
		
		// 合成术cid
		public var mid:int;
		public var furnaceId:String;
		
		public function MixEvent(type:String, bubbles:Boolean = false, cancelable:Boolean = false, mid:int = 0, furnaceId:String = null) 
		{
			super(type, bubbles, cancelable);
			this.mid = mid;
			this.furnaceId = furnaceId;
		}
		
		public override function clone():Event 
		{ 
			return new MixEvent(type, bubbles, cancelable, mid, furnaceId);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("MixEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}