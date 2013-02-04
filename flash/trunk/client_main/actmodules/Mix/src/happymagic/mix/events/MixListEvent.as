package happymagic.mix.events 
{
	import flash.events.Event;
	import happymagic.model.vo.classVo.MixClassVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class MixListEvent extends Event 
	{
		
		public static const SHOW_MIX:String = "showMix";
		
		public var vo:MixClassVo;
		public function MixListEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false, vo:MixClassVo = null) 
		{ 
			super(type, bubbles, cancelable);
			this.vo = vo;
		} 
		
		public override function clone():Event 
		{ 
			return new MixListEvent(type, bubbles, cancelable, vo);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("MixListEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}