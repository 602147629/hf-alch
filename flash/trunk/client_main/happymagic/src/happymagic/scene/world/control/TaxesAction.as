package happymagic.scene.world.control 
{
	import flash.events.Event;
	import happyfish.events.GameMouseEvent;
	import happyfish.scene.world.grid.IsoItem;
	import happyfish.scene.world.WorldState;
	import happyfish.time.Time;
	import happymagic.events.DataManagerEvent;
	import happymagic.manager.DataManager;
	import happymagic.model.command.GetTaxesCommand;
	import happymagic.model.vo.UserVo;
	import happymagic.scene.world.award.AwardItemManager;
	import happymagic.scene.world.grid.item.SpecialBuild;
	import happymagic.scene.world.MagicWorld;
	/**
	 * 收税action
	 * @author 
	 */
	public class TaxesAction extends MouseDefaultAction 
	{
		private var tmpItem:SpecialBuild;
		private var command:GetTaxesCommand;
		
		public function TaxesAction($state:WorldState, $stack_flg:Boolean = false) 
		{
			super($state, false);
		}
		
		override public function onSpecialBuildClick(event:GameMouseEvent):void {
			skipBackgroundClick = true;
			
			//判断是否是占领建筑
			var curSceneUser:UserVo = DataManager.getInstance().curSceneUser;
			if (uint(event.item.data.id) == curSceneUser.ownerBuildId 
				&& Time.getRemainingTimeByEnd(curSceneUser.ownerAwardTime)==0
			) 
			{
				tmpItem = event.item as SpecialBuild;
				event.item.mouseEnabled = false;
				getTaxes();
			}
		}
		
		override public function onSpecialBuildOver(event:GameMouseEvent):void 
		{
			
			
			//判断是否是占领建筑
			var curSceneUser:UserVo = DataManager.getInstance().curSceneUser;
			if (uint(event.item.data.id) == curSceneUser.ownerBuildId 
				&& Time.getRemainingTimeByEnd(curSceneUser.ownerAwardTime)==0
			) event.item.showGlow();
		}
		
		/**
		 * 领取税金
		 */
		private function getTaxes():void 
		{
			command = new GetTaxesCommand();
			command.addEventListener(Event.COMPLETE, getTaxes_complete);
			command.getTaxes(DataManager.getInstance().curSceneUser.uid);
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
		
		private function getTaxes_complete(e:Event):void 
		{
			command.removeEventListener(Event.COMPLETE, getTaxes_complete);
			
			tmpItem.mouseEnabled = true;
			
			
			if (command.result.isSuccess) 
			{
				tmpItem.initTip();
				//掉落
				AwardItemManager.getInstance().addAwardsByResultVo(command.data.result, command.data.addItems,tmpItem.gridPos);
			}
			tmpItem = null;
			
		}
	}

}