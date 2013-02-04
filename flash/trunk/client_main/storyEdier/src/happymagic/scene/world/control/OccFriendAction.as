package happymagic.scene.world.control 
{
	import flash.utils.setTimeout;
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.scene.world.WorldState;
	import happyfish.time.Time;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.model.vo.GameSettingVo;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.bigScene.SceneRolesView;
	import happymagic.scene.world.FriendActionType;
	import happymagic.scene.world.MagicWorld;
	/**
	 * 侵占action
	 * @author 
	 */
	public class OccFriendAction extends MouseDefaultAction 
	{
		private var buildId:int;
		
		public function OccFriendAction($state:WorldState, $stack_flg:Boolean = false) 
		{
			super($state, false);
		}
		
		override public function onNpcOver(e:GameMouseEvent):void 
		{
			
		}
		
		override public function onNpcOut(e:GameMouseEvent):void 
		{
			
		}
		
		override public function onNpcClick(e:GameMouseEvent):void 
		{
			skipBackgroundClick = true;
		}
		
		override public function onSpecialBuildClick(event:GameMouseEvent):void {
			skipBackgroundClick = true;
			
			var curSceneUser:UserVo = DataManager.getInstance().curSceneUser;
			var curUser:UserVo = DataManager.getInstance().currentUser;
			var safeTime:int = Time.getRemainingTimeByEnd(curSceneUser.safeTime);
			var atkSafeTime:int = Time.getRemainingTimeByEnd(curSceneUser.atkSafeTime);
			if (atkSafeTime==0 && safeTime==0 && curSceneUser.ownerUid==0) 
			{
				var price:Array = DataManager.getInstance().gameSetting.occPrice;
				if (curUser.coin<price[0] || curUser.sp<price[1]) 
				{
					DisplayManager.showSysMsg("入侵需要" + price[0] + "金币," + price[1] + "点体力");
					return;
				}
				if (DataManager.getInstance().roleData.getCanPvPRoles().length==0) 
				{
					DisplayManager.showSysMsg("您没有足够的佣兵了");
					return;
				}
				
				DisplayManager.stageMouseEnabled = false;
				
				buildId = event.item.data.id;
				
				var items:Array = (state.world as MagicWorld).items;
				var tmpRole:SceneRolesView;
				var roleNum:int=0;
				for (var i:int = 0; i < items.length; i++) 
				{
					tmpRole = items[i] as SceneRolesView;
					if (tmpRole) 
					{
						roleNum++;
						tmpRole.fight();
					}
				}
				
				if (roleNum>0) 
				{
					setTimeout(openTiger, 500);
				}else {
					openTiger();
				}
				
			}
		}
		
		private function openTiger():void {
			DisplayManager.stageMouseEnabled = true;
			var items:Array = (state.world as MagicWorld).items;
			var tmpRole:SceneRolesView;
			for (var i:int = 0; i < items.length; i++) 
			{
				tmpRole = items[i] as SceneRolesView;
				if (tmpRole) 
				{
					tmpRole.closeFight();
				}
			}
			var curSceneUser:UserVo = DataManager.getInstance().curSceneUser;
			DataManager.getInstance().setVar("tigerMachineData", {type:FriendActionType.OCC, enemyUid:curSceneUser.uid,enemyName:curSceneUser.name,enemyFace:curSceneUser.face, buildId:buildId } );
			ActModuleManager.getInstance().addActModule(DataManager.getInstance().getActByName("TigerMachine"));
		}
		
		override public function onSpecialBuildOver(event:GameMouseEvent):void 
		{
			
			var curSceneUser:UserVo = DataManager.getInstance().curSceneUser;
			var safeTime:int = Time.getRemainingTimeByEnd(curSceneUser.safeTime);
			var atkSafeTime:int = Time.getRemainingTimeByEnd(curSceneUser.atkSafeTime);
			if (curSceneUser.safeTime == 0 && safeTime == 0 && safeTime==0) {
				event.item.showGlow();
			}
		}
		
	}

}