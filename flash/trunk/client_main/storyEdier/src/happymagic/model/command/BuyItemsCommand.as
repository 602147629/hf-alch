package happymagic.model.command 
{
	import com.brokenfunction.json.encodeJson;
	import flash.events.Event;
	import happyfish.manager.InterfaceURLManager;
	/**
	 * 买东西
	 * @author lite3
	 */
	public class BuyItemsCommand extends BaseDataCommand 
	{
		
		public function buyItems(items:Array):void {
			createLoad();
			createRequest(InterfaceURLManager.getInstance().getUrl("buyItems"),
				{ items:encodeJson(items) } );
			
			loader.load(request);
		}
		
		override protected function load_complete(e:Event):void 
		{
			super.load_complete(e);
			
			commandComplete();
		}
		
	}

}