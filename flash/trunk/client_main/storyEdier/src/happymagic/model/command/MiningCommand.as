package happymagic.model.command 
{
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	import com.brokenfunction.json.decodeJson;
	/**
	 * 地下城交互Command 2011.11.11
	 * @author XiaJunJie
	 */
	public class MiningCommand extends BaseDataCommand
	{
		
		public function start(id:int):void 
		{
			takeResult = false;
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("GatherMine"), { id:id } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void
		{
			super.load_complete(e);
			
			objdata = decodeJson(e.target.data);
			
			commandComplete();
		}
		
	}

}