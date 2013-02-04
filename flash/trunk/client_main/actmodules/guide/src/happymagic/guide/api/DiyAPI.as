package happymagic.guide.api 
{
	import flash.events.Event;
	import happyfish.guide.GuideManager;
	import happyfish.manager.EventManager;
	/**
	 * ...
	 * @author lite3
	 */
	public class DiyAPI 
	{
		private var put2guideId:String;
		private var put2stepId:String;
		private var putCid:int;
		private var cancel2guideId:String;
		private var cancel2stepId:String;
		
		public function removePutEvent():void
		{
			EventManager.removeEventListener("diyPutItem", putHandler);
		}
		
		public function removeCancelPutEvent():void
		{
			EventManager.removeEventListener("diyCancelPutItem", cancelPutHandler);
		}
		
		public function put2(guideId:String, stepId:String, cid:int = 0):void
		{
			this.put2guideId = guideId;
			this.put2stepId = stepId;
			this.putCid = putCid;
			EventManager.addEventListener("diyPutItem", putHandler);
		}
		
		public function cancelPut2(guideId:String, stepId:String):void
		{
			this.cancel2guideId = guideId;
			this.cancel2stepId = stepId;
			EventManager.addEventListener("diyCancelPutItem", cancelPutHandler);
		}
		
		private function putHandler(e:Event):void 
		{
			if (0 == putCid || putCid == e["data"].cid)
			{
				EventManager.removeEventListener("diyPutItem", putHandler);
				GuideManager.getInstance().gotoGuide(put2guideId);
				GuideManager.getInstance().gotoGuideStepById(put2stepId);
			}
		}
		
		private function cancelPutHandler(e:Event):void 
		{
			EventManager.removeEventListener("diyCancelPutItem", cancelPutHandler);
			GuideManager.getInstance().gotoGuide(cancel2guideId);
			GuideManager.getInstance().gotoGuideStepById(cancel2stepId);
		}
	}

}