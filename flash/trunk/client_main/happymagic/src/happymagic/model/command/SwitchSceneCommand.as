package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.scene.world.control.ChangeSceneCommand;
	import happymagic.manager.DataManager;
	/**
	 * 切换场景Command 2011.11.11
	 * @author XiaJunJie
	 */
	public class SwitchSceneCommand extends BaseDataCommand
	{
		
		public function go(sceneId:int,portalId:int=0):void 
		{
			DataManager.getInstance().lastSceneId = DataManager.getInstance().currentUser.currentSceneId;
			
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("switchScene"), { sceneId:sceneId, portalId:portalId } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void
		{
			super.load_complete(e);
			
			new ChangeSceneCommand();
			
			commandComplete();
		}
		
	}

}