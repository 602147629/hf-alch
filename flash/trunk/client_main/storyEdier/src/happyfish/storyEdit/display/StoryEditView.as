package happyfish.storyEdit.display 
{
	import com.adobe.serialization.json.JSON2;
	import fl.controls.ScrollPolicy;
	import fl.data.DataProvider;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.storyEdit.model.vo.EditStoryVo;
	/**
	 * ...
	 * @author jj
	 */
	public class StoryEditView extends storyCreateUi 
	{
		public var saved:Boolean;
		public var story:Object;
		
		public function StoryEditView() 
		{
			addEventListener(MouseEvent.CLICK, clickFun);
			
			//grid.verticalScrollPolicy = ScrollPolicy.ON;
			grid.horizontalScrollPolicy = ScrollPolicy.ON;
			
			grid.editable = true;
			
			grid.columns = ["name", "sceneId", "endAt", "items", "coin", "gem", "nextStoryId"
			,"taskId","unlockSceneId"
			];
		}
		
		public function setData(value:EditStoryVo):void {
			story = JSON2.decode(JSON2.encode(value));
			
			grid.dataProvider = new DataProvider([story]);
		}
		
		private function clickFun(e:MouseEvent):void 
		{
			switch (e.target) 
			{
			case cancelBtn:
				closeMe();
				break;
				
				
			case createBtn:
				createStory();
				closeMe();
				break;
			}
		}
		
		private function createStory():void 
		{
			saved = true;
			dispatchEvent(new Event(Event.COMPLETE));
		}
		
		private function closeMe():void 
		{
			parent.removeChild(this);
		}
		
	}

}