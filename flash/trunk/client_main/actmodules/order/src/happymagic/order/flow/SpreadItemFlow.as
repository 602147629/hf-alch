package happymagic.order.flow 
{
	import com.friendsofed.isometric.Point3D;
	import com.greensock.data.TweenMaxVars;
	import com.greensock.TweenMax;
	import happyfish.manager.module.interfaces.IModule;
	import happyfish.manager.module.ModuleManager;
	import happyfish.scene.world.WorldState;
	import happymagic.manager.DataManager;
	import happymagic.model.vo.ResultVo;
	import happymagic.order.view.SatisfactionBar;
	import happymagic.order.vo.ModuleType;
	import happymagic.scene.world.award.AwardItemManager;
	import happymagic.scene.world.award.AwardItemView;
	import happymagic.scene.world.award.AwardType;
	
	/**
	 * ...
	 * @author lite3
	 */
	public class SpreadItemFlow 
	{
		private var tip:int;
		private var result:ResultVo;
		private var items:Array;
		
		/**
		 * 
		 * @param	tip 小费
		 * @param	result
		 * @param	items Array<ItemVo>
		 */
		public function SpreadItemFlow(tip:int, result:ResultVo, items:Array) 
		{
			this.tip = tip;
			this.result = result;
			this.items = items;
		}
		
		public function showAt(pos:Point3D):void 
		{
			
			var len:int = items ? items.length : 0;
			var list:Array = [];
			for (var i:int = len - 1; i >= 0; i--)
			{
				list[i] = { id:items[i].cid, num:items[i].num };
			}
			AwardItemManager.getInstance().addAwardsByResultVo(result, list, pos);
			if (tip <= 0) return;
			
			
			// 消费的动作
			var worldState:WorldState = DataManager.getInstance().worldState;
			var award:AwardItemView = new AwardItemView( { type:AwardType.COIN, num:tip, className:"TipCoin", x:pos.x, y: -1, z:pos.z }, worldState);
			award.physics = false;
			award.mouseEnabled = false;
			worldState.world.addItem(award);
			award.physics = true;
			award.mouseEnabled = true;
			
			//var satisfactionBar:IModule = ModuleManager.getInstance().getModule(ModuleType.SATISFACTION_BAR_NAME);
			//var screenX:int = satisfactionBar.x;
			//var screenY:int = satisfactionBar.y;
			//TweenMax.from(award.view.container, 0.6, { screenX:screenX } );
			//TweenMax.from(award.view.container, 0.6, { screenY:screenY, onComplete:tipMoveComplete, onCompleteParams:[award] } );
		}
		
		private function tipMoveComplete(award:AwardItemView):void 
		{
			award.physics = true;
			award.mouseEnabled = true;
		}
		
	}

}