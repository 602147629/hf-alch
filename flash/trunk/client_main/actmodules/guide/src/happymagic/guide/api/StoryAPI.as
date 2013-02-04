package happymagic.guide.api 
{
	import flash.events.Event;
	import happyfish.guide.GuideManager;
	import happyfish.manager.EventManager;
	import happymagic.display.control.StoryPlayCommand;
	/**
	 * ...
	 * @author lite3
	 */
	public class StoryAPI 
	{
		private var story:StoryPlayCommand;
		
		private var storyEndMap:Object = { };
		private var storyEndMapLen:int = 0;
		
		private var guideId:String;
		private var stepId:String;
		
		public function npcChatEnd2(guideId:String, stepId:String):void
		{
			this.guideId = guideId;
			this.stepId = stepId;
			EventManager.addEventListener("npcChatComplete", npcChangeEndHandler);
		}
		
		private function npcChangeEndHandler(e:Event):void 
		{
			EventManager.removeEventListener("npcChatComplete", npcChangeEndHandler);
			GuideManager.getInstance().gotoGuide(guideId);
			GuideManager.getInstance().gotoGuideStepById(stepId);
		}
		
		public function end2(id:int, guideId:String, stepId:String):void
		{
			this.guideId = guideId;
			this.stepId = stepId;
			//if (!storyEndMap[id]) storyEndMapLen++;
			//storyEndMap[id] = { guideId:guideId, stepId:stepId };
			//storyEndMapLen++;
			EventManager.addEventListener("storyEnd", storyEndFunction);
		}
		
		private function storyEndFunction(e:Event):void 
		{
			//var id:int = e["data"];
			//var o:Object = storyEndMap[id];
			
			//if (o)
			{
				//delete storyEndMap[id];
				//storyEndMapLen--;
				if (0 == storyEndMapLen) EventManager.removeEventListener("storyEnd", storyEndFunction);
				
				GuideManager.getInstance().gotoGuide(guideId);
				GuideManager.getInstance().gotoGuideStepById(stepId);
			}
		}
		
		public function get has():Boolean {
			return StoryPlayCommand.getInstance().story != null;
		}
		
	}

}