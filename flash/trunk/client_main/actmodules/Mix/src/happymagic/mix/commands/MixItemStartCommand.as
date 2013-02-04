package happymagic.mix.commands 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import happymagic.manager.DataManager;
	import happymagic.mix.MixMain;
	import happymagic.model.command.BaseDataCommand;
	import happymagic.model.vo.classVo.MixClassVo;
	import happymagic.model.vo.MixVo;
	import happymagic.scene.world.grid.item.FurnaceDecor;
	import happymagic.scene.world.MagicWorld;
	/**
	 * ...
	 * @author lite3
	 */
	public class MixItemStartCommand
	{
		private var id:String;
		private var mixCid:int;
		private var num:int;
		private var probability:int;
		private var command:BaseDataCommand;
		
		/**
		 * 开始合成
		 * @param	mixCid 合成术cid
		 * @param	num 数量
		 * @param	probability 合成几率
		 * @param	id 工作台id,可没有
		 */
		public function start(mixCid:int, num:int, probability:int, id:String = null):void
		{
			
			this.id = id;
			this.num = num;
			this.mixCid = mixCid;
			this.probability = probability;
			var furnace:FurnaceDecor = MagicWorld(DataManager.getInstance().worldState.world).getDecorById(id) as FurnaceDecor;
			if (furnace) furnace.loadingState = true;
			
			var o:Object = { mixCid:mixCid, num:num, probability:probability };
			if (id != null) o.id = id;
			
			command = new BaseDataCommand();
			command.loadCompleteHandler = load_complete;
			command.createLoad();
			command.createRequest(InterfaceURLManager.getInstance().getUrl("mixItemStart"), o );
			command.loadData();
		}
		
		private function load_complete():void 
		{
			var furnace:FurnaceDecor = MagicWorld(DataManager.getInstance().worldState.world).getDecorById(id) as FurnaceDecor;
			if (!furnace) return;
			
			furnace.loadingState = true;
			if (!command.data.result.isSuccess) return;
			
			furnace.setState(FurnaceDecor.STATE_WORKING);
			
			var mixClassVo:MixClassVo = DataManager.getInstance().mixData.getMixClass(mixCid);
			var mixVo:MixVo = new MixVo();
			mixVo.setData( { furnaceId:id, cid:mixCid, curProbability:probability, remainingTime:mixClassVo.time*num, num:num } );
			DataManager.getInstance().mixData.addCurMix(mixVo);
			
			MixMain.timer.start();
		}
		
	}

}