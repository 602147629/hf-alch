package happyfish.guide.vo 
{
	/**
	 * ...
	 * @author lite3
	 */
	public class GuideVo
	{
		public var id:String
		public var name:String;
		
		/** 完成步骤前的步骤列表 */
		public var prevStepList:Vector.<GuideStepVo>;
		/** 步骤完成列表,这里只是一个步骤 */
		public var finishList:Vector.<GuideFinishVo>;
		/** 完成步骤后的步骤列表 */
		public var nextStepList:Vector.<GuideStepVo>;
		
		//public var stepLen:int;
		
		/** 是否有遮罩 */
		public var hasMasker:Boolean;
		
		/** 是否已经完成 */ 
		public var isFinish:Boolean;
		
		/** 是否完成OK */
		public var isFinishComplete:Boolean;
		
		public function GuideVo(id:String, name:String, hasMasker:Boolean, prevStepList:Vector.<GuideStepVo>, finishList:Vector.<GuideFinishVo>, nextStepList:Vector.<GuideStepVo>) 
		{
			this.id = id;
			this.name = name;
			this.hasMasker = hasMasker;
			this.prevStepList = prevStepList;
			this.finishList = finishList;
			this.nextStepList = nextStepList;
			
			//if (finishList != null)
			//{
				//stepLen = 1;
				//if (prevStepList) stepLen += prevStepList.length;
				//if (nextStepList) stepLen += nextStepList.length;
			//}
		}
		
		public function getStepStep(index:int):GuideStepVo
		{
			var list:Vector.<GuideStepVo> = isFinish ? nextStepList : prevStepList;
			if (list && index >= 0 && index < list.length)
			{
				return list[index];
			}
			return null;
		}
		
	}

}