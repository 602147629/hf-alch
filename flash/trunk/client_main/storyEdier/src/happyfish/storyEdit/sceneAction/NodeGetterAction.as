package happyfish.storyEdit.sceneAction 
{
	import com.friendsofed.isometric.Point3D;
	import flash.events.Event;
	import flash.events.MouseEvent;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.EventManager;
	import happyfish.scene.world.WorldState;
	import happyfish.storyEdit.events.StoryEditEvent;
	import happyfish.storyEdit.model.EditDataManager;
	import happymagic.scene.world.control.MouseDefaultAction;
	import happymagic.scene.world.control.MouseMagicAction;
	import happymagic.scene.world.MagicWorld;
	
	/**
	 * ...
	 * @author jj
	 */
	public class NodeGetterAction extends MouseMagicAction
	{
		
		public function NodeGetterAction($state:WorldState) 
		{
			super($state);
		}
		
		override public function onMouseMove(event:MouseEvent):void 
		{
			var p:Point3D = worldPosition();
			
			(state.world as MagicWorld).setPlayerFlag(p);
		}
		
		override public function onBackgroundClick(event:GameMouseEvent):void 
		{
			var e:StoryEditEvent = new StoryEditEvent(StoryEditEvent.GET_NODE);
			e.node = worldPosition();
			EventManager.getInstance().dispatchEvent(e);
			
			remove(false);
			
			
		}
		
	}

}