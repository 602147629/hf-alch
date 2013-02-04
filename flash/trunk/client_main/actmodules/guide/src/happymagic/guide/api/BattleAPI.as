package happymagic.guide.api 
{
	import flash.events.Event;
	import flash.geom.Point;
	import happyfish.guide.GuideManager;
	import happyfish.manager.EventManager;
	import happymagic.scene.world.control.HandleBattleCommand;
	/**
	 * ...
	 * @author lite3
	 */
	public class BattleAPI 
	{
		
		private var guideId:String;
		private var stepId:String;
		
		public function get aktBtnPoint():Point
		{
			return new Point(104, 492);
		}
		
		public function get firstMobPoint():Point
		{
			return new Point(445, 274);
		}
		
		public function toPlayer(guideId:String, stepId:String):void
		{
			this.guideId = guideId;
			this.stepId = stepId;
			EventManager.addEventListener("battle_playerAction", handler);
		}
		
		public function end2(guideId:String, stepId:String):void
		{
			this.guideId = guideId;
			this.stepId = stepId;
			EventManager.addEventListener("battleFinish", handler);
		}
		
		private function handler(e:Event):void 
		{
			EventManager.removeEventListener("battleFinish", handler);
			EventManager.removeEventListener("battle_playerAction", handler);
			GuideManager.getInstance().gotoGuide(guideId);
			GuideManager.getInstance().gotoGuideStepById(stepId);
		}
		
	}

}