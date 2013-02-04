package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.EventManager;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.display.control.StoryPlayCommand;
	import happymagic.events.SceneEvent;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.scene.world.control.ChangeSceneCommand;
	/**
	 * ...
	 * @author jj
	 */
	public class MoveSceneCommand extends BaseDataCommand
	{
		private var sceneId:uint;
		private var portalId:int;
		private var storyId:int;
		private var fid:String;
		
		private var maskType:int;
		
		public function moveScene(sceneId:uint, portalId:int = 0, storyId:int = 0, maskType:int = 1,fid:String="0"):void
		{
			this.fid = fid;
			this.sceneId = sceneId;
			this.portalId = portalId;
			this.storyId = storyId;
			
			this.maskType = maskType;
			
			if (sceneId == 1) goHome();//家
			else getClassInfo();
		}
		
		private function getClassInfo():void
		{
			if (DataManager.getInstance().getSceneClassById(sceneId) == null)
			{
				var getSceneClassCommand:GetSceneClassCommand = new GetSceneClassCommand;
				getSceneClassCommand.addEventListener(Event.COMPLETE, go);
				getSceneClassCommand.getSceneClass(sceneId);
			}
			else go();
		}
		
		private function go(event:Event = null):void
		{
			if (event) event.target.removeEventListener(Event.COMPLETE, go);
			
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("GoScene"), { sceneId:sceneId , portalId:portalId, storyId:storyId,fid:fid } );
			
			loader.load(request);
		}
		
		private function goHome():void
		{
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("GoHome"),{fid:fid});
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			playStory = false;
			
			super.load_complete(e);
			
			if(maskType != 0) DisplayManager.uiSprite.showSceneOutMv(); //拉幕布
			
			DataManager.getInstance().orderData.setFriendOrder(objdata.friendOrder);
			
			new ChangeSceneCommand();
			EventManager.getInstance().addEventListener(SceneEvent.SCENE_COMPLETE, StoryPlayCommand.getInstance().checkAndPlay);
			
			commandComplete();
		}
	}

}