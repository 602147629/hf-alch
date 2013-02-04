package happymagic.diy.control 
{
	import flash.events.Event;
	import happyfish.events.GameMouseEvent;
	import happyfish.scene.world.WorldState;
	import happymagic.diy.model.SaveDiyMoveCommand;
	import happymagic.manager.DataManager;
	import happymagic.scene.world.grid.item.Door;
	/**
	 * ...
	 * @author 
	 */
	public class MouseRotationAction extends MouseEditAction
	{
		
		public function MouseRotationAction($state:WorldState) 
		{
			super($state);
		}
		
		override public function onBackgroundClick(event:GameMouseEvent):void 
		{
			if (!item) 
			{
				return;
			}
			
			if (item is Door) 
			{
				return;
			}
			
			//旋转物件item
			state.world.removeToGrid(item);
			if (item.mirror==0) 
			{
				item.mirror = 1;
				item.setMirro(item.mirror);
				item.rorate(true);
			}else {
				item.mirror = 0;
				item.setMirro(item.mirror);
				item.rorate(true);
			}
			state.world.addToGrid(item);
			
			//请示移除物件
			requestRotation();
			
			item.rorate();
			item = null;
		}
		
		private function requestRotation():void 
		{
			var command:SaveDiyMoveCommand = new SaveDiyMoveCommand();
			command.addEventListener(Event.COMPLETE, requestRotation_complete);
			command.save(item.data.id,item.x,item.z,item.mirror);
		}
		
		private function requestRotation_complete(e:Event):void 
		{
			e.target.removeEventListener(Event.COMPLETE, requestRotation_complete);
			
			
		}
		
	}

}