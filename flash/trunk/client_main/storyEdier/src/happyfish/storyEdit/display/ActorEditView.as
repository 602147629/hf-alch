package happyfish.storyEdit.display 
{
	import flash.events.Event;
	import flash.events.MouseEvent;
	/**
	 * ...
	 * @author jj
	 */
	public class ActorEditView extends actorEditUi 
	{
		public var actorId:int;
		public var delFlag:Boolean;
		
		public function ActorEditView(actorId:int) 
		{
			this.actorId = actorId;
			addEventListener(MouseEvent.CLICK, clickFun);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
			case cancelBtn:
				dispatchEvent(new Event(Event.COMPLETE));
				closeMe();
				break;
				
			case delActorBtn:
				delFlag = true;
				dispatchEvent(new Event(Event.COMPLETE));
				closeMe();
				break;
			}
		}
		
		private function closeMe():void 
		{
			parent.removeChild(this);
		}
		
	}

}