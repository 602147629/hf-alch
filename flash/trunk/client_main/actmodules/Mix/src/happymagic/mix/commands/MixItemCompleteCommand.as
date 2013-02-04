package happymagic.mix.commands 
{
	import flash.events.Event;
	import flash.geom.Point;
	import happyfish.manager.InterfaceURLManager;
	import happyfish.utils.display.McShower;
	import happymagic.manager.DataManager;
	import happymagic.manager.DisplayManager;
	import happymagic.mix.view.ui.MixCompleteMovie;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.scene.world.award.AwardItemManager;
	import happymagic.scene.world.grid.item.FurnaceDecor;
	import happymagic.scene.world.MagicWorld;
	
	

	/**
	 * ...
	 * @author lite3
	 */
	public class MixItemCompleteCommand
	{
		
		private var id:String;
		private var command:BaseDataCommand;
		
		public function complete(id:String, isFinish:Boolean):void
		{
			this.id = id;
			var furnace:FurnaceDecor = MagicWorld(DataManager.getInstance().worldState.world).getDecorById(id) as FurnaceDecor;
			if (furnace) furnace.loadingState = false;
			
			command = new BaseDataCommand();
			command.loadCompleteHandler = load_complete;
			command.createLoad();
			command.createRequest(InterfaceURLManager.getInstance().getUrl("mixItemComplete"), { id:id, isFinish:(isFinish ? 1 : 0) } );
			command.loadData();
		}
		
		private function load_complete():void 
		{
			var furnace:FurnaceDecor = MagicWorld(DataManager.getInstance().worldState.world).getDecorById(id) as FurnaceDecor;
			if (!furnace) return;
			
			if (!command.data.result.isSuccess)
			{
				furnace.loadingState = true;
				return;
			}
			
			DataManager.getInstance().mixData.removeCurMix(id);
			
			furnace.setState(FurnaceDecor.STATE_WORKING);
			
			var shower:McShower = new McShower(MixCompleteMovie, furnace.view.container.parent, null, null, putItem, [furnace]);
			var p:Point = furnace.view.container.localToGlobal(new Point(0, -furnace.view.container.height));
			p = furnace.view.container.parent.globalToLocal(p);
			shower.x = p.x;
			shower.y = p.y;
		}
		
		private function putItem(furnace:FurnaceDecor):void 
		{
			furnace.setState(FurnaceDecor.STATE_IDLE);
			furnace.loadingState = true;
			var items:Array = [];
			for (var i:int = command.data.addItems ? command.data.addItems.length -1 : 0; i >= 0; i--)
			{
				items[i] = { id:command.data.addItems[i].cid, num:command.data.addItems[i].num };
			}
			AwardItemManager.getInstance().addAwardsByResultVo(command.data.result, items, furnace.gridPos);
		}
		
	}

}