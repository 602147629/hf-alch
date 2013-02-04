package happyfish.guide.events 
{
	import flash.events.Event;
	import happyfish.guide.vo.GuideVo;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class GuideEvent extends Event 
	{
		
		/** 向导开始 */
		public static const STEP_BEFORE:String = "guide_stepBefor";
		
		/** 步骤开始 */
		public static const STEP_START:String = "guide_stepStart";
		
		/** 步骤开始 */
		public static const STEP_REMOVE:String = "guide_stepRemove";
		
		/** 重置步骤 */
		public static const STEP_RESET:String = "guide_stepReset";
		
		/** 完成一小步,触发了完成事件,但这一小步依然需要后续步骤 */
		public static const STEP_FINISH:String = "guide_stepFinish";
		
		/** 完成一小步, 触发完成事件,并且也完成了后续步骤 */
		public static const STEP_ALL_FINISH:String = "guide_stepAllFinish";
		
		/** 完成所有步 */
		public static const ALL_STEP_FINISH:String = "guide_allStepFinish";
		
		/**
		 * 已完成的stepIndex
		 */
		public var stepIndex:int;
		/**
		 * 已完成的guide
		 */
		public var guide:GuideVo;
		
		
		public function GuideEvent(type:String, bubbles:Boolean=false, cancelable:Boolean=false, stepIndex:int = 0, guide:GuideVo = null) 
		{ 
			super(type, bubbles, cancelable);
			this.stepIndex = stepIndex;
			this.guide = guide;
		} 
		
		public override function clone():Event 
		{ 
			return new GuideEvent(type, bubbles, cancelable, stepIndex, guide);
		} 
		
		public override function toString():String 
		{ 
			return formatToString("GuideEvent", "type", "bubbles", "cancelable", "eventPhase"); 
		}
		
	}
	
}