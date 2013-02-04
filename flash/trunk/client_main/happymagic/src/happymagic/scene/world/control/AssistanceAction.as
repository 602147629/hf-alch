package happymagic.scene.world.control 
{
	import happyfish.events.GameMouseEvent;
	import happyfish.manager.actModule.ActModuleManager;
	import happyfish.scene.world.WorldState;
	import happyfish.time.Time;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.FriendActionType;
	/**
	 * 援助action
	 * @author 
	 */
	public class AssistanceAction extends MouseDefaultAction 
	{
		private var assistTip:SpecialBuildingAssistTip = new SpecialBuildingAssistTip;
		
		public function AssistanceAction($state:WorldState, $stack_flg:Boolean = false) 
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
			
			//判断是否是占领建筑
			var curSceneUser:UserVo = DataManager.getInstance().curSceneUser;
			if (event.item.data.id == curSceneUser.ownerBuildId 
				&& curSceneUser.ownerUid.toString()!=DataManager.getInstance().currentUser.uid
			) 
			{
				DataManager.getInstance().setVar("tigerMachineData", {type:FriendActionType.ASSISTANCE, enemyName:curSceneUser.ownerName,enemyFace:curSceneUser.ownerFace, enemyUid:curSceneUser.uid, buildId:event.item.data.id } );
				ActModuleManager.getInstance().addActModule(DataManager.getInstance().getActByName("TigerMachine"));
			}
		}
		
		override public function onSpecialBuildOver(event:GameMouseEvent):void 
		{
			//判断是否是占领建筑
			var curSceneUser:UserVo = DataManager.getInstance().curSceneUser;
			if (event.item.data.id == curSceneUser.ownerBuildId 
				&& curSceneUser.ownerUid.toString()!=DataManager.getInstance().currentUser.uid
			)
			{
				event.item.showGlow();
				event.item.view.container.addChild(assistTip);
			}
		}
		
		override public function onSpecialBuildOut(event:GameMouseEvent):void 
		{
			//判断是否是占领建筑
			var curSceneUser:UserVo = DataManager.getInstance().curSceneUser;
			if (event.item.data.id == curSceneUser.ownerBuildId 
				&& curSceneUser.ownerUid.toString()!=DataManager.getInstance().currentUser.uid
			)
			{
				event.item.hideGlow();
				if(assistTip.parent) assistTip.parent.removeChild(assistTip);
			}
		}
		
	}

}