package happymagic.scene.world.bigScene 
{
	import happyfish.scene.astar.Node;
	import happyfish.scene.world.grid.Person;
	import happyfish.scene.world.WorldState;
	import happyfish.utils.display.McShower;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.RoleVo;
	import happymagic.scene.world.control.AvatarCommand;
	import happymagic.scene.world.MagicWorld;
	/**
	 * ...
	 * @author 
	 */
	public class SceneRolesView extends Person 
	{
		private var rolevo:RoleVo;
		private var surpriseIcon:Mood_RedSurprise;
		
		public function SceneRolesView($data:Object, $worldState:WorldState,__callBack:Function=null) 
		{
			rolevo = $data as RoleVo;
			
			var tmpdata:Object = new Object();
			tmpdata.className = rolevo.className;
			tmpdata.faceClass = rolevo.faceClass;
			var tmpnode:Node = $worldState.getCustomRoomWalkAbleNode();
			tmpdata.x = tmpnode.x;
			tmpdata.z = tmpnode.y;
			tmpdata.name = rolevo.name;
			tmpdata.sex = rolevo.sex;
			tmpdata.profession = rolevo.profession;
			
			super(tmpdata, $worldState, __callBack);
			
			_speed = 2;
		}
		
		override protected function view_complete():void 
		{
			super.view_complete();
			
			fiddle();
		}
		
		override protected function fiddleWaitFun():void 
		{
			var command:AvatarCommand = new AvatarCommand();
			command.setAction("wait", 4000,0, fiddle);
			addCommand(command);
		}
		
		public function fight():void {
			stopMove();
			
			new McShower(Mood_RedSurprise, view.container);
		}
		
		public function closeFight():void {
			//if (surpriseIcon) 
			//{
				//view.container.removeChild(surpriseIcon);
				//surpriseIcon = null;
			//}
			
			fiddle();
		}
		
	}

}